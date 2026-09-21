<?php
/**
 * Honorarios mínimos: una resolución del Consejo Superior por cada publicación
 * (tipo de contenido `resolucion`). La marcada "vigente" se muestra arriba con
 * sus documentos (la resolución y los anexos, subidos al sitio) y las demás se
 * listan como "Resoluciones anteriores" con un enlace de descarga al Consejo
 * Superior. La página vive en /honorarios/ ([cipba_honorarios]).
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * El título de la resolución se arma solo a partir del código ("1553/2026" →
 * "Resolución CS 1553/2026"): quien carga la resolución no escribe título.
 */
function cipba_resolucion_titulo_automatico( $data, $postarr ) {
	if ( 'resolucion' === $data['post_type'] && isset( $postarr['numero'] ) && '' !== trim( $postarr['numero'] ) ) {
		$data['post_title'] = 'Resolución CS ' . sanitize_text_field( wp_unslash( $postarr['numero'] ) );
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'cipba_resolucion_titulo_automatico', 10, 2 );

/**
 * Solo una resolución puede ser "la vigente": al marcar una, se desmarcan las
 * demás (corre después de que Meta Box guarda los campos).
 */
function cipba_resolucion_una_sola_vigente( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || 'resolucion' !== get_post_type( $post_id ) || ! get_post_meta( $post_id, 'vigente', true ) ) {
		return;
	}
	$otras = get_posts( array(
		'post_type'    => 'resolucion',
		'numberposts'  => -1,
		'post_status'  => 'any',
		'post__not_in' => array( $post_id ),
		'meta_key'     => 'vigente',
		'meta_value'   => '1',
		'fields'       => 'ids',
	) );
	foreach ( $otras as $otra_id ) {
		update_post_meta( $otra_id, 'vigente', 0 );
	}
}
add_action( 'save_post', 'cipba_resolucion_una_sola_vigente', 20 );

/**
 * La resolución marcada como vigente (la más reciente si hubiera varias) o null.
 */
function cipba_get_resolucion_vigente() {
	$posts = get_posts( array(
		'post_type'   => 'resolucion',
		'post_status' => 'publish',
		'numberposts' => 1,
		'meta_key'    => 'vigente',
		'meta_value'  => '1',
		'orderby'     => 'modified',
		'order'       => 'DESC',
	) );
	return $posts ? $posts[0] : null;
}

/**
 * Fecha de referencia de una resolución para ordenar el historial: la de
 * publicación o, si no tiene, la de inicio de vigencia.
 */
function cipba_resolucion_fecha_orden( $post_id ) {
	$f = trim( (string) get_post_meta( $post_id, 'fecha_publicacion', true ) );
	return '' !== $f ? $f : trim( (string) get_post_meta( $post_id, 'vigencia_desde', true ) );
}

/**
 * Resoluciones anteriores (todas menos la vigente), de la más nueva a la más
 * vieja. $limit 0 = todas.
 *
 * @return WP_Post[]
 */
function cipba_get_resoluciones_anteriores( $limit = 6 ) {
	$vigente = cipba_get_resolucion_vigente();
	$posts   = get_posts( array(
		'post_type'    => 'resolucion',
		'post_status'  => 'publish',
		'numberposts'  => -1,
		'post__not_in' => $vigente ? array( $vigente->ID ) : array(),
	) );
	usort( $posts, function ( $a, $b ) {
		return strcmp( cipba_resolucion_fecha_orden( $b->ID ), cipba_resolucion_fecha_orden( $a->ID ) );
	} );
	return $limit > 0 ? array_slice( $posts, 0, $limit ) : $posts;
}

/**
 * "17/11/2025" a partir de Y-m-d.
 */
function cipba_fecha_dmy( $ymd ) {
	$ts = strtotime( $ymd );
	return $ts ? gmdate( 'd/m/Y', $ts ) : '';
}

/**
 * Texto del período de una resolución anterior: "Vigente 01/10/2025 –
 * 31/03/2026" si tiene rango de vigencia; si no, "Publicada 17/11/2025".
 */
function cipba_resolucion_periodo( $post_id ) {
	$g = function ( $k ) use ( $post_id ) {
		return trim( (string) get_post_meta( $post_id, $k, true ) );
	};
	if ( $g( 'vigencia_desde' ) && $g( 'vigencia_hasta' ) ) {
		return 'Vigente ' . cipba_fecha_dmy( $g( 'vigencia_desde' ) ) . ' – ' . cipba_fecha_dmy( $g( 'vigencia_hasta' ) );
	}
	if ( $g( 'fecha_publicacion' ) ) {
		return 'Publicada ' . cipba_fecha_dmy( $g( 'fecha_publicacion' ) );
	}
	if ( $g( 'vigencia_desde' ) ) {
		return 'Vigente desde ' . cipba_fecha_dmy( $g( 'vigencia_desde' ) );
	}
	return '';
}

/**
 * Archivo subido en un campo de la resolución: array( formato, peso, url ) o
 * null si no hay archivo.
 */
function cipba_resolucion_archivo( $post_id, $meta_key ) {
	$ids           = get_post_meta( $post_id, $meta_key, false );
	$attachment_id = $ids ? (int) reset( $ids ) : 0;
	if ( ! $attachment_id ) {
		return null;
	}
	$path = get_attached_file( $attachment_id );
	$url  = wp_get_attachment_url( $attachment_id );
	if ( ! $path || ! $url || ! file_exists( $path ) ) {
		return null;
	}
	return array(
		'formato' => strtoupper( pathinfo( $path, PATHINFO_EXTENSION ) ),
		'peso'    => size_format( filesize( $path ) ),
		'url'     => $url,
	);
}

/**
 * [cipba_honorarios] — página /honorarios/: resolución vigente con sus
 * documentos, aviso al listado del Consejo Superior y resoluciones anteriores.
 * El markup vive en template-parts/honorarios.php.
 */
function cipba_honorarios_shortcode() {
	ob_start();
	get_template_part( 'template-parts/honorarios' );
	return ob_get_clean();
}
add_shortcode( 'cipba_honorarios', 'cipba_honorarios_shortcode' );

/**
 * Listado de Resoluciones del admin: código, vigencia, publicación y si tiene
 * los documentos cargados.
 */
function cipba_resolucion_admin_columns( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => 'Resolución',
		'vigente'   => 'Vigente',
		'fechas'    => 'Publicación / vigencia',
		'documentos' => 'Documentos',
	);
}
add_filter( 'manage_resolucion_posts_columns', 'cipba_resolucion_admin_columns' );

function cipba_resolucion_admin_column_content( $col, $post_id ) {
	if ( 'vigente' === $col ) {
		echo get_post_meta( $post_id, 'vigente', true ) ? '★ Vigente' : '—';
	} elseif ( 'fechas' === $col ) {
		$pub = get_post_meta( $post_id, 'fecha_publicacion', true );
		echo esc_html( $pub ? 'Publicada ' . cipba_fecha_dmy( $pub ) : '' );
		$desde = get_post_meta( $post_id, 'vigencia_desde', true );
		if ( $desde ) {
			echo ( $pub ? '<br>' : '' ) . esc_html( 'Desde ' . cipba_fecha_dmy( $desde ) );
		}
	} elseif ( 'documentos' === $col ) {
		$partes = array();
		if ( cipba_resolucion_archivo( $post_id, 'archivo_resolucion' ) ) { $partes[] = 'Resolución'; }
		if ( cipba_resolucion_archivo( $post_id, 'archivo_anexos' ) ) { $partes[] = 'Anexos'; }
		if ( get_post_meta( $post_id, 'enlace_externo', true ) ) { $partes[] = 'Enlace CS'; }
		echo esc_html( $partes ? implode( ' · ', $partes ) : '—' );
	}
}
add_action( 'manage_resolucion_posts_custom_column', 'cipba_resolucion_admin_column_content', 10, 2 );

function cipba_resolucion_admin_default_order( $query ) {
	if ( is_admin() && $query->is_main_query() && 'resolucion' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
	}
}
add_action( 'pre_get_posts', 'cipba_resolucion_admin_default_order' );

/**
 * [cipba_aviso_honorarios] — barra de aviso de la home: "Honorarios mínimos
 * vigentes desde 01/04/2026 — Res. 1553" con enlace a /honorarios/. Se arma
 * con la resolución marcada como vigente (código y vigencia desde); si no hay
 * ninguna, no muestra la barra.
 */
function cipba_aviso_honorarios_shortcode() {
	$vig = cipba_get_resolucion_vigente();
	if ( ! $vig ) {
		return '';
	}
	$numero = trim( (string) get_post_meta( $vig->ID, 'numero', true ) );
	$desde  = trim( (string) get_post_meta( $vig->ID, 'vigencia_desde', true ) );
	$texto  = 'Honorarios mínimos vigentes' . ( $desde ? ' desde ' . cipba_fecha_dmy( $desde ) : '' ) . ( $numero ? ' — Res. ' . strtok( $numero, '/' ) : '' );

	return '<div class="cipba-announcement"><div class="cipba-announcement-wrap"><span>⚖️ ' . esc_html( $texto ) . '</span><a href="' . esc_url( home_url( '/honorarios/' ) ) . '">Ver tabla →</a></div></div>';
}
add_shortcode( 'cipba_aviso_honorarios', 'cipba_aviso_honorarios_shortcode' );
