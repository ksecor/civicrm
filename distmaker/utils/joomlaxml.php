<?php

if( isset( $GLOBALS['_SERVER']['DM_SOURCEDIR'] ) ) {
    $sourceCheckoutDir = $GLOBALS['_SERVER']['DM_SOURCEDIR'];
} else {
    $sourceCheckoutDir = $argv[1];
}
$sourceCheckoutDirLength = strlen( $sourceCheckoutDir );

if( isset( $GLOBALS['_SERVER']['DM_TMPDIR'] ) ) {
    $targetDir = $GLOBALS['_SERVER']['DM_TMPDIR'] . '/com_civicrm';
} else {
    $targetDir = $argv[2];
}
$targetDirLength = strlen( $targetDir );

if( isset( $GLOBALS['_SERVER']['DM_VERSION'] ) ) {
    $version = $GLOBALS['_SERVER']['DM_VERSION'];
} else {
    $version = $argv[3];
}

if( isset( $GLOBALS['_SERVER']['DM_PKGTYPE'] ) ) {
    $pkgType = $GLOBALS['_SERVER']['DM_PKGTYPE'];
} else {
    $pkgType = $argv[4];
}

ini_set('include_path', ini_get('include_path') . ":$sourceCheckoutDir/packages");
require_once "$sourceCheckoutDir/civicrm.config.php";
require_once 'Smarty/Smarty.class.php';

generateJoomlaConfig( $version );

/**
 * This function creates destination directory
 *
 * @param $dir directory name to be created
 * @param $peram mode for that directory
 *
 */
function createDir( $dir, $perm = 0755 ) {
    if ( ! is_dir( $dir ) ) {
        echo "Outdir: $dir\n";
        mkdir( $dir, $perm, true );
    }
}

function generateJoomlaConfig( $version ) {
    global $targetDir, $sourceCheckoutDir, $pkgType;

    $smarty =& new Smarty( );
    $smarty->template_dir = $sourceCheckoutDir . '/xml/templates';
    $smarty->compile_dir  = '/tmp/templates_c';
    createDir( $smarty->compile_dir );

    $smarty->assign( 'CiviCRMVersion', $version );
    $smarty->assign( 'pkgType', $pkgType );

    $xml = $smarty->fetch( 'joomla.tpl' );
    
    $output = $targetDir . '/civicrm.xml';
    $fd = fopen( $output, "w" );
    fputs( $fd, $xml );
    fclose( $fd );
    
}
