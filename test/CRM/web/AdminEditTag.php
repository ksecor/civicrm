<?php

require_once 'api/crm.php';

class TestOfAdminEditTagForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminEditTag()
    {
        // create one simple browser for testing
        $browser = $this->createBrowser();
        $this->setBrowser($browser);
        
        // starting drupal (This needs more work as login part needs to be done in some common file).        
        $url = "http://localhost/drupal";
        $this->get($url);
        
        // username for drupal (need to create Common Const for Username) 
        $this->setFieldById('edit-name', 'Manish');
        // password for drupal (need to create Common Const for Password) 
        $this->setFieldById('edit-pass', 'manish');
        
        $this->clickSubmit('Log in');
        
        // Clicking CiviCRM link
        $this->get('http://localhost/drupal/civicrm');
        
        // Clicking "New Individual" link if it exists on the page.
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        // checking the page title after clicking the "New Individual" link. 
        $this->assertTitle('Administer CiviCRM | CiviCRM');
        
        if ($this->assertLink('Tags (Categories)')) {
            $this->clickLink('Tags (Categories)');
        }
        
        $this->assertTitle('Tags (Categories) | CiviCRM');
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        // checking if fields for First Name and Last Name exists on the page.
        $this->assertFieldById('name' , 'Company');
        $this->assertFieldById('description' , 'For-profit organization.');
        $this->assertField('_qf_Tag_next');
        
        // setting values for the First Name and Last Name Fields.
        $this->setFieldById('name' , 'Company');
        $this->setFieldById('description' , 'For-profit organization. Edited.');
        
        // clicking the submit ("Save") button 
        $this->clickSubmitByName('_qf_Tag_next');
    }
}
?>