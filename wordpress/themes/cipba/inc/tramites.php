<?php
/**
 * Trámites (Inscripción, Rehabilitación, Baja, Credenciales…): una sola
 * plantilla (single-tramite.php) alimentada por un formulario simple en el
 * admin. Los textos admiten marcadores {{clave}} que se reemplazan por los
 * "Datos del Distrito" (ver cipba_tokens() en settings.php).
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Cantidad de bloques de contenido, filas de costo y documentos del formulario. */
define( 'CIPBA_TRAMITE_BLOQUES', 6 );
define( 'CIPBA_TRAMITE_COSTOS', 4 );
define( 'CIPBA_TRAMITE_DOCS', 8 );

/**
 * Texto enriquecido (editor tipo Word) listo para mostrar: marcadores
 * reemplazados, HTML limpiado y párrafos armados.
 */
function cipba_rich( $html ) {
	return wp_kses_post( wpautop( cipba_tokens( $html ) ) );
}

/**
 * Texto simple con marcadores reemplazados y escapado.
 */
function cipba_plain( $text ) {
	return esc_html( wp_strip_all_tags( html_entity_decode( cipba_tokens( $text ), ENT_QUOTES, 'UTF-8' ) ) );
}

/**
 * Filas del cuadro lateral de costos: etiqueta y valor.
 */
function cipba_get_tramite_costos( $post_id ) {
	$out = array();
	for ( $i = 1; $i <= CIPBA_TRAMITE_COSTOS; $i++ ) {
		$label = trim( (string) get_post_meta( $post_id, "costo{$i}_label", true ) );
		$value = trim( (string) get_post_meta( $post_id, "costo{$i}_value", true ) );
		if ( '' === $label && '' === $value ) {
			continue;
		}
		$out[] = array( 'label' => $label, 'value' => $value );
	}
	return $out;
}

/**
 * Bloques de contenido en el orden del formulario: cada uno es una lista de
 * requisitos, un aviso verde o un aviso ámbar. Los de tipo vacío se ignoran.
 */
function cipba_get_tramite_bloques( $post_id ) {
	$validos = array( 'requisitos', 'aviso', 'aviso_ambar' );
	$out     = array();
	for ( $i = 1; $i <= CIPBA_TRAMITE_BLOQUES; $i++ ) {
		$tipo = get_post_meta( $post_id, "bloque{$i}_tipo", true );
		if ( ! in_array( $tipo, $validos, true ) ) {
			continue;
		}
		$out[] = array(
			'tipo'      => $tipo,
			'titulo'    => trim( (string) get_post_meta( $post_id, "bloque{$i}_titulo", true ) ),
			'sub'       => trim( (string) get_post_meta( $post_id, "bloque{$i}_sub", true ) ),
			'contenido' => (string) get_post_meta( $post_id, "bloque{$i}_contenido", true ),
		);
	}
	return $out;
}

/**
 * Documentos elegidos en el trámite (de la biblioteca "Documentos"): título,
 * enlace y formato/peso leídos del archivo. Se omiten los borrados o sin archivo.
 */
function cipba_get_tramite_documentos( $post_id ) {
	$out = array();
	for ( $i = 1; $i <= CIPBA_TRAMITE_DOCS; $i++ ) {
		$doc_id = (int) get_post_meta( $post_id, "doc{$i}", true );
		if ( ! $doc_id || 'publish' !== get_post_status( $doc_id ) ) {
			continue;
		}
		$file = cipba_get_documento_file_meta( $doc_id );
		if ( ! $file ) {
			continue;
		}
		$out[] = array(
			'titulo'  => get_the_title( $doc_id ),
			'url'     => $file['url'],
			'formato' => $file['formato'],
			'peso'    => $file['peso'],
		);
	}
	return $out;
}

/**
 * Al guardar un trámite se le pone el mismo layout de Astra que a las demás
 * páginas del sitio: sin título automático, sin barra lateral y a ancho
 * completo. Astra guarda "default" cuando nadie tocó esos ajustes, y su propia
 * caja de ajustes puede volver a escribirlos al guardar, por eso este hook va
 * en `save_post` con prioridad tardía y reemplaza tanto los vacíos como "default".
 */
function cipba_tramite_layout_defaults( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || 'tramite' !== get_post_type( $post_id ) ) {
		return;
	}
	$defaults = array(
		'site-post-title'         => 'disabled',
		'ast-site-content-layout' => 'full-width-container',
		'site-sidebar-layout'     => 'no-sidebar',
	);
	foreach ( $defaults as $key => $value ) {
		$current = (string) get_post_meta( $post_id, $key, true );
		if ( '' === $current || 'default' === $current ) {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post', 'cipba_tramite_layout_defaults', 99 );

/**
 * Cajita lateral "Datos que podés insertar": los marcadores disponibles con
 * su valor actual, para copiarlos en los textos.
 */
function cipba_tramite_marcadores_metabox() {
	add_meta_box( 'cipba-tramite-marcadores', 'Datos que podés insertar en los textos', 'cipba_tramite_marcadores_render', 'tramite', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'cipba_tramite_marcadores_metabox' );

function cipba_tramite_marcadores_render() {
	echo '<p style="margin-top:0">Escribí el marcador tal cual (con las llaves) en cualquier texto y se reemplaza por el valor vigente.</p>';
	foreach ( cipba_datos_fields() as $key => $f ) {
		if ( in_array( $key, array( 'whatsapp', 'consejo_superior_url', 'instagram', 'facebook', 'linkedin' ), true ) ) {
			continue;
		}
		printf(
			'<div style="border-top:1px solid #dcdcde;padding:6px 0"><code style="user-select:all">{{%s}}</code><div style="color:#50575e;font-size:12px;margin-top:2px">%s</div></div>',
			esc_html( $key ),
			esc_html( cipba_dato_display( $key ) )
		);
	}
	printf( '<p style="margin-bottom:0;border-top:1px solid #dcdcde;padding-top:8px">Para cambiar un valor: <a href="%s">Datos del Distrito</a>.</p>', esc_url( admin_url( 'admin.php?page=cipba-datos-distrito' ) ) );
}

/**
 * Listado del admin de Trámites: ícono y orden a la vista.
 */
function cipba_tramite_admin_columns( $cols ) {
	return array(
		'cb'    => $cols['cb'],
		'title' => 'Trámite',
		'icono' => 'Ícono',
		'orden' => 'Orden',
	);
}
add_filter( 'manage_tramite_posts_columns', 'cipba_tramite_admin_columns' );

function cipba_tramite_admin_column_content( $col, $post_id ) {
	if ( 'icono' === $col ) {
		echo esc_html( get_post_meta( $post_id, 'icono', true ) );
	} elseif ( 'orden' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}
add_action( 'manage_tramite_posts_custom_column', 'cipba_tramite_admin_column_content', 10, 2 );

function cipba_tramite_admin_default_order( $query ) {
	if ( is_admin() && $query->is_main_query() && 'tramite' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'cipba_tramite_admin_default_order' );
