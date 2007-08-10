<?php

require_once 'api/v2/Relationship.php';

/**
 * Class contains api test cases for "civicrm_relationship"
 *
 */

class TestOfRelationshipCreateAPIV2 extends CiviUnitTestCase 
{

    protected $_cId_a;
    protected $_cId_b;
    protected $_relTypeID;
    
    function setUp() 
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
    
    /**
     * check with empty array
     */
    function testRelationshipCreateEmpty( )
    {
        $params = array( );
        $result =& civicrm_relationship_create( $params );

        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_mesage'], 'No Input Parameter present' );
    }

    /**
     * check with No array
     */
    
    function testRelationshipCreateParamsNotArray( )
    {
        $params = 'relationship_type_id = 5';                            
        $result =& civicrm_relationship_delete( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Parameters is not an array' );
    }

    /**
     * check if required fields are not passed
     */
    function testRelationshipCreateWithoutRequired( )
    {
        $params = array(
                        'start_date' => '2007-08-01',
                        'end_date'   => '2007-08-30',
                        'is_active'  => 1
                        );
        
        $result =& civicrm_relationship_create($params);
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Missing required fields' );
    }

    /**
     * check with incorrect required fields
     */
    function testRelationshipCreateWithIncorrectData( )
    {
        $params = array(
                        'contact_id_a'         => $this->_cId_a,
                        'contact_id_b'         => $this->_cId_b,
                        'relationship_type_id' => 'Breaking Relationship'
                        );

        $result =& civicrm_relationship_create( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testRelationshipCreateWithIncorrectContactId( )
    {
        $params = array(
                        'contact_id_a'          => 0,
                        'contact_id_b'          => $this->_cId_b,
                        'relationship_type_id'  => $this->_relTypeID
                        );

        $result =& civicrm_relationship_create( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }

    /**
     * check relationship creation
     */
    function testRelationshipCreate( )
    {
        $relationParams = array( 'contact_id_a'         => $this->_cId_a,
                                 'contact_id_b'         => $this->_cId_b,
                                 'relationship_type_id' => $this->_relTypeID,
                                 'start_date'           => '2007-08-01',
                                 'end_date'             => '2007-08-30',
                                 'is_active'            => 1
                                 );
        
        $result = & civicrm_relationship_create( $params );

        $this->assertEqual( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );   

        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_Relationship', $result['id'], $relationParams ); 
        
        //delete created relationship
        $this->relationshipDelete( $result['id'] );
    }

    /**
     * create relationship with custom data 
     * ( will do this, once custom * v2 api are ready 
         with all changed schema for custom data  )
     */
    function testRelationshipCreateWithCustomData( )
    {         
        
    }
    
    function tearDown() 
    {
        $this->relationshipTypeDelete( $this->_relTypeID );
        $this->contactDelete( $this->_cId_a );
        $this->contactDelete( $this->_cId_b );
    }
}
 
?> 