<?php

require_once 'api/crm.php';

class TestOfCRM1584 extends UnitTestCase 
{
    function testCRM1584()
    {
        // Get the Contacts to a group.
        $group = new CRM_Contact_DAO_Group();
        $group->id = 2;
        $sort = array("contact_id" => "ASC");
        $returnProperties =array( 'display_name', 'do_not_email', 'email');

        $contacts = crm_get_group_contacts(&$group, $returnProperties);
        
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
            //CRM_Core_Error::debug( 'c', $contact );
            
            echo 'Contact ID: ' . $contact->contact_id . ', Display Name: ' . $contact->display_name . ', Do Not Email: ' . $contact->do_not_email . ', Email: ' . $contact->email . '<br />';
        }
    }
}

