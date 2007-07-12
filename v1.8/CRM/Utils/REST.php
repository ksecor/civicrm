<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * This class handles all REST client requests.
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Utils_REST
{
    /**
     * Number of seconds we should let a soap process idle
     * @static
     */
    static $rest_timeout = 0;
    
    /**
     * Cache the actual UF Class
     */
    public $ufClass;

    /**
     * Class constructor.  This caches the real user framework class locally,
     * so we can use it for authentication and validation.
     *
     * @param  string $uf       The userframework class
     */
    public function __construct() {
        // any external program which call SoapServer is responsible for
        // creating and attaching the session
        $args = func_get_args( );
        $this->ufClass = array_shift( $args );
    }

    /**
     * Simple ping function to test for liveness.
     *
     * @param string $var   The string to be echoed
     * @return string       $var
     * @access public
     */
    public function ping($var) {
        $session =& CRM_Core_Session::singleton();
        $key = $session->get('key');
        $session->set( 'key', $var );
        return self::simple( array( 'message' => "PONG: $var ($key)" ) );
    }


    /**
     * Verify a REST key
     *
     * @param string $key   The soap key generated by authenticate()
     * @return none
     * @access public
     */
    public function verify($key) {
        $session =& CRM_Core_Session::singleton();

        $rest_key = $session->get('rest_key');
        
        if ( $key !== sha1($rest_key) ) {
            return false;
        }
        
        $t = time();
        if ( self::$rest_timeout && 
             $t > ($session->get('rest_time') + self::$rest_timeout)) {
            return false;
        }
        
        /* otherwise, we're ok.  update the timestamp */
        $session->set('rest_time', $t);
        return true;
    }
    
    /**
     * Authentication wrapper to the UF Class
     *
     * @param string $name      Login name
     * @param string $pass      Password
     * @return string           The REST Client key
     * @access public
     * @static
     */
    public function authenticate($name, $pass) {
        eval ('$result =& CRM_Utils_System_Drupal::authenticate($name, $pass);');

        if (empty($result)) {
            return self::error( ts( 'Could not authenticate user, invalid name / password' ) );
        }
        
        $session =& CRM_Core_Session::singleton();
        $session->set('rest_key', $result[2]);
        $session->set('rest_time', time());
        
        return self::simple( array( 'key' => sha1( $result[2] ) ) );
    }

    function error( $message = 'Unknown Error' ) {
        $values =
            array( 'error_message' => $message,
                   'is_error'      => 1 );
        return $values;
    }

    function simple( $params ) {
        $values  = array( 'is_error' => 0 );
        $values += $params;
        return $values;
    }

    function run( &$config ) {
        $result = self::handle( $config );

        return self::output( $config, $result );
    }

    function output( &$config, &$result ) {
        $hier = false;
        if ( is_scalar( $result ) ) {
            if ( ! $result ) {
                $result = 0;
            }
            $result = self::simple( array( 'result' => $result ) );
        } else if ( is_array( $result ) ) {
            if ( CRM_Utils_Array::isHierarchical( $result ) ) {
                $hier = true;
            } else if ( ! array_key_exists( 'is_error', $result ) ) {
                $result['is_error'] = 0;
            }
        } else {
            $result = self::error( ts( 'Could not interpert return values from function' ) );
        }

        if ( CRM_Utils_Array::value( 'json', $_GET ) ) {
            require_once 'Services/JSON.php';
            $json =& new Services_JSON( );
            return $json->encode( $result ) . "\n";
        }
        
        $xml = "<?xml version=\"1.0\"?>
<ResultSet xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
";
        // check if this is a single element result (contact_get etc)
        // or multi element
        if ( $hier ) {
            foreach ( $result as $n => $v ) {
                $xml .= "<Result>\n" . CRM_Utils_Array::xml( $v ) . "</Result>\n";
            }
        } else {
            $xml .= "<Result>\n" . CRM_Utils_Array::xml( $result ) . "</Result>\n";
        }

        $xml .= "</ResultSet>\n";
        return $xml;
    }

    function handle( $config ) {

        $q = $_GET['q'];
        $args = explode( '/', $q );
        if ( $args[0] != 'civicrm' ) {
            return self::error( ts( 'Unknown function invocation' ) );
        }

        require_once 'CRM/Utils/Request.php';

        $store = null;
        if ( $args[1] == 'login' ) {
            $name = CRM_Utils_Request::retrieve( 'name', 'String', $store, false, 'GET' );
            $pass = CRM_Utils_Request::retrieve( 'pass', 'String', $store, false, 'GET' );
            if ( empty( $name ) ||
                 empty( $pass ) ) {
                return self::error( ts( 'Invalid name and password' ) );
            }
            return self::authenticate( $name, $pass );
        } else {
            $key = CRM_Utils_Request::retrieve( 'key', 'String', $store, false, 'GET' );
            if ( ! self::verify( $key ) ) {
                return self::error( ts( 'session keys do not match, please re-auth' ) );
            }
        }

        $params =& self::buildParamList( );

        switch ( $args[1] ) {
        
        case 'contact':
            return self::contact( $config, $args, $params );

        case 'constants':
            return self::constant( $config, $args, $params );
            
        case 'group_contact':
            return self::groupContact( $config, $args, $params );
            
        case 'entity_tag':
            return self::entityTag( $config, $args, $params );

        default:
            return self::error( ts( 'Unknown function invocation' ) );
        }

    }

    function &buildParamList( ) {
        $params = array( );

        $skipVars = array( 'q'   => 1,
                           'key' => 1 );

        foreach ( $_GET as $n => $v ) {
            if ( ! array_key_exists( $n, $skipVars ) ) {
                $params[$n] = $v;
            }
        }

        return $params;
    }

    function contact( &$config, &$args, &$params ) {
        require_once 'api/v2/Contact.php';

        switch ( $args[2] ) {
        case 'add':
        case 'get':
        case 'delete':
        case 'search':
            $fnName = "civicrm_contact_{$args[2]}";
            $result = $fnName( $params );
            if ( $result === false ) {
                return self::error( ts( 'Unknown error' ) );
            } else {
                return $result;
            }

        default:
            return self::error( ts( 'Unknown function called' ) );
        }
    }

    function constant( &$config, &$args, &$params ) {
        require_once 'api/v2/Constant.php';

        $values = civicrm_constant_get( $args[2] );
        if ( $values['is_error'] ) {
            return $values;
        }

        // format this into a hierarchical array
        $result = array( );
        $id = $args[2] . '_id';
        foreach ( $values as $n => $v ) {
            $result[] = array( $id    => $n,
                               'name' => $v );
        }
        return $result;
    }

    function groupContact( &$config, &$args, &$params ) {
        require_once 'api/v2/GroupContact.php';

        switch ( $args[2] ) {
        case 'add':
        case 'get':
        case 'remove':
            $fnName = "civicrm_group_contact_{$args[2]}";
            $result = $fnName( $params );
            if ( $result === false ) {
                return self::error( ts( 'Unknown error' ) );
            } else {
                return $result;
            }

        default:
            return self::error( ts( 'Unknown function called' ) );
        }
    }

    function entityTag( &$config, &$args, &$params ) {
        require_once 'api/v2/EntityTag.php';

        switch ( $args[2] ) {
        case 'add':
        case 'get':
        case 'remove':
            $fnName = "civicrm_entity_tag_{$args[2]}";
            $result = $fnName( $params );
            if ( $result === false ) {
                return self::error( ts( 'Unknown error' ) );
            } else {
                return $result;
            }

        default:
            return self::error( ts( 'Unknown function called' ) );
        }
    }

}

?>
