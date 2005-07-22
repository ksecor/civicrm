<?php
ini_set( 'include_path', ".:../packages" );

require_once('DB.php');

$db_type = 'mysql';
$db_user = 'root';
$db_pass = '';
$db_host = 'localhost';
$db_db   = 'drupal';

$data = "$db_type://$db_user:$db_pass@$db_host/$db_db";

$db = DB::connect($data);

if(DB::isError($db)) {
    die($db->getMessage());
}
 
$sql = "SELECT mail FROM users where mail != ''";
$query = $db->query($sql);

while($row = $query->fetchRow(DB_FETCHMODE_ASSOC)) {
    //echo $row['mail'] . "\n";
    $email = $row['mail'];
    $emailContact = "SELECT * FROM civicrm.civicrm_email a WHERE a.email = '$email'";
    $num = $db->query($emailContact);
    if ( !($num->numRows()) ) {
        $insertContact = "INSERT INTO civicrm.civicrm_contact (id, domain_id, contact_type) VALUES ('', 1, 'Individual')";
        $db->query($insertContact);

        $newContactId = mysql_insert_id();
        //echo "$newContactId\n";
        $insertIndividual = "INSERT INTO civicrm.civicrm_individual (id, contact_id) VALUES ('', $newContactId)";
        $db->query($insertIndividual);
        $insertLocation = "INSERT INTO civicrm.civicrm_location (id, contact_id, location_type_id, is_primary) VALUES('', $newContactId, 1, 1)";
        $db->query($insertLocation);
        
        $newLocationId = mysql_insert_id();
        //echo "$newLocationId\n";
        $insertEmail = "INSERT INTO civicrm.civicrm_email (id, location_id, email, is_primary) VALUES ('', $newLocationId, '$email', 1)";
        $db->query($insertEmail);        
    }
}

$db->disconnect();

?>