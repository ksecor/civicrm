<?php

require_once 'api/crm.php';

class TestOfCRM369 extends UnitTestCase 
{
    protected $_individual;

    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }
    
    function testGetContactIndividualByContactID() 
    {
        $params = array('contact_id' => 101 );
        $contact =& crm_get_contact($params);
        CRM_Core_Error::debug( 'c', $contact );
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->id, 101);
        $this->assertNotNull($contact->location[1]->address->state );
        $this->assertNotNull($contact->location[1]->address->country );
    }
    
}

?>
