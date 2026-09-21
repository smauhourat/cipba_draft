<?php
/**
 * Detalle de una novedad o evento (entrada nativa): encabezado, imagen, texto,
 * y en la columna lateral la ficha de la actividad, el botón de acción y
 * compartir; al pie, otras novedades.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	$mail    = trim( cipba_dato( 'email' ) );
	$url     = get_permalink( $id );
	$titulo  = get_the_title();
	$share   = array(
		array( 'WhatsApp', 'whatsapp', 'https://wa.me/?text=' . rawurlencode( $titulo . ' ' . $url ) ),
		array( 'LinkedIn', 'linkedin', 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $url ) ),
		array( 'Facebook', 'facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url ) ),
		array( 'Correo', 'mail', 'mailto:?subject=' . rawurlencode( $titulo ) . '&body=' . rawurlencode( $url ) ),
	);
	?>
	<div id="primary" class="content-area primary">
		<main id="main" class="site-main cipba-nota">

			<div class="cipba-pagehead">
				<div class="cipba-pagehead__crumbs">
					<div class="cipba-pagehead__inner">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo cipba_icon( 'home', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Inicio</a>
						<span>›</span><a href="<?php echo esc_url( home_url( '/novedades/' ) ); ?>">Novedades</a>
						<?php if ( $cat ) : ?><span>›</span><strong><?php echo esc_html( $cat->name ); ?></strong><?php endif; ?>
					</div>
				</div>
			</div>

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

					<aside class="cipba-nota__aside">
						<?php if ( $filas ) : ?>
							<div class="cipba-ficha">
								<h3>Datos de la actividad</h3>
								<?php foreach ( $filas as $f ) : ?>
									<div class="cipba-ficha__row">
										<span class="cipba-ficha__ico"><?php echo cipba_icon( $f[0], 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										<div><div class="cipba-ficha__lbl"><?php echo esc_html( $f[1] ); ?></div><div class="cipba-ficha__val"><?php echo esc_html( $f[2] ); ?></div></div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<div class="cipba-nota__cta">
							<h3><?php echo $insc ? 'Inscripción' : 'Más información'; ?></h3>
							<p><?php echo $insc ? 'La inscripción se confirma por mail una vez verificada la matrícula. Si el cupo está completo, quedás en lista de espera.' : 'Para consultas sobre esta publicación podés escribirnos o comunicarte con el área correspondiente.'; ?></p>
							<a class="cipba-nota__btn" href="<?php echo esc_url( $cta[1] ); ?>"<?php echo 0 === strpos( $cta[1], home_url() ) ? '' : ' target="_blank" rel="noopener"'; ?>><?php echo esc_html( $cta[0] ); ?> <?php echo cipba_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<?php if ( $mail ) : ?><a class="cipba-nota__mail" href="mailto:<?php echo esc_attr( antispambot( $mail ) ); ?>"><?php echo cipba_icon( 'mail', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $mail ); ?></a><?php endif; ?>
						</div>

						<div class="cipba-share">
							<h3>Compartir</h3>
							<div class="cipba-share__row">
								<?php foreach ( $share as $s ) : ?>
									<a href="<?php echo esc_url( $s[2] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $s[0] ); ?>" title="<?php echo esc_attr( $s[0] ); ?>"><?php echo cipba_icon( $s[1], 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
								<?php endforeach; ?>
								<button type="button" class="cipba-share__copy" data-copy-url="<?php echo esc_url( $url ); ?>"><?php echo cipba_icon( 'copy', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span>Copiar enlace</span></button>
							</div>
						</div>

						<a class="cipba-nota__back" href="<?php echo esc_url( home_url( '/novedades/' ) ); ?>"><?php echo cipba_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Volver a todas las novedades</a>
					</aside>
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
							<a href="<?php echo esc_url( home_url( '/novedades/' ) ); ?>">Ver listado completo <?php echo cipba_icon( 'chevron', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						</div>
						<div class="cipba-nov-grid">
							<?php foreach ( $otras as $o ) { cipba_render_nov_card( $o->ID ); } ?>
						</div>
					</div>
				</section>
			<?php endif; ?>

		</main>
	</div>
	<?php
endwhile;

get_footer();
