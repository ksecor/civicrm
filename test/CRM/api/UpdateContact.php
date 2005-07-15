<?php

require_once 'api/crm.php';

class TestOfUpdateContactAPI extends UnitTestCase 
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
    
//     function testCreateIndividual()
//     {
//         $params = array('first_name'    => 'manish',
//                         'last_name'     => 'zope',
//                         'location_type' => 'Home', 
//                         'im'            => 'mlzope', 
//                         'im_provider'   => 'AIM',
//                         'phone'         => '123456', 
//                         'phone_type'    => 'Phone', 
//                         'email'         => 'manish@yahoo.com'
//                         );
//         $contact =& crm_create_contact($params, 'Individual');
//         $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
//         $this->assertEqual($contact->contact_type, 'Individual');
//         $this->_individual = $contact;
//     }

    function testCreateIndividual()
    {
        $params = array('first_name'    => 'manish',
                        'last_name'     => 'zope',
                        'location_type' => 'Home', 
                        'email'         => 'manish@yahoo.com'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }

//     function testCreateHousehold() 
//     {
//         $params = array('household_name' => 'Jalmi House');
//         $contact =& crm_create_contact($params, 'Household');
//         $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
//         $this->assertEqual($contact->contact_type, 'Household');
//         $this->_houseHold =  $contact;
//     }

//     function testCreateOrganization() 
//     {
//         $params = array('organization_name' => 'Jalmi House');
//         $contact =& crm_create_contact($params, 'Organization');
//         $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
//         $this->assertEqual($contact->contact_type, 'Organization');
//         $this->_organization = $contact;
//     }
    
//     function testUpdateContactIndividual() 
//     {
//         $params = array('contact_id'    => $this->_individual->id, 
//                         'location_type' => 'Main', 
//                         'im'            => 'kurundssyahoo', 
//                         'im_provider'   => 'AIM',
//                         'phone'         => '999999', 
//                         'phone_type'    => 'Phone', 
//                         'email'         => 'kurund@yahoo.com'
//                         );
//         $contact = $this->_individual;
//         $contact = crm_update_contact($contact, $params);
//         $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
//         $this->assertEqual($contact->contact_type_object->first_name, 'kurund');
//         $this->assertEqual($contact->contact_type_object->last_name , 'jalmi');
//         $this->assertEqual($contact->location[1]->phone[1]->phone, '999999');
//         $this->assertEqual($contact->location[1]->email[1]->email, 'kurund@yahoo.com');
//     }

    function testUpdateContactIndividual() 
    {
        $params = array('contact_id'    => $this->_individual->id,
                        'location_type' => 'Home',
                        'email'         => 'kurund@yahoo.com'
                        );
        $contact = $this->_individual;
        $contact = crm_update_contact($contact, $params);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->location[1]->email[1]->email, 'kurund@yahoo.com');
    }

//     function testUpdateContactHousehold() 
//     {
//         $params = array('contact_id'    => $this->_houseHold->id, 
//                         'nick_name'     => 'J House', 
//                         'email'         => 'household@yahoo.com', 
//                         'location_type' => 'Main'
//                         );
//         $contact = $this->_houseHold;
//         $contact = crm_update_contact($contact, $params);
//         $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
//         $this->assertEqual( $contact->location[1]->email[1]->email, 'household@yahoo.com' );
//     }
    
//     function testUpdateContactOrganization() 
//     {
//         $params = array('contact_id'    => $this->_organization->id, 
//                         'nick_name'     => 'J House',
//                         'email'         => 'organization@yahoo.com',
//                         'location_type' => 'Main'
//                         );
//         $contact = $this->_organization;
//         $contact = crm_update_contact($contact, $params);
//         $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
//         $this->assertEqual($contact->location[1]->email[1]->email, 'organization@yahoo.com');
//     }
    
//     function testUpdateContactError() 
//     {
//         $contact = new CRM_Contact_BAO_Individual();
//         $contact->id = -2;
//         $params = array('first_name' => 'Whatever');
//         $contact =& crm_update_contact($contact, $params);
//         $this->assertIsA($contact, 'CRM_Core_Error');
//     }
    
//     function testUpdateLocationTypeIndividual()
//     {
//         $params = array('contact_id'    => $this->_individual->id, 
//                         'location_type' => 'Home', 
//                         );
//         $contact = $this->_individual;
//         $contact = crm_update_contact($contact, $params);
//         $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
//     }
    
//     function testUpdateHouseholdNameError()
//     {
//         $params = array('contact_id'     => $this->_houseHold->id, 
//                         'household_name' => '', 
//                         'location_type'  => 'Home'
//                         );
//         $contact = $this->_houseHold;
//         $contact = crm_update_contact($contact, $params);
//         $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
//     }
    
//     function testUpdateEmailIndividualError()
//     {
//         $params = array('contact_id'    => $this->_individual->id, 
//                         'location_type' => 'Home', 
//                         'email'         => 'manishzope.aaa.aaa'
//                         );
//         $contact = $this->_individual;
//         $contact = crm_update_contact($contact, $params);
//         $this->assertIsA($contact, 'CRM_Core_Error');
//     }
    
    function testDeleteIndividual()
    {
        $contact = $this->_individual;
        $val =& crm_delete_contact(& $contact);
        $this->assertNull($val);
    }

//     function testDeleteHousehold()
//     {
//         $contact = $this->_houseHold;
//         $val =& crm_delete_contact(& $contact);
//         $this->assertNull($val);
//     }

//     function testDeleteOrganization()
//     {
//         $contact = $this->_organization;
//         $val =& crm_delete_contact(& $contact);
//         $this->assertNull($val);
//     }
    
}

?>