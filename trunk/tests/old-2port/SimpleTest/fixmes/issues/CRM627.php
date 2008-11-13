<?php

require_once 'api/crm.php';

class TestOfCRM627 extends UnitTestCase 
{
    protected $_group ;

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
        $groupCreated =& crm_create_group($params);
        CRM_Core_Error::debug('groupCreated',$groupCreated);
        $this->assertIsA($groupCreated, 'CRM_Contact_DAO_Group');
        $this->_group = $groupCreated;
    }

    function testDeleteGroup() 
    {
        $val =& crm_delete_group(& $this->_group);
        $this->assertNull($val);
    }
}

