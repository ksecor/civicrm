<?php

require_once 'api/crm.php';

class TestOfCreateUFFieldAPI extends UnitTestCase 
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
                        'title'     => 'New Profile Group F01',
                        'help_pre'  => 'Help For Profile Group F01',
                        'is_active' => 1
                        );
        $this->_UFGroup = crm_create_uf_group($params);
        $this->assertIsA($this->_UFGroup, 'CRM_Core_DAO_UFGroup');
    }
    
    function testCreateUFFieldError()
    {
        $params = array();
        $UFField = crm_create_uf_field($this->_UFGroup, $params);
        $this->assertIsA($UFField , 'CRM_Core_Error');
    }
    
    function testCreateUFFieldWeightError()
    {
        $params = array(
                        'field_name' => 'middle_name',
                        'location_type_id' => 1,
                        'visibility' => 'Public User Pages and Listings',
                        'help_post' => 'This is Middle Name.'
                        );
        $UFField = crm_create_uf_field($this->_UFGroup, $params);
        $this->assertIsA($UFField, 'CRM_Core_Error');
    }
    
    function testCreateUFField1()
    {
        $params = array(
                        'field_name' => 'street_address',
                        'location_type_id' => 2,
                        'visibility' => 'Public User Pages and Listings',
                        'help_post' => 'This is Street Address.',
                        'in_selector' => 1,
                        'is_active' => 1,
                        'weight' => 4
                        );
        $UFField = crm_create_uf_field($this->_UFGroup, $params);
        $this->_UFField[$UFField->id] =  $UFField;
        $this->assertIsA($UFField, 'CRM_Core_DAO_UFField');
    }
    
    function testCreateUFField2()
    {
        $params = array(
                        'field_name' => 'phone',
                        'location_type_id' => 3,
                        'phone_type' => 'Mobile',
                        'visibility' => 'User and User Admin Only',
                        'help_post' => 'This is Phone of Mobile Type.',
                        'is_active' => 1,
                        'in_selector' => 1,
                        'weight' => 5
                        );
        $UFField = crm_create_uf_field($this->_UFGroup, $params);
        $this->_UFField[$UFField->id] =  $UFField;
        $this->assertIsA($UFField, 'CRM_Core_DAO_UFField');
    }
    
    function testDeleteUFField()
    {
        foreach ($this->_UFField as $id => $field) {
            $UFField = crm_delete_uf_field($field);
            $this->assertEqual($UFField,true);
        }
    }
    
    function testDeleteUFGroup()
    {
        $UFGroup = crm_delete_uf_group($this->_UFGroup);
        $this->assertEqual($UFGroup,true);
    }
}

