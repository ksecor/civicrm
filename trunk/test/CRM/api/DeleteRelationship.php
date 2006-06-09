<?php

require_once 'api/crm.php';

class TestOfDeleteRelationshipAPI extends UnitTestCase 
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
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
       
    }

     function testDeleteRelationshipWithInvalidRelObject() 
    {
         require_once 'CRM/Contact/DAO/Contact.php';
        require_once 'CRM/Contact/DAO/RelationshipType.php';
        $reltype = & new CRM_Contact_DAO_RelationshipType();
        $rel = crm_delete_relationship($this->contact1,$contact1,array($reltype));
        $this->assertNull($rel,'CRM_Core_Error');
    }
    
    function testDeleteRelationship() 
    {
        
        require_once 'CRM/Contact/DAO/Contact.php';
        require_once 'CRM/Contact/DAO/RelationshipType.php';
        $reltype = & new CRM_Contact_DAO_RelationshipType();
        $reltype->id = 1;
        $rel = crm_delete_relationship($this->contact1,$this->contact1,array($reltype));
        $this->assertNull($rel);
    }
    

    function testDeleteContact()
    {
        crm_delete_contact($this->contact1);
        crm_delete_contact($this->contact2);
    }


}
?>