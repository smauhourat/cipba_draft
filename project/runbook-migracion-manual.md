# Runbook — Migración manual del WordPress local a DonWeb (Ferozo, sin SSH)

**Cuándo usarlo:** cuando el plugin (Duplicator / Migrador de Ferozo) no sirve, o para repetir el proceso de forma predecible.
**No depende de ningún plugin de backup ni de acceso SSH al hosting.**
Sirve igual para el ensayo (`cipba.site`) y para el destino final (`cipba.org`): solo cambia `<DOMINIO>`.

> Convención: `<DOMINIO>` = `cipba.site` (staging) o `cipba.org` (final). Todos los comandos se corren desde `wordpress/` en el repo, con los contenedores levantados (`docker compose up -d`). Contenedor de WordPress: `wordpress_app`.

**Idea central:** hay 3 cosas para mover y solo una es delicada.
| Qué | Cómo llega al hosting | Delicado |
|---|---|---|
| Archivos (`wp-content`: tema, plugins, uploads) | zip por el Administrador de archivos / FTP | No |
| Base de datos | `.sql` importado por phpMyAdmin | **Sí: las URLs** |
| Núcleo de WordPress + `wp-config.php` | instalación limpia en el hosting | No |

El problema de las URLs: la base guarda `http://localhost:8080` en cientos de lugares, muchos dentro de datos *serializados*. Un buscar/reemplazar de texto sobre el `.sql` **los rompe** (cambia la longitud y corrompe el dato). Como el hosting no tiene WP-CLI, **el reemplazo se hace en local**, con WP-CLI, al momento de exportar.

---

## Fase 0 — Antes de empezar (local)
1. Si está instalado, **desactivar y eliminar Duplicator** y **WP Reset** (dev-only). Duplicator deja logs con `localhost:8080` en la base.
2. Confirmar que el sitio local está en el estado que se quiere publicar (menús, páginas, CPTs).
3. No actualizar plugins/núcleo justo antes de migrar sin probarlos.
4. Hacer un backup de seguridad de local por si algo sale mal:
   ```
   docker exec wordpress_db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --no-tablespaces --single-transaction wordpress > /tmp/backup-local.sql'
   docker cp wordpress_db:/tmp/backup-local.sql ./backups/backup-local-AAAAMMDD.sql
   ```
   (`wordpress/backups/` está ignorado por git: los `.sql` contienen hashes de contraseñas.)

## Fase 1 — Exportar la base de datos con las URLs ya reemplazadas
Esto **no modifica la base local**: escribe un `.sql` nuevo con el reemplazo aplicado (respeta datos serializados).
```
docker exec wordpress_app wp --allow-root search-replace 'http://localhost:8080' 'https://<DOMINIO>' --all-tables --precise --export=/tmp/deploy.sql
docker cp wordpress_app:/tmp/deploy.sql ./backups/deploy-<DOMINIO>-AAAAMMDD.sql
```
- Resultado esperado: `Success: Made ~160 replacements and exported to /tmp/deploy.sql.` (≈1,9 MB).
- Verificar que la base local sigue igual: `docker exec wordpress_app wp --allow-root option get siteurl` → `http://localhost:8080`.
- Verificar que no quedaron URLs viejas: buscar `localhost:8080` en el `.sql`. Con Duplicator/WP Reset ya quitados no debería quedar ninguna.
- El `.sql` ya incluye `DROP TABLE IF EXISTS` + `CREATE TABLE`, así que importarlo pisa las tablas existentes.
- Prefijo de tablas: `wp_` (hay que usar el mismo en `wp-config.php` del hosting).
- ✅ *Verificado el 2026-09-19 (164 reemplazos, base local intacta).*

## Fase 2 — Empaquetar `wp-content`
`wp-content` vive en un volumen Docker (el tema `cipba` está montado desde `wordpress/themes/cipba`; el `tar` lo incluye).
```
docker exec wordpress_app tar -czf /tmp/wp-content.tar.gz -C /var/www/html \
  --exclude=wp-content/cache --exclude=wp-content/upgrade --exclude=wp-content/updraft \
  --exclude=wp-content/duplicator-backups --exclude=wp-content/plugins/wp-reset \
  --exclude=wp-content/plugins/duplicator wp-content
docker cp wordpress_app:/tmp/wp-content.tar.gz ./backups/wp-content.tar.gz
```
Convertir a **.zip** (el Administrador de archivos del hosting extrae zip con seguridad; no usar `Compress-Archive` de PowerShell 5.1: genera rutas con `\` que se rompen en Linux). En PowerShell, con el `tar` de Windows:
```
cd backups
mkdir pkg
tar -xzf wp-content.tar.gz -C pkg
tar -a -c -f wp-content.zip -C pkg wp-content
```
- `tar: file changed as we read it` es un aviso normal (algún archivo temporal cambió mientras se leía); no invalida el paquete.
- Tamaño de referencia: ≈66 MB el `.tar.gz` y ≈70 MB el `.zip`; límite de subida del hosting: 128 MB.
- Extraer y comprimir ≈9.500 archivos en Windows lleva **varios minutos**; hacerlo en una carpeta de un disco local rápido (p. ej. `C:\`), no sobre `D:\` montado en Docker.
- Si el zip supera 128 MB, subirlo por FTP (FileZilla) en vez del Administrador de archivos.
- Los plugins de terceros vienen dentro del zip: no hace falta reinstalarlos. (`wordpress/plugins.txt` es solo el listado de referencia.)

## Fase 3 — Preparar el hosting (panel Ferozo) *(lo hace el usuario)*
> Los nombres exactos de menús de Ferozo pueden variar; si no coinciden, consultar a soporte.
1. **Dominio/subdominio** apuntando a una carpeta propia (staging: `cipba.site`; final: `cipba.org`). Anotar la carpeta raíz.
2. **Base de datos MySQL** nueva + usuario con todos los permisos. Anotar: host, nombre, usuario, contraseña.
3. **PHP:** 8.4 (lsphp) disponible; `memory_limit` 256M, subida 128M, `max_execution_time` 60 s (valores de Herramientas → Salud del sitio).
4. **SSL** (Let's Encrypt) **activo y funcionando** para el dominio *antes* de abrir el sitio con `https://`. Si no, WordPress entra en bucle de redirecciones.
5. Mientras sea staging: proteger/ocultar del público (contraseña de directorio o "Disuadir a los motores de búsqueda" en Ajustes → Lectura).

## Fase 4 — Instalar WordPress limpio en el hosting
1. Instalar WordPress en la carpeta del dominio, con el instalador de aplicaciones de Ferozo o subiendo el zip de wordpress.org. Usar la **misma versión mayor que local** (`docker exec wordpress_app wp --allow-root core version` → hoy 7.1.1) y el **mismo idioma es_AR**.
2. Completar el asistente con datos cualquiera (se sobrescribirán al importar la base).
3. Verificar que `wp-config.php` usa el prefijo `wp_` (línea `$table_prefix = 'wp_';`). Si no, cambiarlo a `wp_`.

## Fase 5 — Subir `wp-content`
1. En el Administrador de archivos (o FTP) subir `wp-content.zip` a la carpeta raíz del dominio.
2. **Renombrar** el `wp-content` existente a `wp-content-vacio` (no borrarlo todavía).
3. Extraer el zip: debe quedar `<raíz>/wp-content/...`.
4. Verificar que existan `wp-content/themes/cipba/`, `wp-content/plugins/` y `wp-content/uploads/`.
5. Permisos: carpetas 755, archivos 644 (que WordPress pueda escribir en `uploads`).
6. Borrar `wp-content.zip` y `wp-content-vacio` cuando todo funcione.

## Fase 6 — Importar la base de datos
1. phpMyAdmin del hosting → seleccionar la base creada → **Importar** → elegir `deploy-<DOMINIO>-AAAAMMDD.sql` (≈1,9 MB, muy por debajo de los 128 MB).
2. Formato SQL, codificación utf-8. Debe terminar sin errores.
3. Si falla por *collation* o versión (MySQL local 8.4 → hosting 8.0.45): el dump usa `utf8mb4_unicode_520_ci`, compatible con 8.0. Si igual da error, copiar el mensaje exacto y consultar.
4. Confirmar en phpMyAdmin: tabla `wp_options`, filas `siteurl` y `home` = `https://<DOMINIO>`.

## Fase 7 — Post-migración (checklist)
- [ ] Abrir `https://<DOMINIO>/wp-login.php` e ingresar con un usuario de la base local.
- [ ] Ajustes → Enlaces permanentes → **Guardar** (regenera `.htaccess`). Estructura: `/%postname%/`.
- [ ] Verificar: home, menú Principal (Max Mega Menu), footer de 4 columnas, CPTs (`/eventos/…`, subcomisiones, trámites), los 4 PDF de Normativa, fuentes y logos.
- [ ] Buscar referencias residuales a `localhost` en el código fuente de páginas clave (Ver código fuente).
- [ ] **Correo:** configurar SMTP del hosting (plugin WP Mail SMTP o similar) y probar un formulario de Fluent Forms.
- [ ] **Seguridad:** cambiar contraseñas de los usuarios `admin` y `cipbaadmin` (vienen de local); revisar usuarios; `WP_DEBUG` en false; claves/salts nuevas en `wp-config.php`.
- [ ] **Caché:** activar LiteSpeed Cache (el hosting usa LiteSpeed).
- [ ] **Plugins:** verificar que ninguno tire errores con PHP 8.4 (fallback: bajar a 8.3 desde el panel).
- [ ] Backups programados de UpdraftPlus a almacenamiento externo.
- [ ] Indexación: staging → bloqueada; producción final → permitir y enviar sitemap (Rank Math) a Search Console.
- [ ] Nota: `/wp-content/uploads` y la Biblioteca de medios deben mostrar las imágenes/PDF.

## Solución de problemas
| Síntoma | Causa probable | Qué hacer |
|---|---|---|
| Pantalla blanca / error crítico | Plugin incompatible con PHP 8.4 o archivo mal subido | Renombrar `wp-content/plugins` a `plugins-off`, entrar, ir reactivando de a uno; o bajar PHP a 8.3 |
| "Error al establecer conexión con la base de datos" | Credenciales de `wp-config.php` o host de DB incorrectos | Revisar `DB_NAME/USER/PASSWORD/HOST` con los datos del panel |
| Bucle de redirecciones | SSL no activo o URLs en `http` | Activar SSL; confirmar `siteurl`/`home` en `https` |
| Contenido mixto / sin estilos | Quedaron URLs `http://localhost:8080` | Repetir Fase 1 buscando restos; o agregar temporalmente en `wp-config.php`: `define('WP_HOME','https://<DOMINIO>'); define('WP_SITEURL','https://<DOMINIO>');` |
| 404 en todas las páginas menos la home | `.htaccess` sin reglas de rewrite | Guardar Enlaces permanentes de nuevo |
| Menú sin estilos / distinto | CSS dinámico de Max Mega Menu | Mega Menu → Menu Themes → guardar una vez (regenera el CSS) |

## Ciclo de re-deploy (mientras se sigue desarrollando en local)
- **Solo cambió el tema (CSS/PHP):** subir por FTP `wordpress/themes/cipba/` completo. Antes, subir la constante `CHILD_THEME_CIPBA_VERSION` en `functions.php` para que el navegador no use CSS cacheado.
- **Cambió contenido, menús u opciones:** repetir Fases 1 y 6 (nueva base exportada e importada) y, si hubo cambios en plugins/uploads, Fases 2 y 5.
- **Desde que el sitio de producción tenga contenido real cargado en producción: no volver a importar la base.** A partir de ahí solo se sube código (tema) y los cambios de menús/CPT se replican a mano.

## Diferencias para la producción final (`cipba.org`)
1. Repetir Fases 0–7 con `<DOMINIO>` = `cipba.org` (exportar la base de nuevo con `https://cipba.org`; **no reutilizar** el `.sql` de `cipba.site`).
2. Antes de importar: DNS/delegación del dominio apuntando a DonWeb y SSL activo.
3. Definir redirecciones `http→https` y `www ↔ sin www`.
4. Permitir indexación, sitemap y Search Console.
5. Recordar cambiar todas las contraseñas y desactivar el modo "disuadir buscadores".
6. Mantener `cipba.site` como entorno de pruebas o eliminarlo.

## Registro de verificación
| Paso | Estado |
|---|---|
| Fase 1: `search-replace --export` | ✅ Probado 2026-09-19 (164 reemplazos, ≈1,9 MB, base local intacta) |
| Fase 2: `tar` con exclusiones (≈66 MB) | ✅ Probado 2026-09-19 (aviso "file changed as we read it" normal) |
| Fase 2: conversión a `.zip` con `tar -a` | ✅ Probado 2026-09-19 (70 MB, 9.527 entradas, exclusiones OK; tarda varios minutos en Windows por la cantidad de archivos; quedan solo `*-es_AR.*` de Duplicator en `languages/`, inofensivos) |
| Fases 3–7 en Ferozo | ⏳ Pendiente de ejecutar en `cipba.site` (los nombres de menús de Ferozo no están verificados) |
