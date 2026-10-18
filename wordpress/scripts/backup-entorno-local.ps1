# Backup completo del entorno local (base de datos + wp-content + .env) para
# replicarlo en otra PC. Ver wordpress/README.md, "Replicar el entorno en otra PC".
#
#   powershell -ExecutionPolicy Bypass -File .\wordpress\scripts\backup-entorno-local.ps1
#
# Deja todo en wordpress\backups\local-AAAAMMDD-HHMM\ (carpeta git-ignored):
#   cipba-local.sql          dump de la base, con las URLs de localhost:8080
#   wp-content-local.tar.gz  plugins y uploads (sin themes/cipba, que esta en git)
#   .env                     credenciales de la base
#
# Solo lee: no modifica la base ni los volumenes.
# (Archivo solo ASCII a proposito: PowerShell 5.1 lee los .ps1 sin BOM como ANSI.)

$ErrorActionPreference = 'Stop'

$wpDir     = Split-Path -Parent $PSScriptRoot
$stamp     = Get-Date -Format 'yyyyMMdd-HHmm'
$backupDir = Join-Path $wpDir "backups\local-$stamp"

function Fail($msg) {
	Write-Host "ERROR: $msg" -ForegroundColor Red
	exit 1
}

# --- Chequeos previos -------------------------------------------------------
$running = docker ps --format '{{.Names}}'
if ($LASTEXITCODE -ne 0) { Fail 'Docker no responde. Abri Docker Desktop.' }
foreach ($c in 'wordpress_db', 'wordpress_app') {
	if ($running -notcontains $c) { Fail "El contenedor $c no esta corriendo (cd wordpress; docker compose up -d)." }
}
if (-not (Test-Path (Join-Path $wpDir '.env'))) { Fail 'No existe wordpress\.env.' }

New-Item -ItemType Directory -Force $backupDir | Out-Null
Write-Host "Destino: $backupDir"

# --- 1. Base de datos -------------------------------------------------------
# El dump se escribe dentro del contenedor y se copia afuera: redirigir con '>'
# en PowerShell 5.1 lo guardaria en UTF-16. MYSQL_PWD evita el aviso de password.
Write-Host '[1/3] Exportando la base...'
docker exec wordpress_db sh -c 'MYSQL_PWD=$MYSQL_ROOT_PASSWORD mysqldump -uroot --single-transaction --routines --default-character-set=utf8mb4 $MYSQL_DATABASE > /tmp/cipba-local.sql'
if ($LASTEXITCODE -ne 0) { Fail 'mysqldump fallo.' }
docker cp wordpress_db:/tmp/cipba-local.sql (Join-Path $backupDir 'cipba-local.sql')
if ($LASTEXITCODE -ne 0) { Fail 'No se pudo copiar el dump.' }
docker exec wordpress_db rm -f /tmp/cipba-local.sql | Out-Null

$sql = Join-Path $backupDir 'cipba-local.sql'
$tail = Get-Content $sql -Tail 3
if (-not ($tail -match 'Dump completed')) { Fail 'El dump parece incompleto (falta "Dump completed" al final).' }

# --- 2. wp-content ----------------------------------------------------------
Write-Host '[2/3] Empaquetando wp-content...'
docker run --rm -v wordpress_docker_wp_content:/data -v "${backupDir}:/backup" alpine tar czf /backup/wp-content-local.tar.gz -C /data --exclude=./themes/cipba --exclude=./cache --exclude=./upgrade-temp-backup .
if ($LASTEXITCODE -ne 0) { Fail 'No se pudo empaquetar wp-content.' }

$tgz = Join-Path $backupDir 'wp-content-local.tar.gz'
$nFiles = docker run --rm -v "${backupDir}:/backup" alpine sh -c 'tar tzf /backup/wp-content-local.tar.gz | wc -l'
if ($LASTEXITCODE -ne 0) { Fail 'El .tar.gz no se puede leer.' }

# --- 3. .env ----------------------------------------------------------------
Write-Host '[3/3] Copiando .env...'
Copy-Item (Join-Path $wpDir '.env') (Join-Path $backupDir '.env')

# --- Resumen ----------------------------------------------------------------
$tables = (Select-String -Path $sql -Pattern '^CREATE TABLE' | Measure-Object).Count
$mb = { param($p) '{0:N1} MB' -f ((Get-Item $p).Length / 1MB) }

Write-Host ''
Write-Host 'Backup listo:' -ForegroundColor Green
Write-Host ("  cipba-local.sql          {0}  ({1} tablas)" -f (& $mb $sql), $tables)
Write-Host ("  wp-content-local.tar.gz  {0}  ({1} entradas)" -f (& $mb $tgz), $nFiles.Trim())
Write-Host  "  .env"
Write-Host ''
Write-Host 'Contiene hashes de contrasenas y envios del formulario: no subirlo a GitHub.'
Write-Host 'Para restaurar: wordpress\README.md, "Replicar el entorno en otra PC".'
Write-Output "BACKUP_DIR=$backupDir"
