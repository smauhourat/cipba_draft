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
2. Confirmar que el sitio local está en el estado que se quiere publicar (menús, páginas, CPTs). Revisar en particular el contenido editable de la sección *"Contenido editable del sitio"* (más abajo): **Datos del Distrito** con los valores reales, **Fluent Forms → Entries** sin envíos de prueba, y los documentos de los trámites con su archivo subido.
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
- [ ] **Seguridad:** cambiar contraseñas de los usuarios `admin` y `cipbaadmin` (vienen de local); revisar usuarios; `WP_DEBUG` en false; claves/salts nuevas en `wp-config.php`.
- [ ] **Caché:** activar LiteSpeed Cache (el hosting usa LiteSpeed) **y purgarla** después de importar la base — el hosting ya venía cacheando la instalación default de WP desde antes de migrar, así que a los visitantes sin login (logueado en el wp-admin no se nota, esa vista no usa caché) les sigue apareciendo la versión vieja hasta que se purga. LiteSpeed Cache → Toolbox → Purge All (o el ícono del tacho en la barra de admin). Repetir cada vez que se reimporte la base o se suba un cambio de tema/contenido en el ciclo de re-deploy.
- [ ] **Plugins:** verificar que ninguno tire errores con PHP 8.4 (fallback: bajar a 8.3 desde el panel).
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
| Contenido editable nuevo (Institucional, Subcomisiones, Contacto, trámites, documentos, Datos del Distrito, formulario) | ⏳ Pendiente de verificar en `cipba.site` con el checklist de la Fase 7 y la sección *"Contenido editable del sitio"*. Local ya probado (2026-09-20): páginas, marcadores, formulario con guardado de entradas y descarga de documentos desde uploads |
