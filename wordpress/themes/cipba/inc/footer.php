<?php
/**
 * Pie de página: reemplaza la fila "Primary Footer" del Constructor de
 * Footer de Astra por markup propio.
 *
 * Por qué: Astra Free solo permite UN elemento "Footer Menu" en el
 * Constructor visual, cableado a la ubicación de menú `footer_menu` —
 * no admite varios menús independientes por columna (eso es de Astra
 * Pro). El diseño necesita 4 columnas de enlaces + una de marca, así
 * que se resuelve por código en vez de con el builder.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Saca el "Primary Footer" que arma Astra desde el Constructor visual
 * y lo reemplaza por cipba_render_footer_columns().
 */
function cipba_replace_primary_footer() {
	if ( class_exists( 'Astra_Builder_Footer' ) ) {
		$instance = Astra_Builder_Footer::get_instance();
		remove_action( 'astra_primary_footer', array( $instance, 'primary_footer' ), 10 );
		remove_action( 'astra_below_footer', array( $instance, 'below_footer' ), 10 );
	}
	add_action( 'astra_primary_footer', 'cipba_render_footer_columns', 10 );
	add_action( 'astra_below_footer', 'cipba_render_footer_below', 10 );
}
add_action( 'wp', 'cipba_replace_primary_footer' );

/**
 * Links de interés del pie: salen del menú **Links de interés** del panel
 * (nombre, dirección y orden). Todos abren en pestaña nueva; la clase
 * `abre-nuevo` les pone el ícono de enlace externo.
 *
 * @return array[] Cada item: título y url.
 */
function cipba_get_links_interes() {
	$posts = get_posts( array(
		'post_type'   => 'link_interes',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby'     => 'menu_order title',
		'order'       => 'ASC',
	) );
	$out = array();
	foreach ( $posts as $p ) {
		$url = trim( (string) get_post_meta( $p->ID, 'url', true ) );
		if ( '' !== $url ) {
			$out[] = array( 'titulo' => get_the_title( $p ), 'url' => $url );
		}
	}
	return $out;
}

/**
 * Marca + 3 menús del pie (Trámites, Normativa, Institucional) + la columna
 * de Links de interés, en grilla 1.5fr + 4×1fr como en el diseño.
 */
function cipba_render_footer_columns() {
	$columnas = array(
		'Pie - Trámites'          => 'Trámites',
		'Pie - Normativa'         => 'Normativa',
		'Pie - Institucional'     => 'Institucional',
	);
	$links = cipba_get_links_interes();
	?>
	<div class="cipba-footer-wrap">
		<div class="cipba-footer-grid">
			<div class="cipba-footer-brand">
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/logo-blanco.svg' ); ?>" alt="CIPBA Distrito VII" class="cipba-footer-logo" />
				<p>Entidad pública no estatal creada por Ley 10.416. Ejercicio profesional habilitado en 23 partidos de la zona oeste y norte del Gran Buenos Aires.</p>
				<div style="margin-top:16px"><?php echo cipba_social_icons( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>
			<?php foreach ( $columnas as $menu_name => $titulo ) :
				if ( ! wp_get_nav_menu_object( $menu_name ) ) {
					continue;
				}
				?>
				<div class="cipba-footer-col">
					<h3><?php echo esc_html( $titulo ); ?></h3>
					<?php
					wp_nav_menu( array(
						'menu'        => $menu_name,
						'container'   => false,
						'items_wrap'  => '<ul class="cipba-footer-links">%3$s</ul>',
						'fallback_cb' => false,
					) );
					?>
				</div>
			<?php endforeach; ?>
			<?php if ( $links ) : ?>
				<div class="cipba-footer-col">
					<h3>Links de interés</h3>
					<ul class="cipba-footer-links">
						<?php foreach ( $links as $l ) : ?>
							<li class="abre-nuevo"><a href="<?php echo esc_url( $l['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $l['titulo'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * "Below Footer": copyright a la izquierda, enlaces legales a la
 * derecha (menú "Pie - Legal"), fondo verde-950. Reemplaza el
 * "Copyright" por defecto de Astra (que traía "Powered by Astra").
 */
function cipba_render_footer_below() {
	?>
	<div class="cipba-footer-below">
		<div class="cipba-footer-below__inner">
			<span>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> Colegio de Ingenieros de la Provincia de Buenos Aires — Distrito VII</span>
			<?php
			wp_nav_menu( array(
				'menu'        => 'Pie - Legal',
				'container'   => false,
				'items_wrap'  => '<ul class="cipba-footer-below__links">%3$s</ul>',
				'fallback_cb' => false,
			) );
			?>
		</div>
	</div>
	<?php
}
