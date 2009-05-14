<?php

require_once 'api/crm.php';

class TestOfSubscribeGroupContact extends UnitTestCase 
{
    protected $_individual;
    protected $_houseHold ;
    protected $_organization;

    protected $_ii = 0;
    protected $_ih = 0;
    protected $_io = 0;
    
    function setUp() 
    {
    }

    function tearDown() 
    {
    }
    
    function testCreateIndividual() 
    {
        $params = array('first_name'    => 'abc7',
                        'last_name'     => 'xyz7', 
                        'location_type' => 'Main',
                        'im'            => 'manyahoo',
                        'im_provider'   => 'AIM',
                        'phone'         => '999999',
                        'phone_type'    => 'Phone',
                        'email'         => 'man7@yahoo.com'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }
    
    function testCreateHousehold() 
    {
        $params = array('household_name' => 'abc8\'s House',
                        'nick_name'      => 'x House',
                        'email'          => 'man8@yahoo.com',
                        'location_type'  => 'Main'
                        );
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_houseHold = $contact;
    }
    
    function testCreateOrganization() 
    {
        $params = array('organization_name' => 'The abc Organization');
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->_organization = $contact;

    }


   
    function testSubscribeContactToGroup() 
    {
        $contacts = array($this->_individual);
        $group = new CRM_Contact_DAO_Group();
        $group->id = 1;
        $return = crm_subscribe_group_contacts($group,$contacts);
        $this->assertNull($return); 
        
    }

    function testSubscribeContactsToGroup()
    {
        $contacts = array($this->_houseHold ,$this->_organization);
        $group = new CRM_Contact_DAO_Group();
        $group->id = 2;
        $return = crm_subscribe_group_contacts($group,$contacts);
        $this->assertNull($return); 
    }
    
    
    
    function testDeleteIndividual() 
    {     
        $contact = $this->_individual;
        $val =& crm_delete_contact(& $contact,102);
        $this->assertNull($val);
    }
    
    function testDeleteHousehold() 
    {
        
        $contact = $this->_houseHold;
        $val =& crm_delete_contact(& $contact,102);
        $this->assertNull($val);
    }

    function testDeleteOrganization() 
    {
        $contact = $this->_organization;
        $val =& crm_delete_contact(& $contact,102);
        $this->assertNull($val);
    }
}

