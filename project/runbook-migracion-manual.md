# Runbook — Migración manual del WordPress local a DonWeb (Ferozo, sin SSH)

**Cuándo usarlo:** cuando el plugin (Duplicator / Migrador de Ferozo) no sirve, o para repetir el proceso de forma predecible.
**No depende de ningún plugin de backup ni de acceso SSH al hosting.**
Sirve igual para el ensayo (`cipba.site`) y para el destino final (`cipba.org`): solo cambia `<DOMINIO>`.

> **Si lo que estás haciendo es el redeploy posterior al compromiso de `cipba.site`**, seguí este runbook *y además* la sección **"Variante: reinstalación limpia post-incidente"** al final, que reemplaza las Fases 3–5 (hay que borrar todo y crear una base nueva) y agrega el endurecimiento. Contexto del incidente: `project/plan-limpieza-migracion.md`.

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
2. Confirmar que el sitio local está en el estado que se quiere publicar (menús, páginas, CPTs). Revisar en particular el contenido editable de la sección *"Contenido editable del sitio"* (más abajo): **Datos del Distrito** con los valores reales, **Fluent Forms → Entries** sin envíos de prueba, y los documentos de los trámites con su archivo subido.
3. No actualizar plugins/núcleo justo antes de migrar sin probarlos. **Excepción:** las actualizaciones de *seguridad* sí se aplican antes de empaquetar (ver 0-bis); después de aplicarlas hay que recorrer el sitio local y confirmar que nada se rompió.
4. Hacer un backup de seguridad de local por si algo sale mal:

   **Opción A — bash / Git Bash / WSL:**
   ```
   docker exec wordpress_db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --no-tablespaces --single-transaction wordpress > /tmp/backup-local.sql'
   docker cp wordpress_db:/tmp/backup-local.sql ./backups/backup-local-AAAAMMDD.sql
   ```

   **Opción B — PowerShell / cmd:** Windows no interpreta comillas simples como agrupador de argumentos, así que el comando de arriba se rompe (`unexpected EOF while looking for matching`). Usar `-r` de `mysqldump` para que escriba el archivo directo, sin `sh -c` ni redirección (reemplazar `<password>` por el valor de `DB_ROOT_PASSWORD` en `wordpress/.env`):
   ```powershell
   docker exec wordpress_db mysqldump -uroot -p<password> --no-tablespaces --single-transaction wordpress -r /tmp/backup-local.sql
   docker cp wordpress_db:/tmp/backup-local.sql .\backups\backup-local-AAAAMMDD.sql
   ```
   (`wordpress/backups/` está ignorado por git: los `.sql` contienen hashes de contraseñas.)

## Fase 0-bis — Chequeos de seguridad antes de empaquetar
Desde el incidente del 27/09 estos pasos son obligatorios: lo que se empaqueta acá es exactamente lo que va a correr en el servidor.

Los puntos 1, 2, 4 y 5 los reporta de una sola corrida el script `wordpress/scripts/chequeo-pre-deploy.php` (solo lee, no modifica nada). Marca con `!` lo que hay que atender:
```powershell
docker cp .\scripts\chequeo-pre-deploy.php wordpress_app:/tmp/chequeo-pre-deploy.php
docker exec wordpress_app wp --allow-root eval-file /tmp/chequeo-pre-deploy.php
```

1. **Actualizar los plugins con parches de seguridad.** Ver qué hay pendiente y actualizar:
   ```
   docker exec wordpress_app wp --allow-root plugin list --fields=name,status,version,update,update_version
   docker exec wordpress_app wp --allow-root plugin update <plugin> [<plugin>...]
   ```
   Mínimo obligatorio: **UpdraftPlus ≥ 1.26.8** (CVE-2026-82841: cualquier usuario logueado podía leer las credenciales del destino remoto de backup) y **FluentSMTP ≥ 2.4.1**.
2. **Verificar que no quedó nada del kit del atacante en local.** No debería aparecer nada:
   ```
   docker exec wordpress_app bash -c 'cd /var/www/html && find wp-content -maxdepth 2 \( -name "wp-signup.php" -o -name ".well-known" -o -name ".tmb" -o -name "wp-file-manager*" -o -name "system" \) -print'
   docker exec wordpress_app bash -c 'cd /var/www/html && find wp-content/uploads -type f -name "*.php*" -print'
   ```
   `wp-signup.php` es archivo de *core* y vive en la raíz: si aparece dentro de `wp-content/`, es una webshell. En `uploads/` el único `.htaccess` esperado es el de Astra (`ast-block-templates-json/.htaccess`); cualquier `.php` ahí adentro es sospechoso.
3. **Usuario `admin`:** el login genérico `admin` es el primer blanco de fuerza bruta. Ya fue renombrado en local (ver *Registro de verificación*); WP-CLI no permite cambiar `user_login`, se hace por base:
   ```php
   // wp eval-file: $wpdb->update( $wpdb->users, array( 'user_login' => '<nuevo>', 'user_nicename' => '<nuevo>' ), array( 'ID' => 1 ) );
   ```
   Los posts referencian al autor por `ID`, así que no se rompe nada. **Ojo:** `display_name` es un campo aparte — si quedó en `admin`, eso es lo que se ve como autor en el front.
4. **Vaciar las entradas de prueba del formulario** (Fluent Forms → Entries). Para contarlas sin salir de la consola:
   ```php
   // wp eval-file: global $wpdb; echo $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}fluentform_submissions" );
   ```
5. **Confirmar que el registro público está cerrado:** `users_can_register` debe ser `0` (`wp option get users_can_register`).
6. **Humo sobre el sitio local** después de las actualizaciones, buscando `Fatal error` / `Warning:` / `Deprecated:` en el HTML:
   ```powershell
   $urls = @('http://localhost:8080/','http://localhost:8080/novedades/','http://localhost:8080/institucional/','http://localhost:8080/contacto/','http://localhost:8080/honorarios/','http://localhost:8080/tramites/inscripcion/','http://localhost:8080/tramites/pago-matricula/')
   foreach ($u in $urls) { $r = Invoke-WebRequest -Uri $u -UseBasicParsing; "{0,-50} {1} fatal/warn:{2}" -f $u,$r.StatusCode,($r.Content -match 'Fatal error|Warning:|Deprecated:') }
   ```

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
`wp-content` vive en un volumen Docker (el tema `cipba` está montado desde `wordpress/themes/cipba`; el paquete lo incluye).

**El `.zip` se arma adentro del contenedor, con PHP.** No se pasa por Windows: las rutas de `vendor/` de UpdraftPlus, FluentSMTP y Rank Math ya superan los 260 caracteres de `MAX_PATH`, así que PowerShell 5.1 no puede ni enumerarlas ni borrarlas (`Could not find a part of the path…`) y cualquier paso intermedio en `C:\` o `D:\` deja archivos afuera en silencio. Tampoco sirve `Compress-Archive`: genera rutas con `\` que se rompen al extraer en Linux.

1. Escribir `wordpress/scripts/zip-wp-content.php` (ya versionado en el repo) y correrlo en el contenedor:
   ```powershell
   docker cp .\scripts\zip-wp-content.php wordpress_app:/tmp/zip-wp-content.php
   docker exec wordpress_app php -d memory_limit=1G /tmp/zip-wp-content.php
   docker cp wordpress_app:/tmp/wp-content.zip .\backups\wp-content-<DOMINIO>-AAAAMMDD.zip
   ```
   El script imprime `archivos / carpetas / excluidos / tamaño`; las exclusiones están adentro: `cache`, `upgrade`, **`upgrade-temp-backup`**, `updraft`, `duplicator-backups`, `plugins/wp-reset`, `plugins/duplicator`.
2. **`upgrade-temp-backup` es importante:** cada `plugin update` deja ahí una copia de la versión *anterior* del plugin. Sin esa exclusión, el paquete se lleva al servidor justo la versión vulnerable que se acaba de reemplazar.
3. Verificar el zip antes de subirlo — entradas, que no haya rutas con `\`, y que estén el tema y los plugins:
   ```
   docker exec wordpress_app php -r '$z=new ZipArchive();$z->open("/tmp/wp-content.zip");echo $z->numFiles." entradas\n";var_dump($z->locateName("wp-content/themes/cipba/style.css")!==false);'
   ```
   Contrastar `numFiles` contra el conteo que imprimió el script (`archivos + carpetas`).

Opcionalmente, para tener también un `.tar.gz` (útil como copia nativa de Linux, no se sube al hosting):
```
docker exec wordpress_app tar -czf /tmp/wp-content.tar.gz -C /var/www/html \
  --exclude=wp-content/cache --exclude=wp-content/upgrade --exclude=wp-content/upgrade-temp-backup \
  --exclude=wp-content/updraft --exclude=wp-content/duplicator-backups \
  --exclude=wp-content/plugins/wp-reset --exclude=wp-content/plugins/duplicator wp-content
docker cp wordpress_app:/tmp/wp-content.tar.gz ./backups/wp-content-AAAAMMDD.tar.gz
```
- Desde **Git Bash**, cualquier `docker exec … /ruta/absoluta` necesita `export MSYS_NO_PATHCONV=1` adelante; si no, MSYS traduce `/var/www/html` a `C:/Program Files/Git/var/www/html` y el comando falla.
- `tar: file changed as we read it` es un aviso normal; no invalida el paquete.
- Tamaño de referencia (02/10/2026): **88,7 MB** el `.zip`, 84,5 MB el `.tar.gz`, 10.884 entradas. Límite de subida del hosting: 128 MB — el margen se está achicando (70 MB en septiembre).
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
5. Permisos: carpetas 755, archivos 644 (que WordPress pueda escribir en `uploads`). Se hace en el hosting, no hay SSH:
   - **Administrador de archivos de Ferozo:** clic derecho sobre `wp-content` → "Permisos"/"Cambiar permisos". Si deja poner un valor numérico con opción "recursivo", usar `755`. Ojo: si el panel aplica ese valor por igual a carpetas y archivos (sin distinguir), no alcanza — pasar a la opción de FTP.
   - **FTP (FileZilla), más preciso:** clic derecho sobre `wp-content` → "Permisos de archivo..." → `755`, tildar "Recurse into subdirectories" → "Apply to directories only". Repetir con `644` → "Apply to files only". Así carpetas y archivos quedan cada uno con su valor.
   - Verificar en `wp-content/uploads/`: la carpeta en `755`, un archivo cualquiera adentro en `644`.
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
- [ ] Verificar las páginas y contenidos editables (detalle en *"Contenido editable del sitio"*): `/institucional/`, `/subcomisiones/`, `/contacto/`, `/tramites/inscripcion/`, `/tramites/rehabilitacion/`, `/tramites/baja/` y `/tramites/solicitud-credenciales/`; la barra superior; y que el header quede fijo al hacer scroll.
- [ ] **Datos del Distrito** (panel → Datos del Distrito): confirmar teléfono, correo, horario, WhatsApp, enlace al Consejo Superior, redes (Facebook y LinkedIn están vacías: sus íconos no se muestran hasta cargar la URL), aviso de honorarios y los datos de *Matrícula y trámites*. Confirmar cuál es el teléfono general (la barra superior usa el de este formulario; la sede San Justo tiene el suyo).
- [ ] **Documentos de los trámites:** abrir cada trámite y descargar todos sus documentos (deben salir de `/wp-content/uploads/`, con tipo y peso visibles, sin enlaces a `colegioingenieros.org.ar`).
- [ ] Buscar referencias residuales a `localhost` en el código fuente de páginas clave (Ver código fuente).
- [ ] **Correo:** configurar SMTP del hosting (plugin WP Mail SMTP o similar) y probar un formulario de Fluent Forms. Sin SMTP los mensajes de `/contacto/` **se guardan** (Fluent Forms → Entries) pero **no llegan por mail**.
- [ ] **Formulario de Contacto (Fluent Forms):** el aviso por correo se envía al destinatario cargado en el formulario (hoy `info@cipba.org`), **no** al correo de Datos del Distrito. Revisarlo en Fluent Forms → *Contacto CIPBA* → Configuración → Notificaciones por correo. Enviar una consulta de prueba y **borrar esa entrada** después.
- [ ] **Akismet:** activarlo ahora que hay un formulario público (el formulario solo tiene la protección básica de Fluent Forms).
- [ ] **Seguridad:** cambiar las contraseñas de **todos** los usuarios (vienen de local, y nunca deben ser las mismas entre local y producción — usar un gestor de contraseñas); revisar la lista de usuarios; `WP_DEBUG` en false; claves/salts nuevas en `wp-config.php`. Detalle completo en *"Variante: reinstalación limpia post-incidente"*.
- [ ] **Caché:** activar LiteSpeed Cache (el hosting usa LiteSpeed) **y purgarla** después de importar la base — el hosting ya venía cacheando la instalación default de WP desde antes de migrar, así que a los visitantes sin login (logueado en el wp-admin no se nota, esa vista no usa caché) les sigue apareciendo la versión vieja hasta que se purga. LiteSpeed Cache → Toolbox → Purge All (o el ícono del tacho en la barra de admin). Repetir cada vez que se reimporte la base o se suba un cambio de tema/contenido en el ciclo de re-deploy.
- [ ] **Plugins:** verificar que ninguno tire errores con PHP 8.4 (fallback: bajar a 8.3 desde el panel).
  - Recorrer wp-admin y el front (home, cada CPT, Fluent Forms, Rank Math, Mega Menu) buscando `Warning:`, `Deprecated:` o `Fatal error:` mezclado en la página.
  - Activar el log sin mostrarlo al público: en `wp-config.php`, `define('WP_DEBUG', true); define('WP_DEBUG_LOG', true); define('WP_DEBUG_DISPLAY', false);`. Navegar el sitio y después revisar `wp-content/debug.log` buscando rutas de `wp-content/plugins/<nombre>/...`. Volver `WP_DEBUG` a `false` al terminar.
  - Revisar también el log de errores de PHP del hosting (panel de Ferozo / `error_log` en la raíz del dominio): captura fatales que tiran pantalla blanca antes de que WordPress llegue a loguear nada.
  - Herramientas → Salud del sitio → pestaña Info: lista funciones deprecadas de PHP en uso (no exhaustivo).
  - Si algo da error crítico: bajar PHP a 8.3 desde el panel, o desactivar plugins de a uno renombrando la carpeta (ver tabla de Solución de problemas).
- [ ] Backups programados de UpdraftPlus a almacenamiento externo.
- [ ] Indexación: staging → bloqueada; producción final → permitir y enviar sitemap (Rank Math) a Search Console.
- [ ] Nota: `/wp-content/uploads` y la Biblioteca de medios deben mostrar las imágenes/PDF.

## Contenido editable del sitio (qué viaja y cómo)
Desde el 2026-09-20 el sitio tiene contenido que se carga desde el panel y **no está en el repositorio git** (git solo tiene el código del tema). Todo esto viaja con la **base de datos (Fase 1/6)** o con **`wp-content` (Fase 2/5)**; no hay nada que exportar aparte.

| Contenido | Dónde vive | Cómo viaja |
|---|---|---|
| Subcomisiones, Sedes, Autoridades, Áreas de contacto, Trámites, Documentos (títulos, textos, campos) | Base de datos: tabla `wp_posts` + `wp_postmeta` | Base (Fase 1 → 6) |
| Páginas Institucional, Subcomisiones, Contacto (y la home) | Base de datos (`wp_posts`) | Base |
| **Datos del Distrito** (teléfono, WhatsApp, redes, honorarios, resolución, montos, códigos de formularios…) | Base de datos: opción `cipba_distrito` en `wp_options` | Base |
| Formulario de Contacto y sus **envíos** (entradas) | Base de datos: tablas `wp_fluentform_*` | Base |
| **Documentos descargables** de los trámites (PDF/DOCX) | Archivos en `wp-content/uploads/` + referencia en la base | `wp-content` (Fase 2 → 5) **y** base |
| Resoluciones de honorarios (`/honorarios/`) y sus **PDF** (resolución y anexos, ≈ 7 MB) | Base (`wp_posts` + `wp_postmeta`) + archivos en `wp-content/uploads/` | Base **y** `wp-content` (mismo caso que los documentos de trámites) |
| Links de interés del pie de página (`link_interes`) | Base de datos (`wp_posts` + `wp_postmeta`) | Base |
| Página TAD (`/tad/`): documentos elegidos y mail (campos de la página) + los 3 PDF (Res. 1433, Disp. 80, formulario) | Base (`wp_posts` + `wp_postmeta`) + archivos en `wp-content/uploads/` | Base **y** `wp-content`. Ojo: el enlace de la tarjeta TAD de la home (`/tad/`) también está en la base (página Inicio) |
| Menús (los ítems apuntan a `/tramites/…/`, `/contacto/`, etc.) | Base de datos | Base |

Cosas a tener en cuenta:
- **Los archivos y la base tienen que ir juntos.** Un documento existe como *archivo en uploads* **y** como fila en la base. Si se sube solo una de las dos partes, el trámite muestra el documento sin archivo (o no lo muestra). Con las Fases 1–2 hechas el mismo día no hay problema; en un re-deploy parcial, subir ambas.
- **Los enlaces a las páginas se generan con las URLs de la base.** El reemplazo de `localhost:8080` de la Fase 1 (con `--precise`) alcanza también al contenido nuevo; no hace falta tocar nada a mano.
- **El formulario se inserta con `[fluentform id="3"]`** en la página Contacto. Al importar la base, los IDs se conservan y funciona igual. **Si en cambio se recrea el formulario a mano en producción**, el ID cambia y hay que corregir el número en la página Contacto.
- **Plugin Meta Box:** el tema solo carga los campos del formulario si el plugin *Meta Box* está activo. Viaja dentro de `wp-content/plugins/`; después de importar, confirmar que sigue **activado** (Plugins). Sin él, las pantallas de edición de Sedes, Trámites, etc. aparecen sin campos.
- **Enlaces permanentes:** los trámites usan la base `/tramites/…`. Guardar *Ajustes → Enlaces permanentes* (Fase 7) regenera esas reglas; si `/tramites/inscripcion/` da 404, es esto.
- **Entradas de prueba del formulario:** antes de exportar, vaciar *Fluent Forms → Entries* para no llevar consultas de prueba a producción.
- **El instructivo para quien carga el contenido** está en `project/instructivo-tramites.md`.

## Solución de problemas
| Síntoma | Causa probable | Qué hacer |
|---|---|---|
| Pantalla blanca / error crítico | Plugin incompatible con PHP 8.4 o archivo mal subido | Renombrar `wp-content/plugins` a `plugins-off`, entrar, ir reactivando de a uno; o bajar PHP a 8.3 |
| "Error al establecer conexión con la base de datos" | Credenciales de `wp-config.php` o host de DB incorrectos | Revisar `DB_NAME/USER/PASSWORD/HOST` con los datos del panel |
| Bucle de redirecciones | SSL no activo o URLs en `http` | Activar SSL; confirmar `siteurl`/`home` en `https` |
| Contenido mixto / sin estilos | Quedaron URLs `http://localhost:8080` | Repetir Fase 1 buscando restos; o agregar temporalmente en `wp-config.php`: `define('WP_HOME','https://<DOMINIO>'); define('WP_SITEURL','https://<DOMINIO>');` |
| 404 en todas las páginas menos la home | `.htaccess` sin reglas de rewrite | Guardar Enlaces permanentes de nuevo |
| Menú sin estilos / distinto | CSS dinámico de Max Mega Menu | Mega Menu → Menu Themes → guardar una vez (regenera el CSS) |
| Logueado en el wp-admin se ve bien, pero de incógnito/otro dispositivo se ve la instalación vieja de WP | Caché de LiteSpeed sirviendo una copia vieja a visitantes sin login (el logueado no pasa por caché) | LiteSpeed Cache → Toolbox → Purge All. Repetir después de cada reimportación de base o cambio de contenido/tema |
| `/tramites/inscripcion/` (u otro trámite) da 404 | Reglas de reescritura sin regenerar | Ajustes → Enlaces permanentes → Guardar |
| Un trámite muestra un documento vacío o sin descarga, o el documento 404 | Se subió la base pero no `wp-content/uploads` (o al revés) | Repetir Fase 2 y 5 (archivos) o Fase 1 y 6 (base) para que ambas partes coincidan |
| Al editar Sedes/Trámites/Autoridades no aparecen los campos | Plugin *Meta Box* desactivado tras la importación | Plugins → activar *Meta Box* |
| La página Contacto muestra el formulario vacío o un error de Fluent Forms | El formulario tiene otro ID en producción | Corregir el `id` del shortcode `[fluentform id="…"]` en la página Contacto |
| El formulario dice "enviado" pero no llega el mail | Falta configurar SMTP en el hosting | Configurar SMTP (Fase 7). Mientras tanto, los mensajes se leen en Fluent Forms → Entries |

## Ciclo de re-deploy (mientras se sigue desarrollando en local)
- **Solo cambió el tema (CSS/PHP):** subir por FTP `wordpress/themes/cipba/` completo. Antes, subir la constante `CHILD_THEME_CIPBA_VERSION` en `functions.php` para que el navegador no use CSS cacheado.
- **Cambió contenido, menús u opciones:** repetir Fases 1 y 6 (nueva base exportada e importada) y, si hubo cambios en plugins/uploads, Fases 2 y 5.
- **Desde que el sitio de producción tenga contenido real cargado en producción: no volver a importar la base.** A partir de ahí solo se sube código (tema) y los cambios de menús/CPT se replican a mano.
- **Ojo con las consultas del formulario de Contacto:** las entradas de Fluent Forms viven en la base. Reimportar la base en un sitio que ya recibió consultas reales las **borra**. Antes de cualquier reimportación en un sitio en uso, exportar las entradas (Fluent Forms → Entries → Export) o directamente no importar.
- **Contenido que se edita en producción** (Datos del Distrito, trámites, documentos, sedes…) tampoco debe pisarse con una base de local; los cambios de código del tema (`single-tramite.php`, `inc/`, `style.css`) sí se suben por FTP sin tocar la base.

## Variante: reinstalación limpia post-incidente
Para el redeploy de `cipba.site` después del compromiso del 27/09/2026 (contexto y hallazgos: `project/plan-limpieza-migracion.md`). La idea es **no limpiar archivo por archivo**: se encontraron varios mecanismos de persistencia (WP File Manager, `wp-content/wp-signup.php`, carpetas `.well-known` repetidas, `.tmb`, carpeta `system`), así que se pisa todo. Local es la fuente de verdad y **no hay contenido nuevo cargado en producción que rescatar** (confirmado con el usuario).

Se corren las Fases 0, 0-bis, 1 y 2 tal cual. Lo que cambia son las Fases 3–5, y después se endurece.

### Antes de tocar nada
- [ ] Sitio protegido con contraseña de directorio desde el panel de Ferozo mientras dura la limpieza.
- [ ] Confirmado que ya se cambiaron las contraseñas de **WP**, **FTP** y **panel de Ferozo/DonWeb**.
- [ ] (Opcional, evidencia) Bajar por FTP una copia completa del sitio comprometido a una carpeta aparte, p. ej. `C:\forensics\cipba-site-comprometido`. **No ejecutar nada de esa copia.**
- [ ] Si UpdraftPlus tenía un destino remoto configurado (Drive/Dropbox/S3/FTP): **rotar esas credenciales**. El sitio corría UpdraftPlus 1.26.7, afectado por CVE-2026-82841, que permitía a cualquier usuario logueado leerlas — y existió un usuario Suscriptor creado por el atacante.

### Fase 3' — Hosting desde cero
1. Por FTP, **borrar el contenido completo** de la carpeta del dominio (con la copia de evidencia ya a salvo). Incluye archivos ocultos: `.htaccess`, `.well-known`, `.tmb`.
2. Crear una **base de datos nueva**, con usuario y contraseña nuevos. **No reusar la vieja**: esas credenciales pueden haber quedado expuestas.
3. PHP 8.4 (fallback 8.3), `memory_limit` 256M, subida 128M — igual que la Fase 3 normal.
4. SSL activo antes de abrir el sitio por `https://`.

### Fase 4' — WordPress limpio
Igual que la Fase 4, pero bajando el núcleo **de wordpress.org**, no reutilizando archivos del sitio anterior. Misma versión mayor que local (`docker exec wordpress_app wp --allow-root core version`) e idioma `es_AR`. Prefijo de tablas `wp_`.

### Fase 5' — Subir el paquete
Igual que la Fase 5. Como la carpeta quedó vacía, no hay `wp-content` previo que renombrar: se sube el `.zip` y se extrae directo. Permisos 755/644 como siempre.

### Fase 8 — Endurecer (nueva, después de la Fase 7)
1. En `wp-config.php`:
   ```php
   define( 'DISALLOW_FILE_EDIT', true );
   define( 'DISALLOW_FILE_MODS', true );
   ```
   `DISALLOW_FILE_EDIT` saca el editor de temas/plugins de wp-admin; `DISALLOW_FILE_MODS` bloquea instalar y actualizar plugins desde el panel — exactamente el vector de este incidente (con sesión de admin válida, instalar WP File Manager y plantar backdoors es una acción autenticada normal, no hace falta ninguna CVE).
   **Contrapartida:** con `DISALLOW_FILE_MODS` las actualizaciones automáticas de plugins tampoco corren. A partir de ahí, actualizar = actualizar en local y repetir Fases 0-bis/2/5. Si se prefiere poder actualizar desde el panel, dejar solo `DISALLOW_FILE_EDIT`.
2. Claves y salts nuevos en `wp-config.php` (generarlos en `https://api.wordpress.org/secret-key/1.1/salt/`). Invalida cualquier cookie de sesión que el atacante tuviera.
3. Instalar **Wordfence** (o equivalente): escaneo de integridad de archivos y bloqueo de fuerza bruta. Sin SSH es la única forma práctica de vigilar que no vuelvan a aparecer archivos.
4. Si el plan de Ferozo lo permite: contraseña de directorio (`.htpasswd`) sobre `/wp-admin/` y `wp-login.php`, además del login de WP.
5. Confirmar que *Ajustes → General → Cualquiera puede registrarse* sigue desactivado (en el incidente el usuario se creó igual, por código, pero es gratis confirmarlo).
6. Revisar la lista de plugins instalados y **borrar los que estén inactivos y no se vayan a usar** (hoy viajan en el paquete: `akismet`, `custom-post-type-ui`, `mystickymenu`, `pdf-embedder`). Un plugin inactivo igual tiene sus archivos PHP accesibles por URL: es superficie de ataque sin contrapartida. Excepción: `akismet` se va a activar (Fase 7).

### Fase 9 — Vigilancia los primeros días
1. Guardar el `.zip` de `wp-content` y el `.sql` de esta instalación como **nuevo baseline de referencia** (reemplaza a los del 19 y 23/09).
2. 1 o 2 veces por día durante la primera semana: revisar la **lista de usuarios** y la **lista de plugins activos**.
3. Comparar por hash el `wp-content` del servidor contra el baseline. ⚠️ El script `Compare-WpContent.ps1` que menciona el plan de limpieza **todavía no existe**; además, hacer ese diff en Windows choca con el mismo `MAX_PATH` de la Fase 2, así que conviene que compare el listado de hashes *dentro* del zip de referencia contra un listado traído del servidor, sin extraer nada a disco.

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
| Fase 1: `search-replace --export` | ✅ Probado 2026-09-19 (164 reemplazos), 2026-10-02 (279 reemplazos) y **2026-10-04 (304 reemplazos, 1,4 MB, 0 restos de `localhost:8080`, base local intacta)**. Quedan 4 `wordpress@localhost` en `wp_fluentform_logs` (logs de mails fallidos de pruebas viejas): inofensivos |
| Fase 2: `tar` con exclusiones | ✅ Probado 2026-09-19 (≈66 MB) y 2026-10-02 (84,5 MB). Aviso "file changed as we read it" normal |
| Fase 2: conversión a `.zip` con `tar -a` en Windows | ❌ **Descartado el 2026-10-02.** Las rutas de `vendor/` pasan `MAX_PATH` y PowerShell 5.1 no las puede enumerar ni borrar; el método no es confiable para contar ni verificar el paquete |
| Fase 2: `.zip` con `scripts/zip-wp-content.php` en el contenedor | ✅ Probado 2026-10-02 (88,7 MB, 9.033 archivos + 1.851 carpetas = 10.884 entradas, coincide exacto con el `.tar.gz`; 0 rutas con `\`) |
| Paquete 2026-10-04 | ✅ Fases 0, 0-bis, 1 y 2 rehechas (incluye Normativa, TAD y los campos nuevos de novedades). Zip 91,0 MB, 9.053 archivos + 1.851 carpetas = 10.904 entradas, 0 rutas con `\`. Humo: 10 URLs en 200 sin errores. **Reemplaza al paquete del 2026-10-02** |
| Fase 0-bis: `scripts/chequeo-pre-deploy.php` | ✅ Probado 2026-10-02 |
| Fase 0-bis: UpdraftPlus 1.26.8 / FluentSMTP 2.4.1 | ✅ Actualizados 2026-10-02; humo sobre 7 URLs locales en 200 sin `Fatal error`/`Warning`/`Deprecated` |
| Fase 0-bis: renombrar `admin` | ✅ Hecho 2026-10-02 — `admin` (ID 1) pasó a `smauhourat` (`user_login` + `user_nicename`). ⚠️ `display_name` quedó en `admin` |
| Fases 3–7 en Ferozo | ⏳ Pendiente de ejecutar en `cipba.site` (los nombres de menús de Ferozo no están verificados) |
| Fases 3'–5', 8 y 9 (reinstalación limpia) | ⏳ Pendiente. Paquete del 2026-10-02 listo en `wordpress/backups/` |
| Contenido editable nuevo (Institucional, Subcomisiones, Contacto, trámites, documentos, Datos del Distrito, formulario) | ⏳ Pendiente de verificar en `cipba.site` con el checklist de la Fase 7 y la sección *"Contenido editable del sitio"*. Local ya probado (2026-09-20): páginas, marcadores, formulario con guardado de entradas y descarga de documentos desde uploads |

### Paquete preparado el 2026-10-04 (vigente para `cipba.site`)
En `wordpress/backups/`: `backup-local-20261004.sql` (1,39 MB, backup previo), **`deploy-cipba.site-20261004.sql`** (1,4 MB, para phpMyAdmin) y **`wp-content-cipba.site-20261004.zip`** (91,0 MB / 95,4 MB en disco, 10.904 entradas, para extraer en la raíz). Mismas versiones de plugins y núcleo que el del 2026-10-02 (abajo); las actualizaciones pendientes siguen sin aplicar.

### Paquete preparado el 2026-10-02 (reinstalación limpia de `cipba.site`) — reemplazado por el del 2026-10-04
En `wordpress/backups/` (ignorado por git):

| Archivo | Qué es |
|---|---|
| `backup-local-20261002.sql` (1,29 MB) | Backup de la base local **antes** de tocar nada (Fase 0.4) |
| `deploy-cipba.site-20261002.sql` (1,3 MB) | **Para importar por phpMyAdmin.** URLs ya en `https://cipba.site` |
| `wp-content-cipba.site-20261002.zip` (88,7 MB) | **Para subir y extraer en la raíz del dominio.** 10.884 entradas |
| `wp-content-20261002.tar.gz` (84,5 MB) | Copia nativa de Linux del mismo contenido; no se sube al hosting |

Versiones de este paquete: WP core **7.1.2**, tema `cipba`, UpdraftPlus **1.26.8**, FluentSMTP **2.4.1**. Quedaron **sin** actualizar, por decisión explícita de no meter cambios no probados: `fluentform` 6.2.14→6.2.15, `megamenu` 3.10.6→3.10.8, `seo-by-rank-math` 1.0.278→1.0.279, `ultimate-addons-for-gutenberg` 2.20.3→2.20.4, `akismet` 5.7→5.7.2, `mystickymenu` 2.9.1→2.9.3. Ninguno tiene CVE conocida aplicable a la versión instalada (cruce del 28/09 en `plan-limpieza-migracion.md`), pero conviene ponerlos al día en el próximo ciclo.
