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
    protected $_element;
    protected $_tables;
    protected $_where;
    protected $_whereClause;
    protected $_fromClause;
    protected $_qill;

    public    $_fields;
    public    $_options;

    protected $_search = true;
    protected $_strict = false;
    protected $_primaryLocation = true;

    protected $_customQuery;

    protected $_includeContactIds;

    static $_dependencies = array( 'civicrm_state_province' => 1,
                                   'civicrm_country'        => 1,
                                   'civicrm_address'        => 1,
                                   'civicrm_phone'          => 1,
                                   'civicrm_email'          => 1,
                                   'civicrm_im'             => 1, );

    function __construct( $params = null, $returnProperties = null, $fields = null,
                          $includeContactIds = false, $strict = false ) {
        require_once 'CRM/Contact/BAO/Contact.php';
        //CRM_Core_Error::debug( 'params', $params );
        //CRM_Core_Error::debug( 'post', $_POST );
        $this->_params =& $params;

        if ( empty( $returnProperties ) ) {
            $this->_returnProperties =& self::defaultReturnProperties( ); 
        } else {
            $this->_returnProperties =& $returnProperties;
        }

        $this->_includeContactIds = $includeContactIds;
        $this->_strict            = $strict;

        if ( $fields ) {
            $this->_fields =& $fields;
            $this->_search = false;
        } else {
            require_once 'CRM/Contact/BAO/Contact.php';
            $this->_fields = CRM_Contact_BAO_Contact::importableFields( 'All' );
        }
 

        // basically do all the work once, and then reuse it
        $this->initialize( );
        //CRM_Core_Error::debug( 'q', $this );
    }

    function initialize( ) {
        $this->_select  = array( ); 
        $this->_element = array( ); 
        $this->_tables  = array( ); 
        $this->_where   = array( ); 
        $this->_qill    = array( ); 
        $this->_options = array( );

        $this->_customQuery = null; 
 
        $this->_select['contact_id']      = 'DISTINCT civicrm_contact.id as contact_id';
        $this->_element['contact_id']     = 1; 
        $this->_tables['civicrm_contact'] = 1; 

        $this->selectClause( ); 
        $this->_whereClause = $this->whereClause( ); 
        $this->_fromClause  = self::fromClause( $this->_tables, null, null, $this->_primaryLocation ); 
    }

    function addSpecialFields( ) {
        static $special = array( 'contact_type', 'sort_name', 'display_name' );
        foreach ( $special as $name ) {
            if ( CRM_Utils_Array::value( $name, $this->_returnProperties ) ) { 
                $this->_select[$name]  = 'civicrm_contact.' . $name . ' as ' . $name;
                $this->_element[$name] = 1;
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

        //CRM_Core_Error::debug( 'f', $this->_fields );
        foreach ($this->_fields as $name => $field) {
            $value = CRM_Utils_Array::value( $name, $this->_params );

            // if we need to get the value for this param or we need all values
            if ( ! CRM_Utils_System::isNull( $value ) || 
                 CRM_Utils_Array::value( $name, $this->_returnProperties ) ) {
                $cfID = CRM_Core_BAO_CustomField::getKeyID( $name );
                if ( $cfID ) {
                    $value = CRM_Utils_Array::value( $name, $this->_params );
                    $cfIDs[$cfID] = $value;
                } else if ( isset( $field['where'] ) ) {
                    list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 ); 
                    if ( isset( $tableName ) ) { 
                        if ( CRM_Utils_Array::value( $tableName, self::$_dependencies ) ) {
                            $this->_tables['civicrm_location'] = 1;
                            $this->_select['location_id']      = 'civicrm_location.id as location_id';
                            $this->_element['location_id']     = 1;

                            $this->_tables['civicrm_address'] = 1;
                            $this->_select['address_id']      = 'civicrm_address.id as address_id';
                            $this->_element['address_id']     = 1;
                        }
                        $this->_tables[$tableName]         = 1;

                        // also get the id of the tableName
                        $tName = substr($tableName, 8 );
                        if ( $tName != 'contact' ) {
                            $this->_select["{$tName}_id"]  = "{$tableName}.id as {$tName}_id";
                            $this->_element["{$tName}_id"] = 1;
                        }

                        $this->_select[$name]              = $field['where'] . " as $name";
                        $this->_element[$name]             = 1;

                    }
                }
            }
        }

        // add location as hierarchical elements
        $this->addHierarchicalElements( );

        if ( ! empty( $cfIDs ) ) {
            //CRM_Core_Error::debug( 'cfIDs', $cfIDs );
            require_once 'CRM/Core/BAO/CustomQuery.php';
            $this->_customQuery = new CRM_Core_BAO_CustomQuery( $cfIDs );
            $this->_customQuery->query( );
            $this->_select  = array_merge( $this->_select , $this->_customQuery->_select );
            $this->_element = array_merge( $this->_element, $this->_customQuery->_element);
            $this->_tables  = array_merge( $this->_tables , $this->_customQuery->_tables );
            $this->_options = $this->_customQuery->_options;
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
            $lName = "`$name-location`";
            $lCond = self::getPrimaryCondition( $name );
            if ( $lCond ) {
                $lCond = "$lName." . $lCond;
            } else {
                $locationTypeId = array_search( $name, $locationTypes );
                if ( $locationTypeId === false ) {
                    continue;
                }
                $lCond = "$lName.location_type_id = $locationTypeId";
            }

            $tName = "$name-location";
            $this->_select["{$tName}_id"]  = "`$tName`.id as `{$tName}_id`"; 
            $this->_element["{$tName}_id"] = 1; 
            $this->_tables[ 'civicrm_location_' . $name ] = "\nLEFT JOIN civicrm_location $lName ON ($lName.entity_table = 'civicrm_contact' AND $lName.entity_id = civicrm_contact.id AND $lCond )";

            $aName = "`$name-address`";
            $tName = "$name-address";
            $this->_select["{$tName}_id"]  = "`$tName`.id as `{$tName}_id`"; 
            $this->_element["{$tName}_id"] = 1; 
            $this->_tables[ 'civicrm_address_' . $name ] = "\nLEFT JOIN civicrm_address $aName ON ($aName.location_id = $lName.id)";

            $processed[$lName] = $processed[$aName] = 1;
            foreach ( $elements as $elementFullName => $dontCare ) {
                $cond = "is_primary = 1";
                $elementName = $elementFullName;
                $elementType = '';
                if ( strpos( $elementName, '-' ) ) {
                    // this is either phone, email or IM
                    list( $elementName, $elementType ) = explode( '-', $elementName );
                    $cond = self::getPrimaryCondition( $elementType );
                    if ( ! $cond ) {
                        $cond = "phone_type = '$elementType'";
                    }
                    $elementType = '-' . $elementType;
                }
                
                $field = CRM_Utils_Array::value( $elementName, $this->_fields );
                if ( $field && isset( $field['where'] ) ) {
                    list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 );  
                    $tName = $name . '-' . substr( $tableName, 8 ) . $elementType;
                    $fieldName = $fieldName;
                    if ( isset( $tableName ) ) {
                        $this->_select["{$tName}_id"]                   = "`$tName`.id as `{$tName}_id`";
                        $this->_element["{$tName}_id"]                  = 1;
                        $this->_select["{$name}-{$elementFullName}"]  = "`$tName`.$fieldName as `{$name}-{$elementFullName}`";
                        $this->_element["{$name}-{$elementFullName}"] = 1;
                        if ( ! CRM_Utils_Array::value( "`$tName`", $processed ) ) {
                            $processed["`$tName`"] = 1;
                            switch ( $tableName ) {
                            case 'civicrm_phone':
                            case 'civicrm_email':
                            case 'civicrm_im':
                                $this->_tables[$tName] = "\nLEFT JOIN $tableName `$tName` ON $lName.id = `$tName`.location_id AND `$tName`.$cond";
                                break;

                            case 'civicrm_state_province':
                                $this->_tables[$tName] = "\nLEFT JOIN $tableName `$tName` ON `$tName`.id = $aName.state_province_id";
                                break;

                            case 'civicrm_country':
                                $this->_tables[$tName] = "\nLEFT JOIN $tableName `$tName` ON `$tName`.id = $aName.country_id";
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
    function query( $count = false, $sortByChar = false, $groupContacts = false ) {
        if ( $count ) {
            $select = 'SELECT count(DISTINCT civicrm_contact.id)'; 
        } else if ( $sortByChar ) {  
            $select = 'SELECT DISTINCT UPPER(LEFT(civicrm_contact.sort_name, 1)) as sort_name';
        } else if ( $groupContacts ) { 
            $select  = 'SELECT DISTINCT civicrm_contact.id as id'; 
        } else {
            if ( CRM_Utils_Array::value( 'group', $this->_params ) ) {
                // make sure there is only one element
                if ( count( $this->_params['group'] ) == 1 ) {
                    $this->_select['group_contact_id']      = 'civicrm_group_contact.id as group_contact_id';
                    $this->_element['group_contact_id']     = 1;
                    $this->_select['status']                = 'civicrm_group_contact.status as status';
                    $this->_element['status']               = 1;
                }
                $this->_tables['civicrm_group_contact'] = 1;
            }
            $select = 'SELECT ' . implode( ', ', $this->_select );
        }

        $where = '';
        if ( ! empty( $this->_whereClause ) ) {
            $where = "WHERE {$this->_whereClause}";
        }

        //CRM_Core_Error::debug( "t", $this );
        //CRM_Core_Error::debug( "$select, {$this->_fromClause} $where", $where );
        return array( $select, $this->_fromClause, $where );
    }

    /** 
     * Given a list of conditions in params generate the required
     * where clause
     * 
     * @return void 
     * @access public 
     */ 
    function whereClause( ) {
        // CRM_Core_Error::debug( 'p', $this->_params );
        // domain id is always part of the where clause
        $config  =& CRM_Core_Config::singleton( ); 
        $this->_where[] = 'civicrm_contact.domain_id = ' . $config->domainID( );
        
        $id = CRM_Utils_Array::value( 'id', $this->_params );
        if ( $id ) {
            $this->_where[] = "civicrm_contact.id = $id";
        }

        // we should get the params only when we are coming from search. when we want to do a restricted query
        // for permissioning etc, the params array is not importnat
        if ( $this->_search ) {
            $this->searchWhereClause( );
        }

        $this->group( );

        $this->tag( );

        $this->postalCode( );

        $this->activity( );

        $this->includeContactIds( );

        //CRM_Core_Error::debug( 'p', $this->_params );
        //CRM_Core_Error::debug( 'f', $this->_fields );
        static $skipFields = array( 'postal_code', 'group', 'tag' );
        foreach ( $this->_fields as $name => $field ) { 
            // skip postal code processing for search since we tackle an
            // extended version of this
            if ( empty( $name ) ||
                 in_array( $name, $skipFields ) ) {
                continue;
            }

            $value = CRM_Utils_Array::value( $name, $this->_params );
                
            if ( ! isset( $value ) || $value == null ) {
                continue;
            }

            if ( CRM_Core_BAO_CustomField::getKeyID( $name ) ) { 
                continue;
            }

            // FIXME: the LOWER/strtolower pairs below most probably won't work
            // with non-US-ASCII characters, as even if MySQL does the proper
            // thing with LOWER-ing them (4.0 almost certainly won't, but then
            // we don't officially support 4.0 for non-US-ASCII data), PHP
            // won't do the proper thing with strtolower-ing them unless the
            // underlying operating system uses an UTF-8 locale for LC_CTYPE
            // for the user the webserver runs at (or suEXECs); we should use
            // mb_strtolower(), but then we'd require mb_strings support; we
            // could wrap this in function_exist(), though
            if ( $name === 'state_province' ) {
                $states =& CRM_Core_PseudoConstant::stateProvince(); 
                if ( is_numeric( $value ) ) {
                    $value  =  $states[(int ) $value];
                }
                $this->_where[] = 'LOWER(' . $field['where'] . ') = "' . strtolower( addslashes( $value ) ) . '"';
                $this->_qill[] = ts('State - "%1"', array( 1 => $value ) );
            } else if ( $name === 'country' ) {
                $countries =& CRM_Core_PseudoConstant::country( ); 
                if ( is_numeric( $value ) ) { 
                    $value     =  $countries[(int ) $value]; 
                }
                $this->_where[] = 'LOWER(' . $field['where'] . ') = "' . strtolower( addslashes( $value ) ) . '"';
                $this->_qill[] = ts('Country - "%1"', array( 1 => $value ) );
            } else if ( $name === 'gender' ) {
                $genders =& CRM_Core_PseudoConstant::gender( );  
                if ( is_numeric( $value ) ) {  
                    $value     =  $genders[(int ) $value];  
                }
                $this->_where[] = 'LOWER(' . $field['where'] . ') = "' . strtolower( addslashes( $value ) ) . '"'; 
                $this->_qill[] = ts('Gender - "%1"', array( 1 => $value ) ); 
            } else if ( $name === 'birth_date' ) {
                $date = CRM_Utils_Date::format( $value );
                if ( ! $date ) {
                    continue;
                }
                $this->_where[] = $field['where'] . " = $date";
                $date = CRM_Utils_Date::customFormat( $value );
                $this->_qill[]  = "$field[title] \"$date\"";
            } else {
                // sometime the value is an array, need to investigate and fix
                if ( is_array( $value ) ) {
                    $value = $value[0];
                }

                if ( $this->_strict ) {
                    $this->_where[] = 'LOWER(' . $field['where'] . ') = "' . strtolower( str_replace( "\"", "", $value)  ) . '"';  
                    $this->_qill[]  = ts( '%1 = "%2"', array( 1 => $field['title'], 2 => $value ) );
                } else {
                    $this->_where[] = 'LOWER(' . $field['where'] . ') LIKE "%' . strtolower( addslashes( $value ) ) . '%"';  
                    $this->_qill[]  = ts( '%1 like "%2"', array( 1 => $field['title'], 2 => $value ) );
                }
            }
            list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 );  
            if ( isset( $tableName ) ) { 
                $this->_tables[$tableName] = 1;  
            }
            // CRM_Core_Error::debug( 'f', $field );
            // CRM_Core_Error::debug( $value, $this->_qill );
        }

        if ( $this->_customQuery ) {
            $this->_where = array_merge( $this->_where  , $this->_customQuery->_where );
            $this->_qill  = array_merge( $this->_qill   , $this->_customQuery->_qill  );
        }

        return  implode( ' AND ', $this->_where );
    }

    function store( $dao ) {
        $value = array( );
        foreach ( $this->_element as $key => $dontCare ) {
            if ( isset( $dao->$key ) ) {
                if ( strpos( $key, '-' ) ) {
                    $values = explode( '-', $key );
                    $lastElement = array_pop( $values );
                    $current =& $value;
                    foreach ( $values as $v ) {
                        if ( ! array_key_exists( $v, $current ) ) {
                            $current[$v] = array( );
                        }
                        $current =& $current[$v];
                    }
                    $current[$lastElement] = $dao->$key;
                } else {
                    $value[$key] = $dao->$key;
                }
            }
        }
        return $value;
    }

    function tables( ) {
        return $this->_tables;
    }

    static function getWhereClause( $params, $fields, &$tables, $strict = false ) {
        $query = new CRM_Contact_BAO_Query( $params, null, $fields,
                                            false, $strict );

        $tables = array_merge( $query->tables( ), $tables );
        return $query->_whereClause;
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
    static function fromClause( &$tables , $inner = null, $right = null, $primaryLocation = true ) {
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
                                                          civicrm_contact.id = civicrm_location.entity_id ";
                if ( $primaryLocation ) {
                    $from .= "AND civicrm_location.is_primary = 1";
                }
                $from .= ")";
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

            case 'civicrm_individual_prefix':
                $from .= " $side JOIN civicrm_individual_prefix ON civicrm_individual.prefix_id = civicrm_individual_prefix.id ";
                continue;
            
            case 'civicrm_individual_suffix':
                $from .= " $side JOIN civicrm_individual_suffix ON civicrm_individual.suffix_id = civicrm_individual_suffix.id ";
                continue;

            case 'civicrm_gender':
                $from .= " $side JOIN civicrm_gender ON civicrm_individual.gender_id = civicrm_gender.id ";
                continue;
            }

        }
        return $from;
    }

    function searchWhereClause( ) {
        $this->contactType( );

        $this->sortName( );

        $this->sortByCharacter( );

        $this->location( );

    }

    function contactType( ) {
        // check for contact type restriction 
        if ( ! CRM_Utils_Array::value( 'contact_type', $this->_params ) ) {
            $this->_qill[]  = ts('Contact Type - All');
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
        $this->_qill[]  = ts('Contact Type -') . ' ' . implode( ' ' . ts('or') . ' ', $clause );
    }

    function group( ) {
        if ( ! CRM_Utils_Array::value( 'group', $this->_params ) ) {
            return;
        }

        $groupClause = 'civicrm_group_contact.group_id IN (' . 
            implode( ',', array_keys($this->_params['group']) ) . ')'; 

        $names = array( );
        $groupNames =& CRM_Core_PseudoConstant::group();
        foreach ( $this->_params['group'] as $id => $dontCare ) {
            $names[] = $groupNames[$id];
        }
        $this->_qill[]  = ts('Member of Group -') . ' ' . implode( ' ' . ts('or') . ' ', $names );
        
        $statii = array(); 
        $in = false; 
        if ( CRM_Utils_Array::value( 'group_contact_status', $this->_params ) &&
             is_array( $this->_params['group_contact_status'] ) ) {
            foreach ( $this->_params['group_contact_status'] as $k => $v ) {
                if ( $v ) {
                    if ( $k == 'Added' ) {
                        $in = true;
                    }
                    $statii[] = "'" . CRM_Utils_Type::escape($k, 'String') . "'";
                }
            }
        } else {
            $statii[] = '"Added"'; 
            $in = true; 
        }

        $groupClause .= ' AND civicrm_group_contact.status IN (' . implode(', ', $statii) . ')';
        $this->_tables['civicrm_group_contact'] = 1;
        $this->_qill[] = ts('Group Status -') . ' ' . implode( ' ' . ts('or') . ' ', $statii );

        if ( $in ) {
            $ssClause = $this->savedSearch( );
            if ( $ssClause ) {
                $groupClause = "( ( $groupClause ) OR ( $ssClause ) )";
            }
        }
        
        $this->_where[] = $groupClause;
    }

    function savedSearch( ) {
        $config =& CRM_Core_Config::singleton( );
        $ssWhere = array(); 
        $group =& new CRM_Contact_BAO_Group(); 
        foreach ( array_keys( $this->_params['group'] ) as $group_id ) { 
            $group->id = $group_id; 
            $group->find(true); 
            if (isset($group->saved_search_id)) {
                require_once 'CRM/Contact/BAO/SavedSearch.php';
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
                        . " AND civicrm_group_contact.status = 'Removed'))"; 
                } else { 
                    $ssw = CRM_Contact_BAO_SavedSearch::whereClause( $group->saved_search_id, $this->_tables);
                    /* FIXME: bug with multiple group searches */ 
                    $ssWhere[] = "($ssw AND
                                   (civicrm_group_contact.id is null OR
                                     (civicrm_group_contact.group_id = " . CRM_Utils_Type::escape($group_id, 'Integer') . " AND
                                      civicrm_group_contact.status = 'Added')))"; 
                }
            }
            $group->reset(); 
            $group->selectAdd('*'); 
        }
        if ( ! empty( $ssWhere ) ) {
            $this->_tables['civicrm_group_contact'] =  
                "civicrm_contact.id = civicrm_group_contact.contact_id AND civicrm_group_contact.group_id IN (" .
                implode(',', array_keys($this->_params['group'])) . ')'; 
            return implode(' OR ', $ssWhere);
        }
        return null;
    }

    function tag( ) {
        if ( ! CRM_Utils_Array::value( 'tag', $this->_params ) ) { 
            return; 
        } 
 
        $names = array( );
        $tagNames =& CRM_Core_PseudoConstant::tag();
        foreach ( $this->_params['tag'] as $id => $dontCare ) {
            $names[] = $tagNames[$id];
        }
        $this->_qill[]  = ts('Tagged as -') . ' ' . implode( ' ' . ts('or') . ' ', $names ); 

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
        $this->_qill[]  = ts( 'Name or Email like - "%1"', array( 1 => $name ) );
    }

    function sortByCharacter( ) {
        if ( ! CRM_Utils_Array::value( 'sortByCharacter', $this->_params ) ) {
            return;
        }

        $name = trim( $this->_params['sortByCharacter'] );
        $cond = " LOWER(civicrm_contact.sort_name) LIKE '" . strtolower(addslashes($name)) . "%'"; 
        $this->_where[] = $cond;
        $this->_qill[]  = ts( 'Restricted to Contacts starting with: "%1"', array( 1 => $name ) );
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
            $this->whereClause = 'WHERE ' . implode( ' AND ', $this->_where ); 
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
                $this->_qill[] = ts('Postal code - "%1"', array( 1 => $this->_params['postal_code'] ) );
            } else {
                $qill = array( );
                if ($this->_params['postal_code_low']) { 
                    $pcArray[] = ' ( civicrm_address.postal_code >= "' .
                        CRM_Utils_Type::escape( $this->_params['postal_code_low'], 'String' ) . 
                        '" ) ';
                    $qill[] = ts( 'greater than "%1"', array( 1 => $this->_params['postal_code_low'] ) );
                } 
                if ($this->_params['postal_code_high']) { 
                    $pcArray[] = ' ( civicrm_address.postal_code <= "' .
                        CRM_Utils_Type::escape( $this->_params['postal_code_high'], 'String' ) . 
                        '" ) ';
                    $qill[] = ts( 'less than "%1"', array( 1 => $this->_params['postal_code_high'] ) );
                }
                if ( !empty( $pcArray ) ) {
                    $this->_where[] = '(' . implode( ' AND ', $pcArray ) . ')';
                    $this->_tables['civicrm_location'] = 1;
                    $this->_tables['civicrm_address' ] = 1;

                    $this->_qill[]  = ts('Postal code -') . ' ' . implode( ' ' . ts('and') . ' ', $qill );
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

            $locationType =& CRM_Core_PseudoConstant::locationType();
            $names = array( );
            foreach ( array_keys( $this->_params['location_type'] ) as $id ) {
                $names[] = $locationType[$id];
            }
            $this->_qill[] = ts('Location type -') . ' ' . implode( ' ' . ts('or') . ' ', $names );
            $this->_primaryLocation = false;
        }

        if ( CRM_Utils_Array::value( 'primary_location', $this->_params ) ) { 
            $this->_where[]  = 'civicrm_location.is_primary = 1';
            $this->_tables['civicrm_location'] = 1; 
            $this->_qill[] = ts('Primary Location only? - Yes');
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
            $this->_qill[]  = ts('Activity Type like - "%1"', array( 1 => $name ) );
        }

        $qill = array( );
        if ( isset( $this->_params['activity_from_date'] ) ) {
            $revDate = array_reverse( $this->_params['activity_from_date'] );
            $date    = CRM_Utils_Date::format( $revDate );
            $format  = CRM_Utils_Date::customFormat( CRM_Utils_Date::format( $revDate, '-' ) );
            if ( $date ) {
                $this->_where[] = "civicrm_activity_history.activity_date >= '$date'"; 
                $this->_tables['civicrm_activity_history'] = 1;
                $qill[] = ts( 'greater than "%1"', array( 1 => $format ) );
            }
        } 

        if ( isset( $this->_params['activity_to_date'] ) ) {
            $revDate = array_reverse( $this->_params['activity_to_date'] );
            $date    = CRM_Utils_Date::format( $revDate );
            $format  = CRM_Utils_Date::customFormat( CRM_Utils_Date::format( $revDate, '-' ) );
            if ( $date ) {
                $this->_where[] = " ( civicrm_activity_history.activity_date <= '$date' ) "; 
                $this->_tables['civicrm_activity_history'] = 1; 
                $qill[] = ts( 'less than "%1"', array( 1 => $format ) );
            }
        }
        
        if ( ! empty( $qill ) ) {
            $this->_qill[] = ts('Activity Date - %1', array( 1 => implode( ' ' . ts('and') . ' ', $qill ) ) );
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
                                                    'birth_date'             => 1,
                                                    'gender'                 => 1,
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

    static function getPrimaryCondition( $value ) {
        if ( is_numeric( $value ) ) {
            $value = (int ) $value;
            return ( $value == 1 ) ?'is_primary = 1' : 'is_primary = 0';
        }
        return null;
    }

    static function getQuery( $params = null, $returnProperties = null, $count = false ) {
        $query =& new CRM_Contact_BAO_Query( $params, $returnProperties );
        list( $select, $from, $where ) = $query->query( );
        return "$select $from $where";
    }

    static function apiQuery( $params = null, $returnProperties = null, $sort = null, $offset = 0, $row_count = 25 ) {
        $query = new CRM_Contact_BAO_Query( $params, $returnProperties, null );
        list( $select, $from, $where ) = $query->query( );
        $sql = "$select $from $where";
        if ( ! empty( $sort ) ) {
            $sql .= " ORDER BY $sort ";
        }
        if ( $row_count > 0 && $offset >= 0 ) {
            $sql .= " LIMIT $offset, $row_count ";
        }

        $dao = CRM_Core_DAO::executeQuery( $sql );
        $values = array( );
        while ( $dao->fetch( ) ) {
            $values[$dao->contact_id] = $query->store( $dao );
        }
        return $values;
    }



    /**
     * create and query the db for an contact search
     *
     * @param array    $formValues array of reference of the form values submitted
     * @param int      $action   the type of action links
     * @param int      $offset   the offset for the query
     * @param int      $rowCount the number of rows to return
     * @param boolean  $count    is this a count only query ?
     * @param boolean  $includeContactIds should we include contact ids?
     * @param boolean  $sortByChar if true returns the distinct array of first characters for search results
     * @param boolean  $groupContacts if true, use a single mysql group_concat statement to get the contact ids
     *
     * @return CRM_Contact_DAO_Contact 
     * @access public
     */
    function searchQuery( $offset, $rowCount, $sort, 
                          $count = false, $includeContactIds = false,
                          $sortByChar = false, $groupContacts = false,
                          $returnQuery = false ) {
        require_once 'CRM/Core/Permission.php';

        if ( $includeContactIds ) {
            $this->_includeContactIds = true;
            $this->includeContactIds( );
        }

        // hack for now, add permission only if we are in search
        $permission = ' ( 1 ) ';
        if ( $this->_search ) {
            $permission = CRM_Core_Permission::whereClause( CRM_Core_Permission::VIEW, $this->_tables );
            
            // regenerate fromClause since permission might have added tables
            if ( $permission ) {
                $this->_fromClause  = self::fromClause( $this->_tables, null, null, $this->_primaryLocation ); 
            }
        }

        list( $select, $from, $where ) = $this->query( $count, $sortByChar, $groupContacts );
        
        if ( empty( $where ) ) {
            $where = 'WHERE ' . $permission;
        } else {
            $where = $where . ' AND ' . $permission;
        }

        $order = $limit = '';

        if ( ! $count ) {
            if ($sort) {
                $order = " ORDER BY " . $sort->orderBy(); 
            } else if ($sortByChar) { 
                $order = " ORDER BY LEFT(civicrm_contact.sort_name, 1) ";
            }
            if ( $rowCount > 0 && $offset >= 0 ) {
                $limit = " LIMIT $offset, $rowCount ";
            }
        }

        // building the query string
        $query = $select . $from . $where . $order . $limit;
        if ( $returnQuery ) {
            return $query;
        }
        
        if ( $count ) {
            return CRM_Core_DAO::singleValueQuery( $query );
        }

        //CRM_Core_Error::debug( 'q', $query );
        $dao =& CRM_Core_DAO::executeQuery( $query );
        if ( $groupContacts ) {
            $ids = array( );
            while ( $dao->fetch( ) ) {
                $ids[] = $dao->id;
            }
            return implode( ',', $ids );
        }

        return $dao;
    }

    function qill( ) {
        return $this->_qill;
    }

}
