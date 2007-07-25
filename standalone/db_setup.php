<?php

function generatePassword ( $length = 10 ) {
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
  
  $i = 0;  
  // add random characters to $password until $length is reached
  while ( $i < $length ) {

    // pick a random character from the possible ones
    $char = substr( $possible, mt_rand( 0, strlen( $possible )-1 ), 1 );
        
    // we don't want this character if it's already in the password
    if ( ! strstr( $password, $char ) ) {
      $password .= $char;
      $i++;
    }

  }

  return $password;
}

function runDbCmdAsAdmin ( $cmd, $mysqlCmd = 'mysql' ) {
    global $dbAdminUser;
    global $dbAdminPass;
    global $mysqlPath;
    
    $throwAway = array( );
    
    $exec = "$mysqlPath/$mysqlCmd $cmd";
    
    print "Executing $exec<br/>";

    system( $exec, $throwAway );
    #exec( $exec, $throwAway, $returnVal );
    
    return $returnVal;
}

$baseURL            = $_POST['base_url'     ];
$dbHost             = $_POST['db_host'      ];
$dbName             = $_POST['db_name'      ];
$dbUser             = $_POST['db_user'      ];
$civicrm_root       = $_POST['crm_root'     ];
$baseURL            = $_POST['base_url'     ];
$dbSocketFile       = $_POST['socket_file'  ];
$dbPortNum          = $_POST['port_number'  ];
$mysqlPath          = $_POST['mysql_path'   ];
$dbAdminUser        = $_POST['db_admin_user'];
$dbAdminPass        = $_POST['db_admin_pass'];
$dbFilesToLoad      = array( );
$dbFilesToLoad[]    = 'sql/civicrm_41.mysql';
$dbFilesToLoad[]    = 'sql/civicrm_generated.mysql';

print "DB Name: $dbName<br/>";

if ( $dbHost == 'localhost' ) {
    $dbHost = "unix($dbSocketFile)";
} else {
    $dbHost = "$dbHost:$dbPortNum";
}

$dbPass  = generatePassword( );

$params = array(
                'cms' => 'Standalone',
                'cmsVersion' => '',
                'cmsURLVar'  => 'q',
                'usersTable' => '',
                'crmRoot' => "$civicrm_root",
                'templateCompileDir' => "$civicrm_root/templates_c",
                'uploadDir' => "$civicrm_root/upload",
                'imageUploadDir' => '',
                'imageUploadURL' => '',
                'customFileUploadDir' => "$civicrm_root/custom",
                'baseURL' => "$baseURL",
                'resourceURL' => "$baseURL",
                'frontEnd' => 0,
                'dbUser' => 'civicrm',
                'dbPass' => "$dbPass",
                'dbHost' => "$dbHost",
                'dbName' => "$dbName",
                'mysqlPath' => "$mysqlPath",
                );

$data = file_get_contents( "$civicrm_root/templates/CRM/common/civicrm.settings.php.sample.tpl" );
foreach ( $params as $key => $value ) {
    $data = str_replace( '%%' . $key . '%%', $value, $data );
}
$filename = 'civicrm.settings.php';
$fd = fopen( "$civicrm_root/" . $filename, "w" );
fputs( $fd, $data );
fclose( $fd );

runDbCmdAsAdmin("$dbName > $dbName.sql.backup", 'mysqldump');
runDbCmdAsAdmin("< 'drop database if exists $dbName'");
runDbCmdAsAdmin("< 'create database $dbName'");
runDbCmdAsAdmin("< 'grant all on $dbName.* to $dbUser identified by \"$dbPass\"'");
#foreach ( $dbFilesToLoad as $file ) {
#    runDbCmdAsAdmin("-D $dbName < $file");
#}

?>