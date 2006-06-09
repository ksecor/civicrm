<?php

require_once "CommonAPI.php";

class TestOfAdminLocationTypeForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddLocationType()
    {
        //echo "\n ************* Admin Location Types : Add ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Configure");
        
        if ($this->assertLink('Location Types (Home, Work...)')) {
            $this->clickLink('Location Types (Home, Work...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertWantedText("New Location Type")) {
            $this->clickLinkById("newLocationType");
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("New Location Type");
        
        $name = 'New Location Type';
        $description = 'This Location Type is Created by Web Test';
        
        $this->setFieldById('name', $name);
        $this->setFieldbyId('description', $description);
        $this->setField('is_active', 0);
        $this->setField('is_default', 0);
        
        $this->clickSubmitByName('_qf_LocationType_next');
        
        $this->assertResponse(200);
    }
    /*
    function testAdminEditLocationType()
    {
        //echo "\n ************* Admin Location Types : Edit ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Configure");
        
        if ($this->assertLink('Location Types (Home, Work...)')) {
            $this->clickLink('Location Types (Home, Work...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Edit Location Type");
        
        $name = 'Home';
        $description = 'Place of residence...Edited.';
        
        $this->setFieldById('name' , $name);
        $this->setFieldById('description' , $description);
        $this->setField('is_active', 0);
        $this->setField('is_default', 0);
        
        $this->clickSubmitByName('_qf_LocationType_next');
        $this->assertWantedText(" The location type \"$name\" has been saved.");
        
        $this->assertResponse(200);
    }
    
    function testAdminDeleteLocationType()
    {
        //echo "\n ************* Admin Location Types : Delete ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Configure");
        
        if ($this->assertLink('Location Types (Home, Work...)')) {
            $this->clickLink('Location Types (Home, Work...)');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Delete Location Type");
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        
        $this->clickSubmitByName('_qf_LocationType_next');
        $this->assertWantedText(' Selected Location type has been deleted.');
        
        $this->assertResponse(200);
    }*/
}
?>