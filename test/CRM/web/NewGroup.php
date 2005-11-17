<?php
require_once "CommonAPI.php";

class TestOfAdminEditTagForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testNewGroup()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('New Group')) {
            $this->clickLink('New Group');
        }
        
        $groupName   = 'test group1';
        $description = 'test group1';
        $visibility  = 'Public User Pages';
        
        $this->setFieldById('title' ,       $groupName  );
        $this->setFieldById('description' , $description);
        $this->setField('visibility' ,      $visibility );
        
        $this->clickSubmit('Continue');
        
        $this->assertWantedText("The Group \"$groupName\" has been saved.");

    }
}
?>