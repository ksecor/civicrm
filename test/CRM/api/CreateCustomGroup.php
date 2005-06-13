<?php

require_once 'api/crm.php';

class TestOfCreateCustomGroupAPI extends UnitTestCase {

    function setUp( ) {
    }

    function tearDown( ) {
    }

    function testCreateNoParam()
    {
        $params = array();
        $customGroup =& crm_create_custom_group($params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
        CRM_Core_Error::debug_var('customGroup', $customGroup);

    }

    function testCreate1()
    {
        $params = array('domain_id' => 1);
        $customGroup =& crm_create_custom_group($params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
        CRM_Core_Error::debug_var('customGroup', $customGroup);

        $params = array('weight' => 3);
        $customGroup =& crm_create_custom_group($params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
        CRM_Core_Error::debug_var('customGroup', $customGroup);

        $params = array('domain_id' => 1, 'weight' => 3);
        $customGroup =& crm_create_custom_group($params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
        CRM_Core_Error::debug_var('customGroup', $customGroup);
    }
}
?>