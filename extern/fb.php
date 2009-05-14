<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

class CRM_Facebook {

    protected $_key;
    protected $_secret;
    protected $_auth;
    protected $_callback;

    function __construct( ) {
        require_once '../civicrm.config.php';
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton( );

        require_once 'facebook/facebook.php';
        require_once 'facebook/facebookapi_php5_restlib.php';

        $this->_key    = 'bb9e5704609d0757b98be31da2108105';
        $this->_secret = 'b0253a9030ee34147db2c5eba615f381';

        require_once 'CRM/Utils/Request.php';
        $this->_auth   = CRM_Utils_Request::retrieve( 'auth_token', 'String',
                                                      CRM_Core_DAO::$_nullObject );

        $this->_callback = $config->userFrameworkResourceURL . 'extern/fb.php';
    }

    function process( ) {
        if ( ! $this->_auth ) {
            $this->login( );
        } else {
            $this->run( );
        }
    }

    function login( ) {
        $facebook = new Facebook( $this->_key,
                                  $this->_secret );
        $user = $facebook->require_login();

        // catch the exception that gets thrown if the cookie has 
        // an invalid session_key in it
        try {
            if ( ! $facebook->api_client->users_isAppAdded( ) ) {
                $facebook->redirect( $facebook->get_add_url( ) );
            }
        } catch ( Exception $ex ) {
            // this will clear cookies for your application and 
            // redirect them to a login prompt
            $facebook->set_user( null, null) ;
            $facebook->redirect( $this->_callback );
        }
    }

    function run( ) {
        $api = new FacebookRestClient( $this->_key,
                                       $this->_secret );
        
        $sessionKey = $api->auth_getSession( $this->_auth );
        $friendList = $api->friends_get( );
        $friends = implode( ',', array_slice( $friendList, 0, 10 ) );
        $info = $api->users_getInfo( $friends, 'first_name,last_name,sex,birthday,current_location' );
        CRM_Core_Error::debug( 'friends', $info );
    }
}

$civicrmFace = new CRM_Facebook( );
$civicrmFace->process( );

