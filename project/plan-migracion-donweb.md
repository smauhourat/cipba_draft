# Plan de migración: WordPress local (Docker) → DonWeb (Ferozo, sin SSH)

## Context
El sitio del Colegio de Ingenieros PBA – Distrito VII se construyó en local (Docker, `D:\Projects\wordpress_docker`). Se quiere publicarlo en DonWeb, plan con panel **Ferozo, sin SSH**. Estrategia elegida: **staging ahora + seguir desarrollando local**, y repetir el deploy cuando el home esté terminado. Como no hay WP-CLI en el hosting, el método es **plugin (UpdraftPlus, ya instalado) o Duplicator + FTP + phpMyAdmin**.

Estado relevado (solo lectura): PHP 8.3, MySQL 8.4.11, DB de solo 4,3 MB, 16 adjuntos, 4 páginas, 6 posts, 3 eventos, 3 subcomisiones, 52 ítems de menú. `siteurl`/`home` = `http://localhost:8080`. `permalink_structure` **vacía** (enlaces simples, hay que fijarla). Sitio chico: migración liviana.

## Principio clave para repetir deploys
Separar **código** (tema `cipba`, CPTs, CSS, snippets: viven en git/archivos y se pisan sin riesgo) de **contenido** (DB + uploads). Mientras el sitio local sea la fuente de verdad, cada deploy = copiar tema + reimportar DB. Cuando en producción se cargue contenido real, pasar a "solo subir código" y no volver a pisar la DB.

## Fase A — Preparación local (antes de tocar DonWeb)
1. ✅ **Lentitud arreglada** (2026-09-19, opción 2): `wp-content` ahora es el volumen nativo `wp_content`; solo `themes/cipba` sigue como bind mount desde Windows. Requests de 25–115 s → ~1.5–2.5 s. Backups previos en `D:\Projects\wordpress_docker\backups\` (DB `.sql` + copia de `wp-content`). Ojo: `wp-content/` en `D:\` (salvo el tema) ya NO es la copia viva; plugins/uploads viven en el volumen Docker.
2. ✅ Fijar permalinks a "Nombre de la entrada" (`/%postname%/`, hecho 2026-09-19 vía WP-CLI; `.htaccess` con reglas de rewrite escrito a mano porque WP-CLI no las genera; CPTs verificados: `/eventos/…`, `/subcomision/…`).
3. ✅ Pendientes que afectan el deploy: URL de "Vademécum" se deja como placeholder `#` por ahora (decisión del usuario, 2026-09-19); breakpoint 1160px de Max Mega Menu ya confirmado guardado.
4. ✅ (parcial: tema, compose, Dockerfile con WP-CLI y `plugins.txt` ya versionados en `wordpress/`; falta definir el paquete de deploy sin WP Reset) Commit del estado: `wp-content/themes/cipba` y los plugins con config propia en git. Quitar plugins solo-dev del paquete de deploy (**WP Reset**, y Code Snippets si no se usa en prod).
5. Confirmar versiones a pedir en el hosting: **PHP 8.1–8.3** y MySQL/MariaDB compatible (ojo: local usa MySQL 8.4; Ferozo suele ofrecer MariaDB/MySQL 5.7–8.0 → exportar sin features exclusivas de 8.4 y probar el import).

## Fase B — Preparar DonWeb (Ferozo)
1. Crear subdominio de staging (ej. `staging.<dominio>`) y **apuntarlo a una carpeta propia**.
2. Crear base de datos MySQL + usuario desde el panel; anotar host, nombre, usuario, clave.
3. Elegir versión de PHP compatible (≥ 8.1) desde el panel; revisar `memory_limit` (≥256M), `upload_max_filesize`, `max_execution_time` (Ferozo suele permitir ajustarlos vía `.htaccess`/`php.ini` del panel).
4. Activar SSL gratuito (Let's Encrypt) para el subdominio.
5. Bloquear acceso público al staging (contraseña de directorio o "Disuadir a buscadores") para que Google no indexe la copia.

## Fase C — Método de migración (sin SSH)
Opción recomendada: **Duplicator** (paquete `installer.php` + `archive.zip`) — soporta reemplazo de URL de forma segura (datos serializados) sin WP-CLI.
Alternativa: **UpdraftPlus** (ya instalado) backup → restaurar en instalación limpia de WP en DonWeb + plugin *Better Search Replace* para cambiar URLs.
Pasos con Duplicator:
1. Instalar/activar Duplicator local, crear paquete (excluir `wp-content/cache`, backups de UpdraftPlus, plugin WP Reset).
2. Subir `installer.php` + `archive.zip` por FTP/Administrador de archivos a la carpeta del staging (si el zip supera el límite del panel, subir por FTP con FileZilla).
3. Abrir `https://staging.<dominio>/installer.php`, ingresar datos de la DB de Ferozo, dejar que reemplace `http://localhost:8080` → URL del staging.
4. Al terminar: ejecutar el paso de limpieza y **borrar `installer.php`, archivos de instalación y logs**.

## Fase D — Post-migración (checklist)
1. Ajustes → Enlaces permanentes → **Guardar** (regenera `.htaccess`).
2. Verificar `siteurl`/`home` correctos, sin referencias residuales a `localhost:8080` (buscar en contenido, menús, Rank Math, Spectra).
3. Verificar: menú Principal (Max Mega Menu flyout, íconos externos, estado activo), footer de 4 columnas, CPTs (evento, documento, resolución, subcomisión, trámite), 4 PDFs de Normativa, fuentes WOFF2 y logos SVG del tema (rutas), Fluent Forms.
4. **Correo**: los formularios no van a mandar mail sin SMTP → configurar SMTP de DonWeb (plugin WP Mail SMTP o similar) y probar Fluent Forms.
5. Rendimiento: activar caché de página liviana + verificar OPcache; comprobar TTFB.
6. Seguridad: nuevo usuario admin con clave fuerte (no `cipbaadmin` con clave de local), quitar credenciales de dev, desactivar/eliminar plugins y temas sin uso (Astra sí; twentytwenty* no), configurar backups programados de UpdraftPlus a almacenamiento externo.
7. `wp-config.php` de producción: `WP_DEBUG` false, claves/salts nuevas, prefijo de tabla revisado.

## Fase E — Ciclo de re-deploy (mientras se sigue en local)
- **Solo cambios de código/CSS** (lo más común): subir por FTP `themes/cipba/` (recordar subir la constante `CHILD_THEME_CIPBA_VERSION` en `functions.php` para invalidar caché del navegador).
- **Cambios de contenido/menús/config** (hasta que haya contenido real en prod): nuevo paquete Duplicator → sobrescribir staging.
- Cuando se cargue contenido real en producción: la DB de producción pasa a ser la fuente de verdad y solo se despliega código; los cambios de menús/CPT nuevos se replican a mano.

## Fase F — Pasaje a producción
1. Dominio definitivo: repetir Fase C sobre el dominio real (o mover carpeta + search-replace de URL).
2. Apuntar DNS / delegación del dominio a DonWeb (TTL bajo antes del cambio), SSL, redirección www ↔ no-www y http → https.
3. Quitar bloqueo de indexación (Ajustes → Lectura) y configurar sitemap de Rank Math + Search Console.
4. Congelar staging o mantenerlo como entorno de pruebas.

## Riesgos / cosas a vigilar
- **Límites del plan Ferozo** (RAM, tiempo de ejecución, tamaño de subida): verificar antes; si el `archive.zip` no entra por el panel, usar FTP.
- **Compatibilidad MySQL 8.4 → MySQL/MariaDB del hosting**: probar el import temprano.
- **Datos serializados**: nunca reemplazar URLs con find/replace sobre el `.sql`.
- **Max Mega Menu**: sus estilos dinámicos y `megamenu_themes` viajan en la DB; verificar CSS generado en staging.
- **Sin SSH**: cualquier tarea antes hecha con WP-CLI (crear menús, `wp menu item update`) hay que hacerla desde wp-admin o con Code Snippets.

## Verificación end-to-end
- Staging abre en HTTPS sin contenido mixto ni referencias a `localhost`.
- Navegación completa (31 ítems del menú, 5 menús de footer) idéntica a local; capturas comparadas contra `project/index.html`.
- Formulario de prueba envía mail; PDFs y uploads cargan; permalinks sin 404.
- TTFB de la home < 1 s (con caché) en staging.

## Archivos/lugares críticos
- `D:\Projects\wordpress_docker\wp-content\themes\cipba\` (tema hijo, funciones, `style.css`, `inc/*.php`)
- `D:\Projects\wordpress_docker\docker-compose.yml`
- `project/plan-inicial-wp.md` (fuente de verdad del build; agregar aquí una "Fase 9 — Hosting" cuando se apruebe este plan)
