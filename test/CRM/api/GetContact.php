<?php

require_once 'api/crm.php';

class TestOfGetContactAPI extends UnitTestCase 
{
    protected $_individual;
    protected $_household;
    protected $_organization;

    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }
    
    function testCreateIndividual() 
    {
        $params = array('first_name'    => 'manish01',
                        'last_name'     => 'zope01', 
                        'location_type' => 'Main', 
                        'im'            => 'manishI', 
                        'im_provider'   => 'AIM',
                        'phone'         => '222222', 
                        'phone_type'    => 'Phone', 
                        'email'         => 'manish01@yahoo.com',
                        'city'          => 'mumbai'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }
    
    function testCreateHousehold() 
    {
        $params = array('household_name' => 'Zope01 House', 
                        'nick_name'      => 'Z01 House', 
                        'email'          => 'household@yahoo.com', 
                        'location_type'  => 'Home',
                        'im'             => 'zopeH', 
                        'im_provider'    => 'AIM',
                        'phone'          => '444444', 
                        'phone_type'     => 'Mobile', 
                        'city'           => 'kolhapur'
                        );
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_household =  $contact;
    }
    
    function testCreateOrganization( ) 
    {
        $params = array('organization_name' => 'Zope01 Pvt. Ltd.', 
                        'nick_name'         => 'Zope01 Companies', 
                        'email'             => 'organization@yahoo.com', 
                        'location_type'     => 'Work',
                        'im'                => 'zopeO', 
                        'im_provider'       => 'AIM',
                        'phone'             => '888888', 
                        'phone_type'        => 'Fax', 
                        'city'              => 'pune'
                        );
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->_organization = $contact;
    }
       
    function testGetContactIndividualByContactID() 
    {
        $params = array('contact_id' => $this->_individual->id);
        $contact =& crm_get_contact($params);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->id, $this->_individual->id);
        $this->assertEqual($contact->location[1]->phone[1]->phone, '222222');
        $this->assertEqual($contact->location[1]->email[1]->email, 'manish01@yahoo.com');
    }
    
    function testGetContactHouseHold() 
    {
        $params = array('contact_id' => $this->_household->id);
        $contact =& crm_get_contact($params);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->id, $this->_household->id);
        $this->assertEqual($contact->location[1]->email[1]->email, 'household@yahoo.com');
    }

    function testGetContactOrganization() 
    {
        $params = array('contact_id' => $this->_organization->id);
        $contact =& crm_get_contact($params);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->id, $this->_organization->id);
        $this->assertEqual($contact->location[1]->email[1]->email, 'organization@yahoo.com');
    }
    
    function testGetContactError() 
    {
        $params = array('contact_id' => -3);
        $contact =& crm_get_contact($params);
        $this->assertIsA($contact, 'CRM_Core_Error');
    }
    
    function testGetContactReturnValuesIndividualByID() 
    {
        $params = array('contact_id' => $this->_individual->id);
        $returnValues = array('contact_id', 'first_name', 'last_name', 'phone',
                              'postal_code', 'state_province', 'email');
        $contact =& crm_get_contact($params, $returnValues);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->id, $this->_individual->id);
        $this->assertEqual($contact->contact_type_object->first_name, 'manish01');
        $this->assertEqual($contact->contact_type_object->last_name, 'zope01');
        $this->assertEqual($contact->location[1]->email[1]->email, 'manish01@yahoo.com');
        $this->assertEqual($contact->location[1]->im[1]->name, 'manishI');
     
    }
    
    function testGetContactReturnValuesIndividualByFNameLName()
    {
        $params = array('contact_id' => $this->_individual->id,
                        'first_name' => 'manish01',
                        'last_name'  => 'zope01',
                        );
        $return_properties = array('contact_id', 'phone', 'phone_type', 'email',
                                   'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->id, $this->_individual->id);
        $this->assertEqual($contact->location[1]->phone[1]->phone, '222222');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Phone');
        $this->assertEqual($contact->location[1]->email[1]->email, 'manish01@yahoo.com');
        $this->assertEqual($contact->location[1]->im[1]->name, 'manishI');
    }
    
    function testGetContactIndividualByEmail()
    {
        $params = array('contact_id' => $this->_individual->id,
                        'email'      => 'manish01@yahoo.com'
                        );
        $return_properties = array('contact_type', 'display_name', 'sort_name', 'phone', 'phone_type',
                                   'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->assertEqual($contact->display_name, 'manish01 zope01' );
        $this->assertEqual($contact->sort_name, 'zope01, manish01');
        $this->assertEqual($contact->location[1]->phone[1]->phone, '222222');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Phone');
        $this->assertEqual($contact->location[1]->im[1]->name, 'manishI');
        $this->assertEqual($contact->location[1]->im[1]->provider_id, '3');
    }
    
    function testGetContactReturnValuesHouseholdByID() 
    {
        $params = array('contact_id' => $this->_household->id);
        $returnValues = array('household_name', 'nick_name', 'email', 'location_type');
        $contact =& crm_get_contact($params, $returnValues);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->id, $this->_household->id);
        $this->assertEqual($contact->contact_type_object->household_name, 'Zope01 House');
        $this->assertEqual($contact->nick_name, 'Z01 House');
        $this->assertEqual($contact->location[1]->email[1]->email, 'household@yahoo.com');
    }

    function testGetContactReturnValuesHouseholdByHName()
    {
        $params = array('contact_id'     => $this->_household->id,
                        'Household_name' => 'Zope01 House',
                        'nick_name'      => 'Z01 House',
                        );
        $return_properties = array('contact_type', 'phone', 'phone_type', 'email',
                                   'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->assertEqual($contact->location[1]->phone[1]->phone, '444444');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Mobile');
        $this->assertEqual($contact->location[1]->email[1]->email, 'household@yahoo.com');
        $this->assertEqual($contact->location[1]->im[1]->name, 'zopeH');
        $this->assertEqual($contact->location[1]->im[1]->provider_id, '3');
    }
    
    function testGetContactReturnValuesOrganization() 
    {
        $params = array('contact_id' => $this->_organization->id);
        $returnValues = array('organization_name', 'nick_name', 'email', 'location_type');
        $contact =& crm_get_contact($params, $returnValues);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->id, $this->_organization->id);
        $this->assertEqual($contact->contact_type_object->organization_name, 'Zope01 Pvt. Ltd.');
        $this->assertEqual($contact->nick_name, 'Zope01 Companies');
        $this->assertEqual($contact->location[1]->email[1]->email, 'organization@yahoo.com');
    }
    
    function testGetContactReturnValuesOrganizationByOrganizationName()
    {
        $params = array('contact_id' => $this->_organization->id,
                        'email'      => 'organization@yahoo.com'
                        );
        $return_properties = array('contact_type', 'organization_name', 'nick_name', 'phone', 'phone_type',
                                   'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->assertEqual($contact->contact_type_object->organization_name, 'Zope01 Pvt. Ltd.');
        $this->assertEqual($contact->nick_name, 'Zope01 Companies');
        $this->assertEqual($contact->location[1]->phone[1]->phone, '888888');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Fax');
        $this->assertEqual($contact->location[1]->im[1]->name, 'zopeO');
    }
    
    function testGetContactContactByPhone()
    {
        $params = array('phone'         => '222222');
        $return_properties = array('contact_type', 'individual_name', 'phone', 'phone_type',
                                   'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        
    }
    
    function testGetContactIndividualByPhoneAndCity()
    {
        $params = array(
                        'phone'         => '222222',
                        'city'          => 'mumbai'
                        );
        $return_properties = array('contact_type', 'individual_name', 'phone', 'phone_type',
                                   'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->location[1]->phone[1]->phone, '222222');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Phone');
        $this->assertEqual($contact->location[1]->email[1]->email, 'manish01@yahoo.com');
        $this->assertEqual($contact->location[1]->im[1]->name, 'manishI');
    }
    
    function testGetContactIndividualCity()
    {
        $params = array('city'          => 'mumbai');
        $return_properties = array('contact_type', 'individual_name', 'phone', 'phone_type',
                                   'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->location[1]->phone[1]->phone, '222222');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Phone');
        $this->assertEqual($contact->location[1]->email[1]->email, 'manish01@yahoo.com');
        $this->assertEqual($contact->location[1]->im[1]->name, 'manishI');
    }
    
    function testGetContactIndividualCityAndEmail()
    {
        $params = array('city'          => 'mumbai',
                        'email'         => 'manish01@yahoo.com');
        $return_properties = array('contact_type', 'individual_name', 'phone', 'phone_type',
                                   'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->location[1]->phone[1]->phone, '222222');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Phone');
        $this->assertEqual($contact->location[1]->email[1]->email, 'manish01@yahoo.com');
        $this->assertEqual($contact->location[1]->im[1]->name, 'manishI');
    }
    
    function testGetContactOrganizationByPhoneAndCity()
    {
        $params = array('phone'         => '888888',
                        'city'          => 'pune');
        $return_properties = array('contact_type', 'organization_name', 'phone');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->assertEqual($contact->contact_type_object->organization_name, 'Zope01 Pvt. Ltd.');
        $this->assertEqual($contact->nick_name, 'Zope01 Companies');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Fax');
        $this->assertEqual($contact->location[1]->im[1]->name, 'zopeO');
    }
    
    function testGetContactOrganizationByCity()
    {
        $params = array('city'          => 'pune');
        $return_properties = array('contact_type', 'organization_name', 'phone');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->assertEqual($contact->contact_type_object->organization_name, 'Zope01 Pvt. Ltd.');
        $this->assertEqual($contact->nick_name, 'Zope01 Companies');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Fax');
        $this->assertEqual($contact->location[1]->im[1]->name, 'zopeO');
        $this->assertEqual($contact->location[1]->phone[1]->phone, '888888');
    } 
    
    function testGetContactHouseholdByPhoneAndCity()
    {
        $params = array('phone'         => '444444',
                        'city'          => 'kolhapur');
        $return_properties = array('contact_type', 'household_name', 'phone');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Mobile');
        $this->assertEqual($contact->location[1]->email[1]->email, 'household@yahoo.com');
        $this->assertEqual($contact->location[1]->im[1]->name, 'zopeH');
        $this->assertEqual($contact->location[1]->im[1]->provider_id, '3');
    }
    
    function testGetContactHouseholdByCity()
    {
        $params = array('city'          => 'kolhapur');
        $return_properties = array('contact_type', 'household_name', 'phone');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Mobile');
        $this->assertEqual($contact->location[1]->email[1]->email, 'household@yahoo.com');
        $this->assertEqual($contact->location[1]->im[1]->name, 'zopeH');
        $this->assertEqual($contact->location[1]->im[1]->provider_id, '3');
        $this->assertEqual($contact->location[1]->phone[1]->phone, '444444');
    }
    
    function testGetContactIndividualHouseholdOrganizationWithError()
    {
        $params = array('phone'         => '888888',
                        'email'         => 'manish01@yahoo.com',
                        'city'          => 'kolhapur');
        $return_properties = array('contact_type', 'individual_name', 'phone', 'phone_type',
                                   'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Core_Error');
    }
    
    function testDeleteIndividual()
    {
        $contact = $this->_individual;
        $val =& crm_delete_contact(& $contact);
        $this->assertNull($val);
    }
    
    function testDeleteHousehold()
    {
        $contact = $this->_household;
        $val =& crm_delete_contact(& $contact);
        $this->assertNull($val);
    }
    
    function testDeleteOrganization()
    {
        $contact = $this->_organization;
        $val =& crm_delete_contact(& $contact);
        $this->assertNull($val);
    }
}
?>