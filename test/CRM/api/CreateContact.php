<?php

require_once 'api/crm.php';

class TestOfCreateContactAPI extends UnitTestCase {

    function setUp( ) {
    }

    function tearDown( ) {
    }

    function testCreateEmptyContact( ) {
        $params = array( );
        $contact =& crm_create_contact( $params );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testCreateBadTypeContact( ) {
        $params = array( 'email' => 'lobo@yahoo.com' );
        $contact =& crm_create_contact( $params, 'Does Not Exist' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testCreateBadRequiredFieldsIndividual( ) {
        $params = array( 'middle_name' => 'This field is not required' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testCreateBadRequiredFieldsHousehold( ) {
        $params = array( 'middle_name' => 'This field is not required' );
        $contact =& crm_create_contact( $params, 'Household' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testCreateBadRequiredFieldsOrganization( ) {
        $params = array( 'middle_name' => 'This field is not required' );
        $contact =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testCreateEmailIndividual( ) {
        $params = array( 'email' => 'lobo@yahoo.com', 'location_type' => 'Home' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Individual' );
    }

    function testCreateBadEmailIndividual( ) {
        $params = array( 'email' => 'lobo.yahoo.com' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testCreateNameIndividual( ) {
        $params = array( 'first_name' => 'Donald', 'last_name' => 'Lobo' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Individual' );
    }

    function testCreateNameHousehold( ) {
        $params = array( 'household_name' => 'The Lobo Household' );
        $contact =& crm_create_contact( $params, 'Household' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Household' );
    }

    function testCreateNameOrganization( ) {
        $params = array( 'organization_name' => 'The Lobo Organization' );
        $contact =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Organization' );
    }

    function testCreateIndividualwithEmailError( ) {
        $params = array( 'first_name' => 'Hello','last_name' => 'Hiii','email' => 'kurund@yahoo.com' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testCreateIndividualwithEmail( ) {
        $params = array( 'first_name' => 'Hello','last_name' => 'Hiii','email' => 'kurund@yahoo.com', 'location_type' => 'Work' );
        $contact =& crm_create_contact( $params, 'Individual' );
        //$this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Individual' );
    }

    function testCreateIndividualwithPhone( ) {
        $params = array( 'first_name' => 'Hello11','last_name' => 'Hiii', 'location_type' => 'Other', 'phone' => '11111', 'phone_type' => 'Phone' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Individual' );
    }

    function testCreateIndividualwithIM( ) {
        $params = array( 'first_name' => 'Hello11','last_name' => 'Hiii', 'location_type' => 'Work', 'im_name' => 'kurundyahoo', 'im_provider' => 'Yahoo' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Individual' );
    }

    function testCreateIndividualwithAll( ) {
        $params = array( 'first_name' => 'kurund','last_name' => 'jalmi', 'location_type' => 'Main', 'im_name' => 'kurundssyahoo', 'im_provider' => 'AIM','phone' => '999999', 'phone_type' => 'Phone', 'email' => 'kurund@yahoo.com');
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Individual' );
    }

    function testCreateHouseholdDetails( ) {
        $params = array( 'household_name' => 'Jasssss\'s House', 'nick_name' => 'J House', 'email' => 'lobo@yahoo.com', 'location_type' => 'Main' );
        $contact =& crm_create_contact( $params, 'Household' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Household' );
    }

}

?>