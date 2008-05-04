<?php

require_once 'api/crm.php';

class TestOfCRM652API extends UnitTestCase 
{
    protected $_UFGroup;
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateUFJoin()
    {
        // creating UF Group.
        $params = array(
                        'title'     => 'New Profile Group',
                        'help_pre'  => 'Help For Profile Group',
                        'is_active' => 1
                        );
        $UFGroup = crm_create_uf_group($params);
        $this->assertIsA($UFGroup, 'CRM_Core_DAO_UFGroup');
        
        
        // creating UF Field for the above created group.
        $params = array(
                        'field_name' => 'street_address',
                        'location_type_id' => 2,
                        'visibility' => 'Public User Pages and Listings',
                        'help_post' => 'This is Street Address.',
                        'in_selector' => 1,
                        'is_active' => 1,
                        'weight' => 4
                        );
        $UFField = crm_create_uf_field($UFGroup, $params);
        $this->assertIsA($UFField, 'CRM_Core_DAO_UFField');
        
        
        // adding uf join and giving above group id as uf_group_id
        $params = array('module' => 'New Post',
                        'entity_table' => 'civicrm_contact',
                        'entity_id' => 1,
                        'uf_group_id' => $UFGroup->id
                        );
        $UFJoin = crm_add_uf_join($params);
        $this->assertIsA($UFJoin, 'CRM_Core_DAO_UFJoin');
        $this->assertEqual($UFJoin->module, 'New Post');
        
        
        // editing uf join
        $params = array('weight' => 5,
                        'is_active' => 1
                        );
        $updatedUFJoin = crm_edit_uf_join($UFJoin, $params);
        $this->assertIsA($updatedUFJoin, 'CRM_Core_DAO_UFJoin');
        $this->assertEqual($updatedUFJoin->module, 'New Post');
        $this->assertEqual($updatedUFJoin->weight, 5);
        
        
        // finding the UFJoin.
        $search1 = array('weight' => 5);
        $findJoin1 = crm_find_uf_join_id($search1);
        $this->assertEqual($findJoin1, $updatedUFJoin->id);
        
        $search2 = array('weight' => 5,
                         'entity_table' => 'civicrm_contact',
                         'entity_id' => 1
                         );
        $findJoin2 = crm_find_uf_join_id($search2);
        $this->assertEqual($findJoin2, $updatedUFJoin->id);
        
        $search3 = array('module' => 'New Post');
        $findJoin3 = crm_find_uf_join_id($search3);
        $this->assertIsA($findJoin3, 'CRM_Core_Error');
        
        
        // finding the UF Group Id
        $searchGroup1 = array('weight' => 5);
        $findGroup1 = crm_find_uf_join_UFGroupId($searchGroup1);
        $this->assertEqual($findGroup1, $updatedUFJoin->uf_group_id);
        
        $searchGroup2 = array('weight' => 5,
                              'entity_table' => 'civicrm_contact',
                              'entity_id' => 1
                              );
        $findGroup2 = crm_find_uf_join_UFGroupId($searchGroup2);
        $this->assertEqual($findGroup2, $updatedUFJoin->uf_group_id);
        
        $searchGroup3 = array('module' => 'New Post');
        $findGroup3 = crm_find_uf_join_UFGroupId($searchGroup3);
        $this->assertIsA($findGroup3, 'CRM_Core_Error');
    }
    
    function testValidateHtmlJoin()
    {
        $userID = 19;
        $title = "Contributor Info";
        $register = false;
        $error = crm_validate_profile_html($userID, $title);
        print($error);
    }
}

