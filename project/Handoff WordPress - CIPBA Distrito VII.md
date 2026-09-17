# Sitio CIPBA Distrito VII en WordPress — Handoff de implementación

Traducción del diseño aprobado a un stack de tema de bloques Astra, Elementor y librería de bloques gratuita, sin addons de pago ni licencias.

- **Diseño de referencia:** https://adhentux.com/cipba/index.html#inicio
- **Design system:** CIPBA Distrito VII V2
- **Versión:** 1.0

---

## 1. Alcance, stack y decisión de arquitectura

El diseño se compone de una home de once bloques, cinco páginas institucionales y de trámites construidas sobre una misma estructura reutilizable, una sección de normativa con documentos descargables, una página de honorarios mínimos y un sistema de novedades con listado filtrable y ficha de detalle. Todo el diseño usa una paleta verde monocromática con un acento púrpura, dos familias tipográficas y una escala de espaciado corta.

### Stack propuesto

| Capa | Elección | Motivo |
|---|---|---|
| Tema | Astra (free) + tema hijo `astra-cipba` | El tema hijo es obligatorio: es donde viven `theme.json`, el CSS adicional y los registros de contenido. Sin hijo, cada actualización de Astra borra el trabajo. |
| Editor de página | Bloques nativos + Spectra (free) | Spectra es de los mismos autores que Astra, no pide licencia y aporta contenedor con enlace, iconos, carrusel de entradas y acordeón: exactamente los cuatro huecos que deja Gutenberg en este diseño. |
| Elementor | Free, acotado a plantillas puntuales | Se usa donde su editor visual acelera el armado (página de contacto, landings de campaña). No se usa para la home ni para plantillas de archivo. |

> **⚠️ Decisión que hay que tomar antes de empezar: Elementor y theme.json no comparten tokens**
>
> Elementor no lee `theme.json`. Sus widgets se estilan desde *Site Settings → Global Colors / Global Fonts*, un sistema paralelo. Si se mezclan bloques y Elementor en la misma página, los tokens quedan duplicados y se desincronizan al primer cambio de paleta.
>
> **Recomendación:** construir el sitio con bloques + Spectra y mantener Elementor como herramienta secundaria, con sus Global Colors cargados con los mismos valores hex (tabla en §3). Si el equipo prefiere Elementor como editor principal, `theme.json` pasa a ser solo la fuente de verdad del editor de bloques y hay que aceptar el mantenimiento doble: está documentado en §9 como riesgo abierto.

> **Nota sobre "tema de bloques"**
>
> Astra es un tema clásico con soporte de editor de bloques, no un tema de bloques FSE: no permite editar cabecera, pie ni plantillas desde el Editor del sitio. Sí lee `theme.json` para tokens y estilos (WordPress lo soporta en temas clásicos desde 5.9). La cabecera y el pie se arman con el Header/Footer Builder de Astra, no con bloques de plantilla. Si el requisito de FSE real es firme, el reemplazo free más cercano es Blocksy o Ollie; se pierde el Header Builder de Astra y hay que rehacer §4.1 y §4.13.

---

## 2. theme.json completo

Archivo `wp-content/themes/astra-cipba/theme.json`. Los nombres de slug replican los tokens del design system, de modo que un cambio de paleta se hace en un solo archivo. Las tipografías se sirven localmente (§7), no desde Google Fonts, para cumplir con la lectura estricta del RGPD europeo y ganar velocidad.

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "settings": {
    "appearanceTools": true,
    "useRootPaddingAwareAlignments": true,
    "layout": { "contentSize": "1200px", "wideSize": "1200px" },
    "color": {
      "defaultPalette": false,
      "defaultGradients": false,
      "defaultDuotone": false,
      "custom": false,
      "customGradient": true,
      "palette": [
        { "slug": "verde-100",  "name": "Verde 100 (superficie tinte)", "color": "#e8f5f1" },
        { "slug": "verde-200",  "name": "Verde 200 (borde tinte)",      "color": "#c0e2ca" },
        { "slug": "verde-500",  "name": "Verde marca",                  "color": "#00a48a" },
        { "slug": "verde-600",  "name": "Verde marca hover",            "color": "#008c75" },
        { "slug": "verde-700",  "name": "Verde profundo",               "color": "#14484a" },
        { "slug": "verde-800",  "name": "Verde profundo hover",         "color": "#1a5f5c" },
        { "slug": "verde-950",  "name": "Verde casi negro",             "color": "#0d3234" },
        { "slug": "tinta-900",  "name": "Tinta (texto)",                "color": "#373d3e" },
        { "slug": "gris-teal",  "name": "Gris teal (texto secundario)", "color": "#607a7c" },
        { "slug": "gris-400",   "name": "Gris 400 (texto mudo)",        "color": "#9aa8a9" },
        { "slug": "gris-100",   "name": "Gris 100 (fondo sección)",     "color": "#eef2f1" },
        { "slug": "gris-50",    "name": "Gris 50 (fondo sección alt)",  "color": "#f7faf9" },
        { "slug": "blanco",     "name": "Blanco",                       "color": "#ffffff" },
        { "slug": "purpura-500","name": "Púrpura institucional",        "color": "#484586" },
        { "slug": "purpura-100","name": "Púrpura tinte",                "color": "#e7e6f3" },
        { "slug": "rojo-800",   "name": "Rojo tierra (taxonomía)",      "color": "#8a3a2a" },
        { "slug": "whatsapp",   "name": "WhatsApp",                     "color": "#25d366" }
      ],
      "gradients": [
        { "slug": "hero-verde", "name": "Hero verde profundo",
          "gradient": "linear-gradient(135deg,#14484a 0%,#1a5f5c 60%,#00a48a 100%)" },
        { "slug": "cta-verde",  "name": "Banner CTA verde",
          "gradient": "linear-gradient(135deg,#00a48a 0%,#008c75 100%)" }
      ]
    },
    "typography": {
      "fluid": true,
      "defaultFontSizes": false,
      "customFontSize": true,
      "fontFamilies": [
        { "slug": "lato", "name": "Lato", "fontFamily": "Lato, system-ui, sans-serif",
          "fontFace": [
            { "fontFamily": "Lato", "fontWeight": "300", "fontStyle": "normal", "fontDisplay": "swap",
              "src": [ "file:./assets/fonts/lato-300.woff2" ] },
            { "fontFamily": "Lato", "fontWeight": "400", "fontStyle": "normal", "fontDisplay": "swap",
              "src": [ "file:./assets/fonts/lato-400.woff2" ] },
            { "fontFamily": "Lato", "fontWeight": "700", "fontStyle": "normal", "fontDisplay": "swap",
              "src": [ "file:./assets/fonts/lato-700.woff2" ] },
            { "fontFamily": "Lato", "fontWeight": "900", "fontStyle": "normal", "fontDisplay": "swap",
              "src": [ "file:./assets/fonts/lato-900.woff2" ] }
          ] },
        { "slug": "condensed", "name": "Roboto Condensed",
          "fontFamily": "'Roboto Condensed', 'Arial Narrow', sans-serif",
          "fontFace": [
            { "fontFamily": "Roboto Condensed", "fontWeight": "400", "fontStyle": "normal", "fontDisplay": "swap",
              "src": [ "file:./assets/fonts/roboto-condensed-400.woff2" ] },
            { "fontFamily": "Roboto Condensed", "fontWeight": "700", "fontStyle": "normal", "fontDisplay": "swap",
              "src": [ "file:./assets/fonts/roboto-condensed-700.woff2" ] },
            { "fontFamily": "Roboto Condensed", "fontWeight": "900", "fontStyle": "normal", "fontDisplay": "swap",
              "src": [ "file:./assets/fonts/roboto-condensed-900.woff2" ] }
          ] }
      ],
      "fontSizes": [
        { "slug": "xs",    "name": "XS 11",        "size": "11px" },
        { "slug": "sm",    "name": "SM 13",        "size": "13px" },
        { "slug": "base",  "name": "Base 14",      "size": "14px" },
        { "slug": "md",    "name": "MD 16",        "size": "16px" },
        { "slug": "lg",    "name": "LG 20",        "size": "20px" },
        { "slug": "card",  "name": "Título card 22","size": "22px" },
        { "slug": "xl",    "name": "XL sección",   "size": "clamp(22px,3vw,30px)",
          "fluid": { "min": "22px", "max": "30px" } },
        { "slug": "hero",  "name": "Hero / H1",    "size": "clamp(26px,4vw,44px)",
          "fluid": { "min": "26px", "max": "44px" } }
      ]
    },
    "spacing": {
      "padding": true, "margin": true, "blockGap": true,
      "units": [ "px", "rem", "%", "vw" ],
      "defaultSpacingSizes": false,
      "spacingSizes": [
        { "slug": "10", "name": "1 — 4",   "size": "4px" },
        { "slug": "20", "name": "2 — 8",   "size": "8px" },
        { "slug": "30", "name": "3 — 12",  "size": "12px" },
        { "slug": "40", "name": "4 — 16",  "size": "16px" },
        { "slug": "50", "name": "5 — 20",  "size": "20px" },
        { "slug": "60", "name": "6 — 24",  "size": "24px" },
        { "slug": "70", "name": "8 — 32",  "size": "32px" },
        { "slug": "80", "name": "10 — 40", "size": "40px" },
        { "slug": "90", "name": "Sección 52", "size": "52px" },
        { "slug": "100","name": "Sección L 60","size": "60px" }
      ]
    },
    "shadow": {
      "defaultPresets": false,
      "presets": [
        { "slug": "card",  "name": "Card reposo", "shadow": "0 2px 8px rgba(0,0,0,.04)" },
        { "slug": "hover", "name": "Card hover",  "shadow": "0 8px 24px rgba(0,0,0,.10)" },
        { "slug": "nav",   "name": "Navbar scroll","shadow": "0 2px 20px rgba(0,0,0,.25)" },
        { "slug": "hero",  "name": "Card sobre hero","shadow": "0 12px 40px rgba(13,45,94,.18)" }
      ]
    },
    "border": { "color": true, "radius": true, "style": true, "width": true },
    "custom": {
      "radius": { "btn": "6px", "card": "10px", "cardLg": "12px", "badge": "20px", "icon": "8px" },
      "borderCard": "#e8f5f1",
      "borderInput": "#dbe6e3",
      "duration": { "fast": ".2s", "base": ".3s", "slow": ".8s" },
      "ease": "cubic-bezier(.4,0,.2,1)",
      "leading": { "tight": "1.15", "snug": "1.25", "base": "1.5", "relaxed": "1.65" },
      "cat": {
        "institucional": "#14484a", "capacitacion": "#00a48a", "normativa": "#8a3a2a",
        "visado": "#607a7c", "honorarios": "#8a3a2a", "matricula": "#00a48a", "comisiones": "#484586"
      }
    }
  },
  "styles": {
    "color": { "background": "var(--wp--preset--color--blanco)", "text": "var(--wp--preset--color--tinta-900)" },
    "typography": {
      "fontFamily": "var(--wp--preset--font-family--lato)",
      "fontSize": "var(--wp--preset--font-size--base)",
      "fontWeight": "400",
      "lineHeight": "1.6"
    },
    "spacing": {
      "blockGap": "var(--wp--preset--spacing--50)",
      "padding": { "left": "24px", "right": "24px" }
    },
    "elements": {
      "link": {
        "color": { "text": "var(--wp--preset--color--verde-600)" },
        "typography": { "textDecoration": "none" },
        ":hover": { "color": { "text": "var(--wp--preset--color--verde-500)" },
                    "typography": { "textDecoration": "underline" } },
        ":focus": { "color": { "text": "var(--wp--preset--color--verde-700)" } }
      },
      "heading": {
        "typography": { "fontFamily": "var(--wp--preset--font-family--condensed)",
                        "fontWeight": "900", "lineHeight": "1.15", "letterSpacing": "-0.5px" },
        "color": { "text": "var(--wp--preset--color--verde-700)" }
      },
      "h1": { "typography": { "fontSize": "var(--wp--preset--font-size--hero)" } },
      "h2": { "typography": { "fontSize": "var(--wp--preset--font-size--xl)" } },
      "h3": { "typography": { "fontSize": "var(--wp--preset--font-size--card)", "fontWeight": "700" } },
      "h4": { "typography": { "fontSize": "var(--wp--preset--font-size--lg)", "fontWeight": "700" } },
      "button": {
        "color": { "background": "var(--wp--preset--color--verde-500)",
                   "text": "var(--wp--preset--color--blanco)" },
        "typography": { "fontFamily": "var(--wp--preset--font-family--lato)",
                        "fontWeight": "700", "fontSize": "var(--wp--preset--font-size--sm)" },
        "border": { "radius": "6px" },
        "spacing": { "padding": { "top": "10px", "bottom": "10px", "left": "20px", "right": "20px" } },
        ":hover": { "color": { "background": "var(--wp--preset--color--verde-600)" } },
        ":focus": { "color": { "background": "var(--wp--preset--color--verde-600)" } }
      },
      "caption": { "typography": { "fontSize": "var(--wp--preset--font-size--xs)" },
                   "color": { "text": "var(--wp--preset--color--gris-400)" } }
    },
    "blocks": {
      "core/group":   { "spacing": { "padding": { "top": "52px", "bottom": "52px" } } },
      "core/columns": { "spacing": { "blockGap": { "top": "20px", "left": "18px" } } },
      "core/separator": { "color": { "text": "var(--wp--preset--color--verde-100)" } },
      "core/quote": { "border": { "left": { "color": "var(--wp--preset--color--verde-500)", "width": "3px", "style": "solid" } },
                      "spacing": { "padding": { "left": "18px" } } },
      "core/post-title": { "typography": { "fontSize": "var(--wp--preset--font-size--card)", "fontWeight": "700" } },
      "core/post-terms": { "typography": { "fontSize": "var(--wp--preset--font-size--xs)", "fontWeight": "700",
                                           "textTransform": "uppercase", "letterSpacing": "1px" } },
      "core/table": { "typography": { "fontSize": "var(--wp--preset--font-size--sm)" },
                      "border": { "color": "var(--wp--preset--color--verde-100)", "width": "1px", "style": "solid" } }
    }
  }
}
```

> **Tres ajustes de Astra que hay que hacer para que theme.json gobierne**
>
> Astra escribe su propio CSS desde el Personalizador y, por especificidad, le gana a `theme.json` en tipografía y color de cuerpo. En *Apariencia → Personalizar*: (1) en *Tipografía → Cuerpo* y *Encabezados*, dejar la familia en "Predeterminado" para que herede la del tema hijo; (2) en *Colores*, cargar la paleta global de Astra con los mismos siete hex primarios; (3) en *Contenedor*, ancho 1200 px y padding 24 px, para que coincida con `contentSize`. Sin estos tres pasos el sitio se ve con la tipografía de Astra y los tokens quedan de adorno.

---

## 3. Mapeo de tokens a Elementor (si se usa)

Se cargan en *Elementor → Site Settings*. Los nombres se mantienen idénticos a los slugs de `theme.json` para poder auditar desincronizaciones de un vistazo.

| Global Color | Valor | Global Font | Definición |
|---|---|---|---|
| Primary — verde-700 | `#14484a` | Primary | Roboto Condensed 900 · 44/1.1 · −0.5px |
| Secondary — verde-500 | `#00a48a` | Secondary | Roboto Condensed 700 · 30/1.2 |
| Text — tinta-900 | `#373d3e` | Text | Lato 400 · 14/1.6 |
| Accent — verde-600 | `#008c75` | Accent | Lato 700 · 13/1.4 (botones y labels) |
| Custom — verde-100 / 200 | `#e8f5f1` · `#c0e2ca` | — | Superficies de tinte y bordes de card |
| Custom — gris-100 / 50 | `#eef2f1` · `#f7faf9` | — | Fondos de sección alternos |
| Custom — púrpura-500 | `#484586` | — | Acento de autogestión y taxonomía Comisiones |
| Custom — rojo-800 | `#8a3a2a` | — | Solo etiquetas Normativa / Honorarios |

En *Site Settings → Layout*: Content Width 1200 px, Widgets Space 20 px, breakpoints según §6.

---

## 4. Sección por sección

Para cada sección: el bloque o widget que la resuelve, su configuración concreta, y qué parte necesita CSS o PHP. Las marcas **[config]**, **[CSS]** y **[PHP]** indican el tipo de trabajo. Todo el CSS referido está en §5.

### 4.1 Barra superior (TopBar) — [config] [CSS]

Astra *Header Builder → Above Header*. Fondo `verde-700`, texto `rgba(255,255,255,.85)` a 12.5 px, alto 7 px de padding vertical, borde inferior `rgba(255,255,255,.1)`. Izquierda: elemento *Text/HTML* con teléfono, mail y horario. Derecha: elemento *Text/HTML* con los enlaces a Consejo Superior y buscador de matriculados.

**Ojo:** el elemento *Social* del Header Builder es Pro. Los iconos de redes se hacen con un *HTML* element y SVG inline (§7), que además evita cargar una librería de iconos. El horario y el enlace a Consejo Superior se ocultan bajo 700 px con la regla `.tb-hours` de §5.

### 4.2 Navbar sticky con desplegables agrupados — [config] [CSS]

Astra *Primary Header*: fondo blanco, logo a la izquierda (§7), menú a la derecha, alto 66 px. El menú *Principal* tiene cinco entradas de primer nivel; Trámites y Normativa abren paneles con subtítulos de grupo ("Matrícula", "Sistemas", "Consulta"), que el menú nativo de WordPress no sabe hacer.

- **Mega menú agrupado:** Max Mega Menu (free). Cada columna es un grupo; el subtítulo se consigue con un ítem de menú de tipo etiqueta y la clase `mm-group-title`.
- **Sticky:** el sticky header de Astra es Pro. Alternativa free: *Sticky Menu (or Anything) on Scroll*, con selector `.ast-main-header-wrap`, o las 8 líneas de CSS/JS de §5.2 que además reproducen la sombra y el `backdrop-filter` del diseño.
- **Ítem destacado "Honorarios mínimos vigentes":** clase CSS `is-destacado` en el ítem de menú (campo Clases CSS, visible desde Opciones de pantalla) + regla §5.3: punto verde, texto `verde-600` en 800 y fondo `#f1faf7`.
- **Enlaces externos y PDFs:** los cuatro PDFs de normativa, Visado online, SIGMA, Otros trámites y Resoluciones abren en pestaña nueva. En el menú de WordPress esto no es una casilla nativa: se marca con la clase `abre-nuevo` y el snippet PHP §5.9 les agrega `target="_blank" rel="noopener"`.

### 4.3 Barra de anuncio (honorarios vigentes) — [config]

Patrón sincronizado ("reusable block") llamado `anuncio-honorarios`: Group a ancho completo, fondo `verde-100`, borde inferior 1 px `verde-200`, padding 10/24. Dentro, un Row (justificación izquierda, gap 10) con icono de balanza (Spectra Icon, 14 px, `verde-700`), párrafo 13.5 px y un enlace "Ver tabla" en `verde-600` 800. Al ser patrón sincronizado, actualizar la resolución vigente se hace una vez y cambia en todas las páginas.

### 4.4 Hero de dos puertas — [config] [CSS]

Group con el gradiente `hero-verde` del preset y el patrón crosshatch como imagen de fondo (§7, repeat, opacidad simulada con un overlay a 6 %). Dentro, Columns de 2 con gap 18 px. Cada puerta es un **Spectra Container con enlace** — el Group nativo no acepta enlace, y envolver todo en un `<a>` a mano rompe la edición visual. Contenido: eyebrow 12 px mayúsculas `verde-200`, título Roboto Condensed 700 22 px, descripción Lato 13.5 px. Colapsa a una columna bajo 700 px.

### 4.5 Autogestión y Trámites (grillas de tarjetas) — [config] [CSS]

Dos patrones de tres tarjetas cada uno, sobre fondos alternos (blanco para Autogestión, `#f2f3f3` para Trámites, con borde superior `verde-100`). Cada tarjeta: Spectra Container con enlace, radio 10 px, borde 1 px `verde-100`, **borde izquierdo 3 px** del color de acento (verde `#00a48a` para trámites propios, púrpura `#484586` para sistemas externos), fondo `#f2faf7` o `#f6f5fb`, padding 20/22/24.

La elevación en hover (`translateY(-3px)` + sombra teñida + flecha que se desplaza 3 px) no es configurable en Spectra free con ese nivel de detalle: va en §5.4. Las tarjetas de sistemas externos (Honorarios, Visado online, SIGMA) llevan icono de enlace externo en lugar de flecha y abren en pestaña nueva.

### 4.6 Eventos y novedades (carrusel) — [config] [PHP]

Se registra el tipo de contenido `evento` (§4.14) y se muestra con el **Post Carousel de Spectra**: 3 visibles en desktop, 2 en tablet, 1 en móvil, flechas sí, puntos no, autoplay apagado. La etiqueta de categoría usa los colores de taxonomía de `settings.custom.cat` vía §5.5.

> **⚠️ Si el carrusel se resiste**
>
> El Loop Carousel de Elementor es Pro y no hay equivalente nativo. Dos salidas sin costo: (a) Query Loop nativo en grilla de 3 con enlace "Ver toda la agenda" — pierde el desplazamiento horizontal pero gana accesibilidad y cero JS; (b) Swiper 11 desde el CDN con 20 líneas de inicialización. La opción (a) es la recomendada si el volumen de eventos es bajo, que es el caso hoy.

### 4.7 Noticias (4 tarjetas) — [config] [CSS]

Query Loop nativo: post type `post`, 4 entradas, orden por fecha descendente, grilla de 4 columnas (3 bajo 1080 px, 2 bajo 900 px, 1 bajo 520 px). Plantilla de cada ítem: Post Terms (categoría) → Post Date (`d/m/Y`) → Post Title (h3, 22 px) → Post Excerpt (2 líneas, 13.5 px `gris-teal`). El badge de categoría se colorea por slug con §5.5; el recorte a dos líneas, con §5.6.

### 4.8 Subcomisiones — [config] [PHP]

CPT `subcomision` con campos ACF: `tag` (sigla), `referentes` (repeater: nombre, matrícula, teléfono, mail). En la home, Query Loop de 3 con enlace al listado completo; en la página interna, Query Loop sin límite con la tarjeta ampliada (avatar de iniciales sobre gradiente, contacto directo del referente). El avatar de iniciales se genera con CSS a partir de la inicial, no con imagen (§5.7).

### 4.9 El Distrito VII: mapa y consultor de partidos — [CSS] [PHP]

Esta sección **no se resuelve con configuración**. Combina un mapa Leaflet con los 23 partidos del distrito y un selector que responde si un partido pertenece o no al Distrito VII. Ningún plugin de mapas free (WP Go Maps, Leaflet Map) permite cargar el GeoJSON de partidos con estilos por feature y vincularlo a un selector.

**Implementación:** un bloque de HTML personalizado o un shortcode en el tema hijo que encola Leaflet 1.9 desde CDN (o local), carga `partidos-distrito-vii.geojson` (§7) y renderiza el selector. Es el único componente del sitio con JavaScript propio de peso. Presupuestar entre 4 y 8 horas y tratarlo como pieza de mantenimiento aparte.

### 4.10 Preguntas frecuentes — [config]

Bloque **Detalles** nativo (WP 6.3+), uno por pregunta, dentro de un Group con fondo `gris-50`. Es accesible por defecto y no requiere plugin. Si se busca marcado `FAQPage` para buscadores, el bloque FAQ de Spectra lo emite automáticamente; el acordeón de Elementor free no.

Detalle del diseño: la primera pregunta aparece abierta. Con el bloque Detalles se logra marcando `open` en la primera instancia.

### 4.11 Contacto por función — [config]

Group fondo `gris-50`, borde superior `verde-100`, padding 54 px. Columns de 2 con dos tarjetas (Mesa de entradas, Área técnica y visado), cada una con `mailto:`. Debajo, grilla de redes de 4 iconos con SVG inline. Si se agrega formulario, *Fluent Forms* o *WPForms Lite* (§5 para estilar inputs con `borderInput` y el anillo de foco verde).

### 4.12 Cabecera plana de páginas internas — [config] [CSS]

Las páginas de trámites, subcomisiones y novedades ya no usan hero con gradiente. La cabecera es: fila de breadcrumb sobre blanco (12.5 px, `gris-400`, borde inferior `verde-100`, padding 11/24) y bloque de título (eyebrow verde mayúsculas, h1 `verde-700` en `clamp(26px,6vw,44px)`, descripción 16.5 px `gris-teal` con ancho máximo 760 px).

Se arma como patrón `cabecera-plana` con tres campos editables. Breadcrumbs: los de Astra free (*Personalizar → Breadcrumb*) o los de Rank Math si se instala por SEO — no ambos, para no duplicar marcado estructurado.

La estructura interior de las páginas de trámites es una grilla de `1.55fr / 1fr` con columna lateral fija al hacer scroll. Columns nativo da la proporción (61 % / 39 %); el `position: sticky` de la columna lateral necesita §5.8.

### 4.13 Pie de página — [config] [PHP]

Astra *Footer Builder*, dos filas. *Primary Footer*: cuatro columnas — bloque de marca (logo, referencia a la Ley 10.416, iconos de redes) y tres menús de enlaces (Trámites, Normativa, Institucional, Links de interés). Cada columna es un widget *Navigation Menu*, no un bloque de lista, para que los enlaces se administren desde *Apariencia → Menús*. *Below Footer*: copyright a la izquierda, enlaces legales a la derecha, fondo `verde-950`.

Los enlaces externos del pie (Visado online, SIGMA, Resoluciones) y los PDFs abren en pestaña nueva con el mismo snippet §5.9. Bajo 1000 px el pie pasa a dos columnas con la de marca a ancho completo; bajo 640 px, a una.

### 4.14 Contenido a registrar — [PHP]

| Tipo | Campos (ACF free) | Uso |
|---|---|---|
| post (nativo) | categoría, imagen destacada, destacado (true/false) | Novedades: listado filtrable y ficha de detalle. Imagen destacada en 21:9 en la ficha (§5.10). |
| evento | fecha_inicio, cupo, inscripcion_abierta, cierre_inscripcion, lugar | Carrusel de agenda en la home; orden por `fecha_inicio` ascendente filtrando pasados. |
| documento | archivo (File), formato, peso, origen, orden | Normativa. `peso` y `formato` se leen del propio objeto de ACF; no se tipean a mano. |
| resolucion | numero, fecha_publicacion, vigencia_desde, vigente (bool), anexos (repeater: título, archivo, descripción) | Honorarios mínimos: bloque vigente destacado + pila histórica cerrada con ventana de vigencia. |
| subcomision | tag, referentes (repeater) | Home y página de subcomisiones. |
| tramite | — | Opcional. Las cuatro páginas de trámites comparten estructura; con CPT se unifica la plantilla y el bloque "Otros trámites" se autogenera. Si se dejan como páginas, ese bloque se mantiene a mano. |

Los CPT se registran en el tema hijo con `register_post_type`, no con un plugin de UI: así viajan con el tema y no se pierden si se desactiva el plugin. **Excepción razonable:** si el equipo no tiene perfil de desarrollo, Custom Post Type UI (free) es aceptable, asumiendo la dependencia.

### 4.15 Listado de novedades con filtros — [config] [CSS]

Barra de filtros fija (sticky a 66 px del borde superior) con chips de categoría y campo de búsqueda, sobre un Query Loop paginado. El Query Loop nativo no tiene facetas en el front-end.

- **Volumen bajo (menos de ~60 novedades):** renderizar el loop completo y filtrar en el cliente con las chips. 25 líneas de JS, sin recarga, y funciona con la búsqueda por texto. Es lo que reproduce el diseño.
- **Volumen alto:** Search & Filter (free) — funciona por recarga de página, no AJAX; el AJAX es de la versión Pro. FacetWP es pago y queda fuera del alcance.

---

## 5. Lo que no se logra con configuración: CSS y snippets mínimos

Todo esto va en `style.css` del tema hijo (no en el Personalizador → CSS adicional, que no permite versionado). Es el total del CSS propio que el diseño necesita: unas 90 líneas.

### 5.1 Variables puente

```css
:root{
  --cipba-ease:cubic-bezier(.4,0,.2,1);
  --cipba-fast:.2s; --cipba-base:.3s;
  --cipba-border-card:#e8f5f1; --cipba-border-input:#dbe6e3;
}
```

### 5.2 Navbar sticky con sombra y blur al scrollear

```css
.ast-main-header-wrap{position:sticky;top:0;z-index:99;background:#fff;
  transition:box-shadow var(--cipba-base) var(--cipba-ease)}
.ast-main-header-wrap.is-scrolled{box-shadow:0 2px 20px rgba(0,0,0,.25);
  background:rgba(255,255,255,.92);backdrop-filter:blur(8px)}
```

```php
// functions.php del tema hijo (o snippet)
add_action('wp_footer',function(){ ?><script>
addEventListener('scroll',()=>document.querySelector('.ast-main-header-wrap')
  ?.classList.toggle('is-scrolled',scrollY>40),{passive:true});
</script><?php });
```

### 5.3 Ítem de menú destacado

```css
.is-destacado > a{background:#f1faf7;color:#008c75!important;font-weight:800;
  display:flex;align-items:center;gap:6px}
.is-destacado > a::before{content:"";width:6px;height:6px;border-radius:50%;
  background:#00a48a;flex-shrink:0}
```

### 5.4 Elevación de tarjetas en hover

```css
.cipba-card{transition:transform var(--cipba-base) var(--cipba-ease),
  box-shadow var(--cipba-base) var(--cipba-ease),border-color var(--cipba-fast);
  box-shadow:0 1px 4px rgba(13,50,52,.05)}
.cipba-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,112,94,.20)}
.cipba-card--purpura:hover{box-shadow:0 8px 24px rgba(48,46,92,.20)}
.cipba-card:hover .cipba-card__arrow{transform:translateX(3px)}
@media (prefers-reduced-motion:reduce){.cipba-card,.cipba-card__arrow{transition:none}
  .cipba-card:hover{transform:none}}
```

### 5.5 Color de badge por taxonomía

```css
.wp-block-post-terms a{color:#607a7c}
.cat-institucional .wp-block-post-terms a,.cat-distrito .wp-block-post-terms a{color:#14484a}
.cat-capacitacion .wp-block-post-terms a,.cat-matricula .wp-block-post-terms a{color:#00a48a}
.cat-normativa .wp-block-post-terms a,.cat-honorarios .wp-block-post-terms a{color:#8a3a2a}
.cat-comisiones .wp-block-post-terms a,.cat-eventos .wp-block-post-terms a{color:#484586}
```

WordPress ya agrega `category-slug` al artículo en el loop; alcanza con usar esas clases si se prefiere no inventar prefijo.

### 5.6 Recorte de extracto a dos líneas

```css
.cipba-excerpt{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
  overflow:hidden}
```

### 5.7 Avatar de iniciales (subcomisiones y autoridades)

```css
.cipba-avatar{width:38px;height:38px;border-radius:50%;display:grid;place-items:center;
  font-family:'Roboto Condensed',sans-serif;font-weight:700;font-size:13px;color:#1a5f5c;
  background:linear-gradient(135deg,#e8f5f1 0%,#c0e2ca 100%);flex-shrink:0}
.cipba-avatar--presidencia{background:linear-gradient(135deg,#c0e2ca 0%,#00a48a 100%);color:#fff}
```

### 5.8 Columna lateral fija en páginas de trámites

```css
@media (min-width:901px){.cipba-aside{position:sticky;top:88px;align-self:start}}
@media (max-width:900px){.cipba-tramite-grid{grid-template-columns:1fr!important}}
```

### 5.9 Enlaces de menú y PDFs en pestaña nueva

```php
add_filter('nav_menu_link_attributes',function($atts,$item){
  $classes = (array) $item->classes;
  $es_pdf  = preg_match('/\.pdf$/i', $atts['href'] ?? '');
  if (in_array('abre-nuevo',$classes,true) || $es_pdf){
    $atts['target']='_blank'; $atts['rel']='noopener';
  }
  return $atts;
},10,2);
```

### 5.10 Imagen destacada en 21:9 y anillo de foco

```css
.cipba-hero-img img{aspect-ratio:21/9;object-fit:cover;width:100%;display:block}
a:focus-visible,button:focus-visible,select:focus-visible,
input:focus-visible,textarea:focus-visible{outline:2px solid #00a48a;outline-offset:2px}
input,select,textarea{border:1px solid var(--cipba-border-input);border-radius:6px}
```

### 5.11 Patrón crosshatch sobre el gradiente del hero

```css
.cipba-hero{position:relative;isolation:isolate}
.cipba-hero::before{content:"";position:absolute;inset:0;z-index:-1;opacity:.06;
  background:url(assets/img/crosshatch.svg) repeat}
```

---

## 6. Breakpoints y estados

### Breakpoints del diseño

| Máx. | Qué cambia | Dónde se configura |
|---|---|---|
| 1248 px | El carrusel de eventos recupera padding lateral de 24 px | CSS (§5) |
| 1160 px | **Menú de escritorio → hamburguesa.** Es el breakpoint crítico: el menú tiene cinco entradas con desplegables anchos y se rompe antes de 1160 | Astra: *Personalizar → Header → Menú → Breakpoint* = 1160. Max Mega Menu: mismo valor en sus ajustes |
| 1080 px | Noticias 4 → 3 columnas | Query Loop (columnas por dispositivo) + CSS |
| 1000 px | Pie 4 → 2 columnas, con la columna de marca a ancho completo | CSS (§5) |
| 900 px | Grilla de trámites a 1 columna; redes y distrito a 1 columna; eventos a 2 por vista | Columns + CSS |
| 700 px | Hero, grillas de 3 y de 2 y pie a 1 columna. Se ocultan horario y enlace a Consejo Superior en la barra superior | Columns + CSS |
| 640 px | Descargas y "Otros trámites" a 1 columna | CSS |
| 520 px | Noticias y sedes a 1 columna | CSS |

> **⚠️ Los breakpoints por defecto no coinciden**
>
> Astra usa 921 px (tablet) y 544 px (móvil); Elementor, 1024 px y 767 px. El diseño necesita 1160, 900 y 700. Hay que alinear los tres sistemas o el menú saltará a hamburguesa en el punto equivocado. En Elementor, *Site Settings → Layout → Breakpoints* permite activar puntos personalizados (tablet 900, mobile 700) en la versión free.

### Estados

| Elemento | Hover | Foco | Activo / actual |
|---|---|---|---|
| Botón primario | Fondo `#008c75`, sube 2 px | Anillo 2 px `#00a48a`, offset 2 px | Sin desplazamiento, sombra reducida |
| Tarjeta | Sube 3 px, sombra teñida, borde al color de acento, flecha +3 px | Anillo en la tarjeta completa (es un enlace) | — |
| Enlace de menú | Fondo blanco 12 % sobre oscuro; `verde-100` sobre claro | Anillo verde | Página actual: `verde-700` en 700 + subrayado de 2 px |
| Ítem de desplegable | Fondo `#e8f5f1`, texto `verde-700` | Anillo verde | Destacado: fondo `#f1faf7` permanente + punto verde |
| Chip de filtro | Borde `verde-500` | Anillo verde | Activo: fondo `verde-700`, texto blanco |
| Fila de documento | Fondo `gris-50`, icono a `verde-600` | Anillo verde en toda la fila | — |
| Acordeón FAQ | Texto de la pregunta a `verde-700` | Anillo verde en el resumen | Abierto: signo rotado, fondo blanco |
| Input | Borde `#c0e2ca` | Borde `verde-500` + anillo | Error: borde `#c0392b` + mensaje 13 px |

Transiciones: 0.2 s para color y fondo, 0.3 s para transformaciones y sombras, curva `cubic-bezier(.4,0,.2,1)`. Nada de rebotes ni curvas elásticas. Respetar `prefers-reduced-motion` (§5.4).

---

## 7. Assets a exportar

| Asset | Formato | Tamaño | Notas |
|---|---|---|---|
| Isologo horizontal (navbar) | SVG + PNG fallback | Alto 44 px · PNG @2x 88 px | El SVG es obligatorio: el isologo tiene el sunburst con radios finos que el PNG degrada en retina |
| Isologo para pie | SVG | Alto 40 px | Variante monocroma blanca para fondo `verde-700` |
| Favicon / Site icon | PNG | 512 × 512 | WordPress genera el resto de tamaños |
| Imagen social por defecto | JPG | 1200 × 630 | Fallback Open Graph cuando la novedad no tiene imagen destacada |
| Patrón crosshatch | SVG | Tile 60 × 60 | Blanco a opacidad 0.05–0.06, solo sobre gradientes |
| Iconografía | SVG inline | 14 / 17 / 22 px, trazo 2 | Set Lucide (licencia ISC). Inline, no como fuente de iconos ni sprite |
| Icono WhatsApp | SVG | 18 px | Único icono que conserva su color de marca `#25d366` |
| Tipografías | WOFF2 | 7 archivos | Lato 300/400/700/900 + Roboto Condensed 400/700/900, subset latin + latin-ext, en `assets/fonts/` |
| Imágenes de novedad | WebP + JPG | 1680 × 720 (21:9) | Se registra el tamaño `cipba-nov-hero` con `add_image_size` |
| GeoJSON de partidos | GeoJSON | ≤ 250 KB simplificado | 23 partidos del Distrito VII, con `nombre` y `delegacion` por feature |
| PDFs de normativa | PDF | — | Ley 10.416, Código de Ética, Reglamento Interno, Ley 12.490. Incumbencias queda como enlace externo al Consejo Superior |

Tamaños de imagen a registrar: `cipba-card` 600 × 400 (recorte duro), `cipba-nov-hero` 1680 × 720 (recorte duro), `cipba-thumb` 300 × 200. Conviene desactivar los tamaños intermedios que WordPress genera y no se usan, para no inflar la biblioteca.

---

## 8. Plugins free necesarios

| Plugin | Para qué | Alternativa si no encaja |
|---|---|---|
| Spectra | Contenedor con enlace, iconos SVG, carrusel de entradas, bloque FAQ con esquema. Cubre los cuatro huecos de Gutenberg en este diseño | Kadence Blocks (free) tiene equivalentes y un Row Layout más potente; GenerateBlocks es más liviano pero no trae carrusel |
| Max Mega Menu | Desplegables de Trámites y Normativa con columnas y subtítulos de grupo | Astra free con submenú plano de un nivel: se pierde el agrupamiento visual pero no la navegación. Evaluarlo antes de sumar el plugin |
| ACF (free) | Campos de resoluciones, documentos, eventos y subcomisiones | Meta Box (free) o Pods. Los repeaters de ACF son Pro: para `anexos` y `referentes`, o se usa Meta Box (repeater incluido en free) o se modelan como CPT hijo. **Definir esto antes de modelar el contenido** |
| Sticky Menu on Scroll | Navbar fija (el sticky de Astra es Pro) | El CSS de §5.2, que además reproduce sombra y blur. Recomendado: preferir el CSS y no instalar el plugin |
| Fluent Forms | Formularios de contacto y consulta | WPForms Lite (más simple, menos campos) o Contact Form 7 + Flamingo (guarda envíos en base) |
| Rank Math (free) | SEO, Open Graph, breadcrumbs, sitemap | SEO Framework o Yoast free. Elegir uno solo: dos plugins de SEO duplican metadatos y esquema |
| Search & Filter | Filtros del listado de novedades — solo si el volumen lo pide | Filtrado en cliente con JS (§4.15), que es más fiel al diseño y no recarga la página |
| WP Super Cache o LiteSpeed Cache | Caché de página y optimización de entrega | Autoptimize + caché del hosting. No acumular dos plugins de caché |
| UpdraftPlus (free) | Respaldo programado antes de cada publicación | Respaldos del hosting, si el proveedor los ofrece con retención razonable |
| Code Snippets | Alojar los snippets PHP si no se quiere tocar `functions.php` | `functions.php` del tema hijo, que es la opción preferible: viaja con el tema y se versiona |

Total: entre 7 y 9 plugins activos según decisiones. Todo lo que no esté en esta lista debería justificarse: cada plugin es superficie de actualización y de falla.

---

## 9. Riesgos, límites de las versiones free y decisiones abiertas

### Riesgos

| Riesgo | Impacto | Mitigación |
|---|---|---|
| Doble sistema de tokens (theme.json vs. Elementor) | Alto | Definir un editor principal (§1). Si conviven, dejar por escrito que la paleta se cambia en dos lugares y auditarla en cada rediseño |
| CSS de Astra sobrescribiendo tokens | Medio | Los tres ajustes del Personalizador de §2. Verificar con DevTools que el color de cuerpo venga de `theme.json` y no de `ast-` |
| Mapa y consultor de partidos como código propio | Medio | Aislarlo en un shortcode con su propio JS y GeoJSON versionado; documentar cómo agregar o quitar un partido sin tocar el código |
| Actualizaciones de plugins rompiendo el carrusel o el mega menú | Medio | Entorno de staging, respaldo previo, y la salida (a) de §4.6 documentada como plan B inmediato |
| Peso de página por Elementor + Spectra + Astra juntos | Medio | No mezclarlos en la misma plantilla. Desactivar en Elementor los widgets no usados y la carga de Font Awesome y Eicons |
| Accesibilidad de los desplegables | Medio | El mega menú debe abrirse con teclado y cerrarse con Escape. Probar con navegación por tabulador antes de publicar; el anillo de foco de §5.10 no es opcional |
| PDFs y enlaces externos al Consejo Superior | Bajo | Las URLs del Consejo Superior cambian sin aviso. Instalar Broken Link Checker (free, con revisión mensual) o revisar a mano cada trimestre |

### Lo que la versión free no da

- **Astra free:** sin sticky header, sin transparent header, sin elemento Social en el Header Builder, sin control de tipografía por dispositivo en todos los elementos. Los tres primeros están resueltos en §5 y §4.1; el cuarto se suple con las tipografías fluidas de `theme.json`.
- **Elementor free:** sin Theme Builder (no puede generar plantillas de archivo ni single), sin Loop Grid ni Loop Carousel, sin formularios, sin popups, sin efectos de movimiento. Por eso las plantillas de novedades y normativa se hacen con bloques nativos, no con Elementor.
- **ACF free:** sin repeaters, sin flexible content, sin páginas de opciones. Es la limitación con más consecuencias en el modelado (§8).
- **Search & Filter free:** filtrado por recarga de página, sin AJAX.
- **Spectra free:** el carrusel de entradas no tiene todos los controles de paginación y transición; asumir el comportamiento por defecto.

### Decisiones que quedan a criterio de quien implemente

1. **Editor principal: bloques o Elementor.** Todo el handoff asume bloques + Spectra. Si se elige Elementor, §2 pasa a segundo plano y hay que replicar los tokens en Site Settings, además de resolver por otra vía las plantillas de archivo, que Elementor free no genera.
2. **Repeaters: Meta Box o CPT hijos.** Afecta el modelo de `resolucion` (anexos) y `subcomision` (referentes). Decidirlo antes de cargar contenido: migrar después es costoso.
3. **Trámites como CPT o como páginas.** Con CPT, el bloque "Otros trámites" se autogenera y la plantilla es única; con páginas, el armado es más simple pero hay mantenimiento manual en cada pie de página de trámite.
4. **Carrusel de eventos o grilla.** Con el volumen actual de eventos, la grilla de tres con enlace a la agenda es defendible y elimina una dependencia.
5. **Filtros de novedades: cliente o servidor.** Depende del volumen proyectado a dos años. El corte práctico está alrededor de 60 entradas.
6. **Alojamiento de los PDFs históricos de honorarios.** Hoy las resoluciones anteriores enlazan al Consejo Superior. Alojarlas localmente da control y permanencia, pero suma la responsabilidad de mantenerlas actualizadas y verificadas contra la fuente oficial.
7. **Tipografías locales o CDN.** El `theme.json` propuesto las sirve localmente. Si se prefiere Google Fonts por simplicidad, quitar los bloques `fontFace` y encolar la hoja de Google, aceptando la consulta a un tercero.
8. **Breadcrumbs: Astra o Rank Math.** Uno solo, por el marcado estructurado duplicado.

---

## Verificación antes de publicar

- Contraste de texto sobre fondos verdes (4.5:1 para cuerpo, 3:1 para titulares)
- Navegación completa por teclado incluyendo desplegables
- Los cuatro PDFs y los cuatro enlaces a sistemas externos abriendo en pestaña nueva con `rel="noopener"`
- Menú saltando a hamburguesa exactamente en 1160 px
- Datos de autoridades, sedes y subcomisiones cotejados contra la fuente oficial
- Resolución de honorarios vigente coincidiendo con el listado del Consejo Superior
