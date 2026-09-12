# CIPBA Distrito VII — Design System

Sistema de diseño para el rediseño del sitio institucional del **Colegio de Ingenieros de la Provincia de Buenos Aires (CIPBA) – Distrito VII** (cipba.org). Distrito VII depende del **Consejo Superior** (colegioingenieros.org.ar) junto con otros distritos, y este rediseño toma como referencia estructural el sitio de **Distrito I** (colegiodeingenieros.org.ar).

El Colegio de Ingenieros de la Provincia de Buenos Aires es una entidad de carácter público no estatal (Ley 10.416) que controla el ejercicio profesional de la ingeniería en la provincia. El Distrito VII cubre 24 partidos de la zona oeste y norte del Gran Buenos Aires (sede central en San Justo, La Matanza), con delegaciones en Haedo, Olivos y General Rodríguez. Sus funciones principales hacia los matriculados: matrícula profesional, visado de trabajos técnicos, honorarios mínimos, capacitación continua, normativa/resoluciones y subcomisiones técnicas por especialidad.

## Fuentes

Este sistema se construyó a partir de un handoff de diseño de alta fidelidad (prototipos HTML/React) provisto por el usuario, NO de un repositorio de producción ni de un archivo Figma:

- `ColegioIngPba-HandOff/design_handoff_cipba/` — handoff completo: `index.html` (Home), `institucional.html`, `subcomisiones.html`, 4 versiones de README con los design tokens y specs de cada sección, y un log de conversación de Claude Code (`wpplan-chat.md`) documentando la implementación en WordPress (Astra + Elementor, stack 100% free).
- `design_handoff_cipba2/` — variante posterior de la home.
- Logos sueltos: `logo-cipba-vii.png`, `logo-cipba-vii-2.png`, `logo_IVAN_HORIZONTAL.png`, `LOGO-CIPBA.bmp`.
- Referencia de sitio hermano: colegiodeingenieros.org.ar (Distrito I) — estructura de menú (Institucional, Normativas, Ejercicio Profesional, Autogestión, De Interés) y tono institucional.
- Consejo Superior: colegioingenieros.org.ar.

Estas carpetas están montadas localmente y no son accesibles vía URL pública; se documentan aquí como referencia si el usuario vuelve a adjuntarlas.

## Índice del proyecto

- `styles.css` — entry point (`@import` a `tokens/*.css` + `base.css`)
- `tokens/` — `colors.css`, `typography.css`, `spacing.css`, `effects.css` (radios, sombras, easing), `fonts.css`
- `assets/` — logo (PNG, con y sin wordmark), patrón decorativo crosshatch (svg/png)
- `guidelines/` — specimen cards (Colors, Type, Spacing, Brand)
- `components/`
  - `core/` — Button, Badge, SectionHeader, IconBadge, Icon
  - `navigation/` — TopBar, Navbar, Breadcrumb, Footer
  - `cards/` — Card, StatCard, QuickAccessCard, NewsCard, AuthorityCard, SedeCard, SubcomisionCard
  - `data/` — DocumentRow, CourseRow, PartidoChip
- `ui_kits/sitio-web/` — recreación clickeable de 3 pantallas: Home, Institucional, Subcomisiones (`index.html`, hash-routed)
- `thumbnail.html` — tile del proyecto

## Intentional additions

El handoff es un prototipo de página completa, no una librería de componentes formal. Los componentes de este sistema se extrajeron de los patrones recurrentes que sí aparecen en el prototipo (botones, badges de categoría, cards de trámite/noticia/autoridad/sede, filas de documento y curso). Un componente `Icon` (wrapper de Lucide) se agregó como sustituto documentado de los SVG dibujados a mano del prototipo — ver Iconografía.

## Content fundamentals

- **Idioma y trato**: español rioplatense formal-cercano, voseo ("Visá tus trabajos", "Inscribite", "Escribinos"). Se dirige al matriculado en segunda persona, nunca en primera del plural ("nosotros").
- **Tono**: institucional pero directo — sin jerga de marketing. Los títulos describen el trámite o beneficio concreto ("Visado Online para Ingenieros Matriculados", "Habilitá tu ejercicio profesional en el Distrito VII").
- **Estructura de copy**: eyebrow corto en mayúsculas + título Roboto Condensed + descripción breve en Lato. Los CTAs son verbos de acción imperativos ("Acceder al sistema", "Solicitar matrícula", "Ver oferta académica").
- **Números y datos**: se usan cifras concretas para transmitir escala institucional (+8.500 matriculados, 24 partidos, 3 sedes, +30 años) — nunca estadísticas vagas.
- **Nomenclatura oficial**: cargos, resoluciones y documentos siguen nomenclatura formal argentina ("Resolución CS 187/2026", "Ing. Civil", "Inga. Civil" con flexión de género en el título profesional).
- **Sin emoji** en el copy institucional (el prototipo usa un 📌 puntual para "Destacado" en noticias — es la única excepción, y es sustituible por el badge de card destacada).

## Visual foundations

- **Colores**: paleta monocromática verde con un acento púrpura, tomada del manual de marca. Verde de marca `--green-500` #00A48A (del isologo) para CTAs primarios, links, badges de "activo" y checkmarks; verde profundo `--green-700` #14484A para navbar, footer y superficies oscuras; `--green-200` #C0E2CA y `--green-100` #E8F5F1 como tintes de superficie. Neutrales: tinta `--ink-900` #373D3E para texto, gris-teal `--teal-gray-500` #607A7C para texto secundario. `--purple-500` #484586 es el acento institucional. Los colores de categoría (rojo tierra `--red-800`, púrpura) se usan ÚNICAMENTE como etiquetas de taxonomía (Normativa, Honorarios, Comisiones) — nunca como acento de marca. No se usa azul, marrón ni ocre en elementos de marca. Los alias `--blue-*` se conservan por compatibilidad y apuntan a los verdes equivalentes.
- **Tipografía**: **Roboto Condensed** (weights 400/700/900) para títulos, cifras y navbar brand; **Lato** (300/400/700/900) para cuerpo, labels y botones. Jerarquía clara: eyebrow uppercase 12px verde → título Roboto Condensed 900 → descripción Lato 400 a 14–17px.
- **Espaciado**: contenedor centrado máx. 1200px; secciones con padding vertical 52–60px; grids con gap 16–20px.
- **Fondos**: alternancia de blanco y gris muy claro (`--gray-100` #eef2f1) sección por sección — nunca dos secciones blancas consecutivas. Los heroes y banners usan **gradientes lineales azules** (135deg, de azul profundo a azul medio) o verdes (banner CTA). No hay imágenes fotográficas de fondo en el prototipo — el único elemento decorativo es un **patrón crosshatch SVG** blanco a opacidad 0.05–0.06 sobre los gradientes.
- **Animación**: sutil y funcional. Transiciones de 0.2–0.3s ease en hovers; el hero-slider rota automáticamente cada 6s con fade de fondo (0.8s). Nada de bounce ni easings elásticos.
- **Hover / press states**: cards suben `translateY(-4px)` + sombra más profunda; botones primarios oscurecen el verde y suben 2px; links de menú ganan fondo blanco translúcido 12%; no hay estado de "press"/active distinto documentado (se infiere `translateY(0)` + sombra reducida).
- **Bordes**: 1px sólido en tono `--border-card` (#e8f5f1, azul pálido) en casi todas las cards — nunca borde grueso ni border-left de acento coloreado.
- **Sombras**: muy suaves en reposo (`0 2px 8px rgba(0,0,0,.04)`), más marcadas en hover (`0 8px 24px rgba(0,0,0,.10)`); la navbar al hacer scroll gana `0 2px 20px rgba(0,0,0,.25)`. Sin sombras internas (inset).
- **Transparencia / blur**: `backdrop-filter: blur(8px)` en la navbar sticky al scrollear; overlays de dropdown y banners usan `rgba()` blancos/verdes al 6–25% de opacidad sobre fondos oscuros.
- **Radios**: 6px en botones, 10px en cards estándar, 12px en cards destacadas (autoridades, sedes, subcomisiones), 20px en badges/pills, 8px en contenedores de ícono.
- **Layout fijo**: navbar sticky (top:0, z-index alto); sin sidebars ni elementos flotantes adicionales.
- **Grilla de imágenes**: el prototipo no incluye fotografía real — todos los avatares de personas son iniciales sobre gradiente circular (azul claro para autoridades regulares, verde para la presidencia), no fotos.

## Iconography

- El prototipo original usa **íconos SVG inline dibujados a mano**, estilo **Feather Icons** (trazo, sin relleno, stroke-width 2, esquinas redondeadas).
- **Sustitución documentada**: este sistema reemplaza esos SVG a mano por **Lucide** (sucesor directo y mantenido de Feather, mismo lenguaje visual — stroke 2px, esquinas redondeadas) vía CDN (`unpkg.com/lucide`), envuelto en el componente `Icon`. Se flagea esta sustitución: si el usuario cuenta con los SVG originales del prototipo, pueden reemplazar el wrapper `Icon` por esos archivos sin cambiar la API de los componentes.
- No hay emoji en la interfaz (excepción puntual de 📌 en una card de noticia destacada del prototipo original, no reproducida aquí).
- No hay sprites PNG ni fuente de iconos propietaria.
- El WhatsApp icon es el único SVG de marca (logo oficial de WhatsApp) usado tal cual, a color verde de marca de WhatsApp (`--whatsapp` #25d366), no del verde institucional.

## Components

**Core**: Button, Badge, SectionHeader, IconBadge, Icon
**Navigation**: TopBar, Navbar, Breadcrumb, Footer
**Cards**: Card, StatCard, QuickAccessCard, NewsCard, AuthorityCard, SedeCard, SubcomisionCard
**Data**: DocumentRow, CourseRow, PartidoChip

## UI kit

`ui_kits/sitio-web/` — recreación clickeable de 3 pantallas reales del handoff (Home, Institucional, Subcomisiones), compartiendo TopBar/Navbar/Footer. Navegación por hash (`#/home`, `#/institucional`, `#/institucional/subcomisiones`) con enlaces de acceso rápido fijos en la esquina inferior derecha para la demo.

## Caveats

- Los archivos fuente (carpeta `web/` local) no incluyen un repositorio de producción ni Figma — todo el trabajo parte del handoff estático de diseño. Si existe un repo WordPress real o un Figma, conectarlo para refinar fidelidad.
- Los íconos son un sustituto (Lucide) del set dibujado a mano original — ver Iconografía.
- El isologo verde (sunburst) se copió tal cual de los assets provistos; no se generó ni modificó.
- Los datos de autoridades, sedes y subcomisiones son reales (provistos en el handoff) pero deben verificarse contra la fuente oficial antes de publicar.
