<?php

ini_set( 'include_path', ".:../packages:.." );
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Quest/API.php';
require_once 'api/crm.php';

function user_access( $str ) {
    return true;
}

function module_list( ) {
    return array( );
}

//create Group if Not Exists already
$params = array(
                'name'        => 'Preapplication_Completed',  
                'title'       => 'Preapplication Completed', 
                'visibility'  => 'User and User Admin Only',
                'group_type' => 'Static',
                'is_active'  => 1
                );

//cheeck if this group already exists
$groupParams = array( 'name'        => 'Preapplication_Completed');
$groupCompleted = crm_get_groups( $groupParams );
$groupCompleted = $groupCompleted[0];
if ( ! $groupCompleted ) {
       $groupCompleted = crm_create_group($params);
}

$params = array(
                'name'        => 'Preapplication_Not_Completed',  
                'title'       => 'Preapplication Not Completed', 
                'visibility'  => 'User and User Admin Only',
                'group_type'  => 'Static',
                'is_active'   => 1
                );


//cheeck if this group already exists
$groupParams = array( 'name'  => 'Preapplication_Not_Completed');
$groupNotCompleted = crm_get_groups( $groupParams );
$groupNotCompleted = $groupNotCompleted[0];

if ( ! $groupNotCompleted ) {
    $groupNotCompleted = crm_create_group($params);
 }

/* Grab all the applicable task_status records */
require_once  'CRM/Core/DAO.php';
$completedContacts = array();
$incompleteContacts = array();

$query = "
SELECT target_entity_id AS contact_id, status_id
FROM civicrm_task_status
WHERE task_id = 2";

$p = array();
$dao =& CRM_Core_DAO::executeQuery( $query, $p );
while ( $dao->fetch( ) ) {
    $con = & new CRM_Contact_DAO_Contact();
    $con->contact_id = $dao->contact_id;
    if ($dao->status_id == 328) {
        $completedContacts[] = $con;
    } else {
        $incompleteContacts[] = $con;
    }
}

echo 'Adding completed applicants to completed group' . "\n";
crm_add_group_contacts( $groupCompleted , $completedContacts);
crm_delete_group_contacts($groupNotCompleted, $completedContacts);

echo 'Adding not-completed applicants to incomplete group' . "\n";
crm_add_group_contacts(  $groupNotCompleted, $incompleteContacts);
crm_delete_group_contacts($groupCompleted, $incompleteContacts);

?>