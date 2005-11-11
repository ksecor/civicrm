<?php

require_once "common.php";

class TestOfAdminDeleteTagForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminDeleteTag()
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
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->clickSubmitByName('_qf_Tag_next');
        
        $this->assertWantedText('Selected Tag has been Deleted Successfuly.');
    }
}
?>