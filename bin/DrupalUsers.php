<?php

require_once '../modules/config.inc.php';

require_once('DB.php');
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/BAO/UFMatch.php';

$config =& CRM_Core_Config::singleton( );

$dsn_drupal  = "mysql://drupal:Mt!Everest@localhost/drupal";
$db_drupal = DB::connect($dsn_drupal);
if ( DB::isError( $db_drupal ) ) {
    die( "Cannot connect to drupal db via $dsn, " . $db_drupal->getMessage( ) );
}
 
$sql   = "SELECT uid, mail FROM users where mail != ''";
$query = $db_drupal->query( $sql );

$user  = null;
$uf    = 'Drupal';

while ( $row = $query->fetchRow( DB_FETCHMODE_ASSOC ) ) {
    echo $row['mail'] . "\n";
    if ( CRM_Core_BAO_UFMatch::synchronizeUFMatch( $user, $row['uid'], $row['mail'], $uf ) ) {
        echo "Created Contact for user\n";
    }
}

$db_drupal->disconnect( );

?>
