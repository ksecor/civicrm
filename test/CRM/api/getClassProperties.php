<?php

require_once 'api/crm.php';

class TestOfGetClassProperties extends UnitTestCase 
{
    protected $_individual;
    protected $_houseHold;
    protected $_organization;

    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testGetClassProperties() 
    {
        $prop = crm_get_class_properties("Individual","custom");
        echo "Individual"."\n";
        print_r($prop);
        $prop = crm_get_class_properties("Organization","core");
        echo "Organization"."\n";
        print_r($prop);
        $prop = crm_get_class_properties("Household","core");
        echo "Household"."\n";
        print_r($prop);
        $prop = crm_get_class_properties("Location","core");
        echo "Location"."\n";
        print_r($prop);
        $prop = crm_get_class_properties("Group","core");
        echo "Group"."\n";
        print_r($prop);

    }

    
}
?>