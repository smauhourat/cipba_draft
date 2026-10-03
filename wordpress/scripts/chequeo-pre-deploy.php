<?php
/**
 * Chequeos de la Fase 0-bis del runbook, en una sola corrida. Solo lee, no modifica nada.
 *
 *   docker cp .\scripts\chequeo-pre-deploy.php wordpress_app:/tmp/chequeo-pre-deploy.php
 *   docker exec wordpress_app wp --allow-root eval-file /tmp/chequeo-pre-deploy.php
 */
global $wpdb;

$linea = function ( $etiqueta, $valor, $ok = null ) {
	$marca = ( null === $ok ) ? ' ' : ( $ok ? '+' : '!' );
	printf( "[%s] %-34s %s\n", $marca, $etiqueta, $valor );
};

// --- Usuarios ---------------------------------------------------------------
echo "== Usuarios ==\n";
foreach ( get_users() as $u ) {
	$linea(
		"#{$u->ID} " . implode( ',', $u->roles ),
		"{$u->user_login} <{$u->user_email}> display={$u->display_name}",
		'admin' !== $u->user_login
	);
}

// --- Opciones ---------------------------------------------------------------
echo "\n== Opciones ==\n";
$linea( 'users_can_register', get_option( 'users_can_register' ), ! get_option( 'users_can_register' ) );
$linea( 'blog_public', get_option( 'blog_public' ) );
$linea( 'permalink_structure', get_option( 'permalink_structure' ), '/%postname%/' === get_option( 'permalink_structure' ) );
$linea( 'siteurl', get_option( 'siteurl' ) );

$tabla = $wpdb->prefix . 'fluentform_submissions';
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $tabla ) ) ) {
	$n = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tabla" );
	$linea( 'entradas de Fluent Forms', $n . ( $n ? ' (vaciar antes de exportar)' : '' ), 0 === $n );
}

// --- URLs locales que van a viajar -----------------------------------------
echo "\n== Opciones con localhost:8080 (las reemplaza la Fase 1) ==\n";
$rows = $wpdb->get_results( "SELECT option_name FROM {$wpdb->options} WHERE option_value LIKE '%localhost:8080%'" );
foreach ( $rows as $r ) {
	$linea( '', $r->option_name );
}

// --- Plugins ----------------------------------------------------------------
echo "\n== Plugins con actualizacion pendiente ==\n";
wp_update_plugins();
$updates = get_site_transient( 'update_plugins' );
$activos = get_option( 'active_plugins', array() );
if ( empty( $updates->response ) ) {
	$linea( 'todo al dia', '', true );
} else {
	foreach ( $updates->response as $archivo => $info ) {
		$datos = get_plugin_data( WP_PLUGIN_DIR . '/' . $archivo, false, false );
		$linea(
			dirname( $archivo ),
			"{$datos['Version']} -> {$info->new_version}" . ( in_array( $archivo, $activos, true ) ? ' (activo)' : '' ),
			false
		);
	}
}

// --- Rastros del kit del atacante ------------------------------------------
echo "\n== Archivos sospechosos en wp-content ==\n";
$sospechosos = array( 'wp-signup.php', '.well-known', '.tmb', 'system' );
$hallados    = array();
$it          = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( WP_CONTENT_DIR, FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);
foreach ( $it as $ruta ) {
	$rel = str_replace( WP_CONTENT_DIR . '/', '', $ruta->getPathname() );
	if ( in_array( $ruta->getFilename(), $sospechosos, true ) || 0 === strpos( $ruta->getFilename(), 'wp-file-manager' ) ) {
		$hallados[] = $rel;
	}
	// Cualquier PHP ejecutable dentro de uploads/.
	if ( 0 === strpos( $rel, 'uploads/' ) && $ruta->isFile() && preg_match( '/\.(php|phtml|pht)[0-9]?$/i', $ruta->getFilename() ) ) {
		$hallados[] = $rel;
	}
}
if ( $hallados ) {
	foreach ( $hallados as $h ) {
		$linea( 'REVISAR', $h, false );
	}
} else {
	$linea( 'nada encontrado', '', true );
}
