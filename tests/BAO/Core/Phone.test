<?php

require_once 'CiviTestCase.php';
require_once 'Contact.php';

class BAO_Core_Phone extends CiviTestCase 
{
    function get_info( ) 
    {
        return array(
                     'name'        => 'Phone BAOs',
                     'description' => 'Test all Core_BAO_Phone methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();
    }
    
    /**
     * add() method (create and update modes)
     */
    function testAdd( )
    {
        $contactId = Contact::createIndividual( );

        $params = array( );
        $params = array( 'phone'            => '(415) 222-1011 x 221',
                         'is_primary'       => 1,
                         'location_type_id' => 1,
                         'phone_type'       => 'Mobile',
                         'contact_id'       => $contactId );
        
        require_once 'CRM/Core/BAO/Phone.php';
        CRM_Core_BAO_Phone::add( $params );
        
        $phoneId = $this->assertDBNotNull( 'CRM_Core_DAO_Phone', $contactId , 'id', 'contact_id',
                                           'Database check for created phone record.' );

        $this->assertDBCompareValue( 'CRM_Core_DAO_Phone', $phoneId, 'phone', 'id', '(415) 222-1011 x 221',
                                     "Check if phone field has expected value in new record ( civicrm_phone.id={$phoneId} )." );

        // Now call add() to modify the existing phone number

        $params = array( );
        $params = array( 'id'           => $phoneId,
                         'contact_id'   => $contactId,
                         'phone'        => '(415) 222-5432' );
        
        CRM_Core_BAO_Phone::add( $params );
        
        $this->assertDBCompareValue( 'CRM_Core_DAO_Phone', $phoneId, 'phone', 'id', '(415) 222-5432',
                                     "Check if phone field has expected value in updated record ( civicrm_phone.id={$phoneId} )." );

        Contact::delete( $contactId );
    }


    /**
     * allPhones() method - get all Phones for our contact, with primary Phone first
     */

    function testAllPhones( )
    {
        $contactParams = array ( 'first_name' => 'Alan',
                                 'last_name'  => 'Smith',
                                 'phone-1'    => '(415) 222-1011 x 221',
                                 'phone-2'    => '(415) 222-5432' );
        
        $contactId = Contact::createIndividual( $contactParams );

        require_once 'CRM/Core/BAO/Phone.php';
        $Phones = CRM_Core_BAO_Phone::allPhones( $contactId );

        $this->assertEqual( count( $Phones ) , 2, 'Checking number of returned Phones.' );
        
        $firstPhoneValue = array_slice( $Phones, 0, 1 );
        
        $this->assertEqual( '(415) 222-1011 x 221',  $firstPhoneValue[0]['phone'], "Confirm primary Phone value ( {$firstPhoneValue[0]['phone']} )." ); 
        $this->assertEqual( 1,  $firstPhoneValue[0]['is_primary'], 'Confirm first Phone is primary.' ); 
        
        Contact::delete( $contactId );
    }

    /**
     * allEntityPhones() method - get all Phones for a location block, with primary Phone first
     */
    
    function testAllEntityPhones( )
    {
        // This test relies on an Event Phone Number inserted in "sample data" by GenerateData.php
        $entityElements = array ( 'entity_id'     => 2,
                                  'entity_table'  => 'civicrm_event' );

        require_once 'CRM/Core/BAO/Phone.php';
        $Phones = CRM_Core_BAO_Phone::allEntityPhones( $entityElements );

        $this->assertEqual( count( $Phones ) , 1, 'Checking number of returned Phones.' );
        
        $firstPhoneValue = array_slice( $Phones, 0, 1 );
        $this->assertEqual( '204 223-1000',  $firstPhoneValue[0]['phone'], "Confirm primary Phone value ( {$firstPhoneValue[0]['phone']} )." ); 
    }
    
}
