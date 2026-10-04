<?php
/**
 * Página TAD (Tribunal de Árbitros y Amigables Componedores): resoluciones,
 * botón del formulario de inicio y mail de contacto. Todo se elige en la caja
 * "Contenido de la página TAD" de la propia página (documentos de la
 * biblioteca). Mismo diseño que tad.html del prototipo; reusa la fila
 * .cipba-hon-doc de Honorarios/Normativa. Se renderiza con [cipba_tad].
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page_id = get_the_ID();

$docs = array();
for ( $i = 1; $i <= CIPBA_TAD_DOCS; $i++ ) {
	$doc_id = (int) get_post_meta( $page_id, "tad_doc{$i}", true );
	$file   = $doc_id && 'publish' === get_post_status( $doc_id ) ? cipba_get_documento_file_meta( $doc_id ) : null;
	if ( $file ) {
		$docs[] = array(
			'titulo' => get_the_title( $doc_id ),
			'desc'   => trim( (string) get_post_meta( $doc_id, 'descripcion', true ) ),
			'origen' => trim( (string) get_post_meta( $doc_id, 'origen', true ) ),
			'file'   => $file,
		);
	}
}

$form_id = (int) get_post_meta( $page_id, 'tad_formulario', true );
$form    = $form_id && 'publish' === get_post_status( $form_id ) ? cipba_get_documento_file_meta( $form_id ) : null;
$mail    = trim( (string) get_post_meta( $page_id, 'tad_email', true ) );
?>
<div class="cipba-norm cipba-tad">
	<?php if ( $docs ) : ?>
		<div class="cipba-tad__label">Resoluciones</div>
		<?php foreach ( $docs as $d ) :
			$ext  = '' === $d['file']['peso'];
			$meta = $ext
				? array( $d['origen'] ? 'Sitio del ' . $d['origen'] : '', preg_replace( '/^www\./', '', (string) wp_parse_url( $d['file']['url'], PHP_URL_HOST ) ) )
				: array( $d['file']['formato'] . ' · ' . $d['file']['peso'], $d['origen'] );
			?>
			<a class="cipba-hon-doc<?php echo $ext ? ' cipba-hon-doc--ext' : ''; ?>" href="<?php echo esc_url( $d['file']['url'] ); ?>" target="_blank" rel="noopener">
				<span class="cipba-hon-doc__ico"><?php echo cipba_icon( $ext ? 'external' : 'file', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="cipba-hon-doc__body">
					<span class="cipba-hon-doc__top"><strong><?php echo esc_html( $d['titulo'] ); ?></strong></span>
					<?php if ( $d['desc'] ) : ?><span class="cipba-hon-doc__desc"><?php echo esc_html( $d['desc'] ); ?></span><?php endif; ?>
					<span class="cipba-hon-doc__meta"><?php echo esc_html( implode( ' · ', array_filter( $meta ) ) ); ?></span>
				</span>
				<span class="cipba-hon-doc__go"><?php echo $ext ? 'Ir al sitio' : 'Abrir'; ?> <?php echo cipba_icon( $ext ? 'external' : 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			</a>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ( $form || $mail ) : ?>
		<div class="cipba-tad__box">
			<?php if ( $form ) : ?>
				<a class="cipba-tad__btn" href="<?php echo esc_url( $form['url'] ); ?>" target="_blank" rel="noopener"><?php echo cipba_icon( 'download', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Descargar formulario</a>
			<?php endif; ?>
			<?php if ( $mail ) : ?>
				<a class="cipba-tad__mail" href="mailto:<?php echo esc_attr( antispambot( $mail ) ); ?>"><?php echo cipba_icon( 'mail', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Mail de contacto: <span><?php echo esc_html( antispambot( $mail ) ); ?></span></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( ! $docs && ! $form && ! $mail ) : ?>
		<div class="cipba-hon-empty">Todavía no hay documentos publicados.</div>
	<?php endif; ?>
</div>
