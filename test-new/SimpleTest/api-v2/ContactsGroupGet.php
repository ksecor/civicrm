<?php

require_once 'api/v2/GroupContact.php';

class TestOfContactsGroupGetAPIV2 extends CiviUnitTestCase 
{
    protected $_contactId ; 
    protected $_groupId;

    function setUp(){
    }
      
    function atearDown(){
        for($i=0; $i<5; $i++ ) {
            $this->contactGroupDelete( $this->_contactId[$i] );
            $this->contactDelete( $this->_contactId );
        }
        $this->groupDelete( $this->_groupId );
    }
   
    function testGetGroupContactsGroupWithoutId()
    { 
        $this->_contactId[] = $this->individualCreate( $params );
        $this->_groupId  = $this->groupCreate( );
        $params = array( );
        $groups = civicrm_contacts_group_get( $params );
        $this->assertEqual( $groups['error_message'], 'group_id is required field');
    }

    function atestGetContactsGroupFromGroupId() 
    {
        $params = array('first_name'    => 'abc' . time( ),
                        'last_name'     => 'xyz' . time( ), 
                        'contact_type'  => 'Individual',
                        'phone'         => '999999',
                        'phone_type'    => 'Phone',
                        'email'         => 'man7@yahoo.com',
                        'prefix'        => 'Mr',
                        'suffix'        => 'VII',
                        'gender'        => 'Male',
                        'do_not_trade'  => 1,
                        'preferred_communication_method' => array(
                                                                  '2' => 1,
                                                                  '3' => 1,
                                                                  '4' => 1,
                                                                  ),
                        );
        $this->_groupId  = $this->groupCreate( );
        
        for ( $i=0; $i<5; $i++ ) {
            
            $this->_contactId[] = $this->individualCreate( $params );
            $gparams = array( 'contact_id.1' => $this->_contactId[$i],
                              'group_id'     => $groupId 
                              );
            civicrm_group_contact_add( $gparams );
        }
        $params = array( 'group_id'             => $this->_groupId,
                         'return.first_name'    => 1,
                         'return.last_name'     => 1,
                         'return.phone'         => 1,
                         'return.email'         => 1
                         );
        $groups = civicrm_contacts_group_get( $params );
        CRM_Core_Error::debug( '1', $groups );
    }
}
?>