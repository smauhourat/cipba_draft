<?php
/**
 * Verifica un zip de deploy (Fase 2 del runbook) desde adentro del contenedor.
 * Solo lee. Imprime clave=valor para que lo procese preparar-deploy.ps1.
 *
 *   docker exec wordpress_app php /tmp/verificar-zip.php /tmp/wp-content.zip [prefijo]
 *
 * prefijo: carpeta raiz esperada dentro del zip ("wp-content" o "cipba").
 */
$archivo = isset( $argv[1] ) ? $argv[1] : '/tmp/wp-content.zip';
$prefijo = isset( $argv[2] ) ? $argv[2] : 'wp-content';

$zip = new ZipArchive();
if ( true !== $zip->open( $archivo ) ) {
	echo "error=no se pudo abrir $archivo\n";
	exit( 1 );
}

$barras  = 0;
$upgrade = 0;
$fuera   = 0;
for ( $i = 0; $i < $zip->numFiles; $i++ ) {
	$n = $zip->getNameIndex( $i );
	if ( false !== strpos( $n, '\\' ) ) {
		$barras++;
	}
	if ( false !== strpos( $n, 'upgrade-temp-backup' ) ) {
		$upgrade++;
	}
	if ( 0 !== strpos( $n, $prefijo . '/' ) && $n !== $prefijo ) {
		$fuera++;
	}
}

$tema = 'wp-content' === $prefijo ? 'wp-content/themes/cipba/style.css' : 'cipba/style.css';

echo 'entradas=' . $zip->numFiles . "\n";
echo "rutas_con_barra_invertida=$barras\n";
echo "entradas_upgrade_temp_backup=$upgrade\n";
echo "entradas_fuera_de_$prefijo=$fuera\n";
echo 'tema_cipba=' . ( false !== $zip->locateName( $tema ) ? 'si' : 'no' ) . "\n";
if ( 'wp-content' === $prefijo ) {
	foreach ( array( 'meta-box', 'litespeed-cache', 'megamenu', 'fluentform' ) as $p ) {
		$ok = false !== $zip->locateName( "wp-content/plugins/$p/" ) || false !== $zip->locateName( "wp-content/plugins/$p" );
		echo "plugin_$p=" . ( $ok ? 'si' : 'no' ) . "\n";
	}
}
$zip->close();
