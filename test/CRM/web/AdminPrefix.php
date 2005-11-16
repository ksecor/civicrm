<?php

require_once "CommonAPI.php";

class TestOfAdminPrefixForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddPrefix()
    {
        //echo "\n ************* Admin Prefix : Add ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Individual Prefixes (Ms, Mr...)')) {
            $this->clickLink('Individual Prefixes (Ms, Mr...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertWantedText("New Individual Prefix Option")) {
            $this->clickLinkById("newIndividualPrefix");
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("New Individual Prefix Option");
        
        $name = 'New Prefix';
        $weight = '-1';
        
        $this->setFieldById('name', $name);
        $this->setFieldbyId('weight', $weight);
        $this->setField('is_active', 1);
        
        $this->clickSubmitByName('_qf_IndividualPrefix_next');
        
        $this->assertResponse(200);
    }
    /*
    function testAdminEditPrefix()
    {
        //echo "\n ************* Admin Prefix : Edit ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Individual Prefixes (Ms, Mr...)')) {
            $this->clickLink('Individual Prefixes (Ms, Mr...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Edit Individual Prefix Option");
        
        $name = 'New Prefix';
        $weight = '0';
        
        $this->setFieldById('name', $name);
        $this->setFieldbyId('weight', $weight);
        $this->setField('is_active', 1);
                
        $this->clickSubmitByName('_qf_IndividualPrefix_next');
        $this->assertWantedText(" The Individual Prefix \"$name\" has been saved");
        
        $this->assertResponse(200);
    }
    
    function testAdminDeletePrefix()
    {
        //echo "\n ************* Admin Prefix : Delete ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Individual Prefixes (Ms, Mr...)')) {
            $this->clickLink('Individual Prefixes (Ms, Mr...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Delete Individual Prefix Option");
        
        $this->clickSubmitByName('_qf_IndividualPrefix_next');
        $this->assertWantedText('Selected Individual Prefix has been deleted.');
        
        $this->assertResponse(200);
    }*/
}
?>