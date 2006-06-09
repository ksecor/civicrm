<?php

require_once "CommonAPI.php";

class TestOfAdminCustomDataForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddPrefix()
    {
        //echo "\n ************* Admin Custom Data Group: Add ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Configure");
        
        if ($this->assertLink('Custom Data')) {
            $this->clickLink('Custom Data');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertWantedText("New Custom Data Group")) {
            $this->clickLinkById("newCustomDataGroup");
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Custom Data Group");
        
        $title    = 'New Custom Group';
        $extends  = 'Individuals';
        $weight   = '-1';
        $style    = 'Tab';
        $helpPre  = 'Enter the Information below';
        $helpPost = 'This is Custom Data Group Created by Web Test.';
        
        $this->setFieldById('title', $title);
        $this->setFieldById('extends', $extends);
        $this->setFieldbyId('weight', $weight);
        $this->setFieldbyId('style', $style);
        $this->setFieldbyId('help_pre', $helpPre);
        $this->setFieldbyId('help_post', $helpPost);
        $this->setField('collapse_display', 0);
        $this->setField('is_active', 1);
        
        $this->clickSubmitByName('_qf_Group_next');
        
        $this->assertResponse(200);
        $this->assertWantedText("Your Group \"$title\" has been added");
        
        //echo "\n ************* Admin Custom Data Field: Add ************* \n";
        
        if ($this->assertLink("add custom fields")) {
            $this->clickLink("add custom fields");
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Custom Data Field");
        
        $label = 'New Field 1.';
        $dataType0 = 'Note';
        $dataType1 = 'Text';
        $weight='-1';
        $default_value = "Default Note";        
        $helpPost = 'This is Custom Data Field Created by Web Test.';
        
        $this->setFieldById('label', $label);
        $this->setFieldById('data_type[0]', $dataType0);
        $this->setFieldById('data_type[1]', $dataType1);
        $this->setFieldbyId('weight', $weight);
        $this->setFieldbyId('default_value', $default_value);
        $this->setFieldbyId('help_post', $helpPost);
        $this->setField('is_required', 0);
        $this->setField('is_searchable', 1);
        $this->setField('is_active', 1);
        
        $this->clickSubmitByName('_qf_Field_next');
        
        $this->assertResponse(200);
        $this->assertWantedText("Your custom field \"$label\" has been saved");
        
    }
}
?>