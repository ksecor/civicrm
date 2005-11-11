<?php

require_once 'login.php';

class TestOfManageGroupsForm extends WebTestCase 
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
        
        if ($this->assertLink('Manage Groups')) {
            $this->clickLink('Manage Groups');
        }
        
        $this->assertTitle('Manage Groups | '.$_drupalTitle);

        $this->assertLink('New Group');
        $this->clickLink('New Group');
        $this->assertFieldById('title');
        $this->assertFieldById('description');
        $this->assertField('visibility' , 'User and User Admin Only');
        $this->assertField('_qf_Edit_next');
        
        $groupName = 'test group2';
        $description = 'test group2';
        $visibility = 'Public User Pages';
        
        $this->setFieldById('title' , $groupName);
        $this->setFieldById('description' , $description);
        $this->setField('visibility' , $visibility);
        $this->clickSubmit('Continue');
        $this->assertWantedText("The Group \"$groupName\" has been saved.");

        //members        
        if ($this->assertLink('Manage Groups')) {
            $this->clickLink('Manage Groups');
        }

        if ($this->assertLink('Members')) {
            $this->clickLink('Members');
        }
        
        $this->assertTitle('Group Members: Advisory Board | '.$_drupalTitle);

        //  $this->assertLink('&raquo; Add Members to Advisory Board');
    }
        
    function testMembersGroup()
    {
        
    }
    
}
?>