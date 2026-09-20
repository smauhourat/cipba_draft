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
| `{{honorarios_desde}}`, `{{honorarios_resolucion}}` | Datos del aviso de honorarios | — | Tal cual |
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

## 5. Tareas frecuentes

### Cambió la resolución de matriculación
1. **Datos del Distrito** → *Resolución vigente* → escribí la nueva (ej.: `1600/26`) → **Guardar cambios**.
2. Listo: se actualiza en todas las páginas que usan `{{resolucion}}`.

### Cambiaron los montos del módulo o su fecha límite
**Datos del Distrito → Matrícula y trámites:** modificá *Módulo…*, *Fecha límite* y/o el segundo monto → **Guardar cambios**.

### Empieza un año nuevo con formularios nuevos (ej.: B-2025)
1. **Datos del Distrito** → cambiá el código del formulario (*Formulario de baja*: `B-2025`).
2. En **Documentos**, subí el PDF nuevo al documento correspondiente.
3. No hace falta tocar los textos de los trámites: usan el marcador.

### Agregar o quitar un requisito
Editá el trámite → bloque de la lista → agregá o borrá un ítem de la lista numerada → **Actualizar**.

### Crear un trámite nuevo
1. **Trámites → Agregar trámite**.
2. Escribí el **título** y completá **Datos del trámite**.
3. **Publicar**. Aparece solo en el bloque "Otros trámites" de los demás. Para sumarlo al menú, agregalo en **Apariencia → Menús**, apuntando a su dirección (`/tramites/nombre-del-tramite/`).

---

## 6. Preguntas y problemas comunes

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

## 7. Notas técnicas (para quien mantiene el tema)

- Plantilla: `single-tramite.php`. Lógica: `inc/tramites.php`. Formulario de campos: `inc/meta-boxes.php` (caja "Datos del trámite").
- Datos y marcadores: `inc/settings.php` (`cipba_datos_fields()`, `cipba_tokens()`, `cipba_dato_display()`). Para sumar un dato nuevo (y su marcador) alcanza con agregar una entrada en `cipba_datos_fields()`.
- Los documentos se leen con `cipba_get_documento_file_meta()` (`inc/helpers.php`): archivo subido primero, enlace externo como respaldo.
- Estilos: al final de `style.css`, bloque "Trámites (single-tramite.php)".
- Al guardar un trámite, `cipba_tramite_layout_defaults()` fija el layout de Astra (ancho completo, sin barra lateral, sin título automático), aunque Astra guarde su valor "default".
