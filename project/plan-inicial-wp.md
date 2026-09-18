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
- [x] Crear el patrón crosshatch SVG (tile 60×60) — hecho en la Fase 7, `assets/img/crosshatch.svg`.
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

El handoff advierte que sin esto `theme.json` queda de adorno porque el CSS de Astra le gana por especificidad. **Se investigó el CSS real que genera el sitio (no solo la teoría) y en esta combinación puntual de versiones (WordPress + Astra 4.13.9 + `cipba`) el problema no se da:**

- [x] **Tipografía (Cuerpo/Encabezados):** los defaults de Astra (`body-font-family` y `headings-font-family`) ya vienen en `'inherit'` de fábrica — no hay nada que cambiar en el Personalizador.
- [x] **Contenedor:** el default de Astra para `site-content-width` ya es **1200px** — coincide con `theme.json` sin tocar nada. El padding lateral de 24px lo pone `theme.json` vía `useRootPaddingAwareAlignments`, independiente de Astra.
- [x] **Colores:** verificado en el HTML real del front. Astra sí emite sus propias reglas (`a{color:var(--ast-global-color-0)}`, etc.) pero WordPress encola `global-styles-inline-css` (el CSS de `theme.json`) **después** del `astra-theme-css-inline-css` — con la misma especificidad, gana el que carga último. Confirmado con `curl` sobre el HTML servido:
  - `body{background-color: var(--wp--preset--color--blanco); color: var(--wp--preset--color--tinta-900); font-family: var(--wp--preset--font-family--lato); ...}` ✅
  - Ningún `h1`-`h6` con `color` propio de Astra (headings quedan 100% gobernados por `theme.json`) ✅
  - Los links sí tienen una regla de Astra compitiendo, pero pierde por orden de carga ✅

**No se tocó el Personalizador de Astra.** Si en el futuro se actualiza Astra o cambia el orden de encolado de estilos, esto podría dejar de cumplirse — repetir esta verificación (`curl http://localhost:8080/ | grep -oE "(html|body)\{[^}]*\}"`) después de cualquier actualización de Astra antes de dar por sentado que `theme.json` sigue mandando.

Pendiente de revisar más adelante (no bloqueante para esta fase): botones (`.wp-block-button__link{border-color:var(--ast-global-color-0)}`, `button{color:var(--ast-global-color-0)}`) sí tienen reglas propias de Astra que compiten con el estilo de botón de `theme.json` — a verificar visualmente cuando se arme una sección con botones reales (Fase de contenido, fuera de este plan inicial).

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
- [x] **Custom Post Type UI:** decidido — los 5 CPTs se registraron por código en `functions.php`/`inc/custom-post-types.php` (preferencia del handoff, §4.14). El plugin sigue instalado pero inactivo; se puede desinstalar cuando se confirme que no hace falta para nada más.
- [ ] **PDF Embedder:** no está mencionado en el handoff — sirve para embeber (no solo enlazar) los PDFs de Normativa. Activar solo si se quiere esa UX.
- [ ] **Akismet:** activar cuando haya un formulario de contacto público (anti-spam).

No instalar: Search & Filter (solo si el volumen de novedades lo justifica más adelante, ver tabla de decisiones).

---

## Fase 5 — Estructura de contenido — ✅ resuelto

Los 5 CPTs se registraron por código en `wp-content/themes/cipba/inc/custom-post-types.php` (+ `inc/meta-boxes.php` para los campos, `inc/image-sizes.php`, `inc/helpers.php`), todos enganchados desde `functions.php`. Verificado con `wp post-type list` que los 5 aparecen registrados y que el sitio no tira error fatal.

- [x] `evento` — campos Meta Box: `fecha_inicio`, `cupo`, `inscripcion_abierta`, `cierre_inscripcion`, `lugar`
- [x] `documento` — campo `archivo` (file_advanced) + `origen` + `orden`. **`formato` y `peso` no se registraron como campos** — se calculan en runtime desde el archivo subido con `cipba_get_documento_file_meta()` en `inc/helpers.php`, tal como pide el handoff ("no se tipean a mano")
- [x] `resolucion` — campos: `numero`, `fecha_publicacion`, `vigencia_desde`, `vigente` (checkbox), `anexos` (Meta Box `group` + `clone` → título, archivo, descripción)
- [x] `subcomision` — campos: `tag`, `referentes` (Meta Box `group` + `clone` → nombre, matrícula, teléfono, mail)
- [x] `tramite` — sin campos custom, `public => true` con `rewrite slug 'tramites'` (necesita páginas individuales, a diferencia de los otros 4 CPT que son `public => false` porque solo alimentan listados en páginas ya existentes)

Taxonomías:
- [x] Categorías nativas de `post` para Novedades creadas: `institucional`, `capacitacion`, `normativa`, `matricula`, `comisiones` (slugs verificados contra `settings.custom.cat` de `theme.json` y el CSS del §5.5). Quedó además un hook `after_switch_theme` en el código para que se sembren solas en cualquier instalación futura del tema.

Tamaños de imagen (`add_image_size`, en `inc/image-sizes.php`):
- [x] `cipba-card` — 600×400, recorte duro
- [x] `cipba-nov-hero` — 1680×720, recorte duro (21:9)
- [x] `cipba-thumb` — 300×200
- [x] Filtro `image_size_names_choose` agregado para que el selector de medios solo muestre estos 3 + los nativos de WordPress, sin los tamaños intermedios que el diseño no usa

**Pendiente de decidir, no bloqueante:** el CPT `tramite` no tiene todavía definido si el bloque "Otros trámites" se autogenera a partir de una taxonomía o de un campo de relación entre trámites — se resuelve cuando se arme esa sección de contenido (fuera de este plan inicial).

---

## Fase 6 — Menús y estructura de navegación — ✅ resuelto por completo

Se tomó la estructura real de navegación de `project/index.html` (arrays `navLinks` y `footerCols`/`footerHrefs` del prototipo), no una genérica — así que los menús ya reflejan el diseño aprobado, ítem por ítem. Se armaron con un script PHP corrido una sola vez vía `wp eval-file` (más confiable que crear ~50 ítems a mano desde el admin), verificado después con `wp menu list`.

1. [x] Menú **Principal** creado (31 ítems: 6 de primer nivel + subniveles) y asignado a la ubicación `primary` del tema. Estructura real:
   - Inicio · Trámites (grupos **Matrícula** e **Sistemas**, con ítems etiqueta `mm-group-title` para cada subtítulo) · Normativa (grupos **Marco legal** y **Consulta**) · Novedades · Institucional (un solo grupo) · Contacto
2. [x] **Max Mega Menu configurado y restyleado — resuelto, con una corrección importante sobre el plan original.** El diseño real de referencia (verificado contra el sitio estático) **no usa columnas**: Trámites y Normativa son un dropdown de una sola columna con subtítulos de grupo separados por una línea — no un mega menú en grilla. Se probó primero en modo "Mega Menu - Grid Layout" (columnas) y no correspondía; se dejó en modo **Flyout** para los dos.
   - Max Mega Menu (v3.x) trae su propio CSS dinámico bastante agresivo (selectores de 2 IDs: `#mega-menu-wrap-primary #mega-menu-primary ...`), que pisaba tanto el estilo de `theme.json` como las clases `mm-group-title`/`abre-nuevo`. Se sobrescribió todo con selectores calcados de los suyos + `!important` en `wp-content/themes/cipba/style.css`: barra blanca (no el skin oscuro por defecto), panel del dropdown blanco/redondeado/con sombra (no gris plano), fondo transparente por ítem, subtítulos de grupo en mayúsculas/gris con divisor, ícono de enlace externo en los ítems `abre-nuevo` (mismo SVG "externalLink" que traía el prototipo), y el estado de "página actual" (ej. "Inicio" en la home) restyleado a verde-700 + subrayado en vez del highlight oscuro por defecto del plugin.
   - Verificado visualmente contra capturas reales del admin en varias iteraciones (el primer intento con `mask`+`currentColor` para el ícono no renderizaba — se cambió a un SVG con color fijo, más simple y confiable).
3. [x] "Honorarios mínimos vigentes" con clase `is-destacado`.
4. [x] Los 8 ítems que van a PDFs o sistemas externos ya tienen la clase `abre-nuevo` (Visado online, SIGMA, Otros trámites, Incumbencias, Resoluciones ×2, + las mismas 2 repetidas en el pie). Los 4 PDF de normativa no necesitan la clase — el snippet del §5.9 los detecta solo por extensión `.pdf`.
5. [x] Creados los **5 menús del pie** (no 3 — el diseño real tiene 4 columnas + la barra legal inferior): `Pie - Trámites`, `Pie - Normativa`, `Pie - Institucional`, `Pie - Links de interés`, `Pie - Legal` (este último ya asignado a la ubicación `footer_menu`). **Resuelto por código, no por el Footer Builder:** se descubrió que Astra Free solo admite UN elemento "Footer Menu" en el builder visual, cableado a una única ubicación de menú — no hay forma de poner 4 menús independientes en columnas separadas sin Astra Pro. Se reemplazó la fila "Primary Footer" del builder por `wp-content/themes/cipba/inc/footer.php` (quita el `primary_footer` que registra Astra vía `remove_action` sobre `astra_primary_footer`, y lo sustituye por una grilla propia con `wp_nav_menu()` para cada uno de los 4 menús + el bloque de marca), con el CSS correspondiente en `style.css`. Verificado visualmente.
6. [x] Breakpoint de hamburguesa en 1160px — configurado en los dos lugares. Astra: `astra-settings[mobile-header-breakpoint] = 1160` (seteado por WP-CLI). Max Mega Menu: campo "Responsive Breakpoint" del tema activo = `1160px` (cargado a mano en *Mega Menu → Menu Themes*, verificado en la opción `megamenu_themes` de la base).

**4 PDFs de Normativa subidos a la Media Library** en esta misma fase (Ley 10.416, Ley Previsional 12.490, Código de Ética, Reglamento Interno — attachment IDs 6-9) para que el submenú de Normativa apunte a archivos reales en vez de placeholders.

**Enlaces que quedaron como placeholder `#` porque el diseño original no trae una URL real** (no se inventaron URLs — están así en el propio prototipo o directamente no figuran):
- [x] "Certificados CAIE" → `http://www.colegioingenieros.org.ar/caie/`
- [x] Autoridad del Agua (ADA) → `https://www.ada.gba.gov.ar/`
- [x] Agencia de Recaudación (ARBA) → `https://web.arba.gov.ar/`
- [x] Caja de Previsión Social → `http://www.caaitba.org.ar/`
- [x] Colegio de Escribanos PBA → `https://www.colescba.org.ar/portal/`
- [x] Ministerio de Ambiente → `https://www.ambiente.gba.gob.ar/`
- [ ] "Vademécum" (Normativa, en el nav y en el pie) — **todavía sin URL real**, sigue en `#`

Los 6 ya cargados quedaron con clase `abre-nuevo` y `target=_blank` (para el ícono de enlace externo + pestaña nueva). Falta solo Vademécum.

**Nota:** todos los enlaces internos (Trámites, Normativa, Institucional, Novedades, Contacto, Honorarios, Subcomisiones) apuntan a las URLs finales previstas (`/tramites/inscripcion/`, `/normativa/`, etc.) aunque esas páginas/CPT todavía no tengan contenido cargado — van a dar 404 hasta que se cree ese contenido, lo cual es esperable en esta fase (menús = estructura, no contenido).

---

## Fase 7 — CSS y snippets del tema hijo — ✅ resuelto

Todo el CSS del §5 del handoff está en `wp-content/themes/cipba/style.css` (no en "CSS adicional" del Personalizador, que no versiona). Los dos snippets PHP quedaron en `wp-content/themes/cipba/inc/navbar.php`, enganchado desde `functions.php`.

- [x] §5.1 Variables puente
- [x] §5.2 Navbar sticky con sombra/blur al scrollear (CSS en `style.css` + script en `inc/navbar.php` vía `wp_footer`)
- [x] §5.3 Ítem de menú destacado — igual que con los subtítulos de grupo, hubo que agregar una versión con el selector de Max Mega Menu + `!important` además de la regla tal cual del handoff, porque el CSS dinámico del plugin le ganaba por especificidad
- [x] §5.4 Elevación de tarjetas en hover — CSS listo, clases (`cipba-card`, `cipba-card__arrow`) todavía sin usar en ningún patrón (eso es contenido, fuera de este plan)
- [x] §5.5 Color de badge por taxonomía
- [x] §5.6 Recorte de extracto a 2 líneas
- [x] §5.7 Avatar de iniciales
- [x] §5.8 Columna lateral fija en trámites
- [x] §5.9 Snippet PHP: enlaces de menú y PDFs en pestaña nueva. **Con un matiz importante:** el filtro `nav_menu_link_attributes` del handoff solo corre en menús renderizados por el `wp_nav_menu()` estándar de WordPress — Max Mega Menu usa su propio sistema de renderizado y probablemente no lo respeta. Para el menú Principal (que sí pasa por MMM) se marcó `target=_blank` directo en el campo nativo de cada ítem (`_menu_item_target`, lo mismo que tildar "abrir en pestaña nueva" a mano en el editor clásico) vía WP-CLI, sobre los 8 ítems con clase `abre-nuevo`. El filtro PHP se deja igual, porque si se arman los menús del pie como widgets *Navigation Menu* (Fase 6, pendiente) esos sí pasan por el estándar y lo van a necesitar.
- [x] §5.10 Imagen 21:9 + anillo de foco
- [x] §5.11 Patrón crosshatch sobre el hero — el asset `crosshatch.svg` tampoco existía (pendiente desde la Fase 0); se generó un patrón geométrico simple de líneas diagonales en `assets/img/crosshatch.svg`

**Pendiente, no bloqueante:** verificar breakpoints alineados (§6 del handoff) — Astra usa 921/544px por defecto, el diseño necesita 1160/900/700. Se ajusta en Astra Customizer (y en Max Mega Menu para el punto de 1160px del menú — es el mismo pendiente que ya estaba anotado en la Fase 6).

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
