<?php

global $mosConfig_absolute_path;

// $mosConfig_absolute_path = "/tmp/mos";

require_once $mosConfig_absolute_path . DIRECTORY_SEPARATOR . 'configuration.php';

function civicrm_setup( ) {
    global $comPath, $frontPath, $crmPath, $sqlPath, $tplPath, $dsn;
    global $compileDir, $uploadDir, $imageUploadDir;
    global $imageUploadDir, $imageUploadURL;

    global $mosConfig_live_site, $mosConfig_absolute_path;
    global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db;

    /**
     $mosConfig_live_site = "MOS_LIVE_SITE";
     $mosConfig_host = "HOST";
     $mosConfig_password = "PASS";
     $mosConfig_db = "DB";
    */

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
    $tplPath = $crmPath . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'CRM' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR;

    $pieces = parse_url( $mosConfig_live_site );

    $frontPath = $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
        'components'             . DIRECTORY_SEPARATOR . 
        'com_civicrm'            ;

    $scratchDir   = $mosConfig_absolute_path . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'civicrm';
    if ( ! is_dir( $scratchDir ) ) {
        mkdir( $scratchDir, 0777 );
    }
    
    $compileDir        = $scratchDir . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR;
    if ( ! is_dir( $compileDir ) ) {
        mkdir( $compileDir, 0777 );
    }
    $compileDir = addslashes( $compileDir );

    $uploadDir         = $scratchDir . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;
    if ( ! is_dir( $uploadDir ) ) {
        mkdir( $uploadDir, 0777 );
    }
    $uploadDir = addslashes( $uploadDir );

    $imageUploadDir = $scratchDir . DIRECTORY_SEPARATOR . 'persist' . DIRECTORY_SEPARATOR;
    if ( ! is_dir( $imageUploadDir ) ) {
        mkdir( $imageUploadDir, 0777 );
    }
    $imageUploadDir = addslashes( $imageUploadDir );

    $dsn =  'mysql://' .  
        $mosConfig_user     . ':' .  
        $mosConfig_password . '@' . 
        $mosConfig_host     . '/' .              
        $mosConfig_db       .   
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

    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_40.mysql'     );
    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_data.mysql');
    
    // generate backend settings file
    $configFile = $comPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php';
    $string = civicrm_config( false );
    civicrm_write_file( $configFile,
                        $string );

    // generate backend config file
    $string = "
<?php
include_once '$configFile';
?>
";
    $string = trim( $string );
    civicrm_write_file( $crmPath . DIRECTORY_SEPARATOR . 'civicrm.config.php',
                        $string );

    // generate frontend settings file
    $string = civicrm_config( true ); 
    civicrm_write_file( $frontPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php',
                        $string );

}

function civicrm_source( $fileName ) {
    global $crmPath, $dsn;

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

function civicrm_config( $frontend = false ) {
    global $crmPath, $comPath;
    global $dsn, $compileDir, $uploadDir, $imageUploadDir;
    global $mysqlPath;
    global $mosConfig_smtphost, $mosConfig_live_site;
    global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db;
    global $tplPath;

    $params = array(
                'cms' => 'Joomla',
                'cmsVersion' => '1.0.11',
                'cmsURLVar'  => 'task',
                'usersTable' => 'jos_users',
                'crmRoot' => $crmPath,
                'templateCompileDir' => $compileDir,
                'uploadDir' => $uploadDir,
                'imageUploadDir' => $imageUploadDir,
                'imageUploadURL' => $mosConfig_live_site . '/media/civicrm/',
                'customFileUploadDir' => $imageUploadDir,
                'customFileUploadURL' => $mosConfig_live_site . '/media/civicrm/',
                'baseURL' => $mosConfig_live_site . '/administrator/',
                'resourceURL' => $mosConfig_live_site . '/administrator/components/com_civicrm/civicrm/',
                'frontEnd' => 0,
                'dbUser' => $mosConfig_user,
                'dbPass' => $mosConfig_password,
                'dbHost' => $mosConfig_host,
                'dbName' => $mosConfig_db,
                );

    if ( $frontend ) {
        $params['baseURL']  = $mosConfig_live_site . '/';
        $params['frontEnd'] = 1;
    }

    
    $str = file_get_contents( $tplPath . 'civicrm.settings.php.sample.tpl' );
    foreach ( $params as $key => $value ) { 
        $str = str_replace( '%%' . $key . '%%', $value, $str ); 
    } 
    return trim( $str );
}

civicrm_main( );

?>
