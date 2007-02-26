<?php

require_once 'api/v2/Contact.php';

class TestOfContactAPIV2 extends UnitTestCase 
{
    /**
     * Assume empty database with just civicrm_data
     */
    protected $_contacts   = array();
    
    function setUp() 
    {
        // login and get a key
    }
    
    function tearDown() 
    {
    }
    
}

?>