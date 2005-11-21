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
        /*
        CommonAPI::startCiviCRM($this);
        
        $fileName = '/home/deepak/rough/import1.csv';
        
        if ($this->assertLink('Import Contacts')) {
            $this->clickLink('Import Contacts');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Import Contacts");
        $this->assertWantedText("Upload Data (step 1 of 4)");
        $this->assertWantedText("Import Data File");
        
        $this->assertFieldById('uploadFile');
        $this->assertField('_qf_UploadFile_upload');
        
        $this->setFieldById('uploadFile' , $fileName);
        
        $this->clickSubmitByName('_qf_UploadFile_upload');
        
        print("\nChecked UpLoad-Data Page ----------------------\n");
        
        $this->assertResponse(200);
        $this->assertWantedText("Match Fields (step 2 of 4)");
        
        $fieldMap1='- do not import -';
        $fieldMap2='First Name';
        $fieldMap3='Last Name';
        $fieldMap4='Email';
        $fieldMap5='Phone';
        $fieldMap6='Country';
        $fieldMap7='City';
        
        $this->setField('mapper[0][0]', $fieldMap1);
        $this->setField('mapper[1][0]', $fieldMap2);
        $this->setField('mapper[2][0]', $fieldMap3);
        $this->setField('mapper[3][0]', $fieldMap4);
        $this->setField('mapper[4][0]', $fieldMap5);
        $this->setField('mapper[5][0]', $fieldMap6);
        $this->setField('mapper[6][0]', $fieldMap7);
        
        
        
        $this->clickSubmitByName('_qf_MapField_next');
        $this->assertWantedText("Preview (step 3 of 4)");
        
        
        print("\nChecked Preview Page ----------------------\n");
        
        
        $this->assertField('_qf_Preview_next');
        $this->clickSubmitByName('_qf_Preview_next');
        
        $this->assertWantedText("Import");
        $this->assertField('_qf_Summary_next');
        
        */
    }
    
}
?>