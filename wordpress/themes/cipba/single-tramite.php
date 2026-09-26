<?php
/**
 * Plantilla de un trámite (Inscripción, Rehabilitación, Baja, Credenciales…).
 * Todo el contenido sale del formulario "Datos del trámite" del admin; los
 * textos admiten marcadores {{clave}} de "Datos del Distrito".
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$id           = get_the_ID();
	$eyebrow      = trim( (string) get_post_meta( $id, 'eyebrow', true ) );
	$intro        = trim( (string) get_post_meta( $id, 'intro', true ) );
	$crumb        = trim( (string) get_post_meta( $id, 'breadcrumb', true ) );
	$crumb        = $crumb ? $crumb : get_the_title();
	$costo_titulo = trim( (string) get_post_meta( $id, 'costo_titulo', true ) );
	$costo_titulo = $costo_titulo ? $costo_titulo : 'Costo del trámite';
	$costos       = cipba_get_tramite_costos( $id );
	$bloques      = cipba_get_tramite_bloques( $id );
	$docs         = cipba_get_tramite_documentos( $id );
	$docs_titulo  = trim( (string) get_post_meta( $id, 'docs_titulo', true ) );
	$docs_titulo  = $docs_titulo ? $docs_titulo : 'Formularios y documentación';
	$docs_intro   = trim( (string) get_post_meta( $id, 'docs_intro', true ) );
	$whatsapp     = cipba_dato_url( 'whatsapp' );
	$mail         = trim( cipba_dato( 'email_tramites' ) );
	$mail         = $mail ? $mail : trim( cipba_dato( 'email' ) );
	?>
	<div id="primary" class="content-area primary">
		<main id="main" class="site-main cipba-tramite">

			<div class="cipba-pagehead">
				<div class="cipba-pagehead__crumbs">
					<div class="cipba-pagehead__inner">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo cipba_icon( 'home', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Inicio</a>
						<span>›</span><span>Trámites</span><span>›</span>
						<strong><?php echo cipba_plain( $crumb ); // phpcs:ignore WordPress.Security.EscapeOutput ?></strong>
					</div>
				</div>
				<header class="cipba-pagehead__title">
					<div class="cipba-pagehead__inner">
						<?php if ( $eyebrow ) : ?><div class="cipba-pagehead__eyebrow"><?php echo cipba_plain( $eyebrow ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?>
						<h1><?php echo cipba_plain( get_the_title() ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
						<?php if ( $intro ) : ?><p><?php echo cipba_plain( $intro ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
					</div>
				</header>
			</div>

			<section class="cipba-tramite-main">
				<div class="cipba-wrap">
					<div class="cipba-tramite-grid">

						<div class="cipba-tramite-col">
							<?php foreach ( $bloques as $b ) : ?>
								<?php if ( 'requisitos' === $b['tipo'] ) : ?>
									<div class="cipba-req">
										<?php if ( $b['titulo'] ) : ?><h2<?php echo $b['sub'] ? ' class="has-sub"' : ''; ?>><?php echo cipba_plain( $b['titulo'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2><?php endif; ?>
										<?php if ( $b['sub'] ) : ?><p class="cipba-req__sub"><?php echo cipba_plain( $b['sub'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
										<div class="cipba-req__list"><?php echo cipba_rich( $b['contenido'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
									</div>
								<?php else : ?>
									<div class="cipba-aviso<?php echo 'aviso_ambar' === $b['tipo'] ? ' cipba-aviso--ambar' : ''; ?>">
										<span class="cipba-aviso__ico"><?php echo cipba_icon( 'shield', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										<div class="cipba-aviso__body">
											<?php if ( $b['titulo'] ) : ?><div class="cipba-aviso__title"><?php echo cipba_plain( $b['titulo'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?>
											<div class="cipba-aviso__text"><?php echo cipba_rich( $b['contenido'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>

							<?php if ( $docs ) : ?>
								<div class="cipba-docs">
									<h2><?php echo cipba_plain( $docs_titulo ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
									<?php if ( $docs_intro ) : ?><p class="cipba-docs__intro"><?php echo cipba_plain( $docs_intro ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
									<div class="cipba-docs__grid">
										<?php foreach ( $docs as $d ) : ?>
											<a class="cipba-doc" href="<?php echo esc_url( $d['url'] ); ?>" target="_blank" rel="noopener">
												<span class="cipba-doc__ico"><?php echo cipba_icon( 'file', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
												<span class="cipba-doc__body">
													<span class="cipba-doc__title"><?php echo cipba_plain( $d['titulo'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
													<span class="cipba-doc__meta"><?php echo esc_html( $d['formato'] . ( $d['peso'] ? ' · ' . $d['peso'] : '' ) ); ?></span>
												</span>
												<span class="cipba-doc__go"><?php echo cipba_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
											</a>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<aside class="cipba-tramite-aside">
							<?php if ( $costos ) : ?>
								<div class="cipba-costo">
									<h3><?php echo cipba_plain( $costo_titulo ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3>
									<?php foreach ( $costos as $c ) : ?>
										<div class="cipba-costo__row">
											<?php if ( $c['label'] ) : ?><div class="cipba-costo__label"><?php echo cipba_plain( $c['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?>
											<?php if ( $c['value'] ) : ?><div class="cipba-costo__value"><?php echo cipba_plain( $c['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<div class="cipba-ayuda">
								<h3>¿Dudas con el trámite?</h3>
								<p>El trámite se inicia por mail en el Distrito que corresponde a tu domicilio legal. Escribinos y te orientamos.</p>
								<?php if ( $mail ) : ?>
									<a href="mailto:<?php echo esc_attr( antispambot( $mail ) ); ?>"><span class="ico"><?php echo cipba_icon( 'mail', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span> <?php echo esc_html( $mail ); ?></a>
								<?php endif; ?>
								<a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>"><span class="ico"><?php echo cipba_icon( 'users', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span> Contacto por área</a>
								<?php if ( $whatsapp ) : ?>
									<a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener"><span class="ico ico--wa"><?php echo cipba_icon( 'whatsapp', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span> Consultar por WhatsApp</a>
								<?php endif; ?>
							</div>
						</aside>

					</div>
				</div>
			</section>

			<?php
			$otros = new WP_Query( array(
				'post_type'      => 'tramite',
				'posts_per_page' => -1,
				'post__not_in'   => array( $id ),
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
			) );
			if ( $otros->have_posts() ) :
				?>
				<section class="cipba-otros">
					<div class="cipba-wrap">
						<h2>Otros trámites</h2>
						<div class="cipba-otros__grid">
							<?php while ( $otros->have_posts() ) : $otros->the_post();
								$icono = get_post_meta( get_the_ID(), 'icono', true );
								?>
								<a href="<?php the_permalink(); ?>">
									<span class="cipba-otros__ico"><?php echo cipba_icon( $icono ? $icono : 'file', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<span><?php echo cipba_plain( get_the_title() ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								</a>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					</div>
				</section>
			<?php endif; ?>

		</main>
	</div>
	<?php
endwhile;

get_footer();
