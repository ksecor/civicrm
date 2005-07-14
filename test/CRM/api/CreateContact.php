<?php

require_once 'api/crm.php';

class TestOfCreateContactAPI extends UnitTestCase 
{
    protected $_individual   = array();
    protected $_houseHold    = array();
    protected $_organization = array();

    protected $_ii = 0;
    protected $_ih = 0;
    protected $_io = 0;
    
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testCreateEmptyContact() 
    {
        $params = array();
        $contact =& crm_create_contact($params);
        $this->assertIsA($contact, 'CRM_Core_Error');
    }

    function testCreateBadTypeContact() 
    {
        $params = array('email' => 'man1@yahoo.com');
        $contact =& crm_create_contact($params, 'Does Not Exist');
        $this->assertIsA($contact, 'CRM_Core_Error');
    }

    function testCreateBadRequiredFieldsIndividual() 
    {
        $params = array('middle_name' => 'This field is not required');
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Core_Error');
    }

    function testCreateBadRequiredFieldsHousehold() 
    {
        $params = array('middle_name' => 'This field is not required');
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Core_Error');
    }

    function testCreateBadRequiredFieldsOrganization() 
    {
        $params = array('middle_name' => 'This field is not required');
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Core_Error');
    }

    function testCreateEmailIndividual() 
    {
        $params = array('email'         => 'man2@yahoo.com', 
                        'location_type' => 'Home' 
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual[$this->_ii++] = $contact;
    }

    function testCreateBadEmailIndividual() 
    {
        $params = array('email' => 'man.yahoo.com');
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Core_Error');
    }

    function testCreateNameIndividual() 
    {
        $params = array('first_name' => 'abc1',
                        'last_name' => 'xyz1'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual[$this->_ii++] = $contact;
    }

    function testCreateNameHousehold() 
    {
        $params = array('household_name' => 'The abc Household');
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_houseHold[$this->_ih++] = $contact;
    }

    function testCreateNameOrganization() 
    {
        $params = array('organization_name' => 'The abc Organization');
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->_organization[$this->_io++] = $contact;

    }

    function testCreateIndividualwithEmailError() 
    {
        $params = array('first_name' => 'abc3',
                        'last_name'  => 'xyz3',
                        'email'      => 'man3@yahoo.com'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Core_Error');
    }

    function testCreateIndividualwithEmail() 
    {
        $params = array('first_name'    => 'abc4',
                        'last_name'     => 'xyz4',
                        'email'         => 'man4@yahoo.com',
                        'location_type' => 'Work'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual[$this->_ii++] = $contact;
    }

    function testCreateIndividualwithPhone() 
    {
        $params = array('first_name'    => 'abc5',
                        'last_name'     => 'xyz5',
                        'location_type' => 'Other',
                        'phone'         => '11111',
                        'phone_type'    => 'Phone'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual[$this->_ii++] = $contact;
    }

    function testCreateIndividualwithIM() 
    {
        $params = array('first_name'    => 'abc6',
                        'last_name'     => 'xyz6', 
                        'location_type' => 'Work', 
                        'im'            => 'manyahoo', 
                        'im_provider'   => 'Yahoo'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual[$this->_ii++] = $contact;
    }

    function testCreateIndividualwithAll() 
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
        $this->_individual[$this->_ii++] = $contact;
    }

    function testCreateHouseholdDetails() 
    {
        $params = array('household_name' => 'abc8\'s House',
                        'nick_name'      => 'x House',
                        'email'          => 'man8@yahoo.com',
                        'location_type'  => 'Main'
                        );
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_houseHold[$this->_ih++] = $contact;
    }

    function testDeleteIndividual() 
    {
        for ($i=0; $i<count($this->_individual); $i++) {
            $contact = $this->_individual[$i];
            $val =& crm_delete_contact(& $contact);
            $this->assertNull($val);
        }
    }
    
    function testDeleteHousehold() 
    {
        for ($i=0; $i<count($this->_houseHold); $i++) {
            $contact = $this->_houseHold[$i];
            $val =& crm_delete_contact(& $contact);
            $this->assertNull($val);
        }
    }

    function testDeleteOrganization() 
    {
        for ($i=0; $i<count($this->_organization); $i++) {
            $contact = $this->_organization[$i];
            $val =& crm_delete_contact(& $contact);
            $this->assertNull($val);
        }
    }
}

?>