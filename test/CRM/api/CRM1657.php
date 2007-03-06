<?php

require_once 'api/crm.php';

class TestOfCRM1657 extends UnitTestCase 
{
    protected $_membership   = array();
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateMembership()
    {
        $contactId = 102;
        $params = array(
                        'membership_type_id' => '1',
                        'join_date'          => '2007-03-03',
                        'start_date'         => '2007-03-09',
                        'end_date'           => '2007-03-21',
                        'source'             => 'Payment',
                        'status_id'          => '2'                  
                        );
        
        $this->_membership = & crm_create_contact_membership( $params, $contactId );
        //CRM_Core_Error::debug( 'Created Membership', $this->_membership );
    }
    
    function testUpdateMembership()
    {
        $params = array(
                        'id'                 => $this->_membership['id'],
                        'membership_type_id' => '1',
                        'join_date'          => '2007-03-03',
                        'start_date'         => '2007-03-09',
                        'end_date'           => '2007-03-27',
                        'source'             => 'Donation',
                        'status_id'          => '1'               
                        );
        $membership = & crm_update_contact_membership($params);
        //CRM_Core_Error::debug( 'Updated Membership', $membership );
    }
    
    function testRelationship() 
    {
        $params = array( 'contact_id' => 102 );
        $contactA =& crm_get_contact( $params );
        
        $params = array( 'household_name' => "Anne Grant's home" );
        $contactB =& crm_get_contact( $params );
        
        // Create Relationship
        $params = array(
                        'start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),
                        'end_date' => array('d'=>'10','M'=>'3','Y'=>'2007')
                        );
        $relationship = 'Household Member of';
        $relationshipCreate = crm_create_relationship( $contactA, $contactB,
                                              $relationship, $params );
        
        // Update Relationship
        $params = array(
                        'start_date' => array('d'=>'10','M'=>'1','Y'=>'2007'),
                        'end_date'   => array('d'=>'26','M'=>'9','Y'=>'2009')
                        );
        $relationshipUpdate = crm_update_relationship($relationshipCreate ,$params);
        
        // Delete Relationship
        $relType = new CRM_Contact_BAO_RelationshipType();
        $relType->name_a_b = $relationship;
        $relType->find(true);
        
        crm_delete_relationship($contactA, $contactB, array($relType));
    }
    
    function testDeleteMembership()
    {
        $val = &crm_delete_membership($this->_membership['id']);
        $this->assertNull($val);
    }
}
?>