<?php
require_once "CommonAPI.php";

class TestOfAdminPhoneProviderForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddMobileProvider()
    {
        //echo "\n ************* Admin Mobile Phone Provider : Add ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Mobile Phone Providers')) {
            $this->clickLink('Mobile Phone Providers');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertWantedText("New Mobile Phone Provider")) {
            $this->clickLinkById("newMobileProvider");
        }
        
        $this->assertResponse(200);
        
        $this->assertWantedText("New Mobile Provider");
        
        $name = 'New Mobile Provider';
        
        $this->setFieldById('name', $name);
        $this->setField('is_active', 0);
        
        $this->clickSubmitByName('_qf_MobileProvider_next');
        
        $this->assertResponse(200);
    }
    /*
    function testAdminEditMobileProvider()
    {
        //echo "\n ************* Admin MobileProvider : Edit ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($test->assertLink('Administer CiviCRM')) {
            $test->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Mobile Phone Providers')) {
            $this->clickLink('Mobile Phone Providers');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $this->assertResponse(200);
        
        $name = 'Cingular';
        
        $this->setFieldById('name', $name);
        $this->setField('is_active', 0);
                
        $this->assertWantedText("Edit Mobile Provider");
        $this->clickSubmitByName('_qf_MobileProvider_next');
        $this->assertWantedText("The Mobile Provider \"$name\" has been saved.");
        
        $this->assertResponse(200);
    }
    
    function testAdminDeleteMobileProvider()
    {
        //echo "\n ************* Admin MobileProvider : Delete ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Mobile Phone Providers')) {
            $this->clickLink('Mobile Phone Providers');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        
        $this->assertWantedText("Delete Mobile Provider");
        $this->clickSubmitByName('_qf_MobileProvider_next');
        $this->assertWantedText(' Selected Mobile Provider has been deleted.');
        
        $this->assertResponse(200);
    }*/
}
?>