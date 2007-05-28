<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
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

    static function &systemObject( ) {
        if ( ! self::$_systemObject ) {
            self::$_systemObject =& new CRM_Core_DAO_Preferences( );
            self::$_systemObject->domain_id  = CRM_Core_Config::domainID( );
            self::$_systemObject->is_domain  = true;
            self::$_systemObject->contact_id = null;
            self::$_systemObject->find( true );
        }
        return self::$_systemObject;
    }

    static function &userObject( $userID = null ) {
        if ( ! self::$_userObject ) {
            if ( ! $userID ) {
                $session =& CRM_Core_Session::singleton( );
                $userID  =  $session->get( 'userID' );
            }
            self::$_userObject =& new CRM_Core_DAO_Preferences( );
            self::$_userObject->domain_id  = CRM_Core_Config::domainID( );
            self::$_userObject->is_domain  = false;
            self::$_userObject->contact_id = $userID;
            self::$_userObject->find( true );
        }
        return self::$_userObject;
    }

    static function locationCount( $system = true, $userID = null ) {
        if ( $system ) {
            $object = self::systemObject( );
        } else {
            $object = self::userObject( $userID );
        }

        return
            isset( self::$_systemObject->location_count ) ? self::$_systemObject->location_count : 1;
    }

    static function commonOptions( $system = true, $userID = null, $optionName ) {
        if ( $system ) {
            $object = self::systemObject( );
        } else {
            $object = self::userObject( $userID );
        }

        $optionValue = $object->$optionName;
        require_once 'CRM/Core/OptionGroup.php';
        $groupValues = CRM_Core_OptionGroup::values( $optionName );
        
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
                    $returnValues[$groupValues[$val]] = 1;
                }
            }
        }
        
        return $returnValues;
    }

    static function contactEditOptions( $system = true, $userID = null ) {
        return self::commonOptions( $system, $userID, 'contact_edit_options' );
    }

    static function contactViewOptions( $system = true, $userID = null ) {
        return self::commonOptions( $system, $userID, 'contact_view_options' );
    }

    static function advancedSearchOptions( $system = true, $userID = null ) {
        return self::commonOptions( $system, $userID, 'advanced_search_options' );
    }

    static function userDashboardOptions( $system = true, $userID = null ) {
        return self::commonOptions( $system, $userID, 'user_dashboard_options' );
    }

    static function addressOptions( $system = true, $userID = null ) {
        return self::commonOptions( $system, $userID, 'address_options' );
    }

}

?>
