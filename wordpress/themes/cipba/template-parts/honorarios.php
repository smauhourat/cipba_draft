<?php
/**
 * Página Honorarios mínimos: resolución vigente con sus documentos, aviso al
 * listado del Consejo Superior y resoluciones anteriores (acordeón).
 * Se renderiza con [cipba_honorarios].
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vig      = cipba_get_resolucion_vigente();
$max      = (int) cipba_dato( 'honorarios_anteriores_max' );
$antes    = cipba_get_resoluciones_anteriores( $max );
$cs_url   = trim( cipba_dato( 'honorarios_cs_url' ) );
$g        = function ( $id, $k ) {
	return trim( (string) get_post_meta( $id, $k, true ) );
};
$piezas   = array();
if ( $vig ) {
	$corto = strtok( $g( $vig->ID, 'numero' ), '/' );
	$res   = cipba_resolucion_archivo( $vig->ID, 'archivo_resolucion' );
	$anex  = cipba_resolucion_archivo( $vig->ID, 'archivo_anexos' );
	if ( $res ) {
		$piezas[] = array( 'Resolución ' . $corto, 'Resolución', 'Texto de la resolución del Consejo Superior: alcance, fecha de vigencia y criterios de aplicación.', $res );
	}
	if ( $anex ) {
		$piezas[] = array( 'Anexos — Tablas de honorarios mínimos', 'Anexos', 'Valores por especialidad y tipo de tarea. Es el documento de consulta habitual para liquidar un trabajo.', $anex );
	}
}
?>
<div class="cipba-hon">
	<?php if ( $vig ) : ?>
		<div class="cipba-hon-vig">
			<div class="cipba-hon-vig__head">
				<div>
					<div class="cipba-hon-vig__meta">
						<span class="cipba-hon-vig__badge"><?php echo cipba_icon( 'check', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Vigente</span>
						<?php if ( $g( $vig->ID, 'fecha_publicacion' ) ) : ?><span>Publicada el <?php echo esc_html( cipba_fecha_larga( $g( $vig->ID, 'fecha_publicacion' ), false ) ); ?></span><?php endif; ?>
					</div>
					<h2><?php echo esc_html( get_the_title( $vig ) ); ?></h2>
					<?php if ( $g( $vig->ID, 'descripcion' ) ) : ?><p><?php echo esc_html( $g( $vig->ID, 'descripcion' ) ); ?></p><?php endif; ?>
				</div>
				<?php if ( $g( $vig->ID, 'vigencia_desde' ) ) : ?>
					<div class="cipba-hon-vig__date">
						<div class="cipba-hon-vig__lbl">Vigente desde</div>
						<div class="cipba-hon-vig__val"><?php echo esc_html( cipba_fecha_larga( $g( $vig->ID, 'vigencia_desde' ), false ) ); ?></div>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $piezas ) : ?>
				<div class="cipba-hon-vig__piezas">
					<?php foreach ( $piezas as $p ) : ?>
						<a class="cipba-hon-doc" href="<?php echo esc_url( $p[3]['url'] ); ?>" target="_blank" rel="noopener">
							<span class="cipba-hon-doc__ico"><?php echo cipba_icon( 'file', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="cipba-hon-doc__body">
								<span class="cipba-hon-doc__top"><strong><?php echo esc_html( $p[0] ); ?></strong><em><?php echo esc_html( $p[1] ); ?></em></span>
								<span class="cipba-hon-doc__desc"><?php echo esc_html( $p[2] ); ?></span>
								<span class="cipba-hon-doc__meta"><?php echo esc_html( $p[3]['formato'] . ' · ' . $p[3]['peso'] ); ?></span>
							</span>
							<span class="cipba-hon-doc__go">Abrir <?php echo cipba_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<div class="cipba-hon-empty">Todavía no hay una resolución vigente publicada.</div>
	<?php endif; ?>

	<?php if ( $cs_url ) : ?>
		<div class="cipba-hon-aviso">
			<span><?php echo cipba_icon( 'clock', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<p>El Consejo Superior actualiza la tabla periódicamente. Si necesitás verificar que esta sea la última resolución publicada, podés consultar el <a href="<?php echo esc_url( $cs_url ); ?>" target="_blank" rel="noopener">listado de honorarios del Consejo Superior</a>.</p>
		</div>
	<?php endif; ?>

	<?php if ( $antes ) : ?>
		<details class="cipba-hon-hist">
			<summary>
				<span class="cipba-hon-hist__title">
					<strong>Resoluciones anteriores</strong>
					<span>Tablas sin vigencia actual, necesarias para trabajos presentados con anterioridad</span>
				</span>
				<span class="cipba-hon-hist__count"><?php echo (int) count( $antes ); ?> <span class="cipba-hon-hist__chev"><?php echo cipba_icon( 'chevron', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></span>
			</summary>
			<div class="cipba-hon-hist__list">
				<?php foreach ( $antes as $r ) :
					$url = $g( $r->ID, 'enlace_externo' );
					$tag = $url ? 'a' : 'div';
					?>
					<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="cipba-hon-row"<?php echo $url ? ' href="' . esc_url( $url ) . '" target="_blank" rel="noopener"' : ''; ?>>
						<span class="cipba-hon-row__body">
							<strong><?php echo esc_html( get_the_title( $r ) ); ?></strong>
							<?php if ( $g( $r->ID, 'descripcion' ) ) : ?><span><?php echo esc_html( $g( $r->ID, 'descripcion' ) ); ?></span><?php endif; ?>
						</span>
						<span class="cipba-hon-row__side">
							<em><?php echo esc_html( cipba_resolucion_periodo( $r->ID ) ); ?></em>
							<?php echo $url ? cipba_icon( 'external', 14 ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</span>
					</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php endforeach; ?>
			</div>
		</details>
	<?php endif; ?>

	<p class="cipba-hon-foot">Los documentos se publican tal como los emite el Consejo Superior y abren en una pestaña nueva. Ante cualquier discrepancia, prevalece el texto oficial.</p>
</div>
