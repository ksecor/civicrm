<?php

require_once 'api/crm.php';

class TestOfCRM65API extends UnitTestCase 
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
//         require_once 'api/UFJoin.php';

//         $params = array('id' => 5,
//                         'module'     => 'New Test',
//                         //'help_pre'  => 'Help For Profile Group G04',
//                         'is_active' => 1,
//                         'uf_group_id' => 2
//                         );
//         $UFJoin = crm_add_uf_join($params);
    }

    function testValidateHtmlJoin()
    {
        $userID = 101;
        $title = "Contributor Info";
        $register = false;
        $error = crm_validate_profile_html($userID, $title);
        print($error);
    }
    

}
?>