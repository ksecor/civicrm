<?php

global $mosConfig_absolute_path;
require_once $mosConfig_absolute_path . DIRECTORY_SEPARATOR . 'configuration.php';

function civicrm_setup( ) {
    global $comPath, $frontPath, $crmPath, $sqlPath, $tplPath, $dsn;
    global $httpBase, $resourceBase, $mainMenu;
    global $httpBaseFE, $mainMenuFE;
    global $compileDir, $uploadDir;
    global $resourceBaseURL;
    global $imageUploadDir, $imageUploadURL;

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
    $tplPath = $crmPath . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

    $pieces = parse_url( $mosConfig_live_site );
    $httpBase     = $pieces['path'] . '/administrator/';
    $resourceBase = $httpBase . 'components/com_civicrm/civicrm/';
    $mainMenu     = $httpBase . 'index.php?option=com_civicrm';

    $frontPath = $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
        'components'             . DIRECTORY_SEPARATOR . 
        'com_civicrm'            ;
    $httpBaseFE = $pieces['path'];
    $mainMenuFE = $httpBaseFE . 'index.php?option=com_civicrm';

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

function civicrm_config( $frontend = false ) {
    global $crmPath, $comPath, $httpBase, $resourceBase;
    global $mainMenu, $dsn, $compileDir, $uploadDir, $mysqlPath;
    global $mosConfig_smtphost, $mosConfig_live_site;
    global $tplPath;

    /**
     * make sure we escape the back slashes in the dir names to prevent any
     * issues with windows wehre the dir seperator is a backslash
     */
    $compileDir = addslashes( $compileDir );
    $uploadDir  = addslashes( $uploadDir  );


    $params = array(
                'cms' => 'Mambo',
                'cmsVersion' => '1.0.8',
                'cmsURLVar'  => 'task',
                'usersTable' => 'jos_users',
                'crmRoot' => $crmPath,
                'templateCompileDir' => $compileDir,
                'uploadDir' => $uploadDir,
                'imageUploadDir' => $imageUploadDir,
                'imageUploadURL' => $imageUploadURL,
                'baseURL' => $mosConfig_live_site . '/administrator/',
                'resourceURL' => $resourceURL,
                'httpBase' => $httpBase,
                'resourceBase' => $resourceBase,
                'mainMenu' => $mainMenu,
                'frontEnd' => 0,
                );

    if ( $frontend ) {
        $params['baseURL']  = $mosConfig_live_site . '/';
        $params['httpBase'] = $httpBaseFE;
        $params['mainMenu'] = $mainMenuFE;
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
