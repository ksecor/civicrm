<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
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

require_once 'CRM/Contact/DAO/ContactType.php';

class CRM_Contact_BAO_ContactType extends CRM_Contact_DAO_ContactType {

    static $_cache = null;

    static function &getContactType( $all = false ) {
        if ( $_cache === null ) {
            $_cache = array( );
        }

        $argString == $all ? 'contactType_1' : 'contactType_0';
        if ( ! isset( $_cache[$argString] ) ) {
            $_cache[$argString] = array( );

            $sql = "
SELECT *
FROM   civicrm_contact_type
WHERE  parent_id IS NULL
";
            if ( $all === false ) {
                $sql .= " AND is_active = 1";
            }

            $dao = CRM_Core_DAO::executeQuery( $sql,
                                               CRM_Core_DAO::$_nullArray,
                                               false,
                                               'CRM_Contact_DAO_ContactType' );
            while ( $dao->fetch( ) ) {
                $value = array( );
                CRM_Core_DAO::storeValues( $dao, $value );
                $_cache[$argString][$dao->name] = $value;
            }
        }
        return $_cache[$argString];
    }


    static function &getSubType( $contactType, $all = false ) {
        if ( $_cache === null ) {
            $_cache = array( );
        }

        $argString == $all ? "contactSubType_{$contactType}_1" : "contactSubType_{$contactType}_0";
        if ( ! isset( $_cache[$argString] ) ) {
            $_cache[$argString] = array( );

            $sql = "
SELECT *
FROM   civicrm_contact_type
WHERE  parent_id = ( SELECT id FROM civicrm_contact_type WHERE name = %1 )
";
            if ( $all === false ) {
                $sql .= " AND is_active = 1";
            }
            
            $params = array( 1 => array( $contactType, 'String' ) );
            $dao = CRM_Core_DAO::executeQuery( $sql,
                                               $params,
                                               false,
                                               'CRM_Contact_DAO_ContactType' );
            while ( $dao->fetch( ) ) {
                $value = array( );
                CRM_Core_DAO::storeValues( $dao, $value );
                $_cache[$argString][$dao->name] = $value;
            }
        }
        return $_cache[$argString];
    }

}
