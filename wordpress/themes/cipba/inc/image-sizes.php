<?php
/**
 * Tamaños de imagen registrados por el diseño (§7 del handoff).
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cipba_add_image_sizes() {
	add_image_size( 'cipba-card', 600, 400, true );
	add_image_size( 'cipba-nov-hero', 1680, 720, true );
	add_image_size( 'cipba-thumb', 300, 200, true );
	// Eventos y novedades: flyers verticales 4:5, pensados para redes sociales.
	add_image_size( 'cipba-card-vert', 480, 600, true );
	// Sin recortar: se usa en el hero de la novedad y en la destacada para mostrar el flyer completo.
	add_image_size( 'cipba-flyer', 900, 1600 );
}
add_action( 'after_setup_theme', 'cipba_add_image_sizes' );

/**
 * Restringe los tamaños que aparecen en el selector de medios a los
 * definidos arriba + los propios de WordPress, para no inflar la
 * librería con tamaños intermedios que el diseño no usa.
 */
function cipba_custom_image_sizes( $sizes ) {
	return array(
		'thumbnail'       => $sizes['thumbnail'],
		'medium'          => $sizes['medium'],
		'large'           => $sizes['large'],
		'cipba-card'      => 'CIPBA — Card (600×400)',
		'cipba-nov-hero'  => 'CIPBA — Hero de novedad (1680×720)',
		'cipba-thumb'     => 'CIPBA — Thumbnail (300×200)',
		'cipba-card-vert' => 'CIPBA — Card vertical 4:5 (480×600)',
		'cipba-flyer'     => 'CIPBA — Flyer sin recortar (máx. 900×1600)',
	);
}
add_filter( 'image_size_names_choose', 'cipba_custom_image_sizes' );
