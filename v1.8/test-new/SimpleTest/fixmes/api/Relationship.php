<?php

require_once 'api/crm.php';

class TestOfCreateRelationshipAPI extends UnitTestCase 
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
        //print_r($this->rel);
       
    }

    function testUpdateRelationship() 
    {
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'26','M'=>'9','Y'=>'2009'),'is_active'=>0);
        $this->rel = crm_update_relationship($this->rel ,$params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
    }


    function testGetRelationship()
    {
        require_once 'CRM/Contact/DAO/Contact.php';
       
        $rel = crm_get_relationships($this->contact1,$this->contact2);
  
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
    
    function testCreateRelationType() 
    {
        
        $params = array(
                        'name_a_b'=>'Friend of',
                        'name_b_a'=>'Friend of',
                        'contact_type_a'=>'Individual',
                        'contact_type_b'=>'Individual'
                        );
        $relType = crm_create_relationship_type($params);
        $this->assertIsA($relType, 'CRM_Contact_DAO_RelationshipType');
    }
        
    function testGetRelationType()
    {
        $relationTypes = crm_get_relationship_types();
        foreach($relationTypes as $rel) {
            $this->assertIsA($rel, 'CRM_Contact_DAO_RelationshipType');
            
        }
        //print_r($relationTypes);
    }
    
    

    
}

?>
