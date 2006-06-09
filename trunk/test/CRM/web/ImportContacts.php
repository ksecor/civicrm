<?php
require_once "CommonAPI.php";

class TestOfImportContactsForm extends WebTestCase 
{  

    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testImportContacts()
    {
        
        CommonAPI::startCiviCRM($this);
        
        $fileName = '/home/manish/CSV/Individual.csv';
        
        if ($this->assertLink('Import Contacts')) {
            $this->clickLink('Import Contacts');
        }
        
        $this->assertWantedText("Upload Data (step 1 of 4)");
        
        $this->assertFieldById('uploadFile');
        $this->assertField('_qf_UploadFile_upload');
        
        $this->setFieldById("uploadFile" , $fileName);
        echo $this->getField("uploadFile");
        
        $this->clickSubmitByName('_qf_UploadFile_upload');
        
        $this->assertWantedText("Match Fields (step 2 of 4)");
        
        $fieldMap0='First Name';
        $fieldMap1='Middle Name';
        $fieldMap2='Last Name';
                
        $this->setField("mapper[0][0]", $fieldMap0);
        $this->setField("mapper[1][0]", $fieldMap1);
        $this->setField("mapper[2][0]", $fieldMap2);
        
        
        
        $this->clickSubmitByName('_qf_MapField_next');
        $this->assertWantedText("Preview (step 3 of 4)");
        //$this->clickSubmitByName('_qf_Preview_back');
        
        $this->assertField('_qf_Preview_next');
        $this->setField('newGroupName', 'Dhinchak');
        
        $this->clickSubmitByName('_qf_Preview_next');
        $this->assertWantedText("Summary (step 4 of 4)");
        
        $this->assertField('_qf_Summary_next');
        $this->clickSubmitByName('_qf_Summary_next');
        
    }
    
}
?>