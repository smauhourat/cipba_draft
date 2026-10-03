<?php
/**
 * Página Normativa: documentos de la biblioteca (CPT `documento`) tildados
 * "Mostrar en la página Normativa". Los que tienen archivo subido van arriba;
 * los que son solo un enlace externo, abajo, bajo "En el sitio del Consejo
 * Superior". Mismo diseño que normativa.html del prototipo.
 * Se renderiza con [cipba_normativa].
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$docs = get_posts( array(
	'post_type'   => 'documento',
	'numberposts' => -1,
	'meta_key'    => 'en_normativa', // phpcs:ignore WordPress.DB.SlowDBQuery
	'meta_value'  => '1', // phpcs:ignore WordPress.DB.SlowDBQuery
	'orderby'     => 'title',
	'order'       => 'ASC',
) );
// Orden manual (campo "Orden"; vacío cuenta como 0) y, a igual orden, por título.
usort( $docs, function ( $a, $b ) {
	return (int) get_post_meta( $a->ID, 'orden', true ) <=> (int) get_post_meta( $b->ID, 'orden', true );
} );

$propios  = array();
$externos = array();
foreach ( $docs as $doc ) {
	$file = cipba_get_documento_file_meta( $doc->ID );
	if ( ! $file ) {
		continue;
	}
	$item = array(
		'titulo' => get_the_title( $doc ),
		'desc'   => trim( (string) get_post_meta( $doc->ID, 'descripcion', true ) ),
		'origen' => trim( (string) get_post_meta( $doc->ID, 'origen', true ) ),
		'file'   => $file,
	);
	// Sin peso = no hay archivo subido, es solo un enlace externo.
	if ( '' !== $file['peso'] ) {
		$propios[] = $item;
	} else {
		$externos[] = $item;
	}
}
?>
<div class="cipba-norm">
	<?php if ( ! $propios && ! $externos ) : ?>
		<div class="cipba-hon-empty">Todavía no hay documentos publicados.</div>
	<?php endif; ?>

	<?php foreach ( $propios as $d ) : ?>
		<a class="cipba-hon-doc" href="<?php echo esc_url( $d['file']['url'] ); ?>" target="_blank" rel="noopener">
			<span class="cipba-hon-doc__ico"><?php echo cipba_icon( 'file', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="cipba-hon-doc__body">
				<span class="cipba-hon-doc__top"><strong><?php echo esc_html( $d['titulo'] ); ?></strong></span>
				<?php if ( $d['desc'] ) : ?><span class="cipba-hon-doc__desc"><?php echo esc_html( $d['desc'] ); ?></span><?php endif; ?>
				<span class="cipba-hon-doc__meta"><?php echo esc_html( implode( ' · ', array_filter( array( $d['file']['formato'] . ' · ' . $d['file']['peso'], $d['origen'] ) ) ) ); ?></span>
			</span>
			<span class="cipba-hon-doc__go">Abrir <?php echo cipba_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</a>
	<?php endforeach; ?>

	<?php if ( $externos ) : ?>
		<div class="cipba-norm__sep"><span>En el sitio del Consejo Superior</span></div>
		<?php foreach ( $externos as $d ) :
			$host = preg_replace( '/^www\./', '', (string) wp_parse_url( $d['file']['url'], PHP_URL_HOST ) );
			?>
			<a class="cipba-hon-doc cipba-hon-doc--ext" href="<?php echo esc_url( $d['file']['url'] ); ?>" target="_blank" rel="noopener">
				<span class="cipba-hon-doc__ico"><?php echo cipba_icon( 'external', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="cipba-hon-doc__body">
					<span class="cipba-hon-doc__top"><strong><?php echo esc_html( $d['titulo'] ); ?></strong></span>
					<?php if ( $d['desc'] ) : ?><span class="cipba-hon-doc__desc"><?php echo esc_html( $d['desc'] ); ?></span><?php endif; ?>
					<span class="cipba-hon-doc__meta"><?php echo esc_html( implode( ' · ', array_filter( array( $d['origen'] ? 'Sitio del ' . $d['origen'] : '', $host ) ) ) ); ?></span>
				</span>
				<span class="cipba-hon-doc__go">Ir al sitio <?php echo cipba_icon( 'external', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			</a>
		<?php endforeach; ?>
	<?php endif; ?>

	<p class="cipba-hon-foot">Las versiones publicadas se mantienen actualizadas según el texto vigente. Ante cualquier discrepancia, prevalece el texto oficial publicado por la Provincia de Buenos Aires y el Consejo Superior.</p>
</div>
