<?php

require_once 'api/crm.php';

class TestOfGetContactAPI extends UnitTestCase 
{
    protected $_individualId;
    protected $_houseHoldId;
    protected $_organizationId;

    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }
    
    function testCreateIndividual() 
    {
        $params = array('first_name'    => 'kurund',
                        'last_name'     => 'jalmi', 
                        'location_type' => 'Main', 
                        'im_name'       => 'kurundssyahoo', 
                        'im_provider'   => 'AIM',
                        'phone'         => '999999', 
                        'phone_type'    => 'Phone', 
                        'email'         => 'kurund@yahoo.com'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individualId = $contact->id;
    }
    
    function testCreateHousehold() 
    {
        $params = array('household_name' => 'Jalmi House', 
                        'nick_name'      => 'J House', 
                        'email'          => 'household@yahoo.com', 
                        'location_type'  => 'Main',
                        'im_name'        => 'kurundssyahoo', 
                        'im_provider'    => 'AIM',
                        'phone'          => '999999', 
                        'phone_type'     => 'Phone' 
                        );
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_houseHoldId =  $contact->id;
    }
    
    function testCreateOrganization( ) 
    {
        $params = array('organization_name' => 'Jalmi House', 
                        'nick_name'         => 'J House', 
                        'email'             => 'organization@yahoo.com', 
                        'location_type'     => 'Main',
                        'im_name'           => 'kurundssyahoo', 
                        'im_provider'       => 'AIM',
                        'phone'             => '999999', 
                        'phone_type'        => 'Phone' 
                        );
        $contact =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Organization' );
        $this->_organizationId = $contact->id;
    }
       
    function testGetContactIndividualByContactID( ) 
    {
        $params = array( 'contact_id' => $this->_individualId );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->id, $this->_individualId );
        $this->assertEqual( $contact->location[1]->phone[1]->phone, '999999' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'kurund@yahoo.com' );
    }
    
    function testGetContactHouseHold( ) 
    {
        $params = array( 'contact_id' => $this->_houseHoldId );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->id, $this->_houseHoldId );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'household@yahoo.com' );
    }

    function testGetContactOrganization( ) 
    {
        $params = array( 'contact_id' => $this->_organizationId );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->id, $this->_organizationId );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'organization@yahoo.com' );
    }

    function testGetContactError( ) 
    {
        $params = array( 'contact_id' => -3 );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Error' );
    }
    
    function testGetContactReturnValuesIndividualByID( ) 
    {
        $params = array( 'contact_id' => $this->_individualId );
        $returnValues = array( 'contact_id', 'first_name', 'last_name', 'phone',
                               'postal_code', 'state_province', 'email', 'im_name', 'im_provider' );
        $contact =& crm_get_contact( $params, $returnValues );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->id, $this->_individualId );
        $this->assertEqual( $contact->contact_type_object->first_name, 'kurund' );
        $this->assertEqual( $contact->contact_type_object->last_name, 'jalmi' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'kurund@yahoo.com' );
        $this->assertEqual( $contact->location[1]->im[1]->name, 'kurundssyahoo' );
     
    }
   
    function testGetContactReturnValuesIndividualByFNameLName()
    {
        $params = array('contact_id' => $this->_individualId,
                        'first_name' => 'kurund',
                        'last_name' => 'jalmi',
                        );
        $return_properties = array('contact_id', 'phone', 'phone_type', 'email', 'im_name', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual( $contact->id, $this->_individualId );
        $this->assertEqual( $contact->location[1]->phone[1]->phone, '999999' );
        $this->assertEqual( $contact->location[1]->phone[1]->phone_type, 'Phone' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'kurund@yahoo.com' );
        $this->assertEqual( $contact->location[1]->im[1]->name, 'kurundssyahoo' );
        $this->assertEqual( $contact->location[1]->im[1]->provider_id, '3' );
    }
    
    function testGetContactIndividualByEmail()
    {
        $params = array('contact_id' => $this->_individualId,
                        'email' => 'kurund@yahoo.com'
                        );
        $return_properties = array('contact_type', 'display_name', 'sort_name', 'phone', 'phone_type', 'im_name', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual( $contact->contact_type, 'Individual' );
        $this->assertEqual( $contact->contact_type_object->display_name, 'kurund  jalmi' );
        $this->assertEqual( $contact->sort_name, 'jalmi, kurund' );
        $this->assertEqual( $contact->location[1]->phone[1]->phone, '999999' );
        $this->assertEqual( $contact->location[1]->phone[1]->phone_type, 'Phone' );
        $this->assertEqual( $contact->location[1]->im[1]->name, 'kurundssyahoo' );
        $this->assertEqual( $contact->location[1]->im[1]->provider_id, '3' );
    }
        
    function testGetContactReturnValuesHouseholdByID() 
    {
        $params = array('contact_id' => $this->_houseHoldId);
        $returnValues = array('household_name', 'nick_name', 'email', 'location_type');
        $contact =& crm_get_contact($params, $returnValues);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual( $contact->id, $this->_houseHoldId );
        $this->assertEqual( $contact->contact_type_object->household_name, 'Jalmi House' );
        $this->assertEqual( $contact->contact_type_object->nick_name, 'J House' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'household@yahoo.com' );
        $this->assertEqual( $contact->location[1]->location_type_id, '3' );
    }

    function testGetContactReturnValuesHouseholdByHName()
    {
        $params = array('contact_id' => $this->_houseHoldId,
                        'Household_name' => 'Jalmi House',
                        'nick_name' => 'J House',
                        );
        $return_properties = array('contact_type', 'phone', 'phone_type', 'email', 'im_name', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual( $contact->contact_type, 'Household' );
        $this->assertEqual( $contact->location[1]->phone[1]->phone, '999999' );
        $this->assertEqual( $contact->location[1]->phone[1]->phone_type, 'Phone' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'household@yahoo.com' );
        $this->assertEqual( $contact->location[1]->im[1]->name, 'kurundssyahoo' );
        $this->assertEqual( $contact->location[1]->im[1]->provider_id, '3' );
    }
    
    function testGetContactReturnValuesOrganization() 
    {
        $params = array('contact_id' => $this->_organizationId);
        $returnValues = array('organization_name', 'nick_name', 'email', 'location_type');
        $contact =& crm_get_contact($params, $returnValues);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual( $contact->id, $this->_organizationId );
        $this->assertEqual( $contact->contact_type_object->organization_name, 'Jalmi House' );
        $this->assertEqual( $contact->contact_type_object->nick_name, 'J House' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'organization@yahoo.com' );
        $this->assertEqual( $contact->location[1]->location_type_id, '3' );
    }
    function testGetContactReturnValuesHouseholdByHouseholdName()
    {
        $params = array('contact_id' => $this->_organizationId,
                        'email' => 'organization@yahoo.com'
                        );
        $return_properties = array('contact_type', 'organization_name', 'nick_name', 'phone', 'phone_type', 'im_name', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual( $contact->contact_type, 'Organization' );
        $this->assertEqual( $contact->contact_type_object->organization_name, 'Jalmi House' );
        $this->assertEqual( $contact->contact_type_object->nick_name, 'J House' );
        $this->assertEqual( $contact->location[1]->phone[1]->phone, '999999' );
        $this->assertEqual( $contact->location[1]->phone[1]->phone_type, 'Phone' );
        $this->assertEqual( $contact->location[1]->im[1]->name, 'kurundssyahoo' );
        $this->assertEqual( $contact->location[1]->im[1]->provider_id, '3' );
    }
}
?>