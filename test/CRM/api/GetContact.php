<?php

require_once 'api/crm.php';

class TestOfGetContactAPI extends UnitTestCase {
    
    protected $_individualId;
    protected $_houseHoldId;
    protected $_organizationId;

    function setUp( ) {
    }

    function tearDown( ) {
    }

    function testCreateIndividual( ) {
        $params = array( 'first_name' => 'kurund','last_name' => 'jalmi', 'location_type' => 'Main', 'im_name' => 'kurundssyahoo', 'im_provider' => 'AIM','phone' => '999999', 'phone_type' => 'Phone', 'email' => 'kurund@yahoo.com');
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Individual' );
        $this->_individualId = $contact->id;
    }

    function testCreateHousehold( ) {
        $params = array( 'household_name' => 'Jasssss\'s House', 'nick_name' => 'J House', 'email' => 'household@yahoo.com', 'location_type' => 'Main' );
        $contact =& crm_create_contact( $params, 'Household' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Household' );
        $this->_houseHoldId =  $contact->id;
    }

    function testCreateOrganization( ) {
        $params = array( 'organization_name' => 'Jasssss\'s House', 'nick_name' => 'J House', 'email' => 'organization@yahoo.com', 'location_type' => 'Main' );
        $contact =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Organization' );
        $this->_organizationId = $contact->id;
    }

    function testGetContactIndividual( ) {
        $params = array( 'contact_id' => $this->_individualId );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->id, $this->_individualId );
        $this->assertEqual( $contact->location[1]->phone[1]->phone, '999999' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'kurund@yahoo.com' );
    }

    function testGetContactHouseHold( ) {
        $params = array( 'contact_id' => $this->_houseHoldId );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->id, $this->_houseHoldId );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'household@yahoo.com' );
    }

    function testGetContactOrganization( ) {
        $params = array( 'contact_id' => $this->_organizationId );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->id, $this->_organizationId );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'organization@yahoo.com' );
    }

    function testGetContactError( ) {
        $params = array( 'contact_id' => -3 );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testGetContactReturnValues( ) {
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
}

?>