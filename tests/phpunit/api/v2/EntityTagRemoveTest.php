<?php

require_once 'api/v2/EntityTag.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_EntityTagRemoveTest extends CiviUnitTestCase 
{
    public $individualID;
    public $householdID;
    public $tagID;

    function get_info( )
    {
        return array(
                     'name'        => 'EntityTag Remove',
                     'description' => 'Test all EntityTag Remove API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }     
    
    function setUp( )
    {
        parent::setUp();
        
        $this->individualID    = $this->individualCreate( );
        $this->householdID     = $this->householdCreate( );
        $this->organizationID  = $this->organizationCreate( );
        
        $this->tagID           = $this->tagCreate( );
    }
    
    function testEntityTagRemoveNoContactId( )
    {
        $entityTagParams = array(
                                 'contact_id_i' => $this->individualID,
                                 'contact_id_h' => $this->householdID,
                                 'tag_id'       => $this->tagID
                                 );
        $this->entityTagAdd( $entityTagParams );
        
        $params = array(
                        'tag_id' => $this->tagID
                        );
                
        $result = civicrm_entity_tag_remove( $params );
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'contact_id is a required field' );
    }
    
    function testEntityTagRemoveNoTagId( )
    {
        $entityTagParams = array(
                                 'contact_id_i' => $this->individualID,
                                 'contact_id_h' => $this->householdID,
                                 'tag_id'       => $this->tagID
                                 );
        $this->entityTagAdd( $entityTagParams );
        
        $params = array(
                        'contact_id_i' => $this->individualID,
                        'contact_id_h' => $this->householdID,
                        );
                
        $result = civicrm_entity_tag_remove( $params );
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'tag_id is a required field' );
    }
    
    function testEntityTagRemoveINDHH( )
    {
        $entityTagParams = array(
                                 'contact_id_i' => $this->individualID,
                                 'contact_id_h' => $this->householdID,
                                 'tag_id'       => $this->tagID
                                 );
        $this->entityTagAdd( $entityTagParams );
        
        $params = array(
                        'contact_id_i' => $this->individualID,
                        'contact_id_h' => $this->householdID,
                        'tag_id'       => $this->tagID
                        );
                
        $result = civicrm_entity_tag_remove( $params );
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertEquals( $result['removed'], 2 );
    }    
    
    function testEntityTagRemoveHH( )
    {
        $entityTagParams = array(
                                 'contact_id_i' => $this->individualID,
                                 'contact_id_h' => $this->householdID,
                                 'tag_id'       => $this->tagID
                                 );
        $this->entityTagAdd( $entityTagParams );
        
        $params = array(
                        'contact_id_h' => $this->householdID,
                        'tag_id'       => $this->tagID
                        );
                
        $result = civicrm_entity_tag_remove( $params );
        $this->assertEquals( $result['removed'], 1 );
    }
    
    function testEntityTagRemoveHHORG( )
    {
        $entityTagParams = array(
                                 'contact_id_i' => $this->individualID,
                                 'contact_id_h' => $this->householdID,
                                 'tag_id'       => $this->tagID
                                 );
        $this->entityTagAdd( $entityTagParams );
        
        $params = array(
                        'contact_id_h' => $this->householdID,
                        'contact_id_o' => $this->organizationID,
                        'tag_id'       => $this->tagID
                        );
                
        $result = civicrm_entity_tag_remove( $params );
        $this->assertEquals( $result['removed'], 1 );
        $this->assertEquals( $result['not_removed'], 1 );
    }
    
    function tearDown( )
    {
        $this->contactDelete( $this->individualID );
        $this->contactDelete( $this->householdID  );
	$this->contactDelete( $this->organizationID );
        $this->tagDelete(     $this->tagID        );
    }
}
