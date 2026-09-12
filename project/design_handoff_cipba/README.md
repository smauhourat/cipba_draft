# Handoff: CIPBA Distrito VII

## Overview
Rediseño del sitio institucional del **Colegio de Ingenieros de la Provincia de Buenos Aires – Distrito VII** (cipba.org).

Este handoff incluye tres páginas completas de alta fidelidad:

| Página | Archivo | Contenido |
|---|---|---|
| **Home** | `index.html` | Hero con slider, accesos rápidos, noticias, banner Visado, capacitación, reglamentaciones, institucional resumido, contacto |
| **Institucional** | `institucional.html` | Autoridades del Consejo Directivo 2024-2027, 23 partidos del distrito (con mapa visual) y 4 sedes con contactos por área |
| **Subcomisiones** | `subcomisiones.html` | Listado de las 10 subcomisiones técnicas del distrito con buscador en vivo y datos de contacto del referente de cada una |

Las tres páginas comparten navbar, footer, design tokens y comportamiento responsive.

## Sobre los archivos de diseño
Los archivos `index.html`, `institucional.html` y `subcomisiones.html` son **prototipos de alta fidelidad creados en HTML/React**. Son referencias visuales y de comportamiento — **no son código de producción para copiar directamente**. La tarea del desarrollador es recrear estos diseños en WordPress usando el theme y page builder **definidos para este proyecto: Astra (free) + Elementor (free)**, respetando los tokens de diseño, layouts y comportamientos documentados aquí.

## Fidelidad
**Alta fidelidad (hifi)**: colores, tipografías, espaciados e interacciones son finales. El desarrollador debe recrear la UI pixel a pixel usando las herramientas de WordPress.

---

## Stack WordPress — 100% gratuito

El proyecto se implementa **exclusivamente con plugins y themes free**. Esta restricción condiciona algunas decisiones de implementación que se detallan en la tabla de workarounds más abajo.

| Componente | Plugin / Herramienta | Función |
|---|---|---|
| Theme base | **Astra** (free) | Theme principal. Configurar las páginas con la plantilla **Elementor Canvas** (sin header/footer del theme) para que el header y footer custom dominen el layout. |
| Child theme | Astra child theme | **Imprescindible**. Aloja variables CSS, estilos custom, JS sticky-header y templates PHP de los listados. |
| Page builder | **Elementor** (free) | Construcción de páginas. |
| Header / Footer builder | **Elementor Header & Footer Builder** (by Brainstorm Force) | **Imprescindible** — Elementor free no permite editar header/footer; este plugin sí, y es compatible con Astra. |
| Mega menú | **Max Mega Menu** (free) | Dropdowns del navbar (Astra Mega Menu es Pro). |
| Sticky header | **myStickymenu** (free) + CSS custom | Header sticky con cambio de fondo al scrollear (Astra Sticky Header es Pro). |
| Slider del hero (home) | **Smart Slider 3** (free) | Slides del hero (widget Slides de Elementor es Pro). |
| Formulario de contacto | **Fluent Forms** (free) | Widget Form de Elementor es Pro. Fluent Forms tiene mejor estilado out-of-the-box que CF7. |
| Custom Post Types | **CPT UI** (free) | Crear los CPTs (Noticias, Resoluciones, Cursos; opcionalmente Subcomisiones / Autoridades / Sedes). |
| Campos personalizados | **ACF Free** + **Pods** (free, para repeaters) | ACF Free no incluye repeater; Pods sí. Si se prefiere todo con ACF, repetir grupos como campos numerados (`referente_1_*`, `referente_2_*`). |
| Buscador / Filtros | **Search & Filter** (free, by Designs & Code) | Filtros AJAX para listados (Resoluciones, Noticias). El buscador de Subcomisiones es JS frontend (más liviano). |
| Visor PDF | **PDF Embedder** (free) | Vista previa en Normativa. |
| Cache / Performance | **LiteSpeed Cache** (si el hosting es LiteSpeed) o **WP Super Cache** | Cache de páginas. |
| SEO | **Rank Math** (free) | SEO on-page. |
| Cookies | **Complianz** (free tier) | Aviso de cookies, política de privacidad. |

### Limitaciones del stack free y workarounds

| Funcionalidad del diseño | Limitación Astra/Elementor Free | Workaround |
|---|---|---|
| Header sticky con cambio de fondo al scrollear | Astra Sticky Header es Pro; Elementor Sticky widget también es Pro. | **myStickymenu** + CSS en el child theme que aplique los estilos `.scrolled` cuando se agrega la clase `is-sticky`. |
| Megamenú del navbar | Astra Mega Menu es Pro. | **Max Mega Menu**, configurar el ítem "Institucional" como mega menu con sub-items Autoridades / Partidos / Sedes / Subcomisiones. |
| Slider del hero con widget Slides | Es Pro. | **Smart Slider 3** free (más control y mejor performance que el widget Slides). |
| Form widget en Elementor | Es Pro. | **Fluent Forms** + widget Shortcode de Elementor para embeberlo. |
| Header/Footer hechos con Elementor | Theme Builder de Elementor es Pro. | **Elementor Header & Footer Builder** (free) — permite asignar plantillas hechas con Elementor a todo el sitio. |
| Custom posts loop (Noticias, Resoluciones, etc.) | Widget Posts de Elementor es Pro. | Para listados que cambian con frecuencia: **Search & Filter** + template PHP del child theme con `WP_Query`. Para listados casi estáticos (Autoridades, Sedes, Subcomisiones): construir con widget HTML de Elementor + CSS del child theme. |
| Hover effects custom (translateY, sombra) | El widget Box/Image de Elementor free tiene pocos efectos. | Markup en widget HTML + CSS del child theme (`:hover { transform: translateY(-3px); }`). |
| Tipografías globales por sección H1-H6 | Astra free maneja H1-H6 globales (sí está en free). | OK con Astra free, no requiere workaround. |
| Validación inline de form | El form de Elementor (Pro) trae validación. | Fluent Forms free incluye validación inline. |

### Child theme — obligatorio

Muchos comportamientos del diseño (CSS custom, sticky JS, hover effects, layouts complejos) no son resolubles desde la UI del builder en versiones free. **Crear un child theme de Astra** y trabajar ahí es indispensable:

```
astra-cipba-child/
├── style.css              ← variables CSS + estilos de componentes
├── functions.php          ← enqueue, registros de CPTs, hooks
├── assets/
│   ├── js/sticky-header.js
│   └── logo.png
└── template-parts/
    ├── subcomisiones-list.php
    ├── autoridades-list.php
    └── sedes-list.php
```

---

## Design Tokens

### Colores
```css
--blue-deep:   #0d2d5e;   /* Fondo navbar, secciones oscuras, títulos */
--blue-mid:    #1a4a8a;   /* Hover estados, gradientes */
--blue-light:  #2563b0;   /* Links, iconos, acentos secundarios */
--blue-pale:   #e8f1fc;   /* Fondos de cards, bordes suaves */
--green:       #2a9e2a;   /* Color acento principal (logo) */
--green-dark:  #1f7a1f;   /* Hover del verde */
--green-light: #d4f0d4;   /* Fondos tintados en verde */
--white:       #f8f9fc;   /* Fondo general de la página */
--gray-light:  #f0f2f7;   /* Secciones alternas (QuickAccess, Capacitación) */
--gray-mid:    #9aa5b8;   /* Textos secundarios, iconos */
--gray-text:   #3d4b63;   /* Texto de soporte, labels */
--text:        #1a2540;   /* Texto principal */
--footer-bg:   #060f1e;   /* Footer */
```

### Tipografía
```css
/* Fuentes: Google Fonts */
font-family: 'Lato', sans-serif;           /* Texto general */
font-family: 'Roboto Condensed', sans-serif; /* Títulos de sección, navbar brand */

/* Escala tipográfica */
--text-xs:   11px / 12px
--text-sm:   13px / 13.5px
--text-base: 14px / 15px
--text-md:   16px / 17px
--text-lg:   20px
--text-xl:   clamp(22px, 3vw, 30px)   /* Títulos de sección */
--text-hero: clamp(26px, 4vw, 44px)   /* H1 del hero */

/* Pesos */
300 — subtítulos light
400 — cuerpo de texto
700 — labels, links, CTAs secundarios
800 — títulos de cards
900 — títulos principales, stats
```

### Espaciado (secciones)
```css
--section-padding-y: 52px 24px  /* QuickAccess, VisadoBanner */
--section-padding-lg: 60px 24px /* Noticias, Capacitación, Institucional, Contacto */
--max-width: 1200px              /* Contenedor máximo centrado */
--card-radius: 10px
--btn-radius: 6px
--gap-grid: 16px / 20px
```

### Sombras
```css
--shadow-card:  0 2px 8px rgba(0,0,0,0.04);
--shadow-hover: 0 8px 24px rgba(0,0,0,0.10);
--shadow-nav:   0 2px 20px rgba(0,0,0,0.25);
```

### Border radius
```css
--radius-btn:   6px
--radius-card:  10px
--radius-badge: 20px   /* pills / tags de categoría */
--radius-icon:  8px    /* contenedores de iconos */
```

---

## Secciones de la Home (`index.html`)

### 1. Top Bar
- Fondo: `#0d2d5e`
- Altura: ~32px, texto 13px
- Contenido izquierda: teléfono, email, horario (con íconos inline SVG)
- Contenido derecha: link WhatsApp + link "Consejo Superior" con ícono external link
- En mobile: se oculta o colapsa

### 2. Navbar (sticky)
- Altura: 64px
- Fondo inicial: `#1a4a8a` → al hacer scroll: `rgba(13,45,94,0.98)` con `backdrop-filter: blur(8px)`
- Border bottom: `3px solid #2a9e2a`
- Logo: imagen PNG (`assets/logo-cipba-vii.png`) con `filter: brightness(0) invert(1)` para versión blanca. Altura: 46px.
- Links: Roboto Condensed no, Lato 13.5px weight 600, color `rgba(255,255,255,0.88)`
- Dropdowns: fondo blanco, border top `3px solid #2a9e2a`, sombra suave
- CTA derecha: botón "Visado Online" con fondo `#2a9e2a`, border-radius 5px
- Mobile: hamburger icon, menú desplegable vertical

**Links del menú:**
- Inicio
- Matrícula (dropdown: Nueva Matrícula, Reinscripción, Baja Temporal, Certificados)
- Visado (dropdown: Visado Online, Tasas y Aranceles, Honorarios Mínimos, Visadores)
- Capacitación
- Normativa
- Institucional (dropdown: Autoridades, Historia, Delegaciones)
- Noticias
- Contacto

### 3. Hero (slider de 3 slides)
- Altura mínima: 520px
- Fondo: gradiente azul (varía por slide)
- Patrón decorativo: SVG crosshatch blanco con opacity 0.05
- Rotación automática cada 6 segundos
- Indicadores de slide: pills con fondo `#2a9e2a` (activo) / `rgba(255,255,255,0.35)`
- Tag badge: fondo `rgba(42,158,42,0.2)`, borde `rgba(42,158,42,0.4)`, texto `#d4f0d4`
- H1: 44px max, weight 900, blanco
- Párrafo: 17px, `rgba(255,255,255,0.78)`
- CTA primario: fondo `#2a9e2a`, sombra verde, hover oscurece + sube 2px
- CTA secundario: borde `rgba(255,255,255,0.4)`, hover fondo `rgba(255,255,255,0.1)`

**Strip de estadísticas debajo del hero:**
- 4 columnas: +8.500 Matriculados | 24 Partidos | 3 Sedes | +30 Años
- Fondo blanco, número 26px weight 900 `#0d2d5e`, label 12px gris

### 4. Acceso Rápido / Trámites
- Fondo: `#f0f2f7`
- Grid: `repeat(auto-fill, minmax(180px, 1fr))`
- Cards blancas con borde `#e8f1fc`, border-radius 10px
- Hover: translateY(-4px), sombra, borde coloreado según ítem
- 6 ítems: Matrícula, Visado Online, Honorarios, Seguro Profesional, Capacitación, Beneficios
- Cada ítem tiene: ícono 44px container, título, descripción, link "Acceder →"

### 5. Noticias
- Fondo: blanco
- Grid: `repeat(auto-fill, minmax(280px, 1fr))`
- Border top de cards: 3px con color de categoría
- Categorías y colores:
  - Institucional: `#0d2d5e`
  - Capacitación: `#1a8a4a`
  - Normativa: `#8a1a1a`
  - Visado: `#2563b0`
- Card destacada: banner superior `#0d2d5e` con "📌 Destacado"

### 6. Banner Visado Online
- Fondo: gradiente `linear-gradient(135deg, #0d2d5e, #1a4a8a)`
- Layout: texto izquierda + checklist derecha + botón CTA
- 4 features con checkmark circular verde `#2a9e2a`
- Botón: `#2a9e2a` con sombra verde

### 7. Capacitación
- Fondo: `#f0f2f7`
- Lista vertical de cursos (no grid)
- Cada curso: layout flex con ícono, título, fecha, modalidad badge, vacantes, botón
- Badge modalidad: Verde claro para Virtual, Azul claro para Presencial
- Vacantes < 10: color naranja-ocre; ≥ 10: verde

### 8. Reglamentaciones y Resoluciones (Normativa)
- Sección `id="normativa"`, fondo blanco, padding `60px 24px`
- Header con eyebrow verde "Marco normativo", título principal y descripción
- **Toolbar superior** con:
  - Campo de búsqueda con icono lupa (busca por título o número de documento)
  - Chips de filtro por categoría (Todas + categorías dinámicas)
  - Chip activo: fondo `#0d2d5e` blanco; inactivo: fondo blanco texto gris con borde `#e0e6f0`
- **Contador de resultados** debajo de la toolbar (12.5px gris)
- **Lista de documentos** dentro de un contenedor con borde redondeado `#e8f1fc`:
  - Cada fila: grid `52px 1fr auto auto auto` con gap 16px y padding `16px 20px`
  - Separador entre filas: borde inferior `1px solid #e8f1fc`
  - **Ícono PDF**: tarjeta 44×52px con fondo tintado en color de categoría (12% opacity), icono file-text + label "PDF" en color de categoría
  - **Título y meta**: badge de categoría coloreado, número ("Resolución CS 187/2026") en Roboto Condensed, título 14.5px weight 700
  - **Fecha**: icono calendar + fecha, 13px gris (oculto en mobile)
  - **Tamaño**: "12 pág · 342 KB" en Roboto Condensed 12px (oculto en mobile)
  - **Acciones**: botón "Ver" (outline, color de categoría) + botón "PDF" (fondo `#0d2d5e`, descarga)
- **Categorías y colores**:
  - Honorarios: `#8a1a1a`
  - Visado: `#2563b0`
  - Institucional: `#0d2d5e`
  - Matrícula: `#1a8a4a`
  - Capacitación: `#7a1a8a`
- **Tipos de documento**: Resolución, Reglamento, Disposición
- **Vista previa expandible** al hacer click en "Ver":
  - Grid 2 columnas: `1fr 280px` (en mobile pasa a 1 columna)
  - Izquierda: PDF simulado (fondo blanco, borde `#d0d8e8`, font Georgia serif, encabezado con border bottom color categoría, contenido tipo "VISTO / CONSIDERANDO")
  - Derecha: panel de metadatos con Tipo, Número, Categoría, Fecha, Páginas, Tamaño, Estado, botón "Descargar PDF completo"
- **Estado vacío**: si no hay resultados, mensaje centrado "No se encontraron documentos con esos criterios"

**Implementación WordPress (stack free)**:
- **CPT UI** → crear CPT "Resoluciones" (slug `resolucion`) y taxonomía "Categoría normativa".
- **ACF Free** → campos: número (text), tipo (select: Resolución / Reglamento / Disposición), fecha (date picker), archivo PDF (file), páginas (number), tamaño (text auto-calc), estado (select).
- **Categorías con color**: agregar campo ACF `color` al término de la taxonomía (color picker free de ACF). Leer el color en el template PHP del child theme.
- **Filtros/búsqueda AJAX**: **Search & Filter** (free) con un template propio en el child theme que renderice cada item con el HTML del prototipo.
- **Vista previa PDF**: **PDF Embedder** (free). El prototipo simula el PDF; en producción se carga el archivo real. Si se quiere thumbnail rápido, generar imagen de la primera página al subir el PDF con `wp_generate_attachment_metadata` + ImageMagick (depende del hosting).

### 9. Institucional
- Fondo: blanco
- Grid 2 columnas: texto/links izquierda, cards de sedes derecha
- 3 cards de sede: Principal (fondo `#0d2d5e` oscuro), Olivos y Haedo (fondo blanco con borde)
- Sede principal incluye: dirección, teléfono, email, horario

### 10. Contacto
- Fondo: `#f0f2f7`
- Grid 2 columnas: info de contacto | formulario
- Info: cards blancas con datos por área (Administrativa, Técnica)
- Botón WhatsApp: fondo `#25d366`, hover `#1da851`
- Formulario: campos con border `#e0e6f0`, focus `#2563b0`, error `#c0392b`
- Estado enviado: icono check verde + mensaje confirmación

### 11. Footer (compartido entre páginas)
- Fondo: `#060f1e`
- Grid 4 columnas: brand | Trámites | Institucional | Servicios
- Logo: PNG invertido a blanco (`filter: brightness(0) invert(1)`), height 52px, opacity 0.9
- Links hover: color `#2a9e2a`
- Copyright bar con borde top `rgba(255,255,255,0.1)`

---

## Página Institucional (`institucional.html`)

Página interna accesible desde el navbar (link "Institucional"). Comparte TopBar, Navbar y Footer con la home. El item "Institucional" del navbar aparece marcado como activo con fondo verde tintado `rgba(42,158,42,0.25)` y border bottom `2px solid #2a9e2a`.

### I.1. Page Hero (cabecera de página interna)
- Fondo: gradiente `linear-gradient(135deg, #0d2d5e 0%, #1a4a8a 60%, #2563b0 100%)` con patrón SVG decorativo opacity 0.06
- Padding: `48px 24px 56px`
- **Breadcrumb** arriba (13px, `rgba(255,255,255,0.65)`):
  - Ícono home + "Inicio" (link) › "Institucional" (activo, blanco bold)
- Badge eyebrow: "Quiénes somos" con punto verde, fondo `rgba(42,158,42,0.22)`
- H1: "Distrito VII" en Roboto Condensed, clamp 28–48px, weight 900, blanco, letter-spacing -0.5
- Párrafo descriptivo (17px, `rgba(255,255,255,0.82)`, max-width 720px)
- **Quick nav chips** (anclas internas): Autoridades, Partidos comprendidos, Sedes y delegaciones
  - Fondo: `rgba(255,255,255,0.1)`, border `rgba(255,255,255,0.25)`
  - Hover: fondo verde `rgba(42,158,42,0.4)`, border `#2a9e2a`

### I.2. Autoridades (Consejo Directivo)
- Sección `id="autoridades"`, fondo blanco, padding `64px 24px`
- Header centrado: eyebrow verde con icono users + título "Autoridades" + pill azul "Período 2024 – 2027"
- **Grid**: `repeat(auto-fill, minmax(280px, 1fr))`, gap 18px
- **Card del Presidente** (destacada):
  - Ocupa `grid-column: span 2`
  - Fondo gradiente azul `linear-gradient(135deg, #0d2d5e 0%, #1a4a8a 100%)`, texto blanco
  - Badge "Presidencia" en esquina superior derecha con icono star y fondo verde `#2a9e2a`
  - Avatar circular grande 72×72px con gradiente verde y borde blanco semitransparente, conteniendo iniciales en Roboto Condensed 22px weight 900
  - Cargo (label verde claro), nombre (19px weight 800 blanco), título profesional (itálica)
- **Cards de Secretario, Tesorero, Vocales** (8 cards):
  - Fondo blanco, border `#e8f1fc`, border-radius 12px
  - Avatar circular 56×56px con gradiente azul claro `linear-gradient(135deg, #e8f1fc, #c8d8f0)` y iniciales azules en Roboto Condensed 18px
  - Hover: `translateY(-3px)`, sombra suave, border `#2a9e2a`
  - Layout: avatar a la izquierda + bloque de texto a la derecha (cargo, nombre, título profesional)

**Datos a cargar (Consejo Directivo 2024 – 2027):**
| Cargo | Nombre | Título profesional |
|---|---|---|
| Presidente | Daniel Héctor PALACIOS | Ing. Electricista y Laboral |
| Secretario | Fabián José Miguel PORCILE | Ing. Civil |
| Tesorero | Marina Helena VACA | Inga. Civil |
| Vocal Titular 1° | Gabriel Esteban BUSNARDO | Ing. en Alimentos |
| Vocal Titular 2° | Mariano Alejandro PRALONG | Ing. Civil |
| Vocal Titular 3° | Fabián Roberto MONTERO | Ing. Civil |
| Vocal Suplente 1° | Juan José URANGA | Ing. Mecánico |
| Vocal Suplente 2° | Perla Beatriz ARMAGNAC | Inga. Civil |
| Vocal Suplente 3° | María Claudia FILIPUZZI | Inga. en Ecología |

**Implementación WordPress (stack free):**
- Como los datos cambian cada 3 años, dos opciones:
  - **Opción A (recomendada)**: armar el listado con un widget HTML de Elementor + CSS del child theme. Editar el HTML cuando cambien las autoridades.
  - **Opción B**: **CPT UI** + **ACF Free** con campos cargo (select), nombre, título profesional, orden (number), destacado (boolean). Template `template-parts/autoridades-list.php` en el child theme con `WP_Query` ordenado por `orden`, primer item con clase `.destacado` para el layout grande.
- Si en el futuro se quieren cargar fotos reales, sustituir el avatar de iniciales por la imagen.

### I.3. Partidos comprendidos
- Sección `id="partidos"`, fondo `#f0f2f7`, padding `64px 24px`
- Header centrado: eyebrow verde con icono location + "Jurisdicción" + título "Partidos comprendidos" + descripción con counts destacados
- **Layout grid**: `1fr 1.4fr` (mapa | listado). En mobile (≤ 900px) pasa a 1 columna
- **Mapa visual** (panel izquierdo):
  - Card blanca sticky (`top: 88px`), border-radius 12px, sombra suave
  - SVG `viewBox="0 0 320 340"` con:
    - Polígono outline del distrito en gradiente azul claro `#e8f1fc → #d4e6f7`, stroke verde `#2a9e2a` 2.5px
    - Pinpoints (círculos azules `#0d2d5e` radio 3.5px) en posiciones aproximadas de los partidos
    - San Justo destacado: círculo verde grande `#2a9e2a` radio 6px con halo, callout-line + rectángulo verde con texto "★ SAN JUSTO" en Roboto Condensed
    - Texto inferior gris itálica: "Distribución aproximada · No a escala"
  - Card inferior con badge "Sede principal: San Justo · La Matanza"
- **Listado de partidos** (panel derecho):
  - Card blanca con campo de búsqueda en vivo (filtra por nombre)
  - Grid: `repeat(auto-fill, minmax(160px, 1fr))`, gap 8px
  - Cada chip: fondo `#f8f9fc`, punto verde `#2a9e2a`, nombre del partido 13.5px
  - Hover: fondo `#e8f5ee`, border `#2a9e2a`, texto `#0d2d5e`
  - Contador dinámico de resultados

**Lista de 23 partidos:**
Escobar, Exaltación de la Cruz, General Las Heras, General Rodríguez, General San Martín, Hurlingham, Ituzaingó, José C. Paz, La Matanza, Luján, Malvinas Argentinas, Marcos Paz, Mercedes, Merlo, Moreno, Morón, Pilar, San Isidro, San Fernando, San Miguel, Tigre, Tres de Febrero, Vicente López.

**Implementación WordPress (stack free):**
- Lista estática en HTML dentro de un widget HTML de Elementor (los partidos cambian solo si se modifica la ley provincial). No requiere CPT.
- El mapa SVG es decorativo — pegar el SVG del prototipo en el widget HTML.
- Alternativa con mapa real: **WP Google Maps** (free) con marcadores en cada partido. La cuenta gratuita de Google Maps requiere API key.
- Búsqueda en vivo: JS inline en el child theme con `addEventListener('input')` sobre los chips.

### I.4. Sedes y delegaciones
- Sección `id="sedes"`, fondo blanco, padding `64px 24px`
- Header centrado: eyebrow + título "Sedes y delegaciones" + descripción
- **Grid**: `repeat(auto-fill, minmax(280px, 1fr))`, gap 18px
- **Card de Sede Central San Justo** (destacada):
  - Ocupa `grid-column: 1 / -1` (todo el ancho)
  - Fondo gradiente azul `linear-gradient(135deg, #0d2d5e 0%, #1a4a8a 100%)`, padding 32px
  - Sombra: `0 12px 40px rgba(13,45,94,0.18)`
  - Halo decorativo verde en esquina superior derecha (`radial-gradient` con `rgba(42,158,42,0.25)`)
  - Header: tag eyebrow + h3 "Sede San Justo" (Roboto Condensed 28px weight 900) + badge verde "★ Casa Central"
  - **Layout 2 columnas**: info izquierda | contactos por área derecha
  - Info: filas con icono cuadrado 28×28px fondo `rgba(255,255,255,0.1)`, label uppercase pequeño, valor 14px blanco. Separadores `1px solid rgba(255,255,255,0.1)`
  - Contactos: lista con scroll (max-height 420px), cada item card `rgba(255,255,255,0.06)` con nombre + badge verde de rol + tel + email
- **Cards de delegaciones** (3, no destacadas):
  - Fondo blanco, border `#e8f1fc`, padding `24px 26px`
  - Tag eyebrow verde, h3 19px weight 900
  - Filas info con icono azul `#2563b0` y separador dashed

**Datos de las sedes:**

**Sede San Justo (Central)**
- Dirección: Almafuerte N° 2868, San Justo (1754) – La Matanza – Bs As
- Teléfono: (011) 3535-0751
- Email: info@cipba.org
- Horario: Lunes a viernes de 9:00 a 16:00 hs
- Contactos directos por área (Administración, Secretaría, Tesorería, Técnica, Gerencia Técnica) — 7 referentes con teléfono celular y email

**Parque Industrial DECA (Haedo)**
- Dirección: Valentín Gómez N° 577 1° of. 1, Haedo
- Teléfono: (011) 5433-5344
- Horario: Lunes a viernes de 9:00 a 15:00 hs

**Vicente López (Olivos)**
- Dirección: Ricardo Gutiérrez N° 1834 (CP 1636), Olivos
- Teléfono: (011) 7398-2119
- Horario: Sólo correspondencia

**General Rodríguez**
- Dirección: Av. España 493 – Gral. Rodríguez
- Teléfono: (011) 2563-1616
- Visador: Ing. Civil Darío Kubar (Mat. 51785)
- Email: cipba7gr@gmail.com
- Horario: Lunes a viernes de 9:00 a 13:00 hs. Visador en el mismo horario.

**Implementación WordPress (stack free):**
- Datos estables → la opción más simple es widget HTML de Elementor + CSS del child theme.
- Si se prefiere editor amigable: **CPT UI** "Sedes" + **Pods** (free, sí tiene repeaters) o **Meta Box** (free) para los contactos directos por área. ACF Free no incluye repeater, así que evitar ACF para este caso o usar campos numerados (`contacto_1_nombre`, `contacto_1_rol`, etc., hasta 7 contactos).
- Plantilla `template-parts/sedes-list.php` en el child theme — condicional `if ($destacada)` cambia layout y colores.

### I.5. CTA Final (banner verde)
- Fondo: gradiente verde `linear-gradient(135deg, #2a9e2a 0%, #1f7a1f 100%)`
- Padding: `48px 24px`, contenido centrado max-width 900px
- Título blanco Roboto Condensed clamp 20–28px + párrafo gris claro
- Dos botones:
  - Primario: fondo blanco, texto `#1f7a1f`, "Formulario de contacto" → ancla a home `index.html#contacto`
  - Secundario outline: fondo `rgba(255,255,255,0.15)`, icono WhatsApp + texto, link a `wa.me/541138293041`

---

## Página Subcomisiones (`subcomisiones.html`)

Página interna accesible desde el dropdown "Institucional" del navbar (ítem "Subcomisiones"). Comparte TopBar, Navbar y Footer con la home e institucional. En el navbar el ítem "Institucional" se muestra activo y, dentro del dropdown, "Subcomisiones" aparece marcado como current (fondo `#e8f5ee`, border-left `3px solid #2a9e2a`, texto `#0d2d5e` weight 800).

### S.1. Page Hero
- Mismo gradiente azul y patrón decorativo del hero de Institucional, padding `48px 24px 56px`
- **Breadcrumb** de 3 niveles: `Inicio › Institucional › Subcomisiones`
- Badge eyebrow: "Participación profesional" con punto verde
- H1: "Subcomisiones" en Roboto Condensed clamp 28–48px weight 900
- Bajada (17px, max-width 760px): "Espacios técnicos de trabajo organizados por especialidad. Funcionan como ámbito de consulta, formación entre pares y elaboración de criterios para la práctica profesional en el distrito. Si querés sumarte, contactá directamente al referente."
- **Chip contador**: ícono `layers` + número en Roboto Condensed weight 700 verde claro + texto "subcomisiones activas"
- **CTA verde** "Ver listado" con ancla a `#listado`

### S.2. Listado (`id="listado"`)
- Fondo: `#f8f9fc`, padding `56px 24px 72px`
- **Toolbar** flex con justify-between:
  - Izquierda: eyebrow verde "Listado completo" + h2 "{N} subcomisiones · {M} referentes" (Roboto Condensed weight 900 `#0d2d5e`) + bajada gris
  - Derecha: **buscador** con ícono lupa, placeholder "Buscar por especialidad o apellido…", border `1.5px solid #e0e6f0`, focus border `#2563b0`, min-width 280px. Botón × para limpiar cuando hay texto.
- **Búsqueda en vivo** (JS frontend): filtra las cards por nombre de subcomisión, tag o nombre/email de cualquiera de sus referentes. Muestra contador de resultados.
- **Grid de cards**: `repeat(auto-fill, minmax(340px, 1fr))`, gap 18px. En ≤ 640px pasa a 1 columna.
- **Estado vacío**: card con border dashed `#d0d8e8`, padding `48px 24px`, mensaje centrado.

### S.3. Card de subcomisión
- Fondo blanco, border `1px solid #e8f1fc`, border-radius 12px, sombra suave `0 2px 8px rgba(0,0,0,0.04)`
- Hover: `translateY(-3px)`, sombra `0 12px 28px rgba(13,45,94,0.10)`, border `#2a9e2a`
- **Header** (padding `18px 22px 16px`, border-bottom `1px solid #f0f2f7`, background gradiente sutil `linear-gradient(135deg, #f8fbff, #ffffff)`):
  - **Tag** cuadrado 44×44 con border-radius 8px, fondo `#e8f1fc`, texto `#1a4a8a` en Roboto Condensed weight 700. Abreviatura corta de la especialidad (HyS, Mec, PAJ, Elec, Agr, Alim, Civ, Amb, OPDS, JP). Si la abreviatura tiene > 3 chars, font-size 11px; si tiene ≤ 3, 13px.
  - Bloque texto: eyebrow verde "Subcomisión" + nombre completo (16px weight 800 `#0d2d5e` Roboto Condensed line-height 1.25 text-wrap balance).
- **Body** (padding `6px 22px 20px`):
  - Lista de 1–2 referentes. Separador entre referentes: `1px dashed #e8f1fc`.
  - **Avatar circular** 38×38 con gradiente azul claro `linear-gradient(135deg, #e8f1fc, #c8d8f0)`, iniciales del referente en Roboto Condensed weight 700 (primer letra del primer nombre + primer letra del apellido).
  - **Nombre**: "Ing. {NOMBRE}" 13.5px weight 800 `#1a2540`
  - **Matrícula**: "MAT. {número}" Roboto Condensed 11.5px gris `#9aa5b8`
  - **Teléfono**: link `tel:` con ícono phone, color `#3d4b63`, hover `#2563b0`
  - **Email**: link `mailto:` con ícono mail, color `#2563b0`, hover `#0d2d5e`
  - `word-break: break-all` en teléfono y email para mobile.

### Datos de las subcomisiones

| Subcomisión | Tag | Referentes (Nombre · Mat. · Tel · Email) |
|---|---|---|
| Higiene y Seguridad en el trabajo | HyS | Maria Claudia FILIPUZZI · 53.929 · 15-5181-9336 · cfilipuzzi@hotmail.com |
| Ingeniería Mecánica | Mec | Javier Victor Manuel TORRES · 55.905 · 15-5024-8414 · jt.ingenieriayservicios@gmail.com |
| Peritos Auxiliares de la Justicia | PAJ | Juan José URANGA · 55.133 · 15-6711-8199 · juanjoseuranga@gmail.com |
| Ingeniería Eléctrica | Elec | Edgardo LEIBER · 54.032 · 15-4498-4624 · edgardoleiber@hotmail.com<br>Juan Pablo MAZZA · 49.294 · 15-6123-7995 · epaim@epaim.com.ar |
| Agrimensura | Agr | Jenaro Antonio STINGA · 31.544 · 15-3104-3010 · jastinga@gmail.com<br>Ismael Alberto CONTE · 39.198 · 02324-15-58-2633 · alberto@estudiodc.net |
| Ingeniería en Alimentos | Alim | Gabriel Esteban BUSNARDO · 55.336 · 15-4938-9842 · gbusnardo1970@gmail.com<br>Yanina Valeria CENTURION · 56.004 · 15-5379-3030 · yvcenturion@gmail.com |
| Ingeniería Civil | Civ | Perla Beatriz ARMAGNAC · 53.929 · 15-3428-4268 · parmagnac@gmaingenieria.com.ar |
| Ingeniería Ambiental | Amb | Carlos Alberto LOSI · 48.892 · 15-5644-0200 · lca1srl.cal@gmail.com<br>Roberto Emilio BASSO · 32.285 · 15-6208-0586 · roberto@ingenieriabasso.com.ar |
| Profesionales ante Ministerio de Ambiente (OPDS) | OPDS | Carlos Alberto LOSI · 48.892 · 15-5644-0200 · lca1srl.cal@gmail.com<br>Jose Luis GUEVARA · 34.956 · 15-4532-6499 · ingguevara@gmail.com |
| Jóvenes Profesionales de CAAITBA | JP | Dario Miguel KUBAR · 51.785 · 15-5644-0200 · dariokubar@hotmail.com.ar |

### S.4. CTA Final (banner verde)
- Mismo banner verde compartido con otras páginas internas
- Título: "¿Querés sumarte a una subcomisión?"
- Bajada: "Las subcomisiones están abiertas a matriculados/as activos/as del distrito. Escribile al referente de tu especialidad o contactanos por los canales generales."
- Botones: "Formulario de contacto" (blanco) → `index.html#contacto` + "WhatsApp" outline → `wa.me/541138293041`

**Implementación WordPress (stack free):**
- **Opción recomendada — widget HTML de Elementor**: los datos cambian 1–2 veces al año; pegar el markup de las cards en un widget HTML y estilarlo desde el child theme. El buscador se hace con un snippet JS en el child theme que filtra divs `[data-subcomision]` por `dataset.search` (string que concatena nombre + tag + referentes). Cero dependencias extra.
- **Opción alternativa — CPT**: CPT UI → "Subcomisiones" + Pods (free, con repeaters) o Meta Box (free) para los referentes. ACF Free no tiene repeater — si se quiere ACF puro, hacer campos `referente_1_*` hasta `referente_3_*`. Template `template-parts/subcomisiones-list.php` con `WP_Query` ordenado por `orden`.
- **Recomendado**: opción A. Es más simple, más liviana y suficiente para la cadencia de cambios de la lista.

---

## Notas de navegación entre páginas

- El `index.html` enlaza a `institucional.html` y `subcomisiones.html` desde el dropdown "Institucional" del navbar
- El `institucional.html` enlaza a `subcomisiones.html` desde el dropdown "Institucional" y desde el footer (columna Institucional)
- El `subcomisiones.html` enlaza de vuelta a `index.html` desde el logo, navbar, breadcrumb y CTA final; y a `institucional.html` desde el breadcrumb y el dropdown
- En `institucional.html` y `subcomisiones.html` el ítem "Institucional" del navbar está marcado como activo (fondo verde tintado, border bottom verde)
- Dentro del dropdown de "Institucional", la entrada de la página actual aparece marcada como current (fondo `#e8f5ee`, border-left verde, texto `#0d2d5e` weight 800)

---

## Interacciones y comportamiento

| Elemento | Comportamiento |
|---|---|
| Navbar | Sticky. Al scroll > 40px: oscurece fondo + sombra |
| Hero slider | Auto-rotación cada 6s, indicadores clicables |
| Cards (trámites, noticias, cursos) | `translateY(-4px)` + sombra en hover |
| Botones primarios | Oscurecimiento de color + `translateY(-2px)` en hover |
| Dropdowns navbar | Aparecen en `onMouseEnter`, desaparecen en `onMouseLeave` |
| Formulario | Validación inline (nombre, email requeridos), estado "enviado" con animación |
| WhatsApp | Link a `https://wa.me/541138293041` en nueva pestaña |

---

## Responsividad
- Breakpoint principal: **768px** (mobile) — navbar colapsa a hamburger
- Breakpoint secundario: **900px** — grilla institucional y contacto pasan a 1 columna
- `clamp()` en todos los títulos principales para fluido entre 320px y 1440px

---

## Assets

| Archivo | Uso | Notas |
|---|---|---|
| `assets/logo-cipba-vii.png` | Navbar + Footer | PNG con transparencia. En fondos oscuros: `filter: brightness(0) invert(1)` para versión blanca. En fondos claros: usar tal cual. |

### Íconos
El prototipo usa **íconos SVG inline dibujados a mano** (estilo Feather Icons). En WordPress se puede usar el set **Feather Icons** o **Heroicons** disponibles en Elementor. Los íconos usados son:

`phone`, `mail`, `location`, `clock`, `chevron-right`, `chevron-down`, `external-link`, `award`, `file-text`, `users`, `dollar-sign`, `shield`, `book`, `calendar`, `search`, `eye`, `download`, `whatsapp` (custom SVG)

---

## Archivos incluidos

```
design_handoff_cipba/
├── README.md            ← este documento
├── index.html           ← prototipo Home (alta fidelidad)
├── institucional.html   ← prototipo Institucional (alta fidelidad)
├── subcomisiones.html   ← prototipo Subcomisiones (alta fidelidad)
└── assets/
    └── logo-cipba-vii.png
```

---

## Notas para el desarrollador WordPress (stack 100% free)

### 1. Setup inicial
1. Instalar **Astra** (free) desde el repositorio de WP.
2. Crear y activar un **child theme de Astra** — imprescindible para CSS custom, JS y templates PHP de los listados.
3. Instalar **Elementor** (free).
4. Instalar los plugins free imprescindibles:
   - **Elementor Header & Footer Builder** (by Brainstorm Force) — para construir header y footer con Elementor sin necesidad de Pro.
   - **Max Mega Menu** — dropdowns multi-columna del navbar.
   - **myStickymenu** — header sticky con cambio de estilo al scrollear.
   - **Smart Slider 3** — slider del hero de la home.
   - **Fluent Forms** — formulario de contacto.
   - **CPT UI** + **Pods** (o **Meta Box**) — CPTs y campos custom con repeaters.
   - **Search & Filter** — filtros AJAX para Resoluciones / Noticias.
   - **PDF Embedder** — vista previa de PDFs en Normativa.
   - **Rank Math** — SEO.
   - **LiteSpeed Cache** o **WP Super Cache** — performance.
   - **Complianz** (free tier) — cookies y políticas.

### 2. Configuración Astra Free
- **Apariencia → Personalizar → Tipografía**: cargar **Lato** (cuerpo) y **Roboto Condensed** (títulos H1–H6).
- **Apariencia → Personalizar → Colores**: configurar los tokens (`#0d2d5e`, `#1a4a8a`, `#2563b0`, `#2a9e2a`, etc.).
- **Plantilla de página**: para todas las páginas del prototipo, usar **Elementor Canvas** (sin header/footer del theme) — el header/footer custom de Elementor Header & Footer Builder se encarga.

### 3. Configuración Elementor Free
- **Site Settings → Global Colors**: cargar los design tokens.
- **Site Settings → Global Fonts**: cargar Lato (Default) y Roboto Condensed (Heading).
- Para componentes complejos (cards con hover translateY, grids auto-fill, gradientes): usar **widget HTML** + CSS del child theme.

### 4. Estructura del child theme
```
astra-cipba-child/
├── style.css                      ← variables CSS (design tokens) + componentes
├── functions.php                  ← enqueue, registros de CPTs, hooks
├── assets/
│   ├── js/
│   │   ├── sticky-header.js       ← toggle clase .scrolled al pasar 40px
│   │   ├── subcomisiones-search.js
│   │   └── partidos-search.js
│   ├── css/
│   │   ├── components.css
│   │   └── pages.css
│   └── logo.png
└── template-parts/
    ├── subcomisiones-list.php     ← si se opta por CPT
    ├── autoridades-list.php       ← si se opta por CPT
    ├── sedes-list.php             ← si se opta por CPT
    └── resoluciones-list.php      ← con Search & Filter
```

### 5. Header + Footer
- Crear las plantillas con **Elementor Header & Footer Builder**:
  - **TopBar** (display location: Before Header): barra azul oscura con teléfono, email, horario.
  - **Header**: logo, menú principal (Max Mega Menu en el menú) y CTA verde "Visado Online".
  - **Footer**: 4 columnas + copyright.
- **Sticky con cambio de fondo**: configurar **myStickymenu** apuntando al selector de la plantilla del header. Agregar JS propio en el child theme que también agregue clase `.scrolled` al pasar 40px de scroll, y CSS que aplique `background: rgba(13,45,94,0.98)` + `backdrop-filter: blur(8px)` cuando esté esa clase.

### 6. Megamenú con Max Mega Menu
- Activar Max Mega Menu sobre el menú principal de WP.
- Configurar los ítems con dropdown (Matrícula, Visado, Institucional) como mega menus.
- Para "Institucional": sub-items Autoridades, Partidos, Sedes, **Subcomisiones**.
- Estilizar los dropdowns con CSS del child theme: fondo blanco, border-top 3px verde, sombra `0 8px 32px rgba(0,0,0,0.15)`.

### 7. Formulario de contacto con Fluent Forms
- Crear el form con campos: nombre, email, teléfono, asunto (select), mensaje.
- Destino: `info@cipba.org`.
- Embeber en la sección Contacto de la home con el widget Shortcode de Elementor: `[fluentform id="X"]`.
- Aplicar el CSS del prototipo desde el child theme (clases `.ff-el-input--input`, focus `#2563b0`, error `#c0392b`).

### 8. Hero slider de la home con Smart Slider 3
- Crear un slider con 3 slides, auto-rotación cada 6s, fade transition.
- Cada slide con su background gradiente azul (varía por slide) y patrón SVG decorativo.
- Indicadores: pills con fondo `#2a9e2a` (activo) / `rgba(255,255,255,0.35)`.
- Insertar el slider en la home con el widget Smart Slider 3 que se integra con Elementor.

### 9. Resumen — qué va con Elementor y qué con widget HTML + CSS del child theme

| Sección | Implementación |
|---|---|
| TopBar | Elementor H&F Builder, widgets Text + Icon |
| Navbar | Elementor + Max Mega Menu + myStickymenu |
| Hero home (slider) | Smart Slider 3 |
| Hero páginas internas | Elementor Section con background gradient + widgets Heading / Text / Buttons |
| Strip de stats | Widget HTML + CSS del child theme |
| Acceso rápido (trámites) | Widget HTML + CSS del child theme |
| Noticias | **Search & Filter** + CPT "Noticias" + template PHP del child theme |
| Banner Visado | Elementor Section con widgets nativos |
| Capacitación | CPT "Cursos" + Search & Filter (si se quieren filtros) o widget HTML |
| Normativa | CPT "Resoluciones" + Search & Filter + PDF Embedder + template PHP |
| Institucional (resumen home) | Widget HTML + CSS del child theme |
| Contacto | Elementor Section + widget Shortcode con Fluent Forms |
| Autoridades (institucional) | Widget HTML (recomendado) o CPT + Pods |
| Partidos | Widget HTML + JS search del child theme |
| Sedes | Widget HTML + CSS del child theme |
| **Subcomisiones** | Widget HTML + JS search del child theme |
| Footer | Elementor H&F Builder |

### 10. Costo total del stack
**$0 USD en licencias de software.** Solo se paga hosting + dominio. Todos los plugins listados son free indefinidos (no son trials).
