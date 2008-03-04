<?php

require_once 'api/crm.php';

class TestOfCRM878 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM878( )
    {
        //CRM_Core_Error::debug( 'f', $fields );
        $group = crm_get_groups(array('id' => 2));
        echo "<pre>";
        print_r($group);
        echo "</pre>";
        echo "<br /><br />Get the contacts for this group<br />";
        $contacts = crm_get_group_contacts($group[0],
                                           NULL, //default instead
                                           'Added', NULL, //status and sort
                                           0,10000); //up to first 10K records
        echo "Returned " . count($contacts) . " in all<br />"; 
        
    }

}

