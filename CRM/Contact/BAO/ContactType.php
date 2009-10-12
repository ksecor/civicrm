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


    static function &subTypeInfo( $contactType = null, $all = false ) {
        static $_cache = null;

        if ( $_cache === null ) {
            $_cache = array( );
        }

        $argString = md5( serialize( func_get_args( ) ) );
        if ( ! isset( $_cache[$argString] ) ) {
            $_cache[$argString] = array( );

            if ( $contactType && !is_array( $contactType ) ) {
                $contactType = array( $contactType );
            }

            $ctWHERE = '';
            if ( ! empty($contactType) ) {
                $ctWHERE = " AND parent.name IN ('" . implode( "','" , $contactType ) . "')" ;
            }

            $sql = "
SELECT subtype.*, parent.name as parent
FROM   civicrm_contact_type subtype
INNER JOIN civicrm_contact_type parent ON subtype.parent_id = parent.id
WHERE  subtype.name IS NOT NULL AND subtype.parent_id IS NOT NULL {$ctWHERE}
";
            if ( $all === false ) {
                $sql .= " AND subtype.is_active = 1";
            }
            
            $dao = CRM_Core_DAO::executeQuery( $sql, array( ), 
                                               false, 'CRM_Contact_DAO_ContactType' );
            while ( $dao->fetch( ) ) {
                $value = array( );
                CRM_Core_DAO::storeValues( $dao, $value );
                $value['parent'] = $dao->parent;
                $_cache[$argString][$dao->name] = $value;
            }
        }
        return $_cache[$argString];
    }

    static function subTypes( $contactType = null, $all = false ) {
        return array_keys( self::subTypeInfo( $contactType, $all ) );
    }

    static function subTypePairs( $contactType = null, $all = false ) {
        $subtypes = self::subTypeInfo( $contactType, $all );

        $pairs = array( );
        foreach ( $subtypes as $name => $info ) {
            $pairs[$name] = $info['label'];
        }
        return $pairs;
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
        return in_array( $subType, self::subTypes( ) );
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

    static function suppressSubTypes( &$subTypes ) {
        $subTypes = array_diff( $subTypes, self::subTypes( ) );
        return $subTypes;
    }

    static function isExtendsContactType( $subType, $contactType ) {
        return in_array( $subType, self::subTypes( $contactType ) );
    }

    /**
     *
     *function to create shortcuts menu for contactTypes
     *
     *@return array  of contactTypes
     *@static
     *
     */
    
    static  function getCreateNewList ( ) {
        require_once 'CRM/Core/DAO.php';
        $shortCuts    = array( );
        $contactTypes = self::getSelectElements(  ); 
        foreach( $contactTypes as $key => $value ) {
            if( $key ) {
                $typeValue = explode( CRM_Core_DAO::VALUE_SEPARATOR, $key );
                $typeUrl   = "ct=" . CRM_Utils_Array::value( '0', $typeValue );
                if( $csType = CRM_Utils_Array::value( '1', $typeValue ) ) { 
                    $typeUrl .= "&cst=$csType";
                }
                $shortCuts[]  = array(
                                      'path'  => "civicrm/contact/add",
                                      'query' => "$typeUrl&reset=1",
                                      'ref'   => "new-$value",
                                      'title' => ts( "%1", array( 1=>$value ) )
                                      );          
            }
        }
        return $shortCuts;
    }
    
}
