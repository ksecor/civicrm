<?php

require_once 'api/crm.php';

class TestOfCRM491 extends UnitTestCase 
{
    protected $CRM;
    
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testCretateContact()
    {
        $values = array('prefix' => 'Mr',
                        'first_name' => 'George',
                        'last_name'  => 'Bush',
                        'postal_code' => '4001',
                        'email'      => uniqid('george').'@cruickshank.biz');
        $this->CRM = crm_create_contact($values);  
        //print_r($this->CRM);
        $this->assertIsA($this->CRM, 'CRM_Contact_BAO_Contact');
    }
    
    function testUpdateContact()
    {
        $values2 = array('prefix' => 'Mrs',
                         'first_name' => 'Hilary',
                         'last_name'  => 'Clinton',
                         'postal_code'=> '4003',
                         'email'      => 'hilary@cruickshank.biz');

        $this->CRM = crm_update_contact($this->CRM,$values2);
        $this->assertIsA($this->CRM, 'CRM_Contact_BAO_Contact');
        // print_r($this->CRM);
    }

    function testDelete()
    {
        crm_delete_contact($this->CRM,102);
    }
}


