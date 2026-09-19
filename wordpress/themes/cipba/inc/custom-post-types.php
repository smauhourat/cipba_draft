<?php
/**
 * Custom Post Types: evento, documento, resolucion, subcomision, tramite.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cipba_register_post_types() {

	register_post_type( 'evento', array(
		'label'        => 'Eventos',
		'labels'       => array(
			'name'          => 'Eventos',
			'singular_name' => 'Evento',
			'add_new_item'  => 'Agregar evento',
			'edit_item'     => 'Editar evento',
			'all_items'     => 'Todos los eventos',
		),
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-calendar-alt',
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'rewrite'      => array( 'slug' => 'eventos' ),
	) );

	register_post_type( 'documento', array(
		'label'        => 'Documentos',
		'labels'       => array(
			'name'          => 'Documentos (Normativa)',
			'singular_name' => 'Documento',
			'add_new_item'  => 'Agregar documento',
			'edit_item'     => 'Editar documento',
			'all_items'     => 'Todos los documentos',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-media-document',
		'supports'     => array( 'title' ),
	) );

	register_post_type( 'resolucion', array(
		'label'        => 'Resoluciones',
		'labels'       => array(
			'name'          => 'Resoluciones (Honorarios)',
			'singular_name' => 'Resolución',
			'add_new_item'  => 'Agregar resolución',
			'edit_item'     => 'Editar resolución',
			'all_items'     => 'Todas las resoluciones',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-media-spreadsheet',
		'supports'     => array( 'title', 'editor' ),
	) );

	register_post_type( 'subcomision', array(
		'label'        => 'Subcomisiones',
		'labels'       => array(
			'name'          => 'Subcomisiones',
			'singular_name' => 'Subcomisión',
			'add_new_item'  => 'Agregar subcomisión',
			'edit_item'     => 'Editar subcomisión',
			'all_items'     => 'Todas las subcomisiones',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-groups',
		'supports'     => array( 'title', 'editor' ),
	) );

	register_post_type( 'tramite', array(
		'label'        => 'Trámites',
		'labels'       => array(
			'name'          => 'Trámites',
			'singular_name' => 'Trámite',
			'add_new_item'  => 'Agregar trámite',
			'edit_item'     => 'Editar trámite',
			'all_items'     => 'Todos los trámites',
		),
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-portfolio',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'rewrite'      => array( 'slug' => 'tramites' ),
	) );
}
add_action( 'init', 'cipba_register_post_types' );

/**
 * Categorías por defecto para Novedades (post nativo).
 * Corren una sola vez al activar el tema; para un sitio ya activo,
 * se cargan a mano con `wp term create` (ver plan-inicial-wp.md).
 */
function cipba_seed_novedades_categories() {
	$categorias = array(
		'institucional' => 'Institucional',
		'capacitacion'  => 'Capacitación',
		'normativa'     => 'Normativa',
		'matricula'     => 'Matrícula',
		'comisiones'    => 'Comisiones',
	);

	foreach ( $categorias as $slug => $nombre ) {
		if ( ! term_exists( $slug, 'category' ) ) {
			wp_insert_term( $nombre, 'category', array( 'slug' => $slug ) );
		}
	}
}
add_action( 'after_switch_theme', 'cipba_seed_novedades_categories' );
