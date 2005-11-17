<?php
require_once "CommonAPI.php";

class TestOfDemoForm extends WebTestCase 
{
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testTrial( ) 
    {
        CommonAPI::startCiviCRM($this);
        $this->assertWantedText("Find Contacts");
    }
    
}
?>