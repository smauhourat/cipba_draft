# Manual técnico — Mantenimiento del tema `cipba`

Guía para quien mantiene el código del sitio CIPBA Distrito VII: tema hijo, tipos de contenido, campos, plantillas, estilos, entorno local y despliegue.

> El manual para quien carga contenido es `project/manual-usuario-contenido.md`. Mantenelos en sync: cada cambio de campos o de comportamiento visible debería reflejarse en los dos. Después de editar un `.md`, regenerá los PDF con `python project/tools/generar-pdf-manuales.py` (requiere `pip install markdown` y Chrome o Edge).

---

## Índice

1. [Arquitectura general](#1-arquitectura-general)
2. [Entorno local](#2-entorno-local)
3. [Estructura del tema](#3-estructura-del-tema)
4. [Tipos de contenido y campos](#4-tipos-de-contenido-y-campos)
5. [Datos del Distrito y marcadores](#5-datos-del-distrito-y-marcadores)
6. [Shortcodes y páginas](#6-shortcodes-y-páginas)
7. [Plantillas](#7-plantillas)
8. [Módulos por sección](#8-módulos-por-sección)
9. [Hooks del tema](#9-hooks-del-tema)
10. [Estilos, tokens y JavaScript](#10-estilos-tokens-y-javascript)
11. [Recetas: cómo extender el tema](#11-recetas-cómo-extender-el-tema)
12. [Despliegue](#12-despliegue)
13. [Gotchas conocidos](#13-gotchas-conocidos)
14. [Pendientes y problemas conocidos](#14-pendientes-y-problemas-conocidos)

---

## 1. Arquitectura general

### 1.1 Stack

| Pieza | Detalle |
|---|---|
| CMS | WordPress (PHP 8.3 local; MySQL 8.4) |
| Tema padre | **Astra** (free) |
| Tema hijo | **`cipba`** — todo el código propio. Versionado en `wordpress/themes/cipba/` |
| Campos | **Meta Box** (free) — sin él, las pantallas de edición quedan sin campos (`meta-boxes.php` solo se carga si existe `RWMB_Loader`) |
| Menú | **Max Mega Menu** (menú *Principal*, ubicaciones `primary` y `mobile_menu`) |
| Formularios | **Fluent Forms** (Contacto = `[fluentform id="3"]`) |
| Correo | **FluentSMTP** |
| SEO | **Rank Math** |
| Bloques extra | **Spectra** (ultimate-addons-for-gutenberg) |
| Backups | **UpdraftPlus** |
| Snippets | **Code Snippets** |

Versiones exactas y plugins inactivos: `wordpress/plugins.txt`. Los plugins de terceros **no** se versionan.

### 1.2 Principio de diseño

El tema separa **diseño** (código) de **contenido** (panel). Las secciones con diseño estricto se renderizan desde PHP con shortcodes y plantillas; quien edita solo completa formularios planos (Meta Box). Los valores repetidos se centralizan en **Datos del Distrito** y se insertan con marcadores `{{clave}}`.

Decisiones que condicionan todo lo demás:

- **Meta Box free, sin extensiones pagas**: no hay repeaters (`group` clonable). Los campos repetibles son **slots numerados** (`ref1_nombre`…`ref4_nombre`), con la cantidad en constantes.
- **Editor clásico** para los CPT propios (formularios simples); **editor de bloques** solo en Entradas y Páginas.
- **Astra free**: el pie (4 columnas de menús) y la barra superior se resuelven por código porque el builder free no lo permite.
- **Sin dependencias JS**: scripts vanilla, encolados solo donde se usan.

### 1.3 Qué vive dónde

| Qué | Dónde | En git |
|---|---|---|
| Código del tema | `wordpress/themes/cipba/` (bind mount) | Sí |
| Plugins, uploads, otros temas | Volumen Docker `wordpress_docker_wp_content` | No |
| Páginas, entradas, CPT, menús, opciones (incl. `cipba_distrito`) | Base de datos (volumen `wordpress_docker_db_data`) | No |
| Prototipos HTML, planes, runbooks, manuales | `project/` | Sí |
| Lista de partidos del Distrito | `themes/cipba/assets/data/partidos-distrito-vii.json` | Sí |

**Consecuencia:** un cambio que requiere tocar contenido de la base (insertar un shortcode en una página, crear un documento, cambiar un menú) **no viaja con el tema**: hay que replicarlo en cada entorno o migrar la base (§12).

---

## 2. Entorno local

Todo en `wordpress/` (ver `wordpress/README.md`).

```bash
cd wordpress
cp .env.example .env            # credenciales (git-ignored)
docker compose up -d --build    # el Dockerfile agrega WP-CLI
```

- Sitio `http://localhost:8080` · phpMyAdmin `http://localhost:8081`.
- Contenedores: `wordpress_app`, `wordpress_db`, `wordpress_pma`. El compose fija `name: wordpress_docker` para reutilizar los volúmenes.
- WP-CLI: `docker exec wordpress_app wp --allow-root <comando>`.
- Para PHP no trivial: escribir un archivo, `docker cp` al contenedor y `wp eval-file` (pasar PHP inline por PowerShell es frágil). Con Git Bash, usar `MSYS_NO_PATHCONV=1` para rutas del contenedor.
- `wordpress/scripts/`: `chequeo-pre-deploy.php` (solo lectura, chequeos de seguridad previos al deploy) y `zip-wp-content.php` (empaqueta `wp-content` dentro del contenedor).

**Replicar el entorno en otra PC** (base + `wp-content` + `.env`): paso a paso en `wordpress/README.md`, sección *Replicar el entorno en otra PC*.

**Cuidado:**

- **Nunca** `docker compose down -v`: borra la base y los uploads.
- Recién levantado, el sitio da 500 unos segundos hasta que MySQL está listo.
- Plugins y uploads se tocan por el contenedor o por wp-admin, no por el disco de Windows.
- `wp post meta delete <id> <key> --all` **ignora la clave y borra toda la meta del post**. Usar `wp eval 'delete_post_meta(…);'`.
- Backups SQL en `wordpress/backups/`; se restauran con `mysql` dentro de `wordpress_db` (el contenedor de la app no tiene cliente mysql).

---

## 3. Estructura del tema

```
themes/cipba/
├── functions.php              Versión (CHILD_THEME_CIPBA_VERSION), encola style.css, incluye inc/*
├── style.css                  Todos los estilos propios (por bloques, ver §10)
├── theme.json                 Paleta, tipografías, tamaños, layout (1200px)
├── single.php                 Detalle de publicación (rama Noticia / Evento)
├── single-tramite.php         Plantilla común de trámites
├── single-tramite-pago-matricula.php   Medios de pago (WP la elige por single-{tipo}-{slug})
├── inc/
│   ├── custom-post-types.php  CPT, categorías semilla, admin (columnas, orden, editor)
│   ├── meta-boxes.php         Todas las cajas de campos (Meta Box)
│   ├── settings.php           Datos del Distrito, marcadores, [cipba_dato]
│   ├── helpers.php            Documentos, íconos SVG, teléfonos, slots, partidos, SVG upload, helpers de admin
│   ├── tramites.php           Constantes y lectura de trámites, cajita de marcadores, layout Astra
│   ├── novedades.php          Paletas de categoría, fechas, tarjetas, tabs, agenda, admin de Entradas
│   ├── honorarios.php         Título automático, una sola vigente, listados, aviso de la home
│   ├── copias-remitidas.php   Formulario propio de Copias remitidas: validación y envío por mail
│   ├── shortcodes.php         Shortcodes de home, institucional, contacto, normativa, TAD
│   ├── topbar.php             Barra superior (antes del #masthead)
│   ├── navbar.php             Sombra al scrollear, panel mobile de Mega Menu, links en pestaña nueva
│   ├── footer.php             Pie propio (reemplaza el Primary/Below Footer de Astra)
│   ├── image-sizes.php        Tamaños de imagen
│   └── favicon.php            Set de favicons propio
├── template-parts/            Markup de listados (autoridades, sedes, subcomisiones, partidos,
│                              consultor-partidos, novedades-list, honorarios, normativa, tad,
│                              copias-remitidas)
└── assets/
    ├── js/                    Scripts por sección (§10.3)
    ├── data/partidos-distrito-vii.json
    ├── fonts/                 Lato y Roboto Condensed (woff2, locales)
    └── img/                   Logos, mapa, favicons
```

Orden de carga en `functions.php`: CPT → image-sizes → helpers → settings → tramites → novedades → honorarios → copias-remitidas → topbar → navbar → footer → shortcodes → favicon → meta-boxes (condicional). `helpers.php` y `tramites.php` definen constantes que `meta-boxes.php` usa, por eso van antes.

---

## 4. Tipos de contenido y campos

### 4.1 CPT

Todos se registran en `cipba_register_post_types()` (`inc/custom-post-types.php`). Salvo `tramite`, ninguno es público (no tiene URL propia): se muestran a través de shortcodes.

| CPT | Menú del panel | Público | Supports | Editor | Se muestra en |
|---|---|---|---|---|---|
| `tramite` | Trámites | Sí, `/tramites/{slug}/` | title, page-attributes | Clásico | `single-tramite*.php` |
| `documento` | Documentos (Normativa) | No | title | Bloques* | Trámites, pago, Normativa, TAD, CTA de entradas, menús |
| `resolucion` | Resoluciones (Honorarios) | No | — (sin título ni editor) | — | `/honorarios/`, aviso de la home |
| `subcomision` | Subcomisiones | No | title, page-attributes | Clásico | `/subcomisiones/`, home |
| `sede` | Sedes | No | title, page-attributes, thumbnail | Clásico | Institucional, Contacto, home |
| `autoridad` | Autoridades | No | title, page-attributes | Clásico | Institucional |
| `area_contacto` | Áreas de contacto | No | title, page-attributes | Clásico | Contacto |
| `link_interes` | Links de interés | No | title, page-attributes | Clásico | Pie |
| `post` (nativo) | Entradas | Sí | nativos | Bloques | `/novedades/`, home, `single.php` |

\* `documento` solo soporta `title`, así que la pantalla es prácticamente un formulario aunque no esté en la lista del editor clásico.

El editor clásico se fuerza en `cipba_subcomision_classic_editor()` (filtro `use_block_editor_for_post_type`). Los placeholders del título están en `cipba_subcomision_title_placeholder()`. `cipba_limpiar_metaboxes_cpt()` saca las cajas de Astra, *Campos personalizados* y *Slug* de todos los tipos propios y de `post`.

Cada CPT con orden manual tiene columnas de admin y orden por defecto `menu_order title` (funciones `cipba_*_admin_columns`, `*_admin_column_content`, `*_admin_default_order`).

### 4.2 Cajas de campos (Meta Box)

Todas en `cipba_register_meta_boxes()` (`inc/meta-boxes.php`), filtro `rwmb_meta_boxes`. Las claves de meta son los `id` de cada campo.

| Caja | Tipo | Claves principales |
|---|---|---|
| Datos de la publicación | `post` | `tipo_pub` (noticia/evento), `bajada`, `fecha_alt`, `destacada`, `galeria` (image_advanced, varias filas), `cta_texto`, `cta_url`, `cta_doc`, `info_texto`, `contacto_email`, `contacto_telefono`, `ocultar_info`, `es_actividad` |
| Datos de la actividad | `post` | `fecha_inicio`, `fecha_fin`, `horario`, `lugar`, `modalidad`, `modalidad_nota`, `cupo`, `arancel`, `inscripcion_abierta`, `cierre_inscripcion` |
| Archivo del documento | `documento` | `archivo` (file_advanced, guarda ID de adjunto), `enlace_externo`, `en_normativa`, `descripcion`, `origen`, `orden` |
| Datos de la resolución | `resolucion` | `numero`, `fecha_publicacion`, `vigencia_desde`, `vigencia_hasta`, `vigente`, `descripcion`, `archivo_resolucion`, `archivo_anexos`, `enlace_externo` |
| Datos de la subcomisión | `subcomision` | `tag`, `mail`, `descripcion`, `ref{1..4}_nombre/_matricula/_telefono/_mail` |
| Datos de la sede | `sede` | `tag`, `destacada`, `direccion`, `telefono`, `email`, `visador`, `horario`, `contacto{1..8}_rol/_nombre/_tel/_email` |
| Datos de la autoridad | `autoridad` | `cargo`, `titulo`, `destacado` |
| Datos del área | `area_contacto` | `icono`, `email`, `persona{1..4}_nombre/_tel/_email` |
| Datos del trámite | `tramite` (≠ pago) | `icono`, `eyebrow`, `breadcrumb`, `intro`, `costo_titulo`, `costo{1..4}_label/_value`, `bloque{1..6}_tipo/_titulo/_sub/_contenido`, `docs_titulo`, `docs_intro`, `doc{1..8}` |
| Datos de la página de pago | `tramite` slug `pago-matricula` | `pago_h1`, `eyebrow`, `breadcrumb`, `intro`, `pago_antes_*`, `pago_mod_*`, `mod{1..4}_titulo/_boton/_desc/_pasos`, `mod1_pie`, `mod2_doc`, `mod3_url`, `mod4_doc`, `pago_cta_titulo/_texto` |
| Datos del link | `link_interes` | `url` (requerido) |
| Contenido de la página TAD | `page` slug `tad` | `tad_doc{1..6}`, `tad_formulario`, `tad_email` |

Fechas: siempre `yy-mm-dd` (se guardan `Y-m-d`, lo que permite comparar como string).

### 4.3 Constantes de slots

| Constante | Valor | Archivo | Qué limita |
|---|---|---|---|
| `CIPBA_TRAMITE_BLOQUES` | 6 | `inc/tramites.php` | Bloques de contenido de un trámite |
| `CIPBA_TRAMITE_COSTOS` | 4 | `inc/tramites.php` | Filas del cuadro lateral |
| `CIPBA_TRAMITE_DOCS` | 8 | `inc/tramites.php` | Documentos por trámite |
| `CIPBA_SUBCOM_MAX_REFS` | 4 | `inc/helpers.php` | Referentes por subcomisión |
| `CIPBA_SEDE_MAX_CONTACTOS` | 8 | `inc/helpers.php` | Contactos directos de la Casa Central |
| `CIPBA_AREA_MAX_PERSONAS` | 4 | `inc/helpers.php` | Personas por área de contacto |
| `CIPBA_TAD_DOCS` | 6 | `inc/helpers.php` | Resoluciones en TAD |

Subir una constante agrega slots al formulario y a la lectura (los lectores iteran hasta la constante). Bajarla **no borra** la meta existente: solo deja de mostrarse.

### 4.4 Cajas condicionales por slug

Meta Box free ignora `include`/`exclude`, así que las cajas que dependen del slug se registran condicionalmente en PHP:

- `cipba_admin_editando_pago()` (`inc/tramites.php`): `true` si se edita/guarda el trámite `pago-matricula`. Decide entre *Datos del trámite* y *Datos de la página de pago*. **Las dos comparten claves** (`eyebrow`, `breadcrumb`, `intro`): si se registraran juntas, una pisaría a la otra al guardar.
- `cipba_admin_editando_pagina( $slug )` (`inc/helpers.php`): idem para páginas (hoy solo `tad`). Lee `$_GET['post']` o `$_POST['post_ID']`, por lo que funciona tanto al abrir como al guardar.

### 4.5 Taxonomía: categorías de Entradas

Term meta `paleta` (`violeta` | `verde` | `verde_oscuro` | `ladrillo`) y `orden`, con campos propios en el formulario de categoría (`cipba_cat_form_fields`, `cipba_cat_save` en `inc/novedades.php`). Paletas en `cipba_cat_paletas()` (etiqueta, color de tinta, color de fondo). Categorías semilla: `cipba_seed_novedades_categories()` en `after_switch_theme` (solo corre al activar el tema).

---

## 5. Datos del Distrito y marcadores

Archivo: `inc/settings.php`. Una opción serializada `cipba_distrito`, una pantalla de admin (`admin.php?page=cipba-datos-distrito`) accesible con `edit_pages` (editores incluidos, vía `option_page_capability_cipba_datos_group`).

### 5.1 API

| Función | Devuelve |
|---|---|
| `cipba_datos_fields()` | Definición de todos los datos: `clave => [section, label, desc, placeholder, default, type, required]` |
| `cipba_dato( $key )` | Valor crudo. Si la opción nunca se guardó, el `default`; si se guardó, lo guardado (aunque esté vacío) |
| `cipba_dato_display( $key )` | Valor para mostrar: `monto` → `$ 395.000`, `fecha` → `d/m/Y` |
| `cipba_dato_url( $key )` | Enlace listo: `whatsapp` → `wa.me`, `telefono` → `tel:`, `email` → `mailto:`, URLs tal cual |
| `cipba_tokens( $text )` | Reemplaza `{{clave}}` por `cipba_dato_display()` escapado; deja intactos los marcadores inexistentes |
| `[cipba_dato campo="x"]` / `[cipba_dato campo="x" formato="url"]` | Shortcode para páginas y bloques |

Tipos (`type`): `text` (defecto), `url`, `email`, `numero`, `monto`, `fecha`. Cada uno tiene su sanitización en `cipba_datos_sanitize()` y su `<input>` en `cipba_dato_input_type()`.

### 5.2 Dónde se aplican los marcadores

`cipba_tokens()` se llama a través de `cipba_rich()` (HTML: tokens → `wpautop` → `wp_kses_post`) y `cipba_plain()` (texto: tokens → strip → `esc_html`), definidas en `inc/tramites.php`. Se usan solo en `single-tramite.php` y `single-tramite-pago-matricula.php` (incluidos los títulos de documentos que se listan ahí). **No** se aplican en Entradas, Páginas, ni en `template-parts/normativa.php` / `tad.php` / `honorarios.php`: un título de documento con `{{…}}` se ve crudo en Normativa y TAD. Si hace falta, envolver esos títulos en `cipba_plain()`.

### 5.3 Agregar un dato nuevo

Agregar una entrada en `cipba_datos_fields()`. Con eso:

- aparece en la pantalla, en la sección indicada por `section` (las secciones se arman por orden de aparición);
- queda disponible como `{{clave}}`, como `cipba_dato('clave')` y como `[cipba_dato campo="clave"]`;
- aparece en la cajita "Datos que podés insertar" de los trámites (salvo las claves excluidas en `cipba_tramite_marcadores_render()`).

Agregá el marcador a la tabla del manual de usuario (§3.2). **No renombres claves existentes**: rompe los textos que ya usan el marcador y pierde el valor guardado.

---

## 6. Shortcodes y páginas

| Shortcode | Página (ID local) | Implementación |
|---|---|---|
| `[cipba_eventos]` | Inicio (81) | `inc/shortcodes.php` → `cipba_get_agenda(3)` |
| `[cipba_noticias]` | Inicio | `inc/shortcodes.php`, 5 entradas tipo noticia |
| `[cipba_subcomisiones]` | Inicio | `inc/shortcodes.php`, 3 primeras por `menu_order` |
| `[cipba_sede_foto]` | Inicio | Imagen destacada de la Casa Central |
| `[cipba_sede_principal]`, `[cipba_delegaciones]` | Inicio | Sedes |
| `[cipba_consultor_partidos]` | Inicio | `template-parts/consultor-partidos.php` + `consultor-partidos.js` |
| `[cipba_aviso_honorarios]` | Inicio (bloque reutilizable 82) | `inc/honorarios.php` |
| `[cipba_autoridades]`, `[cipba_sedes]`, `[cipba_partidos]` | Institucional (152) | `template-parts/*-list.php` |
| `[cipba_dato campo="periodo_autoridades"]` | Institucional | `inc/settings.php` |
| `[cipba_subcomisiones_listado]` | Subcomisiones (136) | `template-parts/subcomisiones-list.php` + `subcomisiones.js` |
| `[cipba_areas_contacto]`, `[cipba_boton_whatsapp]`, `[cipba_sedes_contacto]` | Contacto (159) | `inc/shortcodes.php` |
| `[fluentform id="3"]` | Contacto | Fluent Forms (+ `contact-form.js`) |
| `[cipba_novedades]` | Novedades (198) | `template-parts/novedades-list.php` + `novedades.js` |
| `[cipba_honorarios]` | Honorarios mínimos (220) | `template-parts/honorarios.php` |
| `[cipba_normativa]` | Normativa (262) | `template-parts/normativa.php` |
| `[cipba_tad]` | TAD (285) | `template-parts/tad.php` |
| `[cipba_copias_remitidas]` | Copias remitidas (375) | `inc/copias-remitidas.php` + `template-parts/copias-remitidas.php` + `copias-remitidas.js` |

Las páginas combinan bloques `wp:html` (encabezados, secciones fijas con el markup del prototipo) con los shortcodes. Los IDs son los del entorno local; en producción pueden diferir si no se migró la base.

La home (página 81, `page_on_front`) contiene además secciones fijas en HTML: portada, Autogestión, Trámites del Distrito, Preguntas frecuentes (`<details>`) y Contacto por función. La barra de aviso es el bloque reutilizable 82.

---

## 7. Plantillas

### 7.1 `single-tramite.php`

Encabezado (eyebrow, título, bajada, breadcrumb) → bloques (`cipba_get_tramite_bloques()`) → documentos (`cipba_get_tramite_documentos()`) → columna lateral con el cuadro de costos (`cipba_get_tramite_costos()`) → "Otros trámites" (resto de los `tramite` por `menu_order`, excluye el actual). Todo texto pasa por `cipba_rich()` / `cipba_plain()`.

### 7.2 `single-tramite-pago-matricula.php`

WordPress la elige por jerarquía `single-{post_type}-{slug}.php`. **Renombrar el slug del trámite rompe la plantilla y el formulario.** Ventanas emergentes y botones Copiar en `assets/js/pago-matricula.js` (encolado por `cipba_enqueue_pago_script()` solo en ese trámite). El instructivo de Pagomiscuentas está hardcodeado en la plantilla.

### 7.3 `single.php` (Entradas)

Rama por `cipba_tipo_pub()`:

- **Evento:** layout 2/3–1/3 (`.cipba-ev-det`): encabezado + flyer sin recortar (`cipba-flyer`) + cuerpo + ficha/CTA/compartir; lateral `cipba_render_nov_lista_lateral()` (5 últimos eventos y link a `?tipo=evento`). Sin "Otras novedades".
- **Noticia:** hero apaisado + cuerpo (`.cipba-nota__col`) + ficha/CTA/compartir + "Otras novedades".

La ficha, el recuadro CTA y los botones de compartir se comparten vía una closure `$extras`; la galería vía `$galeria` (grilla de `cipba-card-vert`, visor en `novedades.js`). La galería se lee con `get_post_meta( $id, 'galeria', false )` (una fila por imagen).

---

## 8. Módulos por sección

### 8.1 Novedades (`inc/novedades.php`)

- `cipba_tipo_pub()`: `evento` si `tipo_pub = evento`; si no, `noticia` (incluye entradas sin la meta). `cipba_meta_query_tipo()` replica esa regla en consultas (`NOT EXISTS` cuenta como noticia).
- `cipba_get_agenda( $limit )`: eventos por fecha de publicación desc., descartando actividades cuyo `fecha_fin` (o `fecha_inicio`) es anterior a hoy (`current_time('Y-m-d')`). Trae todos los eventos y filtra en PHP: si crecen mucho, conviene pasar el filtro a `meta_query`.
- Listado `/novedades/`: el tab se resuelve en el servidor por `?tipo=` (`cipba_novedades_tipo_actual()`, defecto `evento`); categoría y búsqueda se filtran en el navegador (`novedades.js`). El `<title>` cambia según el tab (`document_title_parts`).
- Tarjetas: `cipba_render_nov_card()` y `cipba_render_nov_dest()` ramifican por tipo (vertical/flyer para eventos, apaisada para noticias).
- Fechas: `cipba_fecha_novedad()` (respeta `fecha_alt`), `cipba_fecha_larga()`, `cipba_actividad_fecha()` (rango "Del 6 al 27 de mayo de 2026").
- CTA: `cipba_novedad_cta()` — `cta_url` > `cta_doc` > Contacto.
- Admin: columna y filtro *Tipo*; `admin-novedad.js` muestra/oculta la caja *Datos de la actividad* según `es_actividad`.

### 8.2 Honorarios (`inc/honorarios.php`)

- `cipba_resolucion_titulo_automatico()` (`wp_insert_post_data`): título = "Resolución CS {numero}".
- `cipba_resolucion_una_sola_vigente()` (`save_post`, prioridad 20): al guardar una vigente, destilda las demás.
- `cipba_get_resoluciones_anteriores( $limit )`: ordena por `fecha_publicacion` o, si falta, `vigencia_desde`. El límite sale del dato `honorarios_anteriores_max` (0 = todas).
- `[cipba_aviso_honorarios]`: barra de la home con código y vigencia desde de la vigente; vacío si no hay.

### 8.3 Documentos (`inc/helpers.php`)

- `cipba_get_documento_file_meta( $id )`: archivo subido (formato por extensión, peso con `size_format`) o, si no hay, `enlace_externo` (formato deducido de la URL o `ENLACE`). `null` si no hay ninguno.
- `cipba_sync_menu_documento_links()` (`save_post`): ítems de menú tipo *Enlace personalizado* con meta `_cipba_documento_id` se actualizan a la URL vigente del documento al guardarlo. La meta se carga a mano (WP-CLI) al crear el ítem.

### 8.4 Pie (`inc/footer.php`)

Quita `primary_footer` y `below_footer` de Astra (`Astra_Builder_Footer`) y pinta: marca + menús `Pie - Trámites`, `Pie - Normativa`, `Pie - Institucional` (por **nombre** de menú, no por ubicación) + Links de interés (`cipba_get_links_interes()`, CPT `link_interes`). Abajo, copyright + menú `Pie - Legal`. **Renombrar uno de esos menús lo hace desaparecer del pie.**

### 8.5 Barra superior y navegación

- `inc/topbar.php`: hook `astra_header_before`; datos de Datos del Distrito; no se imprime si todos están vacíos.
- `inc/navbar.php`: clase `.is-scrolled` en el header; posición del panel mobile de Max Mega Menu (`position:fixed` + top medido del header); ítems de menú con clase `abre-nuevo` o que apuntan a `.pdf` abren en pestaña nueva.

### 8.6 Partidos

`cipba_get_partidos()` lee `assets/data/partidos-distrito-vii.json` (fuente única para Institucional y el consultor de la home). El consultor hace búsqueda sin acentos/mayúsculas, por substring y con Levenshtein de respaldo. No hay mapa interactivo (descartado); el mapa de Institucional es la imagen estática `assets/img/mapa-distrito-vii.png`.

### 8.7 Otros helpers

- `cipba_icon( $name, $size )`: SVG inline. Íconos: `location, phone, mail, clock, award, users, star, search, facebook, linkedin, calendar, copy, close, check, shield, dollar, chevron, home, building, file, download, external, whatsapp`.
- `cipba_social_icons( $dark )`: íconos de redes según Datos del Distrito.
- `cipba_tel_link()`, `cipba_initials()`, `cipba_initials_first_two()`.
- Lectores de slots: `cipba_get_referentes()`, `cipba_get_sede_contactos()`, `cipba_get_area_personas()` (omiten slots vacíos).
- Subida de SVG habilitada (`upload_mimes` + arreglos de metadata) — solo para administradores.

### 8.8 Imágenes (`inc/image-sizes.php`)

| Tamaño | Medidas | Recorte | Uso |
|---|---|---|---|
| `cipba-card` | 600×400 | Sí | Tarjetas apaisadas |
| `cipba-nov-hero` | 1680×720 | Sí | Hero de noticia / destacada |
| `cipba-thumb` | 300×200 | Sí | Noticias de la home |
| `cipba-card-vert` | 480×600 | Sí | Tarjetas de eventos, galería |
| `cipba-flyer` | máx. 900×1600 | No | Flyer completo de eventos |

Al agregar o cambiar un tamaño, las imágenes ya subidas no lo tienen hasta regenerar miniaturas (`wp media regenerate --yes` o un plugin).

### 8.9 Copias remitidas (`inc/copias-remitidas.php`)

Formulario propio, **sin plugin**: Fluent Forms free no trae campo de archivo (es Pro), y el resto de los plugins de formularios con adjuntos son pagos o traen mucho de más.

- **Flujo:** el `<form>` (multipart) postea a la misma página con `?copia=enviar`. `cipba_copias_template_redirect()` (solo si la página tiene el shortcode) marca la página como no cacheable (`DONOTCACHEPAGE`, `litespeed_control_set_nocache`, `nocache_headers()`) y llama a `cipba_copias_procesar()`. Si sale bien, redirige a `?copia=enviada#copias-form` (PRG). Si hay errores, los deja en `cipba_copias_state()` y el template vuelve a mostrar el formulario con los valores cargados.
- **Validación:** nonce `cipba_copias`; campo trampa `cr_web` (si viene completo, se finge éxito); nombre ≤ 120; `is_email`; matrícula con `^[\p{L}\d][\p{L}\d .\/-]{0,24}$`; PDF por extensión **y** por cabecera `%PDF-`; tamaño ≤ `cipba_copias_max_bytes()` (el menor entre `CIPBA_COPIAS_MAX_BYTES` = 10 MB y `wp_max_upload_size()`). Si el archivo supera `post_max_size`, PHP vacía `$_POST` y se informa "demasiado grande". Hay un límite de `CIPBA_COPIAS_MAX_HORA` (6) envíos por IP por hora, contados con un transient.
- **Mail:** `wp_mail()` (o sea FluentSMTP) a `cipba_copias_destinatario()`, que usa el dato `email_copias` o, si está vacío, `email`. Lleva `Reply-To` al matriculado, cuerpo en texto plano y el adjunto con clave de nombre (`array( 'copia-<matrícula>-<nombre>.pdf' => $tmp )`, soportado desde WP 6.2). El PDF se adjunta desde el temporal de PHP y **no se guarda** en el sitio ni en la base.
- **Si falla el envío:** el formulario muestra un error genérico. A un administrador logueado le agrega el detalle de `wp_mail_failed`, que también va al log de PHP; además está **FluentSMTP → Email Logs**. En local, `certificate verify failed` hacia smtp.gmail.com lo causa el antivirus de la PC, que intercepta el SMTP con TLS (por ejemplo Avast Mail Shield): el contenedor no confía en su certificado raíz. Se arregla desactivando el escaneo de SMTP saliente en el antivirus; no es un problema del sitio.
- **Probar sin enviar mails reales:** un mu-plugin temporal con `add_filter( 'pre_wp_mail', … return true; )` que escriba los argumentos a un log. Borrarlo al terminar.
- **Límite de subida:** en el Docker local PHP tiene `upload_max_filesize = 2M`, así que el formulario muestra "hasta 2 MB". En DonWeb, verificar que `upload_max_filesize` y `post_max_size` sean ≥ 10 MB (Ferozo → PHP), o el máximo baja solo.

---

## 9. Hooks del tema

| Hook | Función | Para qué |
|---|---|---|
| `init` | `cipba_register_post_types` | CPT |
| `after_switch_theme` | `cipba_seed_novedades_categories` | Categorías semilla |
| `after_setup_theme` | `cipba_add_image_sizes` | Tamaños de imagen |
| `save_post` (99) | `cipba_tramite_layout_defaults` | Fuerza layout Astra (sin título, ancho completo, sin sidebar) en `tramite` y `post`: Astra guarda `default` y hay que pisarlo tarde |
| `save_post` (20) | `cipba_resolucion_una_sola_vigente` | Una sola resolución vigente |
| `save_post` (20) | `cipba_sync_menu_documento_links` | URLs de menú → archivo vigente |
| `wp_insert_post_data` | `cipba_resolucion_titulo_automatico` | Título de resoluciones |
| `add_meta_boxes` (99) | `cipba_limpiar_metaboxes_cpt` | Quita cajas técnicas |
| `add_meta_boxes` | `cipba_tramite_marcadores_metabox` | Cajita de marcadores |
| `use_block_editor_for_post_type` | `cipba_subcomision_classic_editor` | Editor clásico en CPT |
| `rwmb_meta_boxes` | `cipba_register_meta_boxes` | Campos |
| `astra_header_before` (5) | `cipba_render_topbar` | Barra superior |
| `wp` | `cipba_replace_primary_footer` | Pie propio |
| `template_redirect` | `cipba_copias_template_redirect` | Página de Copias remitidas: sin caché y procesa el POST |
| `nav_menu_link_attributes` | `cipba_menu_link_attributes` | Pestaña nueva |
| `document_title_parts` | `cipba_novedades_document_title` | Título por tab |
| `pre_get_posts` | `cipba_*_admin_default_order`, `cipba_post_admin_tipo_query` | Orden y filtro en admin |
| `wp_head` / `admin_head` (5) | `cipba_favicon_tags` | Favicons (se quita `wp_site_icon`) |

---

## 10. Estilos, tokens y JavaScript

### 10.1 `style.css`

Un único archivo, organizado en bloques con encabezados `/* ===… */` que referencian las secciones del handoff (`project/Handoff WordPress - CIPBA Distrito VII.md`): §4.x por sección de la home, §5.x por patrones (navbar sticky, tarjetas, badges, avatares, columna fija de trámites…), y al final bloques por página: Subcomisiones, cabecera interna, Sedes, Autoridades, Institucional, Barra superior, Contacto, **Trámites**, **Medios de pago**, **Novedades**, **Honorarios**, **Normativa**, **TAD**, **Galería**. Para encontrar algo, buscá la clase `cipba-…` del markup.

Hay muchos `!important` en la zona de Max Mega Menu: su CSS dinámico usa selectores de dos IDs.

**Regla obligatoria:** cada cambio en `style.css` (o en un JS) requiere subir `CHILD_THEME_CIPBA_VERSION` en `functions.php`. Es el `?ver=` de los assets: sin eso, navegadores y cachés siguen sirviendo la versión vieja.

### 10.2 `theme.json`

Paleta (`verde-500` marca, `verde-700` profundo, `purpura-500` institucional, `rojo-800`…), gradientes (`hero-verde`, `cta-verde`), tipografías locales (Lato y Roboto Condensed en `assets/fonts/`), escala de tamaños y layout de 1200 px. En CSS se usan como `var(--wp--preset--color--verde-700)` con fallback hex.

### 10.3 JavaScript

Vanilla, encolados con la versión del tema y solo donde se usan:

| Archivo | Dónde | Qué hace |
|---|---|---|
| `novedades.js` | `[cipba_novedades]` y `single.php` | Filtros/buscador del listado, copiar enlace, visor de galería |
| `pago-matricula.js` | Medios de pago | Ventanas emergentes y botones Copiar |
| `subcomisiones.js` | `[cipba_subcomisiones_listado]` | Buscador |
| `partidos.js` | `[cipba_partidos]` | Buscador |
| `consultor-partidos.js` | `[cipba_consultor_partidos]` | Búsqueda difusa de partidos |
| `contact-form.js` | Páginas con `[fluentform]` | Desvanece el mensaje de envío |
| `copias-remitidas.js` | `[cipba_copias_remitidas]` | Zona para arrastrar el PDF, nombre/peso del archivo, chequeo de tipo y tamaño, botón "Enviando…" |
| `admin-novedad.js` | Admin, edición de Entradas | Muestra/oculta *Datos de la actividad* |
| `sticky-header.js` | — | Vacío y sin encolar (resto; se puede borrar) |

El script de sombra del header y el del panel mobile están inline en `inc/navbar.php`.

---

## 11. Recetas: cómo extender el tema

### Agregar un campo a un tipo existente
1. Agregalo en la caja correspondiente de `inc/meta-boxes.php`.
2. Leelo en la plantilla/shortcode con `get_post_meta( $id, 'clave', true )` (o `false` para campos multi-fila como `image_advanced`/`file_advanced`).
3. Si es texto de trámite, pasalo por `cipba_plain()`/`cipba_rich()` para que admita marcadores.
4. Estilos en el bloque de la sección en `style.css` + subir versión.
5. Documentalo en el manual de usuario.

### Ampliar slots (más bloques, documentos, referentes…)
Cambiar la constante (§4.3). Formulario y lectura se adaptan solos.

### Agregar un tipo de contenido nuevo
1. `register_post_type()` en `cipba_register_post_types()` (`public => false` si se muestra solo por shortcode; `show_in_rest => true`).
2. Sumarlo a `cipba_subcomision_classic_editor()` y `cipba_limpiar_metaboxes_cpt()`, y su placeholder de título.
3. Caja de campos en `meta-boxes.php`.
4. Columnas y orden del admin (copiar el patrón de `link_interes`).
5. Shortcode en `inc/shortcodes.php` + markup en `template-parts/`.
6. Insertar el shortcode en la página (contenido de la base → replicar en producción).
7. Si es público, guardar *Ajustes → Enlaces permanentes* para regenerar reglas.

### Caja de campos solo para una página
Registrarla dentro de `if ( cipba_admin_editando_pagina( 'slug' ) )`, como TAD.

### Agregar un color de categoría
Agregar la entrada en `cipba_cat_paletas()` (clave, etiqueta, tinta, fondo). Revisá que el contraste tinta/fondo sea suficiente (≥ 4.5:1).

### Agregar un ícono
Agregar el path SVG en `cipba_icon()` y, si se elige desde el panel, la opción en el `select` correspondiente (trámites o áreas).

### Agregar un tipo de bloque a los trámites
Opción en `bloque{i}_tipo` (`meta-boxes.php`), sumarlo a `$validos` en `cipba_get_tramite_bloques()`, render en `single-tramite.php` y estilos en el bloque *Trámites*.

### Mostrar un dato general en una página
`[cipba_dato campo="clave"]` en un bloque; para un `href`, `[cipba_dato campo="whatsapp" formato="url"]`.

### Editar la lista de partidos
`assets/data/partidos-distrito-vii.json` (viaja con el tema).

---

## 12. Despliegue

Procedimiento completo: **`project/runbook-migracion-manual.md`** (DonWeb/Ferozo, sin SSH). Contexto y endurecimiento post-incidente: `project/plan-limpieza-migracion.md`.

### 12.1 Ciclo de cambios

| Cambió | Qué subir |
|---|---|
| Solo tema (PHP/CSS/JS) | Carpeta `themes/cipba/` por FTP, **con la versión subida** |
| Contenido, menús, opciones | Base (runbook Fases 1 y 6) — **solo mientras producción no tenga contenido real propio** |
| Plugins o uploads | `wp-content` (Fases 2 y 5) |

Una vez que producción tenga contenido cargado ahí: **no reimportar la base**. Cambios de estructura que viven en la base (shortcodes en páginas, menús, documentos semilla) se replican a mano o con un script WP-CLI ejecutado en local y reproducido en el panel.

### 12.2 Checklist después de desplegar

- *Meta Box* activo (si no, las pantallas quedan sin campos).
- *Ajustes → Enlaces permanentes → Guardar* si `/tramites/…` da 404.
- *Mega Menu → Menu Themes → Guardar* si el menú pierde estilos (regenera su CSS).
- Purgar la caché del servidor (LiteSpeed Cache → Purge All) si los visitantes ven la versión vieja.
- ID del formulario en `[fluentform id="3"]` si el formulario se recreó a mano.
- SMTP configurado (FluentSMTP) para que lleguen los mails del formulario.

### 12.3 Seguridad (resumen)

`DISALLOW_FILE_EDIT` y `DISALLOW_FILE_MODS` en producción (los plugins se actualizan en local y se suben), salts nuevos, Wordfence, sin plugins inactivos, sin registro abierto, revisar usuarios y plugins periódicamente. Antes de empaquetar: `scripts/chequeo-pre-deploy.php`.

---

## 13. Gotchas conocidos

- **Meta Box free:** sin `group` clonable y sin `include`/`exclude` → slots numerados y cajas condicionales en PHP. Dos cajas con las mismas claves en la misma pantalla se pisan al guardar.
- **Astra y el layout:** Astra escribe `default` en sus metas de layout al guardar; por eso `cipba_tramite_layout_defaults` corre en `save_post` prioridad 99.
- **Menú mobile:** si aparece un menú inesperado con todas las páginas, revisar `wp menu location list`: una ubicación sin menú asignado hace caer a `wp_list_pages()`.
- **Entradas con fecha futura** quedan `future` (programadas), no `publish`: no se ven hasta esa fecha.
- **Imágenes:** tamaños nuevos no existen para imágenes viejas hasta regenerar miniaturas.
- **`file_advanced` / `image_advanced`** guardan una fila de meta por archivo: leer con `get_post_meta( …, false )`, no con `rwmb_meta()` si se quiere solo el ID.
- **PowerShell 5.1** agrega BOM al escribir archivos con pipes (rompió un `.htaccess`): escribir archivos del servidor desde el contenedor o con herramientas que no agreguen BOM.
- **Heredocs grandes en Git Bash** fallan a veces: escribir el archivo y luego `docker cp`.
- **Rutas largas (MAX_PATH)** en `vendor/` de algunos plugins: empaquetar `wp-content` dentro del contenedor (`zip-wp-content.php`), no en Windows.
- **Editor de bloques + automatización de navegador:** la pantalla de Gutenberg suele congelar las herramientas de automatización; probar a mano.
- **Slugs con significado:** `pago-matricula` (plantilla y formulario de pago), `tad` (caja TAD), `novedades` (título por tab), nombres de menús `Pie - …` (pie). No cambiarlos sin tocar el código.

---

## 14. Pendientes y problemas conocidos

- **Sanitización de `numero` en Datos del Distrito:** `cipba_datos_sanitize()` usa `'/D+/'` en vez de `'/\D+/'` para el tipo `numero` (`inc/settings.php`), así que no quita caracteres no numéricos (solo la letra "D"). Hoy no rompe nada porque el `<input type="number">` ya lo restringe en el navegador, pero conviene corregirlo.
- **Verificación en mobile:** varias vistas (detalle de evento, galería, TAD) no se verificaron visualmente en anchos de celular.
- **Guardado desde el editor de bloques:** el mecanismo de Meta Box está verificado en pantallas clásicas; en Entradas conviene una prueba manual después de cada actualización de Meta Box o de WordPress.
- **Contenido creado por WP-CLI en local** (documentos de Normativa y TAD, foto de la sede, shortcodes insertados en páginas, categoría *Actualidad*): si no se migra la base, hay que recrearlo en producción.
- **Instructivo de Pagomiscuentas** hardcodeado en `single-tramite-pago-matricula.php`; candidato a campos editables.
- **`/novedades/`**: el listado completo no distingue visualmente el tipo dentro de cada tab (posible mejora: etiqueta por tipo).
- **Datos a confirmar con el Colegio:** datos bancarios y teléfonos generales cargados desde el prototipo.
- **`assets/js/sticky-header.js`:** vacío y sin uso.
