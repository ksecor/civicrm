<?php

require_once 'CiviTestCase.php';
require_once 'Contact.php';

class BAO_Core_OpenID extends CiviTestCase 
{
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
        
        $openIdURL = "http://test-username.civicrm.org/";
        $params = array('contact_id'       => $contactId, 
                        'location_type_id' => 1,
                        'openid'           => $openIdURL,
                        'is_primary'       => 1,
                        );
        
        require_once 'CRM/Core/BAO/OpenID.php';
        $openObject = CRM_Core_BAO_OpenID::add( $params );
        
        $openId = $openObject->id;
        
        $this->assertDBNotNull( 'CRM_Core_DAO_OpenID', $openIdURL, 'id', 'openid',
                                'Database check for created OpenID.' );
        
        // Now call add() to modify an existing open-id record
        
        $params = array( 'id'               => $openId,
                         'contact_id'       => $contactId,
                         'is_bulkmail'      => 1,
                         'allowed_to_login' => 1 );
        
        CRM_Core_BAO_OpenID::add( $params );
        
        $allowedToLogin = $this->assertDBNotNull( 'CRM_Core_DAO_OpenID', $openId, 'allowed_to_login', 'id',
                                                  'Database check on updated OpenID record.' );
        $this->assertEqual( $allowedToLogin, 1, 'Verify allowed_to_login value is 1.');
        
        Contact::delete( $contactId );
    }
    
    /**
     * ifAllowedToLogin() method (set and reset allowed_to_login)
     */
    function testIfAllowedToLogin( )
    {
        $contactId = Contact::createIndividual( );
        $openIdURL = "http://test-username.civicrm.org/";
        
        $params = array('contact_id'       => $contactId, 
                        'location_type_id' => 1,
                        'openid'           => $openIdURL,
                        'is_primary'       => 1,
                        );
        
        require_once 'CRM/Core/BAO/OpenID.php';
        $openObject = CRM_Core_BAO_OpenID::add( $params );
        
        $openId = $openObject->id;
        $this->assertDBNotNull( 'CRM_Core_DAO_OpenID', $openIdURL, 'id', 'openid',
                                'Database check for created OpenID.' );
        
        $allowedToLogin = CRM_Core_BAO_OpenID::isAllowedToLogin( $openIdURL );
        $this->assertEqual( $allowedToLogin, false, 'Verify allowed_to_login value is 0.');
        
        
        // Now call add() to modify an existing open-id record
        
        $params = array( 'id'               => $openId,
                         'contact_id'       => $contactId,
                         'is_bulkmail'      => 1,
                         'allowed_to_login' => 1 );
        
        CRM_Core_BAO_OpenID::add( $params );
        
        $allowedToLogin = CRM_Core_BAO_OpenID::isAllowedToLogin( $openIdURL );
        
        $this->assertEqual( $allowedToLogin, true, 'Verify allowed_to_login value is 1.');
        Contact::delete( $contactId );
    }
    
    /**
     * allOpenIDs() method - get all OpenIDs for the given contact
     */
    function testAllOpenIDs( )
    {
        $contactId = Contact::createIndividual( );
        
        // create first openid
        $openIdURLOne = "http://test-one-username.civicrm.org/";
        $params  = array('contact_id'       => $contactId, 
                         'location_type_id' => 1,
                         'openid'           => $openIdURLOne,
                         'is_primary'       => 1,
                         'allowed_to_login' => 1
                         );
        
        require_once 'CRM/Core/BAO/OpenID.php';
        $openObjectOne = CRM_Core_BAO_OpenID::add( $params );
        
        $openIdOne = $openObjectOne->id;
        $this->assertDBNotNull( 'CRM_Core_DAO_OpenID', $openIdURLOne, 'id', 'openid',
                                'Database check for created OpenID.' );
        
        // create second openid
        $openIdURLTwo = "http://test-two-username.civicrm.org/";
        $params  = array('contact_id'       => $contactId, 
                         'location_type_id' => 1,
                         'openid'           => $openIdURLTwo
                         );
        
        $openObjectTwo = CRM_Core_BAO_OpenID::add( $params );
        $openIdTwo = $openObjectTwo->id;
        
        $this->assertDBNotNull( 'CRM_Core_DAO_OpenID', $openIdURLTwo, 'id', 'openid',
                                'Database check for created OpenID.' );
        
        // obtain all openids for the contact
        $openIds = CRM_Core_BAO_OpenID::allOpenIDs( $contactId );
        
        // check number of openids for the contact
        $this->assertEqual( count( $openIds ), 2, 'Checking number of returned open-ids.' );
        
        // check first openid values
        $this->assertEqual( $openIdURLOne,  $openIds[$openIdOne]['openid'], 
                            'Confirm first openid value.' ); 
        $this->assertEqual( 1,  $openIds[$openIdOne]['is_primary'],       'Confirm is_primary field value.' ); 
        $this->assertEqual( 1,  $openIds[$openIdOne]['allowed_to_login'], 'Confirm allowed_to_login field value.' ); 
        
        // check second openid values
        $this->assertEqual( $openIdURLTwo,  $openIds[$openIdTwo]['openid'], 
                            'Confirm second openid value.' ); 
        $this->assertEqual( 0,  $openIds[$openIdTwo]['is_primary'], 'Confirm is_primary field value for second openid.' ); 
        $this->assertEqual( 0,  $openIds[$openIdTwo]['allowed_to_login'], 'Confirm allowed_to_login field value for second openid.' ); 
        
        Contact::delete( $contactId );
    }
}
