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


     function testCreateRelationTypeWithError() 
    {
        
        $params = array(
                        'name_b_a'=>'Client of',
                        'contact_type_a'=>'Individual',
                        'contact_type_b'=>'Organization'
                        );
        $relType = crm_create_relationship_type($params);
        $this->assertIsA($relType, 'CRM_Contact_DAO_RelationshipType');
    }

    
}

