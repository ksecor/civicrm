<?php
require_once "CommonAPI.php";

class TestOfFindContactsForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testFindContactsSortName()
    {
        //echo "\n ************* Find Contacts : Sort Name ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        $sort_name = 'Adams';
        $this->setFieldById('sort_name', $sort_name);
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        $this->assertResponse(200);
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->clickLink("View");
            $this->assertWantedText("Adams");
        }
    }
    
    function testFindContactsHousehold()
    {
        //echo "\n ************* Find Contacts : Household Contacts ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        $contact_type = 'Households';
        $this->setFieldById('contact_type', $contact_type);
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        $this->assertResponse(200);
        if ( ! $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Contact Type - \'Household\'");
        }
    }
    
    function testFindContactsForGroup()
    {
        //echo "\n ************* Find Contacts : Group ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        $group = 'Newsletter Subscribers';
        $this->setFieldById('group', $group);
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        $this->assertResponse(200);
        if ( ! $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Member of Group - Newsletter Subscribers");
        }
    }
    
    function testFindContactsForTag()
    {
        //echo "\n ************* Find Contacts : Tag ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        $tag = 'Major Donor';
        $this->setFieldById('tag', $tag);
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        $this->assertResponse(200);
        if ( ! $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Tagged as - Major Donor");
        }
    }
}
?>