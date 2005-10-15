<?php

require_once 'api/crm.php';

class TestOfCreateRelationshipAPI extends UnitTestCase 
{
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testCreateRelationship() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $conatct1 = & new CRM_Contact_DAO_Contact();
        $conatct1->id = 102;
        $conatct2 = & new CRM_Contact_DAO_Contact();
        $conatct2->id = 103;
        $relationShip = 'Child of';
        $rel = crm_create_relationship($conatct1, $conatct2, $relationShip, $params);
       
    }

    function testGetRelationship()
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $conatct = & new CRM_Contact_DAO_Contact();
        $conatct->id = 102;
        $conatct2 = & new CRM_Contact_DAO_Contact();
        $conatct2->id = 103;
        $rel = crm_get_relationships($conatct,$conatct2);
        //print_r($rel);
       
    }
    
    function testDeleteRelationship() 
    {
        
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $conatct1 = & new CRM_Contact_DAO_Contact();
        $conatct1->id = 102;
        $conatct2 = & new CRM_Contact_DAO_Contact();
        $conatct2->id = 103;
        $relationShip = 'Child of';
        $rel = crm_delete_relationship($conatct1,$conatct12,$relationShip);
        
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
        
   }
        
    function testGetRelationType()
    {
        $relationTypes =crm_get_relationship_types();
        //print_r($relationTypes);
    }
    
    

    
}

?>