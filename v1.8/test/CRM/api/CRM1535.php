<?php

require_once 'api/crm.php';

class TestOfCRM1535 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testSmartGroup( ) {
        $params = array('title' => 'All yahoo contacts' );
        $return_properties = array('member_count', 'id');
        $groups =& crm_get_groups($params, $return_properties);
        CRM_Core_Error::debug( 'g', $groups );
        if ($groups) {
            if ($groups[0]->member_count == 0) {
                $group = array($groups[0]->id => 1);
                $params = array('group' => $group);
                CRM_Core_Error::debug( 'p', $params );
                $result =& crm_contact_search_count($params);
                CRM_Core_Error::debug( 'r', $result );
                if ($result) {
                    $num = $result;
                }
            } else {
                $num = $groups[0]->member_count;
            }
            if ($myPrecision > 0 && $num > 0) {
                $multiplier = pow(10, $myPrecision);
                $num = floor($num / $multiplier) * $multiplier;
            }
            print $num;
        }
    }
}

?>
