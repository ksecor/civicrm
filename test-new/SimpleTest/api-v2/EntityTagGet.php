<?php

require_once 'api/v2/EntityTag.php';

class TestOfGetEntitiesByTagAPIV2 extends CiviUnitTestCase 
{
       
    function setUp() 
    {
    }
    
    function testIndividualEntityTagGetWithoutContactID()
    {
        $paramsEntity = array( );
        $entity =& civicrm_entity_tag_get($paramsEntity); crm_core_error::debug('$entity',$entity);
        $this->assertEqual( $entity['is_error'], 1 );
        $this->assertNotNull($entity['error_message']);
    }
    
    function testIndividualEntityTagGet()
    {
        $contactId = $this->individualCreate( );
        $paramsEntity = array('contact_id' =>  $ContactId);
        $entity =& civicrm_entity_tag_get($paramsEntity); crm_core_error::debug('$entity',$entity);
        $this->assertEqual( $entity['is_error'], 0 );
        $this->assertNotNull($entity['tag_id']);
    }
    
    function testHouseholdEntityGetWithoutContactID()
    {
        $paramsEntity = array( );
        $entity =& civicrm_entity_tag_get($paramsEntity); crm_core_error::debug('$entity',$entity);
        $this->assertEqual( $entity['is_error'], 1 );
        $this->assertNotNull($entity['error_message']);
    }

    function testHouseholdEntityGet()
    {
        $contactId = $this->householdCreate( );
        $paramsEntity = array('contact_id' => $contactId); 
        $entity =& civicrm_entity_tag_get($paramsEntity); crm_core_error::debug('$entity',$entity);
        $this->assertEqual( $entity['is_error'], 0 );
        $this->assertNotNull($entity['tag_id']);
    }
     
    function testOrganizationEntityGetWithoutContactID()
    {
        $paramsEntity = array( );
        $entity =& civicrm_entity_tag_get($paramsEntity); crm_core_error::debug('$entity',$entity);
        $this->assertEqual( $entity['is_error'], 1 );
        $this->assertNotNull($entity['error_message']);
    }

    function testOrganizationEntityGet()
    {
        $contactId = $this->organizationCreate( );
        $paramsEntity = array('contact_id' => $contactId);
        $entity =& civicrm_entity_tag_get($paramsEntity); crm_core_error::debug('$entity',$entity);
        $this->assertEqual( $entity['is_error'], 0 ); 
        $this->assertNotNull($entity['tag_id']);
    }
  
    function tearDown() 
    {
    }
       
}
?>
