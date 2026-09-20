<?php
/**
 * Grilla de autoridades del Consejo Directivo (página Institucional).
 * Se renderiza con [cipba_autoridades]. La autoridad marcada como
 * "Presidencia" va como tarjeta destacada de doble ancho.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query = new WP_Query( array(
	'post_type'      => 'autoridad',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order title',
	'order'          => 'ASC',
) );

if ( ! $query->have_posts() ) {
	return;
}
?>
<div class="cipba-autoridades-grid">
	<?php while ( $query->have_posts() ) : $query->the_post();
		$id     = get_the_ID();
		$main   = (bool) get_post_meta( $id, 'destacado', true );
		$cargo  = trim( (string) get_post_meta( $id, 'cargo', true ) );
		$titulo = trim( (string) get_post_meta( $id, 'titulo', true ) );
		?>
		<article class="cipba-autoridad<?php echo $main ? ' cipba-autoridad--main' : ''; ?>">
			<?php if ( $main ) : ?>
				<div class="cipba-autoridad__badge"><?php echo cipba_icon( 'star', 11 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Presidencia</div>
			<?php endif; ?>
			<div class="cipba-autoridad__avatar"><?php echo esc_html( cipba_initials_first_two( get_the_title() ) ); ?></div>
			<div class="cipba-autoridad__body">
				<?php if ( $cargo ) : ?><div class="cipba-autoridad__cargo"><?php echo esc_html( $cargo ); ?></div><?php endif; ?>
				<div class="cipba-autoridad__nombre"><?php the_title(); ?></div>
				<?php if ( $titulo ) : ?><div class="cipba-autoridad__titulo"><?php echo esc_html( $titulo ); ?></div><?php endif; ?>
			</div>
		</article>
	<?php endwhile; wp_reset_postdata(); ?>
</div>
