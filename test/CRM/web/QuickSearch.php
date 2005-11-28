<?php
require_once "CommonAPI.php";

class TestOfQuickSearchForm extends WebTestCase 
{
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testQuickSearchSortName()
    {
        //echo "\n ************* Quick Search : Sort Name ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        $this->assertWantedText("Contact Search");
        
        $contact_type = '- all contacts -';
        $sort_name    = 'adams';
        
        $this->setField('contact_type', $contact_type);
        $this->setField('sort_name',    $sort_name   );
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Name or Email like - \"$sort_name\"");
        }
    }
    
    function testQuickSearchHousehold()
    {
        //echo "\n ************* Quick Search : Household Contacts ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        $this->assertWantedText("Search Criteria");
        
        $contact_type = 'Households';
        $sort_name    = '';
        
        $this->setField('contact_type', $contact_type);
        $this->setField('sort_name',    $sort_name   );
                
        $this->clickSubmitByName('_qf_Search_refresh');
        
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Contact Type - 'Household'");
        }
    }
}
?>