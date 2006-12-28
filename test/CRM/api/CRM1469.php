<?php

require_once 'api/crm.php';

class TestOfGetRelationshipAPI extends UnitTestCase 
{
    private $rel ="";
    private $contact1 ,$contact2;
    function setUp() 
    {
    }

    function tearDown() 
    {
    }
    
    function testCreateContacts() 
    {
        $params = array('first_name'    => 'abc4',
                        'last_name'     => 'xyz4',
                        'email'         => 'man4@yahoo.com',
                        );
        $this->contact1 =& crm_create_contact($params, 'Individual');

        $params = array('first_name'    => 'abc5',
                        'last_name'     => 'xyz5',
                        'email'         => 'man5@yahoo.com',
                        );
        $this->contact2 =& crm_create_contact($params, 'Individual');
   
    }

    function testCreateRelationship() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $relationShip = 'Child of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact2, $relationShip, $params);
        $relationShip = 'Sibling of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact2, $relationShip, $params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
       
    }


    function testGetRelationship()
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $relationship_type_name = array('Employee of');
        $rel = crm_get_relationships($this->contact1,$this->contact2, $relationship_type_name);
        CRM_Core_Error::debug('Relation',$rel);
    }


    function testGetRelationshipWithError()
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        
        $rel = crm_get_relationships($contact1);
       
        
        $this->assertIsA($rel,'CRM_Core_Error');
        
    }

    function testDeleteContact()
    {
        crm_delete_contact($this->contact1,102);
        crm_delete_contact($this->contact2,102);
    }


}
?>