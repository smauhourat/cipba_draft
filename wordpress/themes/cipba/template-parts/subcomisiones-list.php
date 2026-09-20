<?php
/**
 * Listado completo de subcomisiones (página /subcomisiones/).
 * Se renderiza con [cipba_subcomisiones_listado].
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query = new WP_Query( array(
	'post_type'      => 'subcomision',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order title',
	'order'          => 'ASC',
) );

if ( ! $query->have_posts() ) {
	return;
}

$items     = array();
$total_ref = 0;
while ( $query->have_posts() ) {
	$query->the_post();
	$refs = cipba_get_referentes();

	$tag     = rwmb_meta( 'tag' );
	$items[] = array(
		'nombre' => get_the_title(),
		'tag'    => $tag ? $tag : mb_substr( get_the_title(), 0, 3 ),
		'mail'   => trim( (string) get_post_meta( get_the_ID(), 'mail', true ) ),
		'refs'   => $refs,
	);
	$total_ref += count( $refs );
}
wp_reset_postdata();

$count = count( $items );
?>
<section id="listado" class="cipba-subcom-list">
	<div class="cipba-subcom-toolbar">
		<div>
			<div class="cipba-subcom-eyebrow">Listado completo</div>
			<h2><?php echo (int) $count; ?> subcomisiones · <?php echo (int) $total_ref; ?> referentes</h2>
			<p>Buscá por especialidad o por apellido del referente. Los datos de contacto son los oficiales informados al colegio.</p>
		</div>
		<label class="cipba-subcom-search">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
			<input type="search" data-subcom-search placeholder="Buscar por especialidad o apellido…" aria-label="Buscar subcomisión">
		</label>
	</div>

	<div class="cipba-subcom-status" data-subcom-status aria-live="polite"></div>

	<div class="cipba-subcom-full-grid">
		<?php foreach ( $items as $s ) :
			$haystack = $s['nombre'] . ' ' . $s['tag'] . ' ' . $s['mail'] . ' ' . implode( ' ', wp_list_pluck( $s['refs'], 'nombre' ) ) . ' ' . implode( ' ', wp_list_pluck( $s['refs'], 'mail' ) );
			?>
			<article class="cipba-subcom-full" data-search="<?php echo esc_attr( mb_strtolower( $haystack ) ); ?>">
				<header class="cipba-subcom-full__head">
					<div class="cipba-subcom-full__tag<?php echo mb_strlen( $s['tag'] ) > 3 ? ' is-long' : ''; ?>"><?php echo esc_html( $s['tag'] ); ?></div>
					<div>
						<div class="cipba-subcom-full__eyebrow">Subcomisión</div>
						<h3><?php echo esc_html( $s['nombre'] ); ?></h3>
						<?php if ( $s['mail'] ) : ?>
							<a class="cipba-subcom-full__mail" href="mailto:<?php echo esc_attr( antispambot( $s['mail'] ) ); ?>"><?php echo esc_html( $s['mail'] ); ?></a>
						<?php endif; ?>
					</div>
				</header>
				<div class="cipba-subcom-full__refs">
					<?php foreach ( $s['refs'] as $r ) :
						$tel  = isset( $r['telefono'] ) ? $r['telefono'] : '';
						$mail = isset( $r['mail'] ) ? $r['mail'] : '';
						?>
						<div class="cipba-subcom-ref">
							<div class="cipba-avatar"><?php echo esc_html( cipba_initials( $r['nombre'] ) ); ?></div>
							<div class="cipba-subcom-ref__body">
								<div class="cipba-subcom-ref__name">Ing. <?php echo esc_html( $r['nombre'] ); ?></div>
								<?php if ( ! empty( $r['matricula'] ) ) : ?>
									<div class="cipba-subcom-ref__mat">MAT. <?php echo esc_html( $r['matricula'] ); ?></div>
								<?php endif; ?>
								<?php if ( $tel ) : ?>
									<a class="cipba-subcom-ref__tel" href="tel:<?php echo esc_attr( cipba_tel_link( $tel ) ); ?>"><?php echo esc_html( $tel ); ?></a>
								<?php endif; ?>
								<?php if ( $mail ) : ?>
									<a class="cipba-subcom-ref__mail" href="mailto:<?php echo esc_attr( antispambot( $mail ) ); ?>"><?php echo esc_html( $mail ); ?></a>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>

	<div class="cipba-subcom-empty" data-subcom-empty hidden>
		<strong>No encontramos resultados</strong>
		Probá con otro término, por ejemplo: "civil", "agrimensura" o un apellido.
	</div>
</section>
