<?php
/**
 * Listado de eventos y novedades (página /novedades/): barra de filtros con
 * categorías y buscador, publicación destacada y grilla de tarjetas.
 * Se renderiza con [cipba_novedades]; el filtrado lo hace assets/js/novedades.js.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts = get_posts( array(
	'post_type'   => 'post',
	'post_status' => 'publish',
	'numberposts' => -1,
	'orderby'     => 'date',
	'order'       => 'DESC',
) );

// La destacada: la más reciente marcada como tal.
$dest_id = 0;
foreach ( $posts as $p ) {
	if ( get_post_meta( $p->ID, 'destacada', true ) ) {
		$dest_id = $p->ID;
		break;
	}
}
$total = count( $posts );
?>
<div class="cipba-nov" data-nov-total="<?php echo (int) $total; ?>">
	<div class="cipba-nov-filtros">
		<div class="cipba-nov-filtros__inner">
			<div class="cipba-nov-chips" role="group" aria-label="Filtrar por categoría">
				<button type="button" class="cipba-chip is-active" data-nov-cat="">Todas</button>
				<?php foreach ( cipba_novedades_categorias() as $c ) : ?>
					<button type="button" class="cipba-chip" data-nov-cat="<?php echo esc_attr( $c->slug ); ?>" data-nov-name="<?php echo esc_attr( $c->name ); ?>"><?php echo esc_html( $c->name ); ?></button>
				<?php endforeach; ?>
			</div>
			<label class="cipba-nov-search">
				<?php echo cipba_icon( 'search', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<input type="search" data-nov-search placeholder="Buscar publicación…" aria-label="Buscar publicación">
			</label>
		</div>
	</div>

	<div class="cipba-nov-body">
		<div class="cipba-wrap">
			<div class="cipba-nov-count" data-nov-count aria-live="polite"><?php echo (int) $total; ?> <?php echo 1 === $total ? 'publicación' : 'publicaciones'; ?></div>

			<?php if ( $dest_id ) { cipba_render_nov_dest( $dest_id ); } ?>

			<div class="cipba-nov-grid">
				<?php foreach ( $posts as $p ) {
					if ( $p->ID !== $dest_id ) {
						cipba_render_nov_card( $p->ID );
					}
				} ?>
			</div>

			<div class="cipba-nov-empty" data-nov-empty<?php echo $total ? ' hidden' : ''; ?>>
				<strong>No hay publicaciones para esa búsqueda</strong>
				<p>Probá con otra categoría o borrá el texto del buscador.</p>
			</div>
		</div>
	</div>
</div>
