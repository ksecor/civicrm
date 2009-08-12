<?php

require_once 'api/v2/EntityTag.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_EntityTagAddTest extends CiviUnitTestCase 
{
    protected $_individualID;

    protected $_householdID;

    protected $_organizationID;

    protected $_tagID;
    
    function get_info( )
    {
        return array(
                     'name'        => 'EntityTag Add',
                     'description' => 'Test all EntityTag Add API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    function setUp( ) 
    {
        parent::setUp();

        $this->_individualID = $this->individualCreate( );
        $this->_tagID = $this->tagCreate( ); 
        $this->_householdID = $this->houseHoldCreate( );
        $this->_organizationID = $this->organizationCreate( );
    }
    
    function tearDown( ) 
    {
        $this->contactDelete( $this->_individualID );
        $this->contactDelete( $this->_householdID );
        $this->contactDelete( $this->_organizationID );
        $this->tagDelete( $this->_tagID );
    }

    function testIndividualEntityTagAddEmptyParams( ) 
    {
        $params = array( );                             
        $individualEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $individualEntity['is_error'], 1 ); 
        $this->assertEquals( $individualEntity['error_message'], 'contact_id is a required field' );
       
    }
    
    function testIndividualEntityTagAddWithoutTagID( ) 
    {
        $ContactId =  $this->_individualID;
        $params = array('contact_id' =>  $ContactId);              
        $individualEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $individualEntity['is_error'], 1 );
        $this->assertEquals( $individualEntity['error_message'], 'tag_id is a required field' );
    }
    
    function testIndividualEntityTagAdd( ) 
    {
        $ContactId = $this->_individualID; 
        $tagID = $this->_tagID ;  
        $params = array(
                        'contact_id' =>  $ContactId,
                        'tag_id'     =>  $tagID);
        
        $individualEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $individualEntity['is_error'], 0 );
        $this->assertEquals( $individualEntity['added'], 1 );
    }
    
    function testHouseholdEntityTagAddEmptyParams( ) 
    {
        $params = array( );
        $householdEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $householdEntity['is_error'], 1 );
        $this->assertEquals( $householdEntity['error_message'], 'contact_id is a required field' );
    }
    
    function testHouseholdEntityTagAddWithoutTagID( ) 
    {
        $ContactId = $this->_householdID;
        $params = array('contact_id' =>  $ContactId);
        $householdEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $householdEntity['is_error'], 1 );
        $this->assertEquals( $householdEntity['error_message'], 'tag_id is a required field' );
        
    }
    
    function testHouseholdEntityTagAdd( ) 
    {
        $ContactId = $this->_householdID;
        $tagID = $this->_tagID;
        $params = array(
                        'contact_id' =>  $ContactId,
                        'tag_id'     =>  $tagID );
                               
        $householdEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $householdEntity['is_error'], 0 );
        $this->assertEquals( $householdEntity['added'], 1 );
    }
    
    function testOrganizationEntityTagAddEmptyParams( ) 
    {
        $params = array( );
        $organizationEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $organizationEntity['is_error'], 1 );
        $this->assertEquals( $organizationEntity['error_message'], 'contact_id is a required field' );
    }
    
    function testOrganizationEntityTagAddWithoutTagID( ) 
    {
        $ContactId = $this->_organizationID;
        $params = array('contact_id' =>  $ContactId);
        $organizationEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $organizationEntity['is_error'], 1 );
        $this->assertEquals( $organizationEntity['error_message'], 'tag_id is a required field' );
    }
        
    function testOrganizationEntityTagAdd( ) 
    {
        $ContactId = $this->_organizationID;
        $tagID = $this->_tagID;
        $params = array(
                        'contact_id' =>  $ContactId,
                        'tag_id'     =>  $tagID );
        
        $organizationEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $organizationEntity['is_error'], 0 );
        $this->assertEquals( $organizationEntity['added'], 1 );
    }
    
    function testEntityTagAddIndividualDouble( ) 
    {
        $individualId   = $this->_individualID;
        $organizationId = $this->_organizationID;
        $tagID = $this->_tagID;
        $params = array(
                        'contact_id' =>  $individualId,
                        'tag_id'     =>  $tagID
                        );
        
        $result = civicrm_entity_tag_add( $params );
        
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertEquals( $result['added'],    1 );
                
        $params = array(
                        'contact_id_i' => $individualId,
                        'contact_id_o' => $organizationId,
                        'tag_id'       => $tagID
                        );
        
        $result = civicrm_entity_tag_add( $params );
        $this->assertEquals( $result['is_error'],  0 );
        $this->assertEquals( $result['added'],     1 );
        $this->assertEquals( $result['not_added'], 1 );
    }
    
}



