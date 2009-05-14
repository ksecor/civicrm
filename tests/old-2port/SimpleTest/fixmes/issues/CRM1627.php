<?php

require_once 'api/crm.php';

class TestOfCRM1627 extends UnitTestCase 
{
    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }


    function testGetContact() 
    {
        $params = array('contact_id' => 102);
        $returnValues = array('contact_id', 'first_name', 'last_name', 'phone',
                              'postal_code', 'state_province', 'email');
        $this->_individual =& crm_get_contact($params, $returnValues);
    }

    function testUpdateLocation( )
    {
        $locationTypes = CRM_Core_PseudoConstant::locationType( );

        $location =& $this->_individual->location[2];
        $params = array(
                        'location_type' => $locationTypes[$location->location_type_id],
                        'street_address' => 'new',
                        );
        $location =& crm_update_location( $this->_individual, $location->id, $params );
    }



}


