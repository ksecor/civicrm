<?php

require_once 'api/v2/EntityTag.php';

class TestOfEntityTagAdd extends CiviUnitTestCase 
{
    function setup( ) 
    {
    } 
    
    function testIndividualEntityTagAddEmptyParams( ) 
    {
        $params = array( );                             
        $individualEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEqual( $individualEntity['is_error'], 1 ); 
        $this->assertEqual( $individualEntity['error_message'], 'contact_id is a required field' );
       
    }

    function testIndividualEntityTagAddWithoutTagID( ) 
    {
        $ContactId = $this->individualCreate( );
        $params = array('contact_id' =>  $ContactId);
        
        $individualEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEqual( $individualEntity['is_error'], 1 );
        $this->assertEqual( $individualEntity['error_message'], 'tag_id is a required field' );
        $this->contactDelete( $params['contact_id'] );
    }

    function testIndividualEntityTagAdd( ) 
    {
        $ContactId = $this->individualCreate( );
        $tagID = $this->tagCreate( );
        $params = array(
                        'contact_id' =>  $ContactId,
                        'tag_id'     =>  $tagID);
        
        $individualEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEqual( $individualEntity['is_error'], 0 );
        $this->assertEqual( $individualEntity['added'], 1 );
        $this->contactDelete( $params['contact_id'] );
        $this->tagDelete( $tagID );
    }
      
    function testHouseholdEntityTagAddEmptyParams( ) 
    {
        $params = array( );
        $householdEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEqual( $householdEntity['is_error'], 1 );
        $this->assertEqual( $householdEntity['error_message'], 'contact_id is a required field' );
    }

    function testHouseholdEntityTagAddWithoutTagID( ) 
    {
        $ContactId = $this->houseHoldCreate( );
        $params = array('contact_id' =>  $ContactId);
        $householdEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEqual( $householdEntity['is_error'], 1 );
        $this->assertEqual( $householdEntity['error_message'], 'tag_id is a required field' );
        $this->contactDelete( $params['contact_id'] );
    }

    function testHouseholdEntityTagAdd( ) 
    {
        $ContactId = $this->householdCreate( );
        $tagID = $this->tagCreate( );
        $params = array(
                        'contact_id' =>  $ContactId,
                        'tag_id'     =>  $tagID );
                               
        $householdEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEqual( $householdEntity['is_error'], 0 );
        $this->assertEqual( $householdEntity['added'], 1 );
        $this->contactDelete( $params['contact_id'] );
        $this->tagDelete( $tagID );
    }
    
    function testOrganizationEntityTagAddEmptyParams( ) 
    {
        $params = array( );
        $organizationEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEqual( $organizationEntity['is_error'], 1 );
        $this->assertEqual( $organizationEntity['error_message'], 'contact_id is a required field' );
    }
    
    function testOrganizationEntityTagAddWithoutTagID( ) 
    {
        $ContactId = $this->organizationCreate( );
        $params = array('contact_id' =>  $ContactId);
        $organizationEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEqual( $organizationEntity['is_error'], 1 );
        $this->assertEqual( $organizationEntity['error_message'], 'tag_id is a required field' );
        $this->contactDelete( $params['contact_id'] );
    }

    function testOrganizationEntityTagAdd( ) 
    {
        $ContactId = $this->organizationCreate( );
        $tagID = $this->tagCreate( );
        $params = array(
                        'contact_id' =>  $ContactId,
                        'tag_id'     =>  $tagID );
                                           
        $organizationEntity = civicrm_entity_tag_add( $params ); 
        $this->assertEqual( $organizationEntity['is_error'], 0 );
        $this->assertEqual( $organizationEntity['added'], 1 );
        $this->contactDelete( $params['contact_id'] );
        $this->tagDelete( $tagID );
    }
    
    function testEntityTagAddIndividualDouble( ) 
    {
        $individualId   = $this->individualCreate( );
        $organizationId = $this->organizationCreate( );
        $tagID = $this->tagCreate( );
        $params = array(
                        'contact_id' =>  $individualId,
                        'tag_id'     =>  $tagID
                        );
        
        $result = civicrm_entity_tag_add( $params );
        
        $this->assertEqual( $result['is_error'], 0 );
        $this->assertEqual( $result['added'],    1 );
                
        $params = array(
                        'contact_id_i' => $individualId,
                        'contact_id_o' => $organizationId,
                        'tag_id'       => $tagID
                        );
        
        $result = civicrm_entity_tag_add( $params );
        
        $this->assertEqual( $result['is_error'],  0 );
        $this->assertEqual( $result['added'],     1 );
        $this->assertEqual( $result['not_added'], 1 );
        
        $this->contactDelete( $individualId   );
        $this->contactDelete( $organizationId );
        $this->tagDelete( $tagID );
    }
    
    function tearDown( ) 
    {
    }

}
?>