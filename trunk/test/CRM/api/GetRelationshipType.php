<?php

require_once 'api/crm.php';

class TestOfGetRelationshipTypeAPI extends UnitTestCase 
{
    private $rel ="";
    private $contact1 ,$contact2;
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testGetRelationType()
    {
        $relationTypes = crm_get_relationship_types();
        foreach($relationTypes as $rel) {
            $this->assertIsA($rel, 'CRM_Contact_DAO_RelationshipType');
            
        }
     
    }
    
    
}