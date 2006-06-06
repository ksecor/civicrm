<?php
function civicrm_setup( ) {
    global $comPath, $frontPath, $crmPath, $sqlPath, $tplPath, $dsn, $absolute_path;
    global $resourceBase;
    global $compileDir, $uploadDir, $imageUploadDir;
    global $resourceBaseURL;
    global $imageUploadDir, $imageUploadURL,$live_site;
    global $base_url,$db_url;
    $absolute_path = '.';

    $comPath = $absolute_path. DIRECTORY_SEPARATOR.'modules';
    $crmPath = $comPath . DIRECTORY_SEPARATOR . 'civicrm';
    
    $pkgPath = $crmPath . DIRECTORY_SEPARATOR . 'packages';
    set_include_path( $comPath . PATH_SEPARATOR .
                      $crmPath . PATH_SEPARATOR .
                      $pkgPath . PATH_SEPARATOR .
                      get_include_path( ) );

    $sqlPath = $crmPath . DIRECTORY_SEPARATOR . 'sql';
    $tplPath = $crmPath . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'CRM' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR;
    
    //$live_site = variable_get($base_url, null );
    
    $live_site = $base_url ;
    
    $pieces = parse_url( $live_site );

    $httpBase     = $pieces['path'];
    $resourceBase = $httpBase . 'modules/civicrm/';

    $frontPath = $absolute_path . DIRECTORY_SEPARATOR .
        'sites'             . DIRECTORY_SEPARATOR . 
        'default' ;

    $scratchDir   = $absolute_path . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'civicrm';
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
    $dsn = $db_url;
    $dsn = $dsn.'?new_link=true';

}

function civicrm_write_file( $name, &$buffer ) {
    $fd  = fopen( $name, "w" );
    if ( ! $fd ) {
        die( "Cannot open $name" );
    }
    fputs( $fd, $buffer );
    fclose( $fd );

}


function civicrm_install( ) {
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
    global $dsn, $compileDir, $uploadDir, $imageUploadDir;
    global $mysqlPath;
    global $live_site;
    global $tplPath,$db_url;

    $db_dsn = $db_url;
    $pieces = parse_url( $db_dsn );


    $params = array(
                'cms' => 'Drupal',
                'cmsVersion' => '4.7',
                'cmsURLVar'  => 'task',
                'usersTable' => 'jos_users',
                'crmRoot' => $crmPath,
                'templateCompileDir' => $compileDir,
                'uploadDir' => $uploadDir,
                'imageUploadDir' => $imageUploadDir,
                'imageUploadURL' => $live_site . 'files/civicrm/persist',
                'baseURL' => $live_site ,
                'resourceURL' => $live_site . '/modules/civicrm/',
                'resourceBase' => $resourceBase,
                'frontEnd' => 0,
                'dbUser' => $pieces['user'],
                'dbPass' => $pieces['pass'],
                'dbHost' => $pieces['host'],
                'dbName' => substr($pieces['path'],1),
                );

    if ( $frontend ) {
        $params['baseURL']  = $live_site;
        $params['frontEnd'] = 1;
    }

    
    $str = file_get_contents( $tplPath . 'civicrm.settings.php.sample.tpl' );
    foreach ( $params as $key => $value ) { 
        $str = str_replace( '%%' . $key . '%%', $value, $str ); 
    } 
    return trim( $str );
}

civicrm_install( );

?>
