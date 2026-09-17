# Plan inicial — Implementación WordPress CIPBA Distrito VII

Basado en `Handoff WordPress - CIPBA Distrito VII.md`. Este plan cubre desde cero hasta tener un WordPress local configurado y listo para empezar a armar secciones (§4 del handoff). No incluye todavía el armado de cada sección — eso es la fase siguiente.

**Entorno elegido:** WordPress local en Docker (ya instalado). Se migra a hosting definitivo más adelante (fuera de alcance de este plan).

---

## Estado actual (relevado 2026-09-17)

Antes de asumir que se arranca de cero, esto es lo que ya existe en `d:\Projects\wordpress_docker`:

- **Docker:** `wordpress:latest` (PHP 8.3.33), MySQL 8.4, phpMyAdmin. `wp-content` ahora está en bind mount a `d:\Projects\wordpress_docker\wp-content` (se hizo en esta sesión — antes era un volumen Docker sin carpeta local editable).
- **Tema:** ya existe un tema hijo de Astra llamado **`cipba`** (no `astra-cipba`) en `wp-content/themes/cipba/`. Está en estado inicial: boilerplate estándar, sin `theme.json`, con tres template-parts vacíos (`autoridades-list.php`, `sedes-list.php`, `subcomisiones-list.php`, 0 bytes). **Todavía no está activo** — el tema activo hoy es el por defecto `twentytwentyfive`.
- **Plugins:** se decidió seguir el camino recomendado por el handoff (bloques + Spectra, no Elementor). En esta sesión se **desinstalaron** `elementor`, `header-footer-elementor` y `smart-slider-3` (ninguno estaba activo, no había contenido construido con ellos) y se **instaló Spectra** (`ultimate-addons-for-gutenberg`), pendiente de activar desde el admin.
  - Ya instalados y sin tocar: `megamenu` (Max Mega Menu), `mystickymenu` (Sticky Menu on Scroll), `custom-post-type-ui`, `pdf-embedder`, `akismet`, `wp-reset`. Ninguno está activo todavía (solo `wp-reset` lo estaba).
- **WordPress:** instalación fresca — `siteurl`/`home` fijos en `http://localhost:8080` (no hay riesgo de que cambie el puerto), pero el contenido es el de ejemplo por defecto (página de ejemplo, "¡Hola mundo!", política de privacidad en borrador). No hay usuario admin propio configurado todavía (no se relevaron credenciales).

Las fases de abajo se ajustaron para reflejar esto: lo ya resuelto está marcado, y donde el plan original decía "crear" ahora dice "activar" o "completar".

---

## Decisiones adoptadas por defecto

El handoff deja 8 decisiones abiertas en su §9. Para no bloquear el arranque, este plan asume las opciones que el propio handoff recomienda. Si alguna no es la que se quiere, avisar antes de la Fase 3 (son costosas de cambiar después):

| Decisión | Default adoptado | Por qué |
|---|---|---|
| Editor principal | Bloques nativos + Spectra | Evita duplicar tokens entre `theme.json` y Elementor (§1) |
| Repeaters (`anexos`, `referentes`) | Meta Box (free, trae repeater) | ACF free no tiene repeaters |
| Trámites | CPT `tramite` | Plantilla única, "Otros trámites" se autogenera |
| Eventos | Grilla de 3 (Query Loop), no carrusel | Volumen bajo hoy; cero dependencia nueva |
| Filtros de novedades | Cliente (JS), no plugin | Volumen bajo (<60), más fiel al diseño |
| PDFs de honorarios históricos | Enlace al Consejo Superior (no alojados localmente) | Evita responsabilidad de mantenerlos actualizados |
| Tipografías | Locales (WOFF2 en el tema), no Google Fonts CDN | Ya están definidas en `theme.json` del handoff |
| Breadcrumbs | Astra free (no Rank Math) | Evita marcado estructurado duplicado |

---

## Fase 0 — Preparación de assets (antes de instalar nada)

El §7 del handoff pide assets que hoy no están listos como archivos sueltos — están dentro del prototipo estático (`project/`). Extraer antes de empezar evita bloquear la Fase 3:

- [x] Isologo horizontal en SVG + PNG@2x (navbar) y variante monocroma blanca para el pie. No había SVG original en el proyecto (solo los PNG en `uploads/`); se generó por autotrace (VTracer) a partir de `logo-cipba-navbar.png` y `Logo-blanco.png`, y se normalizaron los colores del trazo a los tokens exactos `#14484A` (verde-700) y `#00A48A` (verde-500) — el autotrace producía ~25 tonos casi idénticos por el antialiasing del PNG. Copiado a `wp-content/themes/cipba/assets/img/` (`logo-cipba-navbar.svg`, `logo-cipba-navbar@2x.png`, `logo-blanco.svg`, `logo.svg` con el isotipo completo). **Pendiente de validar contra el archivo vectorial oficial de la marca si existe** — esto es una reconstrucción por trazado, no el original de diseño.
- [x] Convertir las 7 tipografías a WOFF2 (Lato 300/400/700/900, Roboto Condensed 400/700/900), subset latin + latin-ext — hecho, ya están en `wp-content/themes/cipba/assets/fonts/` (ver Fase 3).
- [ ] Generar favicon 512×512.
- [ ] Armar imagen social por defecto (1200×630).
- [ ] Crear el patrón crosshatch SVG (tile 60×60).
- [ ] Confirmar que los 4 PDFs de `project/docs/` (Ley 10.416, Código de Ética, Reglamento Interno, Ley 12.490) son la versión final a publicar en Normativa.
- [ ] Revisar `assets/images/` del prototipo (eventos, novedades) — son placeholders; confirmar si se reemplazan por imágenes reales antes de cargar contenido.
- [ ] Simplificar/verificar el GeoJSON de los 23 partidos del Distrito VII (≤250 KB), con `nombre` y `delegacion` por feature. **Este es el ítem de mayor riesgo de timeline** — no depende de WordPress, se puede preparar en paralelo.

No es necesario tener el 100% de estos assets para instalar WordPress, pero sí antes de tocar `theme.json` (Fase 3) y el Header/Footer Builder (Fase 4).

---

## Fase 1 — Entorno local (Docker) — ✅ resuelto en esta sesión

1. [x] **Versión de PHP del contenedor:** 8.3.33. Está por encima del rango 8.1-8.2 que recomienda el handoff como "probado". No es bloqueante, pero instalar los plugins de la Fase 4 de a uno y comprobar que no tiren warnings antes de sumar el siguiente (en particular Max Mega Menu y Meta Box, que son los que más código PHP propio traen).
2. [x] **Acceso a `wp-content/themes`:** resuelto. Se agregó `./wp-content:/var/www/html/wp-content` al `docker-compose.yml` y se recreó el contenedor (`docker compose up -d wordpress`) sin tocar la base de datos ni el volumen original. Verificado que el bind mount funciona (archivo creado en el host visible dentro del contenedor). A partir de ahora `d:\Projects\wordpress_docker\wp-content\` es una carpeta local editable normal — se puede versionar con git si se quiere.
3. [x] **URL y SSL:** `siteurl`/`home` están fijos en `http://localhost:8080` (confirmado en `wp_options`), así que no hay riesgo de que un reinicio del contenedor cambie el puerto y rompa URLs guardadas. No hace falta SSL local — no hay mixed-content al no combinar http/https.

---

## Fase 2 — Instalación base de WordPress

### 2.1 Ajustes generales (Ajustes → Generales)
- [ ] Título del sitio: "Colegio de Ingenieros de la Provincia de Buenos Aires — Distrito VII"
- [ ] Zona horaria: `America/Argentina/Buenos_Aires`
- [ ] Formato de fecha: `d/m/Y` (coincide con §4.7 del handoff)
- [ ] Idioma: Español (Argentina) si está disponible, si no Español de España

### 2.2 Enlaces permanentes (Ajustes → Enlaces permanentes)
- [ ] Estructura `/%postname%/` — necesario para URLs limpias de novedades, trámites, resoluciones

### 2.3 Privacidad / indexación
- [ ] **Marcar "Desalentar a los motores de búsqueda"** (Ajustes → Lectura) mientras se desarrolla localmente/en staging. Quitar recién en el pase a producción — anotar esto como paso explícito de la checklist de salida a producción para no olvidarlo.

### 2.4 Usuarios — ✅ resuelto
- [x] Hay dos usuarios administrador: `admin` (creado al completar el instalador) y `cipbaadmin` (creado después, con email propio). **Sugerencia:** si `admin` no lo va a usar nadie en particular, considerar borrarlo (reasignando su contenido a `cipbaadmin`) para no dejar una cuenta genérica activa — es una recomendación de seguridad básica, no bloqueante.
- [ ] Definir si habrá roles editoriales separados (ej. alguien que solo carga novedades) — si sí, crear usuario con rol Editor o Autor, sin acceso a Apariencia/Plugins.

### 2.5 Limpieza de instalación por defecto
- [ ] Borrar contenido de ejemplo (post "Hello World", página "Sample Page", comentario de ejemplo).
- [ ] Desactivar comentarios por defecto en entradas nuevas (Ajustes → Comentarios) salvo que se quiera moderar comentarios en Novedades.
- [ ] Desinstalar plugins/temas por defecto que no se van a usar (Hello Dolly, Twenty Twenty-Four si no es el tema activo).

---

## Fase 3 — Tema: Astra + tema hijo `cipba`

El tema hijo ya existe en disco (`wp-content/themes/cipba/`, `Template: astra`), así que esta fase es completarlo y activarlo, no crearlo desde cero. Se mantiene el nombre `cipba` en vez de `astra-cipba` para no generar un tema duplicado — todas las referencias del handoff a `astra-cipba` aplican igual a esta carpeta.

1. [x] **Astra** (tema padre) ya está instalado en `wp-content/themes/astra` — no hace falta instalarlo.
2. [x] `theme.json` — pegado el bloque completo del §2 del handoff en `wp-content/themes/cipba/theme.json`. Confirmado en el HTML del front que `--wp--preset--color--verde-500: #00a48a` y el resto de las variables ya se generan.
   - [x] `assets/img/logo-cipba-navbar.svg`, `logo-cipba-navbar@2x.png`, `logo-blanco.svg`, `logo.svg` — ya copiados (ver más arriba)
   - [x] `assets/fonts/` — los 7 WOFF2 ya están en su lugar. Se generaron desde las fuentes originales de Google Fonts (repo `google/fonts`: Lato estático, Roboto Condensed variable instanciado a 400/700/900 con `fonttools`) y se subsetearon a latin + latin-ext combinados con `pyftsubset`, en vez de usar los archivos ya pre-recortados del CDN de Google (que vienen divididos por subset y no calzan con el único `src` por peso que pide `theme.json`). Verificado por `curl` que los 7 archivos responden 200 y que el `@font-face` del front-end ya los referencia.
   - [ ] `functions.php` — hoy solo tiene el enqueue de estilos; falta sumar el `add_image_size` (Fase 5), el snippet de PDFs en pestaña nueva (§5.9) y el script del navbar sticky (§5.2)
   - [ ] `assets/img/crosshatch.svg` — pendiente de la Fase 0
   - [ ] Los tres `template-parts/*.php` están vacíos (0 bytes) — completarlos cuando se arme Subcomisiones/Institucional (fuera de este plan inicial)
3. [x] **Tema hijo `cipba` activado** (`wp_options.template = astra`, `stylesheet = cipba`) — antes estaba activo `twentytwentyfive`.
4. [ ] Verificar en el editor de bloques que la paleta de colores, tipografías y tamaños de `theme.json` aparecen correctamente (abrir cualquier página nueva → inspector de bloques → Color/Typography) — pendiente de confirmar visualmente en el admin.

### 3.1 Los tres ajustes obligatorios del Personalizador (§2, nota del handoff)

Sin esto, `theme.json` queda de adorno porque el CSS de Astra le gana por especificidad:

- [ ] *Personalizar → Tipografía → Cuerpo* y *Encabezados* → dejar en "Predeterminado" (hereda del tema hijo)
- [ ] *Personalizar → Colores* → cargar los 7 hex primarios de la tabla del §2 como paleta global de Astra
- [ ] *Personalizar → Contenedor* → ancho 1200 px, padding 24 px

Verificación: abrir DevTools en el front, confirmar que `color` y `font-family` del `<body>` vienen de las variables `--wp--preset--*` y no de clases `ast-*`.

---

## Fase 4 — Plugins — ✅ resuelto

Estado real de `wp-content/plugins/` después de esta sesión — **8 plugins activos**, confirmado en `wp_options.active_plugins`:

| Plugin | Estado |
|---|---|
| ~~Elementor~~ / ~~header-footer-elementor~~ / ~~Smart Slider 3~~ | **Desinstalados** (nada estaba activo, no había contenido armado con ellos) — se optó por bloques + Spectra (§1 del handoff) |
| Spectra (`ultimate-addons-for-gutenberg`) | [x] **Activo** — falta verificar que aparecen Container con enlace, Icon, Post Carousel y FAQ en el inserter de bloques |
| Max Mega Menu (`megamenu`) | [x] **Activo** — se configura en Fase 6 |
| Meta Box (`meta-box`) | [x] **Activo** — repeaters para `resolucion.anexos` y `subcomision.referentes` |
| Fluent Forms (`fluentform`) | [x] **Activo** — para el formulario de Contacto (§4.11) |
| Rank Math (`seo-by-rank-math`) | [x] **Activo** — confirmar que no queda ningún otro plugin de SEO activo en simultáneo |
| UpdraftPlus (`updraftplus`) | [x] **Activo** — falta correr el primer backup manual (ver Fase 8) |
| Code Snippets | [x] **Activo** |
| WP Reset | Instalado, no activo — es una herramienta de desarrollo. **No activar y desinstalar antes de pasar a producción** |
| Sticky Menu on Scroll (`mystickymenu`) | Instalado, no activo |
| Custom Post Type UI (`custom-post-type-ui`) | Instalado, no activo |
| PDF Embedder (`pdf-embedder`) | Instalado, no activo |
| Akismet | Instalado, no activo |

Decisiones pendientes sobre los plugins instalados pero inactivos:

- [ ] **Sticky Menu on Scroll:** activarlo, o usar el CSS del §5.2 (el handoff recomienda el CSS, más liviano — en ese caso este plugin se puede desinstalar).
- [ ] **Custom Post Type UI:** usarlo para los 5 CPTs de la Fase 5, o registrarlos por código en `functions.php` (preferencia del handoff, §4.14 — "así viajan con el tema"). Si se registra por código, se puede desinstalar.
- [ ] **PDF Embedder:** no está mencionado en el handoff — sirve para embeber (no solo enlazar) los PDFs de Normativa. Activar solo si se quiere esa UX.
- [ ] **Akismet:** activar cuando haya un formulario de contacto público (anti-spam).

No instalar: Search & Filter (solo si el volumen de novedades lo justifica más adelante, ver tabla de decisiones).

---

## Fase 5 — Estructura de contenido (antes de cargar contenido real)

Registrar los custom post types en `functions.php` del tema hijo (no con Custom Post Type UI, salvo que el equipo no tenga perfil técnico — §4.14 del handoff):

- [ ] `evento` — campos Meta Box: `fecha_inicio`, `cupo`, `inscripcion_abierta`, `cierre_inscripcion`, `lugar`
- [ ] `documento` — campos: `archivo` (File), `formato`, `peso`, `origen`, `orden`
- [ ] `resolucion` — campos: `numero`, `fecha_publicacion`, `vigencia_desde`, `vigente` (bool), `anexos` (repeater: título, archivo, descripción)
- [ ] `subcomision` — campos: `tag`, `referentes` (repeater: nombre, matrícula, teléfono, mail)
- [ ] `tramite` — sin campos custom, solo contenido con plantilla compartida

Taxonomías:
- [ ] Categorías nativas de `post` para Novedades: institucional, capacitación, normativa, matrícula, comisiones (deben coincidir con los slugs usados en `settings.custom.cat` de `theme.json` y con el CSS del §5.5)

Tamaños de imagen (`add_image_size` en `functions.php`):
- [ ] `cipba-card` — 600×400, recorte duro
- [ ] `cipba-nov-hero` — 1680×720, recorte duro (21:9)
- [ ] `cipba-thumb` — 300×200

Desactivar tamaños intermedios de WordPress que no se usan (evita inflar la media library).

---

## Fase 6 — Menús y estructura de navegación

1. [ ] Crear menú **Principal** (Apariencia → Menús) con las 5 entradas de primer nivel. Asignarlo a la ubicación del Header Builder de Astra.
2. [ ] Configurar Max Mega Menu sobre ese menú: columnas para Trámites y Normativa, subtítulos de grupo vía ítems de menú tipo etiqueta con clase `mm-group-title`.
3. [ ] Marcar el ítem "Honorarios mínimos vigentes" con clase CSS `is-destacado`.
4. [ ] Marcar los ítems que van a PDFs/sistemas externos con clase `abre-nuevo` (el snippet PHP del §5.9 depende de esta clase).
5. [ ] Crear los 3 menús del pie (Trámites, Normativa, Institucional/Links de interés) como widgets *Navigation Menu* en el Footer Builder de Astra.
6. [ ] Configurar el breakpoint de escritorio→hamburguesa en **1160 px** tanto en Astra (Personalizar → Header → Menú → Breakpoint) como en Max Mega Menu.

---

## Fase 7 — CSS y snippets del tema hijo

Todo el CSS del §5 del handoff (~90 líneas) va en `style.css` del tema hijo, no en "CSS adicional" del Personalizador (no versiona):

- [ ] §5.1 Variables puente
- [ ] §5.2 Navbar sticky con sombra/blur al scrollear (+ script en `functions.php` vía `wp_footer`)
- [ ] §5.3 Ítem de menú destacado
- [ ] §5.4 Elevación de tarjetas en hover
- [ ] §5.5 Color de badge por taxonomía
- [ ] §5.6 Recorte de extracto a 2 líneas
- [ ] §5.7 Avatar de iniciales
- [ ] §5.8 Columna lateral fija en trámites
- [ ] §5.9 Snippet PHP: enlaces de menú y PDFs en pestaña nueva
- [ ] §5.10 Imagen 21:9 + anillo de foco
- [ ] §5.11 Patrón crosshatch sobre el hero

Verificar breakpoints alineados (§6 del handoff): Astra usa 921/544 px por defecto, Elementor 1024/767 px por defecto — el diseño necesita 1160/900/700. Ajustar en Astra Customizer y, si se usa Elementor en alguna plantilla puntual, en *Site Settings → Layout → Breakpoints*.

---

## Fase 8 — Verificación de la configuración inicial (checkpoint antes de armar secciones)

Antes de pasar a construir las 11 secciones de la home (§4 del handoff), confirmar:

- [x] Existe un usuario administrador propio (no el instalador por defecto) con email real — `cipbaadmin`
- [ ] `theme.json` gobierna colores y tipografías en el front (no CSS de Astra) — verificado con DevTools
- [ ] Los 7 colores primarios coinciden entre `theme.json` y la paleta global de Astra
- [ ] Fuentes locales cargando (Network tab: `.woff2` con 200, no llamadas a fonts.googleapis.com)
- [ ] Los 5 CPTs (`evento`, `documento`, `resolucion`, `subcomision`, `tramite`) aparecen en el admin con sus campos Meta Box
- [ ] Menú principal con mega menú funcionando y navegable por teclado (Tab + Escape)
- [ ] Breakpoint de hamburguesa exactamente en 1160 px
- [ ] "Desalentar motores de búsqueda" sigue activo (es local, pero es el hábito a mantener hasta producción)
- [ ] Backup inicial con UpdraftPlus corrido al menos una vez

---

## Qué sigue después de este plan (fuera de alcance)

- Armado sección por sección de la home (§4.1 a §4.13 del handoff) — patrones de Spectra, Query Loops, el componente de mapa Leaflet (§4.9, presupuestar 4-8 h aparte).
- Carga de contenido real (autoridades, subcomisiones, resoluciones, PDFs de normativa).
- Definición de hosting de producción y plan de migración desde local.
- Checklist de salida a producción del handoff (contraste, teclado, PDFs en pestaña nueva, hamburguesa en 1160px, datos cotejados contra fuente oficial).
