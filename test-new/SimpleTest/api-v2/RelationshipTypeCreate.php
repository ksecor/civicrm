<?php

require_once 'api/v2/Relationship.php';
/**
 * Class contains api test cases for "civicrm_relationship_type"
 *
 */
class TestOfRelationshipTypeCreateAPIV2 extends CiviUnitTestCase 
{

    function setUp( ) 
    {

    }

    function tearDown( ) 
    {
    }
    /**
     * check with empty array
     */    
    function testRelationshipTypeCreateEmpty( )
    {
        $params = array( );        
        $result =& civicrm_relationship_type_add( $params );

        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'No input parameters present' );
    }
    
    /**
     * check with No array
     */
    function testRelationshipTypeCreateParamsNotArray( )
    {
        $params = 'name_a_b = Employee of';                            
        $result =& civicrm_relationship_type_add( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Parameter is not an array' );
    }

    /**
     * check with no name
     */
    function testRelationshipTypeCreateWithoutName( )
    {
        $relTypeParams = array(
                               'name_b_a'       => 'Test 2',
                               'contact_type_a' => 'Individual',
                               'contact_type_b' => 'Organization'
                               );

        $result = & civicrm_relationship_type_add( $relTypeParams );
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Missing required parameters' );
    }
       
    /**
     * check with no contact type
     */
    function testRelationshipTypeCreateWithoutcontactType( )
    {
        $relTypeParams = array(
                               'name_a_b' => 'Relation 1',
                               'name_b_a' => 'Relation 2'
                               );
        
        $result = & civicrm_relationship_type_add( $relTypeParams );  

        $this->assertEqual( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );
   
        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_RelationshipType', $result['id'],  $relTypeParams ); 
        $this->relationshipTypeDelete( $result['id'] );
    }

    /**
     * create relationship type
     */
    function testRelationshipTypeCreate( )
    {
        $relTypeParams = array(
                               'name_a_b'       => 'Relation 1',
                               'name_b_a'       => 'Relation 2',
                               'contact_type_a' => 'Individual',
                               'contact_type_b' => 'Organization',
                               'is_reserved'    => 1,
                               'is_active'      => 1
                               );
        
        $relationshiptype = & civicrm_relationship_type_add( $relTypeParams );  
        $this->assertEqual( $relationshiptype['is_error'], 0 );
        $this->assertNotNull( $relationshiptype['id'] );   

        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_RelationshipType', $relationshiptype['id'],  $relTypeParams ); 
        
        //delete the created relationship
        $this->relationshipTypeDelete( $relationshiptype['id'] );
    }
   
}
 
?> 