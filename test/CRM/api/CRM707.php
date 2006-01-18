<?php

require_once 'api/crm.php';

class TestOfCRM707 extends UnitTestCase 
{
    protected $_individual;
    
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    function testCreateIndividualLocationTypeID() 
    {
            
        $params = array('first_name'    => 'Adam',
                        'last_name'     => 'Bill', 
                        'location_type' => 3,
                        'email'         => 'adambill@yahoo.net',
                        'im'            => 'adam',
                        'phone'         => '56561211'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual[$contact->id] = $contact;
    }
    
}

?>