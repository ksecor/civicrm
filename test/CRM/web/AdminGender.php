<?php

require_once "CommonAPI.php";

class TestOfAdminGenderForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddGender()
    {
        //echo "\n ************* Admin Gender : Add ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Gender Options (Male, Female...)')) {
            $this->clickLink('Gender Options (Male, Female...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertWantedText("New Gender Option")) {
            $this->clickLinkById("newGender");
        }
        
        $this->assertResponse(200);
        
        $name = 'New Gender';
        $weight = '-1';
        
        $this->setFieldById('name', $name);
        $this->setFieldbyId('weight', $weight);
        $this->setField('is_active', array('1'));
        
        $this->clickSubmitByName('_qf_Gender_next');
        
        $this->assertResponse(200);
    }
    
    function testAdminEditGender()
    {
        //echo "\n ************* Admin Gender : Edit ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Gender Options (Male, Female...)')) {
            $this->clickLink('Gender Options (Male, Female...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $this->assertResponse(200);
        
        $name = 'New Gender';
        $weight = '0';
        
        $this->setFieldById('name', $name);
        $this->setFieldbyId('weight', $weight);
        $this->setField('is_active', array('1'));
                
        $this->clickSubmitByName('_qf_Gender_next');
        $this->assertWantedText("The Gender \"$name\" has been saved.");
        
        $this->assertResponse(200);
    }
    
    function testAdminDeleteGender()
    {
        //echo "\n ************* Admin Gender : Delete ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Gender Options (Male, Female...)')) {
            $this->clickLink('Gender Options (Male, Female...)');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        
        $this->clickSubmitByName('_qf_Gender_next');
        $this->assertWantedText(' Selected Gender type has been deleted.');
        
        $this->assertResponse(200);
    }
}
?>