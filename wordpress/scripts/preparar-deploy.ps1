# Prepara el paquete de deploy a DonWeb segun project/runbook-migracion-manual.md
# (Fases 0, 0-bis, 1 y 2). Solo trabaja en local: no se conecta al hosting y no
# modifica la base local (el search-replace usa --export).
#
#   powershell -ExecutionPolicy Bypass -File .\wordpress\scripts\preparar-deploy.ps1 -Dominio cipba.site
#   powershell -ExecutionPolicy Bypass -File .\wordpress\scripts\preparar-deploy.ps1 -Dominio cipba.site -SoloTema
#
# Completo: wordpress\backups\deploy-<dominio>-AAAAMMDD-HHMM\
#   backup-local.sql            backup de la base local antes de exportar (Fase 0.4)
#   chequeo-pre-deploy.txt      salida de scripts/chequeo-pre-deploy.php (Fase 0-bis)
#   deploy-<dominio>.sql        base con URLs https://<dominio>, para phpMyAdmin (Fase 1)
#   wp-content-<dominio>.zip    wp-content, para extraer en la raiz del dominio (Fase 2)
#   resumen.txt                 resultados y alertas
# -SoloTema: solo cipba-tema-<version>.zip (se extrae en wp-content/themes/) + resumen.txt
#
# Las alertas se imprimen como lineas "ALERTA: ..."; al final, "ALERTAS=<n>" y "DEPLOY_DIR=<ruta>".
# (Archivo solo ASCII a proposito: PowerShell 5.1 lee los .ps1 sin BOM como ANSI.)

param(
	[Parameter(Mandatory = $true)][string]$Dominio,
	[switch]$SoloTema
)

$ErrorActionPreference = 'Stop'
$LimiteSubida = 128MB   # Administrador de archivos de Ferozo

$wpDir   = Split-Path -Parent $PSScriptRoot
$scripts = $PSScriptRoot
$stamp   = Get-Date -Format 'yyyyMMdd-HHmm'
$alertas = New-Object System.Collections.ArrayList
$resumen = New-Object System.Collections.ArrayList

function Fail($msg) { Write-Host "ERROR: $msg" -ForegroundColor Red; exit 1 }
function Alerta($msg) { [void]$alertas.Add($msg); Write-Host "ALERTA: $msg" -ForegroundColor Yellow }
function Nota($msg) { [void]$resumen.Add($msg); Write-Host "  $msg" }
function MB($bytes) { '{0:N1} MB' -f ($bytes / 1MB) }

# --- Dominio ----------------------------------------------------------------
$Dominio = $Dominio.Trim().ToLower() -replace '^https?://', '' -replace '/.*$', ''
if ($Dominio -notmatch '^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$') { Fail "Dominio invalido: '$Dominio'." }
$url = "https://$Dominio"

# --- Chequeos previos -------------------------------------------------------
$running = docker ps --format '{{.Names}}'
if ($LASTEXITCODE -ne 0) { Fail 'Docker no responde. Abri Docker Desktop.' }
foreach ($c in 'wordpress_db', 'wordpress_app') {
	if ($running -notcontains $c) { Fail "El contenedor $c no esta corriendo (cd wordpress; docker compose up -d)." }
}
$siteurl = (docker exec wordpress_app wp --allow-root option get siteurl 2>$null | Out-String).Trim()
if ($siteurl -ne 'http://localhost:8080') { Fail "siteurl local es '$siteurl' (se esperaba http://localhost:8080)." }

$verTema = (Select-String -Path (Join-Path $wpDir 'themes\cipba\functions.php') -Pattern "CHILD_THEME_CIPBA_VERSION',\s*'([^']+)'").Matches[0].Groups[1].Value
$verCore = (docker exec wordpress_app wp --allow-root core version 2>$null | Out-String).Trim()

$tipo   = if ($SoloTema) { 'tema' } else { 'completo' }
$outDir = Join-Path $wpDir "backups\deploy-$Dominio-$stamp"
New-Item -ItemType Directory -Force $outDir | Out-Null
Write-Host "Deploy $tipo para $url"
Write-Host "Destino: $outDir"
Nota "Dominio: $url | tipo: $tipo | tema $verTema | WP core $verCore | $(Get-Date -Format 'yyyy-MM-dd HH:mm')"

# ============================================================================
# Solo tema
# ============================================================================
if ($SoloTema) {
	Write-Host '[1/1] Empaquetando el tema...'
	docker cp (Join-Path $scripts 'zip-tema.php') wordpress_app:/tmp/zip-tema.php
	$out = (docker exec wordpress_app php /tmp/zip-tema.php | Out-String).Trim()
	if ($LASTEXITCODE -ne 0) { Fail 'No se pudo empaquetar el tema.' }
	docker cp (Join-Path $scripts 'verificar-zip.php') wordpress_app:/tmp/verificar-zip.php
	$ver = docker exec wordpress_app php /tmp/verificar-zip.php /tmp/cipba-tema.zip cipba
	$zipName = "cipba-tema-$verTema.zip"
	docker cp wordpress_app:/tmp/cipba-tema.zip (Join-Path $outDir $zipName)
	docker exec wordpress_app rm -f /tmp/cipba-tema.zip /tmp/zip-tema.php /tmp/verificar-zip.php | Out-Null

	$v = @{}; foreach ($l in $ver) { if ($l -match '^([^=]+)=(.*)$') { $v[$matches[1]] = $matches[2] } }
	Nota "$zipName : $out"
	if ($v['tema_cipba'] -ne 'si') { Alerta 'El zip del tema no contiene cipba/style.css.' }
	if ([int]$v['rutas_con_barra_invertida'] -gt 0) { Alerta 'El zip tiene rutas con barra invertida.' }
}
else {
# ============================================================================
# Completo (Fases 0, 0-bis, 1 y 2)
# ============================================================================

	# --- Fase 0: plugins de desarrollo y backup local -----------------------
	Write-Host '[1/6] Plugins de desarrollo...'
	$plugins = docker exec wordpress_app wp --allow-root plugin list --field=name 2>$null
	foreach ($p in 'duplicator', 'wp-reset') {
		if ($plugins -contains $p) { Alerta "El plugin $p esta instalado: desinstalarlo antes de exportar (runbook Fase 0.1)." }
	}

	Write-Host '[2/6] Backup de la base local...'
	docker exec wordpress_db sh -c 'MYSQL_PWD=$MYSQL_ROOT_PASSWORD mysqldump -uroot --no-tablespaces --single-transaction --default-character-set=utf8mb4 $MYSQL_DATABASE > /tmp/backup-local.sql'
	if ($LASTEXITCODE -ne 0) { Fail 'mysqldump fallo.' }
	$bkp = Join-Path $outDir 'backup-local.sql'
	docker cp wordpress_db:/tmp/backup-local.sql $bkp
	docker exec wordpress_db rm -f /tmp/backup-local.sql | Out-Null
	if (-not ((Get-Content $bkp -Tail 3) -match 'Dump completed')) { Fail 'El backup local parece incompleto.' }
	Nota "backup-local.sql: $(MB (Get-Item $bkp).Length)"

	# --- Fase 0-bis: chequeos de seguridad ----------------------------------
	Write-Host '[3/6] Chequeo pre-deploy (Fase 0-bis)...'
	docker cp (Join-Path $scripts 'chequeo-pre-deploy.php') wordpress_app:/tmp/chequeo-pre-deploy.php
	$chequeo = docker exec wordpress_app wp --allow-root eval-file /tmp/chequeo-pre-deploy.php 2>$null
	docker exec wordpress_app rm -f /tmp/chequeo-pre-deploy.php | Out-Null
	$chequeo | Set-Content -Encoding UTF8 (Join-Path $outDir 'chequeo-pre-deploy.txt')
	$chequeo | ForEach-Object { Write-Host "    $_" }
	$marcas = @($chequeo | Where-Object { $_ -match '^\[!\]' })
	foreach ($m in $marcas) { Alerta "chequeo-pre-deploy: $($m.Substring(3).Trim())" }
	if (@($chequeo | Where-Object { $_ -match 'REVISAR' }).Count -gt 0) { Alerta 'Hay archivos sospechosos en wp-content: NO subir este paquete hasta revisarlos.' }

	# Humo sobre el sitio local (Fase 0-bis.6)
	Write-Host '[4/6] Prueba de humo en local...'
	$rutas = '/', '/novedades/', '/novedades/?tipo=noticia', '/institucional/', '/subcomisiones/', '/contacto/', '/honorarios/', '/normativa/', '/tad/',
		'/tramites/inscripcion/', '/tramites/rehabilitacion/', '/tramites/baja/', '/tramites/solicitud-credenciales/', '/tramites/solicitud-de-certificados/', '/tramites/pago-matricula/'
	$okHumo = 0
	foreach ($r in $rutas) {
		$u = "http://localhost:8080$r"
		try {
			$resp = Invoke-WebRequest -Uri $u -UseBasicParsing -TimeoutSec 90
			if ($resp.Content -match 'Fatal error|Warning:|Deprecated:') { Alerta "Humo: $r muestra errores de PHP." }
			else { $okHumo++ }
		}
		catch { Alerta "Humo: $r respondio con error ($($_.Exception.Message))." }
	}
	Nota "Humo: $okHumo/$($rutas.Count) URLs en 200 sin errores de PHP"

	# --- Fase 1: base con URLs reemplazadas ---------------------------------
	Write-Host "[5/6] Exportando la base con $url (Fase 1)..."
	$sr = (docker exec wordpress_app wp --allow-root search-replace 'http://localhost:8080' $url --all-tables --precise --export=/tmp/deploy.sql 2>$null | Out-String).Trim()
	if ($LASTEXITCODE -ne 0) { Fail "search-replace fallo: $sr" }
	$sqlName = "deploy-$Dominio.sql"
	$sql = Join-Path $outDir $sqlName
	docker cp wordpress_app:/tmp/deploy.sql $sql
	docker exec wordpress_app rm -f /tmp/deploy.sql | Out-Null

	$reemplazos = if ($sr -match 'Made (\d+) replacement') { $matches[1] } else { '?' }
	$restos   = @(Select-String -Path $sql -SimpleMatch 'localhost:8080').Count
	$nuevas   = @(Select-String -Path $sql -SimpleMatch $url).Count
	$siteurl2 = (docker exec wordpress_app wp --allow-root option get siteurl 2>$null | Out-String).Trim()
	Nota "$sqlName : $(MB (Get-Item $sql).Length), $reemplazos reemplazos, $restos lineas con localhost:8080"
	if ($restos -gt 0) { Alerta "Quedaron $restos lineas con localhost:8080 en $sqlName." }
	if ($nuevas -eq 0) { Alerta "$sqlName no contiene $url." }
	if ($siteurl2 -ne 'http://localhost:8080') { Alerta "La base local cambio: siteurl = $siteurl2." }

	# --- Fase 2: wp-content.zip ---------------------------------------------
	Write-Host '[6/6] Empaquetando wp-content (Fase 2, puede tardar unos minutos)...'
	docker cp (Join-Path $scripts 'zip-wp-content.php') wordpress_app:/tmp/zip-wp-content.php
	$zout = (docker exec wordpress_app php -d memory_limit=1G /tmp/zip-wp-content.php | Out-String).Trim()
	if ($LASTEXITCODE -ne 0) { Fail 'No se pudo armar el zip de wp-content.' }
	docker cp (Join-Path $scripts 'verificar-zip.php') wordpress_app:/tmp/verificar-zip.php
	$ver = docker exec wordpress_app php /tmp/verificar-zip.php /tmp/wp-content.zip wp-content
	$zipName = "wp-content-$Dominio.zip"
	$zip = Join-Path $outDir $zipName
	docker cp wordpress_app:/tmp/wp-content.zip $zip
	docker exec wordpress_app rm -f /tmp/wp-content.zip /tmp/zip-wp-content.php /tmp/verificar-zip.php | Out-Null

	$v = @{}; foreach ($l in $ver) { if ($l -match '^([^=]+)=(.*)$') { $v[$matches[1]] = $matches[2] } }
	$tam = (Get-Item $zip).Length
	Nota "$zipName : $(MB $tam), $($v['entradas']) entradas ($zout)"
	$esperadas = if ($zout -match 'archivos: (\d+)\s+carpetas: (\d+)') { [int]$matches[1] + [int]$matches[2] } else { -1 }
	if ($esperadas -ne [int]$v['entradas']) { Alerta "El zip tiene $($v['entradas']) entradas y el script conto $esperadas." }
	if ([int]$v['rutas_con_barra_invertida'] -gt 0) { Alerta 'El zip tiene rutas con barra invertida.' }
	if ([int]$v['entradas_upgrade_temp_backup'] -gt 0) { Alerta 'El zip incluye upgrade-temp-backup (versiones viejas de plugins).' }
	if ($v['tema_cipba'] -ne 'si') { Alerta 'El zip no contiene el tema cipba.' }
	foreach ($p in 'meta-box', 'litespeed-cache', 'megamenu', 'fluentform') {
		if ($v["plugin_$p"] -ne 'si') { Alerta "El zip no contiene el plugin $p." }
	}
	if ($tam -gt $LimiteSubida) { Alerta "El zip pesa $(MB $tam): supera los 128 MB del Administrador de archivos, subirlo por FTP." }
	elseif ($tam -gt 0.9 * $LimiteSubida) { Nota "Aviso: el zip esta a $(MB ($LimiteSubida - $tam)) del limite de 128 MB del Administrador de archivos (si falla la subida, usar FTP)." }
}

# --- Resumen ----------------------------------------------------------------
[void]$resumen.Add('')
if ($alertas.Count) { [void]$resumen.Add('Alertas:'); foreach ($a in $alertas) { [void]$resumen.Add("  - $a") } }
else { [void]$resumen.Add('Sin alertas.') }
$resumen | Set-Content -Encoding UTF8 (Join-Path $outDir 'resumen.txt')

Write-Host ''
if ($alertas.Count) { Write-Host "Paquete generado con $($alertas.Count) alerta(s): revisarlas antes de subir." -ForegroundColor Yellow }
else { Write-Host 'Paquete generado sin alertas.' -ForegroundColor Green }
Write-Output "ALERTAS=$($alertas.Count)"
Write-Output "DEPLOY_DIR=$outDir"
