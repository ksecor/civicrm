<?php

ini_set( 'include_path', ".:../packages:.." );
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Quest/API.php';
require_once 'api/crm.php';

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
$cotactIDs  =  crm_get_contacts();
require_once  'CRM/Contact/DAO/Contact.php';
foreach ( $cotactIDs as $id ) {
    echo ".";
    $contact = CRM_Quest_API::getContactInfo($id);
    if ( $contact['contact_sub_type'] && $contact['contact_sub_type'] == 'Student' ) {
       $applicationStatus = CRM_Quest_API::getApplicationStatus( $id );
       if ( $applicationStatus == 'Completed' ) {
           $con1 = & new CRM_Contact_DAO_Contact();
           $con1->contact_id = $id;
           $completedContacts[] =  $con1;
           crm_add_group_contacts( $groupCompleted , $completedContacts);
           crm_delete_group_contacts($groupNotCompleted, $completedContacts);
       } else if ( $applicationStatus == 'In Progress' ) {
           $con2 = & new CRM_Contact_DAO_Contact();
           $con2->contact_id = $id;
           $incompletContacts[] = $con2;
           crm_add_group_contacts(  $groupNotCompleted, $incompletContacts);
           crm_delete_group_contacts($groupCompleted, $incompletContacts);
       }

    }
}
    
?>