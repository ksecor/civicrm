<?php

require_once 'api/crm.php';

class TestOfCRM1012 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    // Create Membership Type
    function testCRM1012CreateMembershipType()
    {
        $params = array( 'name' => 'New Membership Type 01',
                         'description' => 'This is new member shipe type',
                         'member_of_contact_id' => 100,
                         'contribution_type_id' => 2
                         );
        
        $membershipType = crm_create_membership_type($params);
        CRM_Core_Error::debug( 'membershipType', $membershipType);
    }
    
    // Get Membership Type(s)
    function testCRM1012GetMembershipType()
    {
        $create = array( 'name' => 'New Membership Type 02',
                         'description' => 'This is new member shipe type',
                         'member_of_contact_id' => 100,
                         'contribution_type_id' => 2
                         );
        
        $membershipType = crm_create_membership_type($create);
        //CRM_Core_Error::debug( 'membershipType', $membershipType);
        
        $getID = array( 'id' => $membershipType['id']);
        $getmembershipTypeID = crm_get_membership_types($getID);
        CRM_Core_Error::debug( 'Get Membership Type Using ID', $getmembershipTypeID);
        
        $getContactIDName = array( 'id'                   => $membershipType['id'],
                                   'member_of_contact_id' => $membershipType['member_of_contact_id'],
                                   'name'                 => 'New Membership Type 02',
                                   );
        $getmembershipTypeContactIDName = crm_get_membership_types($getContactIDName);
        CRM_Core_Error::debug( 'Get Membership Type Using ContactID and Membership Name', $getmembershipTypeContactIDName);
    }
    
    function testCRM1012UpdateMembershipType()
    {
        $create = array( 'name' => 'New Membership Type 03',
                         'description' => 'This is new member shipe type',
                         'member_of_contact_id' => 100,
                         'contribution_type_id' => 2
                         );
        
        $membershipType = crm_create_membership_type($create);
        CRM_Core_Error::debug( 'membershipType', $membershipType);
        
        $updateID = array( 'id' => $membershipType['id'],
                           'description' => 'This is new member shipe type.. updated',
                           );
        $updateMembershipTypeID = crm_update_membership_type($updateID);
        CRM_Core_Error::debug( 'Update Membership Type Using ID', $updateMembershipTypeID);
    }
    
    function testCRM1012DeleteMembershipType()
    {
        $create = array( 'name' => 'New Membership Type 04',
                         'description' => 'This is new member shipe type',
                         'member_of_contact_id' => 100,
                         'contribution_type_id' => 2
                         );
        
        $membershipType = crm_create_membership_type($create);
        CRM_Core_Error::debug( 'membershipType', $membershipType);
        
        $deleteMembershipTypeID = crm_delete_membership_type($membershipType['id']);
        CRM_Core_Error::debug( 'Delete Membership Type', $deleteMembershipTypeID);
    }
    
    function testCRM1012CreateMembershipStatus()
    {
        $create = array( 'name' => 'New Membership Status 01',
                         'start_event' => 3,
                         'end_event' => 2,
                         'is_current_member' => 1,
                         'weight' => 2
                         );
        
        $membershipStatus = crm_create_membership_status($create);
        CRM_Core_Error::debug( 'Membership Status', $membershipStatus);
    }
      
    function testCRM1012GetMembershipStatuses()
    {
        $create = array( 'name' => 'New Membership Status 02',
                         'start_event' => 3,
                         'end_event' => 2,
                         'is_current_member' => 1,
                         'weight' => 3
                         );
        
        $membershipStatus = crm_create_membership_status($create);
        //CRM_Core_Error::debug( 'Membership Status', $membershipStatus);
        
        $get = array( 'weight' => 3 );
        $getStatuses = crm_get_membership_statuses($get);
        CRM_Core_Error::debug( 'Get Membership Status By Weight', $getStatuses);
        
        $get = array( 'is_current_member' => 1 );
        $getStatuses = crm_get_membership_statuses($get);
        CRM_Core_Error::debug( 'Get Membership Status By Current Member ', $getStatuses);
    }
    
    function testCRM1012UpdateMembershipStatuse()
    {
        $create = array( 'name' => 'New Membership Status 03',
                         'start_event' => 3,
                         'end_event' => 2,
                         'is_current_member' => 1,
                         'weight' => 4
                         );
        
        $membershipStatus = crm_create_membership_status($create);
        CRM_Core_Error::debug( 'Membership Status', $membershipStatus);
        
        $update = array( 'id'       => $membershipStatus['id'],
                         'is_admin' => 1
                         );
        $updateStatus = crm_update_membership_status($update);
        CRM_Core_Error::debug( 'Updated Membership Status', $updateStatus);
    }
    
    function testCRM1012DeleteMembershipStatuse()
    {
        $create = array( 'name' => 'New Membership Status 04',
                         'start_event' => 3,
                         'end_event' => 2,
                         'is_current_member' => 1,
                         'weight' => 5
                         );
        
        $membershipStatus = crm_create_membership_status($create);
        CRM_Core_Error::debug( 'Membership Status', $membershipStatus);
        
        $deleteStatus = crm_delete_membership_status($membershipStatus['id']);
        CRM_Core_Error::debug( 'Delete Membership Status', $deleteStatus);
    }
    
    function testCRM1012CreateMembership()
    {
        $params = array ('membership_type_id' => 2,
                         'status_id' => 1
                         );
        $contactID = 90;
        $mem = crm_create_contact_membership($params, $contactID);
        CRM_Core_Error::debug('Member', $mem);
    }
    
    function testCRM1012UpdateMembership()
    {
        $paramsCreate = array ('membership_type_id' => 3,
                               'status_id' => 2
                               );
        $contactID = 90;
        $memberC = crm_create_contact_membership($paramsCreate, $contactID);
        CRM_Core_Error::debug('Membership Create', $memberC);
        
        $paramsUpdate = array( 'id' => $memberC['id'],
                               'status_id' => 2
                               );
        $memberU = crm_update_contact_membership($paramsUpdate);
        CRM_Core_Error::debug('Membership Update', $memberU);
    }
    
    function testCRM1012GetMembership()
    {
        $contactID = 84;
        $memberships = crm_get_contact_memberships($contactID);
        CRM_Core_Error::debug('Memberships', $memberships);
    }
    
    function testCRM1012DeleteMembership()
    {
        $paramsCreate = array ('membership_type_id' => 3,
                               'status_id' => 2
                               );
        $contactID = 50;
        $memberC = crm_create_contact_membership($paramsCreate, $contactID);
        CRM_Core_Error::debug('Membership Create', $memberC);
        
        $memberD = crm_delete_membership($memberC['id']);
        CRM_Core_Error::debug('Membership Delete', $memberD);
    }
    
    function testCRM1012CalcStatus()
    {
        $membershipID = 11;
        $status = crm_calc_membership_status($membershipID);
        CRM_Core_Error::debug('Membership', $status);
    }
}
?>
