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
        
        $contact_type = '- all contacts -';
        $sort_name    = 'adams';
        $group        = '- any group -';
        $tag          = '- any tag -';
        $this->setField('contact_type', $contact_type);
        $this->setField('sort_name',    $sort_name   );
        $this->setFieldById('group',    $group       );
        $this->setFieldById('tag',      $tag         );
        
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
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        $contact_type = 'Households';
        $sort_name    = '';
        $group        = '- any group -';
        $tag          = '- any tag -';
        
        $this->assertFieldById('group','' ,'Found Group');
        $this->assertFieldById('tag','' ,'Found Tag');
        $this->assertField('sort_name','' ,'Found Sort Name');
        $this->assertField('contact_type','' ,'Found Contact Type');
        
        $this->setField('contact_type', $contact_type);
        $this->setField('sort_name',    $sort_name   );
        //$this->setFieldById('group',    $group       );
        //$this->setFieldById('tag',      $tag         );
        
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
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        $contact_type = '- all contacts -';
        $sort_name    = '';
        $group        = 'Newsletter Subscribers';
        $tag          = '- any tag -';
        $this->setField('contact_type', $contact_type);
        $this->setField('sort_name',    $sort_name   );
        $this->setFieldById('group',    $group       );
        $this->setFieldById('tag',      $tag         );
        
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
        
        $this->assertResponse(200);
        $this->assertWantedText("Search Criteria");
        
        $contact_type = '- all contacts -';
        $sort_name    = '';
        $group        = '- any group -';
        $tag          = 'Major Donor';
        $this->setField('contact_type', $contact_type);
        $this->setField('sort_name',    $sort_name   );
        $this->setFieldById('group',    $group       );
        $this->setFieldById('tag',      $tag         );
        
        $this->clickSubmitByName('_qf_Search_refresh');
        
        if ( $this->assertNoUnwantedText("No matches found")) {
            $this->assertWantedText("Tagged as - Major Donor");
        }
    }
}
?>