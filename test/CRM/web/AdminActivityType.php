<?php

require_once "CommonAPI.php";

class TestOfAdminActivityTypeForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddActivityType()
    {
        //echo "\n ************* Admin Activity Types : Add ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Activity Types')) {
            $this->clickLink('Activity Types');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertWantedText("New Activity Type")) {
            $this->clickLinkById("newActivityType");
        }
        
        $this->assertResponse(200);
        
        $name = 'New Activity Type';
        $description = 'This Activity Type Created by Web Test';
        
        $this->setFieldById('name', $name);
        $this->setFieldbyId('description', $description);
        $this->setField('is_active', array('1'));
        
        $this->clickSubmitByName('_qf_ActivityType_next');
        
        $this->assertResponse(200);
    }
    /*
    function testAdminEditActivityType()
    {
        //echo "\n ************* Admin Activity Types : Edit ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Activity Types')) {
            $this->clickLink('Activity Types');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $this->assertResponse(200);
        
        $name = 'Email';
        $description = 'Email Sent...Edited.';
        
        $this->setFieldById('name' , $name);
        $this->setFieldById('description' , $description);
        
        $this->clickSubmitByName('_qf_ActivityType_next');
        $this->assertWantedText(" The tag \"$name\" has been saved.");
        
        $this->assertResponse(200);
    }
    
    function testAdminDeleteActivityType()
    {
        //echo "\n ************* Admin Activity Types : Delete ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Activity Types')) {
            $this->clickLink('Activity Types');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        
        $this->clickSubmitByName('_qf_ActivityType_next');
        $this->assertWantedText('Selected activity type has been Deleted Successfuly.');
        
        $this->assertResponse(200);
    }*/
}
?>