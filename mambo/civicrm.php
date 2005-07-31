<?php

require_once '../configuration.php';

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

    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'Contacts.mysql40.sql'  );
    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'FixedData.sql' );

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

    ini_set( 'include_path',
             '.:' . $crmPath . ':' .
             $crmPath . DIRECTORY_SEPARATOR . 'packages' );
    require_once 'packages/DB.php';

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

    $str = "
<?php

global \$user_home;

\$user_home = '$crmPath';

define( 'CRM_HTTPBASE'    , '$httpBase'     );
define( 'CRM_RESOURCEBASE', '$resourceBase' );
define( 'CRM_MAINMENU'    , '$mainMenu' );

// the new_link option is super important if you are reusing the same user id across both drupal and civicrm
define( 'CRM_DSN'         , '$dsn' );

define( 'CRM_MYSQL_VERSION', 4.0 );

define( 'CRM_TEMPLATE_COMPILEDIR', '$compileDir' );
define( 'CRM_UPLOAD_DIR'         , '$uploadDir'  );

define( 'CRM_USERFRAMEWORK'       , 'Mambo' );
define( 'CRM_USERFRAMEWORK_URLVAR', 'task'  );

define( 'CRM_LC_MESSAGES' , 'en_US' );

// this is used for formatting the various date displays. Change this to match
// your locale if different.
define( 'CRM_DATEFORMAT_DATETIME', '%B %E%f, %Y %l:%M %P' );
define( 'CRM_DATEFORMAT_FULL', '%B %E%f, %Y' );
define( 'CRM_DATEFORMAT_PARTIAL', '%B %Y' );
define( 'CRM_DATEFORMAT_YEAR', '%Y' );
define( 'CRM_DATEFORMAT_QF_DATE', '%b %d %Y' );
define( 'CRM_DATEFORMAT_QF_DATETIME', '%b %d %Y, %I : %M %P' );

// Default maximum Contact Import filesize is 1MB. You may increase this up to a hard-limit of 8MB.
// However, imports above 1MB will take a 'long time' and are server resource intensive.
define( 'CRM_MAX_IMPORT_FILESIZE' , 1048576);

// If you are sending emails from CiviCRM, enter your smtp server address here (e.g.'smtp.example.com').
define( 'CRM_SMTP_SERVER' , 'YOUR SMTP SERVER' );

include_once 'config.main.php';

?>
";

    return $str;
}

?>
