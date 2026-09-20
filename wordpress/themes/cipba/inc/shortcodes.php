<?php
/**
 * Shortcodes para secciones de la home que necesitan datos dinámicos
 * (Query Loop real, no contenido fijo) — eventos, y a futuro noticias
 * y subcomisiones si hace falta más control del que da el bloque nativo.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cipba_fecha_corta( $fecha ) {
	if ( ! $fecha ) {
		return '';
	}
	$meses = array( 1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic' );
	$ts = strtotime( $fecha );
	if ( ! $ts ) {
		return '';
	}
	return (int) date( 'j', $ts ) . ' ' . $meses[ (int) date( 'n', $ts ) ] . ' ' . date( 'Y', $ts );
}

/**
 * [cipba_eventos] — grilla de 3 próximos eventos (CPT evento), ordenados
 * por fecha_inicio ascendente, filtrando los que ya pasaron.
 */
function cipba_eventos_shortcode() {
	$query = new WP_Query( array(
		'post_type'      => 'evento',
		'posts_per_page' => 3,
		'meta_key'       => 'fecha_inicio',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => 'fecha_inicio',
				'value'   => current_time( 'Y-m-d' ),
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	) );

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-ev-grid">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<a href="<?php the_permalink(); ?>" class="cipba-ev-card">
				<div class="cipba-ev-card__img">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'cipba-card' ); ?>
					<?php endif; ?>
				</div>
				<div class="cipba-ev-card__body">
					<h3><?php the_title(); ?></h3>
					<div class="cipba-ev-card__foot">
						<span class="cipba-ev-card__date"><?php echo esc_html( cipba_fecha_corta( get_post_meta( get_the_ID(), 'fecha_inicio', true ) ) ); ?></span>
						<span class="cipba-ev-card__more">Ver detalle ›</span>
					</div>
				</div>
			</a>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'cipba_eventos', 'cipba_eventos_shortcode' );

/**
 * [cipba_noticias] — últimas 5 entradas nativas (post), con categoría
 * coloreada (§5.5), fecha d/m/Y, imagen, título y extracto a 2 líneas.
 */
function cipba_noticias_shortcode() {
	$colores = array(
		'institucional' => '#14484a',
		'capacitacion'  => '#00a48a',
		'normativa'     => '#8a3a2a',
		'matricula'     => '#00a48a',
		'comisiones'    => '#484586',
	);

	$query = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 5,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-news-grid">
		<?php while ( $query->have_posts() ) : $query->the_post();
			$cats  = get_the_category();
			$cat   = $cats ? $cats[0] : null;
			$color = $cat && isset( $colores[ $cat->slug ] ) ? $colores[ $cat->slug ] : '#607a7c';
			?>
			<a href="<?php the_permalink(); ?>" class="cipba-news-card">
				<div class="cipba-news-card__meta">
					<?php if ( $cat ) : ?>
						<span class="cipba-news-card__cat" style="color:<?php echo esc_attr( $color ); ?>"><?php echo esc_html( $cat->name ); ?></span>
						<span class="cipba-news-card__sep">· <?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
					<?php else : ?>
						<span class="cipba-news-card__sep"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
					<?php endif; ?>
				</div>
				<div class="cipba-news-card__img">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'cipba-thumb' ); ?>
					<?php endif; ?>
				</div>
				<div class="cipba-news-card__body">
					<h3><?php the_title(); ?></h3>
					<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				</div>
			</a>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'cipba_noticias', 'cipba_noticias_shortcode' );

/**
 * [cipba_subcomisiones] — 3 subcomisiones (CPT subcomision), tarjeta
 * simple: título, descripción (si está cargada) y mail de la subcomisión. Todas enlazan al
 * listado completo (así lo hace el prototipo, no a fichas individuales).
 */
function cipba_subcomisiones_shortcode() {
	$query = new WP_Query( array(
		'post_type'      => 'subcomision',
		'posts_per_page' => 3,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	) );

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-subcom-grid">
		<?php while ( $query->have_posts() ) : $query->the_post();
			$mail = trim( (string) get_post_meta( get_the_ID(), 'mail', true ) );
			?>
			<a href="/subcomisiones/" class="cipba-subcom-card">
				<h3><?php the_title(); ?></h3>
				<?php $descripcion = trim( (string) get_post_meta( get_the_ID(), 'descripcion', true ) ); ?>
				<?php if ( $descripcion ) : ?><p><?php echo esc_html( $descripcion ); ?></p><?php endif; ?>
				<?php if ( $mail ) : ?>
					<span><?php echo esc_html( $mail ); ?></span>
				<?php endif; ?>
			</a>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'cipba_subcomisiones', 'cipba_subcomisiones_shortcode' );

/**
 * [cipba_subcomisiones_listado] — página /subcomisiones/: contador, buscador
 * (filtra en el navegador por especialidad, sigla, referente o mail) y grilla
 * de tarjetas con todos los referentes. El markup vive en
 * template-parts/subcomisiones-list.php. Todo sale del CPT `subcomision`.
 */
function cipba_subcomisiones_listado_shortcode() {
	wp_enqueue_script( 'cipba-subcomisiones', get_stylesheet_directory_uri() . '/assets/js/subcomisiones.js', array(), CHILD_THEME_CIPBA_VERSION, true );

	ob_start();
	get_template_part( 'template-parts/subcomisiones-list' );
	return ob_get_clean();
}
add_shortcode( 'cipba_subcomisiones_listado', 'cipba_subcomisiones_listado_shortcode' );

/**
 * [cipba_sedes] — sedes y delegaciones (CPT `sede`): la Casa Central como
 * tarjeta ancha con contactos directos y el resto en grilla de 3 columnas.
 * El markup vive en template-parts/sedes-list.php.
 */
function cipba_sedes_shortcode() {
	ob_start();
	get_template_part( 'template-parts/sedes-list' );
	return ob_get_clean();
}
add_shortcode( 'cipba_sedes', 'cipba_sedes_shortcode' );

/**
 * Sedes en la home ("El Distrito VII"): mismos datos que la página de
 * Institucional (CPT `sede`), en dos piezas porque el diseño las separa.
 *
 * [cipba_sede_principal] — tarjeta oscura con la sede marcada como Casa
 *   Central: dirección, teléfono, correo y horario.
 * [cipba_delegaciones]   — grilla con el resto de las sedes: tipo, nombre,
 *   dirección y horario.
 */
function cipba_get_sedes() {
	return get_posts( array(
		'post_type'   => 'sede',
		'numberposts' => -1,
		'orderby'     => 'menu_order title',
		'order'       => 'ASC',
	) );
}

function cipba_sede_principal_shortcode() {
	$principal = null;
	foreach ( cipba_get_sedes() as $sede ) {
		if ( get_post_meta( $sede->ID, 'destacada', true ) ) {
			$principal = $sede;
			break;
		}
	}
	if ( ! $principal ) {
		return '';
	}

	$id   = $principal->ID;
	$rows = array(
		array( 'location', 'Dirección', trim( (string) get_post_meta( $id, 'direccion', true ) ), false ),
		array( 'phone', 'Teléfono', trim( (string) get_post_meta( $id, 'telefono', true ) ), false ),
		array( 'mail', 'Correo electrónico', trim( (string) get_post_meta( $id, 'email', true ) ), true ),
		array( 'clock', 'Horario de atención', trim( (string) get_post_meta( $id, 'horario', true ) ), false ),
	);

	ob_start();
	?>
	<div class="cipba-dist-sede">
		<h3><?php echo esc_html( get_the_title( $id ) ); ?></h3>
		<div class="cipba-dist-sede__badge"><?php echo esc_html( get_post_meta( $id, 'tag', true ) ); ?></div>
		<?php foreach ( $rows as $r ) :
			if ( '' === $r[2] ) {
				continue;
			}
			?>
			<div class="cipba-dist-sede__row"><span class="ico"><?php echo cipba_icon( $r[0], 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><div><div class="lbl"><?php echo esc_html( $r[1] ); ?></div><div class="val"><?php
				if ( $r[3] ) {
					echo '<a href="mailto:' . esc_attr( antispambot( $r[2] ) ) . '">' . esc_html( $r[2] ) . '</a>';
				} else {
					echo esc_html( $r[2] );
				}
			?></div></div></div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cipba_sede_principal', 'cipba_sede_principal_shortcode' );

function cipba_delegaciones_shortcode() {
	$cards = array();
	foreach ( cipba_get_sedes() as $sede ) {
		if ( get_post_meta( $sede->ID, 'destacada', true ) ) {
			continue;
		}
		$cards[] = $sede;
	}
	if ( ! $cards ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-sede-grid">
		<?php foreach ( $cards as $sede ) :
			$lines = array_filter( array(
				trim( (string) get_post_meta( $sede->ID, 'direccion', true ) ),
				trim( (string) get_post_meta( $sede->ID, 'horario', true ) ),
			) );
			?>
			<div class="cipba-sede-card">
				<h3><?php echo esc_html( get_the_title( $sede ) ); ?></h3>
				<div class="cipba-sede-card__role"><?php echo esc_html( get_post_meta( $sede->ID, 'tag', true ) ); ?></div>
				<?php foreach ( $lines as $line ) : ?>
					<div class="cipba-sede-card__line"><?php echo esc_html( $line ); ?></div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cipba_delegaciones', 'cipba_delegaciones_shortcode' );

/**
 * [cipba_autoridades] — autoridades del Consejo Directivo (CPT `autoridad`):
 * el presidente como tarjeta destacada de doble ancho y el resto en grilla.
 * El markup vive en template-parts/autoridades-list.php.
 */
function cipba_autoridades_shortcode() {
	ob_start();
	get_template_part( 'template-parts/autoridades-list' );
	return ob_get_clean();
}
add_shortcode( 'cipba_autoridades', 'cipba_autoridades_shortcode' );
