<?php

require_once 'api/v2/Relationship.php';
require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * Class contains api test cases for "civicrm_relationship_type"
 *
 */
class api_v2_RelationshipTypeCreateTest extends CiviUnitTestCase 
{
    protected $_cId_a;
    protected $_cId_b;
    protected $_relTypeID;

    function get_info( )
    {
        return array(
                     'name'        => 'RelationshipType Create',
                     'description' => 'Test all RelationshipType Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    function setUp( ) 
    { 
        parent::setUp();
        
        $this->_cId_a  = $this->individualCreate( );
        $this->_cId_b  = $this->organizationCreate( );
        
        $relTypeParams = array(
                               'name_a_b'       => 'Relation 1 for create',
                               'name_b_a'       => 'Relation 2 for create',
                               'description'    => 'Testing relationship type',
                               'contact_type_a' => 'Individual',
                               'contact_type_b' => 'Organization',
                               'is_reserved'    => 1,
                               'is_active'      => 1
                               );
        
    }
    
    function tearDown( ) 
    {
	$this->contactDelete( $this->_cId_a );
        $this->contactDelete( $this->_cId_b );
    }
    /**
     * check with empty array
     */    
    function testRelationshipTypeCreateEmpty( )
    {
        $params = array( );        
        $result =& civicrm_relationship_type_add( $params );
        
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'No input parameters present' );
    }
    
    /**
     * check with No array
     */
    function testRelationshipTypeCreateParamsNotArray( )
    {
        $params = 'name_a_b = Employee of';   
        $result =& civicrm_relationship_type_add( $params );                  
       
        
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Parameter is not an array' );
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
        $result =& civicrm_relationship_type_add( $relTypeParams );
        
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Missing required parameters' );
    }
       
    /**
     * check with no contact type
     */
    function testRelationshipTypeCreateWithoutcontactType( )
    {
        $relTypeParams = array(
                               'name_a_b' => 'Relation 1 without contact type',
                               'name_b_a' => 'Relation 2 without contact type'
                               );
        $result = & civicrm_relationship_type_add( $relTypeParams ); 
       

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
                               'name_a_b'       => 'Relation 1 for relationship type create',
                               'name_b_a'       => 'Relation 2 for relationship type create',
                               'contact_type_a' => 'Individual',
                               'contact_type_b' => 'Organization',
                               'is_reserved'    => 1,
                               'is_active'      => 1
                               );
        $relationshiptype =& civicrm_relationship_type_add( $relTypeParams );
        
        $this->assertNotNull( $relationshiptype['id'] );   
        
        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_RelationshipType', $relationshiptype['id'],  $relTypeParams ); 
        
        //delete the created relationship
        $this->relationshipTypeDelete( $relationshiptype['id'] );
    }
    
}
 
?> 