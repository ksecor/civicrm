<?php

require_once "login.php";

class TestOfAdminEditTagForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminEditTag()
    {
        $browser = $this->createBrowser();
        $this->setBrowser($browser);
        
        $loginObj =& new login();
        $loginObj->drupalLogin($this);
        
        $this->get('http://' . $loginObj->host . '/' . $loginObj->userFramework . '/civicrm');
               
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertTitle('Administer CiviCRM | CiviCRM');
        
        if ($this->assertLink('Tags (Categories)')) {
            $this->clickLink('Tags (Categories)');
        }
        
        $this->assertTitle('Tags (Categories) | CiviCRM');
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $name = 'Company';
        $description = 'For-profit organization. Edited.';
        
        $this->setFieldById('name' , $name);
        $this->setFieldById('description' , $description);
        
        $this->clickSubmitByName('_qf_Tag_next');
        
        $this->assertWantedText(" The tag \"$name\" has been saved.");
    }
}
?>