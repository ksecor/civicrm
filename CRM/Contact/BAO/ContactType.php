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


    static function &getContactType( $all = false ) {
        static $_cache = null;

        if ( $_cache === null ) {
            $_cache = array( );
        }

        $argString = md5( serialize( func_get_args( ) ) );
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
        static $_cache = null;

        if ( $_cache === null ) {
            $_cache = array( );
        }

        $argString = md5( serialize( func_get_args( ) ) );
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

    static function &getSelectElements( $all = false ) {
        static $_cache = null;

        if ( $_cache === null ) {
            $_cache = array( );
        }

        $argString = md5( serialize( func_get_args( ) ) );
        if ( ! isset( $_cache[$argString] ) ) {
            $_cache[$argString] = array( );

            $sql = "
SELECT    c.name as child_name , c.label as child_label , c.id as child_id,
          p.name as parent_name, p.label as parent_label, p.id as parent_id
FROM      civicrm_contact_type c
LEFT JOIN civicrm_contact_type p ON ( c.parent_id = p.id )
WHERE     ( c.name IS NOT NULL )
";

            if ( $all === false ) {
                $sql .= "
AND   c.is_active = 1
AND   ( p.is_active = 1 OR p.id IS NULL )
";
            }
            $sql .= " ORDER BY c.id";
            
            $values = array( );
            $dao = CRM_Core_DAO::executeQuery( $sql );
            while ( $dao->fetch( ) ) {
                if ( ! empty( $dao->parent_id ) ) {
                    $key   = $dao->parent_name . CRM_Core_DAO::VALUE_SEPARATOR . $dao->child_name;
                    $label = "  -- {$dao->child_name}";
                    $pName = $dao->parent_name;
                } else {
                    $key   = $dao->child_name;
                    $label = $dao->child_label;
                    $pName = $dao->child_name;
                }

                if ( ! isset( $values[$pName] ) ) {
                    $values[$pName] = array( );
                }
                $values[$pName][] = array( 'key' => $key, 'label' => $label );
            }

            $selectElements = array('' => ts('- select -') );
            foreach ( $values as $pName => $elements ) {
                foreach ( $elements as $element ) {
                    $selectElements[$element['key']] = $element['label'];
                }
            }
            $_cache[$argString] = $selectElements;
        }
        return $_cache[$argString];
    }

    static function isaSubType( $subType ) {
        return in_array( $subType, 
                         CRM_Core_PseudoConstant::contactSubTypes( null, false, true ) );
    }

    static function getBasicType( $subType ) {
        static $_cache = null;
        if ( $_cache === null ) {
            $_cache = array( );
        }

        if ( ! array_key_exists( $subType, $_cache ) ) {
            $sql = "
SELECT name
FROM   civicrm_contact_type
WHERE  id = ( SELECT parent_id FROM civicrm_contact_type WHERE name = %1 )
";
            
            $params = array( 1 => array( $subType, 'String' ) );
            $_cache[$subType] = CRM_Core_DAO::singleValueQuery( $sql, $params );
        }
        return $_cache[$subType];
    }
}
