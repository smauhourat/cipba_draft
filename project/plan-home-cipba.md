# Plan — Secciones de la Home (CIPBA Distrito VII)

Continuación de `plan-inicial-wp.md` (infraestructura, ya cerrado). Esto cubre las 9 secciones de contenido de la página de inicio, §4.3 a §4.11 del handoff. Topbar (§4.1), navbar (§4.2) y footer (§4.13) ya están resueltos a nivel tema — no se tocan acá.

Entorno: mismo Docker (`d:\Projects\wordpress_docker`), tema `cipba`, `http://localhost:8080`.

---

## Decisiones para esta etapa

| Decisión | Elegido | Por qué |
|---|---|---|
| Contenido | Placeholders donde falte data real (eventos, novedades, subcomisiones) | Se decidió no bloquear la estructura esperando contenido real — se reemplaza después sin tocar el diseño |
| Eventos (§4.6) | Grilla de 3 (Query Loop nativo), no carrusel | Ya adoptado en `plan-inicial-wp.md` — volumen bajo, cero JS extra |
| Mapa del Distrito VII (§4.9) | **Diferido** — fuera de esta etapa | El propio handoff lo marca como pieza aparte de 4-8 h. Se deja un placeholder marcado en la página |
| Página de inicio | Page de WordPress nueva, asignada como portada estática | Reemplaza el listado de entradas por defecto (`show_on_front=posts` actual) |
| Bloques | Preferir bloques nativos de Gutenberg sobre los de Spectra cuando el resultado visual sea equivalente | Los bloques nativos tienen markup estable y documentado; los de Spectra requieren inspeccionar su schema exacto antes de generarlos por código (mismo aprendizaje que con Max Mega Menu) — se usa Spectra solo donde el nativo no alcanza (Container con enlace, Post Carousel si hiciera falta) |

---

## Secciones

- [x] §4.3 Barra de anuncio de honorarios (patrón sincronizado, fondo verde-100) — bloque reutilizable `anuncio-honorarios` (ID 82), insertado en la página Inicio (ID 81, ya asignada como portada)
- [x] §4.4 Hero de dos puertas (gradiente + crosshatch + 2 tarjetas-enlace) — con corrección: el gradiente y el patrón crosshatch reales del prototipo (`project/index.html`) NO coinciden con el preset `hero-verde` ni con el crosshatch genérico que se había armado en la Fase 7 del plan inicial. Se reemplazó `assets/img/crosshatch.svg` por el patrón real ("Plus" de Hero Patterns) y se usó el gradiente exacto del prototipo por CSS en vez del preset de `theme.json`. Título de página oculto en Astra (`site-post-title = disabled`) porque el hero lo reemplaza.
- [ ] §4.5 Autogestión y Trámites (2 grillas de 3 tarjetas)
- [ ] §4.6 Eventos (grilla de 3, CPT `evento`)
- [ ] §4.7 Noticias (Query Loop nativo, 4 tarjetas, post nativo)
- [ ] §4.8 Subcomisiones (Query Loop de 3, CPT `subcomision`)
- [ ] §4.9 Mapa del Distrito VII — **diferido**, placeholder únicamente
- [ ] §4.10 Preguntas frecuentes (bloque Detalles nativo)
- [ ] §4.11 Contacto por función (2 tarjetas + redes)

---

## Progreso

- **§4.3 verificado visualmente** (2026-09-18): barra de anuncio, navbar con "Inicio" activo, footer completo con los 6 links externos — todo coincide con el diseño.
- **Pendiente:** ocultar el título de página "Inicio" que Astra muestra por defecto arriba del contenido — se resuelve al armar el hero (§4.4), que lo reemplaza visualmente. Astra tiene un toggle por página para esto (metabox "Astra Settings" → Disable Page/Post Title, o similar) — revisar al llegar a esa sección.
