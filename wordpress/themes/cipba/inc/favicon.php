<?php
/**
 * Favicon: set completo (ico, svg, apple-touch-icon, PNGs y web manifest)
 * generado con realfavicongenerator y guardado en assets/img/favicons/.
 *
 * Se desactiva el <link rel="icon"> que agrega el core de WordPress (a
 * partir del "Site Icon" del Personalizador) para que no queden dos juegos
 * de íconos compitiendo, y se imprime este set en su lugar.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'wp_head', 'wp_site_icon' );
remove_action( 'admin_head', 'wp_site_icon' );

function cipba_favicon_tags() {
	$base = get_stylesheet_directory_uri() . '/assets/img/favicons/';
	?>
	<link rel="icon" type="image/png" href="<?php echo esc_url( $base . 'favicon-96x96.png' ); ?>" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $base . 'favicon.svg' ); ?>" />
	<link rel="shortcut icon" href="<?php echo esc_url( $base . 'favicon.ico' ); ?>" />
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $base . 'apple-touch-icon.png' ); ?>" />
	<link rel="manifest" href="<?php echo esc_url( $base . 'site.webmanifest' ); ?>" />
	<?php
}
add_action( 'wp_head', 'cipba_favicon_tags', 5 );
add_action( 'admin_head', 'cipba_favicon_tags', 5 );
