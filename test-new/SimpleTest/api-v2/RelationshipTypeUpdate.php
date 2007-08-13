<?php

require_once 'api/v2/Relationship.php';
/**
 * Class contains api test cases for "civicrm_relationship_type"
 *
 */
class TestOfRelationshipTypeUpdateAPIV2 extends CiviUnitTestCase 
{
    protected $_relationshipTypeID;
    
    function setUp( ) 
    {
        $this->_relTypeID = $this->relationshipTypeCreate( );
    }
 
    /**
     * check with empty array
     */    
    function testRelationshipTypeUpdateEmpty( )
    {
        $params = array( );        
        $result =& civicrm_relationship_type_add( $params );

        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'No input parameters present' );
    }
    
    /**
     * check with No array
     */
    function testRelationshipTypeUpdateParamsNotArray( )
    {
        $params = 'name_a_b = Relation 1';                            
        $result =& civicrm_relationship_type_add( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Parameter is not an array' );
    }

    /**
     * check with no contact type
     */
    function testRelationshipTypeUpdateWithoutcontactType( )
    {
        $relTypeParams = array(
                               'id'             => $this->_relTypeID,
                               'name_a_b'       => 'Test 1',
                               'name_b_a'       => 'Test 2',
                               'description'    => 'Testing relationship type',
                               'is_reserved'    => 1,
                               'is_active'      => 0
                               );
        
        $result = & civicrm_relationship_type_add( $relTypeParams );  
       
        $this->assertEqual( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );   

        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_RelationshipType', $result['id'],  $relTypeParams ); 
    }
    
    /**
     * check with all parameters
     */
    function testRelationshipTypeUpdate( )
    {
        $relTypeParams = array(
                               'id'             => $this->_relTypeID,
                               'name_a_b'       => 'Test 1',
                               'name_b_a'       => 'Test 2',
                               'description'    => 'Testing relationship type',
                               'contact_type_a' => 'Individual',
                               'contact_type_b' => 'Individual',
                               'is_reserved'    => 0,
                               'is_active'      => 0
                               );
        
        $result = & civicrm_relationship_type_add( $relTypeParams );  
        $this->assertEqual( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );   

        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_RelationshipType', $result['id'],  $relTypeParams ); 
        
     }
    
    function tearDown( ) 
    {
        //delete the created relationship & related contacts
        $this->relationshipTypeDelete( $this->_relTypeID );
    }
}

?> 