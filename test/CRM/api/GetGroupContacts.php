<?php

require_once 'api/crm.php';

class TestOfGetGroupContacts extends UnitTestCase 
{

    
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testGetGroupContacts()
    {
        
        $group = new CRM_Contact_DAO_Group();
        $group->id = 1;
        $contacts = crm_get_group_contacts(&$group);
        $this->assertNotA($contacts,'CRM_Core_Error');
        $queryString ="SELECT * FROM crm_contact RIGHT JOIN  crm_group_contact ON (crm_contact.id =crm_group_contact.contact_id ) WHERE crm_group_contact.status = 'In' AND crm_group_contact.group_id = '1' LIMIT 0,25";

        $crmDAO =& new CRM_Core_DAO();
       
        $crmDAO->query($queryString);
        
        $contactArray = array();
        while($crmDAO->fetch()) { 
            $contactArray[$crmDAO->contact_id]['id'] = $crmDAO->id;
            $contactArray[$crmDAO->contact_id]['domain_id'] = $crmDAO->domain_id;
            $contactArray[$crmDAO->contact_id]['contact_type'] = $crmDAO->contact_type;
            $contactArray[$crmDAO->contact_id]['legal_identifier'] = $crmDAO->legal_identifier;
            $contactArray[$crmDAO->contact_id]['external_identifier'] = $crmDAO->external_identifier;
            $contactArray[$crmDAO->contact_id]['sort_name'] = $crmDAO->sort_name;
            $contactArray[$crmDAO->contact_id]['display_name'] = $crmDAO->display_name;
            $contactArray[$crmDAO->contact_id]['home_URL'] = $crmDAO->home_URL ;
            $contactArray[$crmDAO->contact_id]['image_URL'] = $crmDAO->image_URL ;
            $contactArray[$crmDAO->contact_id]['source'] = $crmDAO->source;
            $contactArray[$crmDAO->contact_id]['preferred_communication_method'] = $crmDAO->preferred_communication_method;
            $contactArray[$crmDAO->contact_id]['preferred_mail_format'] = $crmDAO->preferred_mail_format;
            $contactArray[$crmDAO->contact_id]['do_not_phone'] = $crmDAO->do_not_phone;
            $contactArray[$crmDAO->contact_id]['do_not_email'] = $crmDAO->do_not_email;
            $contactArray[$crmDAO->contact_id]['do_not_mail'] = $crmDAO->do_not_mail;
            $contactArray[$crmDAO->contact_id]['do_not_trade'] = $crmDAO->do_not_trade;
            $contactArray[$crmDAO->contact_id]['hash'] = $crmDAO->hash;
            $contactArray[$crmDAO->contact_id]['group_id'] = $crmDAO->group_id;
            $contactArray[$crmDAO->contact_id]['contact_id'] = $crmDAO->contact_id;
            $contactArray[$crmDAO->contact_id]['status'] = $crmDAO->status;
            $contactArray[$crmDAO->contact_id]['pending_date'] = $crmDAO->pending_date;
            $contactArray[$crmDAO->contact_id]['in_date'] = $crmDAO->in_date;
            $contactArray[$crmDAO->contact_id]['out_date'] = $crmDAO->out_date;
            $contactArray[$crmDAO->contact_id]['pending_method'] = $crmDAO->pending_method;
            $contactArray[$crmDAO->contact_id]['in_method'] = $crmDAO->in_method;
            $contactArray[$crmDAO->contact_id]['out_method'] = $crmDAO->out_method;
        }

        $this->assertEqual($contactArray,$contacts);
        
    }


    function testGetGroupContactsWithFilter()
    {
        $group = new CRM_Contact_DAO_Group();
        $group->id = 2;
        $sort = array("last_name" => "DESC", "first_name" => "DESC");
        $returnProperties =array('contact_id','status','in_date');
        $contacts = crm_get_group_contacts(&$group, $returnProperties, $status = 'In', $sort = null, $offset = 0, $row_count = 25 );
        $this->assertNotA($contacts,'CRM_Core_Error');
        $queryString = "SELECT* FROM crm_contact RIGHT JOIN  crm_group_contact ON (crm_contact.id =crm_group_contact.contact_id ) WHERE crm_group_contact.status = 'In' AND crm_group_contact.group_id = '2'  LIMIT 0,25";
        $crmDAO =& new CRM_Core_DAO();
       
        $crmDAO->query($queryString);
        while($crmDAO->fetch()) { 
            foreach($returnProperties as $retProp) {
                $contactArray[$crmDAO->contact_id][$retProp]=$crmDAO->$retProp; 
            }
        }
        $this->assertEqual($contactArray,$contacts);   
    }
}
?>