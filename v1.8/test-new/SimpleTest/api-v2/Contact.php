<?php

require_once 'api/v2/Contact.php';

class TestOfContactAPIV2 extends CiviUnitTestCase 
{
    /**
     * Assume empty database with just civicrm_data
     */
    protected $_contacts   = array();
    
    function setUp() 
    {
        // make sure this is just _41 and _data
    }
    
    function tearDown() 
    {
    }
    
    function testCreateEmptyContact() 
    {
        $params = array();
        $contact =& civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 1 );
    }
    
    function testCreateBadTypeContact()
    {
        $params = array('email' => 'man1@yahoo.com',
                        'contact_type' => 'Does not Exist' );
        $contact =& civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 1 );
    }
    
    function testCreateBadRequiredFieldsIndividual() 
    {
        $params = array('middle_name' => 'This field is not required',
                        'contact_type' => 'Individual' );
        $contact =& civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 1 );
    }
    
    function testCreateBadRequiredFieldsHousehold() 
    {
        $params = array('middle_name' => 'This field is not required',
                        'contact_type' => 'Household' );
        $contact =& civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 1 );
    }
    
    function testCreateBadRequiredFieldsOrganization() 
    {
        $params = array('middle_name' => 'This field is not required',
                        'contact_type' => 'Organization' );
        $contact =& civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 1 );
    }
    
    function testCreateEmailIndividual() 
    {
        $params = array('email'            => 'man2@yahoo.com',
                        'contact_type'     => 'Individual',
                        'location_type_id' => 1,
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }

    function testCreateNameIndividual() 
    {
        $params = array('first_name' => 'abc1',
                        'contact_type'     => 'Individual',
                        'last_name' => 'xyz1'
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateNameHousehold() 
    {
        $params = array('household_name' => 'The abc Household',
                        'contact_type' => 'Household',
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateNameOrganization() 
    {
        $params = array('organization_name' => 'The abc Organization',
                        'contact_type' => 'Organization',
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateIndividualwithEmail() 
    {
        $params = array('first_name' => 'abc3',
                        'last_name'  => 'xyz3',
                        'contact_type'     => 'Individual',
                        'email'      => 'man3@yahoo.com'
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateIndividualwithEmailLocationType() 
    {
        $params = array('first_name'    => 'abc4',
                        'last_name'     => 'xyz4',
                        'email'         => 'man4@yahoo.com',
                        'contact_type'     => 'Individual',
                        'location_type_id' => 2,
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateIndividualwithPhone() 
    {
        $params = array('first_name'    => 'abc5',
                        'last_name'     => 'xyz5',
                        'contact_type'     => 'Individual',
                        'location_type_id' => 2,
                        'phone'         => '11111',
                        'phone_type'    => 'Phone'
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateIndividualwithAll() 
    {
        $params = array('first_name'    => 'abc7',
                        'last_name'     => 'xyz7', 
                        'contact_type'  => 'Individual',
                        'phone'         => '999999',
                        'phone_type'    => 'Phone',
                        'email'         => 'man7@yahoo.com',
                        'do_not_trade'  => 1,
                        'preferred_communication_method' => array(
                                                                  '2' => 1,
                                                                  '3' => 1,
                                                                  '4' => 1,
                                                                  )
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateHouseholdDetails() 
    {
        $params = array('household_name' => 'abc8\'s House',
                        'nick_name'      => 'x House',
                        'email'          => 'man8@yahoo.com',
                        'contact_type'     => 'Household',
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }

    function testDeleteContacts() 
    {
        foreach ($this->_contacts as $id) {
            $params = array( 'contact_id' => $id );
            $result = civicrm_contact_delete( $params );
            $this->assertEqual( $result['is_error'], 0 );
        }
        
        // delete an unknown id
        $params = array( 'contact_id' => 1000567 );
        $result = civicrm_contact_delete( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }

}

?>
