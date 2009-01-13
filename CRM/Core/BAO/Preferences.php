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

require_once 'CRM/Core/DAO/Preferences.php';

/**
 *
 */
class CRM_Core_BAO_Preferences extends CRM_Core_DAO_Preferences {
    static private $_systemObject = null;

    static private $_userObject   = null;

    static private $_mailingPref  = null;

    static function &systemObject( ) {
        if ( ! self::$_systemObject ) {
            self::$_systemObject =& new CRM_Core_DAO_Preferences( );
            self::$_systemObject->is_domain  = true;
            self::$_systemObject->contact_id = null;
            self::$_systemObject->find( true );
        }
        return self::$_systemObject;
    }

    static function &mailingPreferences( ) {
        if ( ! self::$_mailingPref ) {
            $mailingPref =& new CRM_Core_DAO_Preferences( );
            $mailingPref->is_domain  = true;
            $mailingPref->contact_id = null;
            $mailingPref->find( true );
            if ( $mailingPref->mailing_backend ) { 
                self::$_mailingPref = unserialize( $mailingPref->mailing_backend );
            }
        }
        return self::$_mailingPref;
    }


    static function &userObject( $userID = null ) {
        if ( ! self::$_userObject ) {
            if ( ! $userID ) {
                $session =& CRM_Core_Session::singleton( );
                $userID  =  $session->get( 'userID' );
            }
            self::$_userObject =& new CRM_Core_DAO_Preferences( );
            self::$_userObject->is_domain  = false;
            self::$_userObject->contact_id = $userID;
            self::$_userObject->find( true );
        }
        return self::$_userObject;
    }

    static function value( $name, $system = true, $userID = null ) {
        if ( $system ) {
            $object = self::systemObject( );
        } else {
            $object = self::userObject( $userID );
        }

        if ( $name == 'address_sequence' ) {
            return self::addressSequence( self::$_systemObject->address_format );
        } else if ( $name == 'mailing_sequence' ) {
            return self::addressSequence( self::$_systemObject->mailing_format );
        } 

        return self::$_systemObject->$name;
    }

    static function addressSequence( $format ) {
        // also compute and store the address sequence
        $addressSequence = array('address_name',
                                 'street_address',
                                 'supplemental_address_1',
                                 'supplemental_address_2',
                                 'city',
                                 'county',
                                 'state_province',
                                 'postal_code',
                                 'country');
        
        // get the field sequence from the format
        $newSequence = array();
        foreach($addressSequence as $field) {
            if (substr_count($format, $field)) {
                $newSequence[strpos($format, $field)] = $field;
            }
        }
        ksort($newSequence);
        
        // add the addressSequence fields that are missing in the addressFormat
        // to the end of the list, so that (for example) if state_province is not
        // specified in the addressFormat it's still in the address-editing form
        $newSequence = array_merge($newSequence, $addressSequence);
        $newSequence = array_unique($newSequence);
        return $newSequence;
    }

    static function valueOptions( $name, $system = true, $userID = null, $localize = false ) {
        if ( $system ) {
            $object = self::systemObject( );
        } else {
            $object = self::userObject( $userID );
        }

        $optionValue = $object->$name;
        require_once 'CRM/Core/OptionGroup.php';
        $groupValues = CRM_Core_OptionGroup::values( $name, false, false, $localize, null, 'name' );

        $returnValues = array( );
        foreach ( $groupValues as $gn => $gv ) {
            $returnValues[$gv] = 0;
        }
        if ( ! empty( $optionValue ) ) { 
            require_once 'CRM/Core/BAO/CustomOption.php';
            $dbValues = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,
                                 substr( $optionValue, 1, -1 ) );
            if ( ! empty( $dbValues ) ) {
                foreach ( $dbValues as $key => $val ) {
                    if ( CRM_Utils_Array::value( $val, $groupValues) ) {
                        $returnValues[$groupValues[$val]] = 1;
                    }
                }
            }
        }
        
        return $returnValues;
    }

}


