<?php

require_once 'api/v2/Relationship.php';

/**
 * Class contains api test cases for "civicrm_relationship_type"
 *
 */

class api_v2_TestRelationshipTypeDelete extends CiviUnitTestCase 
{
    protected $_cId_a;
    protected $_cId_b;
    protected $_relTypeID;

    function get_info( )
    {
        return array(
                     'name'        => 'RelationshipType Delete',
                     'description' => 'Test all RelationshipType Delete API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    function setUp( ) 
    {
        $this->_cId_a  = $this->individualCreate( );
        $this->_cId_b  = $this->organizationCreate( );
    }

    // First Create a relationship type
    function testRelationshipTypeCreate( )
    { 
       
        $relTypeParams = array(
                               'name_a_b'       => 'Relation 1 for delete',
                               'name_b_a'       => 'Relation 2 for delete',
                               'description'    => 'Testing relationship type',
                               'contact_type_a' => 'Individual',
                               'contact_type_b' => 'Organization',
                               'is_reserved'    => 1,
                               'is_active'      => 1
                               );


        $result =& civicrm_relationship_type_add( $relTypeParams );
        $this->_relTypeID = $result['id'];
        $this->assertNotNull( $result['id'] ); 
    }
    
    /**
     * check with empty array
     */
    function testRelationshipTypeDeleteEmpty( )
    {
        $params = array( );
        $result =& civicrm_relationship_type_delete( $params );
        
        $this->assertEquals( $result['is_error'], 1 );
    }
    
    /**
     * check with No array
     */
    
    function testRelationshipTypeDeleteParamsNotArray( )
    {
        $params = 'name_a_b = Test1';                            
        $result =& civicrm_relationship_type_delete( $params );
       
        $this->assertEquals( $result['is_error'], 1 );
    }
    
    /**
     * check if required fields are not passed
     */
    function testRelationshipTypeDeleteWithoutRequired( )
    {
        $params = array(
                        'name_b_a'       => 'Relation 2 delete without required',
                        'contact_type_b' => 'Individual',
                        'is_reserved'    => 0,
                        'is_active'      => 0
                        );
        
        $result =& civicrm_relationship_type_delete( $params );
              
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Missing required parameter' );
    }
    
    /**
     * check with incorrect required fields
     */
    function testRelationshipTypeDeleteWithIncorrectData( )
    {
        $params = array(
                        'id'             => 'abcd',
                        'name_b_a'       => 'Relation 2 delete with incorrect',
                        'description'    => 'Testing relationship type',
                        'contact_type_a' => 'Individual',
                        'contact_type_b' => 'Individual',
                        'is_reserved'    => 0,
                        'is_active'      => 0
                        );

        $result =& civicrm_relationship_type_delete( $params );
       
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Invalid value for relationship type ID' );
    }
    
    /**
     * check relationship type delete
     */
    function testRelationshipTypeDelete( )
    {
        $params['id'] = $this->_relTypeID;
        
        $result = & civicrm_relationship_type_delete( $params );
        
        $this->assertEquals( $result['is_error'], 0 );
    }
    
    function tearDown( ) 
    {
	$this->contactDelete( $this->_cId_a );
        $this->contactDelete( $this->_cId_b );   
    }
}
?> 