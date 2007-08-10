<?php

require_once 'api/v2/Relationship.php';

/**
 * Class contains api test cases for "civicrm_relationship"
 *
 */

class TestOfRelationshipUpdateAPIV2 extends CiviUnitTestCase 
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
        
        $relParams     = array(
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
    function testRelationshipUpdateEmpty( )
    {
        $params = array( );
        $result =& civicrm_relationship_add( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_mesage'], 'No Input Parameter present' );
    }
    
    /**
     * check with No array
     */
    function testRelationshipUpdateParamsNotArray( )
    {
        $params = 'relationship_type_id = 5';                            
        $result =& civicrm_relationship_add( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Parameters is not an array' );
    }

    /**
     * check if required fields are not passed
     */
    function testRelationshipUpdateWithoutRequired( )
    {
        $params = array(
                        'start_date' => '2007-08-01',
                        'end_date'   => '2007-08-30',
                        'is_active'  => 1
                        );
        
        $result =& civicrm_relationship_add( $params );
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Missing required fields' );
    }
    
    /**
     * check with incorrect required fields
     */
    function testRelationshipUpdateWithIncorrectData( )
    {
        $params = array(
                        'contact_id_a'          => $this->_cId_a,
                        'contact_id_b'          => $this->_cId_b,
                        'relationship__type_id' => 'Breaking Relationship'
                        );
        
        $result =& civicrm_relationship_add( $params );
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Missing required fields' );
    }
    
    /**
     * check with incorrect required fields
     */
    function testRelationshipUpdateWithIncorrectContactId( )
    {
        $params = array(
                        'contact_id_a'          => 0,
                        'contact_id_b'          => $this->_cId_b,
                        'relationship__type_id' => $this->_relTypeID
                        );
        
        $result =& civicrm_relationship_add( $params );
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Missing required fields' );
    }
    
    /**
     * check relationship creation
     */
    function testRelationshipUpdate( )
    {
        $relationParams = array(
                                'id'                   => $this->_relationID,
                                'contact_id_a'         => $this->_cId_a,
                                'contact_id_b'         => $this->_cId_b,
                                'relationship_type_id' => 5,
                                'start_date'           => '2002-01-01',
                                'end_date'             => '2007-08-30',
                                'is_active'            => 0
                                );
        
        $result = & civicrm_relationship_add( $params );
        
        $this->assertEqual( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );   

        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_Relationship', $result['id'], $relationParams ); 
        
        //delete created relationship
        $this->relationshipDelete( $result['id'] );
    }
    
    /**
     * update relationship with custom data 
     * ( will do this, once custom * v2 api are ready 
         with all changed schema for custom data  )
    */
    function testRelationshipUpdateWithCustomData( )
    {        
    }
    
    function tearDown( ) 
    {
        $this->relationshipDelete( $this->_relationID );
        $this->relationshipTypeDelete( $this->_relTypeID );
        $this->contactDelete( $this->_cId_a );
        $this->contactDelete( $this->_cId_b );
    }
}
?> 