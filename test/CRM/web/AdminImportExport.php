<?php

require_once "CommonAPI.php";

class TestOfAdminImportExportForm extends  WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    } 

    function AdminCommon()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) { 
            $this->clickLink('Administer CiviCRM');
        }
        
        
        $this->assertWantedText("Configure");
       
        if ($this->assertLink('Import/Export Mapping')) {
            $this->clickLink('Import/Export Mapping');
        }
        
        
    }
                
                
   function testAdminEditImportExportMappings()
    {
        $this->AdminCommon();       
        
        if ( $this->assertWantedText("There are no Saved Mappings.")) {
            echo "No Saved Mappings.<br/>"; 
            return;
        } else {
            echo "<br/><B>Note: </B>Please ignore the error message at the top.<br/>"; 
             
        if ( $this->assertWantedText('Edit')){ 
            $this->clickLink('Edit');
        }
        $this->assertWantedText("Edit Mapping");
             
        $name = 'AAA Changed';
        $description = 'Edited Using Web Testing';
        
        $this->setFieldById('name' , $name);
        $this->setFieldById('description' , $description);
        
        $this->clickSubmitByName('_qf_Mapping_next');
        $this->assertWantedText(" The mapping \"$name\" has been saved.");
         
        }
    }
    
    function testAdminDeleteImportExport()
    {
        
        $this->AdminCommon();
        if ( $this->assertWantedText("There are no Saved Mappings.") ) {
            echo "No Saved Mappings.<br/>"; 
            return;
        } else {
            echo "<br/><B>Note: </B>Please ignore the error message at the top.<br/>"; 

            if ($this->assertLink('Delete')) {
                $this->clickLink('Delete');
            }
        
            $this->assertResponse(200);
            $this->assertWantedText("Delete Mapping");
        
            $this->clickSubmitByName('_qf_Mapping_next');
            $this->assertWantedText('Selected Mapping has been Deleted Successfuly.');
        }
    }
}
?>