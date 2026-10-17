# Manual de usuario — Carga de contenido del sitio CIPBA Distrito VII

Guía para quien carga y mantiene el contenido del sitio desde el panel de administración de WordPress. **No hace falta saber programar.**

El sitio arma solo el diseño: quien edita carga textos, fechas, imágenes y archivos en formularios, y el sitio se encarga de los colores, las tarjetas, el orden y la ubicación de cada cosa.

> Para lo técnico (código del tema, plugins, servidor), ver `project/manual-tecnico-tema.md`.

---

## Índice

1. [Antes de empezar](#1-antes-de-empezar)
2. [Mapa: qué parte del sitio se edita dónde](#2-mapa-qué-parte-del-sitio-se-edita-dónde)
3. [Datos del Distrito y marcadores](#3-datos-del-distrito-y-marcadores)
4. [Noticias, eventos y novedades](#4-noticias-eventos-y-novedades)
5. [Trámites](#5-trámites)
6. [Página Medios de pago](#6-página-medios-de-pago)
7. [Documentos descargables y página Normativa](#7-documentos-descargables-y-página-normativa)
8. [Honorarios mínimos](#8-honorarios-mínimos)
9. [Página TAD (Tribunal de Árbitros)](#9-página-tad-tribunal-de-árbitros)
10. [Institucional: autoridades, sedes y partidos](#10-institucional-autoridades-sedes-y-partidos)
11. [Subcomisiones](#11-subcomisiones)
12. [Contacto: áreas, formulario y WhatsApp](#12-contacto-áreas-formulario-y-whatsapp)
13. [Página de inicio](#13-página-de-inicio)
14. [Pie de página y menús](#14-pie-de-página-y-menús)
15. [Imágenes: tamaños recomendados](#15-imágenes-tamaños-recomendados)
16. [Tareas frecuentes (recetas)](#16-tareas-frecuentes-recetas)
17. [Preguntas y problemas comunes](#17-preguntas-y-problemas-comunes)

---

## 1. Antes de empezar

### 1.1 Entrar al panel

1. Abrí `https://<dominio-del-sitio>/wp-admin/`.
2. Ingresá tu usuario y contraseña.
3. A la izquierda está el **menú del panel**. Las secciones propias del sitio son: **Datos del Distrito**, **Entradas**, **Trámites**, **Documentos (Normativa)**, **Resoluciones (Honorarios)**, **Subcomisiones**, **Sedes**, **Autoridades**, **Áreas de contacto** y **Links de interés**.

### 1.2 Tipos de usuario

| Usuario | Qué puede hacer |
|---|---|
| **Editor** | Todo lo de este manual: publicaciones, trámites, documentos, honorarios, Datos del Distrito, sedes, autoridades, etc. **No** puede tocar los menús ni los plugins. |
| **Administrador** | Lo mismo que el editor, más menús (Apariencia → Menús), plugins, usuarios y ajustes. Reservado para quien mantiene el sitio. |

Para el trabajo diario, usá el usuario **editor**. Cada persona debería tener su propio usuario.

### 1.3 Cómo funcionan las pantallas de edición

Casi todas las secciones tienen el mismo esquema:

- **Listado** (al hacer clic en el menú): muestra los elementos cargados, con columnas útiles (orden, tipo, archivo…). Desde ahí: **Editar**, **Papelera** o **Agregar nuevo**.
- **Pantalla de edición**: arriba el **título**; debajo, una o más cajas con campos (**"Datos de…"**); a la derecha, la caja **Publicar**.
- **Publicar / Actualizar**: guarda y deja visible. **Guardar borrador**: guarda sin mostrar en el sitio.
- **Vista previa**: muestra cómo queda antes de publicar.
- **Papelera**: lo quita del sitio. Se puede recuperar desde *Papelera* durante 30 días.

**Campos vacíos:** en casi todo el sitio, un campo que dejás vacío simplemente **no se muestra** (una fila sin teléfono, un botón sin texto, un documento sin archivo). No hace falta escribir "—" ni "N/A".

### 1.4 Orden manual

Muchas secciones (Trámites, Subcomisiones, Sedes, Autoridades, Áreas de contacto, Links de interés) se ordenan con el campo **Atributos → Orden** de la columna derecha: **el número más chico va primero**. Conviene numerar de 10 en 10 (10, 20, 30…) para poder intercalar uno nuevo después sin renumerar todo.

### 1.5 Ver los cambios

Los cambios se ven al instante. Si no los ves, recargá con **Ctrl + F5**. Si el sitio tiene una caché activa en el servidor y sigue sin verse, avisale a quien mantiene el sitio (ver §17).

---

## 2. Mapa: qué parte del sitio se edita dónde

| Lo que se ve en el sitio | Dónde se edita (menú del panel) |
|---|---|
| Teléfono, mail, horario y WhatsApp de la **barra superior** | **Datos del Distrito → Contacto general** |
| Íconos de **redes sociales** (arriba y en el pie) | **Datos del Distrito → Redes sociales** |
| Valores repetidos en los trámites (resolución, montos, fechas, códigos de formularios) | **Datos del Distrito → Matrícula y trámites** |
| Datos de la cuenta bancaria (Medios de pago) | **Datos del Distrito → Datos bancarios** |
| **Noticias** y **Eventos y novedades** (home y `/novedades/`) | **Entradas** |
| Colores y orden de las **categorías** de las publicaciones | **Entradas → Categorías** |
| Páginas de **trámites** (Inscripción, Rehabilitación, Baja, Credenciales, Certificados, Medios de pago) | **Trámites** |
| Formularios y documentos **descargables** | **Documentos (Normativa)** |
| Página **Normativa** (`/normativa/`) | **Documentos (Normativa)** (tilde *Mostrar en la página Normativa*) |
| Página **Honorarios mínimos** y la **barra de aviso** de la home | **Resoluciones (Honorarios)** |
| Página **TAD** (`/tad/`) | **Páginas → TAD** |
| **Autoridades** (Institucional) | **Autoridades** + período en **Datos del Distrito** |
| **Sedes y delegaciones** (Institucional, Contacto y home) y la **foto de la home** | **Sedes** |
| **Subcomisiones** (página Subcomisiones y home) | **Subcomisiones** |
| Tarjetas de **áreas** en la página Contacto | **Áreas de contacto** |
| Mensajes recibidos por el **formulario de contacto** | **Fluent Forms → Entries** |
| Columna **Links de interés** del pie | **Links de interés** |
| Menú principal y las otras columnas del pie | **Apariencia → Menús** (solo administrador) |
| Textos fijos de la home (portada, autogestión, preguntas frecuentes) | **Páginas → Inicio** (ver §13: con cuidado) |

---

## 3. Datos del Distrito y marcadores

**Datos del Distrito** es una sola pantalla con los datos generales que se usan en muchos lugares del sitio. **Se cambian una vez y se actualizan en todas partes.**

### 3.1 Secciones de la pantalla

| Sección | Campos | Dónde se ven |
|---|---|---|
| **Consejo Directivo** | Período de autoridades (ej.: `2024 – 2027`) | Título de la sección Autoridades (Institucional) |
| **Contacto general** | Teléfono, correo, correo de tesorería, horario, WhatsApp, enlace al Consejo Superior | Barra superior, botón de WhatsApp (Contacto y Subcomisiones), recuadros de las publicaciones |
| **Redes sociales** | Instagram, Facebook, LinkedIn | Íconos del sitio. **Vacío = el ícono no aparece** |
| **Página de honorarios mínimos** | Enlace al listado del Consejo Superior; cantidad de resoluciones anteriores a mostrar | Página `/honorarios/` |
| **Matrícula y trámites** | Correo para trámites, resolución vigente, módulos y fecha límite, códigos de formularios | Textos de los trámites (vía marcadores) |
| **Datos bancarios (transferencia)** | Cuenta, sucursal, titular, C.B.U., alias, C.U.I.T. | Ventana "Datos de la cuenta" de Medios de pago |

Para cambiar un valor: **Datos del Distrito** → modificá el campo → **Guardar cambios** (abajo de todo).

**Cómo se completan:**

- **Montos:** solo números, sin puntos ni `$`. Ej.: `395000` → se muestra `$ 395.000`.
- **Fechas:** con el calendario. Se muestran como `31/03/2026`.
- **Teléfonos y WhatsApp:** como se quieran leer (`(011) 3535-0751`, `+54 9 11 2713-3330`). El enlace para llamar o abrir WhatsApp se arma solo.
- **Direcciones web:** completas, con `https://`.
- **Dejar vacío** el WhatsApp, el enlace al Consejo Superior o una red social **oculta** ese botón o ícono.

### 3.2 Marcadores

Un **marcador** es el nombre de un dato entre dos llaves dobles. Donde lo escribas, el sitio lo reemplaza por el valor vigente.

| Marcador | Qué es | Se muestra como (ejemplo) |
|---|---|---|
| `{{resolucion}}` | Resolución vigente de matriculación | 1489/24 |
| `{{modulo_1}}` | Módulo por incumplimiento, hasta la fecha límite | $ 395.000 |
| `{{modulo_fecha}}` | Fecha límite de ese valor | 31/03/2026 |
| `{{modulo_2}}` | Módulo por incumplimiento, vencida la fecha límite | $ 564.000 |
| `{{form_inscripcion}}` | Formulario de inscripción | I-2024 |
| `{{form_registros}}` | Formulario de registros especiales | I.R.-2026 |
| `{{form_rehabilitacion}}` | Formulario de rehabilitación | R-2024 |
| `{{form_baja}}` | Formulario de baja | B-2024 |
| `{{form_baja_fallecimiento}}` | Formulario de baja por fallecimiento | BF-2024 |
| `{{form_credencial}}` | Formulario de credenciales | CRE-2024 |
| `{{form_certificado}}` | Formulario de certificados | CER-2024 |
| `{{email_tramites}}` | Correo para iniciar trámites | info@cipba.org |
| `{{email}}`, `{{telefono}}`, `{{horario}}` | Contacto general | tal cual |
| `{{email_tesoreria}}` | Correo de tesorería | tal cual |
| `{{periodo_autoridades}}` | Período del Consejo Directivo | 2024 – 2027 |
| `{{banco_cta_cte}}`, `{{banco_sucursal}}`, `{{banco_titular}}`, `{{banco_cbu}}`, `{{banco_alias}}`, `{{banco_cuit}}` | Datos bancarios | tal cual |

Al editar un trámite, la cajita lateral **"Datos que podés insertar en los textos"** muestra esta lista con los valores actuales, para copiar y pegar.

**Dónde funcionan:** en todos los textos de los **trámites** (incluida Medios de pago) y en el **título de los documentos cuando se muestran dentro de un trámite**. En las publicaciones (Entradas), en las páginas comunes y en los listados de Normativa y TAD **no** se reemplazan: ahí escribí el valor directamente.

**Ejemplo.** Escribís:

> Además de lo normado por la Resolución `{{resolucion}}`… el módulo vale `{{modulo_1}}` por año hasta el `{{modulo_fecha}}`; vencido ese plazo, `{{modulo_2}}`.

Y se ve:

> Además de lo normado por la Resolución 1489/24… el módulo vale $ 395.000 por año hasta el 31/03/2026; vencido ese plazo, $ 564.000.

**Reglas:**

- Dos llaves de cada lado, en minúscula y sin tildes: `{{form_baja}}`. Se admiten espacios adentro: `{{ form_baja }}`.
- Si escribís uno que **no existe** (ej.: `{{resolucion1}}`), la página lo muestra tal cual, con las llaves: es la señal de un error de tipeo.

---

## 4. Noticias, eventos y novedades

Todas las publicaciones son **Entradas** del panel. Hay dos tipos:

| Tipo | Para qué | Dónde aparece |
|---|---|---|
| **Noticia** | Novedades generales de la profesión: normativa, gestiones, comisiones | Home, sección *Noticias* (las 5 más recientes) y `/novedades/?tipo=noticia` |
| **Evento o novedad** | Jornadas, cursos, asambleas, beneficios, avisos internos | Home, sección *Eventos y novedades* (hasta 3; las actividades ya realizadas se ocultan solas) y `/novedades/` |

El listado `/novedades/` tiene dos pestañas (una por tipo), filtros por categoría y buscador. Cada publicación tiene su propia página de detalle.

### 4.1 Cargar una publicación paso a paso

1. **Entradas → Añadir nueva**.
2. **Título.**
3. **Contenido** con el editor de bloques: párrafos; subtítulos con el bloque *Encabezado*; listas con el bloque *Lista* (se ven con tildes verdes). Evitá pegar texto con formato desde Word: pegalo como texto simple (**Ctrl + Shift + V**).
4. Panel derecho:
   - **Imagen destacada** (ver §15 sobre el formato según el tipo).
   - **Categoría**: elegí **una sola**. Define el color de la etiqueta y el filtro del listado.
   - **Fecha de publicación** (en *Publicar*): ordena el listado y es la fecha que se muestra. Se puede **programar** una fecha futura: la publicación aparece sola ese día.
5. Debajo del editor, la caja **Datos de la publicación** (ver 4.2).
6. Si es una actividad con fecha, tildá **Es una actividad con fecha** y completá **Datos de la actividad** (ver 4.3).
7. **Publicar.**

### 4.2 Datos de la publicación

| Campo | Para qué sirve |
|---|---|
| **Tipo de publicación** | **Noticia** o **Evento o novedad** (ver tabla de arriba). Si no se elige, cuenta como Noticia. |
| **Bajada** | Resumen de una o dos oraciones: va en las tarjetas y bajo el título. |
| **Texto de fecha alternativo** | Opcional. Reemplaza la fecha mostrada (ej.: *Vigente todo 2026*). |
| **Destacada** | Se muestra como tarjeta grande arriba del listado. Si hay varias, la más reciente. |
| **Galería de fotos** | Opcional. *Agregar medios* para subir o elegir varias fotos a la vez; se arrastran para ordenarlas. Se muestran en una grilla debajo del texto y se amplían al hacer clic. Para quitar una, pasá el mouse sobre la miniatura y tocá la ×. |
| **Botón de acción** | *Texto del botón* + *Enlace* (formulario de inscripción, otra página, un archivo…), **o** un *documento de la biblioteca*. Sin enlace ni documento, el botón dice "Escribinos" y lleva a Contacto. |
| **Texto del recuadro** | Opcional. Reemplaza el texto fijo del recuadro *Inscripción / Más información*. |
| **Mail** y **Teléfono** de contacto | Se muestran en el recuadro. Sin mail, se usa el general del Colegio; sin teléfono, no se muestra. |
| **Ocultar el recuadro "Más información"** | Para publicaciones que no necesitan botón ni contacto. |
| **Es una actividad con fecha** | Hace aparecer la caja *Datos de la actividad*. |

### 4.3 Datos de la actividad (eventos)

Fecha de inicio (y de fin, si dura varios días), horario, lugar, modalidad (Presencial / Virtual / Mixta, con aclaración opcional), cupo, arancel, **Inscripción abierta** y fecha de cierre.

Con eso, la página muestra la ficha **"Datos de la actividad"** (la fecha se escribe sola: *Jueves 9 de abril de 2026* o *Del 6 al 27 de mayo de 2026*) y el recuadro **Inscripción** con el botón. Si la inscripción no está abierta, el recuadro dice *Más información*.

> **Cuándo desaparece un evento de la home:** al día siguiente de su *fecha de fin* (o de inicio, si no tiene fin). Sigue visible en `/novedades/` y en su página. Un "Evento o novedad" **sin** fecha de actividad no se oculta nunca solo: van saliendo de la home a medida que se cargan otros más nuevos.

### 4.4 Cómo se ve cada tipo

- **Eventos y novedades:** la imagen se muestra **completa, sin recortes**, en su proporción original (pensada para los flyers de Instagram, 4:5 o 9:16). La página se arma en dos columnas: a la izquierda la publicación; a la derecha, los últimos 5 eventos.
- **Noticias:** imagen **apaisada** que se recorta a lo ancho; al pie aparecen "Otras novedades" (3, de la misma categoría primero).

### 4.5 Categorías y colores

**Entradas → Categorías**: cada categoría tiene **Color** (violeta, verde, verde oscuro o ladrillo) y **Orden** (posición en los filtros; menor, primero). Las categorías sin publicaciones no aparecen en los filtros.

### 4.6 Lo que se arma solo

Botones de compartir (WhatsApp, LinkedIn, Facebook, correo, copiar enlace), "Otras novedades", la columna de eventos laterales, la ruta de navegación, el contador y el filtrado del listado.

### 4.7 En el listado del panel

**Entradas** muestra una columna **Tipo** (con ★ si es destacada y la fecha de la actividad) y un filtro **Todos los tipos / Noticia / Evento o novedad**.

---

## 5. Trámites

Los trámites (Inscripción, Rehabilitación, Baja, Solicitud de credenciales, Solicitud de certificados) usan **una sola plantilla**: lo que cambia de uno a otro se carga en un formulario. Medios de pago es un caso especial (§6).

Panel → **Trámites** → elegí el trámite → **Editar**. El **título** es el nombre de la página (y el que aparece en el menú y en "Otros trámites").

### 5.1 Encabezado

| Campo | Para qué sirve |
|---|---|
| **Ícono** | Ícono del trámite en el bloque "Otros trámites" (medalla, tilde, documento, escudo, pesos). |
| **Etiqueta superior** | Texto chico en verde sobre el título (ej.: "Matrícula profesional"). |
| **Nombre corto (opcional)** | Solo si el título es largo: se usa en la ruta de navegación (Inicio › Trámites › …). |
| **Bajada** | Texto bajo el título. |

### 5.2 Cuadro lateral (costos y condiciones)

El recuadro oscuro de la derecha: un **título** y hasta **4 filas**, cada una con **etiqueta** (chica, en mayúsculas) y **texto**. Una fila vacía no se muestra.

### 5.3 Contenido de la página (bloques)

Hay **6 bloques** que se muestran **en el orden en que están cargados**. Cada uno tiene:

- **Tipo:** *Lista de requisitos* (tarjetas numeradas), *Aviso (verde)*, *Aviso importante (ámbar)* o *— No usar este bloque —* (se ignora).
- **Título**.
- **Texto introductorio** (opcional, solo listas).
- **Contenido**, con un editor parecido a Word.

**Listas de requisitos:** usá una **lista numerada**; en cada ítem, el nombre en **negrita** y el detalle a continuación:

1. **Formulario `{{form_inscripcion}}` completo** — o sus posteriores actualizaciones.
2. **Documento de identidad (DNI)** — copia escaneada.

La numeración y el diseño de tarjeta los pone el sitio. Un ítem solo con el nombre en negrita también vale.

**Avisos:** uno o más párrafos. Se pueden poner enlaces con el botón de la cadena.

### 5.4 Formularios y documentación

- **Título de la sección** y **texto introductorio**.
- **Documento 1 a 8:** desplegables para elegir de la biblioteca de Documentos (§7). Se muestran en ese orden; los vacíos no aparecen.

### 5.5 Guardar y ordenar

- **Actualizar** guarda; **Vista previa** muestra cómo queda.
- En "Otros trámites" se ordenan por **Atributos → Orden**.

### 5.6 Crear un trámite nuevo

1. **Trámites → Agregar trámite**.
2. Título + **Datos del trámite**.
3. **Publicar.** Aparece solo en "Otros trámites" de los demás. Para sumarlo al menú principal o al pie, pedíselo a un administrador (§14.2). Su dirección será `/tramites/nombre-del-tramite/`.

---

## 6. Página Medios de pago

Es un trámite más (**Trámites → Medios de pago**, dirección `/tramites/pago-matricula/`) con diseño propio: cuatro tarjetas (transferencia, débito automático, Red Link, Pagomiscuentas) y ventanas emergentes. Su formulario se llama **Datos de la página de pago**.

> No cambies la dirección (slug) `pago-matricula` de este trámite: es lo que hace que use su diseño especial.

### 6.1 Datos bancarios (Modalidad 1)

Se cargan en **Datos del Distrito → Datos bancarios (transferencia)** y se muestran en la ventana **"Datos de la cuenta"**.

- El botón **Copiar** del C.B.U. copia **solo los números**; el del alias, el alias tal cual.
- Un dato vacío hace desaparecer su fila.

### 6.2 Documentos de descarga (Modalidades 2 y 4)

1. En **Documentos (Normativa)** se sube cada archivo una vez (formulario de adhesión al débito automático, instructivo de Pagomiscuentas).
2. En **Trámites → Medios de pago**, dentro de *Modalidad 2* y *Modalidad 4*, se elige el documento.
3. **Modalidad 2:** el botón de la tarjeta descarga el archivo. **Modalidad 4:** aparece como "Descargar instructivo" al pie de su ventana.

### 6.3 Resto de los textos

Título y bajada; sección "Antes de pagar" (texto y lista "tené a mano", **un elemento por línea**); para cada modalidad: título, descripción, pasos (**uno por línea**; los correos y enlaces se vuelven clickeables) y texto del botón; texto al pie de los datos bancarios; dirección de la consulta de Red Link; mensaje final. Todos admiten marcadores.

> **Ventana de Red Link:** muestra una página del Consejo Superior que no usa conexión segura (`http`). Algunos navegadores la bloquean dentro del sitio; la ventana avisa y ofrece abrirla en una pestaña nueva.
>
> **Instructivo de Pagomiscuentas:** su contenido (tabla y pasos) es fijo y no se edita desde el panel. Para cambiarlo, pedíselo a quien mantiene el tema.

---

## 7. Documentos descargables y página Normativa

**Documentos (Normativa)** es la **biblioteca única** de archivos descargables. Cada documento se carga **una sola vez** y se usa en todos los lugares que lo necesiten: trámites, Medios de pago, Normativa, TAD, botones de publicaciones.

### 7.1 Campos de un documento

| Campo | Para qué sirve |
|---|---|
| **Título** | Nombre que se muestra. Dentro de los trámites admite marcadores: `{{form_baja}} Formulario de baja` → *B-2024 Formulario de baja*. En Normativa y TAD el marcador se vería tal cual, así que en documentos que van ahí no los uses. |
| **Archivo** | PDF, Word, Excel… El **tipo** y el **peso** se leen solos del archivo. |
| **Enlace externo (opcional)** | Solo si el archivo **no** está en este sitio. Si además hay archivo subido, se ignora. |
| **Mostrar en la página Normativa** | Tildalo para que aparezca en `/normativa/`. Los formularios de trámites **no** se tildan. |
| **Descripción** | Una o dos oraciones; se muestra en Normativa y TAD. |
| **Origen** | Quién lo emite: "Provincia de Buenos Aires", "Consejo Superior", "Distrito VII". |
| **Orden** | Orden en el listado de Normativa (menor, primero). |

El listado del panel muestra columnas **Archivo** (tipo y peso), **En Normativa** (✓) y **Orden**.

### 7.2 Página Normativa

Lista los documentos con el tilde *Mostrar en la página Normativa*, por **Orden**. Los que son solo un enlace externo (sin archivo subido) se listan aparte, bajo **"En el sitio del Consejo Superior"**.

### 7.3 Reemplazar un archivo por una versión nueva

Abrí el documento → quitá el archivo viejo (×) → subí el nuevo → **Actualizar**. **Todas** las páginas que lo usan (trámites, Medios de pago, menús que lo enlazan) quedan actualizadas solas. No crees un documento nuevo: perderías las vinculaciones.

### 7.4 Usar un documento en un trámite

Editá el trámite → *Formularios y documentación* → elegí el documento en el primer desplegable libre → **Actualizar**.

---

## 8. Honorarios mínimos

La página **`/honorarios/`** muestra la **resolución vigente** con sus dos documentos y una lista de **resoluciones anteriores**. Cada resolución es un ítem de **Resoluciones (Honorarios)**.

### 8.1 Cargar una resolución

**Resoluciones (Honorarios) → Agregar resolución.** No hay campo de título: se arma solo con el código (*1553/2026* → **"Resolución CS 1553/2026"**).

| Campo | Para qué sirve |
|---|---|
| **Código** | Número y año (ej.: `1553/2026`). |
| **Fecha de publicación** | *"Publicada el 27 de febrero de 2026"* (vigente) o *"Publicada 27/02/2026"* (anteriores). |
| **Vigencia desde** | En la vigente, el recuadro *"Vigente desde"*. |
| **Vigencia hasta** *(opcional)* | En las anteriores: *"Vigente 01/10/2025 – 31/03/2026"*. |
| **Es la resolución vigente** | **Solo puede haber una**: al tildarla, la anterior se destilda sola. |
| **Descripción** | En la vigente, resumen bajo el título; en las anteriores, la nota de la lista. |
| **Resolución (documento)** y **Anexos (documento)** | Los dos archivos de la vigente, subidos al sitio. |
| **Enlace de descarga (Consejo Superior)** | Para las anteriores: dónde las publica el Consejo Superior. |

### 8.2 Orden y cantidad

- Anteriores: de la **más nueva a la más vieja**.
- Se muestran las **6 más recientes**; se cambia en **Datos del Distrito → Página de honorarios mínimos** (0 = todas).
- La **barra de aviso de la home** ("Honorarios mínimos vigentes desde 01/04/2026 — Res. 1553") se arma sola con la resolución vigente. Sin vigente, no se muestra.

---

## 9. Página TAD (Tribunal de Árbitros)

**`/tad/`** muestra las resoluciones del Tribunal, el botón **Descargar formulario** y el mail de contacto. Se edita en **Páginas → TAD**, caja **Contenido de la página TAD**:

| Campo | Para qué sirve |
|---|---|
| **Documento 1 a 6** | Las resoluciones, en orden, elegidas de la biblioteca de **Documentos**. Título, descripción y origen son los del documento. |
| **Formulario de inicio** | Documento del botón *Descargar formulario*. Vacío = sin botón. |
| **Mail de contacto** | Vacío = no se muestra. |

El título y el texto del encabezado se editan en el contenido de la página.

---

## 10. Institucional: autoridades, sedes y partidos

### 10.1 Autoridades (Consejo Directivo)

**Autoridades → Agregar autoridad.**

- **Título:** nombre y apellido (ej.: *Daniel Héctor PALACIOS*).
- **Cargo** (Presidente, Secretario, Vocal Titular 1°…) y **Título profesional** (Ing. Civil…).
- **Presidencia:** tildalo **solo** en quien preside: se muestra como tarjeta grande.
- **Orden** (Atributos): posición en la grilla.

Las iniciales del avatar se generan solas del nombre. El **período** (2024 – 2027) se cambia en **Datos del Distrito → Consejo Directivo**.

**Cambio de autoridades:** editá cada ficha (nombre, cargo, título) en lugar de borrar y crear; mandá a la papelera las que sobren y actualizá el período.

### 10.2 Sedes y delegaciones

**Sedes → Agregar sede.** Se usan en tres lugares: Institucional, Contacto y la sección "El Distrito VII" de la home.

| Campo | Para qué sirve |
|---|---|
| **Título** | Nombre de la sede (ej.: *Sede San Justo*). |
| **Tipo** | Etiqueta sobre el nombre (ej.: *Sede Central*, *Delegación Haedo*). |
| **Casa Central** | Tildalo **solo** en la sede principal: tarjeta grande, lista de contactos directos y su foto en la home. |
| **Dirección**, **Teléfono**, **Email**, **Visador** (opcional), **Horario** | Datos de la tarjeta. |
| **Contacto directo 1 a 8** | Solo Casa Central: área, nombre, teléfono y email de cada contacto. |
| **Foto de la sede** (columna derecha) | Solo se usa la de la Casa Central: es la foto de la home, con la dirección como epígrafe. Sin foto, no se muestra nada. |

### 10.3 Partidos comprendidos

La lista de los 23 partidos del Distrito VII (sección de Institucional y el buscador "¿Tu partido pertenece al Distrito VII?" de la home) es **fija** y no se edita desde el panel: la fija la Ley 10.416. Si hubiera que corregirla, pedíselo a quien mantiene el tema.

---

## 11. Subcomisiones

**Subcomisiones → Agregar subcomisión.** Se muestran en la página `/subcomisiones/` (con buscador) y las **3 primeras** según el orden en la home.

| Campo | Para qué sirve |
|---|---|
| **Título** | Nombre (ej.: *Ingeniería Civil*). |
| **Sigla** | Abreviatura del recuadro de la tarjeta (ej.: *Civ*, *HyS*). |
| **Email de la subcomisión** | Bajo el nombre en el listado y en la tarjeta de la home. |
| **Descripción** | Opcional; solo en la tarjeta de la home. |
| **Referente 1 a 4** | Nombre, matrícula, teléfono y email. El 1 es el principal; los vacíos no se muestran. |
| **Orden** (Atributos) | Posición en el listado y cuáles van a la home. |

---

## 12. Contacto: áreas, formulario y WhatsApp

### 12.1 Áreas de contacto

**Áreas de contacto → Agregar.** Son las tarjetas de la página Contacto.

- **Título:** nombre del área (ej.: *Área Administrativa*).
- **Ícono:** personas, edificio o documento.
- **Correo del área:** al pie de la tarjeta.
- **Persona 1 a 4:** nombre, teléfono y email.
- **Orden** (Atributos).

### 12.2 Formulario de contacto: leer los mensajes

Los mensajes que envían los visitantes se guardan en **Fluent Forms → Entries** (además de llegar por mail, si el servidor tiene el envío de correo configurado). Ahí se pueden leer, marcar como leídos y exportar. Si alguien dice que escribió y no llegó el mail, buscalo ahí.

### 12.3 Botón de WhatsApp y sedes

El botón verde de WhatsApp usa el número de **Datos del Distrito**; si se vacía, el botón desaparece. Las tarjetas de sedes de Contacto salen de **Sedes**.

---

## 13. Página de inicio

La home combina partes **automáticas** (se actualizan solas desde otras secciones) y partes de **texto fijo** (en la página *Inicio*).

| Sección de la home | De dónde sale |
|---|---|
| Barra de aviso de honorarios | Automática: resolución vigente (§8) |
| Portada ("Todo tu ejercicio profesional…") | Texto fijo en **Páginas → Inicio** |
| Autogestión y Trámites del Distrito (tarjetas) | Texto fijo en **Páginas → Inicio** |
| Eventos y novedades | Automática: Entradas tipo *Evento o novedad* (§4) |
| Noticias | Automática: Entradas tipo *Noticia* (§4) |
| Subcomisiones | Automática: las 3 primeras (§11) |
| El Distrito VII (foto, Casa Central, delegaciones, buscador de partidos) | Automática: **Sedes** (§10.2) |
| Preguntas frecuentes | Texto fijo en **Páginas → Inicio** |
| Contacto por función | Texto fijo en **Páginas → Inicio** |

**Editar los textos fijos:** las secciones de la home están armadas con bloques de código HTML para respetar el diseño. Se puede corregir un texto con cuidado (Páginas → Inicio → seleccionar el bloque → cambiar solo las palabras, sin tocar lo que está entre `<` y `>`), pero **para agregar o quitar tarjetas o preguntas, pedíselo a quien mantiene el tema**. Antes de guardar, usá **Vista previa**. Si algo se rompe, en la columna derecha está **Revisiones**, que permite volver a la versión anterior.

---

## 14. Pie de página y menús

### 14.1 Links de interés (última columna del pie)

Panel → **Links de interés**.

- **Agregar:** *Agregar link de interés* → **nombre del sitio** (título), **dirección completa** con `https://` y el **orden** → **Publicar**.
- **Cambiar:** abrí el link, corregí y **Actualizar**.
- **Quitar:** a la papelera, o pasalo a **Borrador** para conservarlo sin mostrarlo.

Se abren en pestaña nueva con el ícono de enlace externo. Un link sin dirección no se muestra.

### 14.2 Menús (solo administrador)

El menú principal y las columnas **Trámites**, **Normativa** e **Institucional** del pie, y los enlaces legales del final, se editan en **Apariencia → Menús** (menús *Principal*, *Pie - Trámites*, *Pie - Normativa*, *Pie - Institucional* y *Pie - Legal*). Requiere usuario administrador.

---

## 15. Imágenes: tamaños recomendados

| Dónde | Formato recomendado | Qué pasa |
|---|---|---|
| **Evento o novedad** (imagen destacada) | **Vertical 4:5** (ej.: 1080 × 1350), o 9:16. El flyer de Instagram tal cual. | Se muestra completa, sin recortes. |
| **Noticia** (imagen destacada) | **Horizontal**, al menos **1680 × 720** | Se recorta a lo ancho: dejá lo importante en el centro. |
| **Galería de fotos** | Cualquier proporción; mínimo 1200 px de lado mayor | Miniaturas recortadas; al ampliar, se ve completa. |
| **Foto de la sede** (Casa Central) | Horizontal, mínimo 1200 px de ancho | Se muestra en la home. |

Consejos:

- Usá **JPG** para fotos y **PNG** para gráficos con texto. Evitá subir imágenes de más de 2–3 MB: achicalas antes (ej.: con [squoosh.app](https://squoosh.app)).
- Completá el **texto alternativo** de la imagen (en la biblioteca de medios): lo leen los lectores de pantalla y los buscadores.
- No pongas en la imagen información que no esté también en el texto (fechas, lugares).

---

## 16. Tareas frecuentes (recetas)

### Publicar un evento con inscripción
1. **Entradas → Añadir nueva** → título, contenido, flyer vertical como imagen destacada, categoría.
2. *Tipo de publicación*: **Evento o novedad**. Completá la **Bajada**.
3. Tildá **Es una actividad con fecha** → fecha, horario, lugar, modalidad, cupo, arancel, **Inscripción abierta** y cierre.
4. *Botón de acción*: texto ("Inscribirme") y el **enlace** al formulario de inscripción.
5. **Publicar.**

### Subir las fotos de un evento que ya pasó
Abrí la publicación → **Galería de fotos → Agregar medios** → elegí las fotos → **Actualizar**. Opcional: destildá *Inscripción abierta*.

### Salió una nueva resolución de honorarios
1. **Resoluciones (Honorarios) → Agregar resolución**: código, fecha de publicación, vigencia desde, descripción, los dos documentos, y tildá **Es la resolución vigente**.
2. Abrí la que estaba vigente (ya pasó sola a *anteriores*) y cargale el **Enlace de descarga (Consejo Superior)** y, si corresponde, *Vigencia hasta*.
3. La barra de la home se actualiza sola.

### Cambió la resolución de matriculación
**Datos del Distrito** → *Resolución vigente* → **Guardar cambios**. Se actualiza en todos los textos que usan `{{resolucion}}`.

### Cambiaron los montos del módulo o su fecha límite
**Datos del Distrito → Matrícula y trámites** → montos y/o fecha → **Guardar cambios**.

### Año nuevo, formularios nuevos (ej.: B-2025)
1. **Datos del Distrito** → cambiá el código (*Formulario de baja*: `B-2025`).
2. **Documentos** → abrí el documento → reemplazá el archivo por el nuevo → **Actualizar**.
3. Los textos de los trámites no se tocan: usan el marcador.

### Cambió la cuenta bancaria
**Datos del Distrito → Datos bancarios** → **Guardar cambios**.

### Cambió el formulario de débito automático o el instructivo de Pagomiscuentas
**Documentos** → abrí el documento → subí el archivo nuevo → **Actualizar**.

### Agregar o quitar un requisito de un trámite
Editá el trámite → bloque de la lista → agregá o borrá un ítem de la lista numerada → **Actualizar**.

### Agregar una ley o resolución a Normativa
**Documentos → Agregar documento** → título, archivo (o enlace externo), descripción, origen, orden y tildá **Mostrar en la página Normativa** → **Publicar**.

### Cambió el teléfono, el horario o el WhatsApp
**Datos del Distrito → Contacto general** → **Guardar cambios**. Si cambió el de una sede en particular, editalo en **Sedes**.

### Cambió un referente de subcomisión
**Subcomisiones** → abrí la subcomisión → corregí el referente → **Actualizar**.

### Agregar, cambiar o quitar un link del pie
Ver §14.1.

---

## 17. Preguntas y problemas comunes

**Veo `{{algo}}` con las llaves en la página.**
El marcador está mal escrito o no existe. Compará con §3.2 o con la cajita lateral del trámite. Recordá que en las Entradas los marcadores no funcionan.

**Cambié algo y no se ve.**
Recargá con **Ctrl + F5** o probá en una ventana de incógnito. Si sigue igual, el servidor puede tener una caché: pedile a quien mantiene el sitio que la vacíe.

**Un evento no aparece en la home.**
Revisá que el *Tipo de publicación* sea **Evento o novedad**, que esté **publicado** (no programado para una fecha futura ni en borrador) y que su fecha de actividad no haya pasado. La home muestra solo 3.

**Una noticia aparece en Eventos (o al revés).**
Cambiá el *Tipo de publicación* y **Actualizar**.

**La imagen de un evento se ve con bandas a los costados.**
Es normal si la imagen no es vertical: los eventos muestran la imagen completa sin recortar. Usá un flyer 4:5.

**Un documento no aparece en la lista de un trámite.**
No está **publicado**, o no tiene ni archivo ni enlace externo.

**Un documento muestra "ENLACE" como tipo.**
Tiene un enlace externo que no termina en `.pdf`, `.docx`, etc. Subí el archivo al sitio para que el tipo se detecte solo.

**Necesito más de 6 bloques, 4 filas de costos, 8 documentos en un trámite, 4 referentes o 6 documentos en TAD.**
Son límites del formulario; los puede ampliar quien mantiene el tema.

**Al editar una sede, un trámite o una autoridad no aparecen los campos.**
Falta activar el plugin *Meta Box*. Avisale al administrador.

**¿Se puede romper el diseño escribiendo mal?**
En los formularios, no: el sitio arma el diseño y limpia el código no permitido que se pegue. El único lugar delicado son los textos fijos de la página *Inicio* (§13).

**Borré algo por error.**
Está en la **Papelera** del listado correspondiente durante 30 días: *Restaurar*. Para un texto que se cambió mal en una página o entrada, usá **Revisiones** (columna derecha) para volver a una versión anterior.
