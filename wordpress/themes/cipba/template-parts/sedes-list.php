<?php
/**
 * Grilla de sedes y delegaciones (página Institucional).
 * Se renderiza con [cipba_sedes]. La sede marcada como "Casa Central" va
 * como tarjeta ancha con sus contactos directos.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query = new WP_Query( array(
	'post_type'      => 'sede',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order title',
	'order'          => 'ASC',
) );

if ( ! $query->have_posts() ) {
	return;
}
?>
<div class="cipba-sedes-grid">
	<?php while ( $query->have_posts() ) : $query->the_post();
		$id       = get_the_ID();
		$main     = (bool) get_post_meta( $id, 'destacada', true );
		$contacts = $main ? cipba_get_sede_contactos( $id ) : array();

		$rows = array(
			array( 'location', 'Dirección', trim( (string) get_post_meta( $id, 'direccion', true ) ), 'strong' ),
			array( 'phone', 'Teléfono', trim( (string) get_post_meta( $id, 'telefono', true ) ), '' ),
			array( 'mail', 'Correo electrónico', trim( (string) get_post_meta( $id, 'email', true ) ), 'mail' ),
			array( 'award', 'Visador', trim( (string) get_post_meta( $id, 'visador', true ) ), '' ),
			array( 'clock', 'Horario de atención', trim( (string) get_post_meta( $id, 'horario', true ) ), '' ),
		);
		$rows = array_filter( $rows, function ( $r ) {
			return '' !== $r[2];
		} );
		?>
		<article class="cipba-sede<?php echo $main ? ' cipba-sede--main' : ''; ?>">
			<header class="cipba-sede__head">
				<div>
					<div class="cipba-sede__tag"><?php echo esc_html( get_post_meta( $id, 'tag', true ) ); ?></div>
					<h3><?php the_title(); ?></h3>
				</div>
				<?php if ( $main ) : ?>
					<div class="cipba-sede__badge"><?php echo cipba_icon( 'star', 11 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Casa Central</div>
				<?php endif; ?>
			</header>

			<div class="cipba-sede__body">
				<div class="cipba-sede__info">
					<?php foreach ( $rows as $r ) : ?>
						<div class="cipba-sede__row">
							<span class="cipba-sede__ico"><?php echo cipba_icon( $r[0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<div>
								<div class="cipba-sede__lbl"><?php echo esc_html( $r[1] ); ?></div>
								<div class="cipba-sede__val<?php echo 'strong' === $r[3] ? ' is-strong' : ''; ?>">
									<?php if ( 'mail' === $r[3] ) : ?>
										<a href="mailto:<?php echo esc_attr( antispambot( $r[2] ) ); ?>"><?php echo esc_html( $r[2] ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $r[2] ); ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( $contacts ) : ?>
					<div class="cipba-sede__contacts">
						<div class="cipba-sede__contacts-title"><?php echo cipba_icon( 'users', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Áreas y contactos directos</div>
						<div class="cipba-sede__contacts-list">
							<?php foreach ( $contacts as $c ) : ?>
								<div class="cipba-sede-contact">
									<div class="cipba-sede-contact__top">
										<span class="cipba-sede-contact__name"><?php echo esc_html( $c['nombre'] ); ?></span>
										<?php if ( $c['rol'] ) : ?><span class="cipba-sede-contact__rol"><?php echo esc_html( $c['rol'] ); ?></span><?php endif; ?>
									</div>
									<div class="cipba-sede-contact__links">
										<?php if ( $c['tel'] ) : ?>
											<a href="tel:<?php echo esc_attr( cipba_tel_link( $c['tel'] ) ); ?>"><?php echo cipba_icon( 'phone', 11 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $c['tel'] ); ?></a>
										<?php endif; ?>
										<?php if ( $c['email'] ) : ?>
											<a href="mailto:<?php echo esc_attr( antispambot( $c['email'] ) ); ?>"><?php echo cipba_icon( 'mail', 11 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $c['email'] ); ?></a>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</article>
	<?php endwhile; wp_reset_postdata(); ?>
</div>
