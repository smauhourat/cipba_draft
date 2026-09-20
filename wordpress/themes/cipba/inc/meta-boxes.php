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
		'title'      => 'Archivo de normativa',
		'post_types' => 'documento',
		'fields'     => array(
			array(
				'name'     => 'Archivo (PDF)',
				'id'       => 'archivo',
				'type'     => 'file_advanced',
				'max_file_uploads' => 1,
				'mime_type' => 'application/pdf',
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

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'cipba_register_meta_boxes' );
