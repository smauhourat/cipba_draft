<?php
/**
 * Datos generales del Distrito: una pantalla del admin ("Datos del Distrito")
 * con campos simples, guardados en una sola opción (cipba_distrito).
 *
 * Para sumar un dato nuevo alcanza con agregar una entrada en
 * cipba_datos_fields(); queda en el formulario y disponible con
 * cipba_dato( 'clave' ) en PHP o [cipba_dato campo="clave"] en cualquier
 * página o bloque. Con formato="url" el shortcode devuelve el enlace listo
 * (wa.me / tel: / mailto:) para usar dentro de un href.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CIPBA_DATOS_OPTION = 'cipba_distrito';

/**
 * Definición de los datos generales.
 *
 * @return array[] clave => array( section, label, desc, placeholder, default, type, required )
 *   type: text (por defecto) | url | email
 */
function cipba_datos_fields() {
	return array(
		// — Consejo Directivo —
		'periodo_autoridades' => array(
			'section'     => 'Consejo Directivo',
			'label'       => 'Período de autoridades',
			'desc'        => 'Solo los años, por ejemplo: 2024 – 2027. Se muestra en la sección Autoridades de la página Institucional.',
			'placeholder' => '2024 – 2027',
			'default'     => '2024 – 2027',
			'required'    => true,
		),

		// — Contacto general —
		'telefono'            => array(
			'section'     => 'Contacto general',
			'label'       => 'Teléfono',
			'desc'        => 'Teléfono principal del Distrito. El link para llamar se genera solo.',
			'placeholder' => '(011) 4651-0064',
			'default'     => '(011) 4651-0064',
		),
		'email'               => array(
			'section'     => 'Contacto general',
			'label'       => 'Correo electrónico',
			'desc'        => 'Correo de contacto general.',
			'placeholder' => 'info@cipba.org',
			'default'     => 'info@cipba.org',
			'type'        => 'email',
		),
		'horario'             => array(
			'section'     => 'Contacto general',
			'label'       => 'Horario de atención',
			'desc'        => 'Se muestra tal cual lo escribas.',
			'placeholder' => 'Lun–Vie 9 a 16 h (San Justo)',
			'default'     => 'Lun–Vie 9 a 16 h (San Justo)',
		),
		'whatsapp'            => array(
			'section'     => 'Contacto general',
			'label'       => 'WhatsApp',
			'desc'        => 'Número con el que atiende WhatsApp. El link (wa.me) se genera solo. Dejalo vacío para ocultar el botón.',
			'placeholder' => '+54 9 11 2713-3330',
			'default'     => '+54 9 11 2713-3330',
		),

		'consejo_superior_url' => array(
			'section'     => 'Contacto general',
			'label'       => 'Enlace al Consejo Superior',
			'desc'        => 'Se muestra como "Consejo Superior" en la barra superior. Dejalo vacío para ocultarlo.',
			'placeholder' => 'http://www.colegioingenieros.org.ar/',
			'default'     => 'http://www.colegioingenieros.org.ar/',
			'type'        => 'url',
		),

		// — Redes sociales —
		'instagram'           => array(
			'section'     => 'Redes sociales',
			'label'       => 'Instagram',
			'desc'        => 'Dirección completa del perfil. Si queda vacío, el ícono no se muestra en el sitio.',
			'placeholder' => 'https://www.instagram.com/cipba7/',
			'default'     => 'https://www.instagram.com/cipba7/',
			'type'        => 'url',
		),
		'facebook'            => array(
			'section'     => 'Redes sociales',
			'label'       => 'Facebook',
			'desc'        => 'Dirección completa de la página. Si queda vacío, el ícono no se muestra.',
			'placeholder' => 'https://www.facebook.com/…',
			'default'     => '',
			'type'        => 'url',
		),
		'linkedin'            => array(
			'section'     => 'Redes sociales',
			'label'       => 'LinkedIn',
			'desc'        => 'Dirección completa de la página. Si queda vacío, el ícono no se muestra.',
			'placeholder' => 'https://www.linkedin.com/company/…',
			'default'     => '',
			'type'        => 'url',
		),

		// — Aviso de honorarios (barra de la home) —
		'honorarios_desde'    => array(
			'section'     => 'Aviso de honorarios (barra de la home)',
			'label'       => 'Vigentes desde',
			'desc'        => 'Fecha de vigencia de los honorarios mínimos. Ej: 01/04/2026.',
			'placeholder' => '01/04/2026',
			'default'     => '01/04/2026',
		),
		'honorarios_resolucion' => array(
			'section'     => 'Aviso de honorarios (barra de la home)',
			'label'       => 'Resolución',
			'desc'        => 'Solo el número, sin "Res.". Ej: 1553.',
			'placeholder' => '1553',
			'default'     => '1553',
		),
	);
}

/**
 * Valor de un dato general. Si nunca se guardó el formulario, el valor por
 * defecto; una vez guardado, lo que haya en el formulario (incluso vacío).
 */
function cipba_dato( $key ) {
	$fields = cipba_datos_fields();
	if ( ! isset( $fields[ $key ] ) ) {
		return '';
	}
	$saved = get_option( CIPBA_DATOS_OPTION, null );
	if ( is_array( $saved ) && array_key_exists( $key, $saved ) ) {
		return (string) $saved[ $key ];
	}
	return $fields[ $key ]['default'];
}

/**
 * Enlace listo para un dato de contacto: whatsapp → wa.me, telefono → tel:,
 * email → mailto:. Vacío si el dato está vacío.
 */
function cipba_dato_url( $key ) {
	$value = trim( cipba_dato( $key ) );
	if ( '' === $value ) {
		return '';
	}
	switch ( $key ) {
		case 'whatsapp':
			$digits = ltrim( cipba_tel_link( $value ), '+' );
			return $digits ? 'https://wa.me/' . $digits : '';
		case 'telefono':
			$tel = cipba_tel_link( $value );
			return $tel ? 'tel:' . $tel : '';
		case 'email':
			return 'mailto:' . $value;
	}
	return $value; // Redes sociales: ya es una URL.
}

/**
 * [cipba_dato campo="periodo_autoridades"] — imprime un dato general.
 * [cipba_dato campo="whatsapp" formato="url"] — imprime el enlace (para href).
 */
function cipba_dato_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'campo' => '', 'formato' => '' ), $atts, 'cipba_dato' );
	if ( 'url' === $atts['formato'] ) {
		return esc_url( cipba_dato_url( $atts['campo'] ) );
	}
	return esc_html( cipba_dato( $atts['campo'] ) );
}
add_shortcode( 'cipba_dato', 'cipba_dato_shortcode' );

/**
 * Pantalla del admin.
 */
function cipba_datos_admin_menu() {
	add_menu_page(
		'Datos del Distrito',
		'Datos del Distrito',
		'edit_pages',
		'cipba-datos-distrito',
		'cipba_datos_render_page',
		'dashicons-admin-site-alt3',
		30
	);
}
add_action( 'admin_menu', 'cipba_datos_admin_menu' );

function cipba_datos_register_setting() {
	register_setting( 'cipba_datos_group', CIPBA_DATOS_OPTION, array(
		'type'              => 'array',
		'sanitize_callback' => 'cipba_datos_sanitize',
		'default'           => array(),
	) );
}
add_action( 'admin_init', 'cipba_datos_register_setting' );

/**
 * Por defecto guardar una opción exige "manage_options" (solo administradores);
 * se permite a quien puede editar páginas, igual que el resto del contenido.
 */
function cipba_datos_capability() {
	return 'edit_pages';
}
add_filter( 'option_page_capability_cipba_datos_group', 'cipba_datos_capability' );

function cipba_datos_sanitize( $input ) {
	$out = array();
	foreach ( cipba_datos_fields() as $key => $f ) {
		$raw  = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : '';
		$type = isset( $f['type'] ) ? $f['type'] : 'text';
		if ( 'url' === $type ) {
			$out[ $key ] = esc_url_raw( trim( $raw ) );
		} elseif ( 'email' === $type ) {
			$out[ $key ] = sanitize_email( $raw );
		} else {
			$out[ $key ] = sanitize_text_field( $raw );
		}
	}
	return $out;
}

function cipba_datos_render_page() {
	$sections = array();
	foreach ( cipba_datos_fields() as $key => $f ) {
		$sections[ $f['section'] ][ $key ] = $f;
	}
	?>
	<div class="wrap">
		<h1>Datos del Distrito</h1>
		<p>Datos generales que se usan en distintas secciones del sitio. Al cambiarlos acá se actualizan en todos los lugares donde se muestran.</p>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'cipba_datos_group' ); ?>
			<?php foreach ( $sections as $title => $fields ) : ?>
				<h2 class="title"><?php echo esc_html( $title ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $fields as $key => $f ) : ?>
						<tr>
							<th scope="row"><label for="cipba-dato-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $f['label'] ); ?></label></th>
							<td>
								<input type="<?php echo esc_attr( isset( $f['type'] ) ? $f['type'] : 'text' ); ?>" class="regular-text" id="cipba-dato-<?php echo esc_attr( $key ); ?>"
									name="<?php echo esc_attr( CIPBA_DATOS_OPTION . '[' . $key . ']' ); ?>"
									value="<?php echo esc_attr( cipba_dato( $key ) ); ?>"
									placeholder="<?php echo esc_attr( $f['placeholder'] ); ?>"
									<?php echo ! empty( $f['required'] ) ? 'required' : ''; ?>>
								<?php if ( $f['desc'] ) : ?><p class="description"><?php echo esc_html( $f['desc'] ); ?></p><?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button( 'Guardar cambios' ); ?>
		</form>
	</div>
	<?php
}
