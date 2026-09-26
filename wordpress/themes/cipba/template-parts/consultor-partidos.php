<?php
/**
 * Consultor de partidos (home, tarjeta "¿Tu partido pertenece al Distrito
 * VII?"). Se renderiza con [cipba_consultor_partidos]. El listado de
 * partidos (assets/data/partidos-distrito-vii.json, vía cipba_get_partidos())
 * se embebe inline para que la búsqueda en assets/js/consultor-partidos.js
 * corra sin pedidos al servidor.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$partidos = cipba_get_partidos();
?>
<div class="cipba-consultor">
	<label class="cipba-consultor__field">
		<?php echo cipba_icon( 'search', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<input type="text" data-consultor-input placeholder="Escribí el nombre de tu partido…" aria-label="Buscar tu partido" autocomplete="off">
	</label>
	<div class="cipba-consultor__resultado" data-consultor-resultado aria-live="polite"></div>
	<a class="cipba-consultor__link" href="/institucional/#partidos"><?php echo cipba_icon( 'location', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Ver los 23 partidos</a>
</div>
<script type="application/json" id="cipba-partidos-data"><?php echo wp_json_encode( array_values( $partidos ), JSON_HEX_TAG | JSON_HEX_AMP ); // phpcs:ignore WordPress.Security.EscapeOutput ?></script>
