<?php

require_once 'api/crm.php';

class TestOfGetGroups extends UnitTestCase 
{

    
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testGetAllGroups()
    {
        $group = crm_get_groups();
        $this->assertNotA($group,'CRM_Core_Error');
        $queryString = "SELECT * FROM crm_group";
        $crmDAO =& new CRM_Contact_DAO_Group();
        $error = $crmDAO->query($queryString);
        while($crmDAO->fetch()) { 
            $rows = array();
            CRM_Core_DAO::storeValues($crmDAO,$rows);
            $groupArray[] = $rows;
            
        }
        $this->assertEqual($group,$groupArray);
        
    }

    function testGetFilterdGroup()
    {
        $params = array('name'=>'summer');
        $return_prop = array('name','title','group_type');
        
        $groups = crm_get_groups($params,$return_prop);
        
       
        $this->assertNotA($group,'CRM_Core_Error');
        
        $queryString = "SELECT id,name,title,group_type FROM crm_group WHERE name LIKE '%summer%'";
        $crmDAO =& new CRM_Contact_DAO_Group();
        $error = $crmDAO->query($queryString);
        while($crmDAO->fetch()) { 
            $rows = array();
            CRM_Core_DAO::storeValues($crmDAO,$rows);
            $groupArray[] = $rows;

        }
        
        $this->assertEqual($groups,$groupArray);
       
        
    }




}
?>