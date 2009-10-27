<?php

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'CiviTest/Contact.php';

class CRM_Core_BAO_OpenIDTest extends CiviUnitTestCase 
{

    # OpenID 2.0 style
    private $openid = array(
        'endpoint_url' => "http://test-site.civicrm.org/",
        'claimed_id'   => "http://test-site.civicrm.org/?id=foobar42",
        'display_id'   => "foobar42@test-site.civicrm.org"
    );

    # OpenID 1.0 style
    private $openid_two = array(
        'endpoint_url' => "http://baz33.civicrm.org/",
        'claimed_id'   => "http://baz33.civicrm.org/",
        'display_id'   => "http://baz33.civicrm.org/"
    );

    function get_info( ) 
    {
        return array(
                     'name'        => 'OpenID BAOs',
                     'description' => 'Test all Core_BAO_OpenID methods.',
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
        
        $params = array('contact_id'       => $contactId, 
                        'location_type_id' => 1,
                        'endpoint_url'     => $openid['endpoint_url'],
                        'claimed_id'       => $openid['claimed_id'],
                        'display_id'       => $openid['display_id'],
                        'is_primary'       => 1,
                        );
        
        require_once 'CRM/Core/BAO/OpenID.php';
        $openObject = CRM_Core_BAO_OpenID::add( $params );
        
        $openid_id = $openObject->id;

        $this->assertDBCompareValues( 'CRM_Core_DAO_OpenID',
            array('id' => $openid_id),
            $openid);
        
        // Now call add() to modify an existing OpenID record
        
        $params = array( 'id'               => $openid_id,
                         'contact_id'       => $contactId,
                         'is_bulkmail'      => 1,
                         'allowed_to_login' => 1 );
        
        CRM_Core_BAO_OpenID::add( $params );
        
        $allowedToLogin = $this->assertDBNotNull( 'CRM_Core_DAO_OpenID', $openid_id, 'allowed_to_login', 'id',
                                                  'Database check on updated OpenID record.' );
        $this->assertEquals( $allowedToLogin, 1, 'Verify allowed_to_login value is 1.');
        
        Contact::delete( $contactId );
    }
    
    /**
     * ifAllowedToLogin() method (set and reset allowed_to_login)
     */
    function testIfAllowedToLogin( )
    {
        $contactId = Contact::createIndividual( );
        
        $params = array('contact_id'       => $contactId, 
                        'location_type_id' => 1,
                        'endpoint_url'     => $openid['endpoint_url'],
                        'claimed_id'       => $openid['claimed_id'],
                        'display_id'       => $openid['display_id'],
                        'is_primary'       => 1,
                        );
        
        require_once 'CRM/Core/BAO/OpenID.php';
        $openObject = CRM_Core_BAO_OpenID::add( $params );
        
        $openid_id = $openObject->id;
        $this->assertDBCompareValues( 'CRM_Core_DAO_OpenID',
            array('id' => $openid_id),
            $openid);
        
        $allowedToLogin = CRM_Core_BAO_OpenID::isAllowedToLogin( $openid );
        $this->assertEquals( $allowedToLogin, false, 'Verify allowed_to_login value is 0.');
        
        
        // Now call add() to modify an existing OpenID record
        
        $params = array( 'id'               => $openid_id,
                         'contact_id'       => $contactId,
                         'is_bulkmail'      => 1,
                         'allowed_to_login' => 1 );
        
        CRM_Core_BAO_OpenID::add( $params );
        
        $allowedToLogin = CRM_Core_BAO_OpenID::isAllowedToLogin( $openid );
        
        $this->assertEquals( $allowedToLogin, true, 'Verify allowed_to_login value is 1.');
        Contact::delete( $contactId );
    }
    
    /**
     * allOpenIDs() method - get all OpenIDs for the given contact
     */
    function testAllOpenIDs( )
    {
        $contactId = Contact::createIndividual( );
        
        // create first openid
        $params  = array('contact_id'       => $contactId, 
                         'location_type_id' => 1,
                         'endpoint_url'     => $openid['endpoint_url'],
                         'claimed_id'       => $openid['claimed_id'],
                         'display_id'       => $openid['display_id'],
                         'is_primary'       => 1,
                         'allowed_to_login' => 1
                         );
        
        require_once 'CRM/Core/BAO/OpenID.php';
        $openObjectOne = CRM_Core_BAO_OpenID::add( $params );
        
        $openIdOne_id = $openObjectOne->id;
        $this->assertDBCompareValues( 'CRM_Core_DAO_OpenID',
            array('id' => $openIdOne_id),
            $openid);
        
        // create second openid
        $params  = array('contact_id'       => $contactId, 
                         'location_type_id' => 1,
                         'endpoint_url'     => $openid_two['endpoint_url'],
                         'claimed_id'       => $openid_two['claimed_id'],
                         'display_id'       => $openid_two['display_id'],
                         );
        
        $openObjectTwo = CRM_Core_BAO_OpenID::add( $params );
        $openIdTwo_id = $openObjectTwo->id;
        
        $this->assertDBCompareValues( 'CRM_Core_DAO_OpenID',
            array('id' => $openIdTwo_id),
            $openid_two);
        
        // obtain all openids for the contact
        $openIds = CRM_Core_BAO_OpenID::allOpenIDs( $contactId );
        
        // check number of openids for the contact
        $this->assertEquals( count( $openIds ), 2, 'Checking number of returned OpenIDs.' );
        
        // check first openid values
        $this->assertAttributesEquals( $openid, $openIds[$openIdOne_id] );
        $this->assertEquals( 1,  $openIds[$openIdOne_id]['is_primary'],       'Confirm is_primary field value.' );
        $this->assertEquals( 1,  $openIds[$openIdOne_id]['allowed_to_login'], 'Confirm allowed_to_login field value.' );
        
        // check second openid values
        $this->assertAttributesEquals( $openid_two, $openIds[$openIdTwo_id] );
        $this->assertEquals( 0,  $openIds[$openIdTwo_id]['is_primary'], 'Confirm is_primary field value for second openid.' );
        $this->assertEquals( 0,  $openIds[$openIdTwo_id]['allowed_to_login'], 'Confirm allowed_to_login field value for second openid.' );
        
        Contact::delete( $contactId );
    }
}
