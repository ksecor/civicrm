<?php
require_once "CommonAPI.php";

class TestOfQuickAddForm extends WebTestCase 
{
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testQuickAdd( ) 
    {
        CommonAPI::startCiviCRM($this);
        if ($this->assertWantedText("New Individual")) {
            
            $first_name = 'Preeti';
            $last_name  = 'Bhamare';
            $email      = 'preeti@yahoo.co.in';
            
            $this->setFieldById('qa_first_name', $first_name);
            $this->setFieldbyId('qa_last_name', $last_name  );
            $this->setFieldbyId('qa_email',     $email      );
            
            $this->clickSubmitByName('_qf_Edit_next');
            $this->assertWantedText("Your Individual contact record has been saved.");
        }
    }
    
}
?>