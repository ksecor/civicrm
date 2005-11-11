<?php
require_once "common.php";

class login extends WebTestCase 
{
    
    public $host;
    public $userFramework;
    public $userFrameworkUsername;
    public $userFrameworkPassword;
    
    function __construct()
    {
        if (defined('HOST')) {
            $this->host                  = HOST;
        }
        
        if (defined('CMS')) {
            $this->userFramework         = CMS;
        }
        
        if (defined('USERNAME')) {
            $this->userFrameworkUsername = USERNAME;
        }
        
        if (defined('PASSWORD')) {
            $this->userFrameworkPassword = PASSWORD;
        }
    }
    
    function drupalLogin($test)
    {
        // starting drupal.        
        $url = 'http://' . $this->host . '/' . $this->userFramework;
        $test->get($url);
        
        // username for drupal.
        $test->setFieldById('edit-name', $this->userFrameworkUsername);
        // password for drupal.
        $test->setFieldById('edit-pass', $this->userFrameworkPassword);
        
        $test->clickSubmit('Log in');
    }
}
?>