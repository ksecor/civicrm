<?php

require_once 'api/crm.php';

class TestOfUpdateContactMembership extends UnitTestCase 
{
    protected $_membership   = array();
    protected $_individual;
        
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateContact() 
    {
        $params = array(
                        'first_name' => 'Apoorva',
                        'last_name'  => 'Mehta'
                        );
        $contact =& crm_create_contact($params,'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }

    function testCreateMembership()
    {
        $contactId = $this->_individual->id;
        $params = array(
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => '1', 
                        'status_id'          => '2'                       
                        );
	
        $this->_membership = & crm_create_contact_membership($params,$contactId);    
    }

    function testUpdateWrongMembershipEmptyParams()
    {
        $params = array();                        
        $membership = & crm_update_contact_membership($params);
        $this->assertIsA($membership,'CRM_Core_Error');
    }


    function testUpdateWrongMembershipWithoutId()
    {
        $params = array(
                        'membership_type_id' => '2',
                        'join_date'          => '2007-01-21',
                        'start_date'         => '2007-01-21',
                        'end_date'           => '2007-12-21',
                        'source'             => 'Donation',
                        'is_override'        => '1',
                        'status_id'          => '3'                       
                        );
	
        $membership = & crm_update_contact_membership($params);
        $this->assertIsA($membership,'CRM_Core_Error');
    }

    function testUpdateMembership()
    {
        $params = array(
                        'id'                 =>  $this->_membership['id'],
                        'membership_type_id' => '2',
                        'join_date'          => '20060125',
                        'start_date'         => '20060125',
                        'end_date'           => '20061225',
                        'source'             => 'Donation',
                        'status_id'          => '2'                       
                        );	
        $membership = & crm_update_contact_membership($params);        
        $this->assertEqual($membership['id'],$this->_membership['id']);
        $this->assertEqual($membership['membership_type_id'],'2');
        $this->assertEqual($membership['join_date'],'20060125');
        //$this->assertEqual($membership['start_date'],'20060125');
        //$this->assertEqual($membership['end_date'],'20061225');
        //$this->assertEqual($membership['source'],'Donation');
        $this->assertEqual($membership['status_id' ],'2');                          
        $this->_membership = $membership ;
    }
    
    
  
    
    function testDeleteMembership()
    {
        $val = &crm_delete_membership($this->_membership['id']);
        $this->assertNull($val);
    }

    function testDeleteContact()
    {
        $val = &crm_delete_contact(& $this->_individual,102);
        $this->assertNull($val);
    }

}