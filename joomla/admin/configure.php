<?php

// escape early if called directly
defined('_JEXEC') or die('No direct access allowed'); 

global $civicrmUpgrade;
$civicrmUpgrade = false;

function civicrm_setup( ) {
    include_once
        JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR .
        'components'        . DIRECTORY_SEPARATOR .
        'com_civicrm'       . DIRECTORY_SEPARATOR .
        'civicrm.settings.php';
    $jConfig =& JFactory::getConfig( );
    set_time_limit(4000);

    // Path to the archive
    $archivename = 
        JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR .
        'components'        . DIRECTORY_SEPARATOR .
        'com_civicrm'       . DIRECTORY_SEPARATOR .
        'civicrm.zip';

    $extractdir = 
        JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR .
        'components'        . DIRECTORY_SEPARATOR .
        'com_civicrm';

    JArchive::extract( $archivename, $extractdir);

    $scratchDir   = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'civicrm';
    if ( ! is_dir( $scratchDir ) ) {
        JFolder::create( $scratchDir, 0777 );
    }
    
    $compileDir   = $scratchDir . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR;
    if ( ! is_dir( $compileDir ) ) {
        JFolder::create( $compileDir, 0777 );
    }

    $db =& JFactory::getDBO();
    $db->setQuery(' SELECT count( * )
FROM information_schema.tables
WHERE table_name LIKE "civicrm_domain"
AND table_schema = "' . $jConfig->getValue('config.db') .'" ');

    global $civicrmUpgrade;
    $civicrmUpgrade = ( $db->loadResult() == 0 ) ? false : true;
}

function civicrm_write_file( $name, &$buffer ) {
    $fd  = fopen( $name, "w" );
    if ( ! $fd ) {
        die( "Cannot open $name" );
    }
    fputs( $fd, $buffer );
    fclose( $fd );
}

function civicrm_main( ) {
    global $civicrmUpgrade;

    civicrm_setup( );

    // generate backend settings file
    $adminPath =
        JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR .
        'components'        . DIRECTORY_SEPARATOR .
        'com_civicrm';

    $configFile = $adminPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php';

    // generate backend config file
    $string = "
<?php
require_once '$configFile';
";
    $string = trim( $string );
    civicrm_write_file( $adminPath . DIRECTORY_SEPARATOR . 
                        'civicrm'  . DIRECTORY_SEPARATOR . 
                        'civicrm.config.php',
                        $string );

    if ( ! $civicrmUpgrade ) {
        $sqlPath = 
            $adminPath . DIRECTORY_SEPARATOR . 
            'civicrm'  . DIRECTORY_SEPARATOR .
            'sql';

        civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm.mysql'     );
        civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_data.mysql');
    }
    
    // now also build the menu
    require_once 'CRM/Core/Config.php';
    $config =& CRM_Core_Config::singleton();

    // now also build the menu
    require_once 'CRM/Core/Menu.php';
    CRM_Core_Menu::store( );
}

function civicrm_source( $fileName ) {

    $dsn = CIVICRM_DSN;

    require_once 'DB.php';

    $db  =& DB::connect( $dsn );
    if ( PEAR::isError( $db ) ) {
        die( "Cannot open $dsn: " . $db->getMessage( ) );
    }

    $string = file_get_contents( $fileName );

    //get rid of comments starting with # and --
    $string = preg_replace("/^#[^\n]*$/m", "\n", $string );
    $string = preg_replace("/^\-\-[^\n]*$/m", "\n", $string );
    
    $queries  = explode( ';', $string );
    foreach ( $queries as $query ) {
        $query = trim( $query );
        if ( ! empty( $query ) ) {
            $res =& $db->query( $query );
            if ( PEAR::isError( $res ) ) {
                die( "Cannot execute $query: " . $res->getMessage( ) );
            }
        }
    }
}

civicrm_main( );
