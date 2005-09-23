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
     * the default set of return properties
     *
     * @var array
     * @static
     */
    static    $_defaultReturnProperties;

    protected $_params;
    protected $_returnProperties;
    protected $_select;
    protected $_tables;
    protected $_where;
    protected $_fields;
    protected $_count;
    protected $_includeContactIds;
    protected $_sortByChar;
    protected $_groupContacts;

    function __construct( $params = null, $returnProperties = null, $fields = null,
                          $count = false, $includeContactIds = false,
                          $sortByChar = false, $groupContacts = false ) {
        $this->_params =& $params;
        if ( empty( $returnProperties ) ) {
            $this->_returnProperties =& self::defaultReturnProperties( ); 
        } else {
            $this->_returnProperties =& $returnProperties;
        }

        $this->_count             = $count;
        $this->_includeContactIds = $includeContactIds;
        $this->_sortByChar        = $sortByChar;
        $this->_groupContacts     = $groupContacts;

        if ( $fields ) {
            $this->_fields =& $fields;
        } else {
            $this->_fields = CRM_Contact_BAO_Contact::importableFields( 'All' );
        }
        $this->_select = array( );
        $this->_tables = array( );
        $this->_where  = array( );
    }

    function addSpecialFields( ) {
        static $special = array( 'contact_type', 'sort_name', 'display_name' );
        foreach ( $special as $name ) {
            if ( CRM_Utils_Array::value( $name, $this->_returnProperties ) ) { 
                $this->_select[] = 'civicrm_contact.' . $name . ' as ' . $name;
            }
        }
    }

    /**
     * Given a list of conditions in params and a list of desired
     * return Properties generate the required select and from
     * clauses. Note that since the where clause introduces new
     * tables, the initial attempt also retrieves all variables used
     * in the params list
     *
     * @return void
     * @access public
     */
    function selectClause( ) {
        $properties = array( );
        $cfIDs      = array( );

        $this->addSpecialFields( );

        foreach ($this->_fields as $name => $field) {
            // if we need to get the value for this param or we need all values
            if ( CRM_Utils_Array::value( $name, $this->_params )           ||
                 CRM_Utils_Array::value( $name, $this->_returnProperties ) ||
                 ( ! $this->_params ) ) {
                $cfID = CRM_Utils_Array::value( 'custom_field_id', $field );
                if ( $cfID ) {
                    $cfIDs[] = $cfID;
                } else if ( isset( $field['where'] ) ) {
                    list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 ); 
                    if ( isset( $tableName ) ) { 
                        $this->_select[] = $field['where']. ' as ' . $name;
                        $this->_tables[$tableName] = 1;
                    }
                }
            }
        }

        // check if location is present in return Properties and if so, is it an
        // array
        if ( CRM_Utils_Array::value( 'location', $this->_returnProperties ) &&
             is_array( $this->_returnProperties['location'] ) ) {
            $this->addHierarchicalElements( );
        }

        if ( ! empty( $cfIDs ) ) {
            $customSelect = $customFrom = null;
            CRM_Core_BAO_CustomGroup::selectFromClause( $cfIDs, $customSelect, $customFrom ); 
            if ( $customSelect ) {
                $this->_select[] = $customSelect;
                $this->_tables['civicrm_custom_value'] = $customFrom;
            }
        }
    }

    function addHierarchicalElements( ) {
        if ( ! CRM_Utils_Array::value( 'location', $this->_returnProperties ) ) {
            return;
        }
        if ( ! is_array( $this->_returnProperties['location'] ) ) {
            return;
        }

        $locationTypes = CRM_Core_PseudoConstant::locationType( );
        $processed     = array( );
        foreach ( $this->_returnProperties['location'] as $name => $elements ) {
            $locationTypeId = array_search( $name, $locationTypes );
            if ( $locationTypeId === false ) {
                continue;
            }

            $lName = 'location-' . $name;
            $this->_tables[ 'civicrm_location_' . $name ] = "\nLEFT JOIN civicrm_location `$lName` ON ('$lName.entity_table' = 'civicrm_contact' AND '$lName.entity_id' = civicrm_contact.id AND '$lName.location_type_id' = $locationTypeId )";
            $aName = 'address-' . $name;
            $this->_tables[ 'civicrm_address_' . $name ] = "\nLEFT JOIN civicrm_address `$aName` ON ('$aName.location_id' = '$lName.id')";
            $processed[$lName] = $processed[$aName] = 1;

            foreach ( $elements as $elementName => $dontCare ) {
                $isPrimary = '1';
                $elementType = '';
                if ( strpos( $elementName, '-' ) ) {
                    // this is either phone, email or IM
                    list( $elementName, $elementType ) = explode( '-', $elementName );
                    if ( $elementType == '2' ) {
                        $isPrimary = '0';
                    }
                    $elementType = '-' . $elementType;
                }
                
                $field = CRM_Utils_Array::value( $elementName, $this->_fields );
                if ( $field && isset( $field['where'] ) ) {
                    list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 );  
                    $tName = substr( $tableName, 8 ) . '-' . $name . $elementType;
                    $fieldName = $fieldName;
                    if ( isset( $tableName ) ) {  
                        $this->_select[] = "'$tName.$fieldName' as `{$tName}-$fieldName`";
                        if ( ! CRM_Utils_Array::value( $tName, $processed ) ) {
                            $processed[$tName] = 1;
                            switch ( $tableName ) {
                            case 'civicrm_address':
                                $this->_tables[$tName] = "\nLEFT JOIN $tableName `$tName` ON '$lName.id' = '$tName.location_id'";
                                break;

                            case 'civicrm_phone':
                            case 'civicrm_email':
                            case 'civicrm_im':
                                $this->_tables[$tName] = "\nLEFT JOIN $tableName `$tName` ON '$lName.id' = '$tName.location_id' AND '$tName.is_primary' = $isPrimary";
                                break;

                            case 'civicrm_state_province':
                                $this->_tables[$tName] = "\nLEFT JOIN $tableName `$tName` ON '$tName.id' = '$aName.state_province_id'";
                                break;

                            case 'civicrm_country':
                                $this->_tables[$tName] = "\nLEFT JOIN $tableName `$tName` ON '$tName.id' = '$aName.country_id'";
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    /** 
     * Given a list of conditions in params and a list of desired 
     * return Properties generate the required query
     * 
     * @return the sql string for that query (this will most likely
     * change soon)
     * @access public 
     */ 
    function query( ) {
        $this->_select[]                  = 'civicrm_contact.id as contact_id';
        $this->_tables['civicrm_contact'] = 1;
        
        $this->selectClause( );

        if ( $this->_count ) {
            $select = 'SELECT count(DISTINCT civicrm_contact.id)'; 
        } else if ( $this->_sortByChar ) {  
            $select = 'SELECT DISTINCT UPPER(LEFT(civicrm_contact.sort_name, 1)) as sort_name';
        } else if ( $this->_groupContacts ) { 
            $select  = 'SELECT DISTINCT civicrm_contact.id as id'; 
        } else {
            if ( CRM_Utils_Array::value( 'group', $this->_params ) ) {
                $this->_select[] = 'civicrm_group_contact.status as status';
                $this->_tables['civicrm_group_contact'] = 1;
            }
            $select = 'SELECT ' . implode( ', ', $this->_select );
        }

        $where  = $this->whereClause( );
        $from   = self::fromClause( $this->_tables );
        if ( $where ) {
            $where = "WHERE $where";
        }

        // CRM_Core_Error::debug( "$select, $from", $where );
        return array( $select, $from, $where );
    }

    /** 
     * Given a list of conditions in params generate the required
     * where clause
     * 
     * @return void 
     * @access public 
     */ 
    function whereClause( ) {
        $this->search( );

        // CRM_Core_Error::debug( 'p', $this->_params );
        // domain id is always part of the where clause
        $config  =& CRM_Core_Config::singleton( ); 
        $this->_where[] = 'civicrm_contact.domain_id = ' . $config->domainID( );
        
        $id = CRM_Utils_Array::value( 'id', $this->_params );
        if ( $id ) {
            $this->_where[] = "civicrm_contact.id = $id";
        }

        $cfIDs = array( );
        // CRM_Core_Error::debug( 'p', $this->_params );
        // CRM_Core_Error::debug( 'f', $this->_fields );
        foreach ( $this->_fields as $name => $field ) { 
            $value = CRM_Utils_Array::value( $name, $this->_params );
                
            if ( ! isset( $value ) || $value == null ) {
                continue;
            }

            if ( $cfID = CRM_Core_BAO_CustomField::getKeyID( $field['name'] ) ) { 
                $cfIDs[$cfID] = $value; 
            } else {
                if ( $field['name'] === 'state_province_id' && is_numeric( $value ) ) {
                    $states =& CRM_Core_PseudoConstant::stateProvince(); 
                    $value  =  $states[$value]; 
                } else if ( $field['name'] === 'country_id' && is_numeric( $value ) ) { 
                    $countries =& CRM_Core_PseudoConstant::country( ); 
                    $value     =  $countries[$value]; 
                } 

                $value = strtolower( $value ); 
                $this->_where[] = 'LOWER(' . $field['where'] . ') LIKE "%' . strtolower( addslashes( $value ) ) . '%"';  
                
                list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 );  
                if ( isset( $tableName ) ) { 
                    $this->_tables[$tableName] = 1;  
                }
            }

            if ( ! empty( $cfIDs ) ) {
                $sql = CRM_Core_BAO_CustomValue::whereClause( $cfIDs );
                if ( $sql ) {
                    $this->_where[] = $sql;
                }
            }

        }

        return implode( ' AND ', $this->_where );
    }

    function tables( ) {
        return $this->_tables;
    }

    static function getQuery( $params = null, $returnProperties = null, $count = false ) {
        $query = new CRM_Contact_BAO_Query( $params, $returnProperties, null,
                                            $count, false, 
                                            false, false );
        list( $select, $from, $where ) = $query->query( );
        return "$select $from $where";
    }

    static function getWhereClause( &$params, &$fields, &$tables ) {
        $query = new CRM_Contact_BAO_Query( $params, null, $fields,
                                            false, false,
                                            false, false );

        $sql    = $query->whereClause( );
        $tables = array_merge( $query->tables( ), $tables );
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
            $tables = array_merge( array( 'civicrm_location' => 1,
                                          'civicrm_address'  => 1 ),
                                   $tables );
        }
        // add location table if address / phone / email is set
        if ( ( CRM_Utils_Array::value( 'civicrm_address' , $tables ) ||
               CRM_Utils_Array::value( 'civicrm_phone'   , $tables ) ||
               CRM_Utils_Array::value( 'civicrm_email'   , $tables ) ||
               CRM_Utils_Array::value( 'civicrm_im'      , $tables ) ) &&
             ! CRM_Utils_Array::value( 'civicrm_location', $tables ) ) {
            $tables = array_merge( array( 'civicrm_location' => 1 ),
                                   $tables ); 
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

            case 'civicrm_household':
                $from .= " $side JOIN civicrm_household ON (civicrm_contact.id = civicrm_household.contact_id) ";
                continue;

            case 'civicrm_organization':
                $from .= " $side JOIN civicrm_organization ON (civicrm_contact.id = civicrm_organization.contact_id) ";
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

            case 'civicrm_note':
                $from .= " $side JOIN civicrm_note ON ( civicrm_note.entity_table = 'civicrm_contact' AND
                                                        civicrm_contact.id = civicrm_note.entity_id ) "; 
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

    function search( ) {
        $this->contactType( );

        $this->group( );

        $this->tag( );

        $this->sortName( );

        $this->sortByCharacter( );

        $this->includeContactIDs( );

        $this->postalCode( );
    }

    function contactType( ) {
        // check for contact type restriction 
        if ( ! CRM_Utils_Array::value( 'contact_type', $this->_params ) ) {
            return;
        }

        $clause = array( );
        if ( is_array( $this->_params['contact_type'] ) ) {
            foreach ( $this->_params['contact_type'] as $k => $v) { 
                $clause[] = "'" . CRM_Utils_Type::escape( $k, 'String' ) . "'";
            }
        } else {
            $clause[] = "'" . CRM_Utils_Type::escape( $this->_params['contact_type'], 'String' ) . "'";
        }
        $this->_where[] = 'civicrm_contact.contact_type IN (' . implode( ',', $clause ) . ')';
    }

    function group( ) {
        if ( ! CRM_Utils_Array::value( 'group', $this->_params ) ) {
            return;
        }

        $this->_where[] = 'civicrm_group_contact.group_id IN (' .
            implode( ',', array_keys($this->_params['group']) ) . ')';

        $statii = array(); 
        $in = false; 
        if ( CRM_Utils_Array::value( 'group_contact_status', $this->_params ) &&
             is_array( $this->_params['group_contact_status'] ) ) {
            foreach ( $this->_params['group_contact_status'] as $k => $v ) {
                if ( $v ) {
                    if ( $k = 'Added' ) {
                        $in = true;
                    }
                    $statii = "'" . CRM_Utils_Type::escape($k, 'String') . "'";
                }
            }
        } else {
            $statii[] = '"Added"'; 
            $in = true; 
        }
        $this->_where[] = 'civicrm_group_contact.status IN (' . implode(', ', $statii) . ')';
        $this->_tables['civicrm_group_contact'] = 1;

        if ( $in ) {
            $this->savedSearch( );
        }
    }

    function savedSearch( ) {
        $ssWhere = array(); 
        $group =& new CRM_Contact_BAO_Group(); 
        foreach ( array_keys( $this->_params['group'] ) as $group_id ) { 
            $group->id = $group_id; 
            $group->find(true); 
            if (isset($group->saved_search_id)) { 
                if ( $config->mysqlVersion >= 4.1 ) { 
                    $sfv =& CRM_Contact_BAO_SavedSearch::getFormValues($group->saved_search_id);

                    $smarts =& CRM_Contact_BAO_Contact::searchQuery($sfv, 0, 0, null,  
                                                                    false, false, false, true, true);
                    $ssWhere[] = " 
                            (civicrm_contact.id IN ($smarts)  
                            AND civicrm_contact.id NOT IN ( 
                            SELECT contact_id FROM civicrm_group_contact 
                            WHERE civicrm_group_contact.group_id = "  
                        . CRM_Utils_Type::escape($group_id, 'Integer')
                        . "AND civicrm_group_contact.status = 'Removed'))"; 
                } else { 
                    $ssw = CRM_Contact_BAO_SavedSearch::whereClause( $group->saved_search_id, $this->_tables);
                    /* FIXME: bug with multiple group searches */ 
                    $ssWhere[] = "($ssw AND
                                   (civicrm_group_contact.id is null OR
                                     (civicrm_group_contact.group_id = $group_id AND
                                      civicrm_group_contact.status = 'Added')))"; 
                }
            }
            $group->reset(); 
            $group->selectAdd('*'); 
        }
        if (count($ssWhere)) { 
            $this->_tables['civicrm_group_contact'] =  
                "civicrm_contact.id = civicrm_group_contact.contact_id AND civicrm_group_contact.group_id IN (" .
                implode(',', array_keys($this->_params['group'])) . ')'; 
            $this->_where[]  = "(({$andArray['group']}) OR (" 
                . implode(' OR ', $ssWhere)  
                . '))'; 
        } 
    }

    function tag( ) {
        if ( ! CRM_Utils_Array::value( 'tag', $this->_params ) ) { 
            return; 
        } 
 
        $this->_where[] = 'tag_id IN (' . implode( ',', array_keys( $this->_params['tag'] ) ) . ')';
        $this->_tables['civicrm_entity_tag'] = 1;                                          
    } 

    function sortName( ) {
        if ( ! CRM_Utils_Array::value( 'sort_name', $this->_params ) ) {
            return;
        }

        $name = trim($this->_params['sort_name']); 

        $sub  = array( ); 
        // if we have a comma in the string, search for the entire string 
        if ( strpos( $name, ',' ) !== false ) { 
            $sub[] = " ( LOWER(civicrm_contact.sort_name) LIKE '%" . strtolower(addslashes($name)) . "%' )"; 
            $sub[] = " ( LOWER(civicrm_email.email)       LIKE '%" . strtolower(addslashes($name)) . "%' )"; 
            $this->_tables = array_merge( array( 'civicrm_location' => 1 ), $this->_tables );
            $this->_tables['civicrm_email']    = 1; 
        } else { 
            // split the string into pieces 
            $pieces =  explode( ' ', $name ); 
            foreach ( $pieces as $piece ) { 
                $sub[] = " ( LOWER(civicrm_contact.sort_name) LIKE '%" . strtolower(addslashes(trim($piece))) . "%' ) "; 
                $sub[] = " ( LOWER(civicrm_email.email)       LIKE '%" . strtolower(addslashes(trim($piece))) . "%' )"; 
            } 
            $this->_tables = array_merge( array( 'civicrm_location' => 1 ), $this->_tables );
            $this->_tables['civicrm_email']    = 1; 
        } 
        $this->_where[] = ' ( ' . implode( '  OR ', $sub ) . ' ) '; 
    }

    function sortByCharacter( ) {
        if ( ! CRM_Utils_Array::value( 'sortByCharacter', $this->_params ) ) {
            return;
        }

        $name = trim( $this->_params['sortByCharacter'] );
        $cond = " LOWER(civicrm_contact.sort_name) LIKE '" . strtolower(addslashes($name)) . "%'"; 
        $this->_where[] = $cond;
    }

    function includeContactIDs( ) {
        if ( ! $this->_includeContactIds || empty( $this->_params ) ) {
            return;
        }

        $contactIds = array( ); 
        foreach ( $this->_params as $name => $value ) { 
            if ( substr( $name, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) { 
                $contactIds[] = substr( $name, CRM_Core_Form::CB_PREFIX_LEN ); 
            } 
        } 
        if ( ! empty( $contactIds ) ) { 
            $this->_where[] = " ( civicrm_contact.id in (" . implode( ',', $contactIds ) . " ) ) "; 
        }
    }

    function postalCode( ) {
        // postal code processing 
        if ( CRM_Utils_Array::value( 'postal_code'     , $this->_params ) || 
             CRM_Utils_Array::value( 'postal_code_low' , $this->_params ) || 
             CRM_Utils_Array::value( 'postal_code_high', $this->_params ) ) { 
            $tables['civicrm_location'] = 1; 
            $tables['civicrm_address']   = 1; 
 
            // we need to do postal code processing 
            $pcArray   = array(); 
 
            if ($this->_params['postal_code']) { 
                $this->_where[] = 'civicrm_address.postal_code = "' .
                    CRM_Utils_Type::escape( $this->_params['postal_code'], 'String' ) .
                    '"'; 
                $this->_tables['civicrm_location'] = 1;
                $this->_tables['civicrm_address' ] = 1;
            } else {
                if ($this->_params['postal_code_low']) { 
                    $pcArray[] = 'civicrm_address.postal_code >= ' .
                        CRM_Utils_Type::escape( $this->_params['postal_code_low'], 'Integer' ) . 
                        '"';  
                } 
                if ($this->_params['postal_code_high']) { 
                    $pcArray[] = ' ( civicrm_address.postal_code <= ' .
                        CRM_Utils_Type::escape( $this->_params['postal_code_high'], 'Integer' ) . 
                        '"';  
                }
                if ( !empty( $pcArray ) ) {
                    $this->_where[] = '(' . implode( ' AND ', $pcArray ) . ')';
                    $this->_tables['civicrm_location'] = 1;
                    $this->_tables['civicrm_address' ] = 1;
                }
            }
        }
    }

    function location( ) {
        if ( CRM_Utils_Array::value( 'location_type', $this->_params ) ) {
            $this->_where[] = 'civicrm_location.location_type_id IN (' .
                implode( ',', array_keys( $this->_params['location_type'] ) ) .
                ')';
            $this->_tables['civicrm_location'] = 1;
        }

        if ( CRM_Utils_Array::value( 'primary_location', $this->_params ) ) { 
            $this->_where[]  = 'civicrm_location.is_primary = 1';
            $this->_tables['civicrm_location'] = 1; 
        }
    }

    function activity( ) {
        if ( CRM_Utils_Array::value( 'activity_type', $this->_params ) ) {
            $name = trim($this->_params['activity_type']); 

            // split the string into pieces 
            $pieces =  explode( ' ', $name ); 
            $sub    = array( );
            foreach ( $pieces as $piece ) { 
                $sub[] = " LOWER(civicrm_activity_history.activity_type) LIKE '%" . strtolower(addslashes(trim($piece))) . "%'"; 
            } 
            $this->_where[] = ' ( ' . implode( '  OR ', $sub ) . ' ) ';
            $this->_tables['civicrm_activity_history'] = 1; 
        }

        if ( isset( $this->_params['activity_from_date'] ) ) {
            $date = CRM_Utils_Date::format( array_reverse( CRM_Utils_Array::value( 'activity_from_date',
                                                                                               $this->_params ) ) );
            if ( $date ) {
                $this->_where[] = "civicrm_activity_history.activity_date >= '$date'"; 
                $this->_tables['civicrm_activity_history'] = 1;
            }
        } 

        if ( isset( $this->_params['activity_to_date'] ) ) {
            $date = CRM_Utils_Date::format( array_reverse( CRM_Utils_Array::value( 'activity_to_date',
                                                                                   $this->_params ) ) );
            if ( $date ) {
                $this->_where[] = " ( civicrm_activity_history.activity_date <= '$date' ) "; 
                $this->_tables['civicrm_activity_history'] = 1; 
            }
        }
     }

    static function &defaultReturnProperties( ) {
        if ( ! isset( self::$_defaultReturnProperties ) ) {
            self::$_defaultReturnProperties = array( 
                                                    'home_URL'               => 1, 
                                                    'image_URL'              => 1, 
                                                    'legal_identifier'       => 1, 
                                                    'external_identifier'    => 1,
                                                    'contact_type'           => 1,
                                                    'sort_name'              => 1,
                                                    'display_name'           => 1,
                                                    'nick_name'              => 1, 
                                                    'first_name'             => 1, 
                                                    'middle_name'            => 1, 
                                                    'last_name'              => 1, 
                                                    'prefix'                 => 1, 
                                                    'suffix'                 => 1,
                                                    'street_address'         => 1, 
                                                    'supplemental_address_1' => 1, 
                                                    'supplemental_address_2' => 1, 
                                                    'city'                   => 1, 
                                                    'postal_code'            => 1, 
                                                    'postal_code_suffix'     => 1, 
                                                    'state_province'         => 1, 
                                                    'country'                => 1, 
                                                    'email'                  => 1, 
                                                    'phone'                  => 1, 
                                                    'im'                     => 1, 
                                                    ); 
        }
        return self::$_defaultReturnProperties;
    }

}
