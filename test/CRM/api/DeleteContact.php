<?php

require_once 'api/crm.php';

class TestOfDeleteContactAPI extends UnitTestCase 
{
    protected $_individual;
    protected $_houseHold;
    protected $_organization;

    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testCreateIndividual() 
    {
        $params = array('first_name' => 'Manish',
                        'last_name'  => 'Zope'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }
    
    function testCreateHousehold() 
    {
        $params = array('household_name' => 'Zope House');
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_houseHold = $contact;
    }
    
    function testCreateOrganization() 
    {
        $params = array('organization_name' => 'Zope Industries');
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->_organization = $contact;
    }
    
    function testDeleteIndividual()
    {
        $contact = $this->_individual;
        $val =& crm_delete_contact(& $contact);
        $this->assertNull($val);
    }

    function testDeleteIndividualError()
    {
        $contact = $this->_individual->id;
        $contact =& crm_get_contact(& $contact);
        $val =& crm_delete_contact(& $contact);
        $this->AssertIsA($val, 'CRM_Core_Error');
    }
    function testDeleteHousehold()
    {
        $contact = $this->_houseHold;
        $val = crm_delete_contact(& $contact);
        $this->assertNull($val);
    }    
    
    function testDeleteOrganization()
    {
        $contact = $this->_organization;
        $val = crm_delete_contact(& $contact);
        $this->assertNull($val);
    }    
}
?>