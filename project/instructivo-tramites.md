# Instructivo: editar trámites, documentos y datos del Distrito

Guía para quien carga y mantiene el contenido del sitio. No hace falta saber programar.

Los trámites (Inscripción, Rehabilitación, Baja y Solicitud de credenciales) usan **una sola plantilla**. Lo que cambia de una página a otra se carga desde un formulario en el panel de administración, y los valores que se repiten (resoluciones, montos, fechas, códigos de formularios) se cargan **una sola vez** y se insertan en los textos con *marcadores*.

---

## 1. Idea general: tres lugares, tres tareas

| Qué querés cambiar | Dónde | Menú del panel |
|---|---|---|
| Un valor que aparece en varios textos (resolución, monto del módulo, fecha límite, código de un formulario) | Se cambia una vez y se actualiza en todas las páginas | **Datos del Distrito → Matrícula y trámites** |
| El texto de una página de trámite (requisitos, avisos, costos, bajada) | Formulario de cada trámite | **Trámites** |
| Un formulario o documento descargable (título, archivo) | Biblioteca de documentos | **Documentos (Normativa)** |
| Los **links de interés** de la última columna del pie de página | Un link por sitio: nombre, dirección y orden | **Links de interés** |

---

## 2. Datos del Distrito y marcadores

### 2.1 Cambiar un valor

1. Panel de administración → **Datos del Distrito**.
2. Buscá la sección **Matrícula y trámites** y modificá el campo.
3. **Guardar cambios**. Se actualizan solas todas las páginas que usan ese dato.

Cómo se completan:

- **Montos:** solo números, sin puntos ni signo `$`. Ejemplo: `395000`. En la página se muestra como `$ 395.000`.
- **Fechas:** con el selector de calendario. En la página se muestran como `31/03/2026`.
- **Textos** (resolución, códigos de formulario): tal como deben leerse. Ejemplo: `1489/24`, `I-2024`.

### 2.2 Marcadores disponibles

Un **marcador** es el nombre del dato entre dos llaves. Donde lo escribas se reemplaza por el valor vigente.

| Marcador | Qué es | Valor actual (ejemplo) | Se muestra como |
|---|---|---|---|
| `{{resolucion}}` | Resolución vigente | 1489/24 | 1489/24 |
| `{{modulo_1}}` | Módulo por incumplimiento, hasta la fecha límite | 395000 | $ 395.000 |
| `{{modulo_fecha}}` | Fecha límite de ese valor | 2026-03-31 | 31/03/2026 |
| `{{modulo_2}}` | Módulo por incumplimiento, vencida la fecha límite | 564000 | $ 564.000 |
| `{{form_inscripcion}}` | Formulario de inscripción | I-2024 | I-2024 |
| `{{form_registros}}` | Formulario de registros especiales | I.R.-2026 | I.R.-2026 |
| `{{form_rehabilitacion}}` | Formulario de rehabilitación | R-2024 | R-2024 |
| `{{form_baja}}` | Formulario de baja | B-2024 | B-2024 |
| `{{form_baja_fallecimiento}}` | Formulario de baja por fallecimiento | BF-2024 | BF-2024 |
| `{{form_credencial}}` | Formulario de credenciales | CRE-2024 | CRE-2024 |
| `{{email}}`, `{{telefono}}`, `{{horario}}` | Contacto general del Distrito | — | Tal cual |
| `{{periodo_autoridades}}` | Período del Consejo Directivo | 2024 – 2027 | Tal cual |

Al editar un trámite, la cajita lateral **"Datos que podés insertar en los textos"** muestra esta misma lista con los valores actuales, para copiar y pegar.

### 2.3 Cómo se usan en un texto

Se escriben tal cual, con las dos llaves, en cualquier texto del trámite (bajada, costos, títulos, requisitos, avisos) e incluso en el **título de un documento**.

> Además de lo normado por la Resolución `{{resolucion}}` se debe presentar documentación… el módulo por incumplimiento vale `{{modulo_1}}` por cada año hasta el `{{modulo_fecha}}`; vencido ese plazo, `{{modulo_2}}` por cada año.

Se ve en la página:

> Además de lo normado por la Resolución 1489/24 se debe presentar documentación… el módulo por incumplimiento vale $ 395.000 por cada año hasta el 31/03/2026; vencido ese plazo, $ 564.000 por cada año.

Reglas:

- Van **con dos llaves de cada lado** y sin tildes ni mayúsculas: `{{form_baja}}`.
- Se permiten espacios adentro: `{{ form_baja }}` funciona igual.
- Si escribís un marcador que **no existe** (por ejemplo `{{resolucion1}}`), la página lo muestra tal cual, con las llaves. Es la señal de que hay un error de tipeo.

---

## 3. Editar un trámite

Panel → **Trámites** → elegí el trámite → **Editar**. El **título** es el nombre de la página. Debajo hay un formulario, **Datos del trámite**.

### 3.1 Campos del encabezado

| Campo | Para qué sirve |
|---|---|
| **Ícono** | El ícono con el que aparece el trámite en el bloque "Otros trámites". |
| **Etiqueta superior** | El texto chico en verde sobre el título (por ejemplo, "Matrícula profesional"). |
| **Nombre corto (opcional)** | Solo si el título es largo: se usa en la ruta de navegación (Inicio › Trámites › …). |
| **Bajada** | Texto que va bajo el título. |

### 3.2 Cuadro lateral (costos y condiciones)

El recuadro oscuro de la derecha. Se completa con un **título** y hasta **4 filas**, cada una con su **etiqueta** (chica, en mayúsculas) y su **texto**. Una fila vacía no se muestra.

### 3.3 Contenido de la página (bloques)

Hay **6 bloques**, que se muestran **en el orden en que están cargados**. Cada uno tiene:

- **Tipo:**
  - *Lista de requisitos*: tarjetas numeradas.
  - *Aviso (verde)*: recuadro informativo.
  - *Aviso importante (ámbar)*: recuadro de advertencia.
  - *— No usar este bloque —*: se ignora.
- **Título** del bloque.
- **Texto introductorio** (opcional, solo para listas de requisitos).
- **Contenido**, con un editor parecido a Word.

**Listas de requisitos.** En el editor usá una **lista numerada** y, en cada ítem, poné el nombre en **negrita** y el detalle a continuación:

1. **Formulario `{{form_inscripcion}}` completo** — o sus posteriores actualizaciones.
2. **Documento de identidad (DNI)** — copia escaneada. El original se solicita al retirar la credencial.

La numeración y el diseño de tarjeta los pone el sitio solo. Un ítem sin detalle (solo el nombre en negrita) también es válido.

**Avisos.** El contenido son uno o más párrafos. Se pueden usar enlaces con el botón de la cadena del editor (por ejemplo, un correo o un sitio).

### 3.4 Formularios y documentación

- **Título de la sección** y **texto introductorio** (por defecto: "Formularios y documentación" y la recomendación de usar Adobe Acrobat Reader).
- **Documento 1 a 8:** desplegables para elegir de la biblioteca de documentos. Se muestran en ese orden; los que quedan vacíos no aparecen.

### 3.5 Guardar y ver

- **Actualizar** guarda los cambios.
- **Vista previa** o **Ver entrada** muestra cómo queda la página.
- Los trámites se ordenan en "Otros trámites" según **Atributos de entrada → Orden** (menor número, primero).

---

## 4. Documentos descargables

Panel → **Documentos (Normativa)**. Cada documento se carga **una sola vez** y se puede usar en todos los trámites que lo necesiten.

Por cada documento:

- **Título:** el nombre que se muestra. Admite marcadores. Ejemplo: `{{form_baja}} Formulario de baja` se ve como *B-2024 Formulario de baja*.
- **Archivo:** se sube desde la biblioteca de medios (PDF, Word, Excel…). El **tipo** (PDF, DOCX…) y el **peso** se leen solos del archivo; no hay que escribirlos.
- **Enlace externo (opcional):** solo si el archivo **no** está en este sitio. Pegá la dirección completa. Si además subís un archivo, el enlace externo se ignora.

**Actualizar un formulario nuevo:** en el documento, subí el archivo nuevo en lugar del anterior y guardá. Todas las páginas que lo usan quedan actualizadas.

**Usar un documento en un trámite:** editá el trámite → sección *Formularios y documentación* → elegí el documento en el primer desplegable libre → **Actualizar**.

---

## 5. Página Medios de pago

Es un trámite más (**Trámites → Medios de pago**, dirección `/tramites/pago-matricula/`), pero con su propio diseño: cuatro tarjetas (transferencia bancaria, débito automático, Red Link y Pagomiscuentas) y tres ventanas emergentes. Su formulario de edición es distinto al de los otros trámites, y se llama **Datos de la página de pago**.

### 5.1 Datos bancarios (Modalidad 1)

Se cargan en **Datos del Distrito → Datos bancarios (transferencia)**: cuenta corriente, sucursal, titular, C.B.U., alias y C.U.I.T. Se muestran en la ventana **"Datos de la cuenta"**.

- El botón **Copiar** del C.B.U. copia **solo los números** (sin espacios ni guiones); el del alias copia el alias tal cual.
- Si dejás un dato vacío, esa fila desaparece de la ventana.
- Los mismos datos se pueden usar en cualquier texto con los marcadores `{{banco_cta_cte}}`, `{{banco_sucursal}}`, `{{banco_titular}}`, `{{banco_cbu}}`, `{{banco_alias}}` y `{{banco_cuit}}`.

### 5.2 Documentos de descarga (Modalidades 2 y 4)

1. En **Documentos (Normativa)** se sube cada archivo una sola vez (por ejemplo, el formulario de adhesión al débito automático en `.xlsx` y el instructivo de Pagomiscuentas en `.pdf`).
2. En **Trámites → Medios de pago**, dentro de *Modalidad 2* y *Modalidad 4*, se elige el documento en el desplegable.
3. **Modalidad 2:** el botón de la tarjeta descarga ese archivo. **Modalidad 4:** el archivo aparece como enlace "Descargar instructivo" al pie de la ventana del instructivo.

Para reemplazar el formulario por uno nuevo, subí el archivo nuevo al documento existente (Documentos → editar → archivo) y guardá: la página queda actualizada sin tocar nada más.

### 5.3 Resto de los textos

En **Datos de la página de pago** se editan el título y la bajada, la sección "Antes de pagar" (texto y lista "tené a mano"), y para cada modalidad: título, descripción, pasos (**uno por línea**; los correos y enlaces se vuelven clickeables) y texto del botón. También el texto al pie de los datos bancarios, la dirección de la consulta de Red Link y el mensaje final. Todos admiten marcadores.

> **Ventana de Red Link:** muestra una página del Consejo Superior que se sirve sin conexión segura (`http`). En un sitio con `https` los navegadores pueden bloquearla; la ventana avisa al usuario y ofrece abrirla en una pestaña nueva.
>
> **Ventana del instructivo de Pagomiscuentas:** por ahora su contenido (tabla de datos y pasos por internet y cajeros) es fijo en el tema y no se edita desde el panel.

---

## 6. Eventos y novedades

Todas las publicaciones (jornadas, cursos, asambleas, beneficios, avisos) son **entradas** del panel (**Entradas → Añadir nueva**) y se muestran juntas en **`/novedades/`**, con filtros por categoría y buscador. Cada una tiene su propia página de detalle.

### 6.1 Cargar una publicación

1. **Entradas → Añadir nueva**.
2. **Título** y **contenido**: el contenido se escribe con el editor de bloques (párrafos, subtítulos con el bloque *Encabezado* y listas con el bloque *Lista*; las listas se ven con tildes verdes).
3. **Imagen destacada** (panel lateral): una sola imagen sirve para la tarjeta y para la portada de la página. Se recorta sola; conviene una horizontal de al menos 1680 × 720.
4. **Categoría** (panel lateral): elegí **una**. Define el color de la etiqueta y el filtro.
5. **Fecha de publicación** (panel lateral → *Publicar*): ordena el listado y es la fecha que se muestra en las tarjetas. Se puede **programar** una fecha futura.
6. En **Datos de la publicación** (debajo del editor) completá:

| Campo | Para qué sirve |
|---|---|
| **Tipo de publicación** | **Noticia** o **Evento o novedad**. Define en qué sección de la página de inicio aparece: *Noticias* (las 5 más recientes) o *Eventos y novedades* (la agenda: las actividades ya realizadas se ocultan solas). Si no se elige, cuenta como Noticia. |
| **Bajada** | Resumen corto: va en las tarjetas y bajo el título. |
| **Texto de fecha alternativo** | Opcional. Reemplaza a la fecha de publicación (ej.: *Vigente todo 2026*). |
| **Destacada** | Se muestra como tarjeta grande arriba del listado. Si hay varias, la más reciente. |
| **Botón de acción** | Texto y **enlace** (formulario de inscripción, otra página, un archivo…), o un **documento de la biblioteca**. Sin enlace, el botón lleva a Contacto. |
| **Es una actividad con fecha** | Tildalo para eventos: aparece la caja *Datos de la actividad*. |

> **Cómo se reparten en la página de inicio.** *Eventos y novedades* muestra hasta 3 publicaciones de tipo **Evento o novedad**, ordenadas por fecha (la de la actividad o, si no es una actividad, la de publicación). *Noticias* muestra las 5 más recientes de tipo **Noticia**. El listado completo de `/novedades/` incluye ambos tipos. En **Entradas** hay una columna **Tipo** y un filtro para verlos por separado.

### 6.2 Actividades (eventos)

Al tildar **Es una actividad con fecha** aparece **Datos de la actividad**: fecha de inicio (y de fin, si dura varios días), horario, lugar, modalidad (Presencial / Virtual / Mixta, con una aclaración opcional), cupo, arancel, y si la **inscripción está abierta** (con su fecha de cierre).

Con eso la página de la publicación muestra la **ficha "Datos de la actividad"** (la fecha se escribe sola: *Jueves 9 de abril de 2026* o *Del 6 al 27 de mayo de 2026*) y el recuadro **Inscripción** con el botón. Si la inscripción no está abierta, el recuadro dice *Más información*.

### 6.3 Categorías y colores

**Entradas → Categorías**: cada categoría tiene un **Color** (violeta, verde, verde oscuro o ladrillo) y un **Orden** (posición en los filtros; menor, primero). Las categorías sin publicaciones no aparecen en los filtros.

### 6.4 Lo que se arma solo

Los botones de compartir (WhatsApp, LinkedIn, Facebook, correo, copiar enlace), "Otras novedades" (3, de la misma categoría primero), la ruta de navegación, el contador y el filtrado del listado. El correo de contacto del recuadro sale de **Datos del Distrito**.

---

## 7. Honorarios mínimos

La página **`/honorarios/`** muestra la **resolución vigente** con sus dos documentos y una lista de **resoluciones anteriores**. Cada resolución del Consejo Superior es un ítem de **Resoluciones (Honorarios)** en el panel.

### 7.1 Cargar una resolución

**Resoluciones (Honorarios) → Agregar resolución.** No hay campo de título: se arma solo con el código (*1553/2026* → **"Resolución CS 1553/2026"**).

| Campo | Para qué sirve |
|---|---|
| **Código** | Número y año (ej.: `1553/2026`). |
| **Fecha de publicación** | Se muestra como *"Publicada el 27 de febrero de 2026"* (en la vigente) o *"Publicada 27/02/2026"* (en las anteriores). |
| **Vigencia desde** | En la vigente, el recuadro grande *"Vigente desde"*. |
| **Vigencia hasta** *(opcional)* | En las anteriores con rango, se muestra *"Vigente 01/10/2025 – 31/03/2026"*. |
| **Es la resolución vigente** | La que se muestra arriba, con sus documentos. **Solo puede haber una**: al tildarla, la que estaba vigente se destilda sola. |
| **Descripción** | En la vigente, el resumen bajo el título; en las anteriores, la nota de la lista. |
| **Resolución (documento)** y **Anexos (documento)** | Los dos archivos de la vigente, **subidos a este sitio** (botón *Add Media*). El tipo (PDF) y el peso se leen solos. |
| **Enlace de descarga (Consejo Superior)** | Para las anteriores: dirección donde el Consejo Superior publica la resolución. |

### 7.2 Cómo se ordena y cuánto se muestra

- Las anteriores se listan de la **más nueva a la más vieja** (por fecha de publicación o, si no la tiene, por inicio de vigencia).
- Se muestran las **6 más recientes**. Ese número se cambia en **Datos del Distrito → Página de honorarios mínimos → Resoluciones anteriores a mostrar** (0 = todas).
- El enlace del aviso *"listado de honorarios del Consejo Superior"* también se edita ahí.

- La **barra de aviso de la página de inicio** ("Honorarios mínimos vigentes desde 01/04/2026 — Res. 1553", con enlace a esta página) se arma sola con la resolución vigente: toma su **código** y su **vigencia desde**. Si no hay ninguna vigente, la barra no se muestra.
---

## 8. Tareas frecuentes

### Salió una nueva resolución de honorarios
1. **Resoluciones (Honorarios) → Agregar resolución**: cargá el código, la fecha de publicación, la vigencia desde, la descripción y subí los dos documentos (resolución y anexos). Tildá **Es la resolución vigente**.
2. La resolución que estaba vigente pasa sola a *Resoluciones anteriores*. Abrila y cargale el **Enlace de descarga (Consejo Superior)** (y, si tiene, la *Vigencia hasta*).
3. La **barra de aviso de la home** ("Honorarios mínimos vigentes desde … — Res. …") se actualiza sola con la nueva vigente: no hay nada más que cargar.

### Agregar, cambiar o quitar un link de interés del pie de página
Panel → **Links de interés** (menú lateral). Es la lista de la última columna del pie de todo el sitio.

- **Agregar:** **Agregar link de interés** → escribí el **nombre del sitio** (el título), la **dirección completa** (con `https://`) y, si querés, el **orden** (Atributos → Orden; menor número, primero) → **Publicar**.
- **Cambiar:** abrí el link, corregí el nombre o la dirección y **Actualizar**.
- **Quitar:** mandalo a la papelera, o pasalo a **Borrador** si querés conservarlo sin mostrarlo.
- Todos se abren en una **pestaña nueva** y muestran el ícono de enlace externo; no hay nada más que configurar. Un link sin dirección no se muestra.

### Cambió la resolución de matriculación
1. **Datos del Distrito** → *Resolución vigente* → escribí la nueva (ej.: `1600/26`) → **Guardar cambios**.
2. Listo: se actualiza en todas las páginas que usan `{{resolucion}}`.

### Cambiaron los montos del módulo o su fecha límite
**Datos del Distrito → Matrícula y trámites:** modificá *Módulo…*, *Fecha límite* y/o el segundo monto → **Guardar cambios**.

### Empieza un año nuevo con formularios nuevos (ej.: B-2025)
1. **Datos del Distrito** → cambiá el código del formulario (*Formulario de baja*: `B-2025`).
2. En **Documentos**, subí el PDF nuevo al documento correspondiente.
3. No hace falta tocar los textos de los trámites: usan el marcador.

### Cambió la cuenta bancaria (C.B.U., alias, etc.)
**Datos del Distrito → Datos bancarios (transferencia):** modificá el dato → **Guardar cambios**. La ventana "Datos de la cuenta" de Medios de pago se actualiza sola.

### Cambió el formulario de débito automático (o el instructivo de Pagomiscuentas)
**Documentos (Normativa)** → abrí el documento → subí el archivo nuevo → **Actualizar**. Medios de pago usa siempre el archivo vigente de ese documento.

### Agregar o quitar un requisito
Editá el trámite → bloque de la lista → agregá o borrá un ítem de la lista numerada → **Actualizar**.

### Crear un trámite nuevo
1. **Trámites → Agregar trámite**.
2. Escribí el **título** y completá **Datos del trámite**.
3. **Publicar**. Aparece solo en el bloque "Otros trámites" de los demás. Para sumarlo al menú, agregalo en **Apariencia → Menús**, apuntando a su dirección (`/tramites/nombre-del-tramite/`).

---

## 9. Preguntas y problemas comunes

**Veo `{{algo}}` en la página con las llaves.**
El marcador está mal escrito o no existe. Compará con la lista de la sección 2.2 o con la cajita lateral del trámite.

**Cambié un valor en Datos del Distrito y no se actualizó.**
Probá recargar la página con `Ctrl + F5`. Si el sitio usa una caché, hay que vaciarla.

**Un documento no aparece en la lista de un trámite.**
Está sin **Publicar**, o no tiene ni archivo ni enlace externo cargado.

**Un documento muestra "ENLACE" como tipo.**
Tiene un enlace externo cuya dirección no termina en `.pdf`, `.docx`, etc. Subí el archivo al sitio para que el tipo se detecte solo.

**Necesito más de 6 bloques, 4 filas de costos u 8 documentos.**
Es un límite del formulario, ajustable por quien mantiene el tema (`CIPBA_TRAMITE_BLOQUES`, `CIPBA_TRAMITE_COSTOS` y `CIPBA_TRAMITE_DOCS` en `inc/tramites.php`).

**¿Se puede romper el diseño escribiendo mal?**
No. El sitio arma el diseño; quien edita solo carga texto y listas. El contenido que se pega se limpia de código no permitido.

---

## 10. Notas técnicas (para quien mantiene el tema)

- Plantilla: `single-tramite.php`. Lógica: `inc/tramites.php`. Formulario de campos: `inc/meta-boxes.php` (caja "Datos del trámite").
- Datos y marcadores: `inc/settings.php` (`cipba_datos_fields()`, `cipba_tokens()`, `cipba_dato_display()`). Para sumar un dato nuevo (y su marcador) alcanza con agregar una entrada en `cipba_datos_fields()`.
- Los documentos se leen con `cipba_get_documento_file_meta()` (`inc/helpers.php`): archivo subido primero, enlace externo como respaldo.
- Estilos: al final de `style.css`, bloque "Trámites (single-tramite.php)".
- Al guardar un trámite, `cipba_tramite_layout_defaults()` fija el layout de Astra (ancho completo, sin barra lateral, sin título automático), aunque Astra guarde su valor "default".
- Medios de pago: plantilla `single-tramite-pago-matricula.php` (WordPress la elige por el nombre `single-{tipo}-{slug}.php`), script `assets/js/pago-matricula.js` (ventanas y botón Copiar) y estilos en `style.css` (bloque "Medios de pago"). Los datos bancarios son campos de `cipba_datos_fields()` (`banco_*`).
- Formulario de edición de esa página: caja "Datos de la página de pago" en `inc/meta-boxes.php`. Cada trámite muestra **solo** su caja (común o de pago): la decide `cipba_admin_editando_pago()` (`inc/tramites.php`), porque las dos comparten nombres de campo y, si aparecieran juntas, una pisaría a la otra al guardar.
- Eventos y novedades: entradas nativas (`single.php`, listado `[cipba_novedades]` en `template-parts/novedades-list.php`, lógica y colores de categoría en `inc/novedades.php`, filtros en `assets/js/novedades.js`). Los campos están en `inc/meta-boxes.php` (cajas *Datos de la publicación* y *Datos de la actividad*; esta última se muestra u oculta con `assets/js/admin-novedad.js`). El tipo de contenido anterior `evento` se retiró y sus eventos se migraron a entradas.
- Honorarios mínimos: lógica en `inc/honorarios.php` (título automático, una sola vigente, listados), página en `template-parts/honorarios.php` ([cipba_honorarios]), campos en `inc/meta-boxes.php` (caja *Datos de la resolución*). El tipo `resolucion` no tiene título ni editor.
- Links de interés del pie: tipo de contenido `link_interes` (título + campo `url`; orden por `menu_order`), leído por `cipba_get_links_interes()` en `inc/footer.php`. Reemplaza al menú "Pie - Links de interés" (las otras 3 columnas del pie siguen siendo menús).
