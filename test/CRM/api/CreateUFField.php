<?php

require_once 'api/crm.php';

class TestOfCreateUFGroupAPI extends UnitTestCase 
{
    protected $_UFGroup;
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateUFGroup()
    {
        $params = array(
                        'title'     => 'New Profile Group F02',
                        'help_pre'  => 'Help For Profile Group F02',
                        'is_active' => 1
                        );
        $UFGroup = crm_create_uf_group($params);
        $this->assertIsA($UFGroup, 'CRM_Core_BAO_UFGroup');
        $this->_UFGroup = $UFGroup;
    }
    
    function testCreateUFFieldError()
    {
        $params = array();
        $UFField = crm_create_uf_field($this->_UFGroup, $params);
        $this->assertIsA($UFGroup, 'CRM_Core_Error');
    }
    
    function testCreateUFField()
    {
        $params = array(
                        'field_name' => 'first_name',
                        'visibility' => 'Public User Pages and Listings',
                        );
        $UFField = crm_create_uf_field($this->_UFGroup, $params);
        $this->assertIsA($UFGroup, 'CRM_Core_BAO_UFField');
    }
}
?>