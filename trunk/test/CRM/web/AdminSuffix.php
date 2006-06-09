<?php

require_once "CommonAPI.php";

class TestOfAdminSuffixForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddSuffix()
    {
        //echo "\n ************* Admin Suffix : Add ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Individual Suffixes (Jr, Sr...)')) {
            $this->clickLink('Individual Suffixes (Jr, Sr...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertWantedText("New Individual Suffix Option")) {
            $this->clickLinkById("newIndividualSuffix");
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("New Individual Suffix");
        
        $name = 'New Suffix';
        $weight = '-1';
        
        $this->setFieldById('name', $name);
        $this->setFieldbyId('weight', $weight);
        $this->setField('is_active',1);
        
        $this->clickSubmitByName('_qf_IndividualSuffix_next');
        
        $this->assertResponse(200);
    }
    /*
    function testAdminEditSuffix()
    {
        //echo "\n ************* Admin Suffix : Edit ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Individual Suffixes (Jr, Sr...)')) {
            $this->clickLink('Individual Suffixes (Jr, Sr...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Edit Individual Suffix");
        
        $name = 'New Suffix';
        $weight = '0';
        
        $this->setFieldById('name', $name);
        $this->setFieldbyId('weight', $weight);
        $this->setField('is_active', 1);
                
        $this->clickSubmitByName('_qf_IndividualSuffix_next');
        $this->assertWantedText(" The Individual Suffix \"$name\" has been saved");
        
        $this->assertResponse(200);
    }
    
    function testAdminDeleteSuffix()
    {
        //echo "\n ************* Admin Suffix : Delete ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Setup");
        
        if ($this->assertLink('Individual Suffixes (Jr, Sr...)')) {
            $this->clickLink('Individual Suffixes (Jr, Sr...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Delete Individual Suffix");
        
        $this->clickSubmitByName('_qf_IndividualSuffix_next');
        $this->assertWantedText('Selected Individual Suffix has been deleted.');
        
        $this->assertResponse(200);
    }*/
}
?>