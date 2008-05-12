<?php

require_once 'api/crm.php';

class TestOfDeleteContactMembership extends UnitTestCase 
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
                        'status_id'          => '2'                       
                        );
	
        $this->_membership = & crm_create_contact_membership($params,$contactId);        
    }

    function testDeleteWrongMembershipWithoutId()
    {
        $params = array();                       
        $membership = & crm_delete_membership($params);
        $this->assertIsA($membership,'CRM_Core_Error');
    }

    function testDeleteMembershipWrongId()
    {        
        $contactId = -88;	
        $membership = & crm_delete_membership($contactId);
        $this->assertIsA($membership,'CRM_Core_Error');      
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
