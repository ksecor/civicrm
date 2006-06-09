<?php

require_once 'api/crm.php';

class TestOfCreateCustomGroupAPI extends UnitTestCase 
{
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateGroup()
    {
        $title = 'This is a Demo';
        $params = array(
                        'title'      => $title,
                        'name'       => 'Lobby 123',
                        'desc'       => 'This is desc',
                        'source'     => 'Lobby',
                        'group_type' => 'Static',
                        'is_active'  => 1
                        );
        $class_name = 'Individual';
        $customGroup =& crm_create_group($params);
        print_r($customGroup);
        $this->assertIsA($customGroup, 'CRM_Contact_DAO_Group');
    }
}
?>