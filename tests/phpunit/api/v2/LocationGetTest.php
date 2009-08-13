<?php

require_once 'api/v2/Contribute.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_LocationGetTest extends CiviUnitTestCase
{
    var $_contactId;
    var $_location;

    function get_info( )
    {
        return array(
                     'name'        => 'Location Get',
                     'description' => 'Test all Location Get API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
    
    function setUp()
    {
        parent::setUp();        
    
        $this->_contactId = $this->individualCreate();
        $this->_location =& $this->locationAdd($this->_contactId);
    }

    function testGetWithoutProperParams()
    {
        // empty params
        $result =& civicrm_location_get(array());
        $this->assertEquals($result['is_error'], 1);
        // no contact_id
        $result =& civicrm_location_get(array('location_type' => 'Main'));
        $this->assertEquals($result['is_error'], 1);
        // location_type an empty array
        $result =& civicrm_location_get(array('contact_id' => $this->_contactId, 'location_type' => array()));
        $this->assertEquals($result['is_error'], 1);
    }

    function testGetProper()
    {
        $proper = array(
            'country_id'             => 1228,
            'county_id'              => 3,
            'state_province_id'      => 1021,
            'supplemental_address_1' => 'Hallmark Ct',
            'supplemental_address_2' => 'Jersey Village',
        );
        $result = civicrm_location_get(array('contact_id' => $this->_contactId));
        foreach ($result as $location) {
            if ( CRM_Utils_Array::value( 'address', $location ) ) {
                foreach ($proper as $field => $value) {
                    $this->assertEquals($location['address'][$field], $value);
                }
            }
        }
    }

    function tearDown()
    {
        $this->contactDelete($this->_contactId);
    }
}

