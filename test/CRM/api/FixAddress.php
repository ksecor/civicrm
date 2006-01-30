<?php

require_once 'api/crm.php';

class TestOfFixAddressAPI extends UnitTestCase 
{
    protected $_individual;
    protected $_household;
    protected $_organization;

    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }

    function testFixAddress() 
    {

        $params  = array('contact_id' => 1);
        $contact =& crm_get_contact($params);
        $locations =& crm_get_locations($contact);
        $object =& $locations[1]->address;
        crm_fix_address($contact); //params $contact or $object

    }

}
?>