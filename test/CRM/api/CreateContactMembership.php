<?php

require_once 'api/crm.php';

class TestOfCreateContactMembership extends UnitTestCase 
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
        $this->_individual =& crm_create_contact($params,'Individual');
        $this->assertIsA($this->_individual, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($this->_individual->contact_type, 'Individual');
    }

    
    function testCreateWrongMembershipWithoutId()
    {
        $params = array(
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'status_id'          => '2'                       
                        );
	
        $membership = & crm_create_contact_membership($params,NULL);
        $this->assertIsA($membership,'CRM_Core_Error');
    }

    function testCreateMembershipWithoutParams()
    {
        $contactId = $this->_individual->id;
        $params = array();	
        $membership = & crm_create_contact_membership($params,$contactId);
        $this->assertIsA($membership,'CRM_Core_Error');      
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
        $this->assertEqual($this->_membership['membership_type_id'],'1');
         $this->assertEqual($this->_membership['join_date'],'20060121');
//         $this->assertEqual($this->_membership['start_date'],'2006-01-21');
//         $this->assertEqual($this->_membership['end_date'],'2006-12-21');
//         $this->assertEqual($this->_membership['source'],'Payment');
         $this->assertEqual($this->_membership['status_id'],'2');      
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