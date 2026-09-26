<?php
/**
 * Listado de eventos y novedades (página /novedades/): encabezado y tabs por
 * tipo de publicación ("Eventos y novedades" / "Noticias", ver ?tipo= en
 * cipba_novedades_tipo_actual()), barra de filtros con categorías y buscador,
 * publicación destacada y grilla de tarjetas. Se renderiza con
 * [cipba_novedades]; el filtrado por categoría/búsqueda lo hace assets/js/novedades.js.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tabs = cipba_novedades_tabs();
$tipo = cipba_novedades_tipo_actual();
$tab  = $tabs[ $tipo ];

$posts = get_posts( array(
	'post_type'   => 'post',
	'post_status' => 'publish',
	'numberposts' => -1,
	'orderby'     => 'date',
	'order'       => 'DESC',
	'meta_query'  => cipba_meta_query_tipo( $tipo ),
) );

// La destacada: la más reciente marcada como tal (dentro de este tab).
$dest_id = 0;
foreach ( $posts as $p ) {
	if ( get_post_meta( $p->ID, 'destacada', true ) ) {
		$dest_id = $p->ID;
		break;
	}
}
$total = count( $posts );

// Categorías presentes en este tab (para no ofrecer filtros vacíos).
$cats_presentes = array();
foreach ( $posts as $p ) {
	$c = cipba_novedad_categoria( $p->ID );
	if ( $c ) {
		$cats_presentes[ $c->term_id ] = true;
	}
}
$categorias = array_filter( cipba_novedades_categorias(), function ( $c ) use ( $cats_presentes ) {
	return isset( $cats_presentes[ $c->term_id ] );
} );
?>
<div class="cipba-pagehead">
	<div class="cipba-pagehead__crumbs">
		<div class="cipba-pagehead__inner">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a><span>›</span>
			<span>Novedades</span><span>›</span>
			<strong><?php echo esc_html( $tab['label'] ); ?></strong>
		</div>
	</div>
	<header class="cipba-pagehead__title">
		<div class="cipba-pagehead__inner">
			<h1><?php echo esc_html( $tab['title'] ); ?></h1>
			<p><?php echo esc_html( $tab['desc'] ); ?></p>
		</div>
		<nav class="cipba-nov-tabs" role="tablist" aria-label="Tipo de publicación">
			<div class="cipba-wrap cipba-nov-tabs__inner">
				<?php foreach ( $tabs as $k => $t ) : ?>
					<a href="<?php echo esc_url( cipba_novedades_url( $k ) ); ?>" role="tab" aria-selected="<?php echo $k === $tipo ? 'true' : 'false'; ?>" class="cipba-nov-tab<?php echo $k === $tipo ? ' is-active' : ''; ?>"><?php echo esc_html( $t['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</nav>
	</header>
</div>

<div class="cipba-nov" data-nov-total="<?php echo (int) $total; ?>">
	<div class="cipba-nov-filtros">
		<div class="cipba-nov-filtros__inner">
			<div class="cipba-nov-chips" role="group" aria-label="Filtrar por categoría">
				<button type="button" class="cipba-chip is-active" data-nov-cat="">Todas</button>
				<?php foreach ( $categorias as $c ) : ?>
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
