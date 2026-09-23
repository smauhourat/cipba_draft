<?php
/**
 * Campos personalizados (Meta Box) para los CPT del sitio.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cipba_register_meta_boxes( $meta_boxes ) {

	// Eventos y novedades: entradas nativas con dos cajas de campos. El título, el
	// texto, la imagen destacada, la categoría y la fecha de publicación son los
	// campos nativos de WordPress.
	$meta_boxes[] = array(
		'title'      => 'Datos de la publicación',
		'post_types' => 'post',
		'fields'     => array(
			array(
				'name'    => 'Tipo de publicación',
				'id'      => 'tipo_pub',
				'desc'    => 'Define en qué sección de la página de inicio aparece. <strong>Evento o novedad</strong>: sección "Eventos y novedades" (agenda; las actividades ya realizadas se ocultan solas). <strong>Noticia</strong>: sección "Noticias" (las más recientes).',
				'type'    => 'select',
				'options' => array(
					'noticia' => 'Noticia',
					'evento'  => 'Evento o novedad',
				),
				'std'     => 'noticia',
			),
			array(
				'name' => 'Bajada',
				'id'   => 'bajada',
				'desc' => 'Resumen corto: se muestra en las tarjetas y bajo el título de la publicación.',
				'type' => 'textarea',
				'rows' => 3,
			),
			array(
				'name'        => 'Texto de fecha alternativo',
				'id'          => 'fecha_alt',
				'desc'        => 'Opcional. Reemplaza a la fecha de publicación en las tarjetas y el encabezado. Ej: "Vigente todo 2026".',
				'type'        => 'text',
				'placeholder' => 'Vigente todo 2026',
			),
			array(
				'name' => 'Destacada',
				'id'   => 'destacada',
				'desc' => 'Se muestra como tarjeta grande arriba del listado de Novedades (si hay varias tildadas, la más reciente).',
				'type' => 'checkbox',
			),
			array(
				'type' => 'heading',
				'name' => 'Botón de acción (opcional)',
			),
			array(
				'name'        => 'Texto del botón',
				'id'          => 'cta_texto',
				'type'        => 'text',
				'columns'     => 4,
				'placeholder' => 'Inscribirme a la jornada',
			),
			array(
				'name'        => 'Enlace',
				'id'          => 'cta_url',
				'desc'        => 'Dirección completa (formulario de inscripción, otra página, un archivo…).',
				'type'        => 'url',
				'columns'     => 8,
				'placeholder' => 'https://…',
			),
			array(
				'name'        => 'O un documento de la biblioteca',
				'id'          => 'cta_doc',
				'desc'        => 'Se usa solo si el enlace está vacío. Sin enlace ni documento, el botón dice "Escribinos" y lleva a Contacto.',
				'type'        => 'post',
				'post_type'   => 'documento',
				'field_type'  => 'select_advanced',
				'placeholder' => '— Ninguno —',
				'query_args'  => array( 'post_status' => 'publish', 'posts_per_page' => -1 ),
			),
			array(
				'type' => 'heading',
				'name' => 'Tipo de publicación',
			),
			array(
				'name' => 'Es una actividad con fecha',
				'id'   => 'es_actividad',
				'desc' => 'Tildalo para jornadas, cursos, asambleas y demás eventos: aparece la caja "Datos de la actividad" y la ficha en la publicación.',
				'type' => 'checkbox',
			),
		),
	);

	$meta_boxes[] = array(
		'title'      => 'Datos de la actividad',
		'post_types' => 'post',
		'fields'     => array(
			array(
				'name'    => 'Fecha de inicio',
				'id'      => 'fecha_inicio',
				'type'    => 'date',
				'columns' => 6,
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
			array(
				'name'    => 'Fecha de fin (opcional)',
				'id'      => 'fecha_fin',
				'desc'    => 'Solo para actividades de varios días.',
				'type'    => 'date',
				'columns' => 6,
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
			array(
				'name'        => 'Horario',
				'id'          => 'horario',
				'type'        => 'text',
				'columns'     => 6,
				'placeholder' => '18:00 a 21:00 h',
			),
			array(
				'name'        => 'Lugar',
				'id'          => 'lugar',
				'type'        => 'text',
				'columns'     => 6,
				'placeholder' => 'Sede San Justo — Salón de actos',
			),
			array(
				'name'        => 'Modalidad',
				'id'          => 'modalidad',
				'type'        => 'select',
				'columns'     => 4,
				'placeholder' => '— Elegir —',
				'options'     => array(
					'Presencial' => 'Presencial',
					'Virtual'    => 'Virtual',
					'Mixta'      => 'Mixta',
				),
			),
			array(
				'name'        => 'Aclaración de la modalidad',
				'id'          => 'modalidad_nota',
				'desc'        => 'Opcional. Ej: "con transmisión en vivo".',
				'type'        => 'text',
				'columns'     => 8,
				'placeholder' => 'con transmisión en vivo',
			),
			array(
				'name'        => 'Cupo',
				'id'          => 'cupo',
				'type'        => 'text',
				'columns'     => 6,
				'placeholder' => '80 matriculados',
			),
			array(
				'name'        => 'Arancel',
				'id'          => 'arancel',
				'type'        => 'text',
				'columns'     => 6,
				'placeholder' => 'Sin cargo para matriculados al día',
			),
			array(
				'name'    => 'Inscripción abierta',
				'id'      => 'inscripcion_abierta',
				'desc'    => 'Muestra "Inscripción" en la ficha y en el recuadro lateral.',
				'type'    => 'checkbox',
				'columns' => 6,
			),
			array(
				'name'    => 'Cierre de inscripción',
				'id'      => 'cierre_inscripcion',
				'type'    => 'date',
				'columns' => 6,
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
		),
	);

	// Documento — Normativa. "formato" y "peso" NO son campos: se calculan
	// del archivo subido (ver cipba_get_documento_file_meta en helpers.php),
	// tal como pide el handoff.
	$meta_boxes[] = array(
		'title'      => 'Archivo del documento',
		'post_types' => 'documento',
		'fields'     => array(
			array(
				'name'     => 'Archivo (PDF, Word, Excel…)',
				'id'       => 'archivo',
				'desc'     => 'El tipo (PDF, DOCX…) y el peso se leen solos del archivo.',
				'type'     => 'file_advanced',
				'max_file_uploads' => 1,
				'mime_type' => 'application',
			),
			array(
				'name' => 'Enlace externo (opcional)',
				'id'   => 'enlace_externo',
				'desc' => 'Solo si el archivo NO está en este sitio: pegá la dirección completa. Si subiste un archivo arriba, este campo se ignora.',
				'type' => 'url',
			),
			array(
				'name' => 'Origen',
				'id'   => 'origen',
				'desc' => 'Ej: "Consejo Superior" si el documento se aloja externamente, o "Distrito VII" si es propio.',
				'type' => 'text',
			),
			array(
				'name' => 'Orden',
				'id'   => 'orden',
				'desc' => 'Orden manual de aparición en el listado (menor primero).',
				'type' => 'number',
				'std'  => 0,
			),
		),
	);

	// Resolución de honorarios mínimos (página /honorarios/). El título se arma solo
	// a partir del código ("Resolución CS 1553/2026"). La vigente lleva los dos
	// documentos subidos al sitio; las anteriores, un enlace al Consejo Superior.
	$meta_boxes[] = array(
		'title'      => 'Datos de la resolución',
		'post_types' => 'resolucion',
		'fields'     => array(
			array(
				'name'        => 'Código',
				'id'          => 'numero',
				'desc'        => 'Número y año de la resolución. El título se arma solo: "Resolución CS 1553/2026".',
				'type'        => 'text',
				'columns'     => 4,
				'placeholder' => '1553/2026',
			),
			array(
				'name'    => 'Fecha de publicación',
				'id'      => 'fecha_publicacion',
				'type'    => 'date',
				'columns' => 4,
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
			array(
				'name'    => 'Vigencia desde',
				'id'      => 'vigencia_desde',
				'type'    => 'date',
				'columns' => 4,
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
			array(
				'name'    => 'Vigencia hasta (opcional)',
				'id'      => 'vigencia_hasta',
				'desc'    => 'Solo en resoluciones anteriores con rango de vigencia: se muestra "Vigente 01/10/2025 – 31/03/2026".',
				'type'    => 'date',
				'columns' => 4,
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
			array(
				'name' => 'Es la resolución vigente',
				'id'   => 'vigente',
				'desc' => 'Se muestra arriba en la página de Honorarios, con sus documentos. Solo puede haber una: al tildarla, las demás se destildan solas.',
				'type' => 'checkbox',
			),
			array(
				'name' => 'Descripción',
				'id'   => 'descripcion',
				'desc' => 'En la vigente: resumen bajo el título. En las anteriores: la nota de la lista (ej: "Tabla general con Anexos I, II y III").',
				'type' => 'textarea',
				'rows' => 3,
			),
			array(
				'type' => 'heading',
				'name' => 'Documentos de la resolución vigente (se suben a este sitio)',
			),
			array(
				'name'             => 'Resolución (documento)',
				'id'               => 'archivo_resolucion',
				'type'             => 'file_advanced',
				'max_file_uploads' => 1,
				'mime_type'        => 'application',
			),
			array(
				'name'             => 'Anexos (documento)',
				'id'               => 'archivo_anexos',
				'type'             => 'file_advanced',
				'max_file_uploads' => 1,
				'mime_type'        => 'application',
			),
			array(
				'type' => 'heading',
				'name' => 'Resoluciones anteriores',
			),
			array(
				'name'        => 'Enlace de descarga (Consejo Superior)',
				'id'          => 'enlace_externo',
				'desc'        => 'Dirección completa donde el Consejo Superior publica esta resolución.',
				'type'        => 'url',
				'placeholder' => 'http://www.colegioingenieros.org.ar/resolucion-1543-y-sus-anexos/',
			),
		),
	);

	// Subcomisión: formulario plano (sin repeater — el grupo clonable
	// requiere una extensión de pago). Hasta CIPBA_SUBCOM_MAX_REFS referentes;
	// los que se dejan vacíos no se muestran en el sitio.
	$subcom_fields = array(
		array(
			'name'        => 'Sigla',
			'id'          => 'tag',
			'desc'        => 'Abreviatura corta que se muestra en el recuadro de la tarjeta. Ej: HyS, Elec, Civ.',
			'type'        => 'text',
			'size'        => 10,
			'placeholder' => 'Ej: Civ',
		),
		array(
			'name'        => 'Email de la subcomisión',
			'id'          => 'mail',
			'desc'        => 'Correo institucional de la subcomisión. Se muestra bajo el nombre en el listado y en la tarjeta de la home.',
			'type'        => 'email',
			'placeholder' => 'civil@cipba.org',
		),
		array(
			'name' => 'Descripción',
			'id'   => 'descripcion',
			'desc' => 'Opcional. Texto corto que se muestra solo en la tarjeta de la home; en el listado de Subcomisiones no aparece.',
			'type' => 'textarea',
			'rows' => 3,
		),
	);
	for ( $i = 1; $i <= CIPBA_SUBCOM_MAX_REFS; $i++ ) {
		$subcom_fields[] = array(
			'type' => 'heading',
			'name' => 1 === $i ? 'Referente 1 (principal)' : "Referente $i (opcional)",
		);
		$subcom_fields[] = array(
			'name'        => 'Nombre y apellido',
			'id'          => "ref{$i}_nombre",
			'type'        => 'text',
			'columns'     => 4,
			'placeholder' => 'Ej: Maria Claudia FILIPUZZI',
		);
		$subcom_fields[] = array(
			'name'        => 'Matrícula',
			'id'          => "ref{$i}_matricula",
			'type'        => 'text',
			'columns'     => 2,
			'placeholder' => '53.929',
		);
		$subcom_fields[] = array(
			'name'        => 'Teléfono',
			'id'          => "ref{$i}_telefono",
			'type'        => 'text',
			'columns'     => 3,
			'placeholder' => '(11) 5857-0060',
		);
		$subcom_fields[] = array(
			'name'        => 'Email',
			'id'          => "ref{$i}_mail",
			'type'        => 'email',
			'columns'     => 3,
			'placeholder' => 'nombre@cipba.org',
		);
	}

	$meta_boxes[] = array(
		'title'      => 'Datos de la subcomisión',
		'post_types' => 'subcomision',
		'fields'     => $subcom_fields,
	);

	// Sede o delegación: formulario plano (mismo criterio que Subcomisión).
	// Los "contactos directos" solo se muestran en la sede marcada como
	// Casa Central; los slots vacíos se ignoran.
	$sede_fields = array(
		array(
			'name'        => 'Tipo',
			'id'          => 'tag',
			'desc'        => 'Etiqueta que va arriba del nombre. Ej: Sede Central, Delegación Haedo.',
			'type'        => 'text',
			'placeholder' => 'Ej: Delegación Haedo',
		),
		array(
			'name' => 'Casa Central',
			'id'   => 'destacada',
			'desc' => 'Tildar solo en la sede principal: se muestra como tarjeta grande, con la lista de contactos directos.',
			'type' => 'checkbox',
		),
		array(
			'name'        => 'Dirección',
			'id'          => 'direccion',
			'type'        => 'text',
			'columns'     => 12,
			'placeholder' => 'Ej: Almafuerte N° 2868, San Justo (1754) – La Matanza – Bs As',
		),
		array(
			'name'        => 'Teléfono',
			'id'          => 'telefono',
			'type'        => 'text',
			'columns'     => 4,
			'placeholder' => '(011) 3535-0751',
		),
		array(
			'name'        => 'Email',
			'id'          => 'email',
			'type'        => 'email',
			'columns'     => 4,
			'placeholder' => 'info@cipba.org',
		),
		array(
			'name'        => 'Visador',
			'id'          => 'visador',
			'desc'        => 'Opcional.',
			'type'        => 'text',
			'columns'     => 4,
			'placeholder' => 'Ing. Civil Nombre (Mat. 12345)',
		),
		array(
			'name'        => 'Horario de atención',
			'id'          => 'horario',
			'type'        => 'text',
			'columns'     => 12,
			'placeholder' => 'Lunes a viernes de 9:00 a 16:00 hs',
		),
	);
	for ( $i = 1; $i <= CIPBA_SEDE_MAX_CONTACTOS; $i++ ) {
		$sede_fields[] = array(
			'type' => 'heading',
			'name' => "Contacto directo $i (solo Casa Central, opcional)",
		);
		$sede_fields[] = array(
			'name'        => 'Área',
			'id'          => "contacto{$i}_rol",
			'type'        => 'text',
			'columns'     => 2,
			'placeholder' => 'Secretaría',
		);
		$sede_fields[] = array(
			'name'        => 'Nombre',
			'id'          => "contacto{$i}_nombre",
			'type'        => 'text',
			'columns'     => 3,
			'placeholder' => 'Nombre y apellido',
		);
		$sede_fields[] = array(
			'name'        => 'Teléfono',
			'id'          => "contacto{$i}_tel",
			'type'        => 'text',
			'columns'     => 3,
			'placeholder' => '(011) 15-2713-3330',
		);
		$sede_fields[] = array(
			'name'        => 'Email',
			'id'          => "contacto{$i}_email",
			'type'        => 'email',
			'columns'     => 4,
			'placeholder' => 'secretaria@cipba.org',
		);
	}

	$meta_boxes[] = array(
		'title'      => 'Datos de la sede',
		'post_types' => 'sede',
		'fields'     => $sede_fields,
	);

	// Autoridad del Consejo Directivo: el nombre va en el título.
	$meta_boxes[] = array(
		'title'      => 'Datos de la autoridad',
		'post_types' => 'autoridad',
		'fields'     => array(
			array(
				'name'        => 'Cargo',
				'id'          => 'cargo',
				'desc'        => 'Ej: Presidente, Secretario, Vocal Titular 1°.',
				'type'        => 'text',
				'columns'     => 6,
				'placeholder' => 'Ej: Vocal Titular 1°',
			),
			array(
				'name'        => 'Título profesional',
				'id'          => 'titulo',
				'desc'        => 'Se muestra en cursiva debajo del nombre.',
				'type'        => 'text',
				'columns'     => 6,
				'placeholder' => 'Ej: Ing. Civil',
			),
			array(
				'name' => 'Presidencia',
				'id'   => 'destacado',
				'desc' => 'Tildar solo en el presidente: se muestra como tarjeta grande y destacada.',
				'type' => 'checkbox',
			),
		),
	);

	// Área de contacto (página Contacto): el nombre del área va en el título.
	$area_fields = array(
		array(
			'name'    => 'Ícono',
			'id'      => 'icono',
			'desc'    => 'Ícono que acompaña al nombre del área.',
			'type'    => 'select',
			'options' => array(
				'users'    => 'Personas',
				'building' => 'Edificio',
				'file'     => 'Documento',
			),
			'std'     => 'users',
		),
		array(
			'name'        => 'Correo del área',
			'id'          => 'email',
			'desc'        => 'Se muestra al pie de la tarjeta.',
			'type'        => 'email',
			'placeholder' => 'administracion@cipba.org',
		),
	);
	for ( $i = 1; $i <= CIPBA_AREA_MAX_PERSONAS; $i++ ) {
		$area_fields[] = array(
			'type' => 'heading',
			'name' => 1 === $i ? 'Persona 1' : "Persona $i (opcional)",
		);
		$area_fields[] = array(
			'name'        => 'Nombre',
			'id'          => "persona{$i}_nombre",
			'type'        => 'text',
			'columns'     => 6,
			'placeholder' => 'Ej: Rolando Menna',
		);
		$area_fields[] = array(
			'name'        => 'Teléfono',
			'id'          => "persona{$i}_tel",
			'type'        => 'text',
			'columns'     => 6,
			'placeholder' => '(011) 15-5857-0060',
		);
	}

	$meta_boxes[] = array(
		'title'      => 'Datos del área',
		'post_types' => 'area_contacto',
		'fields'     => $area_fields,
	);

	// Trámite (Inscripción, Rehabilitación, Baja…): el nombre va en el título.
	// Los textos admiten marcadores {{clave}} (ver la cajita "Datos que podés insertar").
	$tramite_fields = array(
		array(
			'name'    => 'Ícono',
			'id'      => 'icono',
			'desc'    => 'Ícono con el que aparece en "Otros trámites".',
			'type'    => 'select',
			'options' => array(
				'award'  => 'Medalla',
				'check'  => 'Tilde',
				'file'   => 'Documento',
				'shield' => 'Escudo',
				'dollar' => 'Pesos',
			),
			'std'     => 'file',
		),
		array(
			'name'        => 'Etiqueta superior',
			'id'          => 'eyebrow',
			'type'        => 'text',
			'columns'     => 6,
			'placeholder' => 'Matrícula profesional',
		),
		array(
			'name'        => 'Nombre corto (opcional)',
			'id'          => 'breadcrumb',
			'desc'        => 'Solo si el título es largo: se usa en la ruta de navegación (Inicio › Trámites › …).',
			'type'        => 'text',
			'columns'     => 6,
			'placeholder' => 'Credenciales',
		),
		array(
			'name' => 'Bajada',
			'id'   => 'intro',
			'desc' => 'Texto que va bajo el título, en el encabezado.',
			'type' => 'textarea',
			'rows' => 3,
		),
		array(
			'type' => 'heading',
			'name' => 'Cuadro lateral (costos y condiciones)',
		),
		array(
			'name'        => 'Título del cuadro',
			'id'          => 'costo_titulo',
			'type'        => 'text',
			'placeholder' => 'Costo del trámite',
		),
	);
	for ( $i = 1; $i <= CIPBA_TRAMITE_COSTOS; $i++ ) {
		$tramite_fields[] = array(
			'name'        => "Fila $i: etiqueta",
			'id'          => "costo{$i}_label",
			'type'        => 'text',
			'columns'     => 4,
			'placeholder' => 'Ej: Costo del trámite',
		);
		$tramite_fields[] = array(
			'name'    => "Fila $i: texto",
			'id'      => "costo{$i}_value",
			'type'    => 'textarea',
			'columns' => 8,
			'rows'    => 2,
		);
	}
	$tramite_fields[] = array(
		'type' => 'heading',
		'name' => 'Contenido de la página (se muestra en este orden)',
	);
	for ( $i = 1; $i <= CIPBA_TRAMITE_BLOQUES; $i++ ) {
		$tramite_fields[] = array(
			'type' => 'heading',
			'name' => "Bloque $i",
		);
		$tramite_fields[] = array(
			'name'        => 'Tipo',
			'id'          => "bloque{$i}_tipo",
			'type'        => 'select',
			'columns'     => 4,
			'placeholder' => '— No usar este bloque —',
			'options'     => array(
				'requisitos'  => 'Lista de requisitos',
				'aviso'       => 'Aviso (verde)',
				'aviso_ambar' => 'Aviso importante (ámbar)',
			),
		);
		$tramite_fields[] = array(
			'name'    => 'Título',
			'id'      => "bloque{$i}_titulo",
			'type'    => 'text',
			'columns' => 8,
		);
		$tramite_fields[] = array(
			'name' => 'Texto introductorio (opcional, solo listas)',
			'id'   => "bloque{$i}_sub",
			'type' => 'textarea',
			'rows' => 2,
		);
		$tramite_fields[] = array(
			'name'    => 'Contenido',
			'id'      => "bloque{$i}_contenido",
			'desc'    => 'En las listas de requisitos, usá una lista numerada: cada ítem con el nombre en negrita y el detalle a continuación.',
			'type'    => 'wysiwyg',
			'raw'     => true,
			'options' => array(
				'textarea_rows' => 8,
				'teeny'         => true,
				'media_buttons' => false,
			),
		);
	}
	$tramite_fields[] = array(
		'type' => 'heading',
		'name' => 'Formularios y documentación',
	);
	$tramite_fields[] = array(
		'name'        => 'Título de la sección',
		'id'          => 'docs_titulo',
		'type'        => 'text',
		'placeholder' => 'Formularios y documentación',
	);
	$tramite_fields[] = array(
		'name' => 'Texto introductorio',
		'id'   => 'docs_intro',
		'type' => 'textarea',
		'rows' => 2,
	);
	for ( $i = 1; $i <= CIPBA_TRAMITE_DOCS; $i++ ) {
		$tramite_fields[] = array(
			'name'        => "Documento $i",
			'id'          => "doc{$i}",
			'desc'        => 1 === $i ? 'Se eligen de la biblioteca de Documentos (allí se sube el archivo una sola vez).' : '',
			'type'        => 'post',
			'post_type'   => 'documento',
			'field_type'  => 'select_advanced',
			'placeholder' => '— Elegí un documento —',
			'query_args'  => array( 'post_status' => 'publish', 'posts_per_page' => -1 ),
			'columns'     => 6,
		);
	}

	if ( ! cipba_admin_editando_pago() ) {
		$meta_boxes[] = array(
			'title'      => 'Datos del trámite',
			'post_types' => 'tramite',
			'fields'     => $tramite_fields,
		);
	}

	// Página "Medios de pago" (trámite con slug pago-matricula): formulario propio.
	// Los datos bancarios NO están acá: se cargan en Datos del Distrito.
	$pago_fields = array(
		array( 'type' => 'heading', 'name' => 'Encabezado' ),
		array(
			'name'        => 'Título de la página',
			'id'          => 'pago_h1',
			'desc'        => 'El nombre del trámite (arriba, en el título de esta pantalla) es el que se usa en el menú y en "Otros trámites".',
			'type'        => 'text',
			'placeholder' => 'Pagar Matrícula',
		),
		array(
			'name'        => 'Etiqueta superior',
			'id'          => 'eyebrow',
			'type'        => 'text',
			'columns'     => 6,
			'placeholder' => 'Matrícula profesional',
		),
		array(
			'name'        => 'Nombre corto (ruta de navegación)',
			'id'          => 'breadcrumb',
			'type'        => 'text',
			'columns'     => 6,
			'placeholder' => 'Pagar Matrícula',
		),
		array(
			'name' => 'Bajada',
			'id'   => 'intro',
			'type' => 'textarea',
			'rows' => 3,
		),
		array( 'type' => 'heading', 'name' => 'Antes de pagar' ),
		array(
			'name' => 'Título',
			'id'   => 'pago_antes_titulo',
			'type' => 'text',
		),
		array(
			'name'    => 'Texto',
			'id'      => 'pago_antes_texto',
			'type'    => 'wysiwyg',
			'raw'     => true,
			'options' => array( 'textarea_rows' => 8, 'teeny' => true, 'media_buttons' => false ),
		),
		array(
			'name' => 'Título del recuadro "tené a mano"',
			'id'   => 'pago_antes_lista_titulo',
			'type' => 'text',
		),
		array(
			'name' => 'Elementos del recuadro',
			'id'   => 'pago_antes_lista',
			'desc' => 'Uno por línea.',
			'type' => 'textarea',
			'rows' => 4,
		),
		array( 'type' => 'heading', 'name' => 'Formas de pago' ),
		array(
			'name'    => 'Etiqueta superior',
			'id'      => 'pago_mod_eyebrow',
			'type'    => 'text',
			'columns' => 4,
		),
		array(
			'name'    => 'Título',
			'id'      => 'pago_mod_titulo',
			'type'    => 'text',
			'columns' => 8,
		),
	);
	$pago_mod_extra = array(
		1 => array(
			array(
				'name' => 'Texto al pie de la ventana de datos bancarios',
				'id'   => 'mod1_pie',
				'desc' => 'Los datos de la cuenta se cargan en Datos del Distrito → Datos bancarios.',
				'type' => 'textarea',
				'rows' => 2,
			),
		),
		2 => array(
			array(
				'name'        => 'Documento a descargar (formulario de adhesión)',
				'id'          => 'mod2_doc',
				'desc'        => 'Se elige de la biblioteca Documentos (allí se sube el archivo).',
				'type'        => 'post',
				'post_type'   => 'documento',
				'field_type'  => 'select_advanced',
				'placeholder' => '— Elegí un documento —',
				'query_args'  => array( 'post_status' => 'publish', 'posts_per_page' => -1 ),
			),
		),
		3 => array(
			array(
				'name'        => 'Dirección de la consulta del código de pago',
				'id'          => 'mod3_url',
				'desc'        => 'Página del Consejo Superior que se muestra en la ventana emergente.',
				'type'        => 'url',
				'placeholder' => 'http://www.colegioingenieros.org.ar/link/',
			),
		),
		4 => array(
			array(
				'name'        => 'Documento a descargar (instructivo)',
				'id'          => 'mod4_doc',
				'desc'        => 'Aparece como enlace de descarga al pie de la ventana del instructivo.',
				'type'        => 'post',
				'post_type'   => 'documento',
				'field_type'  => 'select_advanced',
				'placeholder' => '— Elegí un documento —',
				'query_args'  => array( 'post_status' => 'publish', 'posts_per_page' => -1 ),
			),
		),
	);
	for ( $n = 1; $n <= 4; $n++ ) {
		$pago_fields[] = array( 'type' => 'heading', 'name' => "Modalidad $n" );
		$pago_fields[] = array(
			'name'    => 'Título',
			'id'      => "mod{$n}_titulo",
			'type'    => 'text',
			'columns' => 6,
		);
		$pago_fields[] = array(
			'name'    => 'Texto del botón',
			'id'      => "mod{$n}_boton",
			'type'    => 'text',
			'columns' => 6,
		);
		$pago_fields[] = array(
			'name' => 'Descripción',
			'id'   => "mod{$n}_desc",
			'type' => 'textarea',
			'rows' => 3,
		);
		$pago_fields[] = array(
			'name' => 'Pasos',
			'id'   => "mod{$n}_pasos",
			'desc' => 'Un paso por línea. Los correos y enlaces se vuelven clickeables.',
			'type' => 'textarea',
			'rows' => 4,
		);
		foreach ( $pago_mod_extra[ $n ] as $extra ) {
			$pago_fields[] = $extra;
		}
	}
	$pago_fields[] = array( 'type' => 'heading', 'name' => 'Cierre de la página' );
	$pago_fields[] = array(
		'name' => 'Título',
		'id'   => 'pago_cta_titulo',
		'type' => 'text',
	);
	$pago_fields[] = array(
		'name' => 'Texto',
		'id'   => 'pago_cta_texto',
		'type' => 'textarea',
		'rows' => 3,
	);

	// Cada trámite muestra SOLO su formulario: el de pago (slug pago-matricula) tiene
	// campos con los mismos nombres que el común, y si aparecieran los dos, al guardar
	// uno pisaría al otro. Meta Box no filtra por slug, así que se decide acá.
	if ( cipba_admin_editando_pago() ) {
		$meta_boxes[] = array(
			'title'      => 'Datos de la página de pago',
			'post_types' => 'tramite',
			'fields'     => $pago_fields,
		);
	}

	// Link de interés (pie de página): el nombre va en el título.
	$meta_boxes[] = array(
		'title'      => 'Datos del link',
		'post_types' => 'link_interes',
		'fields'     => array(
			array(
				'name'        => 'Dirección',
				'id'          => 'url',
				'desc'        => 'Dirección completa del sitio, con https://. Se abre en una pestaña nueva. El orden se define en Atributos → Orden.',
				'type'        => 'url',
				'required'    => true,
				'placeholder' => 'https://www.arba.gov.ar/',
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'cipba_register_meta_boxes' );
