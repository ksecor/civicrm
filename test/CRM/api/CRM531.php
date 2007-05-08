<?php

require_once 'api/crm.php';

class TestOfCRM531 extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateContactIndividual()
    {
        $contact = crm_get_contact( array( 'id' => 101 ) );
        $params = array('contact_id' => 101,
                        'custom_2'   => 'Party Name 1');
        
        $contact = crm_update_contact($contact, $params);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
    }
}
?>
