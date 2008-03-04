<?php

require_once 'api/crm.php';

class TestOfCRM825 extends UnitTestCase 
{
    protected $_individual;
    protected $_houseHold ;
    protected $_group ;

    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    


    function testCreateIndividual() 
    {
        $params = array('first_name'    => 'abc7',
                        'last_name'     => 'xyz7', 
                        'location_type' => 'Main',
                        'im'            => 'manyahoo',
                        'im_provider'   => 'AIM',
                        'phone'         => '999999',
                        'phone_type'    => 'Phone',
                        'email'         => 'man7@yahoo.com'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }
    
    function testCreateHousehold() 
    {
        $params = array('household_name' => 'abc8\'s House',
                        'nick_name'      => 'x House',
                        'email'          => 'man8@yahoo.com',
                        'location_type'  => 'Main'
                        );
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_houseHold = $contact;
    }

    function testCRM825()
    {
        // Get the Contacts to a group.
        $group = new CRM_Contact_DAO_Group();
        $group->id = 2;
        $sort = array("contact_id" => "ASC");
        $returnProperties =array('method','display_name','do_not_email', 'email');
        //$returnProperties = null;
        $contacts = crm_get_group_contacts(&$group, $returnProperties ,'Added',$sort, $offset = 0, $row_count = 25 );
        
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
            echo 'Contact ID: ' . $contact->contact_id . ', Display Name: ' . $contact->display_name . ', Do Not Email: ' . $contact->do_not_email . ', Email: ' . $contact->email . '<br />';
        }
        
        // Create one group
        $groupParams = array(
                             'name'      => 'Trial Group',
                             'title'     => 'Trial Group',
                             'is_active' => 1,
                             );
        $groupCreated = crm_create_group($groupParams);
        $this->assertIsA($groupCreated, 'CRM_Contact_DAO_Group');
        $this->_group = $groupCreated;
        
        //passing wrong params i,e $contacts
        $groupContacts = crm_add_group_contacts($groupCreated, $contacts);
        $this->assertIsA($groupContacts, 'CRM_Core_Error');

        //passing right params i,e $contacts
        $contacts = array($this->_individual,$this->_houseHold );
        $groupContacts = crm_add_group_contacts($groupCreated, $contacts);
        $this->assertNull($groupContacts);

        // now add the previous contacts to the group created
        //$returnProperties = null;
        $sort = array("contact_id" => "DESC");
        $newContacts = crm_get_group_contacts($groupCreated, null ,'Added',$sort, $offset = 0, $row_count = 25 );
        
        foreach($newContacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
            echo 'Contact ID: ' . $contact->contact_id . ', Display Name: ' . $contact->display_name . ', Do Not Email: ' . $contact->do_not_email . ', Email: ' . $contact->email . '<br />';
        }
    }

    function testDeleteGroup() 
    {
        $val =& crm_delete_group(& $this->_group);
        $this->assertNull($val);
    }

    function testDeleteIndividual() 
    {
        $contact = $this->_individual;
        $val =& crm_delete_contact(& $contact,102);
        $this->assertNull($val);
    }
    
    function testDeleteHousehold() 
    {
        $contact = $this->_houseHold;
        $val =& crm_delete_contact(& $contact,102);
        $this->assertNull($val);
    }

}

