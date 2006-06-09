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
        
        $this->assertWantedText("Search Criteria");
        
        $contact_type = '- all contacts -';
        $sort_name    = 'adams';
        $group        = '- any group -';
        $tag          = '- any tag -';
        $this->setFieldById('contact_type', $contact_type);
        $this->setFieldById('sort_name',    $sort_name   );
        $this->setFieldById('group',        $group       );
        $this->setFieldById('tag',          $tag         );
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Name or Email like - \"$sort_name\"");
        }
    }
    
    function testFindContactsHousehold()
    {
        //echo "\n ************* Find Contacts : Household Contacts ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertWantedText("Search Criteria");
        
        $contact_type = 'Households';
        $sort_name    = '';
        $group        = '- any group -';
        $tag          = '- any tag -';
        
        $this->setFieldById('contact_type', $contact_type);
        $this->setFieldById('sort_name',    $sort_name   );
        $this->setFieldById('group',        $group       );
        $this->setFieldById('tag',          $tag         );
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Contact Type - 'Household'");
        }
    }
    
    function testFindContactsForGroup()
    {
        //echo "\n ************* Find Contacts : Group ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Find Contacts')) {
            $this->clickLink('Find Contacts');
        }
        
        $this->assertWantedText("Search Criteria");
        
        $contact_type = '- all contacts -';
        $sort_name    = '';
        $group        = 'Newsletter Subscribers';
        $tag          = '- any tag -';
        $this->setFieldById('contact_type', $contact_type);
        $this->setFieldById('sort_name',    $sort_name   );
        $this->setFieldById('group',        $group       );
        $this->setFieldById('tag',          $tag         );
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        if ( $this->assertNoUnwantedText("No matches found")) {
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
        
        $this->assertWantedText("Search Criteria");
        
        $contact_type = '- all contacts -';
        $sort_name    = '';
        $group        = '- any group -';
        $tag          = 'Major Donor';
        $this->setFieldById('contact_type', $contact_type);
        $this->setFieldById('sort_name',    $sort_name   );
        $this->setFieldById('group',        $group       );
        $this->setFieldById('tag',          $tag         );
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Tagged as - Major Donor");
        }
    }
}
?>