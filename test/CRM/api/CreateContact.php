<?php

require_once 'api/Contact.php';

class TestOfContactAPI extends UnitTestCase {

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
        $params = array( 'email' => 'lobo@yahoo.com' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact' );
    }

    function testCreateBadEmailIndividual( ) {
        $params = array( 'email' => 'lobo.yahoo.com' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Error' );
    }

    function testCreateNameIndividual( ) {
        $params = array( 'first_name' => 'Donald', 'last_name' => 'Lobo' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact' );
    }

    function testCreateNameHousehold( ) {
        $params = array( 'household_name' => 'The Lobo Household' );
        $contact =& crm_create_contact( $params, 'Household' );
        $this->assertIsA( $contact, 'CRM_Contact' );

        $params = array( 'nick_name' => 'The Lobo Household NickName' );
        $contact =& crm_create_contact( $params, 'Household' );
        $this->assertIsA( $contact, 'CRM_Contact' );
    }

    function testCreateNameOrganization( ) {
        $params = array( 'organization_name' => 'The Lobo Organization' );
        $contact =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA( $contact, 'CRM_Contact' );

        $params = array( 'nick_name' => 'The Lobo Organization NickName' );
        $contact =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA( $contact, 'CRM_Contact' );
    }


    

}

?>