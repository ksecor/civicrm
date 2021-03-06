<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
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

class CRM_Core_Key {
    static $_key = null;

    static $_sessionID = null;

    /**
     * Generate a private key per session and store in session
     *
     * @return string private key for this session
     * @static
     * @access private
     */
    static function privateKey( ) {
        if ( ! self::$_key ) {
            $session =& CRM_Core_Session::singleton( );
            self::$_key     =  $session->get( 'qfPrivateKey' );
            if ( ! self::$_key ) {
                self::$_key =
                    md5( uniqid( mt_rand( ), true ) ) .
                    md5( uniqid( mt_rand( ), true ) );
                $session->set( 'qfPrivateKey', self::$_key );
            }
        }
        return self::$_key;
    }

    static function sessionID( ) {
        if ( ! self::$_sessionID ) {
            $session =& CRM_Core_Session::singleton( );
            self::$_sessionID = $session->get( 'qfSessionID' );
            if ( ! self::$_sessionID ) {
                self::$_sessionID = session_id( );
                $session->set( 'qfSessionID', self::$_sessionID );
            }
        }
        return self::$_sessionID;
    }

    /**
     * Generate a form key based on form name, the current user session
     * and a private key. Modelled after drupal's form API
     *
     * @param string  $value       name of the form
     * @paeam boolean $addSequence should we add a unique sequence number to the end of the key
     * 
     * @return string       valid formID
     * @static
     * @acess public
     */
    static function get( $name, $addSequence = false ) {
        $privateKey = self::privateKey( );
        $sessionID  = self::sessionID ( );
        $key = md5( $sessionID . $name . $privateKey );

        if ( $addSequence ) {
            // now generate a random number between 1 and 100K and add it to the key
            // so that we can have forms in mutiple tabs etc
            $key = $key . '_' . mt_rand( 1, 10000 );
        }
        return $key;
    }

    /**
     * Validate a form key based on the form name
     *
     * @param string $formKey 
     * @param string $name
     *
     * @return string $formKey if valid, else null
     * @static
     * @acess public
     */
    static function validate( $key, $name, $addSequence = false ) {
        if ( $addSequence ) {
            list( $k, $t ) = explode( '_', $key );
            if ( $t < 1 || $t > 10000 ) {
                return null;
            }
        } else {
            $k = $key;
        }

        $privateKey = self::privateKey( );
        $sessionID  = self::sessionID ( );
        if ( $k != md5( $sessionID . $name . $privateKey ) ) {
            return null;
        }
        return $key;
    }

}


