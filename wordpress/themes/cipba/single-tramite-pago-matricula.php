<?php
/**
 * Página "Medios de pago" (trámite con slug pago-matricula): 4 modalidades de
 * pago con tres ventanas emergentes (datos bancarios, Red Link e instructivo de
 * Pagomiscuentas). WordPress usa este archivo en lugar de single-tramite.php
 * por el nombre (single-{tipo}-{slug}.php).
 *
 * Los datos bancarios salen de "Datos del Distrito"; los documentos de las
 * modalidades 2 y 4 se eligen de la biblioteca Documentos; el resto de los
 * textos, del formulario "Datos de la página de pago".
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$id     = get_the_ID();
	$m      = function ( $key, $default = '' ) use ( $id ) {
		$v = trim( (string) get_post_meta( $id, $key, true ) );
		return '' !== $v ? $v : $default;
	};
	$lineas = function ( $text ) {
		return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $text ) ) ) );
	};
	// Texto con marcadores, escapado y con los correos/enlaces clickeables.
	$linkify = function ( $text ) {
		return make_clickable( cipba_plain( $text ) );
	};
	// Documento elegido en un campo: array(formato, peso, url) o null.
	$doc = function ( $key ) use ( $id ) {
		$doc_id = (int) get_post_meta( $id, $key, true );
		if ( ! $doc_id || 'publish' !== get_post_status( $doc_id ) ) {
			return null;
		}
		return cipba_get_documento_file_meta( $doc_id );
	};

	$h1       = $m( 'pago_h1', get_the_title() );
	$crumb    = $m( 'breadcrumb', $h1 );
	$whatsapp = cipba_dato_url( 'whatsapp' );

	$mods = array(
		1 => array( 'icon' => 'building', 'btn_icon' => 'building', 'modal' => 'banco' ),
		2 => array( 'icon' => 'calendar', 'btn_icon' => 'file', 'doc' => $doc( 'mod2_doc' ) ),
		3 => array( 'icon' => 'external', 'btn_icon' => 'external', 'modal' => 'link' ),
		4 => array( 'icon' => 'dollar', 'btn_icon' => 'file', 'modal' => 'pmc' ),
	);

	$banco = array(
		array( 'Cta. Cte. N°', cipba_dato_display( 'banco_cta_cte' ), '' ),
		array( 'Sucursal', cipba_dato_display( 'banco_sucursal' ), '' ),
		array( 'Titular', cipba_dato_display( 'banco_titular' ), '' ),
		array( 'C.B.U.', cipba_dato_display( 'banco_cbu' ), preg_replace( '/\D+/', '', cipba_dato( 'banco_cbu' ) ) ),
		array( 'Alias', cipba_dato_display( 'banco_alias' ), trim( cipba_dato( 'banco_alias' ) ) ),
		array( 'C.U.I.T.', cipba_dato_display( 'banco_cuit' ), '' ),
	);

	// Contenido de la ventana del instructivo de Pagomiscuentas (fijo por ahora).
	$pmc_datos = array(
		array( 'Nombre del servicio en cajeros', 'CIPBA – Matric' ),
		array( 'Nombre del servicio en internet', 'CIPBA – Matric' ),
		array( 'Nombre en PMC', 'CIPBA – Matric' ),
		array( 'Concepto utilizado como ID. del cliente', 'Cod Electronico' ),
		array( 'Longitud de la clave', '8' ),
	);
	$pmc_pasos = array(
		'Por internet'          => array(
			'Ingresar en www.pagomiscuentas.com o al homebanking de su banco',
			'Seleccionar la opción "Pago de servicios"',
			'Elegir rubro CLUBES Y ASOCIACIONES',
			'Seleccionar el nombre',
			'Ingresar ID',
			'Seleccionar la cuenta a debitar',
			'Confirmar datos del pago',
			'Imprimir o guardar el comprobante',
		),
		'En cajeros automáticos' => array(
			'Ingresar clave',
			'Elegir opción "Pagomiscuentas"',
			'Elegir rubro ASOCIACIONES Y CLUBES',
			'Seleccionar el nombre',
			'Ingresar ID',
			'Seleccionar la cuenta a debitar',
			'Confirmar datos del pago',
			'Retirar el comprobante',
		),
	);
	$link_url = $m( 'mod3_url', 'http://www.colegioingenieros.org.ar/link/' );
	$mod4_doc = $mods[4] ? $doc( 'mod4_doc' ) : null;
	?>
	<div id="primary" class="content-area primary">
		<main id="main" class="site-main cipba-pago">

			<section class="cipba-inst-hero">
				<div class="cipba-inst-hero__inner">
					<div class="cipba-inst-hero__crumbs">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a><span>›</span><strong><?php echo cipba_plain( $crumb ); // phpcs:ignore WordPress.Security.EscapeOutput ?></strong>
					</div>
					<?php if ( $m( 'eyebrow' ) ) : ?><div class="cipba-inst-hero__badge"><i></i><?php echo cipba_plain( $m( 'eyebrow' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?>
					<h1><?php echo cipba_plain( $h1 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
					<?php if ( $m( 'intro' ) ) : ?><p><?php echo cipba_plain( $m( 'intro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
					<a href="#modalidades" class="cipba-inst-hero__cta">Ver formas de pago ›</a>
				</div>
			</section>

			<section class="cipba-pago-intro">
				<div class="cipba-wrap">
					<div class="cipba-pago-intro__grid">
						<div class="cipba-pago-intro__text">
							<div class="cipba-pago-eyebrow">Antes de pagar</div>
							<?php if ( $m( 'pago_antes_titulo' ) ) : ?><h2><?php echo cipba_plain( $m( 'pago_antes_titulo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2><?php endif; ?>
							<?php echo cipba_rich( $m( 'pago_antes_texto' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
						<?php $tene = $lineas( $m( 'pago_antes_lista' ) ); ?>
						<?php if ( $tene ) : ?>
							<div class="cipba-pago-tene">
								<?php if ( $m( 'pago_antes_lista_titulo' ) ) : ?><div class="cipba-pago-tene__title"><?php echo cipba_plain( $m( 'pago_antes_lista_titulo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?>
								<?php foreach ( $tene as $item ) : ?>
									<div class="cipba-pago-tene__item"><span class="cipba-pago-tene__check"><?php echo cipba_icon( 'check', 11 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><?php echo cipba_plain( $item ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<section class="cipba-pago-mods" id="modalidades">
				<div class="cipba-wrap">
					<div class="cipba-inst-head">
						<?php if ( $m( 'pago_mod_eyebrow' ) ) : ?><div class="cipba-inst-head__eyebrow"><?php echo cipba_plain( $m( 'pago_mod_eyebrow' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?>
						<?php if ( $m( 'pago_mod_titulo' ) ) : ?><h2><?php echo cipba_plain( $m( 'pago_mod_titulo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2><?php endif; ?>
					</div>
					<div class="cipba-pago-grid">
						<?php foreach ( $mods as $n => $mod ) :
							$pasos = $lineas( $m( "mod{$n}_pasos" ) );
							?>
							<article class="cipba-pago-card">
								<div class="cipba-pago-card__head">
									<span class="cipba-pago-card__ico"><?php echo cipba_icon( $mod['icon'], 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<div>
										<div class="cipba-pago-card__tag">Modalidad <?php echo (int) $n; ?></div>
										<h3><?php echo cipba_plain( $m( "mod{$n}_titulo" ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3>
									</div>
								</div>
								<?php if ( $m( "mod{$n}_desc" ) ) : ?><p class="cipba-pago-card__desc"><?php echo $linkify( $m( "mod{$n}_desc" ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
								<?php if ( $pasos ) : ?>
									<ol class="cipba-pago-card__steps">
										<?php foreach ( $pasos as $paso ) : ?>
											<li><span><?php echo $linkify( $paso ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></li>
										<?php endforeach; ?>
									</ol>
								<?php endif; ?>
								<?php
								$boton = $m( "mod{$n}_boton" );
								if ( $boton ) {
									if ( ! empty( $mod['modal'] ) ) {
										printf(
											'<button type="button" class="cipba-pago-btn" data-cipba-modal="%s">%s %s</button>',
											esc_attr( $mod['modal'] ),
											cipba_icon( $mod['btn_icon'], 14 ), // phpcs:ignore WordPress.Security.EscapeOutput
											cipba_plain( $boton ) // phpcs:ignore WordPress.Security.EscapeOutput
										);
									} elseif ( ! empty( $mod['doc'] ) ) {
										printf(
											'<a class="cipba-pago-btn" href="%s" target="_blank" rel="noopener">%s %s</a>',
											esc_url( $mod['doc']['url'] ),
											cipba_icon( $mod['btn_icon'], 14 ), // phpcs:ignore WordPress.Security.EscapeOutput
											cipba_plain( $boton ) // phpcs:ignore WordPress.Security.EscapeOutput
										);
									}
								}
								?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>

			<section class="cipba-cta">
				<div class="cipba-cta__inner">
					<?php if ( $m( 'pago_cta_titulo' ) ) : ?><h3><?php echo cipba_plain( $m( 'pago_cta_titulo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3><?php endif; ?>
					<?php if ( $m( 'pago_cta_texto' ) ) : ?><p><?php echo cipba_plain( $m( 'pago_cta_texto' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
					<div class="cipba-cta__actions">
						<a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>" class="cipba-cta__btn">Formulario de contacto →</a>
						<?php if ( $whatsapp ) : ?><a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener" class="cipba-cta__btn cipba-cta__btn--ghost">WhatsApp</a><?php endif; ?>
					</div>
				</div>
			</section>

			<!-- Ventana: datos bancarios -->
			<div class="cipba-modal" id="cipba-modal-banco" hidden>
				<div class="cipba-modal__dialog cipba-modal__dialog--sm" role="dialog" aria-modal="true" aria-labelledby="cipba-modal-banco-t">
					<div class="cipba-modal__head">
						<div><div class="cipba-modal__eyebrow">Transferencia bancaria</div><h3 id="cipba-modal-banco-t">Datos de la cuenta</h3></div>
						<button type="button" class="cipba-modal__close" data-cipba-close aria-label="Cerrar"><?php echo cipba_icon( 'close', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</div>
					<div class="cipba-modal__body">
						<?php foreach ( $banco as $b ) :
							if ( '' === $b[1] ) {
								continue;
							}
							?>
							<div class="cipba-bank-row">
								<div>
									<div class="cipba-bank-row__label"><?php echo esc_html( $b[0] ); ?></div>
									<div class="cipba-bank-row__value<?php echo $b[2] ? ' is-strong' : ''; ?>"><?php echo esc_html( $b[1] ); ?></div>
								</div>
								<?php if ( $b[2] ) : ?>
									<button type="button" class="cipba-copy" data-copy="<?php echo esc_attr( $b[2] ); ?>" aria-label="Copiar <?php echo esc_attr( $b[0] ); ?>"><?php echo cipba_icon( 'copy', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span>Copiar</span></button>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
						<?php if ( $m( 'mod1_pie' ) ) : ?><p class="cipba-modal__note"><?php echo $linkify( $m( 'mod1_pie' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Ventana: Red Link (consulta del Consejo Superior) -->
			<div class="cipba-modal" id="cipba-modal-link" hidden>
				<div class="cipba-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="cipba-modal-link-t">
					<div class="cipba-modal__head">
						<div><div class="cipba-modal__eyebrow">Red Link · Pago electrónico</div><h3 id="cipba-modal-link-t">Código de pago electrónico</h3></div>
						<button type="button" class="cipba-modal__close" data-cipba-close aria-label="Cerrar"><?php echo cipba_icon( 'close', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</div>
					<div class="cipba-modal__alert">La información del Consejo Superior se sirve sin conexión segura (http). Si el recuadro aparece vacío, tu navegador la bloqueó: abrila en una pestaña nueva con el link de abajo.</div>
					<iframe class="cipba-modal__frame" title="Código de pago electrónico" data-src="<?php echo esc_url( $link_url ); ?>"></iframe>
					<div class="cipba-modal__foot">
						<span>Consulta provista por el Consejo Superior.</span>
						<a href="<?php echo esc_url( $link_url ); ?>" target="_blank" rel="noopener">Abrir en una pestaña nueva <?php echo cipba_icon( 'external', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					</div>
				</div>
			</div>

			<!-- Ventana: instructivo de Pagomiscuentas -->
			<div class="cipba-modal" id="cipba-modal-pmc" hidden>
				<div class="cipba-modal__dialog cipba-modal__dialog--lg" role="dialog" aria-modal="true" aria-labelledby="cipba-modal-pmc-t">
					<div class="cipba-modal__head">
						<div><div class="cipba-modal__eyebrow">Pagomiscuentas</div><h3 id="cipba-modal-pmc-t">Instructivo de pago</h3></div>
						<button type="button" class="cipba-modal__close" data-cipba-close aria-label="Cerrar"><?php echo cipba_icon( 'close', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</div>
					<div class="cipba-modal__body cipba-modal__body--pad">
						<p class="cipba-modal__lead">Desde junio de 2019 se puede pagar la matrícula a través de Pagomiscuentas.</p>
						<div class="cipba-pmc-table">
							<?php foreach ( $pmc_datos as $d ) : ?>
								<div class="cipba-pmc-table__row"><span><?php echo esc_html( $d[0] ); ?></span><strong><?php echo esc_html( $d[1] ); ?></strong></div>
							<?php endforeach; ?>
						</div>
						<div class="cipba-pmc-cols">
							<?php foreach ( $pmc_pasos as $titulo => $pasos ) : ?>
								<div>
									<div class="cipba-pmc-cols__title"><?php echo esc_html( $titulo ); ?></div>
									<ol>
										<?php foreach ( $pasos as $paso ) : ?><li><?php echo esc_html( $paso ); ?></li><?php endforeach; ?>
									</ol>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="cipba-modal__foot">
						<span>Información del Consejo Superior.</span>
						<?php if ( $mod4_doc ) : ?><a href="<?php echo esc_url( $mod4_doc['url'] ); ?>" target="_blank" rel="noopener">Descargar instructivo <?php echo cipba_icon( 'file', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a><?php endif; ?>
					</div>
				</div>
			</div>

		</main>
	</div>
	<?php
endwhile;

get_footer();
