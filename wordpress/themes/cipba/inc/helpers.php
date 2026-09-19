<?php
/**
 * Helpers varios del tema.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formato y peso de un documento de Normativa, leídos del propio archivo
 * subido (no se tipean a mano — así lo pide el handoff en §4.14).
 *
 * @param int $post_id ID del post `documento`.
 * @return array{formato: string, peso: string, url: string}|null
 */
function cipba_get_documento_file_meta( $post_id ) {
	$attachment_id = rwmb_meta( 'archivo', array(), $post_id );

	// El campo file_advanced de Meta Box guarda un array de IDs de adjunto.
	if ( is_array( $attachment_id ) ) {
		$attachment_id = reset( $attachment_id );
	}

	if ( ! $attachment_id ) {
		return null;
	}

	$path = get_attached_file( $attachment_id );
	$url  = wp_get_attachment_url( $attachment_id );

	if ( ! $path || ! file_exists( $path ) ) {
		return null;
	}

	return array(
		'formato' => strtoupper( pathinfo( $path, PATHINFO_EXTENSION ) ),
		'peso'    => size_format( filesize( $path ) ),
		'url'     => $url,
	);
}

/**
 * Permite subir SVG a la Media Library (para el logo y otros assets
 * vectoriales). Solo administradores pueden subir archivos, así que el
 * riesgo de SVG con script embebido es bajo en este sitio.
 */
function cipba_allow_svg_upload( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'cipba_allow_svg_upload' );

function cipba_fix_svg_filetype( $data, $file, $filename, $mimes ) {
	if ( ! $data['type'] ) {
		$filetype = wp_check_filetype( $filename, $mimes );
		if ( 'svg' === $filetype['ext'] ) {
			$data['ext']             = 'svg';
			$data['type']            = 'image/svg+xml';
			$data['proper_filename'] = $filename;
		}
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'cipba_fix_svg_filetype', 10, 4 );

/**
 * WordPress no genera width/height para adjuntos SVG (no los procesa
 * como imagen) — sin esos datos en la metadata, cosas como el logo del
 * Header Builder de Astra (que recalcula el ancho a partir de las
 * dimensiones) terminan sin mostrarse. Se completan a mano leyendo el
 * viewBox/width/height del propio archivo.
 */
function cipba_svg_attachment_metadata( $metadata, $attachment_id ) {
	$file = get_attached_file( $attachment_id );

	if ( ! $file || 'svg' !== strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) ) {
		return $metadata;
	}

	$xml = @simplexml_load_file( $file );
	if ( ! $xml ) {
		return $metadata;
	}

	$attrs = $xml->attributes();
	$width  = (float) ( $attrs->width ?? 0 );
	$height = (float) ( $attrs->height ?? 0 );

	if ( ( ! $width || ! $height ) && isset( $attrs->viewBox ) ) {
		$box = explode( ' ', (string) $attrs->viewBox );
		if ( count( $box ) === 4 ) {
			$width  = (float) $box[2];
			$height = (float) $box[3];
		}
	}

	if ( $width && $height ) {
		$metadata['width']  = (int) round( $width );
		$metadata['height'] = (int) round( $height );
	}

	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'cipba_svg_attachment_metadata', 10, 2 );

/**
 * wp_get_attachment_image_src() para SVG con un $size con nombre (no
 * "full") devuelve false porque WordPress nunca genera esa variante —
 * el propio SVG sirve para cualquier tamaño. Se lo devolvemos con las
 * dimensiones reales del archivo en vez de false.
 */
function cipba_svg_image_src( $image, $attachment_id, $size ) {
	if ( $image || 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
		return $image;
	}

	$meta = wp_get_attachment_metadata( $attachment_id );
	if ( empty( $meta['width'] ) || empty( $meta['height'] ) ) {
		return $image;
	}

	return array(
		wp_get_attachment_url( $attachment_id ),
		$meta['width'],
		$meta['height'],
		false,
	);
}
add_filter( 'wp_get_attachment_image_src', 'cipba_svg_image_src', 10, 3 );

/**
 * Grilla de íconos de redes (§4.11 y §4.13 del handoff — se usa tanto en
 * Contacto como en el pie). SVG inline, sin librería de íconos.
 * Facebook y LinkedIn quedan con # — el prototipo no trae URL real.
 *
 * @param bool $dark Variante para fondos oscuros (pie).
 */
function cipba_social_icons( $dark = false ) {
	$redes = array(
		'Instagram' => array(
			'href' => 'https://www.instagram.com/cipba7/',
			'svg'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.1" fill="currentColor" stroke="none"/></svg>',
		),
		'Facebook' => array(
			'href' => '#',
			'svg'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
		),
		'LinkedIn' => array(
			'href' => '#',
			'svg'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4V9h4v1.5A6 6 0 0116 8z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
		),
		'WhatsApp' => array(
			'href' => 'https://wa.me/5491127133330',
			'svg'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
		),
	);

	$class = 'cipba-social' . ( $dark ? ' cipba-social--dark' : '' );
	$out   = '<div class="' . esc_attr( $class ) . '">';
	foreach ( $redes as $label => $r ) {
		$out .= sprintf(
			'<a href="%s" target="_blank" rel="noopener" aria-label="%s" title="%s">%s</a>',
			esc_url( $r['href'] ),
			esc_attr( $label ),
			esc_attr( $label ),
			$r['svg']
		);
	}
	$out .= '</div>';
	return $out;
}
