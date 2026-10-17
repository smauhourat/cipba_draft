# WordPress local (CIPBA Distrito VII)

Entorno Docker de desarrollo + tema hijo `cipba` (sobre Astra).

## Uso
```
cd wordpress
cp .env.example .env      # y ajustar credenciales
docker compose up -d --build   # el Dockerfile agrega WP-CLI (`docker exec wordpress_app wp --allow-root ...`)
```
- Sitio: http://localhost:8080 — phpMyAdmin: http://localhost:8081

## Que vive donde
- `themes/cipba/` — tema hijo, **versionado**; montado como bind mount en el contenedor (se edita aca).
- `wp-content` (plugins, uploads, otros temas) — volumen Docker `wordpress_docker_wp_content`, **no** esta en git.
- Base de datos — volumen `wordpress_docker_db_data`. Menus, paginas y opciones viven ahi, no en archivos.
- `plugins.txt` — lista de plugins y versiones (los de terceros no se versionan).
- `scripts/` — scripts WP-CLI reutilizables (`docker exec wordpress_app wp --allow-root eval-file ...`).

## Cuidado
- No correr `docker compose down -v` (borra los volumenes con la DB y los uploads).
- Al editar `style.css` subir `CHILD_THEME_CIPBA_VERSION` en `functions.php` para invalidar cache.
- Plan de migracion a DonWeb: `../project/plan-migracion-donweb.md`.
- Manuales: `../project/manual-usuario-contenido.md` (carga de contenido) y `../project/manual-tecnico-tema.md` (mantenimiento del tema).

## Replicar el entorno en otra PC (con todos los datos)

Ademas del codigo de GitHub hacen falta 3 cosas que **no estan en git** (`.gitignore` excluye `wordpress/.env`, `wordpress/backups/` y `*.sql`):

| Que | Donde esta | Que contiene |
|---|---|---|
| Base de datos | volumen `wordpress_docker_db_data` | Paginas, entradas, tramites, documentos, sedes, menus, Datos del Distrito, usuarios, envios del formulario |
| `wp-content` | volumen `wordpress_docker_wp_content` | Plugins y uploads (imagenes, PDFs) |
| `wordpress/.env` | disco local | Credenciales de la base (usar el mismo en la PC nueva) |

El nucleo de WordPress (volumen `wordpress_data`) no hace falta: lo regenera la imagen. Antes de empezar, hacer commit y push de lo pendiente.

### 1. Exportar (PC de origen, PowerShell, desde `wordpress\`)

Base de datos. El dump se escribe **dentro** del contenedor y se copia afuera: redirigir con `>` en PowerShell 5.1 lo guarda en UTF-16 y no se puede importar.
```powershell
docker exec wordpress_db sh -c 'mysqldump -uroot -p$MYSQL_ROOT_PASSWORD --single-transaction --default-character-set=utf8mb4 $MYSQL_DATABASE > /tmp/cipba-local.sql'
docker cp wordpress_db:/tmp/cipba-local.sql .\backups\cipba-local-AAAAMMDD.sql
```
Este dump conserva las URLs `http://localhost:8080`. Los `.sql` preparados para DonWeb **no sirven** para esto (tienen las URLs ya reemplazadas por el dominio).

`wp-content` (sin el tema, que viene de GitHub):
```powershell
docker run --rm -v wordpress_docker_wp_content:/data -v "${PWD}\backups:/backup" alpine tar czf /backup/wp-content-local-AAAAMMDD.tar.gz -C /data --exclude=./themes/cipba --exclude=./cache --exclude=./upgrade-temp-backup .
```

Llevar el `.sql`, el `.tar.gz` y el `.env` a la otra PC por pendrive o un Drive privado. **No subirlos a GitHub**: contienen hashes de contraseñas y los envios del formulario de contacto.

### 2. Importar (PC nueva)

1. Instalar Docker Desktop (con WSL2) y Git; clonar el repo (mejor fuera de OneDrive).
2. Copiar el `.env` a `wordpress\.env` y los archivos a `wordpress\backups\`.
3. Levantar el entorno (si aparece el instalador de WordPress, **no completarlo**) y frenar la app:
   ```powershell
   cd wordpress
   docker compose up -d --build
   docker compose stop wordpress
   ```
4. Restaurar `wp-content` en el volumen y volver a levantar la app:
   ```powershell
   docker run --rm -v wordpress_docker_wp_content:/data -v "${PWD}\backups:/backup" alpine tar xzf /backup/wp-content-local-AAAAMMDD.tar.gz -C /data
   docker compose start wordpress
   docker exec wordpress_app chown -R www-data:www-data /var/www/html/wp-content/uploads /var/www/html/wp-content/plugins
   ```
5. Importar la base:
   ```powershell
   docker cp .\backups\cipba-local-AAAAMMDD.sql wordpress_db:/tmp/db.sql
   docker exec wordpress_db sh -c 'mysql -uroot -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE < /tmp/db.sql'
   ```
6. Verificar:
   - Entrar a http://localhost:8080/wp-admin con los mismos usuarios de siempre.
   - Plugins: **Meta Box** activo (sin el, las pantallas de edicion quedan sin campos).
   - Ajustes → Enlaces permanentes → **Guardar** (si no, `/tramites/...` puede dar 404).
   - Mega Menu → Menu Themes → **Guardar** una vez (regenera el CSS del menu).

Si en la PC nueva se usa otro puerto que 8080, despues de importar:
```powershell
docker exec wordpress_app wp --allow-root search-replace http://localhost:8080 http://localhost:NUEVO --all-tables --precise
```

### Opcional
- Prototipos HTML: `npm install` en la raiz del repo (`build.mjs` / `serve.mjs`).
- Memoria de Claude Code: queda por maquina en `C:\Users\<usuario>\.claude\projects\<proyecto>\memory\`; copiarla si se quiere conservar el contexto del proyecto.
