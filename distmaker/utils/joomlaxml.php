<?php

if( isset( $GLOBALS['_SERVER']['DM_SOURCEDIR'] ) ) {
    $sourceCheckoutDir = $GLOBALS['_SERVER']['DM_SOURCEDIR'];
} else {
    $sourceCheckoutDir = $argv[1];
}
$sourceCheckoutDirLength = strlen( $sourceCheckoutDir );

if( isset( $GLOBALS['_SERVER']['DM_TMPDIR'] ) ) {
    $targetDir = $GLOBALS['_SERVER']['DM_TMPDIR'] . '/com_civicrm/admin/civicrm';
} else {
    $targetDir = $argv[2];
}
$targetDirLength = strlen( $targetDir );

ini_set('include_path', ini_get('include_path') . ":$sourceCheckoutDir/packages");
require_once "$sourceCheckoutDir/civicrm.config.php";
require_once 'Smarty/Smarty.class.php';

generateJoomlaConfig( );

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

function generateJoomlaConfig( ) {
    global $targetDir, $sourceCheckoutDir;

    $smarty =& new Smarty( );
    $smarty->template_dir = $sourceCheckoutDir . '/xml/templates';
    $smarty->compile_dir  = '/tmp/templates_c';
    createDir( $smarty->compile_dir );

    $xml = $smarty->fetch( 'joomla.tpl' );
    
    $output = $targetDir . '/joomla/civicrm.xml';
    $fd = fopen( $output, "w" );
    fputs( $fd, $xml );
    fclose( $fd );
    
}
