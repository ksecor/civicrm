<?php

require_once 'api/crm.php';

class TestOfUpdateContactAPI extends UnitTestCase {
    
    protected $_individual;
    protected $_houseHold;
    protected $_organization;

    function setUp( ) {
    }

    function tearDown( ) {
    }

    function testCreateIndividual( ) {
        $params = array( 'first_name' => 'kurund','last_name' => 'jalmi' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Individual' );
        $this->_individual = $contact;
    }

    function testCreateHousehold( ) {
        $params = array( 'household_name' => 'Jasssss\'s House' );
        $contact =& crm_create_contact( $params, 'Household' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Household' );
        $this->_houseHold =  $contact;
    }

    function testCreateOrganization( ) {
        $params = array( 'organization_name' => 'Jasssss\'s House' );
        $contact =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Organization' );
        $this->_organization = $contact;
    }

    function testUpdateContactIndividual( ) {
        $params = array( 'contact_id' => $this->_individualId, 'location_type' => 'Main', 'im_name' => 'kurundssyahoo', 'im_provider' => 'AIM','phone' => '999999', 'phone_type' => 'Phone', 'email' => 'kurund@yahoo.com');
        $contact = $this->_individual;
        $contact = crm_update_contact( $contact, $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type_object->first_name, 'kurund' );
        $this->assertEqual( $contact->contact_type_object->last_name , 'jalmi'  );
        $this->assertEqual( $contact->location[1]->phone[1]->phone, '999999' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'kurund@yahoo.com' );
    }

    function testUpdateContactHouseHold( ) {
        $params = array( 'contact_id' => $this->_houseHoldId, 'nick_name' => 'J House', 'email' => 'household@yahoo.com', 'location_type' => 'Main'  );
        $contact = $this->_houseHold;
        $contact = crm_update_contact( $contact, $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'household@yahoo.com' );
    }

    function testUpdateContactOrganization( ) {
        $params = array( 'contact_id' => $this->_organizationId, 'nick_name' => 'J House', 'email' => 'organization@yahoo.com', 'location_type' => 'Main'  );
        $contact = $this->_organization;
        $contact = crm_update_contact( $contact, $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'organization@yahoo.com' );
    }

    function testUpdateContactError( ) {
        $contact = new CRM_Contact_BAO_Individual( );
        $contact->id = -2;
        $params = array( 'first_name' => 'Whatever' );
        $contact =& crm_update_contact( $contact, $params );
        $this->assertIsA( $contact, 'CRM_Error' );
    }
}

?>