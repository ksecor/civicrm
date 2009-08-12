<?php

require_once 'api/v2/EntityTag.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_EntityTagGetTest extends CiviUnitTestCase 
{
    protected $_individualID;
    
    protected $_householdID;
    
    protected $_organizationID;

    protected $_tagID; 

    function get_info( )
    {
        return array(
                     'name'        => 'Get EntityTag',
                     'description' => 'Test all Get EntityTag API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
    
    function setUp() 
    { 
        parent::setUp();
        
        $this->_individualID   = $this->individualCreate( );
        $this->_tagID          = $this->tagCreate( ); 
        $this->_householdID    = $this->houseHoldCreate( );
        $this->_organizationID = $this->organizationCreate( );
    }
    
    function testIndividualEntityTagGetWithoutContactID( )
    {
        $paramsEntity = array( );
        $entity       =& civicrm_entity_tag_get( $paramsEntity ); 
        $this->assertEquals( $entity['is_error'], 1 );
        $this->assertNotNull( $entity['error_message'] );
    }
    
    function testIndividualEntityTagGet( )
    {
        $ContactId = $this->_individualID; 
        $tagID     = $this->_tagID ;  
        $params    = array(
                           'contact_id' =>  $ContactId,
                           'tag_id'     =>  $tagID );
        
        $individualEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $individualEntity['is_error'], 0 );
        $this->assertEquals( $individualEntity['added'], 1 );
        
        $paramsEntity = array('contact_id' =>  $ContactId );
        $entity =& civicrm_entity_tag_get( $paramsEntity );
    }
    
    function testHouseholdEntityGetWithoutContactID( )
    {
        $paramsEntity = array( );
        $entity       =& civicrm_entity_tag_get( $paramsEntity );
        $this->assertEquals( $entity['is_error'], 1 );
        $this->assertNotNull( $entity['error_message'] );
    }

    function testHouseholdEntityGet( )
    {
       
        $ContactId = $this->_householdID;
        $tagID     = $this->_tagID;
        $params    = array(
                           'contact_id' =>  $ContactId,
                           'tag_id'     =>  $tagID );
        
        $householdEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $householdEntity['is_error'], 0 );
        $this->assertEquals( $householdEntity['added'], 1 );
        
        $paramsEntity = array('contact_id' => $ContactId ); 
        $entity =& civicrm_entity_tag_get( $paramsEntity );
    }
    
    function testOrganizationEntityGetWithoutContactID()
    {
        $paramsEntity = array( );
        $entity =& civicrm_entity_tag_get( $paramsEntity ); 
        $this->assertEquals( $entity['is_error'], 1 );
        $this->assertNotNull( $entity['error_message'] );
    }

    function testOrganizationEntityGet( )
    {
        $ContactId = $this->_organizationID;
        $tagID     = $this->_tagID;
        $params    = array(
                           'contact_id' =>  $ContactId,
                           'tag_id'     =>  $tagID );
        
        $organizationEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEquals( $organizationEntity['is_error'], 0 );
        $this->assertEquals( $organizationEntity['added'], 1 );
        
        $paramsEntity = array('contact_id' => $ContactId );
        $entity =& civicrm_entity_tag_get( $paramsEntity ); 
    }
    
    function tearDown( ) 
    {
        $this->contactDelete( $this->_individualID );
        $this->contactDelete( $this->_householdID );
        $this->contactDelete( $this->_organizationID );
        $this->tagDelete( $this->_tagID );
        
    }
    
}

