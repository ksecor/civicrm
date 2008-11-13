<?php

require_once 'api/crm.php';

class TestOfUpdateUFFieldAPI extends UnitTestCase 
{
    protected $_UFGroup;
    protected $_UFField;
    
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
        $this->_UFGroup = crm_create_uf_group($params);
        $this->assertIsA($this->_UFGroup, 'CRM_Core_DAO_UFGroup');
    }
    
    function testCreateUFField()
    {
        $params = array(
                        'field_name' => 'phone',
                        'location_type_id' => 2,
                        'phone_type' => 'Mobile',
                        'visibility' => 'Public User Pages and Listings',
                        'help_post' => 'This is Phone of Mobile type.',
                        'is_active' => 0,
                        'in_selector' => 1,
                        'weight' => 4
                        );
        $this->_UFField = crm_create_uf_field($this->_UFGroup, $params);
        $this->assertIsA($this->_UFField, 'CRM_Core_DAO_UFField');    
    }
    
    function testUpdateUFFieldError()
    {
        $params = array();
        $UFField = crm_update_uf_field($params, $this->UFField);
        $this->assertIsA($UFField, 'CRM_Core_Error');
    }
    
    function testUpdateUFFieldAddHelp()
    {
        $params = array(
                        'field_type' => 'Individual',
                        'help_post' => 'This is Phone of UF Group \'New Profile Group F02\'',
                        'is_active' => 1,
                        );
       
        $UFField = crm_update_uf_field($params, $this->_UFField);
        $this->assertIsA($UFField, 'CRM_Core_DAO_UFField');
    }
    
    function testUpdateUFField()
    {
        $params = array(
                        'field_name' => 'street_address',
                        'help_post' => 'This is street address',
                        'is_active' => 1,
                        );
        $UFField = crm_update_uf_field($params, $this->_UFField);
        $this->assertIsA($UFField, 'CRM_Core_DAO_UFField');
    }
    
    function testUpdateUFFieldChangeWeight()
    {
        $params = array(
                        'weight' => 6
                        );
        
        $UFField = crm_update_uf_field($params, $this->_UFField);
        $this->assertIsA($UFField, 'CRM_Core_DAO_UFField');
    }
    
    function testDeleteUFField()
    {
        $UFField = crm_delete_uf_field($this->_UFField);
        $this->assertEqual($UFField,true);
    }
    
    function testDeleteUFGroup()
    {
        $UFGroup = crm_delete_uf_group($this->_UFGroup);
        $this->assertEqual($UFGroup,true);
    }
}

