<?php

if( isset( $GLOBALS['_ENV']['DM_SOURCEDIR'] ) ) {
    $sourceCheckoutDir = $GLOBALS['_ENV']['DM_SOURCEDIR'];
} else {
    // backward compatibility
    $sourceCheckoutDir = $GLOBALS['_ENV']['HOME'] . '/svn/crm';
}
$sourceCheckoutDirLength = strlen( $sourceCheckoutDir );

if( isset( $GLOBALS['_ENV']['DM_GENFILESDIR'] ) ) {
    $targetDir = $GLOBALS['_ENV']['DM_GENFILESDIR'];
} else {
    // backward compatibility
    $targetDir = $GLOBALS['_ENV']['HOME'] . '/svn/crm';
}

require_once "$sourceCheckoutDir/modules/config.inc.php";
require_once 'Smarty/Smarty.class.php';

$path = array( 'CRM', 'api', 'bin', 'css', 'gmaps', 'i', 'js', 'l10n', 'sql', 'templates', 'mambo', 'packages' );
$files = array( 'license.txt' => 1 );
foreach ( $path as $v ) {
    $rootDir = "$sourceCheckoutDir/$v";
    walkDirectory( new DirectoryIterator( $rootDir ), $files, $sourceCheckoutDirLength );
}
generateMamboConfig( $files );

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

function generateMamboConfig( &$files ) {
    global $sourceCheckoutDir;

    $smarty =& new Smarty( );
    $smarty->template_dir = $sourceCheckoutDir . '/xml/templates';
    $smarty->compile_dir  = '/tmp/templates_c';
    createDir( $smarty->compile_dir );

    $smarty->assign( 'files', array_keys( $files ) );
    $xml = $smarty->fetch( 'mambo.tpl' );

    $output = $sourceCheckoutDir . '/mambo/civicrm.xml';
    $fd = fopen( $output, "w" );
    fputs( $fd, $xml );
    fclose( $fd );
}

function walkDirectory( $iter, &$files, $length ) {
    while ($iter->valid()) {
        $node = $iter->current();
        
        $path = $node->getPathname( );
        $name = $node->getFilename( );
        if ( $node->isDir( )      && 
             $node->isReadable( ) &&
             ! $node->isDot( )    &&
             $name != '.svn' ) {
            $files[ substr( $path, $length + 1 ) . '/' ] = 1;
            walkDirectory(new DirectoryIterator( $path ), $files, $length);
        } else if ( $node->isFile( ) ) {
            if ( substr( $name, -1, 1 ) != '~' && substr( $name, 0, 1 ) != '#' ) {
                $files[ substr( $path, $length + 1 ) ] = 1;
            }
        }
        
        $iter->next( );
    }
}
?>