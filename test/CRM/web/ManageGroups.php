<?php
require_once "CommonAPI.php";

class TestOfManageGroupsForm extends WebTestCase 
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

        $groupName = 'abc4';
        $description = 'abc4......';
        $visibility = 'Public User Pages';
        
        if ($this->assertLink('Manage Groups')) {
            $this->clickLink('Manage Groups');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Manage Groups");

        if ($this->assertWantedText('New Group')) {
            $this->clickLinkById('newGroup');
        }

        $this->assertFieldById('title');
        $this->assertFieldById('description');
        $this->assertField('visibility' , 'User and User Admin Only');
        $this->assertField('_qf_Edit_next');
        
        $this->setFieldById('title' , $groupName);
        $this->setFieldById('description' , $description);
        $this->setField('visibility' , $visibility);
        $this->clickSubmit('Continue');
        $this->assertWantedText("The Group \"$groupName\" has been saved.");

        //print("\nChecked New Group Link ----------------------\n");

    }
        
    function testMembersGroup()
    {

        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Manage Groups')) {
            $this->clickLink('Manage Groups');
        }

        $this->assertResponse(200);
        $this->assertWantedText("Manage Groups");

        if ($this->assertLink('Members')) {
            $this->clickLink('Members');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Group Members:");
        $this->assertWantedText("Find Members within this Group");

        //print("\nChecked 'Members' link -----------------------\n");

    }

    function testSettingsGroup()
    {

        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Manage Groups')) {
            $this->clickLink('Manage Groups');
        }

        $this->assertResponse(200);
        $this->assertWantedText("Manage Groups");

        if ($this->assertLink('Settings')) {
            $this->clickLink('Settings');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Manage Groups");
        $this->assertWantedText("Group Settings");

        //print("\nChecked 'Settings' link -----------------------\n");

    }

    function testDisableGroup()
    {

        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Manage Groups')) {
            $this->clickLink('Manage Groups');
        }

        $this->assertResponse(200);
        $this->assertWantedText("Manage Groups");

        if ($this->assertLink('Disable')) {
            $this->clickLink('Disable');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Manage Groups");

        //print("\nChecked 'Disable' link -----------------------\n");

    }

    function testEnableGroup()
    {

        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Manage Groups')) {
            $this->clickLink('Manage Groups');
        }

        $this->assertResponse(200);
        $this->assertWantedText("Manage Groups");

        if ($this->assertLink('Enable')) {
            $this->clickLink('Enable');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Manage Groups");

        //print("\nChecked 'Enable' link -----------------------\n");

    }
    

    function testDeleteGroup()
    {

        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Manage Groups')) {
            $this->clickLink('Manage Groups');
        }

        $this->assertResponse(200);
        $this->assertWantedText("Manage Groups");

        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        if ($this->assertWantedText("Are you sure you want to delete the group")) {
            $this->clickSubmit('Delete Group');
        }

        $this->assertResponse(200);
        $this->assertWantedText("The Group");
        $this->assertWantedText("has been deleted.");

        //print("\nChecked 'Delete' link -----------------------\n");

    }

}
?>