<?php
/**
 * Formulario de Copias remitidas: datos del matriculado + PDF del contrato
 * firmado. Lo procesa inc/copias-remitidas.php; se renderiza con
 * [cipba_copias_remitidas].
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$state   = cipba_copias_state();
$errors  = $state['errors'];
$v       = wp_parse_args( $state['values'], array( 'nombre' => '', 'email' => '', 'matricula' => '' ) );
$max     = cipba_copias_max_bytes();
$enviada = ! $errors && isset( $_GET['copia'] ) && 'enviada' === $_GET['copia']; // phpcs:ignore WordPress.Security.NonceVerification
$action  = add_query_arg( 'copia', 'enviar', get_permalink() ) . '#copias-form';
?>
<div class="cipba-copias" id="copias-form">
	<?php if ( $enviada ) : ?>
		<div class="cipba-copias__ok" role="status">
			<span class="cipba-copias__ok-ico"><?php echo cipba_icon( 'check', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<h2>¡Copia enviada!</h2>
			<p>Recibimos tu contrato y tus datos. La secretaría lo va a revisar y, si hace falta, te va a contactar al mail que indicaste.</p>
			<a class="cipba-copias__again" href="<?php echo esc_url( get_permalink() ); ?>#copias-form">Enviar otra copia</a>
		</div>
	<?php else : ?>
		<div class="cipba-aviso cipba-aviso--ambar">
			<span class="cipba-aviso__ico"><?php echo cipba_icon( 'info', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<div class="cipba-aviso__body">
				<div class="cipba-aviso__text">Adjuntá el contrato firmado en formato <strong>PDF</strong> junto con tus datos y presioná <strong>Enviar copia</strong> para remitirlo a la secretaría.</div>
			</div>
		</div>

		<?php if ( $errors ) : ?>
			<div class="cipba-copias__errors" role="alert">
				<strong>No pudimos enviar la copia:</strong>
				<ul>
					<?php foreach ( $errors as $e ) : ?>
						<li><?php echo esc_html( $e ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form class="cipba-copias__card" method="post" action="<?php echo esc_url( $action ); ?>" enctype="multipart/form-data" data-max="<?php echo (int) $max; ?>" data-max-label="<?php echo esc_attr( size_format( $max ) ); ?>">
			<?php wp_nonce_field( 'cipba_copias', 'cr_nonce', false ); ?>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo (int) $max; ?>">
			<div class="cipba-copias__trap" aria-hidden="true">
				<label>No completar <input type="text" name="cr_web" tabindex="-1" autocomplete="off"></label>
			</div>

			<div class="cipba-copias__fields">
				<div class="cipba-copias__field cipba-copias__field--wide">
					<label for="cr_nombre">Apellido y nombre <span>*</span></label>
					<input type="text" id="cr_nombre" name="cr_nombre" required maxlength="120" autocomplete="name" placeholder="Ej: Pérez, Juan" value="<?php echo esc_attr( $v['nombre'] ); ?>">
				</div>
				<div class="cipba-copias__field">
					<label for="cr_email">Mail <span>*</span></label>
					<input type="email" id="cr_email" name="cr_email" required maxlength="120" autocomplete="email" placeholder="tu@correo.com" value="<?php echo esc_attr( $v['email'] ); ?>">
				</div>
				<div class="cipba-copias__field">
					<label for="cr_matricula">N° de matrícula <span>*</span></label>
					<input type="text" id="cr_matricula" name="cr_matricula" required maxlength="25" inputmode="numeric" placeholder="Ej: 12345" value="<?php echo esc_attr( $v['matricula'] ); ?>">
				</div>
			</div>

			<div class="cipba-copias__label">Contrato firmado <span>*</span></div>
			<label class="cipba-copias__drop" for="cr_archivo">
				<input type="file" id="cr_archivo" name="cr_archivo" accept="application/pdf,.pdf" required>
				<span class="cipba-copias__drop-ico"><?php echo cipba_icon( 'upload', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="cipba-copias__drop-empty">
					<strong>Arrastrá el PDF acá</strong> o <u>hacé clic para elegirlo</u>
					<small>Solo archivos PDF · hasta <?php echo esc_html( size_format( $max ) ); ?></small>
				</span>
				<span class="cipba-copias__drop-file" hidden>
					<span class="cipba-copias__drop-name"></span>
					<small><span class="cipba-copias__drop-size"></span> · <u>Cambiar archivo</u></small>
				</span>
			</label>
			<p class="cipba-copias__drop-error" role="alert" hidden></p>

			<div class="cipba-copias__foot">
				<p>Los datos y el archivo se envían por mail a la secretaría del Distrito; no quedan publicados en el sitio.</p>
				<button type="submit" class="cipba-copias__btn"><?php echo cipba_icon( 'send', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span>Enviar copia</span></button>
			</div>
		</form>
	<?php endif; ?>
</div>
