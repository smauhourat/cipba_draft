<?php
/**
 * Detalle de una novedad o evento (entrada nativa).
 * Noticias: encabezado, imagen apaisada, texto y en la columna lateral la ficha
 * de la actividad, el botón de acción y compartir; al pie, otras novedades.
 * Eventos y novedades (flyers de Instagram 4:5 o 9:16): layout 2/3 – 1/3; en
 * 2/3 la publicación con la imagen completa (sin recortar) y debajo del texto
 * ficha/acción/compartir; en 1/3 los últimos eventos y el link al listado.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Botón "Copiar enlace" de la tarjeta Compartir.
wp_enqueue_script( 'cipba-novedades', get_stylesheet_directory_uri() . '/assets/js/novedades.js', array(), CHILD_THEME_CIPBA_VERSION, true );

get_header();

while ( have_posts() ) :
	the_post();

	$id      = get_the_ID();
	$cat     = cipba_novedad_categoria( $id );
	$bajada  = cipba_bajada( $id );
	$es_act  = (bool) get_post_meta( $id, 'es_actividad', true );
	$filas   = $es_act ? cipba_actividad_filas( $id ) : array();
	$cta     = cipba_novedad_cta( $id );
	$insc    = $es_act && get_post_meta( $id, 'inscripcion_abierta', true );
	$mail    = trim( (string) get_post_meta( $id, 'contacto_email', true ) );
	$mail    = $mail ? $mail : trim( cipba_dato( 'email' ) );
	$tel     = trim( (string) get_post_meta( $id, 'contacto_telefono', true ) );
	$tel_url = cipba_tel_link( $tel );
	$url     = get_permalink( $id );
	$titulo  = get_the_title();
	$tipo    = cipba_tipo_pub( $id );
	$evento  = 'evento' === $tipo;
	$nov_url = cipba_novedades_url( $tipo ); // Vuelve al tab (Eventos/Noticias) que corresponde a esta publicación.
	$share   = array(
		array( 'WhatsApp', 'whatsapp', 'https://wa.me/?text=' . rawurlencode( $titulo . ' ' . $url ) ),
		array( 'LinkedIn', 'linkedin', 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $url ) ),
		array( 'Facebook', 'facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url ) ),
		array( 'Correo', 'mail', 'mailto:?subject=' . rawurlencode( $titulo ) . '&body=' . rawurlencode( $url ) ),
	);

	// Ficha de la actividad, recuadro de contacto/inscripción, compartir y volver:
	// en Noticias van en la columna lateral; en Eventos, debajo del texto.
	$extras = function () use ( $id, $filas, $insc, $cta, $mail, $tel, $tel_url, $share, $url, $nov_url ) {
		if ( $filas ) :
			?>
			<div class="cipba-ficha">
				<h3>Datos de la actividad</h3>
				<?php foreach ( $filas as $f ) : ?>
					<div class="cipba-ficha__row">
						<span class="cipba-ficha__ico"><?php echo cipba_icon( $f[0], 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<div><div class="cipba-ficha__lbl"><?php echo esc_html( $f[1] ); ?></div><div class="cipba-ficha__val"><?php echo esc_html( $f[2] ); ?></div></div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php
		endif;

		if ( ! get_post_meta( $id, 'ocultar_info', true ) ) :
			?>
			<div class="cipba-nota__cta">
				<h3><?php echo $insc ? 'Inscripción' : 'Más información'; ?></h3>
				<?php
				$info = trim( (string) get_post_meta( $id, 'info_texto', true ) );
				if ( '' === $info ) {
					$info = $insc ? 'La inscripción se confirma por mail una vez verificada la matrícula. Si el cupo está completo, quedás en lista de espera.' : 'Para consultas sobre esta publicación podés escribirnos o comunicarte con el área correspondiente.';
				}
				?>
				<p><?php echo nl2br( esc_html( $info ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<a class="cipba-nota__btn" href="<?php echo esc_url( $cta[1] ); ?>"<?php echo 0 === strpos( $cta[1], home_url() ) ? '' : ' target="_blank" rel="noopener"'; ?>><?php echo esc_html( $cta[0] ); ?> <?php echo cipba_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php if ( $mail ) : ?><a class="cipba-nota__mail" href="mailto:<?php echo esc_attr( antispambot( $mail ) ); ?>"><?php echo cipba_icon( 'mail', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $mail ); ?></a><?php endif; ?>
				<?php if ( $tel_url ) : ?><a class="cipba-nota__mail" href="<?php echo esc_url( 'tel:' . $tel_url ); ?>"><?php echo cipba_icon( 'phone', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $tel ); ?></a><?php endif; ?>
			</div>
			<?php
		endif;
		?>
		<div class="cipba-share">
			<h3>Compartir</h3>
			<div class="cipba-share__row">
				<?php foreach ( $share as $s ) : ?>
					<a href="<?php echo esc_url( $s[2] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $s[0] ); ?>" title="<?php echo esc_attr( $s[0] ); ?>"><?php echo cipba_icon( $s[1], 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endforeach; ?>
				<button type="button" class="cipba-share__copy" data-copy-url="<?php echo esc_url( $url ); ?>"><?php echo cipba_icon( 'copy', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span>Copiar enlace</span></button>
			</div>
		</div>

		<a class="cipba-nota__back" href="<?php echo esc_url( $nov_url ); ?>"><?php echo cipba_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Volver a todas las novedades</a>
		<?php
	};
	?>
	<div id="primary" class="content-area primary">
		<main id="main" class="site-main cipba-nota">

			<div class="cipba-pagehead">
				<div class="cipba-pagehead__crumbs">
					<div class="cipba-pagehead__inner">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo cipba_icon( 'home', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Inicio</a>
						<span>›</span><a href="<?php echo esc_url( $nov_url ); ?>">Novedades</a>
						<?php if ( $cat ) : ?><span>›</span><strong><?php echo esc_html( $cat->name ); ?></strong><?php endif; ?>
					</div>
				</div>
			</div>

			<?php if ( $evento ) : ?>

				<section class="cipba-ev-det">
					<div class="cipba-nota__inner cipba-ev-det__grid">
						<div class="cipba-ev-det__main">
							<header class="cipba-ev-det__head">
								<div class="cipba-nota__meta">
									<?php echo cipba_cat_badge( $id, 11.5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<span><?php echo cipba_icon( 'calendar', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( cipba_fecha_novedad( $id ) ); ?></span>
								</div>
								<h1><?php echo esc_html( $titulo ); ?></h1>
								<?php if ( $bajada ) : ?><p class="cipba-nota__lead"><?php echo esc_html( $bajada ); ?></p><?php endif; ?>
							</header>

							<?php if ( has_post_thumbnail() ) : ?>
								<figure class="cipba-ev-det__flyer"><?php the_post_thumbnail( 'cipba-flyer' ); ?></figure>
							<?php endif; ?>

							<article class="cipba-nota__cuerpo"><?php the_content(); ?></article>

							<div class="cipba-ev-det__extras"><?php $extras(); ?></div>
						</div>

						<aside class="cipba-ev-det__side"><?php cipba_render_nov_lista_lateral( $id ); ?></aside>
					</div>
				</section>

			<?php else : ?>

				<header class="cipba-nota__head">
					<div class="cipba-nota__inner">
						<div class="cipba-nota__meta">
							<?php echo cipba_cat_badge( $id, 11.5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span><?php echo cipba_icon( 'calendar', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( cipba_fecha_novedad( $id ) ); ?></span>
						</div>
						<h1><?php echo esc_html( $titulo ); ?></h1>
						<?php if ( $bajada ) : ?><p class="cipba-nota__lead"><?php echo esc_html( $bajada ); ?></p><?php endif; ?>
					</div>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="cipba-nota__hero">
						<div class="cipba-nota__inner"><div class="cipba-nota__hero-img"><?php the_post_thumbnail( 'cipba-nov-hero' ); ?></div></div>
					</div>
				<?php endif; ?>

				<section class="cipba-nota__main">
					<div class="cipba-nota__inner cipba-nota__grid">
						<article class="cipba-nota__cuerpo"><?php the_content(); ?></article>

						<aside class="cipba-nota__aside"><?php $extras(); ?></aside>
					</div>
				</section>

				<?php
				// Otras novedades: primero las de la misma categoría.
				$otras = array();
				if ( $cat ) {
					$otras = get_posts( array( 'post_type' => 'post', 'numberposts' => 3, 'post__not_in' => array( $id ), 'category' => $cat->term_id ) );
				}
				if ( count( $otras ) < 3 ) {
					$ya    = array_merge( array( $id ), wp_list_pluck( $otras, 'ID' ) );
					$otras = array_merge( $otras, get_posts( array( 'post_type' => 'post', 'numberposts' => 3 - count( $otras ), 'post__not_in' => $ya ) ) );
				}
				if ( $otras ) :
					?>
					<section class="cipba-nota__otras">
						<div class="cipba-nota__inner">
							<div class="cipba-nota__otras-head">
								<h2>Otras novedades</h2>
								<a href="<?php echo esc_url( $nov_url ); ?>">Ver listado completo <?php echo cipba_icon( 'chevron', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							</div>
							<div class="cipba-nov-grid">
								<?php foreach ( $otras as $o ) { cipba_render_nov_card( $o->ID ); } ?>
							</div>
						</div>
					</section>
				<?php endif; ?>

			<?php endif; ?>

		</main>
	</div>
	<?php
endwhile;

get_footer();
