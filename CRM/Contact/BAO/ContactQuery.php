<?php 

/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.1                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright (c) 2005 Social Source Foundation                        | 
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
 | License along with this program; if not, contact the Social Source | 
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have | 
 | questions about the Affero General Public License or the licensing | 
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   | 
 | at http://www.openngo.org/faqs/licensing.html                      | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Social Source Foundation (c) 2005 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Core/DAO/Location.php'; 
require_once 'CRM/Core/DAO/Address.php'; 
require_once 'CRM/Core/DAO/Phone.php'; 
require_once 'CRM/Core/DAO/Email.php'; 

class CRM_Contact_BAO_ContactQuery {
    
    function &selectClause( &$params, &$returnProperties, &$fields, &$select, &$tables ) {
        $properties = array( );
        $cfIDs      = array( );

        foreach ($fields as $name => $field) {
            // if we need to get the value for this param or we need all values
            if ( CRM_Utils_Array::value( $name, $params )           ||
                 CRM_Utils_Array::value( $name, $returnProperties ) ||
                 ( ! $params ) ) {
                $cfID = CRM_Utils_Array::value( 'custom_field_id', $field );
                if ( $cfID ) {
                    $cfIDs[] = $cfID;
                } else if ( isset( $field['where'] ) ) {
                    list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 ); 
                    if ( isset( $tableName ) ) { 
                        $select[] = $field['where']. ' as ' . $name;
                        $tables[$tableName] = 1;
                    }
                }
            }
        }

        if ( ! empty( $cfIDs ) ) {
            $customSelect = $customFrom = null;
            CRM_Core_BAO_CustomGroup::selectFromClause( $cfIDs, $customSelect, $customFrom ); 
            if ( $customSelect ) {
                $select[] = $customSelect;
                $tables['civicrm_custom_value'] = $customFrom;
            }
        }
    }

    function query( $params, $returnProperties ) {
        $fields =& CRM_Contact_BAO_Contact::importableFields( 'Individual' );

        $select = array( 'civicrm_contact.id as contact_id' );
        $tables = array( 'civicrm_contact' => 1 );
        
        self::selectClause( $params,
                            $returnProperties,
                            $fields, $select, $tables );
        
        $select = 'SELECT ' . implode( ', ', $select );
        $where  = self::whereClause( $params, $fields, $select, $tables );
        $from   = CRM_Contact_BAO_Contact::fromClause( $tables );
        if ( $where ) {
            $where = "WHERE $where";
        }
        
        $sql = "$select $from $where";
        print_r( $sql );
        echo "\n\n";
    }

    function whereClause( &$params, &$fields, &$select, &$tables ) {
        $where = array( );

        $id = CRM_Utils_Array::value( 'id', $params );
        if ($id ) {
            $where[] = "civicrm_contact.id = $id";
        }

        foreach ( $fields as $name => $field ) { 
            $value = CRM_Utils_Array::value( $name, $params );
                
            if ( ! isset( $value ) || $value == null ) {
                continue;
            }

            if ( $cfID = CRM_Core_BAO_CustomField::getKeyID( $field['name'] ) ) { 
                $params = array( );
                $params[$cfID] = $value; 
                $sql = CRM_Core_BAO_CustomValue::whereClause($params);   
                if ( $sql ) {  
                    $where[] = $sql;  
                }  
            } else {
                if ( $field['name'] === 'state_province_id' && is_numeric( $value ) ) {
                    $states =& CRM_Core_PseudoConstant::stateProvince(); 
                    $value  =  $states[$value]; 
                } else if ( $field['name'] === 'country_id' && is_numeric( $value ) ) { 
                    $countries =& CRM_Core_PseudoConstant::country( ); 
                    $value     =  $countries[$value]; 
                } 

                $value = strtolower( $value ); 
                $where[] = 'LOWER(' . $field['where'] . ') LIKE "%' . addslashes( $value ) . '%"';  
                
                list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 );  
                if ( isset( $tableName ) ) { 
                    $tables[$tableName] = 1;  
                } 
            } 
        }

        return implode( ' AND ', $where );
    }

}
