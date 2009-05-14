<?php

require_once 'api/crm.php';

class TestOfDemoAPI extends UnitTestCase {
  
    protected $_individual;
    protected $_houseHold;
    protected $_organization;

    function setUp( ) {
    }

    function tearDown( ) {
    }

    function testCreateIndividual( ) {
        echo "Creating an Individual Contact Record ...<br/>\n";
        flush( );
        $params = array( 'first_name' => 'kurund','last_name' => 'jalmi' );
        $contact =& crm_create_contact( $params, 'Individual' );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type, 'Individual' );
        $this->_individual = $contact;
    }
    
    function testCreateSameIndividual( ) {
        return $this->testCreateIndividual( );
    }

    function testGetContactIndividual( )
    {
        echo "Getting the inserted contact record ...<br/>\n";
        flush( );
        $params = array( 'contact_id' => $this->_individual->id );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->id, $this->_individual->id );
        $this->assertEqual( $contact->contact_type_object->first_name, 'kurund' );
    }


    function testUpdateContactIndividual( ) {
        echo "Updating the inserted contact record ...<br/>\n";
        flush( );
        $params = array( 'contact_id' => $this->_individual->id, 'first_name' => 'yash', 'location_type' => 'Main', 'im_name' => 'kurundssyahoo', 'im_provider' => 'AIM','phone' => '999999', 'phone_type' => 'Phone', 'email' => 'kurund@yahoo.com');
        $contact = crm_update_contact( $this->_individual, $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->contact_type_object->first_name, 'yash' );
        $this->assertEqual( $contact->contact_type_object->last_name , 'jalmi'  );
        $this->assertEqual( $contact->location[1]->phone[1]->phone, '999999' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'kurund@yahoo.com' );
        $this->_individual = $contact;
    }

    function testGetUpdateContactIndividual( ) {
        echo "Getting the updated contact record ...<br/>\n";
        flush( );
        $params = array( 'contact_id' => $this->_individual->id );
        $contact =& crm_get_contact( $params );
        $this->assertIsA( $contact, 'CRM_Contact_DAO_Contact' );
        $this->assertEqual( $contact->id, $this->_individual->id );
        $this->assertEqual( $contact->contact_type_object->first_name, 'yash' );
        $this->assertEqual( $contact->contact_type_object->last_name , 'jalmi'  );
        $this->assertEqual( $contact->location[1]->phone[1]->phone, '999999' );
        $this->assertEqual( $contact->location[1]->email[1]->email, 'kurund@yahoo.com' );
    }
}
