<?php
/**
 * Shortcodes para secciones de la home que necesitan datos dinámicos
 * (Query Loop real, no contenido fijo) — eventos, y a futuro noticias
 * y subcomisiones si hace falta más control del que da el bloque nativo.
 *
 * @package cipba
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cipba_fecha_corta( $fecha ) {
	if ( ! $fecha ) {
		return '';
	}
	$meses = array( 1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic' );
	$ts = strtotime( $fecha );
	if ( ! $ts ) {
		return '';
	}
	return (int) date( 'j', $ts ) . ' ' . $meses[ (int) date( 'n', $ts ) ] . ' ' . date( 'Y', $ts );
}

/**
 * [cipba_eventos] — sección "Eventos y novedades" de la home: hasta 3 publicaciones de tipo
 * "Evento o novedad", sin las actividades ya realizadas, por fecha
 * ascendente (ver cipba_get_agenda() en inc/novedades.php).
 */
function cipba_eventos_shortcode() {
	$agenda = cipba_get_agenda( 3 );

	if ( ! $agenda ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-ev-grid">
		<?php foreach ( $agenda as $p ) :
			$ini   = trim( (string) get_post_meta( $p->ID, 'fecha_inicio', true ) );
			$fecha = $ini ? cipba_fecha_corta( $ini ) : cipba_fecha_novedad( $p->ID );
			?>
			<a href="<?php echo esc_url( get_permalink( $p->ID ) ); ?>" class="cipba-ev-card">
				<div class="cipba-ev-card__img">
					<?php echo get_the_post_thumbnail( $p->ID, 'cipba-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<div class="cipba-ev-card__body">
					<h3><?php echo esc_html( get_the_title( $p->ID ) ); ?></h3>
					<div class="cipba-ev-card__foot">
						<span class="cipba-ev-card__date"><?php echo esc_html( $fecha ); ?></span>
						<span class="cipba-ev-card__more">Ver detalle ›</span>
					</div>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cipba_eventos', 'cipba_eventos_shortcode' );

/**
 * [cipba_noticias] — sección "Noticias" de la home: las 5 últimas publicaciones
 * de tipo "Noticia" (entradas nativas), con la categoría en el color de su
 * paleta, fecha d/m/Y, imagen, título y bajada.
 */
function cipba_noticias_shortcode() {
	$query = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 5,
		'meta_query'     => cipba_meta_query_tipo( 'noticia' ), // Solo tipo "Noticia" (los eventos y novedades van en la agenda).
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-news-grid">
		<?php while ( $query->have_posts() ) : $query->the_post();
			$cat   = cipba_novedad_categoria( get_the_ID() );
			$color = $cat ? cipba_cat_paleta( $cat->term_id )['ink'] : '#607a7c';
			?>
			<a href="<?php the_permalink(); ?>" class="cipba-news-card">
				<div class="cipba-news-card__meta">
					<?php if ( $cat ) : ?>
						<span class="cipba-news-card__cat" style="color:<?php echo esc_attr( $color ); ?>"><?php echo esc_html( $cat->name ); ?></span>
						<span class="cipba-news-card__sep">· <?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
					<?php else : ?>
						<span class="cipba-news-card__sep"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
					<?php endif; ?>
				</div>
				<div class="cipba-news-card__img">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'cipba-thumb' ); ?>
					<?php endif; ?>
				</div>
				<div class="cipba-news-card__body">
					<h3><?php the_title(); ?></h3>
					<p><?php echo esc_html( cipba_bajada( get_the_ID() ) ); ?></p>
				</div>
			</a>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'cipba_noticias', 'cipba_noticias_shortcode' );

/**
 * [cipba_subcomisiones] — 3 subcomisiones (CPT subcomision), tarjeta
 * simple: título, descripción (si está cargada) y mail de la subcomisión. Todas enlazan al
 * listado completo (así lo hace el prototipo, no a fichas individuales).
 */
function cipba_subcomisiones_shortcode() {
	$query = new WP_Query( array(
		'post_type'      => 'subcomision',
		'posts_per_page' => 3,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	) );

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-subcom-grid">
		<?php while ( $query->have_posts() ) : $query->the_post();
			$mail = trim( (string) get_post_meta( get_the_ID(), 'mail', true ) );
			?>
			<a href="/subcomisiones/" class="cipba-subcom-card">
				<h3><?php the_title(); ?></h3>
				<?php $descripcion = trim( (string) get_post_meta( get_the_ID(), 'descripcion', true ) ); ?>
				<?php if ( $descripcion ) : ?><p><?php echo esc_html( $descripcion ); ?></p><?php endif; ?>
				<?php if ( $mail ) : ?>
					<span><?php echo esc_html( $mail ); ?></span>
				<?php endif; ?>
			</a>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'cipba_subcomisiones', 'cipba_subcomisiones_shortcode' );

/**
 * [cipba_subcomisiones_listado] — página /subcomisiones/: contador, buscador
 * (filtra en el navegador por especialidad, sigla, referente o mail) y grilla
 * de tarjetas con todos los referentes. El markup vive en
 * template-parts/subcomisiones-list.php. Todo sale del CPT `subcomision`.
 */
function cipba_subcomisiones_listado_shortcode() {
	wp_enqueue_script( 'cipba-subcomisiones', get_stylesheet_directory_uri() . '/assets/js/subcomisiones.js', array(), CHILD_THEME_CIPBA_VERSION, true );

	ob_start();
	get_template_part( 'template-parts/subcomisiones-list' );
	return ob_get_clean();
}
add_shortcode( 'cipba_subcomisiones_listado', 'cipba_subcomisiones_listado_shortcode' );

/**
 * [cipba_sedes] — sedes y delegaciones (CPT `sede`): la Casa Central como
 * tarjeta ancha con contactos directos y el resto en grilla de 3 columnas.
 * El markup vive en template-parts/sedes-list.php.
 */
function cipba_sedes_shortcode() {
	ob_start();
	get_template_part( 'template-parts/sedes-list' );
	return ob_get_clean();
}
add_shortcode( 'cipba_sedes', 'cipba_sedes_shortcode' );

/**
 * Sedes en la home ("El Distrito VII"): mismos datos que la página de
 * Institucional (CPT `sede`), en dos piezas porque el diseño las separa.
 *
 * [cipba_sede_principal] — tarjeta oscura con la sede marcada como Casa
 *   Central: dirección, teléfono, correo y horario.
 * [cipba_delegaciones]   — grilla con el resto de las sedes: tipo, nombre,
 *   dirección y horario.
 */
function cipba_get_sedes() {
	return get_posts( array(
		'post_type'   => 'sede',
		'numberposts' => -1,
		'orderby'     => 'menu_order title',
		'order'       => 'ASC',
	) );
}

function cipba_sede_principal_shortcode() {
	$principal = null;
	foreach ( cipba_get_sedes() as $sede ) {
		if ( get_post_meta( $sede->ID, 'destacada', true ) ) {
			$principal = $sede;
			break;
		}
	}
	if ( ! $principal ) {
		return '';
	}

	$id   = $principal->ID;
	$rows = array(
		array( 'location', 'Dirección', trim( (string) get_post_meta( $id, 'direccion', true ) ), false ),
		array( 'phone', 'Teléfono', trim( (string) get_post_meta( $id, 'telefono', true ) ), false ),
		array( 'mail', 'Correo electrónico', trim( (string) get_post_meta( $id, 'email', true ) ), true ),
		array( 'clock', 'Horario de atención', trim( (string) get_post_meta( $id, 'horario', true ) ), false ),
	);

	ob_start();
	?>
	<div class="cipba-dist-sede">
		<h3><?php echo esc_html( get_the_title( $id ) ); ?></h3>
		<div class="cipba-dist-sede__badge"><?php echo esc_html( get_post_meta( $id, 'tag', true ) ); ?></div>
		<?php foreach ( $rows as $r ) :
			if ( '' === $r[2] ) {
				continue;
			}
			?>
			<div class="cipba-dist-sede__row"><span class="ico"><?php echo cipba_icon( $r[0], 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><div><div class="lbl"><?php echo esc_html( $r[1] ); ?></div><div class="val"><?php
				if ( $r[3] ) {
					echo '<a href="mailto:' . esc_attr( antispambot( $r[2] ) ) . '">' . esc_html( $r[2] ) . '</a>';
				} else {
					echo esc_html( $r[2] );
				}
			?></div></div></div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cipba_sede_principal', 'cipba_sede_principal_shortcode' );

function cipba_delegaciones_shortcode() {
	$cards = array();
	foreach ( cipba_get_sedes() as $sede ) {
		if ( get_post_meta( $sede->ID, 'destacada', true ) ) {
			continue;
		}
		$cards[] = $sede;
	}
	if ( ! $cards ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-sede-grid">
		<?php foreach ( $cards as $sede ) :
			$lines = array_filter( array(
				trim( (string) get_post_meta( $sede->ID, 'direccion', true ) ),
				trim( (string) get_post_meta( $sede->ID, 'horario', true ) ),
			) );
			?>
			<div class="cipba-sede-card">
				<h3><?php echo esc_html( get_the_title( $sede ) ); ?></h3>
				<div class="cipba-sede-card__role"><?php echo esc_html( get_post_meta( $sede->ID, 'tag', true ) ); ?></div>
				<?php foreach ( $lines as $line ) : ?>
					<div class="cipba-sede-card__line"><?php echo esc_html( $line ); ?></div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cipba_delegaciones', 'cipba_delegaciones_shortcode' );

/**
 * [cipba_partidos] — sección "Partidos comprendidos" (página Institucional):
 * mapa del Distrito VII y listado de partidos con buscador en el navegador.
 * El markup vive en template-parts/partidos-list.php.
 */
function cipba_partidos_shortcode() {
	wp_enqueue_script( 'cipba-partidos', get_stylesheet_directory_uri() . '/assets/js/partidos.js', array(), CHILD_THEME_CIPBA_VERSION, true );

	ob_start();
	get_template_part( 'template-parts/partidos-list' );
	return ob_get_clean();
}
add_shortcode( 'cipba_partidos', 'cipba_partidos_shortcode' );

/**
 * [cipba_autoridades] — autoridades del Consejo Directivo (CPT `autoridad`):
 * el presidente como tarjeta destacada de doble ancho y el resto en grilla.
 * El markup vive en template-parts/autoridades-list.php.
 */
function cipba_autoridades_shortcode() {
	ob_start();
	get_template_part( 'template-parts/autoridades-list' );
	return ob_get_clean();
}
add_shortcode( 'cipba_autoridades', 'cipba_autoridades_shortcode' );

/**
 * Página Contacto.
 *
 * [cipba_areas_contacto]  — tarjetas de área (CPT `area_contacto`): personas
 *   con teléfono y correo del área.
 * [cipba_boton_whatsapp texto="…"] — botón verde de WhatsApp (dato general);
 *   no muestra nada si el WhatsApp está vacío en "Datos del Distrito".
 * [cipba_sedes_contacto]  — sedes y delegaciones en tarjetas compactas (CPT
 *   `sede`), la Casa Central destacada.
 */
function cipba_areas_contacto_shortcode() {
	$areas = get_posts( array(
		'post_type'   => 'area_contacto',
		'numberposts' => -1,
		'orderby'     => 'menu_order title',
		'order'       => 'ASC',
	) );
	if ( ! $areas ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-areas">
		<?php foreach ( $areas as $area ) :
			$icono    = get_post_meta( $area->ID, 'icono', true );
			$icono    = $icono ? $icono : 'users';
			$email    = trim( (string) get_post_meta( $area->ID, 'email', true ) );
			$personas = cipba_get_area_personas( $area->ID );
			?>
			<article class="cipba-area">
				<header class="cipba-area__head">
					<span class="cipba-area__ico"><?php echo cipba_icon( $icono, 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<h3><?php echo esc_html( get_the_title( $area ) ); ?></h3>
				</header>
				<?php if ( $personas ) : ?>
					<div class="cipba-area__people">
						<?php foreach ( $personas as $p ) : ?>
							<div class="cipba-area__person">
								<span class="cipba-area__name"><?php echo esc_html( $p['nombre'] ); ?></span>
								<span class="cipba-area__person-links">
									<?php if ( $p['tel'] ) : ?>
										<a href="tel:<?php echo esc_attr( cipba_tel_link( $p['tel'] ) ); ?>"><?php echo esc_html( $p['tel'] ); ?></a>
									<?php endif; ?>
									<?php if ( $p['email'] ) : ?>
										<a href="mailto:<?php echo esc_attr( antispambot( $p['email'] ) ); ?>"><?php echo esc_html( $p['email'] ); ?></a>
									<?php endif; ?>
								</span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<a class="cipba-area__mail" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo cipba_icon( 'mail', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $email ); ?></a>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cipba_areas_contacto', 'cipba_areas_contacto_shortcode' );

function cipba_boton_whatsapp_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'texto' => 'Consultar por WhatsApp' ), $atts, 'cipba_boton_whatsapp' );
	$url  = cipba_dato_url( 'whatsapp' );
	if ( ! $url ) {
		return '';
	}
	return '<a class="cipba-wa-btn" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . cipba_icon( 'whatsapp', 20 ) . ' ' . esc_html( $atts['texto'] ) . '</a>';
}
add_shortcode( 'cipba_boton_whatsapp', 'cipba_boton_whatsapp_shortcode' );

function cipba_sedes_contacto_shortcode() {
	$sedes = cipba_get_sedes();
	if ( ! $sedes ) {
		return '';
	}

	ob_start();
	?>
	<div class="cipba-sedes-mini">
		<?php foreach ( $sedes as $sede ) :
			$id   = $sede->ID;
			$main = (bool) get_post_meta( $id, 'destacada', true );
			$rows = array(
				array( 'location', trim( (string) get_post_meta( $id, 'direccion', true ) ), false ),
				array( 'phone', trim( (string) get_post_meta( $id, 'telefono', true ) ), false ),
				array( 'mail', trim( (string) get_post_meta( $id, 'email', true ) ), true ),
				array( 'clock', trim( (string) get_post_meta( $id, 'horario', true ) ), false ),
			);
			?>
			<article class="cipba-sede-mini<?php echo $main ? ' cipba-sede-mini--main' : ''; ?>">
				<div class="cipba-sede-mini__tag"><?php echo esc_html( get_post_meta( $id, 'tag', true ) ); ?></div>
				<h4><?php echo esc_html( get_the_title( $sede ) ); ?></h4>
				<div class="cipba-sede-mini__rows">
					<?php foreach ( $rows as $r ) :
						if ( '' === $r[1] ) {
							continue;
						}
						?>
						<div class="cipba-sede-mini__row"><?php echo cipba_icon( $r[0], 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span><?php
							if ( $r[2] ) {
								echo '<a href="mailto:' . esc_attr( antispambot( $r[1] ) ) . '">' . esc_html( $r[1] ) . '</a>';
							} else {
								echo esc_html( $r[1] );
							}
						?></span></div>
					<?php endforeach; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cipba_sedes_contacto', 'cipba_sedes_contacto_shortcode' );

/**
 * Formulario de Contacto: script que hace desaparecer solo el mensaje de
 * éxito/error del envío. Se carga solo en páginas que muestran un
 * formulario de Fluent Forms.
 */
function cipba_enqueue_contact_form_script() {
	$post = get_post();
	if ( is_singular() && $post && has_shortcode( $post->post_content, 'fluentform' ) ) {
		wp_enqueue_script( 'cipba-contact-form', get_stylesheet_directory_uri() . '/assets/js/contact-form.js', array(), CHILD_THEME_CIPBA_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'cipba_enqueue_contact_form_script' );
