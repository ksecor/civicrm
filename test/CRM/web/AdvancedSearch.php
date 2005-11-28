<?php
require_once "CommonAPI.php";

class TestOfAdvancedSearchForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdvancedSearchSortName()
    {
        //echo "\n ************* Advanced Search : Sort Name ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        if ($this->assertLink('Advanced Search')) {
            $this->clickLink('Advanced Search');
        }
        
        $sort_name = 'Adams';
        $this->setFieldById('sort_name', $sort_name);
        
        $this->clickSubmitByName('_qf_Advanced_refresh');
        
        $this->assertResponse(200);
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Name or Email like - \"Adams\"");
        }
    }
    
    function testAdvancedSearchHouseholdAndOrganization()
    {
        //echo "\n ************* Advanced Search : Contacts ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        if ($this->assertLink('Advanced Search')) {
            $this->clickLink('Advanced Search');
        }
        
        $this->setField('contact_type[Individual]',   0);
        $this->setField('contact_type[Household]',    1);
        $this->setField('contact_type[Organization]', 1);
        
        $this->clickSubmitByName('_qf_Advanced_refresh');
        
        $this->assertResponse(200);
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Contact Type - 'Household' or 'Organization'");
        }
    }
    
    function testAdvancedSearchForGroup()
    {
        //echo "\n ************* Advanced Search : Groups ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        if ($this->assertLink('Advanced Search')) {
            $this->clickLink('Advanced Search');
        }
        
        $this->setField('group[1]', 0);
        $this->setField('group[2]', 1);
        $this->setField('group[3]', 1);
        
        $this->clickSubmitByName('_qf_Advanced_refresh');
        
        $this->assertResponse(200);
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Member of Group - Advisory Board or Summer Program Volunteers");
        }
    }
    
    function testAdvancedSearchForTag()
    {
        //echo "\n ************* Advanced Search : Tags ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        if ($this->assertLink('Advanced Search')) {
            $this->clickLink('Advanced Search');
        }
        
        $this->setField('tag[1]', 1);
        $this->setField('tag[2]', 0);
        $this->setField('tag[3]', 0);
        $this->setField('tag[4]', 1);
        $this->setField('tag[5]', 0);
        
        $this->clickSubmitByName('_qf_Advanced_refresh');
        
        $this->assertResponse(200);
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Tagged as - Major Donor or Non-profit");
        }
    }
}
?>