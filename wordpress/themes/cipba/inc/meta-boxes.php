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

	// Evento — agenda de la home.
	$meta_boxes[] = array(
		'title'      => 'Datos del evento',
		'post_types' => 'evento',
		'fields'     => array(
			array(
				'name' => 'Fecha de inicio',
				'id'   => 'fecha_inicio',
				'type' => 'date',
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
			array(
				'name' => 'Cupo',
				'id'   => 'cupo',
				'type' => 'number',
			),
			array(
				'name' => 'Inscripción abierta',
				'id'   => 'inscripcion_abierta',
				'type' => 'checkbox',
			),
			array(
				'name' => 'Cierre de inscripción',
				'id'   => 'cierre_inscripcion',
				'type' => 'date',
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
			array(
				'name' => 'Lugar',
				'id'   => 'lugar',
				'type' => 'text',
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

	// Resolución — Honorarios mínimos.
	$meta_boxes[] = array(
		'title'      => 'Datos de la resolución',
		'post_types' => 'resolucion',
		'fields'     => array(
			array(
				'name' => 'Número',
				'id'   => 'numero',
				'type' => 'text',
			),
			array(
				'name' => 'Fecha de publicación',
				'id'   => 'fecha_publicacion',
				'type' => 'date',
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
			array(
				'name' => 'Vigencia desde',
				'id'   => 'vigencia_desde',
				'type' => 'date',
				'js_options' => array( 'dateFormat' => 'yy-mm-dd' ),
			),
			array(
				'name' => 'Vigente',
				'id'   => 'vigente',
				'type' => 'checkbox',
			),
			array(
				'name'   => 'Anexos',
				'id'     => 'anexos',
				'type'   => 'group',
				'clone'  => true,
				'sort_clone' => true,
				'fields' => array(
					array(
						'name' => 'Título',
						'id'   => 'titulo',
						'type' => 'text',
					),
					array(
						'name'     => 'Archivo',
						'id'       => 'archivo',
						'type'     => 'file_advanced',
						'max_file_uploads' => 1,
					),
					array(
						'name' => 'Descripción',
						'id'   => 'descripcion',
						'type' => 'textarea',
					),
				),
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

	$meta_boxes[] = array(
		'title'      => 'Datos del trámite',
		'post_types' => 'tramite',
		'fields'     => $tramite_fields,
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'cipba_register_meta_boxes' );
