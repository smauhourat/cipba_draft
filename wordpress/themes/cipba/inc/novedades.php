<?php
/**
 * Eventos y novedades: una sola lista de publicaciones (entradas nativas).
 * Cada publicación puede ser una novedad simple o una "actividad" con fecha
 * (ficha con lugar, horario, cupo…). El listado filtrable vive en /novedades/
 * ([cipba_novedades]) y el detalle en single.php.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==========================================================================
   Categorías: color y orden
   ========================================================================== */

/**
 * Paletas disponibles para el color de una categoría: clave => etiqueta, tinta, fondo.
 */
function cipba_cat_paletas() {
	return array(
		'violeta'      => array( 'Violeta', '#484586', '#f1f0f9' ),
		'verde'        => array( 'Verde', '#00705e', '#e8f5f1' ),
		'verde_oscuro' => array( 'Verde oscuro', '#14484a', '#eef2f1' ),
		'ladrillo'     => array( 'Ladrillo', '#8a3a2a', '#fbf0ed' ),
	);
}

/**
 * Colores (tinta y fondo) de una categoría; verde oscuro si no tiene paleta.
 */
function cipba_cat_paleta( $term_id ) {
	$paletas = cipba_cat_paletas();
	$clave   = get_term_meta( (int) $term_id, 'paleta', true );
	$p       = isset( $paletas[ $clave ] ) ? $paletas[ $clave ] : $paletas['verde_oscuro'];
	return array( 'ink' => $p[1], 'bg' => $p[2] );
}

function cipba_cat_form_fields( $term = null ) {
	$paletas = cipba_cat_paletas();
	$actual  = $term ? get_term_meta( $term->term_id, 'paleta', true ) : 'verde_oscuro';
	$orden   = $term ? get_term_meta( $term->term_id, 'orden', true ) : '';
	$row     = $term ? array( '<tr class="form-field"><th scope="row"><label for="cipba-paleta">Color</label></th><td>', '</td></tr>' ) : array( '<div class="form-field"><label for="cipba-paleta">Color</label>', '</div>' );
	$row2    = $term ? array( '<tr class="form-field"><th scope="row"><label for="cipba-orden">Orden</label></th><td>', '</td></tr>' ) : array( '<div class="form-field"><label for="cipba-orden">Orden</label>', '</div>' );

	echo $row[0]; // phpcs:ignore WordPress.Security.EscapeOutput
	echo '<select name="cipba_paleta" id="cipba-paleta">';
	foreach ( $paletas as $clave => $p ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $clave ), selected( $actual, $clave, false ), esc_html( $p[0] ) );
	}
	echo '</select><p class="description">Color de la etiqueta de la categoría en el listado y las tarjetas.</p>';
	echo $row[1]; // phpcs:ignore WordPress.Security.EscapeOutput

	echo $row2[0]; // phpcs:ignore WordPress.Security.EscapeOutput
	printf( '<input type="number" name="cipba_orden" id="cipba-orden" value="%s" min="0" step="1" style="width:6em"><p class="description">Posición en los filtros del listado de novedades (menor primero).</p>', esc_attr( $orden ) );
	echo $row2[1]; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'category_add_form_fields', 'cipba_cat_form_fields' );
add_action( 'category_edit_form_fields', 'cipba_cat_form_fields' );

function cipba_cat_save( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	if ( isset( $_POST['cipba_paleta'] ) && array_key_exists( $_POST['cipba_paleta'], cipba_cat_paletas() ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		update_term_meta( $term_id, 'paleta', sanitize_key( $_POST['cipba_paleta'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}
	if ( isset( $_POST['cipba_orden'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		update_term_meta( $term_id, 'orden', '' === $_POST['cipba_orden'] ? '' : absint( $_POST['cipba_orden'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}
}
add_action( 'created_category', 'cipba_cat_save' );
add_action( 'edited_category', 'cipba_cat_save' );

/**
 * Categorías con publicaciones, en el orden de los filtros (campo Orden, luego nombre).
 */
function cipba_novedades_categorias() {
	$terms = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => true ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	usort( $terms, function ( $a, $b ) {
		$oa = get_term_meta( $a->term_id, 'orden', true );
		$ob = get_term_meta( $b->term_id, 'orden', true );
		$oa = '' === $oa ? 999 : (int) $oa;
		$ob = '' === $ob ? 999 : (int) $ob;
		return $oa === $ob ? strcasecmp( $a->name, $b->name ) : $oa - $ob;
	} );
	return $terms;
}

/* ==========================================================================
   Fechas y textos de una publicación
   ========================================================================== */

/**
 * Bajada de la publicación (campo propio; si falta, el extracto nativo).
 */
function cipba_bajada( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$b       = trim( (string) get_post_meta( $post_id, 'bajada', true ) );
	return '' !== $b ? $b : wp_strip_all_tags( get_the_excerpt( $post_id ) );
}

/**
 * Fecha para tarjetas y encabezado: el texto alternativo ("Vigente todo 2026")
 * o la fecha de publicación como "09 abr 2026".
 */
function cipba_fecha_novedad( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$alt     = trim( (string) get_post_meta( $post_id, 'fecha_alt', true ) );
	if ( '' !== $alt ) {
		return $alt;
	}
	$meses = array( 1 => 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic' );
	$ts    = strtotime( get_post_field( 'post_date', $post_id ) );
	return sprintf( '%02d %s %d', (int) gmdate( 'j', $ts ), $meses[ (int) gmdate( 'n', $ts ) ], (int) gmdate( 'Y', $ts ) );
}

/**
 * "Jueves 9 de abril de 2026" a partir de Y-m-d (o "9 de abril" sin año/día con $corta).
 */
function cipba_fecha_larga( $ymd, $con_dia = true ) {
	$ts = strtotime( $ymd );
	if ( ! $ts ) {
		return '';
	}
	$dias  = array( 'domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado' );
	$meses = array( 1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' );
	$txt   = sprintf( '%d de %s de %d', (int) gmdate( 'j', $ts ), $meses[ (int) gmdate( 'n', $ts ) ], (int) gmdate( 'Y', $ts ) );
	return $con_dia ? ucfirst( $dias[ (int) gmdate( 'w', $ts ) ] ) . ' ' . $txt : $txt;
}

/**
 * Fecha de la actividad en texto: un día ("Jueves 9 de abril de 2026") o un
 * rango ("Del 6 al 27 de mayo de 2026").
 */
function cipba_actividad_fecha( $post_id ) {
	$ini = trim( (string) get_post_meta( $post_id, 'fecha_inicio', true ) );
	$fin = trim( (string) get_post_meta( $post_id, 'fecha_fin', true ) );
	if ( ! $ini ) {
		return '';
	}
	if ( ! $fin || $fin <= $ini ) {
		return cipba_fecha_larga( $ini );
	}
	$meses = array( 1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' );
	$a     = strtotime( $ini );
	$b     = strtotime( $fin );
	if ( gmdate( 'Y-m', $a ) === gmdate( 'Y-m', $b ) ) {
		return sprintf( 'Del %d al %d de %s de %d', (int) gmdate( 'j', $a ), (int) gmdate( 'j', $b ), $meses[ (int) gmdate( 'n', $b ) ], (int) gmdate( 'Y', $b ) );
	}
	return sprintf( 'Del %s al %s', cipba_fecha_larga( $ini, false ), cipba_fecha_larga( $fin, false ) );
}

/**
 * Filas de la ficha "Datos de la actividad": array( icono, etiqueta, valor ).
 */
function cipba_actividad_filas( $post_id ) {
	$g   = function ( $k ) use ( $post_id ) {
		return trim( (string) get_post_meta( $post_id, $k, true ) );
	};
	$mod = $g( 'modalidad' );
	if ( $mod && $g( 'modalidad_nota' ) ) {
		$mod .= ' ' . $g( 'modalidad_nota' );
	}
	$insc = '';
	if ( $g( 'inscripcion_abierta' ) ) {
		$insc = $g( 'cierre_inscripcion' ) ? 'Abierta hasta el ' . cipba_fecha_larga( $g( 'cierre_inscripcion' ), false ) : 'Abierta';
	}
	$filas = array(
		array( 'calendar', 'Fecha', cipba_actividad_fecha( $post_id ) ),
		array( 'clock', 'Horario', $g( 'horario' ) ),
		array( 'location', 'Lugar', $g( 'lugar' ) ),
		array( 'building', 'Modalidad', $mod ),
		array( 'users', 'Cupo', $g( 'cupo' ) ),
		array( 'dollar', 'Arancel', $g( 'arancel' ) ),
		array( 'check', 'Inscripción', $insc ),
	);
	return array_values( array_filter( $filas, function ( $f ) {
		return '' !== $f[2];
	} ) );
}

/**
 * Botón de acción de la publicación: array( texto, url ) — enlace libre, o el
 * archivo de un documento de la biblioteca; sin ninguno, lleva a Contacto.
 */
function cipba_novedad_cta( $post_id ) {
	$texto = trim( (string) get_post_meta( $post_id, 'cta_texto', true ) );
	$url   = trim( (string) get_post_meta( $post_id, 'cta_url', true ) );
	if ( '' === $url ) {
		$doc_id = (int) get_post_meta( $post_id, 'cta_doc', true );
		if ( $doc_id && 'publish' === get_post_status( $doc_id ) ) {
			$file = cipba_get_documento_file_meta( $doc_id );
			$url  = $file ? $file['url'] : '';
		}
	}
	if ( '' === $url ) {
		// Sin enlace ni documento: el botón lleva a Contacto (con su texto, si lo tiene).
		return array( '' !== $texto ? $texto : 'Escribinos', home_url( '/contacto/' ) );
	}
	return array( '' !== $texto ? $texto : 'Ver más', $url );
}

/**
 * Categoría principal de la publicación: objeto de término o null.
 */
function cipba_novedad_categoria( $post_id ) {
	$cats = get_the_category( $post_id );
	return $cats ? $cats[0] : null;
}

/**
 * Etiqueta (pill) de la categoría con los colores de su paleta.
 */
function cipba_cat_badge( $post_id, $size = 11 ) {
	$cat = cipba_novedad_categoria( $post_id );
	if ( ! $cat ) {
		return '';
	}
	$p = cipba_cat_paleta( $cat->term_id );
	return sprintf( '<span class="cipba-badge" style="background:%s;color:%s;font-size:%spx">%s</span>', esc_attr( $p['bg'] ), esc_attr( $p['ink'] ), esc_attr( $size ), esc_html( $cat->name ) );
}

/* ==========================================================================
   Tarjetas
   ========================================================================== */

/**
 * Tarjeta de una publicación (listado y "Otras novedades").
 */
function cipba_render_nov_card( $post_id ) {
	$cat    = cipba_novedad_categoria( $post_id );
	$titulo = get_the_title( $post_id );
	$bajada = cipba_bajada( $post_id );
	?>
	<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="cipba-nov-card" data-nov-item data-cat="<?php echo esc_attr( $cat ? $cat->slug : '' ); ?>" data-search="<?php echo esc_attr( mb_strtolower( $titulo . ' ' . $bajada ) ); ?>">
		<div class="cipba-nov-card__img"><?php echo get_the_post_thumbnail( $post_id, 'cipba-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="cipba-nov-card__body">
			<div class="cipba-nov-card__meta"><?php echo cipba_cat_badge( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( cipba_fecha_novedad( $post_id ) ); ?></span></div>
			<h3><?php echo esc_html( $titulo ); ?></h3>
			<?php if ( $bajada ) : ?><p><?php echo esc_html( $bajada ); ?></p><?php endif; ?>
			<span class="cipba-nov-card__more">Leer más <?php echo cipba_icon( 'chevron', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</div>
	</a>
	<?php
}

/**
 * Tarjeta grande de la publicación destacada.
 */
function cipba_render_nov_dest( $post_id ) {
	$cat    = cipba_novedad_categoria( $post_id );
	$titulo = get_the_title( $post_id );
	$bajada = cipba_bajada( $post_id );
	$es_act = (bool) get_post_meta( $post_id, 'es_actividad', true );
	$lugar  = trim( (string) get_post_meta( $post_id, 'lugar', true ) );
	?>
	<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="cipba-nov-dest" data-nov-item data-cat="<?php echo esc_attr( $cat ? $cat->slug : '' ); ?>" data-search="<?php echo esc_attr( mb_strtolower( $titulo . ' ' . $bajada ) ); ?>">
		<div class="cipba-nov-dest__img"><?php echo get_the_post_thumbnail( $post_id, 'cipba-nov-hero' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="cipba-nov-dest__txt">
			<div class="cipba-nov-card__meta"><span class="cipba-badge cipba-badge--dest">Destacado</span><?php echo cipba_cat_badge( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( cipba_fecha_novedad( $post_id ) ); ?></span></div>
			<h2><?php echo esc_html( $titulo ); ?></h2>
			<?php if ( $bajada ) : ?><p><?php echo esc_html( $bajada ); ?></p><?php endif; ?>
			<?php if ( $es_act ) : ?>
				<div class="cipba-nov-dest__facts">
					<?php if ( cipba_actividad_fecha( $post_id ) ) : ?><span><?php echo cipba_icon( 'calendar', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( cipba_actividad_fecha( $post_id ) ); ?></span><?php endif; ?>
					<?php if ( $lugar ) : ?><span><?php echo cipba_icon( 'location', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $lugar ); ?></span><?php endif; ?>
				</div>
			<?php endif; ?>
			<span class="cipba-nov-dest__btn">Ver la novedad <?php echo cipba_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</div>
	</a>
	<?php
}

/**
 * Tabs del listado de /novedades/ ("Eventos y novedades" / "Noticias"):
 * etiqueta, título y bajada del encabezado — mismos textos que el prototipo
 * (novedades-tabs.html).
 */
function cipba_novedades_tabs() {
	return array(
		'evento'  => array(
			'label' => 'Eventos y novedades',
			'title' => 'Eventos y novedades',
			'desc'  => 'Jornadas, cursos, asambleas, beneficios y avisos internos del Distrito VII. Las actividades con inscripción abierta se publican con cupo y fecha de cierre.',
		),
		'noticia' => array(
			'label' => 'Noticias',
			'title' => 'Noticias',
			'desc'  => 'Novedades generales de la profesión: normativa, gestiones institucionales, comisiones y actualización profesional.',
		),
	);
}

/**
 * Tab actual del listado según ?tipo= en la URL ("evento" por defecto, igual
 * que el prototipo).
 */
function cipba_novedades_tipo_actual() {
	$tipo = isset( $_GET['tipo'] ) ? sanitize_key( wp_unslash( $_GET['tipo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	return 'noticia' === $tipo ? 'noticia' : 'evento';
}

/**
 * Enlace al listado de novedades ya en el tab indicado (para "volver" desde
 * el detalle al tab que corresponde, o para los links de la home).
 */
function cipba_novedades_url( $tipo ) {
	return add_query_arg( 'tipo', $tipo, home_url( '/novedades/' ) );
}

/**
 * Pestaña del navegador según el tab de /novedades/ (?tipo=), igual que hace
 * el prototipo con document.title.
 */
function cipba_novedades_document_title( $title ) {
	if ( is_page( 'novedades' ) ) {
		$tabs            = cipba_novedades_tabs();
		$title['title']  = $tabs[ cipba_novedades_tipo_actual() ]['title'];
	}
	return $title;
}
add_filter( 'document_title_parts', 'cipba_novedades_document_title' );

/**
 * [cipba_novedades] — listado completo: encabezado y tabs por tipo de
 * publicación, filtros por categoría, buscador, destacada y grilla de
 * tarjetas (el filtrado por categoría/búsqueda es en el navegador; el tab se
 * resuelve en el servidor vía ?tipo=).
 */
function cipba_novedades_shortcode() {
	wp_enqueue_script( 'cipba-novedades', get_stylesheet_directory_uri() . '/assets/js/novedades.js', array(), CHILD_THEME_CIPBA_VERSION, true );

	ob_start();
	get_template_part( 'template-parts/novedades-list' );
	return ob_get_clean();
}
add_shortcode( 'cipba_novedades', 'cipba_novedades_shortcode' );

/* ==========================================================================
   Administración
   ========================================================================== */

/**
 * Tipos de publicación: "noticia" (sección Noticias de la home; también lo que
 * no tenga tipo) y "evento" (sección Eventos y novedades / agenda).
 */
function cipba_tipos_pub() {
	return array(
		'noticia' => 'Noticia',
		'evento'  => 'Evento o novedad',
	);
}

function cipba_tipo_pub( $post_id ) {
	return 'evento' === get_post_meta( $post_id, 'tipo_pub', true ) ? 'evento' : 'noticia';
}

/**
 * meta_query para traer solo las publicaciones de un tipo (las sin tipo
 * cuentan como noticias).
 */
function cipba_meta_query_tipo( $tipo ) {
	if ( 'evento' === $tipo ) {
		return array( array( 'key' => 'tipo_pub', 'value' => 'evento' ) );
	}
	return array(
		'relation' => 'OR',
		array( 'key' => 'tipo_pub', 'value' => 'noticia' ),
		array( 'key' => 'tipo_pub', 'compare' => 'NOT EXISTS' ),
	);
}

/**
 * Listado de entradas del admin: columna Tipo (con destacada y fecha de la
 * actividad) y filtro por tipo.
 */
function cipba_post_admin_columns( $cols ) {
	$out = array();
	foreach ( $cols as $key => $label ) {
		$out[ $key ] = $label;
		if ( 'title' === $key ) {
			$out['tipo_pub'] = 'Tipo';
		}
	}
	return $out;
}
add_filter( 'manage_post_posts_columns', 'cipba_post_admin_columns' );

function cipba_post_admin_column_content( $col, $post_id ) {
	if ( 'tipo_pub' !== $col ) {
		return;
	}
	$tipos = cipba_tipos_pub();
	echo esc_html( $tipos[ cipba_tipo_pub( $post_id ) ] );
	if ( get_post_meta( $post_id, 'destacada', true ) ) {
		echo ' · ★ Destacada';
	}
	if ( get_post_meta( $post_id, 'es_actividad', true ) && cipba_actividad_fecha( $post_id ) ) {
		echo '<br><span style="color:#646970">' . esc_html( cipba_actividad_fecha( $post_id ) ) . '</span>';
	}
}
add_action( 'manage_post_posts_custom_column', 'cipba_post_admin_column_content', 10, 2 );

function cipba_post_admin_tipo_filter( $post_type ) {
	if ( 'post' !== $post_type ) {
		return;
	}
	$actual = isset( $_GET['tipo_pub_filtro'] ) ? sanitize_key( $_GET['tipo_pub_filtro'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	echo '<select name="tipo_pub_filtro"><option value="">Todos los tipos</option>';
	foreach ( cipba_tipos_pub() as $k => $label ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $actual, $k, false ), esc_html( $label ) );
	}
	echo '</select>';
}
add_action( 'restrict_manage_posts', 'cipba_post_admin_tipo_filter' );

function cipba_post_admin_tipo_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'post' !== $query->get( 'post_type' ) || empty( $_GET['tipo_pub_filtro'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$tipo = sanitize_key( $_GET['tipo_pub_filtro'] ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( array_key_exists( $tipo, cipba_tipos_pub() ) ) {
		$query->set( 'meta_query', cipba_meta_query_tipo( $tipo ) );
	}
}
add_action( 'pre_get_posts', 'cipba_post_admin_tipo_query' );

/**
 * En la edición de una entrada, la caja "Datos de la actividad" solo se ve si
 * está tildado "Es una actividad con fecha".
 */
function cipba_novedad_admin_script( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'post' === $screen->post_type ) {
		wp_enqueue_script( 'cipba-admin-novedad', get_stylesheet_directory_uri() . '/assets/js/admin-novedad.js', array(), CHILD_THEME_CIPBA_VERSION, true );
	}
}
add_action( 'admin_enqueue_scripts', 'cipba_novedad_admin_script' );

/**
 * Agenda de la home ("Eventos y novedades"): publicaciones de tipo "Evento o novedad", sin las
 * actividades ya pasadas, por fecha ascendente (fecha de la actividad o, si no
 * es una actividad, fecha de publicación).
 *
 * @return WP_Post[]
 */
function cipba_get_agenda( $limit = 3 ) {
	$posts = get_posts( array(
		'post_type'   => 'post',
		'numberposts' => -1,
		'meta_key'    => 'tipo_pub',
		'meta_value'  => 'evento',
	) );
	$hoy   = current_time( 'Y-m-d' );
	$items = array();
	foreach ( $posts as $p ) {
		$ini = trim( (string) get_post_meta( $p->ID, 'fecha_inicio', true ) );
		$fin = trim( (string) get_post_meta( $p->ID, 'fecha_fin', true ) );
		if ( $ini && ( $fin ? $fin : $ini ) < $hoy ) {
			continue; // Actividad ya realizada.
		}
		$items[] = array( 'post' => $p, 'fecha' => $ini ? $ini : substr( $p->post_date, 0, 10 ) );
	}
	usort( $items, function ( $a, $b ) {
		return strcmp( $a['fecha'], $b['fecha'] );
	} );
	return array_slice( wp_list_pluck( $items, 'post' ), 0, $limit );
}
