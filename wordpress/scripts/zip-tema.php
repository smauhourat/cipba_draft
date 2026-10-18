<?php
/**
 * Empaqueta solo el tema cipba en /tmp/cipba-tema.zip (re-deploy "solo cambio el
 * tema" del runbook). Se arma adentro del contenedor para que las rutas queden
 * con "/" y se extraigan bien en Linux.
 *
 *   docker cp .\scripts\zip-tema.php wordpress_app:/tmp/zip-tema.php
 *   docker exec wordpress_app php /tmp/zip-tema.php
 *
 * Dentro del zip todo queda bajo "cipba/": se sube a wp-content/themes/ y se extrae ahi.
 */
$origen  = '/var/www/html/wp-content/themes/cipba';
$destino = '/tmp/cipba-tema.zip';

if ( file_exists( $destino ) ) {
	unlink( $destino );
}

$zip = new ZipArchive();
if ( true !== $zip->open( $destino, ZipArchive::CREATE ) ) {
	fwrite( STDERR, "no se pudo crear $destino\n" );
	exit( 1 );
}

$it = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $origen, FilesystemIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);

$archivos = 0;
$dirs     = 0;
foreach ( $it as $ruta ) {
	$rel = 'cipba/' . substr( $ruta->getPathname(), strlen( $origen ) + 1 );
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

printf( "archivos: %d  carpetas: %d  tamano: %.1f MB\n", $archivos, $dirs, filesize( $destino ) / 1048576 );
