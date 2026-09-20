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
		'supports'     => array( 'title', 'page-attributes' ),
	) );

	register_post_type( 'sede', array(
		'label'        => 'Sedes',
		'labels'       => array(
			'name'          => 'Sedes',
			'singular_name' => 'Sede',
			'add_new_item'  => 'Agregar sede',
			'edit_item'     => 'Editar sede',
			'all_items'     => 'Todas las sedes',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-location-alt',
		'supports'     => array( 'title', 'page-attributes' ),
	) );

	register_post_type( 'autoridad', array(
		'label'        => 'Autoridades',
		'labels'       => array(
			'name'          => 'Autoridades',
			'singular_name' => 'Autoridad',
			'add_new_item'  => 'Agregar autoridad',
			'edit_item'     => 'Editar autoridad',
			'all_items'     => 'Todas las autoridades',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-id',
		'supports'     => array( 'title', 'page-attributes' ),
	) );

	register_post_type( 'area_contacto', array(
		'label'        => 'Áreas de contacto',
		'labels'       => array(
			'name'          => 'Áreas de contacto',
			'singular_name' => 'Área de contacto',
			'add_new_item'  => 'Agregar área de contacto',
			'edit_item'     => 'Editar área de contacto',
			'all_items'     => 'Todas las áreas de contacto',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-phone',
		'supports'     => array( 'title', 'page-attributes' ),
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
		'supports'     => array( 'title', 'page-attributes' ),
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

/**
 * Subcomisiones — listado del admin: sigla, referentes y orden a la vista.
 * El orden se edita en "Atributos > Orden" (menu_order) de cada subcomisión.
 */
function cipba_subcomision_admin_columns( $cols ) {
	return array(
		'cb'         => $cols['cb'],
		'title'      => 'Subcomisión',
		'sigla'      => 'Sigla',
		'referentes' => 'Referentes',
		'orden'      => 'Orden',
	);
}
add_filter( 'manage_subcomision_posts_columns', 'cipba_subcomision_admin_columns' );

function cipba_subcomision_admin_column_content( $col, $post_id ) {
	if ( 'sigla' === $col ) {
		echo esc_html( rwmb_meta( 'tag', array(), $post_id ) );
	} elseif ( 'referentes' === $col ) {
		$refs = cipba_get_referentes( $post_id );
		echo esc_html( implode( ' · ', array_filter( wp_list_pluck( $refs, 'nombre' ) ) ) );
	} elseif ( 'orden' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}
add_action( 'manage_subcomision_posts_custom_column', 'cipba_subcomision_admin_column_content', 10, 2 );

function cipba_subcomision_admin_default_order( $query ) {
	if ( is_admin() && $query->is_main_query() && 'subcomision' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'cipba_subcomision_admin_default_order' );

/**
 * Subcomisión: pantalla de edición clásica (un formulario simple: título +
 * cajas de campos), sin el editor de bloques que no aporta nada acá.
 */
function cipba_subcomision_classic_editor( $use_block_editor, $post_type ) {
	return in_array( $post_type, array( 'subcomision', 'sede', 'autoridad', 'area_contacto', 'tramite' ), true ) ? false : $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'cipba_subcomision_classic_editor', 10, 2 );

function cipba_subcomision_title_placeholder( $text, $post ) {
	if ( 'subcomision' === $post->post_type ) {
		return 'Nombre de la subcomisión (ej: Ingeniería Civil)';
	}
	if ( 'tramite' === $post->post_type ) {
		return 'Nombre del trámite (ej: Inscripción)';
	}
	if ( 'area_contacto' === $post->post_type ) {
		return 'Nombre del área (ej: Área Administrativa)';
	}
	if ( 'autoridad' === $post->post_type ) {
		return 'Nombre y apellido (ej: Daniel Héctor PALACIOS)';
	}
	if ( 'sede' === $post->post_type ) {
		return 'Nombre de la sede (ej: Sede San Justo)';
	}
	return $text;
}
add_filter( 'enter_title_here', 'cipba_subcomision_title_placeholder', 10, 2 );

/**
 * Sedes — listado del admin: tipo, dirección y orden a la vista.
 */
function cipba_sede_admin_columns( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => 'Sede',
		'tipo'      => 'Tipo',
		'direccion' => 'Dirección',
		'orden'     => 'Orden',
	);
}
add_filter( 'manage_sede_posts_columns', 'cipba_sede_admin_columns' );

function cipba_sede_admin_column_content( $col, $post_id ) {
	if ( 'tipo' === $col ) {
		echo esc_html( get_post_meta( $post_id, 'tag', true ) );
		if ( get_post_meta( $post_id, 'destacada', true ) ) {
			echo ' ★';
		}
	} elseif ( 'direccion' === $col ) {
		echo esc_html( get_post_meta( $post_id, 'direccion', true ) );
	} elseif ( 'orden' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}
add_action( 'manage_sede_posts_custom_column', 'cipba_sede_admin_column_content', 10, 2 );

function cipba_sede_admin_default_order( $query ) {
	if ( is_admin() && $query->is_main_query() && 'sede' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'cipba_sede_admin_default_order' );

/**
 * Autoridades — listado del admin: cargo, título y orden a la vista.
 */
function cipba_autoridad_admin_columns( $cols ) {
	return array(
		'cb'     => $cols['cb'],
		'title'  => 'Autoridad',
		'cargo'  => 'Cargo',
		'titulo' => 'Título',
		'orden'  => 'Orden',
	);
}
add_filter( 'manage_autoridad_posts_columns', 'cipba_autoridad_admin_columns' );

function cipba_autoridad_admin_column_content( $col, $post_id ) {
	if ( 'cargo' === $col ) {
		echo esc_html( get_post_meta( $post_id, 'cargo', true ) );
		if ( get_post_meta( $post_id, 'destacado', true ) ) {
			echo ' ★';
		}
	} elseif ( 'titulo' === $col ) {
		echo esc_html( get_post_meta( $post_id, 'titulo', true ) );
	} elseif ( 'orden' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}
add_action( 'manage_autoridad_posts_custom_column', 'cipba_autoridad_admin_column_content', 10, 2 );

function cipba_autoridad_admin_default_order( $query ) {
	if ( is_admin() && $query->is_main_query() && 'autoridad' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'cipba_autoridad_admin_default_order' );

/**
 * Áreas de contacto — listado del admin: personas, correo y orden a la vista.
 */
function cipba_area_admin_columns( $cols ) {
	return array(
		'cb'       => $cols['cb'],
		'title'    => 'Área',
		'personas' => 'Personas',
		'email'    => 'Correo',
		'orden'    => 'Orden',
	);
}
add_filter( 'manage_area_contacto_posts_columns', 'cipba_area_admin_columns' );

function cipba_area_admin_column_content( $col, $post_id ) {
	if ( 'personas' === $col ) {
		echo esc_html( implode( ' · ', wp_list_pluck( cipba_get_area_personas( $post_id ), 'nombre' ) ) );
	} elseif ( 'email' === $col ) {
		echo esc_html( get_post_meta( $post_id, 'email', true ) );
	} elseif ( 'orden' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}
add_action( 'manage_area_contacto_posts_custom_column', 'cipba_area_admin_column_content', 10, 2 );

function cipba_area_admin_default_order( $query ) {
	if ( is_admin() && $query->is_main_query() && 'area_contacto' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'cipba_area_admin_default_order' );

/**
 * Pantallas de edición más limpias: fuera las cajas técnicas que no le sirven
 * a quien carga contenido (ajustes de Astra, campos personalizados, slug) en
 * los tipos de contenido del tema. En Trámites, el layout de Astra lo fija
 * cipba_tramite_layout_defaults() en cada guardado.
 */
function cipba_limpiar_metaboxes_cpt() {
	$tipos = array( 'evento', 'documento', 'resolucion', 'subcomision', 'sede', 'autoridad', 'area_contacto', 'tramite' );
	foreach ( $tipos as $tipo ) {
		remove_meta_box( 'astra_settings_meta_box', $tipo, 'side' );
		remove_meta_box( 'postcustom', $tipo, 'normal' );
		remove_meta_box( 'slugdiv', $tipo, 'normal' );
	}
}
add_action( 'add_meta_boxes', 'cipba_limpiar_metaboxes_cpt', 99 );
