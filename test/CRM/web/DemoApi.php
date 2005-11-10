<?php

require_once 'api/crm.php';

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
        $url = "http://localhost/drupal";
        $this->get($url);
        $this->assertResponse(200);

        // $url = "http://192.168.2.9/drupal/";
//         $this->get($url);
//         $this->assertResponse(200);
        
    }
    
}
?>