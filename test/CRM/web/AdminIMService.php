<?php
require_once "CommonAPI.php";

class TestOfAdminIMServiceForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddIMProvider()
    {
        //echo "\n ************* Admin IMProvider : Add ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Instant Messenger Services')) {
            $this->clickLink('Instant Messenger Services');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertWantedText("New IM Service Provider")) {
            $this->clickLinkById("newIMProvider");
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("New IM Service Provider");
        
        $name = 'New IM Provider';
        
        $this->setFieldById('name', $name);
        $this->setField('is_active', 0);
        
        $this->clickSubmitByName('_qf_IMProvider_next');
        
        $this->assertResponse(200);
    }
    /*
    function testAdminEditIMProvider()
    {
        //echo "\n ************* Admin IMProvider : Edit ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Instant Messenger Services')) {
            $this->clickLink('Instant Messenger Services');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Edit IM Service Provider");
        
        $name = 'AIM';
        
        $this->setFieldById('name', $name);
        $this->setField('is_active', 1);
                
        $this->clickSubmitByName('_qf_IMProvider_next');
        $this->assertWantedText("The IM Provider \"$name\" has been saved.");
        
        $this->assertResponse(200);
    }
    
    function testAdminDeleteIMProvider()
    {
        //echo "\n ************* Admin IMProvider : Delete ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Instant Messenger Services')) {
            $this->clickLink('Instant Messenger Services');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Delete IM Service Provider");
        
        $this->clickSubmitByName('_qf_IMProvider_next');
        $this->assertWantedText('Selected IMProvider has been deleted.');
        
        $this->assertResponse(200);
    }*/
}
?>