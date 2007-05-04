<?php

require_once 'api/crm.php';

class TestOfCRM272 extends UnitTestCase 
{
    protected $_individual;

    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }
    
    function testCreateIndividual() 
    {
        $params = array('first_name'    => 'kurund',
                        'last_name'     => 'jalmi_4', 
                        'im'            => 'kurundssyahoo', 
                        'im_provider'   => 'AIM',
                        'phone'         => '999999', 
                        'phone_type'    => 'Phone', 
                        'email'         => 'kurund@yahoo.com',
                        'city'          => 'mumbai'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }
    
    function testGetContactIndividualByContactID() 
    {
        $params = array('contact_id' => $this->_individual->id);
        $contact =& crm_get_contact($params);
        CRM_Core_Error::debug( 'c', $contact );
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->id, $this->_individual->id);
        $this->assertEqual($contact->location[1]->phone[1]->phone, '999999');
        $this->assertEqual($contact->location[1]->email[1]->email, 'kurund@yahoo.com');
    }
    
    /***
    function testDeleteIndividual()
    {
        $contact = $this->_individual;
        $val =& crm_delete_contact($contact);
        $this->assertNull($val);
    }
    ***/

}

?>
