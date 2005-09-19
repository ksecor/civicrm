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

class CRM_Contact_BAO_Query {

    /**
     * Given a list of conditions in params and a list of desired
     * return Properties generate the required select and from
     * clauses. Note that since the where clause introduces new
     * tables, the initial attempt also retrieves all variables used
     * in the params list
     *
     * @param array $params           associative array of conditions
     * @param array $returnProperties associative array of values that
     * need to be returned
     * @param array $fields           associative array of fields in
     * the data model (typically retrieved from an xml file)
     * @param array $select           associative array of select
     * clauses
     * @param array $tables           associative array of from
     * clauses
     *
     * @return void
     * @access public
     * @static
     */
    static function &selectClause( &$params, &$returnProperties, &$fields, &$select, &$tables ) {
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

    /** 
     * Given a list of conditions in params and a list of desired 
     * return Properties generate the required query
     * 
     * @param array $params           associative array of conditions 
     * @param array $returnProperties associative array of values that
     * need to be returned 
     * 
     * @return the sql string for that query (this will most likely
     * change soon)
     * @access public 
     * @static 
     */ 
    static function query( $params, $returnProperties ) {
        $fields =& CRM_Contact_BAO_Contact::importableFields( 'Individual' );

        $select = array( 'civicrm_contact.id as contact_id' );
        $tables = array( 'civicrm_contact' => 1 );
        
        self::selectClause( $params,
                            $returnProperties,
                            $fields, $select, $tables );
        
        $select = 'SELECT ' . implode( ', ', $select );
        $where  = self::whereClause( $params, $fields, $select, $tables );
        $from   = CRM_Contact_BAO_Query::fromClause( $tables );
        if ( $where ) {
            $where = "WHERE $where";
        }
        
        return "$select $from $where";
    }

    /** 
     * Given a list of conditions in params generate the required
     * where clause
     * 
     * @param array $params           associative array of conditions 
     * @param array $fields           associative array of fields in 
     * the data model (typically retrieved from an xml file) 
     * @param array $select           associative array of select 
     * clauses 
     * @param array $tables           associative array of from 
     * clauses 
     * 
     * @return void 
     * @return void 
     * @access public 
     * @static 
     */ 
    static function whereClause( &$params, &$fields, &$select, &$tables ) {
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

    /**
     * create the from clause
     *
     * @param array $tables tables that need to be included in this from clause
     *                      if null, return mimimal from clause (i.e. civicrm_contact)
     * @param array $inner  tables that should be inner-joined
     * @param array $right  tables that should be right-joined
     *
     * @return string the from clause
     * @access public
     * @static
     */
    static function fromClause( &$tables , $inner = null, $right = null) {
        $from = ' FROM civicrm_contact ';
        if ( empty( $tables ) ) {
            return $from;
        }
        
        if ( ( CRM_Utils_Array::value( 'civicrm_state_province', $tables ) ||
               CRM_Utils_Array::value( 'civicrm_country'       , $tables ) ) &&
             ! CRM_Utils_Array::value( 'civicrm_address'       , $tables ) ) {
            $tables = array_merge( array( 'civicrm_address' => 1 ), $tables );
        }
        // add location table if address / phone / email is set
        if ( ( CRM_Utils_Array::value( 'civicrm_address' , $tables ) ||
               CRM_Utils_Array::value( 'civicrm_phone'   , $tables ) ||
               CRM_Utils_Array::value( 'civicrm_email'   , $tables ) ||
               CRM_Utils_Array::value( 'civicrm_im'      , $tables ) ) &&
             ! CRM_Utils_Array::value( 'civicrm_location', $tables ) ) {
            $tables = array_merge( array( 'civicrm_location' => 1 ), $tables ); 
        }

        // add group_contact table if group table is present
        if ( CRM_Utils_Array::value( 'civicrm_group', $tables ) &&
            !CRM_Utils_Array::value('civicrm_group_contact', $tables)) {
            $tables['civicrm_group_contact'] = 1;
        }

        // add group_contact and group table is subscription history is present
        if ( CRM_Utils_Array::value( 'civicrm_subscription_history', $tables )
            && !CRM_Utils_Array::value('civicrm_group', $tables)) {
            $tables = array_merge( array( 'civicrm_group'         => 1,
                                          'civicrm_group_contact' => 1 ),
                                   $tables );
        }

        foreach ( $tables as $name => $value ) {
            if ( ! $value ) {
                continue;
            }

            if (CRM_Utils_Array::value($name, $inner)) {
                $side = 'INNER';
            } elseif (CRM_Utils_Array::value($name, $right)) {
                $side = 'RIGHT';
            } else {
                $side = 'LEFT';
            }
            
            if ( $value != 1 ) {
                // if there is already a join statement in value, use value itself
                if ( strpos( $value, 'JOIN' ) ) { 
                    $from .= " $value ";
                } else {
                    $from .= " $side JOIN $name ON ( $value ) ";
                }
                continue;
            }
            
            switch ( $name ) {
            case 'civicrm_individual':
                $from .= " $side JOIN civicrm_individual ON (civicrm_contact.id = civicrm_individual.contact_id) ";
                continue;

            case 'civicrm_location':
                $from .= " $side JOIN civicrm_location ON (civicrm_location.entity_table = 'civicrm_contact' AND
                                                          civicrm_contact.id = civicrm_location.entity_id  AND
                                                          civicrm_location.is_primary = 1)";
                continue;

            case 'civicrm_address':
                $from .= " $side JOIN civicrm_address ON civicrm_location.id = civicrm_address.location_id ";
                continue;

            case 'civicrm_phone':
                $from .= " $side JOIN civicrm_phone ON (civicrm_location.id = civicrm_phone.location_id AND civicrm_phone.is_primary = 1) ";
                continue;

            case 'civicrm_email':
                $from .= " $side JOIN civicrm_email ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1) ";
                continue;

            case 'civicrm_im':
                $from .= " $side JOIN civicrm_im ON (civicrm_location.id = civicrm_im.location_id AND civicrm_im.is_primary = 1) ";
                continue;

            case 'civicrm_state_province':
                $from .= " $side JOIN civicrm_state_province ON civicrm_address.state_province_id = civicrm_state_province.id ";
                continue;

            case 'civicrm_country':
                $from .= " $side JOIN civicrm_country ON civicrm_address.country_id = civicrm_country.id ";
                continue;

            case 'civicrm_group':
                $from .= " $side JOIN civicrm_group ON civicrm_group.id =  civicrm_group_contact.group_id ";
                continue;

            case 'civicrm_group_contact':
                $from .= " $side JOIN civicrm_group_contact ON civicrm_contact.id = civicrm_group_contact.contact_id ";
                continue;

            case 'civicrm_entity_tag':
                $from .= " $side JOIN civicrm_entity_tag ON ( civicrm_entity_tag.entity_table = 'civicrm_contact' AND
                                                             civicrm_contact.id = civicrm_entity_tag.entity_id ) ";
                continue;

            case 'civicrm_activity_history':
                $from .= " $side JOIN civicrm_activity_history ON ( civicrm_activity_history.entity_table = 'civicrm_contact' AND  
                                                               civicrm_contact.id = civicrm_activity_history.entity_id ) ";
                continue;

            case 'civicrm_custom_value':
                $from .= " $side JOIN civicrm_custom_value ON ( civicrm_custom_value.entity_table = 'civicrm_contact' AND
                                                          civicrm_contact.id = civicrm_custom_value.entity_id )";
                continue;
                
            case 'civicrm_subscription_history':
                $from .= " $side JOIN civicrm_subscription_history
                                   ON civicrm_group_contact.contact_id = civicrm_subscription_history.contact_id
                                  AND civicrm_group_contact.group_id   =  civicrm_subscription_history.group_id";
                continue;
            }

        }
        return $from;
    }

}
