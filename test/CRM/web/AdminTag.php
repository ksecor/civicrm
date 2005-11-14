<?php

require_once "CommonAPI.php";

class TestOfAdminTagForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddTag()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Tags (Categories)')) {
            $this->clickLink('Tags (Categories)');
        }
        
        $this->assertResponse(200);
        
        $this->assertWantedText("New Tag");
        
        $this->clickLinkById("newTag");
        
        $this->assertResponse(200);
        
        $name = 'New Tag';
        $description = 'This is Tag Created by Web Test';
        
        $this->setFieldById('name', $name);
        $this->setFieldbyId('description', $description);
        
        $this->clickSubmitByName('_qf_Tag_next');
        
        $this->assertResponse(200);
    }
    /*
    function testAdminEditTag()
    {
        //echo "\n ************* Admin Tags(Categories) : Edit ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Tags (Categories)')) {
            $this->clickLink('Tags (Categories)');
        }
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $this->assertResponse(200);
        
        $name = 'Company';
        $description = 'For-profit organization. Edited.';
        
        $this->setFieldById('name' , $name);
        $this->setFieldById('description' , $description);
        
        $this->clickSubmitByName('_qf_Tag_next');
        $this->assertWantedText(" The tag \"$name\" has been saved.");
        
        $this->assertResponse(200);
    }
    
    function testAdminDeleteTag()
    {
        //echo "\n ************* Admin Tags(Categories) : Delete ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Tags (Categories)')) {
            $this->clickLink('Tags (Categories)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        
        $this->clickSubmitByName('_qf_Tag_next');
        $this->assertWantedText('Selected Tag has been Deleted Successfuly.');
        
        $this->assertResponse(200);
    }*/
}
?>