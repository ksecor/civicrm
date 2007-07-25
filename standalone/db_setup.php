<?php

require_once 'bootstrap_common.php';

function generatePassword ( $length = 10 ) {
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyz@_%*+"; 
  
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

$crmRoot = $_POST['crm_root'];
$baseURL = $_POST['base_url'];
$dbHost  = $_POST['db_host' ];
$dbName  = $_POST['db_name' ];

$dbPass  = generatePassword( );

$params = array(
                'cms' => 'Standalone',
                'cmsVersion' => '',
                'cmsURLVar'  => 'q',
                'usersTable' => '',
                'crmRoot' => "$crmRoot",
                'templateCompileDir' => "$crmRoot/templates_c",
                'uploadDir' => "$crmRoot/upload",
                'imageUploadDir' => '',
                'imageUploadURL' => '',
                'customFileUploadDir' => "$crmRoot/custom",
                'baseURL' => "$baseURL",
                'resourceURL' => "$baseURL",
                'frontEnd' => 0,
                'dbUser' => 'civicrm',
                'dbPass' => "$dbPass",
                'dbHost' => "$dbHost",
                'dbName' => "$dbName",
                );

$data = file_get_contents( "$civicrm_root/templates/CRM/common/civicrm.settings.php.sample.tpl" );
foreach ( $params as $key => $value ) {
    $data = str_replace( '%%' . $key . '%%', $value, $data );
}
$filename = 'civicrm.settings.php';
$fd = fopen( "$civicrm_root/" . $filename, "w" );
fputs( $fd, $data );
fclose( $fd );

?>