<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once 'api/v2/Relationship.php';
require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * Class contains api test cases for "civicrm_relationship_type"
 *
 */
class api_v2_RelationshipTypeTest extends CiviUnitTestCase 
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
    }
    
    function tearDown( ) 
    {
        $this->contactDelete( $this->_cId_a );
        $this->contactDelete( $this->_cId_b );
    }

///////////////// civicrm_relationship_type_add methods
    
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
    function testRelationshipTypeCreateWithoutContactType( )
    {
        $relTypeParams = array(
                               'name_a_b' => 'Relation 1 without contact type',
                               'name_b_a' => 'Relation 2 without contact type'
                               );
        $result = & civicrm_relationship_type_add( $relTypeParams ); 
        
        $this->assertNotNull( $result['id'] );
        
        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_RelationshipType', $result['id'],  $relTypeParams ); 
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
    }

///////////////// civicrm_relationship_type_delete methods
    
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
        // create sample relationship type.
        $params['id'] = $this->_relationshipTypeCreate( );
        
        $result = & civicrm_relationship_type_delete( $params );
        
        $this->assertEquals( $result['is_error'], 0 );
    }

///////////////// civicrm_relationship_type_update
    
    /**
     * check with empty array
     */    
    function testRelationshipTypeUpdateEmpty( )
    {
        $params = array( );        
        $result =& civicrm_relationship_type_update( $params );
        
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'No input parameters present' );
    }
    
    /**
     * check with No array
     */
    function testRelationshipTypeUpdateParamsNotArray( )
    {
        $params = 'name_a_b = Relation 1';                            
        $result =& civicrm_relationship_type_update( $params );
        
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Parameter is not an array' );
    }
    
    /**
     * check with no contact type
     */
    function testRelationshipTypeUpdateWithoutContactType( )
    {
        // create sample relationship type.
        $this->_relTypeID = $this->_relationshipTypeCreate( );
        
        $relTypeParams = array(
                               'id'             => $this->_relTypeID,
                               'name_a_b'       => 'Test 1',
                               'name_b_a'       => 'Test 2',
                               'description'    => 'Testing relationship type',
                               'is_reserved'    => 1,
                               'is_active'      => 0
                               );
        
        $result = & civicrm_relationship_type_update( $relTypeParams );  
        
        $this->assertNotNull( $result['id'] );   
        
        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_RelationshipType', $result['id'],  $relTypeParams ); 
    }
    
    /**
     * check with all parameters
     */
    function testRelationshipTypeUpdate( )
    {
        // create sample relationship type.
        $this->_relTypeID = $this->_relationshipTypeCreate( );
        
        $relTypeParams = array(
                               'id'             => $this->_relTypeID,
                               'name_a_b'       => 'Test 1 for update',
                               'name_b_a'       => 'Test 2 for update',
                               'description'    => 'SUNIL PAWAR relationship type',
                               'contact_type_a' => 'Individual',
                               'contact_type_b' => 'Individual',
                               'is_reserved'    => 0,
                               'is_active'      => 0
                               );
        
        $result = & civicrm_relationship_type_update( $relTypeParams );  
        $this->assertNotNull( $result['id'] );   
        
        // assertDBState compares expected values in $result to actual values in the DB          
        $this->assertDBState( 'CRM_Contact_DAO_RelationshipType', $result['id'],  $relTypeParams ); 
    }

///////////////// civicrm_relationship_types_get methods
    
    /**
     * check with empty array
     */    
    function testRelationshipTypesGetEmptyParams( )
    {
        $firstRelTypeParams = array(
                                    'name_a_b'       => 'Relation 1 for create',
                                    'name_b_a'       => 'Relation 2 for create',
                                    'description'    => 'Testing relationship type',
                                    'contact_type_a' => 'Individual',
                                    'contact_type_b' => 'Organization',
                                    'is_reserved'    => 1,
                                    'is_active'      => 1
                                    );
        
        $secondRelTypeParams = array(
                                     'name_a_b'       => 'Relation 3 for create',
                                     'name_b_a'       => 'Relation 4 for create',
                                     'description'    => 'Testing relationship type second',
                                     'contact_type_a' => 'Individual',
                                     'contact_type_b' => 'Organization',
                                     'is_reserved'    => 0,
                                     'is_active'      => 1
                                     );
        
        $relTypeIds = array( );
        // create sample relationship types.
        foreach ( array( 'firstRelType', 'secondRelType' ) as $relType ) {
            $params = "{$relType}Params";
            $relTypeIds["{$relType}Id"] = $this->_relationshipTypeCreate( $$params );
        }
        
        //get relationship types from db.
        $params = array( );        
        $results =& civicrm_relationship_types_get( $params );
        
        $retrievedRelTypes  = array( );
        if ( is_array( $results ) ) {
            foreach ( $results as $relTypeValues ) {
                if ( ( $relTypeId = CRM_Utils_Array::value( 'id', $relTypeValues ) ) 
                     && in_array( $relTypeId, $relTypeIds ) ) {
                    $retrievedRelTypes[$relTypeId] = $relTypeValues;
                }
            }
        }
        
        if ( count( $retrievedRelTypes ) < 2 ) {
            $this->fail( 'Failed to retrieve relationship types.' );  
        }
        
        foreach ( array( 'firstRelType', 'secondRelType' ) as $relType ) {
            $relTypeId     = $relTypeIds["{$relType}Id"];
            $relTypeparams = "{$relType}Params";
            foreach ( $$relTypeparams as $key => $val ) {
                $this->assertEquals( CRM_Utils_Array::value($key, $retrievedRelTypes[$relTypeId]), 
                                     $val, "Fail to retrieve {$key}" ); 
            }
        }        
    }
    
    /**
     * check with params Not Array.
     */
    function testRelationshipTypesGetParamsNotArray( )
    {
        $firstRelTypeParams = array(
                                    'name_a_b'       => 'Relation 1 for create',
                                    'name_b_a'       => 'Relation 2 for create',
                                    'description'    => 'Testing relationship type',
                                    'contact_type_a' => 'Individual',
                                    'contact_type_b' => 'Organization',
                                    'is_reserved'    => 1,
                                    'is_active'      => 1
                                    );
        
        $secondRelTypeParams = array(
                                     'name_a_b'       => 'Relation 3 for create',
                                     'name_b_a'       => 'Relation 4 for create',
                                     'description'    => 'Testing relationship type second',
                                     'contact_type_a' => 'Individual',
                                     'contact_type_b' => 'Organization',
                                     'is_reserved'    => 0,
                                     'is_active'      => 1
                                     );
        $relTypeIds = array( );
        // create sample relationship types.
        foreach ( array( 'firstRelType', 'secondRelType' ) as $relType ) {
            $params = "{$relType}Params";
            $relTypeIds["{$relType}Id"] = $this->_relationshipTypeCreate( $$params );
        }
        
        //get relationship types from db.
        $params = 'name_a_b = Employee of';        
        $results =& civicrm_relationship_types_get( $params );
        
        $retrievedRelTypes  = array( );
        if ( is_array( $results ) ) {
            foreach ( $results as $relTypeValues ) {
                if ( ( $relTypeId = CRM_Utils_Array::value( 'id', $relTypeValues ) ) 
                     && in_array( $relTypeId, $relTypeIds ) ) {
                    $retrievedRelTypes[$relTypeId] = $relTypeValues;
                }
            }
        }
        
        if ( count( $retrievedRelTypes ) < 2 ) {
            $this->fail( 'Fail to retrieve relationship types.' );  
        }
        
        foreach ( array( 'firstRelType', 'secondRelType' ) as $relType ) {
            $relTypeId     = $relTypeIds["{$relType}Id"];
            $relTypeparams = "{$relType}Params";
            foreach ( $$relTypeparams as $key => $val ) {
                $this->assertEquals( CRM_Utils_Array::value($key, $retrievedRelTypes[$relTypeId]), 
                                     $val, "Fail to retrieve {$key}" ); 
            }
        }        
    }
    
    /**
     * check with valid params array.
     */
    function testRelationshipTypesGet( )
    {
        $firstRelTypeParams = array(
                                    'name_a_b'       => 'Relation 1 for create',
                                    'name_b_a'       => 'Relation 2 for create',
                                    'description'    => 'Testing relationship type',
                                    'contact_type_a' => 'Individual',
                                    'contact_type_b' => 'Organization',
                                    'is_reserved'    => 1,
                                    'is_active'      => 1
                                    );
        
        $secondRelTypeParams = array(
                                     'name_a_b'       => 'Relation 3 for create',
                                     'name_b_a'       => 'Relation 4 for create',
                                     'description'    => 'Testing relationship type second',
                                     'contact_type_a' => 'Individual',
                                     'contact_type_b' => 'Organization',
                                     'is_reserved'    => 0,
                                     'is_active'      => 1
                                     );
        $relTypeIds = array( );
        // create sample relationship types.
        foreach ( array( 'firstRelType', 'secondRelType' ) as $relType ) {
            $params = "{$relType}Params";
            $relTypeIds["{$relType}Id"] = $this->_relationshipTypeCreate( $$params );
        }
        
        //get relationship types from db.
        $params = array( 'name_a_b' => 'Relation 3 for create', 
                         'name_b_a' => 'Relation 4 for create',
                         'description'    => 'Testing relationship type second' );        
        $results =& civicrm_relationship_types_get( $params );
        
        $retrievedRelTypes  = array( );
        if ( is_array( $results ) ) {
            foreach ( $results as $relTypeValues ) {
                if ( ( $relTypeId = CRM_Utils_Array::value( 'id', $relTypeValues ) ) 
                     && in_array( $relTypeId, $relTypeIds ) ) {
                    $retrievedRelTypes[$relTypeId] = $relTypeValues;
                }
            }
        }
        
        if ( count( $retrievedRelTypes ) != 1 ) {
            $this->fail( 'Fail to retrieve target relationship type.' );  
        }
        
        foreach ( $secondRelTypeParams as $key => $val ) {
            $this->assertEquals( CRM_Utils_Array::value( $key, $retrievedRelTypes[$relTypeIds['secondRelTypeId']]), 
                                 $val, "Fail to retrieve {$key}" ); 
        }
    }
    
    /**
     * create relationship type.
     */
    function _relationshipTypeCreate( $params = null )
    {
        if ( !is_array( $params ) || empty( $params ) ) {
            $params = array(
                            'name_a_b'       => 'Relation 1 for create',
                            'name_b_a'       => 'Relation 2 for create',
                            'description'    => 'Testing relationship type',
                            'contact_type_a' => 'Individual',
                            'contact_type_b' => 'Organization',
                            'is_reserved'    => 1,
                            'is_active'      => 1
                            );
        }

        return $this->relationshipTypeCreate( $params );
    }
}
 
?> 