<?php

// escape early if called directly
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) {
	die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
}

require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'configuration.php';

// ** SET civicrmUpgrade to true if you are doing an UPGRADE **
define( 'CIVICRM_UPGRADE', false );


function civicrm_setup( ) {
    $jConfig = new JConfig( );

    define( 'COM_PATH', dirname(__FILE__) );

    define( 'CRM_PATH', COM_PATH . DIRECTORY_SEPARATOR . 'civicrm' );

    $pkgPath = CRM_PATH . DIRECTORY_SEPARATOR . 'packages';
    set_include_path( COM_PATH . PATH_SEPARATOR .
                      CRM_PATH . PATH_SEPARATOR .
                      $pkgPath . PATH_SEPARATOR .
                      get_include_path( ) );

    define( 'SQL_PATH', CRM_PATH . DIRECTORY_SEPARATOR . 'sql' );

    define( 'TPL_PATH', CRM_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'CRM' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR );

    $pieces = parse_url( $jConfig->live_site );

    define( 'FRONT_PATH', JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_civicrm' );

    $scratchDir   = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'civicrm';
    if ( ! is_dir( $scratchDir ) ) {
        mkdir( $scratchDir, 0777 );
    }
    
    $compileDir   = $scratchDir . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR;
    if ( ! is_dir( $compileDir ) ) {
        mkdir( $compileDir, 0777 );
    }
    $compileDir = addslashes( $compileDir );

    define( 'COMPILE_DIR', $compileDir );

    define( 'DSN', 'mysql://'  .  
            $jConfig->user     . ':' .  
            $jConfig->password . '@' . 
            $jConfig->host     . '/' .              
            $jConfig->db       .   
            '?new_link=true' );
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
    civicrm_setup( );

    civicrm_source( SQL_PATH . DIRECTORY_SEPARATOR . 'civicrm.mysql'     );
    civicrm_source( SQL_PATH . DIRECTORY_SEPARATOR . 'civicrm_data.mysql');
    
    // generate backend settings file
    $configFile = COM_PATH . DIRECTORY_SEPARATOR . 'civicrm.settings.php';
    $string = civicrm_config( false );
    civicrm_write_file( $configFile,
                        $string );

    // generate backend config file
    $string = "
<?php

require_once '$configFile';


";
    $string = trim( $string );
    civicrm_write_file( CRM_PATH . DIRECTORY_SEPARATOR . 'civicrm.config.php',
                        $string );

    // generate frontend settings file
    $string = civicrm_config( true ); 
    civicrm_write_file( FRONT_PATH . DIRECTORY_SEPARATOR . 'civicrm.settings.php',
                        $string );

}

function civicrm_source( $fileName ) {
    $dsn = DSN;

    if ( CIVICRM_UPGRADE ) {
        return;
    }

    require_once 'DB.php';

    $db  =& DB::connect( $dsn );
    if ( PEAR::isError( $db ) ) {
        die( "Cannot open $dsn: " . $db->getMessage( ) );
    }

    $string = file_get_contents( $fileName );

    //get rid of comments starting with # and --
    $string = ereg_replace("^#[^\n]*\n", "\n", $string );
    $string = ereg_replace("^\-\-[^\n]*\n", "\n", $string );
    
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
    $jConfig = new JConfig( );

    $params = array(
                    'cms'        => 'Joomla',
                    'cmsVersion' => '1.0',
                    'usersTable' => $jConfig->dbprefix . 'users',
                    'crmRoot'    => CRM_PATH,
                    'templateCompileDir' => COMPILE_DIR,
                    'baseURL'    => $jConfig->live_site . '/administrator/',
                    'frontEnd'   => 0,
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
        $params['baseURL']  = $jConfig->live_site . '/';
        $params['frontEnd'] = 1;
    }

    
    $str = file_get_contents( TPL_PATH . 'civicrm.settings.php.sample.tpl' );
    foreach ( $params as $key => $value ) { 
        $str = str_replace( '%%' . $key . '%%', $value, $str ); 
    } 
    return trim( $str );
}

civicrm_main( );


