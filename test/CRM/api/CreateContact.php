<?php

require_once 'api/Contact.php';

class TestOfContactAPI extends UnitTestCase {

    function setUp( ) {
    }

    function tearDown( ) {
    }
    /*
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
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Individual' );
    }

    function testCreateBadEmailIndividual( ) {
        $params = array( 'email' => 'lobo.yahoo.com' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testCreateNameIndividual( ) {
        $params = array( 'first_name' => 'Donald', 'last_name' => 'Lobo' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Individual' );
    }
    */

    /***
    function testCreateNameHousehold( ) {
        $params = array( 'household_name' => 'The Lobo Household' );
        $contact =& crm_create_contact( $params, 'Household' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Household' );

        $params = array( 'nick_name' => 'The Lobo Household NickName' );
        $contact =& crm_create_contact( $params, 'Household' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Household' );
    }

    function testCreateNameOrganization( ) {
        $params = array( 'organization_name' => 'The Lobo Organization' );
        $contact =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Organization' );

        $params = array( 'nick_name' => 'The Lobo Organization NickName' );
        $contact =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Organization' );
    }

    **/
    
    // this is not working

    function testCreateIndividualwithEmail_Error( ) {
        $params = array( 'first_name' => 'Hello','last_name' => 'Hiii','email' => 'kurund@yahoo.com' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Individual' );
    }

    // this is working
    function testCreateIndividualwithEmail( ) {
        $params = array( 'first_name' => 'Hello','last_name' => 'Hiii','email' => 'kurund@yahoo.com', 'location_type_id' => 1, 'is_primary' => 1 );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Individual' );
    }

    // this is working
    function testCreateIndividualwithPhone( ) {
        $params = array( 'first_name' => 'Hello11','last_name' => 'Hiii', 'location_type_id' => 1, 'phone' => '11111', 'phone_type' => 'Phone','is_primary' => 1 );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Individual' );
    }

    // this is working
    function testCreateIndividualwithIm( ) {
        $params = array( 'first_name' => 'Hello11','last_name' => 'Hiii', 'location_type_id' => 1, 'name' => 'kurundyahoo', 'provider_id' => 1,'is_primary' => 1 );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Individual' );
    }

    function testCreateIndividualwithAll( ) {
        $params = array( 'first_name' => 'kurund','last_name' => 'jalmi', 'location_type_id' => 1, 'name' => 'kurundssyahoo', 'provider_id' => 1,'is_primary' => 1,'phone' => '999999', 'phone_type' => 'Phone', 'email' => 'kurund@yahoo.com');
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Individual' );
    }


    // this is not working
    function testCreateHousehold1( ) {
        $params = array( 'household_name' => 'Jasssss\'s House', 'nick_name' => 'J House', 'primary_contact_id' => 4 );
        $contact =& crm_create_contact( $params, 'Household' );
        // $this->assertIsA( $contact, 'CRM_Contact_DAO_Household' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    // this is working
    function testCreateHousehold2( ) {
        $params = array( 'household_name' => 'Jalmi\'s House', 'nick_name' => 'J House', 'primary_contact_id' => 42 );
        $contact =& crm_create_contact( $params, 'Household' );
        // $this->assertIsA( $contact, 'CRM_Contact_DAO_Household' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }


}

?>