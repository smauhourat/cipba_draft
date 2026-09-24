<?php
/**
 * Partidos comprendidos por el Distrito VII (página Institucional).
 * Se renderiza con [cipba_partidos]. La lista de partidos es fija (la
 * define la ley de creación del Distrito), por eso va hardcodeada acá en
 * vez de salir de un CPT o de Datos del Distrito.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$partidos = array(
	'Escobar', 'Exaltación de la Cruz', 'General Las Heras', 'General Rodríguez',
	'General San Martín', 'Hurlingham', 'Ituzaingó', 'José C. Paz',
	'La Matanza', 'Luján', 'Malvinas Argentinas', 'Marcos Paz',
	'Mercedes', 'Merlo', 'Moreno', 'Morón',
	'Pilar', 'San Isidro', 'San Fernando', 'San Miguel',
	'Tigre', 'Tres de Febrero', 'Vicente López',
);
$total = count( $partidos );
?>
<div class="cipba-partidos-grid">
	<div class="cipba-partidos-map">
		<div class="cipba-partidos-map__label">Mapa del Distrito VII</div>
		<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/mapa-distrito-vii.png' ); ?>" alt="Mapa de los partidos que comprende el Distrito VII, con San Justo (La Matanza) destacado" loading="lazy">
		<div class="cipba-partidos-map__caption">Distribución aproximada · No a escala</div>
		<div class="cipba-partidos-map__sede">
			<span class="cipba-partidos-map__sede-ico"><?php echo cipba_icon( 'star', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<div>
				<div class="cipba-partidos-map__sede-lbl">Sede principal</div>
				<div class="cipba-partidos-map__sede-val">San Justo · La Matanza</div>
			</div>
		</div>
	</div>

	<div class="cipba-partidos-list">
		<label class="cipba-partidos-search">
			<?php echo cipba_icon( 'search', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<input type="search" data-partidos-search placeholder="Buscar partido…" aria-label="Buscar partido">
		</label>
		<div class="cipba-partidos-items">
			<?php foreach ( $partidos as $p ) : ?>
				<div class="cipba-partidos-item" data-search="<?php echo esc_attr( mb_strtolower( $p ) ); ?>">
					<span class="cipba-partidos-item__dot"></span><span><?php echo esc_html( $p ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="cipba-partidos-empty" data-partidos-empty hidden>Sin coincidencias.</div>
		<div class="cipba-partidos-count" data-partidos-count data-total="<?php echo (int) $total; ?>">Mostrando <?php echo (int) $total; ?> de <?php echo (int) $total; ?> partidos</div>
	</div>
</div>
