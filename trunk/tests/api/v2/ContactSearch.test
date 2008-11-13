<?php

require_once 'api/v2/Contact.php';

class testPublicCivicrmContactSearch extends CiviUnitTestCase 

{

    // class properties here
    // e.g protected $_participant;

    
    // end class properties
            
    /**
     * Prepares environment for given testcase.
     */
    function setUp() {
    }

    /**
     * Cleans up environment after given testcase.
     */    
    function tearDown() {
    }


    // Put test functions below. Each function's name
    // needs to start with "test", e.g. testCreateEmptyContact

    function testGetGroupContacts()
    {
        $retrieve = array( 'group' => array(3 => 1),
                           'return.sort_name'    => 1
                           );
        
        $contacts = civicrm_contact_search( $retrieve );
        //crm_core_error::debug('no of contacts', count($contacts));
        //crm_core_error::debug('contacts', $contacts);        
    }

}

