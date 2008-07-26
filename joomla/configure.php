<?php

// escape early if called directly
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) {
	die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
}

require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'configuration.php';

global $civicrmUpgrade;
// ** SET civicrmUpgrade to true if you are doing an UPGRADE **
$civicrmUpgrade = false;

function civicrm_setup( ) {
    global $comPath, $crmPath, $sqlPath, $tplPath, $frontPath, $dsn, $compileDir;

    $jConfig = new JConfig( );

    $comPath = dirname(__FILE__);
    $crmPath = $comPath . DIRECTORY_SEPARATOR . 'civicrm';

    $pkgPath = $crmPath . DIRECTORY_SEPARATOR . 'packages';
    set_include_path( $comPath . PATH_SEPARATOR .
                      $crmPath . PATH_SEPARATOR .
                      $pkgPath . PATH_SEPARATOR .
                      get_include_path( ) );

    $sqlPath  = $crmPath . DIRECTORY_SEPARATOR . 'sql';
    $tplPath  = $crmPath . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'CRM' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR;

    $liveSite = substr_replace(JURI::root(), '', -1, 1);
    $pieces   = parse_url( $liveSite );

    $frontPath = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_civicrm';

    $scratchDir   = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'civicrm';
    if ( ! is_dir( $scratchDir ) ) {
        mkdir( $scratchDir, 0777 );
    }
    
    $compileDir   = $scratchDir . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR;
    if ( ! is_dir( $compileDir ) ) {
        mkdir( $compileDir, 0777 );
    }
    $compileDir = addslashes( $compileDir );

    $dsn = 'mysql://'      .  
        $jConfig->user     . ':' .  
        $jConfig->password . '@' . 
        $jConfig->host     . '/' .              
        $jConfig->db       .   
        '?new_link=true';
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
    global $sqlPath, $comPath, $crmPath, $frontPath;

    civicrm_setup( );

    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm.mysql'     );
    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_data.mysql');
    
    // generate backend settings file
    $configFile = $comPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php';
    $string = civicrm_config( false );
    civicrm_write_file( $configFile,
                        $string );

    // generate backend config file
    $string = "
<?php

require_once '$configFile';


";
    $string = trim( $string );
    civicrm_write_file( $crmPath . DIRECTORY_SEPARATOR . 'civicrm.config.php',
                        $string );

    // generate frontend settings file
    $string = civicrm_config( true ); 
    civicrm_write_file( $frontPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php',
                        $string );

    include_once $comPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php';

    // now also build the menu
    require_once 'CRM/Core/Config.php';
    $config =& CRM_Core_Config::singleton();

    // now also build the menu
    require_once 'CRM/Core/Menu.php';
    CRM_Core_Menu::store( );
}

function civicrm_source( $fileName ) {
    global $dsn, $civicrmUpgrade;

    if ( $civicrmUpgrade ) {
        return;
    }

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

function civicrm_config( $frontend = false ) {
    global $crmPath, $compileDir, $frontend, $tplPath;

    $jConfig = new JConfig( );

    $liveSite = substr_replace(JURI::root(), '', -1, 1);
    $params = array(
                    'cms'        => 'Joomla',
                    'crmRoot'    => $crmPath,
                    'templateCompileDir' => $compileDir,
                    'baseURL'    => $liveSite . '/administrator/',
                    'dbUser'     => $jConfig->user,
                    'dbPass'     => $jConfig->password,
                    'dbHost'     => $jConfig->host,
                    'dbName'     => $jConfig->db,
                    'CMSdbUser'  => $jConfig->user,
                    'CMSdbPass'  => $jConfig->password,
                    'CMSdbHost'  => $jConfig->host,
                    'CMSdbName'  => $jConfig->db,
                    );

    if ( $frontend ) {
        $params['baseURL']  = $liveSite . '/';
    }

    
    $str = file_get_contents( $tplPath . 'civicrm.settings.php.sample.tpl' );
    foreach ( $params as $key => $value ) { 
        $str = str_replace( '%%' . $key . '%%', $value, $str ); 
    } 
    return trim( $str );
}

civicrm_main( );
