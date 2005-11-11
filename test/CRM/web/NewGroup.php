<?php

require_once 'login.php';

class TestOfAdminEditTagForm extends WebTestCase 
{  
  
    protected $_drupalTitle;

  function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testNewGroup()
    {
        $_drupalTitle = 'My Drupal';

        $browser = $this->createBrowser();
        $this->setBrowser($browser);
        
        $loginObj =& new login();
        $loginObj->drupalLogin($this);
        
        $this->get('http://' . $loginObj->host . '/' . $loginObj->userFramework . '/civicrm');

        if ($this->assertLink('New Group')) {
            $this->clickLink('New Group');
        }

        $this->assertTitle('Create New Group | '.$_drupalTitle);
        
        $this->assertFieldById('title');
        $this->assertFieldById('description');
        $this->assertField('visibility' , 'User and User Admin Only');
        $this->assertField('_qf_Edit_next');
        
        $groupName = 'test group1';
        $description = 'test group1';
        $visibility = 'Public User Pages';
        
        $this->setFieldById('title' , $groupName);

        $this->setFieldById('description' , $description);

        $this->setField('visibility' , $visibility);
        
        $this->clickSubmit('Continue');

        $this->assertWantedText("The Group \"$groupName\" has been saved.");

    }
}
?>