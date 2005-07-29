<?php

global $mosConfig_host;
global $mosConfig_user;
global $mosConfig_password;
global $mosConfig_db;
global $mosConfig_live_site;
global $mosConfig_absolute_path;

$mosConfig_host      = 'localhost';
$mosConfig_user      = 'civicrm';
$mosConfig_password  = 'Mt!Everest';
$mosConfig_db        = 'civicrm';
$mosConfig_live_site = 'http://localhost/lobo/mambo';

$mosConfig_absolute_path = '/Users/lobo/htdocs/mambo';

global $comPath, $crmPath;
$comPath = $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
           'administrator'          . DIRECTORY_SEPARATOR .
           'components'             . DIRECTORY_SEPARATOR .
           'com_civicrm'            ;
$crmPath = $comPath . DIRECTORY_SEPARATOR . 'civicrm';

global $httpBase, $resourceBase, $mainMenu;
$pieces = parse_url( $mosConfig_live_site );
$httpBase     = $pieces['path'] . '/administrator/';
$resourceBase = $httpBase . 'administrator/components/com_civicrm/civicrm/';
$mainMenu     = $httpBase . 'administrator/index2.php?option=com_civicrm';

ini_set( 'include_path',
         '.:' . $crmPath . ':' .
         $crmPath . DIRECTORY_SEPARATOR . 'packages' );

require_once 'packages/DB.php';

global $dsn;
$dsn =  'mysql://' . $mosConfig_user . ':' . $mosConfig_password . '@' . $mosConfig_host . '/' . $mosConfig_db . '?new_link=true';
$db  =& DB::connect( $dsn );
if ( PEAR::isError( $db ) ) {
    die( $db->getMessage( ) );
}

$sqlPath = $crmPath . DIRECTORY_SEPARATOR . 'sql';

sourceFile( $db, $sqlPath . DIRECTORY_SEPARATOR . 'Contacts.sql'  );
sourceFile( $db, $sqlPath . DIRECTORY_SEPARATOR . 'FixedData.sql' );

$configFile = $sqlPath . DIRECTORY_SEPARATOR . 'config.inc.php';
$string = generateConfigString( );
$fd = fopen( $configFile, "w" );
fputs( $fd, $string );
fclose ( $fd );

function sourceFile( &$db, $fileName ) {
    return;

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
                die( $res->getMessage( ) );
            }
        }
    }
}

function generateConfigString( ) {
    global $crmPath, $httpBase, $resourceBase, $mainMenu, $dsn;

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
