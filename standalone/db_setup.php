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

function dbConnect ( $dbHost, $dbUser, $dbPass, $dbName = null ) {
    $dbLink = mysql_connect( $dbHost, $dbUser, $dbPass );
    if ( ! $dbLink ) {
        die("Error: Could not connect to MySQL server: " . mysql_error( ) );
    }
    
    if ( isset( $dbName ) ) {
        mysql_select_db( $dbName );
    }
    
    return $dbLink;
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

if ( $dbHost == 'localhost' ) {
    $dbConfigHost = "unix($dbSocketFile)";
    $dbConnectHost = "localhost:$dbSocketFile";
} else {
    $dbConnectHost = $dbConfigHost = "$dbHost:$dbPortNum";
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
                'dbHost' => "$dbConfigHost",
                'dbName' => "$dbName",
                'mysqlPath' => "$mysqlPath",
                );

$data = file_get_contents( "$civicrm_root/templates/CRM/common/civicrm.settings.php.sample.tpl" );
foreach ( $params as $key => $value ) {
    $data = str_replace( '%%' . $key . '%%', $value, $data );
}
$filename = 'civicrm.settings.php';
$fd = fopen( "$civicrm_root/" . $filename, "w" );
if ( ! $fd ) {
  die("Couldn't open ".$civicrm_root/$filename." for writing, check directory permissions.");
}
fputs( $fd, $data );
fclose( $fd );

$dbLink = dbConnect( $dbConnectHost, $dbAdminUser, $dbAdminPass );

$query = "SHOW DATABASES LIKE '$dbName'";
$result = mysql_query( $query, $dbLink );
if ( mysql_num_rows( $result ) > 0 ) {
    die( "Error: $dbName schema in MySQL server $dbConnectHost already exists! Please back it up and drop it before running setup." );
}
$query = "CREATE DATABASE $dbName";
mysql_query( $query, $dbLink );

if ( strpos( $dbConnectHost, 'localhost' ) == 0 ) {
    $grantHost = 'localhost';
} else {
    $grantHost = '%';
}

$query = "GRANT ALL ON $dbName.* TO $dbUser@`$grantHost` IDENTIFIED BY '$dbPass'";
mysql_query( $query, $dbLink );

foreach ( $dbFilesToLoad as $file ) {
    $cmd = "$mysqlPath/mysql -h $dbHost -u $dbAdminUser ";
    if ( $dbAdminPass != '' ) {
        $cmd .= "-p$dbAdminPass ";
    }
    if ( $dbSocketFile != '' ) {
        $cmd .= "-S $dbSocketFile ";
    }
    $cmd .= "-D $dbName < $civicrm_root/$file";
    //print "<p>$cmd</p>";
    exec( $cmd );
}

header( "Location: new_install.php" );

?>
