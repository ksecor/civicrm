<?php

global $mosConfig_absolute_path;
require_once $mosConfig_absolute_path . DIRECTORY_SEPARATOR . 'configuration.php';

function civicrm_setup( ) {
    global $comPath, $crmPath, $sqlPath, $dsn;
    global $httpBase, $resourceBase, $mainMenu;
    global $compileDir, $uploadDir;

    global $mosConfig_live_site, $mosConfig_absolute_path;
    global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db;

    $comPath = $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
        'administrator'          . DIRECTORY_SEPARATOR .
        'components'             . DIRECTORY_SEPARATOR .
        'com_civicrm'            ;
    $crmPath = $comPath . DIRECTORY_SEPARATOR . 'civicrm';
    $pkgPath = $crmPath . DIRECTORY_SEPARATOR . 'packages';
    set_include_path( $comPath . PATH_SEPARATOR .
                      $crmPath . PATH_SEPARATOR .
                      $pkgPath . PATH_SEPARATOR .
                      get_include_path( ) );

    
    $sqlPath = $crmPath . DIRECTORY_SEPARATOR . 'sql';

    $pieces = parse_url( $mosConfig_live_site );
    $httpBase     = $pieces['path'] . '/administrator/';
    $resourceBase = $httpBase . 'components/com_civicrm/civicrm/';
    $mainMenu     = $httpBase . 'index2.php?option=com_civicrm';

    $scratchDir   = $mosConfig_absolute_path . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'civicrm';
    if ( ! is_dir( $scratchDir ) ) {
        mkdir( $scratchDir, 0777 );
    }
    
    $compileDir        = $scratchDir . DIRECTORY_SEPARATOR . 'templates_c';
    if ( ! is_dir( $compileDir ) ) {
        mkdir( $compileDir, 0777 );
    }
    
    $uploadDir         = $scratchDir . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;
    if ( ! is_dir( $uploadDir ) ) {
        mkdir( $uploadDir, 0777 );
    }

    $dsn =  'mysql://' . 
        $mosConfig_user     . ':' . 
        $mosConfig_password . '@' .
        $mosConfig_host     . '/' .
        $mosConfig_db       .
        '?new_link=true';

}

function civicrm_main( ) {
    global $sqlPath, $comPath;

    civicrm_setup( );

    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_40.mysql'     );
    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_data.mysql');
    
    $configFile = $comPath . DIRECTORY_SEPARATOR . 'config.inc.php';
    $string = civicrm_config( );
    $fd = fopen( $configFile, "w" );
    if ( ! $fd ) {
        die( "Cannot open $configFile" );
    }

    fputs( $fd, $string );
    fclose ( $fd );
}

function civicrm_source( $fileName ) {
    global $dsn, $crmPath;

    require_once 'DB.php';

    $db  =& DB::connect( $dsn );
    if ( PEAR::isError( $db ) ) {
        die( "Cannot open $fileName: " . $db->getMessage( ) );
    }
    
    $string = file_get_contents( $fileName );

    //get rid of comments starting with # and --
    $string = ereg_replace("\n#[^\n]*\n", "\n", $string );
    $string = ereg_replace("\n\-\-[^\n]*\n", "\n", $string );
    
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

function civicrm_config( ) {
    global $crmPath, $httpBase, $resourceBase, $mainMenu, $dsn, $compileDir, $uploadDir;
    global $mosConfig_smtphost, $mosConfig_live_site;

    /**
     * make sure we escape the back slashes in the dir names to prevent any
     * issues with windows wehre the dir seperator is a backslash
     */
    $compileDir = addslashes( $compileDir );
    $uploadDir  = addslashes( $uploadDir  );

    $str = "
<?php
/**
 * CiviCRM configuration file.
 */

global \$civicrm_root;

define( 'CIVICRM_UF'               , 'Mambo' ); 
define( 'CIVICRM_UF_URLVAR'        , 'task'  ); 
define( 'CIVICRM_UF_DSN'           , '$dsn' );
define( 'CIVICRM_UF_USERSTABLENAME', 'mos_users' ); 
define( 'CIVICRM_UF_BASEURL'       , '$mosConfig_live_site' );

\$civicrm_root = '$crmPath';
define( 'CIVICRM_TEMPLATE_COMPILEDIR', '$compileDir' );
define( 'CIVICRM_UPLOADDIR'          , '$uploadDir'  );

define( 'CIVICRM_HTTPBASE'    , '$httpBase'     );
define( 'CIVICRM_RESOURCEBASE', '$resourceBase' );
define( 'CIVICRM_MAINMENU'    , '$mainMenu'     );

define( 'CIVICRM_MYSQL_VERSION', 4.0 );
define( 'CIVICRM_DSN'         , '$dsn' );

define( 'CIVICRM_SMTP_SERVER' , '$mosConfig_smtphost' );

define( 'CIVICRM_PROVINCE_LIMIT' , 'US' ); 

define( 'CIVICRM_LC_MESSAGES' , 'en_US' );

define( 'CIVICRM_DATEFORMAT_DATETIME', '%B %E%f, %Y %l:%M %P' ); 
define( 'CIVICRM_DATEFORMAT_FULL', '%B %E%f, %Y' ); 
define( 'CIVICRM_DATEFORMAT_PARTIAL', '%B %Y' ); 
define( 'CIVICRM_DATEFORMAT_YEAR', '%Y' ); 
define( 'CIVICRM_DATEFORMAT_QF_DATE', '%b %d %Y' ); 
define( 'CIVICRM_DATEFORMAT_QF_DATETIME', '%b %d %Y, %I : %M %P' ); 

define('CIVICRM_GOOGLE_MAP_API_KEY', 'ABQIAAAAJqPUffDG76eXgUTeRaGh9hQjQG_QTQsz9JF3GmcmKBQr3bx34RR4Dj1RteDd-FoQ3iCpVIznWMLUtQ'); 

define('CIVICRM_GEOCODE_METHOD', 'CRM_Utils_Geocode_RPC' ); 

define('CIVICRM_DOMAIN_ID' , 1 ); 

include_once 'config.main.php';

?>
";

    return $str;
}

civicrm_main( );

?>
