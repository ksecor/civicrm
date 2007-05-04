<?php

require_once 'api/crm.php';

class TestOfCRM619 extends UnitTestCase 
{
    protected $_individual;

    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }
    
    function testGetContactIndividualByEmail() 
    {
        $params = array('email' => 'lobo@civicrm.org' );
        $contact =& crm_get_contact($params);
        CRM_Core_Error::debug( 'c', $contact );
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
    }
    
}

?>
