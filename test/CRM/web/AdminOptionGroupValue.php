<?php

require_once "CommonAPI.php";

class TestofAdminOptionGroupValue extends WebTestCase
{
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    /**
     * common functioning for Option Group test cases.
     * 
     * This function searches 'Administer Link' on the page. Then clicks that link. 
     * Further it searches for link 'Options' and then clicks the link.
     * 
     * @param NULL
     * @return void
     * 
     * @access public
     * 
     */
    function AdminCommon()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }

        $this->assertWantedText("Configure");
        
        if ($this->assertLink('Options')){
            $this->clickLink('Options');                                  
        }
    }
    
    function testAdminAddOptionGroup()
    {
        $this->AdminCommon();
        
        if ($this->assertWantedText("New Option Group")){
            $this->clickLinkById('newOptionGroup');
        }
        
        $this->assertWantedText("New Option Group");
        
        $name        = 'AAA OptionGroup';
        $description = 'used option group';
        $is_active   = '1';       
        
        $this->setFieldById('name', $name);  
        $this->setFieldById('title', $description);
        $this->setField('is_active', $is_active);              
        
        $this->clickSubmitByName("_qf_OptionGroup_next"); 
        
        $this->assertWantedText(" The Option Group \"$name\" has been saved."); 
        
        $this->AdminAddOptionValue($name);
        $this->AdminEditOptionValue();
        $this->AdminEnableDisableOptionValue();
        $this->AdminDeleteOptionValue();
    }
    
    function testAdminEditOptionGroup()
    {
        $this->AdminCommon();
        
        if ($this->assertWantedText("New Option Group")){
            $this->clickLink('Edit Group');
        }
        
        $this->assertWantedText("Edit Option Group");  
        
        $name      = 'AAA OptionGroup';
        $title     = 'half used option group';
        $is_active = '1';        
        
        $this->setField('name',$name);  
        $this->setField('title',$title);
        $this->setField('is_active',$is_active);
        
        $this->clickSubmitByName("_qf_OptionGroup_next");
        $this->assertWantedText( "The Option Group \"$name\" has been saved.");
    }
    
    function testAdminEnableDisableOptionGroup()
    {
        $this->AdminCommon();
        
        if ($this->assertWantedText("New Option Group")){
            $this->clickLink('Disable');
        }
        
        $this->clickLink('Enable');
    }
    
    function testAdminDeleteOptionGroup()
    {
        $this->AdminCommon();
        
        if ($this->assertWantedText("New Option Group")){ 
            $this->clickLink('Delete');
        }
        
        $this->assertWantedText("Delete Option Group");
        $this->clickSubmitByname("_qf_OptionGroup_next");
        $this->assertWantedText(" Selected option group has been deleted.");
    }
    
    //echo "\n ************* Admin Option Value Functions ************* \n";
    
    /**
     * adds option value in option group
     *
     * This function is called after creation of Option Group 
     * The basic Aim of this function is to add Option Value for the created Option Group  
     *
     * @param string $name         name of the option group
     *
     * @return void
     * 
     * @access public
     * 
     */
    function AdminAddOptionValue($groupName)
    {
        if ($this->assertWantedText(" The Option Group \"$groupName\" has been saved.")){
            $this->clickLink('Multiple Choice Options');
        }
        
        if ($this->assertWantedText("There are no Option Value entered.")){
            $this->clickLink('add one');
        }
        
        $this->assertWantedText("New Option Value");
        
        $title       = 'AAA OptionValue';
        $name        = 'AAA OptionValueName';
        $weight      = '1';
        $is_active   = '0';       
        
        $this->setFieldById('title', $title);
        $this->setFieldById('name', $name);  
        $this->setField('weight',$weight);
        $this->setField('is_active', $is_active);              
        
        $this->clickSubmitByName("_qf_OptionValue_next"); 
        $this->assertWantedText(" The Option Value \"$title\" has been saved.");
    }
    
    /**
     * edits option value in option group
     *
     * The basic Aim of this function is to edit Option Value of an Option Group 
     * 
     * @param NULL
     * @return void
     * 
     * @access public
     * 
     */
    function AdminEditOptionValue()
    {
        if ($this->assertWantedText("New Option Value")){
            $this->clickLink('Edit');
        }
        
        $this->assertWantedText("Edit Option Value");  
        
        $title       = 'AAA OptionValue';
        $description = 'half used option value';
        $is_active   = '1';        
        
        $this->setFieldById('title',$title);  
        $this->setFieldById('description',$description);
        $this->setField('is_active',$is_active);
        
        $this->clickSubmitByName("_qf_OptionValue_next");
        $this->assertWantedText( " The Option Value \"$title\" has been saved.");
        
    }
        
    /**
     * enables/disables option value in option group
     *
     * The basic Aim of this function is to enable/disable  Option Value of an Option Group
     * @param NULL
     *
     * @return void
     * 
     * @access public
     * 
     */
    function AdminEnableDisableOptionValue()
    {
        if ($this->assertWantedText("New Option Value")){
            $this->clickLink('Disable');
        }
        
        $this->clickLink('Enable');
    }
    
    /**
     * deletes option value in option group
     *
     * The basic Aim of this function is to delete Option Value of an Option Group
     * @param  NULL
     *
     * @return void
     * 
     * @access public
     * 
     */   
    function AdminDeleteOptionValue()
    {
        if ($this->assertWantedText("New Option Value")){ 
            $this->clickLink('Delete');
        }
        
        $this->assertWantedText("Delete Option Value");
        $this->clickSubmitByname("_qf_OptionValue_next");
        $this->assertWantedText(" Selected option value has been deleted.");
    }
}
?>