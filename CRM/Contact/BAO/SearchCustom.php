<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

class CRM_Contact_BAO_SearchCustom {

    static function details( $csID, $ssID = null, $gID = null ) {
        $error = array( null, null, null );

        if ( ! $csID &&
             ! $ssID &&
             ! $gID ) {
            return $error;
        }

        $customSearchID = $csID;
        $formValues     = array( );
        if ( $ssID || $gID ) {
            if ( $gID ) {
                $ssID = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Group', $gID, 'saved_search_id' );
            }

            $formValues = CRM_Contact_BAO_SavedSearch::getFormValues( $ssID );
            $customSearchID    = CRM_Utils_Array::value( 'customSearchID',
                                                         $formValues );
        }

        if ( ! $customSearchID ) {
            return $error;
        }

        // check that the csid exists in the db along with the right file
        // and implements the right interface
        require_once 'CRM/Core/OptionGroup.php';
        $customSearchClass = CRM_Core_OptionGroup::getLabel( 'custom_search',
                                                             $customSearchID );
        if ( ! $customSearchClass ) {
            return $error;
        }

        $customSearchFile = str_replace( '_',
                                         DIRECTORY_SEPARATOR,
                                         $customSearchClass ) . '.php';
        
        $error = include_once( $customSearchFile );
        if ( $error == false ) {
            return $error;
        }

        return array( $customSearchID, $customSearchClass, $formValues );
    }

    static function contactIDSQL( $csID, $ssID ) {
        list( $customSearchID, $customSearchClass, $formValues ) =
            self::details( $csID, $ssID );

        if ( ! $customSearchID ) {
            CRM_Core_Error::fatal( 'Could not resolve custom search ID' );
        }

        // instantiate the new class
        eval( '$customClass = new ' . $customSearchClass . '( $formValues );' );

        $params = array( );
        $sql = $customClass->contactIDs( $params );
        self::validateUserSQL( $where, true );

        $dao = new CRM_Core_DAO( );
        return CRM_Core_DAO::composeQuery( $sql, $params, true, $dao );
    }

    static function fromWhereEmail( $csID, $ssID ) {
        list( $customSearchID, $customSearchClass, $formValues ) =
            self::details( $csID, $ssID );

        if ( ! $customSearchID ) {
            CRM_Core_Error::fatal( 'Could not resolve custom search ID' );
        }

        // instantiate the new class
        eval( '$customClass = new ' . $customSearchClass . '( $formValues );' );

        $params = array( );
        $from  = $customClass->from ( $params );
        $where = $customClass->where( $params );

        $dao = new CRM_Core_DAO( );
        $where = CRM_Core_DAO::composeQuery( $where, $params, true, $dao );
        self::validateUserSQL( $where, true );

        return array( $from, $where );
    }

    /**
     * Returns total number of rows for the query.
     *
     * @param 
     * @return int Total number of rows 
     * @access public
     */
    function getTotalCount( &$custom ) {
        $params = array( );
        $sql = $custom->count( $params );
        self::validateUserSQL( $sql );

        return CRM_Core_DAO::singleValueQuery( $sql, $params );
    }

    static function validateUserSQL( &$sql, $onlyWhere = false ) {
        $includeStrings = array( 'civicrm_contact', 'contact_a', 'contact_a.domain_id = ' );
        $excludeStrings = array( 'insert', 'delete', 'update' );

        if ( ! $onlyWhere ) {
            $includeStrings += array( 'select', 'from', 'where' );
        }

        foreach ( $includeStrings as $string ) {
            if ( stripos( $sql, $string ) === false ) {
                CRM_Core_Error::fatal( ts( 'Could not find "%1" string in SQL clause',
                                           array( 1 => $string ) ) );
            }
        }

        foreach ( $excludeStrings as $string ) {
            if ( stripos( $sql, $string ) !== false ) {
                CRM_Core_Error::fatal( ts( 'Found illegal "%1" string in SQL clause',
                                           array( 1 => $string ) ) );
            }
        }
    }

}

?>
