<?php
/**
 * Empaqueta wp-content en /tmp/wp-content.zip desde adentro del contenedor.
 *
 * Se corre asi (ver project/runbook-migracion-manual.md, Fase 2):
 *   docker cp .\scripts\zip-wp-content.php wordpress_app:/tmp/zip-wp-content.php
 *   docker exec wordpress_app php -d memory_limit=1G /tmp/zip-wp-content.php
 *   docker cp wordpress_app:/tmp/wp-content.zip .\backups\wp-content-<DOMINIO>-AAAAMMDD.zip
 *
 * Por que adentro del contenedor y no con tar/Compress-Archive en Windows:
 * - varias rutas de vendor/ (UpdraftPlus, FluentSMTP, Rank Math) pasan los 260
 *   caracteres de MAX_PATH y PowerShell 5.1 no las puede ni listar ni borrar;
 * - Compress-Archive genera rutas con "\" que se rompen al extraer en Linux.
 */
$raiz    = '/var/www/html';
$destino = '/tmp/wp-content.zip';

$excluir = array(
	'wp-content/cache',
	'wp-content/upgrade',
	// Copia de la version ANTERIOR de cada plugin que deja `wp plugin update`.
	// Sin esta exclusion el paquete se lleva al servidor la version vulnerable.
	'wp-content/upgrade-temp-backup',
	'wp-content/updraft',
	'wp-content/duplicator-backups',
	'wp-content/plugins/wp-reset',
	'wp-content/plugins/duplicator',
);

$excluido = function ( $rel ) use ( $excluir ) {
	foreach ( $excluir as $e ) {
		if ( $rel === $e || strpos( $rel, $e . '/' ) === 0 ) {
			return true;
		}
	}
	return false;
};

if ( file_exists( $destino ) ) {
	unlink( $destino );
}

$zip = new ZipArchive();
if ( $zip->open( $destino, ZipArchive::CREATE ) !== true ) {
	fwrite( STDERR, "no se pudo crear $destino\n" );
	exit( 1 );
}

$it = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $raiz . '/wp-content', FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);

$archivos = 0;
$dirs     = 0;
$saltados = 0;
foreach ( $it as $ruta ) {
	$rel = substr( $ruta->getPathname(), strlen( $raiz ) + 1 );
	if ( $excluido( $rel ) ) {
		$saltados++;
		continue;
	}
	if ( $ruta->isDir() ) {
		$zip->addEmptyDir( $rel );
		$dirs++;
	} elseif ( $ruta->isFile() ) {
		$zip->addFile( $ruta->getPathname(), $rel );
		$archivos++;
	}
}

if ( ! $zip->close() ) {
	fwrite( STDERR, "error al cerrar el zip\n" );
	exit( 1 );
}

printf(
	"archivos: %d  carpetas: %d  excluidos: %d  tamaño: %.1f MB\n",
	$archivos,
	$dirs,
	$saltados,
	filesize( $destino ) / 1048576
);
