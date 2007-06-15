<?php

require_once 'api/v2/Contact.php';
require_once 'CRM/Utils/Array.php';

class testPublicCivicrmContactAdd extends CiviUnitTestCase 

{

    // class properties here
    // e.g protected $_participant;
    protected $_createdContacts   = array();

    protected $allparams = array(

                // civicrm_contact
                'id' => '',
                'nick_name' => '',
                'domain_id' => '',
                'contact_type' => '',
                'do_not_email' => '',
                'do_not_phone' => '',
                'do_not_mail' => '',
                'contact_sub_type' => '',
                'legal_identifier' => '',
                'external_identifier' => '',
                'sort_name' => '',
                'display_name' => '',
                'home_URL' => '',
                'image_URL' => '',
                'preferred_communication_method' => '',
                'preferred_mail_format' => '',
                'do_not_trade' => '',
                'hash' => '',
                'is_opt_out' => '',
                'source' => '',

                // civicrm_individual
                'id' => '',
                'contact_id' => '',
                'first_name' => '',
                'middle_name' => '',
                'last_name' => '',
                'prefix_id' => '',
                'suffix_id' => '',
                'greeting_type' => '',
                'custom_greeting' => '',
                'job_title' => '',
                'gender_id' => '',
                'birth_date' => '',
                'is_deceased' => '',
                'deceased_date' => '',
                'phone_to_household_id' => '',
                'email_to_household_id' => '',
                'mail_to_household_id' => '',
                                                
                // civicrm_organisation
                'id' => '',
                'contact_id' => '',
                'organization_name' => '',
                'legal_name' => '',
                'sic_code' => '',
                'primary_contact_id' => '',                

                // civicrm_household
                'id' => '',
                'contact_id' => '',
                'household_name' => '',
                'primary_contact_id' => '',
                
                );
    
    // end class properties
            
    /**
     * Prepares environment for given testcase.
     */
    function setUp() 
    {
    }

    /**
     * Cleans up environment after given testcase.
     */    
    function tearDown() 
    {
    }


    // Put test functions below. Each function's name
    // needs to start with "test", e.g. testCreateEmptyContact


    /**
     * Create contact without parameters.
     */
    function testCreateEmptyContact() 
    {
        $params = array();
        $contact = &civicrm_contact_add( $params );
        $this->assertEqual( $contact['is_error'], 1 );
        $this->assertEqual( $contact['error_message'], 'Input Parameters empty' );
    }

    /**
     * Create contact with bad contact type.
     */
    function testCreateBadTypeContact()
    {
        $params = array('email' => 'man1@yahoo.com',
                        'contact_type' => 'Does not Exist' );
        $contact = &civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 1 );
        $this->assertEqual( $contact['error_message'], 'Invalid Contact Type: Does not Exist' );
    }

    /**
     * Create Individual without required fields.
     */    
    function testCreateBadRequiredFieldsIndividual() 
    {
        $params = array('middle_name' => 'This field is not required for Individual',
                        'contact_type' => 'Individual' );
        $contact = &civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 1 );
        $this->assertEqual( $contact['error_message'], 'Required fields not found for Individual first_name' );
    }

    /**
     * Create Household without required fields.
     */
    function testCreateBadRequiredFieldsHousehold() 
    {
        $params = array('middle_name' => 'This field is not required for Household',
                        'contact_type' => 'Household' );
        $contact = &civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 1 );
        $this->assertEqual( $contact['error_message'], 'Required fields not found for Household ' );

    }

    /**
     * Create Organisation without required fields.
     */
    function testCreateBadRequiredFieldsOrganization()
    {
        $params = array('middle_name' => 'This field is not required for Organisation',
                        'contact_type' => 'Organization' );
        $contact = &civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 1 );
        $this->assertEqual( $contact['error_message'], 'Required fields not found for Organization ' );
    }


    /**
     * Create Individual with minimal set of fields.
     */    
    function testCreateEmailIndividual()
    {
        $email = 'man2@yahoo.com';
        // FIXME: make sure this is minimal set of needed data
        $params = array('email'            => $email,
                        'contact_type'     => 'Individual',
                        );
        $contact = &civicrm_contact_add( CRM_Utils_Array::array_deep_copy( $params ) );

        $this->assertEqual( $contact['is_error'], 0 );
        $this->assertNotNull( $contact['contact_id'] );

        $retrieved = &civicrm_contact_get( CRM_Utils_Array::array_deep_copy( $params ) );
        $this->assertEqual( $contact['contact_id'], $retrieved['contact_id'] );
//        $this->_assertAttributesEqual( $params, $retrieved );
        
//        $this->_verifyContactAttributes( $params );

        $this->_contacts[] = $contact['contact_id'];
    }



    function testCreateNameIndividual()
    {
        $params = array('first_name' => 'abc1',
                        'contact_type'     => 'Individual',
                        'last_name' => 'xyz1'
                        );
        $contact = &civicrm_contact_add($params);
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

    private function _verifyContactAttributes( $params ) {

    }

    private function _assertAttributesEqual( $params, $target ) {
        foreach( $params as $paramName => $paramValue ) {
            echo 'Comparing ' . $paramName . '<br>';
            $this->assertEqual( $paramValue, $target[$paramName] );
        }        
    }



}



?>