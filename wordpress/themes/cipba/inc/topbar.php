<?php
/**
 * Barra superior (teléfono, correo, horario, WhatsApp y Consejo Superior).
 *
 * Se imprime ANTES del #masthead de Astra, así que scrollea con la página y
 * se va; lo que queda fijo arriba es el #masthead (ver style.css, "Header
 * sticky"), igual que en el prototipo. Los datos salen de "Datos del
 * Distrito" (inc/settings.php).
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cipba_render_topbar() {
	$telefono = trim( cipba_dato( 'telefono' ) );
	$email    = trim( cipba_dato( 'email' ) );
	$horario  = trim( cipba_dato( 'horario' ) );
	$whatsapp = cipba_dato_url( 'whatsapp' );
	$consejo  = cipba_dato_url( 'consejo_superior_url' );

	if ( ! ( $telefono || $email || $horario || $whatsapp || $consejo ) ) {
		return;
	}
	?>
	<div class="cipba-topbar">
		<div class="cipba-topbar__inner">
			<div class="cipba-topbar__group">
				<?php if ( $telefono ) : ?>
					<a class="cipba-topbar__item" href="<?php echo esc_url( cipba_dato_url( 'telefono' ) ); ?>"><?php echo cipba_icon( 'phone', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $telefono ); ?></a>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<a class="cipba-topbar__item" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo cipba_icon( 'mail', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $email ); ?></a>
				<?php endif; ?>
				<?php if ( $horario ) : ?>
					<span class="cipba-topbar__item cipba-topbar__hours"><?php echo cipba_icon( 'clock', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $horario ); ?></span>
				<?php endif; ?>
			</div>
			<div class="cipba-topbar__group cipba-topbar__group--right">
				<?php if ( $whatsapp ) : ?>
					<a class="cipba-topbar__link" href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener"><?php echo cipba_icon( 'whatsapp', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> WhatsApp</a>
				<?php endif; ?>
				<?php if ( $consejo ) : ?>
					<a class="cipba-topbar__link cipba-topbar__cs" href="<?php echo esc_url( $consejo ); ?>" target="_blank" rel="noopener">Consejo Superior <?php echo cipba_icon( 'external', 11 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'astra_header_before', 'cipba_render_topbar', 5 );
