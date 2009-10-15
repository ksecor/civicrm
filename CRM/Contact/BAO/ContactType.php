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

     /**
     *
     *function to retrieve basic contact type information.
     *
     *@return  array of basic contact types information.
     *@static
     *
     */
    
    static function &contactTypeInfo( $all = false ) {
        static $_cache = null;
        
        if ( $_cache === null ) {
            $_cache = array( );
        }

        $argString = $all ? '1' : '0';
        if ( ! array_key_exists( $argString, $_cache ) ) {
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

    /**
     *
     *function to  retrieve  all basic contact types.
     *
     *@return  array of basic contact types
     *@static
     *
     */ 

    static function contactTypes( $all = false ) {
        return array_keys( self::contactTypeInfo( $all ) );
    }

    /**
     *
     *function to retrieve basic contacttypes & subtypes.
     *
     *@return  array of basic contact types + all subtypes.
     *@static
     *
     */

    static function contactSubTypes( $all = false ) {
        return array_merge( self::contactTypes( $all ),
                            self::subTypes( null, $all ) );
    }
    
    /**
     *
     *function to retrieve all subtypes Information.
     *
     *@param array $contactType.
     *@return  array of sub type information
     *@static
     *
     */
     static function &subTypeInfo( $contactType = null, $all = false ) {
        static $_cache = null;

        if ( $_cache === null ) {
            $_cache = array( );
        }
        if ( $contactType && !is_array( $contactType ) ) {
            $contactType = array( $contactType );
        }

        $argString = $all ? '1_' : '0_';
        if ( ! empty( $contactType ) ) {
            $argString .= implode( "_" , $contactType );
        }

        if ( ! array_key_exists( $argString, $_cache ) ) {
            $_cache[$argString] = array( );

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

    /**
     *
     *function to  retrieve all subtypes
     *
     *@param array $contactType.
     *@return  list of all subtypes OR list of subtypes associated to
     *a given basic contact type  
     *@static
     *
     */
 
    static function subTypes( $contactType = null, $all = false ) {
        return array_keys( self::subTypeInfo( $contactType, $all ) );
    }

    /**
     *
     *function to retrieve subtype pairs with name as 'subtype-name' and 'label' as value
     *
     *@param array $contactType.
     *@return list of subtypes with name as 'subtype-name' and 'label' as value
     *@static
     *
     */


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

        $argString = $all ? '1' : '0';
        if ( ! array_key_exists( $argString, $_cache ) ) {
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

    /**
     * function to check if a given type is a subtype
     *
     *@param string $subType contact subType.
     *@return  boolean true if subType, false otherwise.
     *@static
     *
     */
   
    static function isaSubType( $subType ) {
        return in_array( $subType, self::subTypes( ) );
    }
    
    /**
     *function to retrieve the basic contact type associated with
     *given subType. 
     *
     *@param array/string $subType contact subType.
     *@return array/string of basicTypes.
     *@static
     *
     */

    static function getBasicType( $subType ) { 
        static $_cache = null;
        if ( $_cache === null ) {
            $_cache = array( );
        }
        
        $isArray = true;
        if ( $subType && !is_array( $subType ) ) {
            $subType = array( $subType );
            $isArray = false;
        }
        $argString = implode( "_" , $subType );

        if ( ! array_key_exists( $argString, $_cache ) ) {
            $_cache[$argString] = array( );
            
            $sql = "
SELECT subtype.name as contact_subtype, type.name as contact_type 
FROM   civicrm_contact_type subtype
INNER JOIN civicrm_contact_type type ON ( subtype.parent_id = type.id )
WHERE  subtype.name IN ('".implode("','",$subType)."' )";
            $dao = CRM_Core_DAO::executeQuery( $sql );
            while( $dao->fetch( ) ) {
                if( !$isArray ) { 
                    $_cache[$argString] = $dao->contact_type;
                    break;
                }
                $_cache[$argString][$dao->contact_subtype] = $dao->contact_type;
            }
        } 
        return $_cache[$argString];
    }
    
    /**
     *
     *function to suppress all subtypes present in given array. 
     *
     *@param array $subType contact subType.
     *@return array of suppresssubTypes .
     *@static
     *
     */
   
    static function suppressSubTypes( &$subTypes ) {
        $subTypes = array_diff( $subTypes, self::subTypes( ) );
        return $subTypes;
    }

    /**
     *
     *function to verify if a given subtype is associated with a given basic contact type.
     *
     *@param  string  $subType contact subType
     *@param  string  $contactType contact Type
     *@return boolean true if contact extends, false otherwise.
     *@static
     *
     */
 
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
