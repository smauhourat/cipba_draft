<?php
/**
 * cipba Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package cipba
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_CIPBA_VERSION', '1.0.57' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'cipba-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_CIPBA_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

/**
 * Custom post types, campos (Meta Box), tamaños de imagen y helpers.
 */
require_once get_stylesheet_directory() . '/inc/custom-post-types.php';
require_once get_stylesheet_directory() . '/inc/image-sizes.php';
require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/settings.php';
require_once get_stylesheet_directory() . '/inc/tramites.php';
require_once get_stylesheet_directory() . '/inc/novedades.php';
require_once get_stylesheet_directory() . '/inc/topbar.php';
require_once get_stylesheet_directory() . '/inc/navbar.php';
require_once get_stylesheet_directory() . '/inc/footer.php';
require_once get_stylesheet_directory() . '/inc/shortcodes.php';

if ( class_exists( 'RWMB_Loader' ) ) {
	require_once get_stylesheet_directory() . '/inc/meta-boxes.php';
}