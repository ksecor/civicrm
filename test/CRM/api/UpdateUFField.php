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
        $UFGroup = crm_create_uf_group($params);
        $this->assertIsA($UFGroup, 'CRM_Core_DAO_UFGroup');
        $this->_UFGroup = $UFGroup;
    }
    
    function testCreateUFField()
    {
        $params = array(
                        'field_name' => 'street_address',
                        'location_type_id' => 2,
                        'visibility' => 'Public User Pages and Listings',
                        'help_post' => 'This is Street Address.',
                        'in_selector' => 1,
                        'weight' => 4
                        );
        $UFField = crm_create_uf_field($this->_UFGroup, $params);
        $this->_UFField =  $UFField;
        $this->assertIsA($UFField, 'CRM_Core_DAO_UFField');    
    }
    
    function testUpdateUFFieldError()
    {
        $params = array();
        $UFField = crm_update_uf_field($params, $this->UFField->id);
        $this->assertIsA($UFField, 'CRM_Core_Error');
    }
    
    function testUpdateUFFieldAddHelp()
    {
        $params = array(
                        'help_post' => 'This is Street Address of Group \'New Profile Group F02\'',
                        );
       
        $UFField = crm_update_uf_field($params, $this->_UFField->id);
        $this->assertIsA($UFField, 'CRM_Core_DAO_UFField');
    }
    
    function testUpdateUFFieldChangeWeight()
    {
        $params = array(
                        'weight' => 6
                        );
        
        $UFField = crm_update_uf_field($params, $this->_UFField->id);
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
?>