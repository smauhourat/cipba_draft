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
 * @return array{formato: string, peso: string, url: string}|null (archivo subido, o enlace externo si no hay archivo)
 */
function cipba_get_documento_file_meta( $post_id ) {
	// El campo file_advanced de Meta Box guarda el ID del adjunto (una fila de
	// meta por archivo); se lee el ID directo, no el arreglo enriquecido de rwmb_meta().
	$ids           = get_post_meta( $post_id, 'archivo', false );
	$attachment_id = $ids ? (int) reset( $ids ) : 0;

	if ( $attachment_id ) {
		$path = get_attached_file( $attachment_id );
		$url  = wp_get_attachment_url( $attachment_id );

		if ( $path && file_exists( $path ) ) {
			return array(
				'formato' => strtoupper( pathinfo( $path, PATHINFO_EXTENSION ) ),
				'peso'    => size_format( filesize( $path ) ),
				'url'     => $url,
			);
		}
	}

	// Sin archivo subido: se usa el enlace externo (si se cargó), con el
	// formato deducido de la extensión de la dirección.
	$externo = trim( (string) get_post_meta( $post_id, 'enlace_externo', true ) );
	if ( $externo ) {
		$ext = strtoupper( pathinfo( (string) wp_parse_url( $externo, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
		return array(
			'formato' => $ext ? $ext : 'ENLACE',
			'peso'    => '',
			'url'     => $externo,
		);
	}

	return null;
}

/**
 * Algunos ítems de menú (Apariencia → Menús) enlazan directo a un Documento
 * de la biblioteca en vez de mostrarse en una lista de trámites/novedades
 * (ej. "Código de Ética" en el menú de Normativa). Como el CPT `documento` no
 * es público (no tiene página propia), el ítem de menú se carga como "Enlace
 * personalizado" y guarda el ID del documento en el meta "_cipba_documento_id".
 *
 * Este hook mantiene esa URL sincronizada: al guardar el Documento (por
 * ejemplo, al reemplazar el archivo subido), se actualiza sola la URL de
 * todos los ítems de menú vinculados a él, para que no quede apuntando al
 * archivo viejo.
 */
function cipba_sync_menu_documento_links( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || 'documento' !== get_post_type( $post_id ) ) {
		return;
	}
	$file = cipba_get_documento_file_meta( $post_id );
	if ( ! $file ) {
		return;
	}
	$items = get_posts( array(
		'post_type'      => 'nav_menu_item',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'meta_key'       => '_cipba_documento_id',
		'meta_value'     => $post_id,
		'fields'         => 'ids',
	) );
	foreach ( $items as $item_id ) {
		update_post_meta( $item_id, '_menu_item_url', $file['url'] );
	}
}
add_action( 'save_post', 'cipba_sync_menu_documento_links', 20 );

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
 * Las URLs salen de "Datos del Distrito"; una red sin URL no se muestra.
 *
 * @param bool $dark Variante para fondos oscuros (pie).
 */
function cipba_social_icons( $dark = false ) {
	$redes = array(
		'Instagram' => array(
			'href' => cipba_dato_url( 'instagram' ),
			'svg'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.1" fill="currentColor" stroke="none"/></svg>',
		),
		'Facebook' => array(
			'href' => cipba_dato_url( 'facebook' ),
			'svg'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
		),
		'LinkedIn' => array(
			'href' => cipba_dato_url( 'linkedin' ),
			'svg'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4V9h4v1.5A6 6 0 0116 8z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>',
		),
		'WhatsApp' => array(
			'href' => cipba_dato_url( 'whatsapp' ),
			'svg'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
		),
	);

	$class = 'cipba-social' . ( $dark ? ' cipba-social--dark' : '' );
	$out   = '<div class="' . esc_attr( $class ) . '">';
	foreach ( $redes as $label => $r ) {
		if ( '' === $r['href'] ) {
			continue; // Sin URL cargada en Datos del Distrito: no se muestra el ícono.
		}
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

/**
 * Iniciales para el avatar de un referente: primera letra del nombre +
 * primera del apellido (última palabra), como en el prototipo.
 */
function cipba_initials( $nombre ) {
	$parts = preg_split( '/\s+/', trim( (string) $nombre ) );
	if ( ! $parts || '' === $parts[0] ) {
		return '';
	}
	return mb_strtoupper( mb_substr( $parts[0], 0, 1 ) . mb_substr( end( $parts ), 0, 1 ) );
}

/**
 * Número para href="tel:" a partir de un teléfono tipeado a mano.
 * Entiende los formatos habituales de Argentina:
 *   "15-5181-9336"       → +5491151819336  (celular AMBA: sin 15, con 9 y área 11)
 *   "02324-15-58-2633"   → +5492324582633  (celular con código de área: sin 0 ni 15, con 9)
 *   "(11) 3535-0751"     → +541135350751   (fijo: se antepone solo 54)
 * Si ya trae 54 al inicio no lo duplica.
 */
function cipba_tel_link( $tel ) {
	// Se descarta el interno ("… int 5 o 7") — no forma parte del número.
	$tel    = preg_split( '/\b(?:int|interno|anexo|ext)\b/i', (string) $tel )[0];
	$digits = ltrim( preg_replace( '/\D+/', '', $tel ), '0' );
	if ( '' === $digits ) {
		return '';
	}
	if ( 0 === strpos( $digits, '54' ) ) {
		return '+' . $digits;
	}
	// Celular AMBA en formato local: 15 + 8 dígitos.
	if ( 10 === strlen( $digits ) && 0 === strpos( $digits, '15' ) ) {
		return '+54911' . substr( $digits, 2 );
	}
	// Celular con código de área + 15 + número (12 dígitos en total).
	if ( 12 === strlen( $digits ) ) {
		foreach ( array( 4, 3, 2 ) as $area_len ) {
			if ( '15' === substr( $digits, $area_len, 2 ) ) {
				return '+549' . substr( $digits, 0, $area_len ) . substr( $digits, $area_len + 2 );
			}
		}
	}
	return '+54' . $digits;
}

/**
 * Cantidad de slots de referente que ofrece el formulario de Subcomisión.
 * Para permitir más, subir este número (los campos se generan en un loop).
 */
define( 'CIPBA_SUBCOM_MAX_REFS', 4 );

/**
 * Referentes de una subcomisión, leídos de los campos planos refN_*.
 * Devuelve solo los que tienen nombre cargado.
 *
 * @param int|null $post_id ID de la subcomisión (por defecto, el post actual).
 * @return array[] Cada item: nombre, matricula, telefono, mail.
 */
function cipba_get_referentes( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$out     = array();
	for ( $i = 1; $i <= CIPBA_SUBCOM_MAX_REFS; $i++ ) {
		$nombre = trim( (string) get_post_meta( $post_id, "ref{$i}_nombre", true ) );
		if ( '' === $nombre ) {
			continue;
		}
		$out[] = array(
			'nombre'    => $nombre,
			'matricula' => trim( (string) get_post_meta( $post_id, "ref{$i}_matricula", true ) ),
			'telefono'  => trim( (string) get_post_meta( $post_id, "ref{$i}_telefono", true ) ),
			'mail'      => trim( (string) get_post_meta( $post_id, "ref{$i}_mail", true ) ),
		);
	}
	return $out;
}

/**
 * Cantidad de slots de "contacto directo" que ofrece el formulario de Sede.
 */
define( 'CIPBA_SEDE_MAX_CONTACTOS', 8 );

/**
 * Contactos directos de una sede (área, nombre, tel, email) — solo los que
 * tienen nombre cargado.
 *
 * @param int|null $post_id ID de la sede (por defecto, el post actual).
 * @return array[]
 */
function cipba_get_sede_contactos( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$out     = array();
	for ( $i = 1; $i <= CIPBA_SEDE_MAX_CONTACTOS; $i++ ) {
		$nombre = trim( (string) get_post_meta( $post_id, "contacto{$i}_nombre", true ) );
		if ( '' === $nombre ) {
			continue;
		}
		$out[] = array(
			'rol'    => trim( (string) get_post_meta( $post_id, "contacto{$i}_rol", true ) ),
			'nombre' => $nombre,
			'tel'    => trim( (string) get_post_meta( $post_id, "contacto{$i}_tel", true ) ),
			'email'  => trim( (string) get_post_meta( $post_id, "contacto{$i}_email", true ) ),
		);
	}
	return $out;
}

/**
 * Íconos SVG inline (trazo, mismos del prototipo) para las tarjetas de sedes.
 */
function cipba_icon( $name, $size = 14 ) {
	$paths = array(
		'location' => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>',
		'phone'    => '<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.01 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92v2z"/>',
		'mail'     => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
		'clock'    => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
		'award'    => '<circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>',
		'users'    => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>',
		'star'     => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
		'search'   => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
		'facebook' => '<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>',
		'linkedin' => '<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4V9h4v1.5A6 6 0 0116 8z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
		'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
		'copy'     => '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>',
		'close'    => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
		'check'    => '<polyline points="20 6 9 17 4 12"/>',
		'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
		'dollar'   => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
		'chevron'  => '<polyline points="9 18 15 12 9 6"/>',
		'home'     => '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
		'building' => '<rect x="3" y="3" width="18" height="18" rx="1"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/>',
		'file'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
		'external' => '<path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>',
		'whatsapp' => '<path stroke="none" fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">' . $paths[ $name ] . '</svg>';
}

/**
 * Iniciales del avatar de una autoridad: primera letra de las dos primeras
 * palabras del nombre (como en el prototipo de Institucional).
 */
function cipba_initials_first_two( $nombre ) {
	$parts = array_slice( preg_split( '/\s+/', trim( (string) $nombre ) ), 0, 2 );
	$out   = '';
	foreach ( $parts as $p ) {
		$out .= mb_substr( $p, 0, 1 );
	}
	return mb_strtoupper( $out );
}

/**
 * Cantidad de slots de "persona" que ofrece el formulario de Área de contacto.
 */
define( 'CIPBA_AREA_MAX_PERSONAS', 4 );

/**
 * Personas de un área de contacto (nombre, tel, email) — solo las que tienen nombre.
 *
 * @param int|null $post_id ID del área (por defecto, el post actual).
 * @return array[]
 */
function cipba_get_area_personas( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$out     = array();
	for ( $i = 1; $i <= CIPBA_AREA_MAX_PERSONAS; $i++ ) {
		$nombre = trim( (string) get_post_meta( $post_id, "persona{$i}_nombre", true ) );
		if ( '' === $nombre ) {
			continue;
		}
		$out[] = array(
			'nombre' => $nombre,
			'tel'    => trim( (string) get_post_meta( $post_id, "persona{$i}_tel", true ) ),
			'email'  => trim( (string) get_post_meta( $post_id, "persona{$i}_email", true ) ),
		);
	}
	return $out;
}
