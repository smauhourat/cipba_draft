# Plan de limpieza: compromiso de seguridad en `cipba.site` (2026-09-28)

## Contexto del incidente
Apareció un usuario no creado por nosotros (`cipbaqx@yahoo.com`) en el WordPress deployado en `cipba.site` (DonWeb/Ferozo, sin SSH). `users_can_register` estaba en `0` en el último backup limpio (`deploy-cipba.site-20260927.sql`), así que no fue autoregistro — se creó con acceso ya comprometido (credenciales de admin filtradas/débiles y/o vulnerabilidad de plugin).

Hallazgos, en orden de aparición:
1. Usuario `cipbaqx@yahoo.com` no presente en ningún backup hasta el 27/09 → creado en las últimas 24-48hs antes de detectarlo.
2. Plugin **WP File Manager** instalado, nunca lo instalamos nosotros. Es el plugin de la explotación masiva CVE-2020-25213 (conector accesible sin login de WP en versiones vulnerables, da lectura/escritura de todo el filesystem).
3. Archivo `wp-signup.php` dentro de `wp-content/` (no pertenece ahí — es archivo de core de WP, vive en la raíz). Probable webshell disfrazado con nombre de archivo legítimo.
4. Carpetas `.well-known` repetidas en varios directorios (no solo la raíz) — patrón típico de malware de WP que abusa de la excepción a `.well-known` en reglas de hardening `.htaccess` (pensadas para no romper validación SSL de Let's Encrypt) para esconder backdoors adicionales.

Acciones ya tomadas por el usuario:
- Contraseñas de los 3 usuarios (`admin`, `cipbaadmin`, `cipbaeditor`) cambiadas.
- Usuario `cipbaqx@yahoo.com` borrado.
- Plugin WP File Manager desactivado (pendiente: borrar carpeta completa por FTP, no alcanza con desactivar).
- Contraseñas de FTP y del panel de Ferozo/DonWeb cambiadas.
- Borrados: `wp-file-manager`, `wp-content/wp-signup.php`, carpetas `.well-known` (aparecieron repetidas en varios directorios), carpeta `.tmb` en la raíz (caché de miniaturas de elFinder, confirma que el explorador de archivos se usó a nivel de todo el sitio), y carpeta `system` en la raíz (otra copia del mismo kit: `wp-signup.php` + `.well-known`).

## Causa raíz — investigación
**Hipótesis descartada:** se sospechó de **CVE-2024-9511** (FluentSMTP ≤ 2.2.82, PHP Object Injection sin autenticación → RCE con gadget chain de otro plugin). Se verificó la versión de FluentSMTP instalada en el sitio comprometido: **2.4.0** — posterior a la 2.2.83 donde se corrigió. También se descarta CVE-2025-24739 (CSRF, solo hasta 2.2.80). Con esa versión, FluentSMTP no era explotable por ninguna vulnerabilidad pública conocida. Fuentes: [SentinelOne — CVE-2024-9511](https://www.sentinelone.com/vulnerability-database/cve-2024-9511/), [CVEDetails — CVE-2024-9511](https://www.cvedetails.com/cve/CVE-2024-9511/), [Tenable — CVE-2025-24739](https://www.tenable.com/cve/CVE-2025-24739).

**Versiones instaladas en el sitio comprometido (confirmadas por el usuario, coinciden con `plugins.txt` del 19/09):** `code-snippets` 3.10.2, `fluentform` 6.2.14, `megamenu` 3.10.6, `meta-box` 5.15.1, `seo-by-rank-math` 1.0.278, `ultimate-addons-for-gutenberg` 2.20.3, `updraftplus` 1.26.7, `fluent-smtp` 2.4.0.

**Cruce contra CVEs conocidas (2026-09-28):**
| Plugin | Versión | Resultado |
|---|---|---|
| Rank Math | 1.0.278 | Parchado (RCE ≤1.0.276, escaladas ≤1.0.271/277) |
| Fluent Forms | 6.2.14 | Parchado (XSS ≤6.2.11) |
| Max Mega Menu | 3.10.6 | Sin CVE conocida |
| Meta Box | 5.15.1 | Certificado seguro (CleanTalk PSC-2026-65699) |
| Code Snippets | 3.10.2 | Parchado (injection <3.9.2, CSRF <3.9.5) |
| Ultimate Addons for Gutenberg | 2.20.3 | Parchado (varias ≤2.19.x/≤2.3.0) |
| FluentSMTP | 2.4.0 | Parchado (≤2.2.82 / ≤2.2.80) |
| **UpdraftPlus** | **1.26.7** | **CVE-2026-82841 (CVSS 5.3) aplica** |

## Conclusión
**Ningún plugin activo tenía una vulnerabilidad de RCE sin autenticación aplicable a la versión instalada.** Esto descarta la explotación remota de un plugin como vector de entrada más probable y **apunta de nuevo a credenciales de administrador comprometidas** (robadas, débiles, o `cipbaadmin` con la contraseña de local reusada — el ítem que había quedado pendiente en `plan-migracion-donweb.md`, línea 52). Con sesión de admin válida, instalar WP File Manager y plantar los backdoors vía su explorador es una acción autenticada normal, sin necesitar ninguna CVE.

**Hallazgo secundario real y aplicable:** `CVE-2026-82841` en UpdraftPlus ≤1.26.7 — falta de chequeo de capacidad que permite a cualquier usuario logueado (incluso Suscriptor) leer las credenciales del almacenamiento remoto de backups desde una página de admin. Se corrige en 1.26.8+. Como el usuario `lesabken` existió (Suscriptor), **es posible que se hayan leído esas credenciales** si UpdraftPlus tenía un destino remoto configurado (Google Drive/Dropbox/S3/FTP/etc.).

**Implicancias para la limpieza:**
- Actualizar **todos** los plugins a la última versión en el Docker local antes de armar el paquete de Duplicator (UpdraftPlus a 1.26.8+ en particular).
- **Rotar las credenciales del destino remoto de backup de UpdraftPlus** si hay uno configurado (posible exposición vía CVE-2026-82841).
- Reforzar: contraseñas de WP nunca reusadas entre local (Docker) y producción; usar un gestor de contraseñas para generar credenciales únicas por entorno.

## Línea de tiempo confirmada
- **2026-09-27 22:54** — momento exacto del compromiso, confirmado por mail de WordPress ("Registrado un nuevo usuario en tu sitio", usuario `lesabken` / `cipbaqx@yahoo.com`, enviado vía FluentSMTP desde `adhentux@gmail.com` — cuenta legítima del usuario, no fue reconfigurada por el atacante).
- El usuario se creó pese a `users_can_register = 0`: solo se explica si el atacante, ya con RCE vía CVE-2024-9511, llamó directamente a las funciones internas de WP (`wp_insert_user` / `wp_new_user_notification`) en vez de pasar por el formulario público de registro. Probablemente fue solo un paso de verificación del exploit ("¿tengo ejecución de código?"), no la vía principal de persistencia — esa fue el resto del kit (file manager, `wp-signup.php`, `.well-known`, `system`).
- El mail de "establecer contraseña" a `cipbaqx@yahoo.com` rebotó (mailbox inexistente) — no hay evidencia de que el atacante haya necesitado o usado esa cuenta más allá de crearla.
- Pendiente si el panel de Ferozo lo permite: revisar logs de acceso crudos alrededor de las 22:54 del 27/09 para identificar la IP/request que disparó el exploit.

**Decisión clave:** dado el número de vectores de persistencia encontrados, no se intenta limpiar archivo por archivo. Se hace un **redeploy completo desde cero**, reusando el proceso de migración ya documentado en `plan-migracion-donweb.md` (Duplicator + FTP + phpMyAdmin, sin WP-CLI/SSH). El Docker local sigue siendo la fuente de verdad; **no hay contenido nuevo en el sitio deployado que rescatar** — se confirmó con el usuario que no se cargó nada directamente en producción desde el último deploy limpio.

## Fase 0 — Contener
1. Proteger el sitio con contraseña de directorio desde el panel de Ferozo (o método equivalente) mientras se limpia.
2. Confirmar que ya se cambiaron, además de las contraseñas de WP: **FTP** y **panel de Ferozo/DonWeb**.

## Fase 1 — Evidencia (rápido, opcional)
1. Bajar por FTP una copia completa del sitio comprometido a una carpeta local aparte (ej. `C:\forensics\cipba-site-comprometido`), solo como respaldo/evidencia. No ejecutar nada de esa copia.

## Fase 2 — Preparar el paquete limpio en local
1. En el Docker local: confirmar contraseñas nuevas y fuertes para `admin`, `cipbaadmin`, `cipbaeditor` (generar otras nuevas si las que se usaron en el sitio comprometido no se consideran ya seguras, ya que no viajaron nunca por el sitio afectado).
2. Evaluar borrar/renombrar el usuario `admin` genérico si no se usa activamente (es el primer objetivo de cualquier ataque de fuerza bruta).
3. Actualizar todos los plugins a la última versión, **especialmente FluentSMTP** (causa raíz confirmada, CVE-2024-9511, corregido después de 2.2.82); revisar `wordpress/plugins.txt` y agregar FluentSMTP a la lista (faltaba).
4. Empaquetar con Duplicator local, mismos filtros de siempre: excluir `wp-reset`, `updraft`, `cache`.

## Fase 3 — Redeploy completo (pisar todo)
1. Por FTP: borrar el contenido completo del sitio en el servidor (con la copia de evidencia ya a salvo).
2. Crear una **base de datos nueva** (usuario/contraseña nuevos) desde el panel de Ferozo — no reusar la vieja, por si esas credenciales quedaron expuestas.
3. Subir `installer.php` + `archive.zip` a la carpeta vacía, correr el instalador apuntando a la DB nueva, dejar que reemplace la URL.
4. Borrar `installer.php` y todo archivo/log de instalación al terminar.

## Fase 4 — Endurecer
1. En `wp-config.php`:
   ```php
   define('DISALLOW_FILE_EDIT', true);
   define('DISALLOW_FILE_MODS', true);
   ```
   Bloquea instalar/editar plugins y temas desde wp-admin — el vector usado en este incidente.
2. Regenerar las claves secretas de WP si Duplicator no lo hace solo.
3. Instalar Wordfence (o equivalente) para escaneo de malware y bloqueo de fuerza bruta — sin SSH es la única forma práctica de vigilar integridad de archivos.
4. Si el panel de Ferozo lo permite: contraseña de directorio (`.htpasswd`) sobre `/wp-admin/` y `wp-login.php`, además del login de WP.
5. Confirmar que "Cualquiera puede registrarse" (Ajustes → General) siga desactivado.

## Fase 5 — Verificar
1. Sitio funcionando: menú (Max Mega Menu), Fluent Forms mandando mail, PDFs y uploads, permalinks sin 404.
2. Guardar el zip de `wp-content` y el dump de esta instalación limpia como **nuevo backup de referencia** (reemplaza a los del 19/23-09 como baseline).
3. Primeros días: revisar manualmente 1-2 veces por día lista de usuarios y lista de plugins activos.
4. Correr `Compare-WpContent.ps1` (script de diff por hash contra un zip de referencia) contra este nuevo baseline para confirmar que el redeploy quedó idéntico y para detectar cualquier reinfección temprana.

## Pendiente / a decidir
- Confirmar con soporte de DonWeb/Ferozo si tienen logs de acceso crudo (para identificar IP/momento exacto de la intrusión) y si el plan permite `.htpasswd` sobre `/wp-admin/`.
- Evaluar si migrar antes a un hosting con SSH/WP-CLI facilitaría el monitoreo futuro (fuera de alcance de este plan de limpieza puntual).
