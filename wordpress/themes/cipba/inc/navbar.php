<?php
/**
 * Navbar: sombra/blur al scrollear (§5.2) y enlaces de menú que abren
 * en pestaña nueva (§5.9).
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Agrega/quita .is-scrolled en .ast-main-header-wrap para el CSS de §5.2.
 */
function cipba_sticky_navbar_script() {
	?>
	<script>
	addEventListener('scroll',()=>document.querySelector('.ast-main-header-wrap')
	  ?.classList.toggle('is-scrolled',scrollY>40),{passive:true});
	</script>
	<?php
}
add_action( 'wp_footer', 'cipba_sticky_navbar_script' );

/**
 * El panel del menú mobile de Max Mega Menu se saca del flujo con
 * position:fixed (ver style.css) para que ocupe todo el ancho en vez de
 * quedar encerrado en la columna del logo — pero position:fixed no sabe
 * dónde termina el header solo con CSS. Al tocar el botón hamburguesa,
 * se mide el borde inferior real de #ast-desktop-header (que sí queda
 * en flujo normal) y se usa como "top" del panel.
 */
function cipba_mobile_menu_panel_position_script() {
	?>
	<script>
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('#mega-menu-wrap-primary .mega-menu-toggle button');
		if (!btn) return;
		requestAnimationFrame(function () {
			var menu = document.getElementById('mega-menu-primary');
			var header = document.getElementById('ast-desktop-header');
			if (menu && header) {
				menu.style.top = Math.round(header.getBoundingClientRect().bottom) + 'px';
			}
		});
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'cipba_mobile_menu_panel_position_script' );

/**
 * Los ítems de menú marcados con la clase "abre-nuevo" (o que enlazan
 * directo a un PDF) abren en pestaña nueva con rel="noopener".
 */
function cipba_menu_link_attributes( $atts, $item ) {
	$classes = (array) $item->classes;
	$es_pdf  = preg_match( '/\.pdf$/i', $atts['href'] ?? '' );

	if ( in_array( 'abre-nuevo', $classes, true ) || $es_pdf ) {
		$atts['target'] = '_blank';
		$atts['rel']    = 'noopener';
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'cipba_menu_link_attributes', 10, 2 );
