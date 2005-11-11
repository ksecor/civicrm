<?php

require_once "login.php";

class TestOfAdminAddTagForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddTag()
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
        
        $this->assertWantedText("New Tag");
    }
}
?>