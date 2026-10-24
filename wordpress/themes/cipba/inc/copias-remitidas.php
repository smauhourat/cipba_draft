<?php
/**
 * Copias remitidas: formulario propio (sin plugins) para que el matriculado
 * envíe a la secretaría el contrato firmado en PDF junto con sus datos.
 *
 * - [cipba_copias_remitidas] muestra el formulario (markup en
 *   template-parts/copias-remitidas.php).
 * - El envío se procesa en template_redirect sobre la misma página: si hay
 *   errores se vuelve a mostrar el formulario con lo cargado; si sale bien,
 *   redirige a ?copia=enviada y se muestra el aviso de "enviado".
 * - El mail va a "Correo para copias remitidas" (Datos del Distrito), o al
 *   correo de contacto general si está vacío, con el PDF adjunto y Reply-To
 *   al matriculado. Sale por wp_mail(), o sea por FluentSMTP.
 * - El PDF no se guarda en el sitio: se adjunta desde el archivo temporal de PHP.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Tamaño máximo del PDF (bytes); el límite real es el menor entre este y el de PHP. */
const CIPBA_COPIAS_MAX_BYTES = 10 * MB_IN_BYTES;

/** Envíos permitidos por IP por hora (freno básico contra abuso). */
const CIPBA_COPIAS_MAX_HORA = 6;

/**
 * Estado del envío en curso (errores y valores cargados) para el template.
 *
 * @param array|null $set Nuevo estado.
 * @return array{errors: string[], values: array<string,string>}
 */
function cipba_copias_state( $set = null ) {
	static $state = array( 'errors' => array(), 'values' => array() );
	if ( null !== $set ) {
		$state = $set;
	}
	return $state;
}

/**
 * Límite efectivo del adjunto: el de la constante o el de PHP, el menor.
 */
function cipba_copias_max_bytes() {
	return (int) min( CIPBA_COPIAS_MAX_BYTES, wp_max_upload_size() );
}

/**
 * Casilla que recibe las copias.
 */
function cipba_copias_destinatario() {
	$mail = trim( cipba_dato( 'email_copias' ) );
	return is_email( $mail ) ? $mail : trim( cipba_dato( 'email' ) );
}

/**
 * ¿La página actual muestra el formulario?
 */
function cipba_copias_en_pagina() {
	$post = get_post();
	return is_singular() && $post && has_shortcode( $post->post_content, 'cipba_copias_remitidas' );
}

/**
 * La página del formulario no se cachea (el nonce caduca y la página
 * cambia según el resultado del envío) y procesa el POST.
 */
function cipba_copias_template_redirect() {
	if ( ! cipba_copias_en_pagina() ) {
		return;
	}
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	do_action( 'litespeed_control_set_nocache', 'cipba: formulario de copias remitidas' );
	nocache_headers();

	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! isset( $_GET['copia'] ) || 'enviar' !== $_GET['copia'] ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	cipba_copias_procesar();
}
add_action( 'template_redirect', 'cipba_copias_template_redirect' );

/**
 * Valida el envío y manda el mail. Si sale bien redirige (PRG); si no, deja
 * los errores en cipba_copias_state() para el template.
 */
function cipba_copias_procesar() {
	$volver = get_permalink();

	// Si el archivo supera post_max_size PHP descarta todo el POST.
	if ( empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		cipba_copias_state( array(
			'errors' => array( sprintf( 'El archivo es demasiado grande. El máximo es %s.', size_format( cipba_copias_max_bytes() ) ) ),
			'values' => array(),
		) );
		return;
	}

	$values = array(
		'nombre'    => sanitize_text_field( wp_unslash( $_POST['cr_nombre'] ?? '' ) ),
		'email'     => sanitize_email( wp_unslash( $_POST['cr_email'] ?? '' ) ),
		'matricula' => sanitize_text_field( wp_unslash( $_POST['cr_matricula'] ?? '' ) ),
	);
	$errors = array();

	if ( ! isset( $_POST['cr_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['cr_nonce'] ), 'cipba_copias' ) ) {
		$errors[] = 'La página estuvo abierta mucho tiempo. Volvé a adjuntar el archivo y enviá de nuevo.';
	}

	// Campo trampa: los bots lo completan, las personas no lo ven.
	if ( ! empty( $_POST['cr_web'] ) ) {
		wp_safe_redirect( add_query_arg( 'copia', 'enviada', $volver ) . '#copias-form' );
		exit;
	}

	if ( '' === $values['nombre'] || mb_strlen( $values['nombre'] ) > 120 ) {
		$errors[] = 'Completá tu apellido y nombre.';
	}
	if ( ! is_email( $values['email'] ) ) {
		$errors[] = 'Ingresá un mail válido.';
	}
	if ( ! preg_match( '/^[\p{L}\d][\p{L}\d .\/-]{0,24}$/u', $values['matricula'] ) ) {
		$errors[] = 'Ingresá tu número de matrícula.';
	}

	$file  = $_FILES['cr_archivo'] ?? null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$max   = cipba_copias_max_bytes();
	$error = is_array( $file ) ? (int) $file['error'] : UPLOAD_ERR_NO_FILE;
	if ( UPLOAD_ERR_NO_FILE === $error ) {
		$errors[] = 'Adjuntá el contrato firmado en PDF.';
	} elseif ( in_array( $error, array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true ) || ( UPLOAD_ERR_OK === $error && $file['size'] > $max ) ) {
		$errors[] = sprintf( 'El archivo es demasiado grande. El máximo es %s.', size_format( $max ) );
	} elseif ( UPLOAD_ERR_OK !== $error || ! is_uploaded_file( $file['tmp_name'] ) ) {
		$errors[] = 'No se pudo recibir el archivo. Probá de nuevo.';
	} elseif ( 'pdf' !== strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) || '%PDF-' !== (string) file_get_contents( $file['tmp_name'], false, null, 0, 5 ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
		$errors[] = 'El archivo tiene que ser un PDF.';
	}

	$ip_key = 'cipba_copias_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$envios = (int) get_transient( $ip_key );
	if ( ! $errors && $envios >= CIPBA_COPIAS_MAX_HORA ) {
		$errors[] = 'Se enviaron demasiadas copias desde esta conexión. Probá de nuevo en una hora.';
	}

	if ( ! $errors ) {
		$destino = cipba_copias_destinatario();
		$nombre  = str_replace( array( "\r", "\n", '"', '<', '>' ), '', $values['nombre'] );
		$adjunto = sanitize_file_name( 'copia-' . $values['matricula'] . '-' . remove_accents( $nombre ) . '.pdf' );
		$cuerpo  = implode( "\n", array(
			'Se recibió una copia remitida desde el sitio web.',
			'',
			'Apellido y nombre: ' . $values['nombre'],
			'Mail: ' . $values['email'],
			'Matrícula: ' . $values['matricula'],
			'Archivo: ' . sanitize_file_name( $file['name'] ) . ' (' . size_format( $file['size'] ) . ')',
			'Fecha: ' . wp_date( 'd/m/Y H:i' ),
			'',
			'Para responder al matriculado, respondé este mail.',
		) );
		$fallo = '';
		$capturar_fallo = function ( $wp_error ) use ( &$fallo ) {
			$fallo = $wp_error->get_error_message();
		};
		add_action( 'wp_mail_failed', $capturar_fallo );
		$enviado = $destino && wp_mail(
			$destino,
			sprintf( 'Copia remitida — %s (Mat. %s)', $nombre, $values['matricula'] ),
			$cuerpo,
			array( sprintf( 'Reply-To: "%s" <%s>', $nombre, $values['email'] ) ),
			array( $adjunto => $file['tmp_name'] )
		);
		remove_action( 'wp_mail_failed', $capturar_fallo );
		if ( $enviado ) {
			set_transient( $ip_key, $envios + 1, HOUR_IN_SECONDS );
			wp_safe_redirect( add_query_arg( 'copia', 'enviada', $volver ) . '#copias-form' );
			exit;
		}
		$errors[] = 'No pudimos enviar la copia en este momento. Probá de nuevo más tarde o escribinos a ' . antispambot( $destino ) . '.';
		$detalle  = $destino ? ( $fallo ? $fallo : 'wp_mail() devolvió false sin detalle.' ) : 'No hay casilla de destino configurada (Datos del Distrito).';
		error_log( 'cipba copias remitidas: ' . $detalle ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		// El detalle técnico solo lo ven los administradores (ver también FluentSMTP → Email Logs).
		if ( current_user_can( 'manage_options' ) ) {
			$errors[] = 'Detalle (solo visible para administradores): ' . $detalle;
		}
	}

	cipba_copias_state( array( 'errors' => $errors, 'values' => $values ) );
}

/**
 * [cipba_copias_remitidas] — formulario de la página Copias remitidas.
 */
function cipba_copias_remitidas_shortcode() {
	ob_start();
	get_template_part( 'template-parts/copias-remitidas' );
	return ob_get_clean();
}
add_shortcode( 'cipba_copias_remitidas', 'cipba_copias_remitidas_shortcode' );

/**
 * Script de la zona de arrastre (solo en la página del formulario).
 */
function cipba_copias_enqueue() {
	if ( cipba_copias_en_pagina() ) {
		wp_enqueue_script( 'cipba-copias', get_stylesheet_directory_uri() . '/assets/js/copias-remitidas.js', array(), CHILD_THEME_CIPBA_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'cipba_copias_enqueue' );
