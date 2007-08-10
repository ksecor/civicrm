<?php

require_once 'api/v2/Relationship.php';

/**
 * Class contains api test cases for "civicrm_relationship"
 *
 */

class TestOfRelationshipDeleteAPIV2 extends CiviUnitTestCase 
{
    
    protected $_cId_a;
    protected $_cId_b;
    protected $_relTypeID;
    protected $_relationID;
    
    function setUp( ) 
    {
        $this->_cId_a  = $this->individualCreate( );
        $this->_cId_b  = $this->organizationCreate( );
        
        $relTypeParams = array(
                               'name_a_b'       => 'Relation 1',
                               'name_b_a'       => 'Relation 2',
                               'description'    => 'Testing relationship type',
                               'contact_type_a' => 'Individual',
                               'contact_type_b' => 'Organization',
                               'is_reserved'    => 1,
                               'is_active'      => 1
                               );
        
        $this->_relTypeID = $this->relationshipTypeCreate( $relTypeParams );
    }
    
    function testRelationshipCreate( )
    {
        $relParams = array(
                           'contact_id_a'         => $this->_cId_a,
                           'contact_id_b'         => $this->_cId_b,
                           'relationship_type_id' => $this->_relTypeID,
                           'start_date'           => '2007-08-01',
                           'end_date'             => '2007-08-30',
                           'is_active'            => 1
                           );
        
        $this->_relationID = $this->relationshipCreate( $relParams );
    }
    
    /**
     * check with empty array
     */
    function testRelationshipDeleteEmpty( )
    {
        $params = array( );
        $result =& civicrm_relationship_delete( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_mesage'], 'No Input Parameter present' );
    }
    
    /**
     * check with No array
     */
    
    function testRelationshipDeleteParamsNotArray( )
    {
        $params = 'relationship_type_id = 5';                            
        $result =& civicrm_relationship_delete( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Parameters is not an array' );
    }
    
    /**
     * check if required fields are not passed
     */
    function testRelationshipDeleteWithoutRequired( )
    {
        $params = array(
                        'start_date' => '2007-08-01',
                        'end_date'   => '2007-08-30',
                        'is_active'  => 1
                        );
        
        $result =& civicrm_relationship_delete( $params );
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Missing required fields' );
    }
    
    /**
     * check with incorrect required fields
     */
    function testRelationshipDeleteWithIncorrectData( )
    {
        $params = array(
                        'contact_id_a'         => $this->_cId_a,
                        'contact_id_b'         => $this->_cId_b,
                        'relationship_type_id' => 'Breaking Relationship'
                        );
        
        $result =& civicrm_relationship_delete( $params );
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Missing required fields' );
    }
    
    /**
     * check with incorrect required fields contact id
     */
    function testRelationshipDeleteWithIncorrectContactId( )
    {
        $params = array(
                        'contact_id_a'          => 0,
                        'contact_id_b'          => $this->_cId_b,
                        'relationship__type_id' => $this->_relTypeID
                        );
        
        $result =& civicrm_relationship_delete( $params );
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Incorrect or no fields' );
    }
    
    /**
     * check relationship creation
     */
    function testRelationshipDelete( )
    {
        $relationParams = array(
                                'id'                   => $this->_relationID,
                                'contact_id_a'         => $this->_cId_a,
                                'contact_id_b'         => $this->_cId_b,
                                'relationship_type_id' => 5,
                                );
        
        $result = & civicrm_relationship_delete( $params );
        
        $this->assertEqual( $result['is_error'], 0 );
    }
    
    /**
     * create relationship with custom data 
     * ( will do this, once custom * v2 api are ready 
         with all changed schema for custom data  )
    */
    function testRelationshipDeleteWithCustomData( )
    {        
        
    }
    
    function tearDown() 
    {
        $this->reltionshipTypeDelete( $this->_relTypeID );
        $this->contactDelete( $this->_cId_a );
        $this->contactDelete( $this->_cId_b );
    }
}
 
?> 