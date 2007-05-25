<?php

require_once 'api/v2/Membership.php';
require_once 'api/v2/Contact.php';

class TestOfContactMembershipAPIV2 extends UnitTestCase 
{
    protected $_membership   = array();
    protected $_individual;

        
    function setUp() 
        {
        }
    
    function tearDown() 
        {
        }
    
    function testCreateContact()
        {
            $params = array(
                            'first_name'    => 'jyoti',
                            'last_name'     => 'ahuja',
                            'location_type' => 'Home',
                            'email'         => 'jprrra@y.com',
                            'contact_type'  => 'Individual'
                            );
            $contact =& civicrm_contact_add($params);
            
            $this->assertEqual( $contact['is_error'], 0  );
            $this->_individual = $contact;
           
        }
    
    function testCreateWrongMembershipWithoutId()
        {
            $params = array(
                            'membership_type_id' => '1',
                            'join_date'          => '2006-01-21',
                            'start_date'         => '2006-01-21',
                            'end_date'           => '2006-12-21',
                            'source'             => 'Payment',
                            'status_id'          => '2'                       
                        );
            
            $membership = &civicrm_contact_membership_create($params);
            $this->assertEqual( $membership['is_error'], 1 );
            
        }
    
    function testCreateMembershipWithoutParams()
        {
            
            $params = array();	
            $membership = &civicrm_contact_membership_create($params);
            $this->assertEqual( $membership['is_error'], 1 );
        }
    
    function testCreateMembership()
        {
            $params = array(
                            'contact_id'         => $this->_individual['contact_id'],
                            'membership_type_id' => '1',
                            'join_date'          => '2006-01-21',
                            'start_date'         => '2006-01-21',
                            'end_date'           => '2006-12-21',
                            'source'             => 'Payment',
                            'status_id'          => '2'                       
                            );
            
            $this->_membership = &civicrm_contact_membership_create($params);
            $this->assertEqual( $this->_membership['is_error'], 0 );
        }
    
    function testUpdateWrongMembershipWithoutId()
        {
            $params = array(
                            'membership_type_id' => '2',
                            'join_date'          => '2007-01-21',
                            'start_date'         => '2007-01-21',
                            'end_date'           => '2007-12-21',
                            'source'             => 'Donation',
                            'is_override'        => '1',
                            'status_id'          => '3'                       
                            );
            
            $membership = &civicrm_contact_membership_update($params);
            $this->assertEqual( $membership['is_error'], 1 );

        }

    function testUpdateMembership()
        {
            $params = array(
                            'id'                 =>  $this->_membership['id'],
                            'membership_type_id' => '2',
                            'join_date'          => '20060125',
                            'start_date'         => '20060125',
                            'end_date'           => '20061225',
                            'source'             => 'Donation',
                            'status_id'          => '2'                       
                            );	
            $membership = &civicrm_contact_membership_update($params);

            $this->assertEqual($membership['id'],$this->_membership['id']);
            $this->assertEqual($membership['membership_type_id'],'2');
            $this->assertEqual($membership['join_date'],'20060125');
            $this->assertEqual($membership['start_date'],'20060125');
            $this->assertEqual($membership['end_date'],'20061225');
            $this->assertEqual($membership['source'],'Donation');
            $this->assertEqual($membership['status_id' ],'2');                          
            $this->_membership = $membership ;
        }
    
    function testGetMemberships()
        {
            $contactId = $this->_individual['contact_id'];
            
            $membership = & civicrm_contact_memberships_get($contactId);
            
            $this->assertEqual($membership[$contactId][$this->_membership['id']]['id'],$this->_membership['id']); 
            $this->assertEqual($membership[$contactId][$this->_membership['id']]['membership_type_id'],'2');
            $this->assertEqual($membership[$contactId][$this->_membership['id']]['join_date'],'2006-01-25');
            $this->assertEqual($membership[$contactId][$this->_membership['id']]['start_date'],'2006-01-25');
            $this->assertEqual($membership[$contactId][$this->_membership['id']]['end_date'],'2006-12-25');
            $this->assertEqual($membership[$contactId][$this->_membership['id']]['source'],'Donation');
            $this->assertEqual($membership[$contactId][$this->_membership['id']]['status_id'],'2');   
        }
    
    function testDeleteMembership()
        {
            $val = &civicrm_membership_delete($this->_membership['id']);
            $this->assertEqual( $val['is_error'], 0);
        }
    
    function testDeleteContact()
        {
            $val = &civicrm_contact_delete( $this->_individual);
            $this->assertEqual($val['is_error'], 0);
        }
    
    
   }
  