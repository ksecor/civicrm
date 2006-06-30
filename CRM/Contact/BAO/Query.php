<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.4                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright (c) 2005 Donald A. Lobo                                  | 
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
 * @copyright Donald A. Lobo (c) 2005 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Core/DAO/Location.php'; 
require_once 'CRM/Core/DAO/Address.php'; 
require_once 'CRM/Core/DAO/Phone.php'; 
require_once 'CRM/Core/DAO/Email.php'; 

class CRM_Contact_BAO_Query {
  
    /**
     * The various search modes
     *
     * @var int
     */
    const
        MODE_CONTACTS   =  1,
        MODE_CONTRIBUTE =  2,
        MODE_QUEST      =  4,
        MODE_MEMBER     =  8,
        MODE_ALL        = 15;
    
    /**
     * the default set of return properties
     *
     * @var array
     * @static
     */
    static $_defaultReturnProperties;

    /**
     * the default set of hier return properties
     *
     * @var array
     * @static
     */
    static $_defaultHierReturnProperties;
    
    /** 
     * the set of input params
     * 
     * @var array 
     */ 
    public $_params;

    public $_cfIDs;

    public $_paramLookup;

    /** 
     * the set of output params
     * 
     * @var array 
     */ 
    public $_returnProperties;

    /** 
     * the select clause 
     * 
     * @var array 
     */
    public $_select;

    /** 
     * the name of the elements that are in the select clause 
     * used to extract the values 
     * 
     * @var array 
     */ 
    public $_element;
 
    /**  
     * the tables involved in the query 
     *  
     * @var array  
     */  
    public $_tables;

    /**
     * the table involved in the where clause
     *
     * @var array
     */
    public $_whereTables;

    /**  
     * the where clause  
     *  
     * @var array  
     */  
    public $_where;

    /**   
     * the where string
     *
     * @var string
     *
     */
    public $_whereClause;

    /**    
     * the from string 
     * 
     * @var string 
     * 
     */ 
    public $_fromClause;

    /**
     * the from clause for the simple select and alphabetical
     * select
     *
     * @var string
     */
    public $_simpleFromClause;

    /** 
     * The english language version of the query 
     *   
     * @var array   
     */  
    public $_qill;

    /**
     * All the fields that could potentially be involved in
     * this query
     *
     * @var array
     */
    public    $_fields;

    /** 
     * The cache to translate the option values into labels 
     *    
     * @var array    
     */  
    public    $_options;

    /**
     * are we in search mode
     *
     * @var boolean
     */
    public $_search = true;

    /**
     * are we in strict mode (use equality over LIKE)
     *
     * @var boolean
     */
    public $_strict = false;

    public $_mode = 1;

    /** 
     * Should we only search on primary location
     *    
     * @var boolean
     */  
    public $_primaryLocation = true;

    /**
     * are contact ids part of the query
     *
     * @var boolean
     */
    public $_includeContactIds = false;

    /**
     * reference to the query object for custom values
     *
     * @var Object
     */
    public $_customQuery;

    /**
     * should we enable the distinct clause, used if we are including
     * more than one group
     *
     * @var boolean
     */
    public $_useDistinct = false;

    /**
     * the default set of return properties
     *
     * @var array
     * @static
     */
    static $_relType;
    

    /**
     * The tables which have a dependency on location and/or address
     *
     * @var array
     * @static
     */
    static $_dependencies = array( 'civicrm_state_province' => 1,
                                   'civicrm_country'        => 1,
                                   'civicrm_address'        => 1,
                                   'civicrm_phone'          => 1,
                                   'civicrm_email'          => 1,
                                   'civicrm_im'             => 1,
                                   'civicrm_location_type'  => 1,
                                   );

    /**
     * class constructor which also does all the work
     *
     * @param array   $params
     * @param array   $returnProperties
     * @param array   $fields
     * @param boolean $includeContactIds
     * @param boolean $strict
     * @param boolean $mode - mode the search is operating on
     *
     * @return Object
     * @access public
     */
    function __construct( $params = null, $returnProperties = null, $fields = null,
                          $includeContactIds = false, $strict = false, $mode = 1 ) {
        require_once 'CRM/Contact/BAO/Contact.php';

        //CRM_Core_Error::backtrace( );
        // CRM_Core_Error::debug( 'params', $params );
        // CRM_Core_Error::debug( 'f', $fields );
        // exit( );
        //CRM_Core_Error::debug( 'post', $_POST );
        //CRM_Core_Error::debug( 'r', $returnProperties );
        $this->_params =& $params;

        if ( empty( $returnProperties ) ) {
            $this->_returnProperties =& self::defaultReturnProperties( $mode );
        } else {
            $this->_returnProperties =& $returnProperties;
        }

        $this->_includeContactIds = $includeContactIds;
        $this->_strict            = $strict;
        $this->_mode              = $mode;

        if ( $fields ) {
            $this->_fields =& $fields;
            $this->_search = false;
        } else {
            require_once 'CRM/Contact/BAO/Contact.php';
            $this->_fields = CRM_Contact_BAO_Contact::exportableFields( 'All', false, true );

            require_once 'CRM/Core/Component.php';
            $fields =& CRM_Core_Component::getQueryFields( );
            unset( $fields['note'] );

            $this->_fields = array_merge( $this->_fields, $fields );
        }

        // basically do all the work once, and then reuse it
        $this->initialize( );
        //CRM_Core_Error::debug( 'q', $this );
    }

    /**
     * function which actually does all the work for the constructor
     *
     * @return void
     * @access private
     */
    function initialize( ) {
        $this->_select      = array( ); 
        $this->_element     = array( ); 
        $this->_tables      = array( );
        $this->_whereTables = array( );
        $this->_where       = array( ); 
        $this->_qill        = array( ); 
        $this->_options     = array( );
        $this->_cfIDs       = array( );
        $this->_paramLookup = array( );

        $this->_customQuery = null; 
 
        $this->_select['contact_id']      = 'contact_a.id as contact_id';
        $this->_element['contact_id']     = 1; 
        $this->_tables['civicrm_contact'] = 1;
        $this->_whereTables['civicrm_contact'] = 1;

        if ( ! empty( $this->_params ) ) {
            $this->buildParamsLookup( );
        }

        $this->selectClause( ); 
        $this->_whereClause      = $this->whereClause( );
        $this->_fromClause       = self::fromClause( $this->_tables     , null, null, $this->_primaryLocation, $this->_mode ); 
        $this->_simpleFromClause = self::fromClause( $this->_whereTables, null, null, $this->_primaryLocation, $this->_mode );
    }

    function buildParamsLookup( ) {

        foreach ( $this->_params as $value ) {
            $cfID = CRM_Core_BAO_CustomField::getKeyID( $value[0] );
            if ( $cfID ) {
                if ( ! array_key_exists( $cfID, $this->_cfIDs ) ) {
                    $this->_cfIDs[$cfID] = array( );
                }
                $this->_cfIDs[$cfID][] = $value;
            }

            if ( ! array_key_exists( $value[0], $this->_paramLookup ) ) {
                $this->_paramLookup[$value[0]] = array( );
            }
            $this->_paramLookup[$value[0]][] = $value;
        }
    }

    /**
     * Some composite fields do not appear in the fields array
     * hack to make them part of the query
     *
     * @return void 
     * @access public 
     */
    function addSpecialFields( ) {
        static $special = array( 'contact_type', 'contact_sub_type', 'sort_name', 'display_name' );
        foreach ( $special as $name ) {
            if ( CRM_Utils_Array::value( $name, $this->_returnProperties ) ) { 
                $this->_select[$name]  = "contact_a.{$name} as $name";
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

        $this->addSpecialFields( );

        //CRM_Core_Error::debug( 'f', $this->_fields );
        //CRM_Core_Error::debug( 'p', $this->_params );
        
        foreach ($this->_fields as $name => $field) {
            // if this is a hierarchical name, we ignore it
            $names = explode( '-', $name );
            if ( count( $names > 1 ) && is_numeric( $names[1] ) ) {
                continue;
            }

            $cfID = CRM_Core_BAO_CustomField::getKeyID( $name );
            if ( CRM_Utils_Array::value( $name, $this->_paramLookup ) ||
                 CRM_Utils_Array::value( $name, $this->_returnProperties ) ) {

                if ( $cfID ) {
                    // add to cfIDs array if not present
                    if ( ! array_key_exists( $cfID, $this->_cfIDs ) ) {
                        $this->_cfIDs[$cfID] = null;
                    }
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
                        
                        //special case for phone
                        if ($name == 'phone') {
                            $this->_select ['phone_type'] = "civicrm_phone.phone_type as phone_type";
                            $this->_element['phone_type'] = 1;
                        }

                        if ( $name == 'state_province' ) {
                            $this->_select [$name]              = "civicrm_state_province.abbreviation as `$name`";
                        } else if ( $tName == 'contact' ) {
                            if ( $fieldName != 'id' ) {
                                $this->_select [$name]          = "contact_a.{$fieldName}  as `$name`";
                            }
                        } else {
                            $this->_select [$name]              = "{$field['where']} as `$name`";
                        }
                        $this->_element[$name]             = 1;

                    }
                } elseif ($name === 'tags') {
                    $this->_select[$name               ] = "GROUP_CONCAT(DISTINCT(civicrm_tag.name)) AS tags";
                    $this->_tables['civicrm_tag'       ] = 1;
                    $this->_tables['civicrm_entity_tag'] = 1;
                } elseif ($name === 'groups') {
                    $this->_select[$name                  ] = "GROUP_CONCAT(DISTINCT(civicrm_group.name)) AS groups";
                    $this->_tables['civicrm_group'        ] = 1;
                    $this->_tables['civicrm_group_contact'] = 1;
                }
            } else if ( CRM_Utils_Array::value( 'is_search_range', $field ) ) {
                // this is a custom field with range search enabled, so we better check for two/from values
                if ( $cfID ) {
                    if ( CRM_Utils_Array::value( $name . '_from', $this->_paramLookup ) ) {
                        if ( ! array_key_exists( $cfID, $this->_cfIDs ) ) {
                            $this->_cfIDs[$cfID] = array( );
                        }
                        foreach ( $this->_paramLookup[$name . '_from'] as $pID => $p ) {
                            // search in the cdID array for the same grouping
                            $fnd = false;
                            foreach ( $this->_cfIDs[$cfID] as $cID => $c ) {
                                if ( $c[3] == $p[3] ) {
                                    $this->_cfIDs[$cfID][$cID][2]['from'] = $p[2];
                                    $fnd = true;
                                }
                            }
                            if ( ! $fnd ) {
                                $p[2] = array( 'from' => $p[2] );
                                $this->_cfIDs[$cfID][] = $p;
                            }
                        }
                    }
                    if ( CRM_Utils_Array::value( $name . '_to', $this->_paramLookup ) ) {
                        if ( ! array_key_exists( $cfID, $this->_cfIDs ) ) {
                            $this->_cfIDs[$cfID] = array( );
                        }
                        foreach ( $this->_paramLookup[$name . '_to'] as $pID => $p ) {
                            // search in the cdID array for the same grouping
                            $fnd = false;
                            foreach ( $this->_cfIDs[$cfID] as $cID => $c ) {
                                if ( $c[4] == $p[4] ) {
                                    $this->_cfIDs[$cfID][$cID][2]['to'] = $p[2];
                                    $fnd = true;
                                }
                            }
                            if ( ! $fnd ) {
                                $p[2] = array( 'to' => $p[2] );
                                $this->_cfIDs[$cfID][] = $p;
                            }
                        }

                    }
                }
            }
        }

        // add location as hierarchical elements
        $this->addHierarchicalElements( );
        
        //fix for CRM-951
        CRM_Core_Component::alterQuery( $this, 'select' );

        if ( ! empty( $this->_cfIDs ) ) {
            require_once 'CRM/Core/BAO/CustomQuery.php';
            $this->_customQuery = new CRM_Core_BAO_CustomQuery( $this->_cfIDs );
            $this->_customQuery->query( );
            $this->_select       = array_merge( $this->_select , $this->_customQuery->_select );
            $this->_element      = array_merge( $this->_element, $this->_customQuery->_element);
            $this->_tables       = array_merge( $this->_tables , $this->_customQuery->_tables );
            $this->_whereTables  = array_merge( $this->_whereTables , $this->_customQuery->_whereTables );
            $this->_options      = $this->_customQuery->_options;
        }
    }

    /**
     * If the return Properties are set in a hierarchy, traverse the hierarchy to get
     * the return values
     *
     * @return void 
     * @access public 
     */
    function addHierarchicalElements( ) {
        if ( ! CRM_Utils_Array::value( 'location', $this->_returnProperties ) ) {
            return;
        }
        if ( ! is_array( $this->_returnProperties['location'] ) ) {
            return;
        }

        $locationTypes = CRM_Core_PseudoConstant::locationType( );
        $processed     = array( );
        $index = 0;

        foreach ( $this->_returnProperties['location'] as $name => $elements ) {
            $index++;
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

            $locationJoin = $locationTypeJoin = $addressJoin = $locationIndex = null;

            $tName = "$name-location";
            $this->_select["{$tName}_id"]  = "`$tName`.id as `{$tName}_id`"; 
            $this->_element["{$tName}_id"] = 1; 
            $locationJoin = "\nLEFT JOIN civicrm_location $lName ON ($lName.entity_table = 'civicrm_contact' AND $lName.entity_id = contact_a.id AND $lCond )"; 
            $this->_tables[ $tName ] = $locationJoin;            
            $locationIndex = $index;

            $tName  = "$name-location_type";
            $ltName ="`$name-location_type`";
            $this->_select["{$tName}_id" ]  = "`$tName`.id as `{$tName}_id`"; 
            $this->_select["{$tName}"    ]  = "`$tName`.name as `{$tName}`"; 
            $this->_element["{$tName}_id"]  = 1;
            $this->_element["{$tName}"   ]  = 1;  
            $locationTypeJoin = "\nLEFT JOIN civicrm_location_type $ltName ON ($lName.location_type_id = $ltName.id )";
            $this->_tables[ $tName ] = $locationTypeJoin;

            $tName = "$name-address";
            $aName = "`$name-address`";
            $this->_select["{$tName}_id"]  = "`$tName`.id as `{$tName}_id`"; 
            $this->_element["{$tName}_id"] = 1; 
            $addressJoin = "\nLEFT JOIN civicrm_address $aName ON ($aName.location_id = $lName.id)";
            $this->_tables[ $tName ] = $addressJoin;

            $processed[$lName] = $processed[$aName] = 1;
            foreach ( $elements as $elementFullName => $dontCare ) {
                $index++;
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

                // hack for profile, add location id
                if ( ! $field ) {
                    if ( ! is_numeric($elementType) ) { //fix for CRM-882( to handle phone types )
                        $field =& CRM_Utils_Array::value( $elementName . "-$locationTypeId$elementType", $this->_fields );
                    } else {
                        $field =& CRM_Utils_Array::value( $elementName . "-$locationTypeId", $this->_fields );
                    }
                }

                // check if there is a value, if so also add to where Clause
                $addWhere = false;
                if ( $this->_params ) {
                    $nm = $elementName . "-$locationTypeId";
                    if ( !is_numeric($elementType) ) {
                        $nm .= "$elementType";
                    }

                    foreach ( $this->_params as $id => $values ) {
                        if ( $values[0] == $nm ) {
                            $addWhere = true;
                            break;
                        }
                    }
                }

                if ( $addWhere ) {
                    $this->_whereTables[ "{$name}-location" ] = $locationJoin;
                    $this->_whereTables[ "{$name}-location_type" ] = $locationTypeJoin;
                }
                
                if ( $field && isset( $field['where'] ) ) {
                    list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 );  
                    $tName = $name . '-' . substr( $tableName, 8 ) . $elementType;
                    $fieldName = $fieldName;
                    if ( isset( $tableName ) ) {
                        $this->_select["{$tName}_id"]                   = "`$tName`.id as `{$tName}_id`";
                        $this->_element["{$tName}_id"]                  = 1;
                        if ( substr( $tName, -15 ) == '-state_province' ) {
                            $this->_select["{$name}-{$elementFullName}"]  = "`$tName`.abbreviation as `{$name}-{$elementFullName}`";
                        } else {
                            $this->_select["{$name}-{$elementFullName}"]  = "`$tName`.$fieldName as `{$name}-{$elementFullName}`";
                        }
                        $this->_element["{$name}-{$elementFullName}"] = 1;
                        if ( ! CRM_Utils_Array::value( "`$tName`", $processed ) ) {
                            $processed["`$tName`"] = 1;
                            $newName = $tableName . '_' . $index;
                            switch ( $tableName ) {
                            case 'civicrm_phone':
                            case 'civicrm_email':
                            case 'civicrm_im':
                                $this->_tables[$tName] = "\nLEFT JOIN $tableName `$tName` ON $lName.id = `$tName`.location_id AND `$tName`.$cond";
                                if ( $addWhere ) {
                                    $this->_whereTables[$tName] = $this->_tables[$tName];
                                }
                                break;

                            case 'civicrm_state_province':
                                $this->_tables[$tName] = "\nLEFT JOIN $tableName `$tName` ON `$tName`.id = $aName.state_province_id";
                                if ( $addWhere ) {
                                    $this->_whereTables[ "{$name}-address" ] = $addressJoin;
                                    $this->_whereTables[$tName] = $this->_tables[$tName];
                                }
                                break;

                            case 'civicrm_country':
                                $this->_tables[$newName] = "\nLEFT JOIN $tableName `$tName` ON `$tName`.id = $aName.country_id";
                                if ( $addWhere ) {
                                    $this->_whereTables[ "{$name}-address" ] = $addressJoin;
                                    $this->_whereTables[$newName] = $this->_tables[$newName];
                                }
                                break;

                            default:
                                if ( $addWhere ) {
                                    $this->_whereTables[ "{$name}-address" ] = $addressJoin;
                                }
                                break;

                            }
                        }
                    }
                }
            }
        }
    }

    /** 
     * generate the query based on what type of query we need
     *
     * @param boolean $count
     * @param boolean $sortByChar
     * @param boolean $groupContacts
     * 
     * @return the sql string for that query (this will most likely
     * change soon)
     * @access public 
     */ 
    function query( $count = false, $sortByChar = false, $groupContacts = false ) {
        if ( $count ) {
            if ( $this->_useDistinct ) {
                $select = 'SELECT count(DISTINCT contact_a.id)';
            } else {
                // $select = 'SELECT count(contact_a.id)'; 
                // using count(*) since we've heard this is slightly faster :)
                $select = 'SELECT count(*)'; 
            }
            $from = $this->_simpleFromClause;
        } else if ( $sortByChar ) {  
            $select = 'SELECT DISTINCT UPPER(LEFT(contact_a.sort_name, 1)) as sort_name';
            $from = $this->_simpleFromClause;
        } else if ( $groupContacts ) { 
            if ( $this->_useDistinct ) { 
                $select  = 'SELECT DISTINCT(contact_a.id) as id'; 
            } else {
                $select  = 'SELECT contact_a.id as id'; 
            }
            $from = $this->_simpleFromClause;
        } else {
            if ( $this->_useDistinct ) {
                $this->_select['contact_id'] = 'DISTINCT(contact_a.id) as contact_id';
            }
            $select = 'SELECT ' . implode( ', ', $this->_select );
            $from = $this->_fromClause;
        }

        $where = '';
        if ( ! empty( $this->_whereClause ) ) {
            $where = "WHERE {$this->_whereClause}";
        }

        //CRM_Core_Error::debug( "t", $this );
        //CRM_Core_Error::debug( "$select, $from $where", $where );
        return array( $select, $from, $where );
    }

    function &getWhereValues( $name, $grouping ) {
        foreach ( $this->_params as $id => $values ) {
            if ( $values[0] == $name && $values[4] == $grouping ) {
                return $values;
            }
        }
        return null;
    }

    static function &fixWhereValues( $id, &$values ) {
        // skip a few search variables
        static $skipWhere   = null;
        static $arrayValues = null;

        if ( CRM_Utils_System::isNull( $values ) ) {
            return null;
        }

        if  ( ! $skipWhere ) {
            $skipWhere   = array( 'task', 'radio_ts', 'uf_group_id' );
        }

        if ( in_array( $id, $skipWhere ) || substr( $id, 0, 4 ) == '_qf_' ) {
            return null;
        }

        if ( $id == 'sort_name' ) {
            return array( $id, 'LIKE', $values, 0, 1 );
        } else if ( strpos( $values, '%' ) !== false ) {
            return array( $id, 'LIKE', $values, 0, 0 );
        } else if ( $id == 'group' || $id == 'tag' ) {
            return array( $id, 'IN', $values, 0, 0 );
        } else {
            return array( $id, '=', $values, 0, 0 );
        }
    }

    function whereClauseSingle( &$values ) {

        // do not process custom fields or prefixed contact ids or component params
        if ( CRM_Core_BAO_CustomField::getKeyID( $values[0] ) ||
             ( substr( $values[0], 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) ||
             ( substr( $values[0], 0, 13 ) == 'contribution_' ) ||
             ( substr( $values[0], 0, 6  ) == 'quest_' ) ) {
            return;
        }

        switch ( $values[0] ) {

        case 'contact_type':
            $this->contactType( $values );
            return;

        case 'group':
            $this->group( $values );
            return;

        case 'tag':
            $this->tag( $values );
            return;

        case 'sort_name':
            $this->sortName( $values );
            return;

        case 'sortByCharacter':
            $this->sortByCharacter( $values );
            return;

        case 'location_name':
            $this->locationName( $values ); 
            return;

        case 'location_type':
            $this->locationType( $values ); 
            return;

        case 'postal_code':
        case 'postal_code_low':
        case 'postal_code_high':
            $this->postalCode( $values );
            return;

        case 'activity_type':
            $this->activityType( $values );
            return;

        case 'activity_date':
        case 'activity_date_low':
        case 'activity_date_high':
            $this->activityDate( $values );
            return;
            
        case 'do_not_phone':
        case 'do_not_email':
        case 'do_not_mail':
        case 'do_not_trade':
        case 'is_opt_out':
            $this->privacy( $values );

        case 'relation_type_id':
            $this->relationship( $values );
            return;

        case 'relation_target_name':
            // since this case is handled with the above
            return;

        default:
            $this->restWhere( $values );
            return;
                
        }

    }

    /** 
     * Given a list of conditions in params generate the required
     * where clause
     * 
     * @return void 
     * @access public 
     */ 
    function whereClause( ) {
        $this->_where[0] = array( );

        $config =& CRM_Core_Config::singleton( );

        require_once 'CRM/Core/BAO/Domain.php';
        if ( CRM_Core_BAO_Domain::multipleDomains( ) ) {
            $this->_where[0][] = 'contact_a.domain_id = ' . $config->domainID( );
        }

        $this->includeContactIds( );
        
        // CRM_Core_Error::debug( 'p', $this->_params );
        if ( ! empty( $this->_params ) ) {
            foreach ( array_keys( $this->_params ) as $id ) {
                // check for both id and contact_id
                if ( $this->_params[$id][0] == 'id' || $this->_params[$id][0] == 'contact_id' ) {
                    $this->_where[0][] = "contact_a.id = {$this->_params[$id][2]}";
                } else {
                    $this->whereClauseSingle( $this->_params[$id] );
                }
            }

            CRM_Core_Component::alterQuery( $this, 'where' );
        }

        if ( $this->_customQuery ) {
            $this->_where = array_merge_recursive( $this->_where, $this->_customQuery->_where );
            $this->_qill  = array_merge_recursive( $this->_qill , $this->_customQuery->_qill  );
        }

        $clauses = array( );

        foreach ( $this->_where as $grouping => $values ) {
            if ( $grouping > 0 && ! empty( $values ) ) {
                $clauses[$grouping] = ' ( ' . implode( ' AND ', $values ) . ' ) ';
            }
        }

        // CRM_Core_Error::debug( 'c', $clauses );

        $andClauses = array( );
        if ( ! empty( $this->_where[0] ) ) {
            $andClauses[] = ' ( ' . implode( ' AND ', $this->_where[0] ) . ' ) ';
        }
        if ( ! empty( $clauses ) ) {
            $andClauses[] = ' ( ' . implode( ' OR ', $clauses ) . ' ) ';
        }

        // CRM_Core_Error::debug( 'a', $andClauses );
        return implode( ' AND ', $andClauses );
    }

    function restWhere( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        if ( ! CRM_Utils_Array::value( $grouping, $this->_where ) ) {
            $this->_where[$grouping] = array( );
        }

        //check if the location type exits for fields
        $lType = '';
        $locType = array( );
        $locType = explode('-', $name);

        //add phone type if exists
        if ($locType[2]) {
            $locType[2] = addslashes( $locType[2] );
        }

        $field = CRM_Utils_Array::value( $name, $this->_fields );
        if ( ! $field ) {
            $field = CRM_Utils_Array::value( $locType[0], $this->_fields );
            if ( ! $field ) {
                return;
            }
        }

        $setTables = true;

        // FIXME: the LOWER/strtolower pairs below most probably won't work
        // with non-US-ASCII characters, as even if MySQL does the proper
        // thing with LOWER-ing them (4.0 almost certainly won't, but then
        // we don't officially support 4.0 for non-US-ASCII data), PHP
        // won't do the proper thing with strtolower-ing them unless the
        // underlying operating system uses an UTF-8 locale for LC_CTYPE
        // for the user the webserver runs at (or suEXECs); we should use
        // mb_strtolower(), but then we'd require mb_strings support; we
        // could wrap this in function_exist(), though
        if ( substr($name,0,14) === 'state_province' ) {
            $states =& CRM_Core_PseudoConstant::stateProvince(); 
            if ( is_numeric( $value ) ) {
                $value  =  $states[(int ) $value];
            }
            $this->_where[$grouping][] = 'LOWER(' . $field['where'] . ") $op '" . strtolower( addslashes( $value ) ) . "'";
            if (!$lType) {
                $this->_qill[$grouping][] = ts('State %2 "%1"', array( 1 => $value, 2 => $op ) );         
            } else {
                $this->_qill[$grouping][] = ts('State (%2) %3 "%1"', array( 1 => $value, 2 => $lType, 3 => $op ) );         
            }
        } else if ( substr($name,0,7) === 'country' ) {
            $countries =& CRM_Core_PseudoConstant::country( ); 
            if ( is_numeric( $value ) ) { 
                $value     =  $countries[(int ) $value]; 
            }
            $this->_where[$grouping][] = 'LOWER(' . $field['where'] . ") $op '" . strtolower( addslashes( $value ) ) . "'"; 
            if (!$lType) {
                $this->_qill[$grouping][] = ts('Country %2 "%1"', array( 1 => $value, 2 => $op ) );
            } else {
                $this->_qill[$grouping][] = ts('Country (%2) %3 "%1"', array( 1 => $value, 2 => $lType, 3 => $op ) );         
            }

        } else if ( $name === 'individual_prefix' ) {
            $individualPrefixs =& CRM_Core_PseudoConstant::individualPrefix( ); 
            if ( is_numeric( $value ) ) { 
                $value     =  $individualPrefixs[(int ) $value];  
            }
            $this->_where[$grouping][] = 'LOWER(' . $field['where'] . ') = "' . strtolower( addslashes( $value ) ) . '"';
            $this->_qill[$grouping][] = ts('Individual Prefix - "%1"', array( 1 => $value ) );
        } else if ( $name === 'individual_suffix' ) {
            $individualSuffixs =& CRM_Core_PseudoConstant::individualsuffix( ); 
            if ( is_numeric( $value ) ) { 
                $value     =  $individualSuffixs[(int ) $value];  
            }
            $this->_where[$grouping][] = 'LOWER(' . $field['where'] . ') = "' . strtolower( addslashes( $value ) ) . '"';
            $this->_qill[$grouping][] = ts('Individual Suffix - "%1"', array( 1 => $value ) );
        } else if ( $name === 'gender' ) {
            $genders =& CRM_Core_PseudoConstant::gender( );  
            if ( is_numeric( $value ) ) {  
                $value     =  $genders[(int ) $value];  
            }
            $this->_where[$grouping][] = "LOWER({$field['where']}) $op '" . strtolower( addslashes( $value ) ) . "'"; 
            $this->_qill[$grouping][] = ts('Gender %2 "%1"', array( 1 => $value, 2 => $op ) ); 
        } else if ( $name === 'birth_date' ) {
            $date = CRM_Utils_Date::format( $value );
            if ( $date ) {
                $this->_where[$grouping][] = $field['where'] . " $op $date";
                $date = CRM_Utils_Date::customFormat( $value );
                $this->_qill[$grouping][]  = "$field[title] - \"$date\"";
            }
        } else if ( $name === 'contact_id' ) {
            if ( is_int( $value ) ) {
                $this->_where[$grouping][] = $field['where'] . " $op $value";
                $this->_qill[$grouping][]  = ts( "%1 $op %2", array( 1 => $field['title'], 2 => $value ) );
            }
        } else if ( $name === 'name' ) {
            $value = strtolower( addslashes( $value ) );
            if ( $wildcard ) {
                $value = "%$value%"; 
            }
            $this->_where[$grouping][] = "LOWER( {$field['where']} ) $op '$value'";
            $this->_qill[$grouping][]  = ts( '%1 $op "%2"', array( 1 => $field['title'], 2 => $value ) );
        } else {
            // sometime the value is an array, need to investigate and fix
            if ( is_array( $value ) ) {
                CRM_Core_Error::debug( 'v', $values );
                CRM_Core_Error::fatal( ts( 'This is an unexpected place to be in, contact support' ) );
            }

            if ( ! empty( $field['where'] ) ) {
                $value = strtolower( addslashes( $value ) );
                if ( $wildcard ) {
                    $value = "%$value%"; 
                }

                if (is_numeric($locType[1])) {
                    $setTables = false;
                    list($tbName, $fldName) = explode("." , $field['where']);
                    
                    //get the location name 
                    $locationType =& CRM_Core_PseudoConstant::locationType();
                    if ( $locType[0] == 'email' || $locType[0] == 'im' || $locType[0] == 'phone' ) {
                        if ($locType[2]) {
                            $tName = $locationType[$locType[1]] . "-" . $locType[0] . '-' . $locType[2];
                        } else {
                            $tName = $locationType[$locType[1]] . "-" . $locType[0] . '-1';
                        }
                    } else {
                        $tName = $locationType[$locType[1]] . "-address";
                    }
                    $where = "`$tName`.$fldName";
                    $this->_where[$grouping][] = "LOWER( $where ) $op '$value'";
                    $this->_whereTables[$tName] = $this->_tables[$tName];
                    if ( $locType[2] && ( strtolower( $locType[2] ) != ts( 'phone' ) ) ) {
                        $this->_qill[$grouping][]  = ts( "%1-%4 (%3) $op '%2'", array( 1 => $field['title'],
                                                                                       2 => $value,
                                                                                       3 => $locationType[$locType[1]],
                                                                                       4 => $locType[2] ) );
                    } else {
                        $this->_qill[$grouping][]  = ts( "%1 (%3) $op '%2'", array( 1 => $field['title'],
                                                                                    2 => $value,
                                                                                    3 => $locationType[$locType[1]] ) );
                    }
                } else {
                    $this->_where[$grouping][] = "LOWER( {$field['where']} ) $op '$value'";
                    $this->_qill[$grouping][]  = ts( "%1 $op '%2'", array( 1 => $field['title'], 2 => $value ) );
                }
                
            }
        }

        if ( $setTables ) {
            list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 );  
            if ( isset( $tableName ) ) { 
                $this->_tables[$tableName] = 1;  
                $this->_whereTables[$tableName] = 1;  
            }
        }
    }

        
    /**
     * Given a result dao, extract the values and return that array
     *
     * @param Object $dao
     *
     * @return array values for this query
     */
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

    /**
     * getter for tables array
     *
     * @return array
     * @access public
     */
    function tables( ) {
        return $this->_tables;
    }

    function whereTables( ) {
        return $this->_whereTables;
    }

    /**
     * generate the where clause (used in match contacts and permissions)
     *
     * @param array $params
     * @param array $fields
     * @param array $tables
     * @param boolean $strict
     * 
     * @return string
     * @access public
     * @static
     */
    static function getWhereClause( $params, $fields, &$tables, &$whereTables, $strict = false ) {
        $query =& new CRM_Contact_BAO_Query( $params, null, $fields,
                                             false, $strict );

        $tables      = array_merge( $query->tables( ), $tables );
        $whereTables = array_merge( $query->whereTables( ), $whereTables );

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
    static function fromClause( &$tables , $inner = null, $right = null, $primaryLocation = true, $mode = 1 ) {

        $from = ' FROM civicrm_contact contact_a';
        if ( empty( $tables ) ) {
            return $from;
        }

        if ( ( CRM_Utils_Array::value( 'civicrm_gender', $tables ) ||
               CRM_Utils_Array::value( 'civicrm_individual_prefix' , $tables ) ||
               CRM_Utils_Array::value( 'civicrm_individual_suffix' , $tables )) &&
             ! CRM_Utils_Array::value( 'civicrm_individual'       , $tables ) ) {
            $tables = array_merge( array( 'civicrm_individual' => 1 ),
                                   $tables );
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


        //add contribution table
        if ( CRM_Utils_Array::value( 'civicrm_product', $tables ) ) {
            $tables = array_merge( array( 'civicrm_contribution' => 1), $tables );
        }

        //format the table list according to the weight
        require_once 'CRM/Core/TableHierarchy.php';
        $info =& CRM_Core_TableHierarchy::info( );

        foreach ($tables as $key => $value) {
            $k = 99;
            if ( strpos( $key, '-' ) ) {
                //echo $key . "<br>";
                $keyArray = explode('-', $key);
                $k = CRM_Utils_Array::value( 'civicrm_' . $keyArray[1], $info, 99 );
            } else if ( strpos( $key, '_' ) ) {
                $keyArray = explode( '_', $key );
                if ( is_numeric( array_pop( $keyArray ) ) ) {
                    $k = CRM_Utils_Array::value( implode( '_', $keyArray ), $info, 99 );
                } else {
                    $k = CRM_Utils_Array::value($key, $info, 99 );
                }
            } else {
                $k = CRM_Utils_Array::value($key, $info, 99 );
            }
            $tempTable[$k . ".$key"] = $key;
        }

        ksort($tempTable);

        $newTables = array ();
        foreach ($tempTable as $key) {
            $newTables[$key] = $tables[$key];
        }

        $tables = $newTables;
        //CRM_Core_Error::debug("aa", $tables);
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
                $from .= " $side JOIN civicrm_individual ON (contact_a.id = civicrm_individual.contact_id) ";
                continue;

            case 'civicrm_household':
                $from .= " $side JOIN civicrm_household ON (contact_a.id = civicrm_household.contact_id) ";
                continue;

            case 'civicrm_organization':
                $from .= " $side JOIN civicrm_organization ON (contact_a.id = civicrm_organization.contact_id) ";
                continue;

            case 'civicrm_location':
                $from .= " $side JOIN civicrm_location ON (civicrm_location.entity_table = 'civicrm_contact' AND
                                                           contact_a.id = civicrm_location.entity_id ";
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

            case 'civicrm_im_provider':
                $from .= " $side JOIN civicrm_im_provider ON civicrm_im.provider_id = civicrm_im_provider.id ";
                continue;

            case 'civicrm_state_province':
                $from .= " $side JOIN civicrm_state_province ON civicrm_address.state_province_id = civicrm_state_province.id ";
                continue;

            case 'civicrm_country':
                $from .= " $side JOIN civicrm_country ON civicrm_address.country_id = civicrm_country.id ";
                continue;
            
            case 'civicrm_location_type':
                $from .= " $side JOIN civicrm_location_type ON civicrm_location.location_type_id = civicrm_location_type.id ";
                continue;

            case 'civicrm_group':
                $from .= " $side JOIN civicrm_group ON civicrm_group.id =  civicrm_group_contact.group_id ";
                continue;

            case 'civicrm_group_contact':
                $from .= " $side JOIN civicrm_group_contact ON contact_a.id = civicrm_group_contact.contact_id ";
                continue;

            case 'civicrm_entity_tag':
                $from .= " $side JOIN civicrm_entity_tag ON ( civicrm_entity_tag.entity_table = 'civicrm_contact' AND
                                                             contact_a.id = civicrm_entity_tag.entity_id ) ";
                continue;

            case 'civicrm_note':
                $from .= " $side JOIN civicrm_note ON ( civicrm_note.entity_table = 'civicrm_contact' AND
                                                        contact_a.id = civicrm_note.entity_id ) "; 
                continue; 

            case 'civicrm_activity_history':
                $from .= " $side JOIN civicrm_activity_history ON ( civicrm_activity_history.entity_table = 'civicrm_contact' AND  
                                                               contact_a.id = civicrm_activity_history.entity_id ) ";
                continue;

            case 'civicrm_custom_value':
                $from .= " $side JOIN civicrm_custom_value ON ( civicrm_custom_value.entity_table = 'civicrm_contact' AND
                                                          contact_a.id = civicrm_custom_value.entity_id )";
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

            case 'civicrm_relationship':
                if( self::$_relType == 'a') {
                    $from .= " $side JOIN civicrm_relationship ON (civicrm_relationship.contact_id_b = contact_a.id )";
                    $from .= " $side JOIN civicrm_contact contact_b ON (civicrm_relationship.contact_id_a = contact_b.id )";
                } else {
                    $from .= " $side JOIN civicrm_relationship ON (civicrm_relationship.contact_id_a = contact_a.id )";
                    $from .= " $side JOIN civicrm_contact contact_b ON (civicrm_relationship.contact_id_b = contact_b.id )";
                }
                continue;

            case 'civicrm_entity_tag':
                $from .= " $side  JOIN  civicrm_entity_tag  ON ( civicrm_entity_tag.entity_table = 'civicrm_contact' 
                                                                  AND contact_a.id = civicrm_entity_tag.entity_id )";
                continue; 
                
            case 'civicrm_tag':
                $from .= " $side  JOIN civicrm_tag ON civicrm_entity_tag.tag_id = civicrm_tag.id ";
                continue; 
                
            case 'civicrm_group_contact':
                $from .= " $side  JOIN  civicrm_group_contact ON contact_a.id = civicrm_group_contact.contact_id ";
                continue; 
                
            case 'civicrm_group':
                $from .= " $side  JOIN civicrm_group ON civicrm_group_contact.group_id = civicrm_group.id ";
                continue; 
                
            default:
                $from .= CRM_Core_Component::from( $name, $mode, $side );
                continue;
            }
        }
        return $from;
    }

    /**
     * where / qill clause for contact_type
     *
     * @return void
     * @access public
     */
    function contactType( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        $clause = array( );
        if ( is_array( $value ) ) {
            foreach ( $value as $k => $v) { 
                if ($k) { //fix for CRM-771
                    $clause[] = "'" . CRM_Utils_Type::escape( $k, 'String' ) . "'";
                }
            }
        } else {
            $clause[] = "'" . CRM_Utils_Type::escape( $value, 'String' ) . "'";
        }
        
        if ( ! empty( $clause ) ) { //fix for CRM-771
            $this->_where[$grouping][] = 'contact_a.contact_type IN (' . implode( ',', $clause ) . ')';
            $this->_qill [$grouping][]  = ts('Contact Type -') . ' ' . implode( ' ' . ts('or') . ' ', $clause );
        }
    }

    /**
     * where / qill clause for groups
     *
     * @return void
     * @access public
     */
    function group( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        if ( count( $value ) > 1 ) {
            $this->_useDistinct = true;
        }

        $groupClause =
            "civicrm_group_contact.group_id $op (" . 
            implode( ',', array_keys($value) ) . ')'; 

        $names = array( );
        $groupNames =& CRM_Core_PseudoConstant::group();
        foreach ( $value as $id => $dontCare ) {
            $names[] = $groupNames[$id];
        }
        $this->_qill[$grouping][]  = ts('Member of Group %1', array( 1 => $op ) ) . ' ' . implode( ' ' . ts('or') . ' ', $names );
        
        $statii    =  array(); 
        $in        =  false; 
        $gcsValues =& $this->getWhereValues( 'group_contact_status', $grouping );

        if ( $gcsValues &&
             is_array( $gcsValues[2] ) ) {
            foreach ( $gcsValues[2] as $k => $v ) {
                if ( $v ) {
                    if ( $k == 'Added' ) {
                        $in = true;
                    }
                    $statii[] = "'" . CRM_Utils_Type::escape($k, 'String') . "'";
                }
            }
        } else {
            if ( $op == "IN" ) {
                $statii[] = '"Added"'; 
                $in = true; 
            }
        }

        $this->_tables['civicrm_group_contact'] = 1;
        $this->_whereTables['civicrm_group_contact'] = 1;

        if ( ! empty( $statii ) ) {
            $groupClause .= ' AND civicrm_group_contact.status IN (' . implode(', ', $statii) . ')';
            $this->_qill[$grouping][] = ts('Group Status -') . ' ' . implode( ' ' . ts('or') . ' ', $statii );
        }

        if ( $in ) {
            $ssClause = $this->savedSearch( $values );
            if ( $ssClause ) {
                $groupClause = "( ( $groupClause ) OR ( $ssClause ) )";
            }
        }
        
        $this->_where[$grouping][] = $groupClause;
    }
    
    /**
     * where / qill clause for smart groups
     *
     * @return void
     * @access public
     */
    function savedSearch( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        
        $config =& CRM_Core_Config::singleton( );
        $ssWhere = array(); 
        $group =& new CRM_Contact_BAO_Group(); 
        foreach ( array_keys( $value ) as $group_id ) { 
            $group->id = $group_id; 
            $group->find(true); 
            if (isset($group->saved_search_id)) {
                $this->_useDistinct = true;

                require_once 'CRM/Contact/BAO/SavedSearch.php';
                if ( $config->mysqlVersion >= 4.1 ) { 
                    $ssParams =& CRM_Contact_BAO_SavedSearch::getSearchParams($group->saved_search_id);
                    $returnProperties = array();
                    if (CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_SavedSearch', $group->saved_search_id, 'mapping_id' ) ) {
                        require_once "CRM/Core/BAO/Mapping.php";
                        $fv =& CRM_Contact_BAO_SavedSearch::getFormValues($group->saved_search_id);
                        $returnProperties = CRM_Core_BAO_Mapping::returnProperties( $fv );
                    }        

                    $query =& new CRM_Contact_BAO_Query($ssParams, $returnProperties);
                    $smarts =& $query->searchQuery($ssParams, 0, 0, null, false, false, true, true, true);
  
                    $ssWhere[] = " 
                            (contact_a.id IN ( $smarts )  
                            AND contact_a.id NOT IN ( 
                            SELECT contact_id FROM civicrm_group_contact 
                            WHERE civicrm_group_contact.group_id = "  
                        . CRM_Utils_Type::escape($group_id, 'Integer')
                        . " AND civicrm_group_contact.status = 'Removed'))";
                } else { 
                    $ssw = CRM_Contact_BAO_SavedSearch::whereClause( $group->saved_search_id, $this->_tables, $this->_whereTables);
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
                "contact_a.id = civicrm_group_contact.contact_id AND civicrm_group_contact.group_id IN (" .
                implode(',', array_keys($value)) . ')'; 
            $this->_whereTables['civicrm_group_contact'] = $this->_tables['civicrm_group_contact'];
            return implode(' OR ', $ssWhere);
        }
        return null;
    }

    /**
     * where / qill clause for tag
     *
     * @return void
     * @access public
     */
    function tag( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        
        if ( count( $value ) > 1 ) {
            $this->_useDistinct = true;
        }

        $names = array( );
        $tagNames =& CRM_Core_PseudoConstant::tag( );
        foreach ( $value as $id => $dontCare ) {
            $names[] = $tagNames[$id];
        }
        $this->_qill[$grouping][]  = ts('Tagged as -') . ' ' . implode( ' ' . ts('or') . ' ', $names ); 

        $this->_where[$grouping][] = 'tag_id IN (' . implode( ',', array_keys( $value ) ) . ')';
        $this->_tables['civicrm_entity_tag'] = $this->_whereTables['civicrm_entity_tag'] = 1;
    } 

    /**
     * where / qill clause for sort_name
     *
     * @return void
     * @access public
     */
    function sortName( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        
        $name = trim( $value ); 

        $config =& CRM_Core_Config::singleton( );

        $sub  = array( ); 
        // if we have a comma in the string, search for the entire string 
        if ( strpos( $name, ',' ) !== false ) {
            $value = strtolower(addslashes($name));
            $value = ( $wildcard ) ? "'%$value%'" : "'$value'";
            $sub[] = " ( LOWER(contact_a.sort_name) $op $value )";
            if ( $config->includeEmailInSearch ) {
                $sub[] = " ( LOWER(civicrm_email.email) $op $value )";
                $this->_tables['civicrm_location'] = $this->_whereTables['civicrm_location'] = 1;
                $this->_tables['civicrm_email'] = $this->_whereTables['civicrm_email'] = 1; 
            }
        } else { 
            // split the string into pieces 
            $pieces =  explode( ' ', $name ); 
            foreach ( $pieces as $piece ) { 
                $value = strtolower(addslashes(trim($piece)));
                $value = ( $wildcard ) ? "'%$value%'" : "'$value'";
                $sub[] = " ( LOWER(contact_a.sort_name) $op $value )";
                if ( $config->includeEmailInSearch ) {
                    $sub[] = " ( LOWER(civicrm_email.email) $op $value )";
                }
            } 
            if ( $config->includeEmailInSearch ) {
                $this->_tables['civicrm_location'] = $this->_whereTables['civicrm_location'] = 1;
                $this->_tables['civicrm_email']    = $this->_whereTables['civicrm_email'] = 1; 
            }
        } 

        $this->_where[$grouping][] = ' ( ' . implode( '  OR ', $sub ) . ' ) '; 
        if ( $config->includeEmailInSearch ) {
            $this->_qill[$grouping][]  = ts( 'Name or Email like - "%1"', array( 1 => $name ) );
        } else {
            $this->_qill[$grouping][]  = ts( 'Name like - "%1"', array( 1 => $name ) );
        }            
    }

    /**
     * where / qill clause for sorting by character
     *
     * @return void
     * @access public
     */
    function sortByCharacter( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        $name = trim( $value );
        $cond = " LOWER(contact_a.sort_name) LIKE '" . strtolower(addslashes($name)) . "%'"; 
        $this->_where[$grouping][] = $cond;
        $this->_qill[$grouping][]  = ts( 'Restricted to Contacts starting with: "%1"', array( 1 => $name ) );
    }

    /**
     * where / qill clause for including contact ids
     *
     * @return void
     * @access public
     */
    function includeContactIDs( ) {
        if ( ! $this->_includeContactIds || empty( $this->_params ) ) {
            return;
        }

        $contactIds = array( ); 
        foreach ( $this->_params as $id => $values ) { 
            if ( substr( $values[0], 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) { 
                $contactIds[] = substr( $values[0], CRM_Core_Form::CB_PREFIX_LEN ); 
            } 
        } 
        if ( ! empty( $contactIds ) ) { 
            $this->_where[0][] = " ( contact_a.id IN (" . implode( ',', $contactIds ) . " ) ) "; 
        }
    }

    /**
     * where / qill clause for postal code
     *
     * @return void
     * @access public
     */
    function postalCode( &$values ) {

        // skip if the fields dont have anything to do with postal_code
        if ( ! CRM_Utils_Array::value( 'postal_code', $this->_fields ) ) {
            return;
        }

        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        
        $this->_tables['civicrm_location'] = $this->_tables['civicrm_address' ] = 1;
        $this->_whereTables['civicrm_location'] = $this->_whereTables['civicrm_address' ] = 1;

        if ( $name == 'postal_code' ) {
            $this->_where[$grouping][] = 'civicrm_address.postal_code $op "' .
                CRM_Utils_Type::escape( $value, 'String' ) .
                '"'; 
            $this->_qill[$grouping][] = ts('Postal code - "%1"', array( 1 => $value ) );
        } else if ( $name =='postal_code_from') { 
            $this->_where[$grouping][] = ' civicrm_address.postal_code >= "' .
                CRM_Utils_Type::escape( $value, 'String' ) . 
                '" ) ';
            $this->_qill[$grouping][] = ts('Postal code greater than "%1"', array( 1 => $value ) );
        } else if ( $name == 'postal_code_to' ) {
            $this->_where[$grouping][] = ' ( civicrm_address.postal_code <= "' .
                CRM_Utils_Type::escape( $value, 'String' ) .
                '" ) ';
            $this->_qill[$grouping][] = ts('Postal code less than "%1"', array( 1 => $value ) );
        }
    }

    /**
     * where / qill clause for location type
     *
     * @return void
     * @access public
     */
    function locationType( &$values, $status = null ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        
        if (is_array($value)) {
            $this->_where[$grouping][] = 'civicrm_location.location_type_id IN (' .
                implode( ',', array_keys( $value ) ) .
                ')';
            $this->_tables['civicrm_location'] = 1;
            $this->_whereTables['civicrm_location'] = 1;
            
            $locationType =& CRM_Core_PseudoConstant::locationType();
            $names = array( );
            foreach ( array_keys( $value ) as $id ) {
                $names[] = $locationType[$id];
            }
            
            $this->_primaryLocation = false;
            
            if (!$status) {
                $this->_qill[$grouping][] = ts('Location type -') . ' ' . implode( ' ' . ts('or') . ' ', $names );
            } else {
                return implode( ' ' . ts('or') . ' ', $names );
            }
        }
    }

    /**
     * where / qill clause for location Name
     *
     * @return void
     * @access public
     */
    function locationName( &$values, $status = null ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values; 

        // do the same for location name
        $name = strtolower(addslashes($value));
        $name = ( $wildcard ) ? "'%$name%'" : "'$name'";
        $this->_where[$grouping][] = "civicrm_location.name $op $name";
        $this->_tables['civicrm_location'] = 1;
        $this->_whereTables['civicrm_location'] = 1;
        $this->_qill[$grouping][] = ts( "Location name $op '%1'", array( 1 => $value ) );
    }

    /**
     * where / qill clause for activity types
     *
     * @return void
     * @access public
     */
    function activityType( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        $name = trim( $value );

        // split the string into pieces 
        $pieces =  explode( ' ', $name ); 
        $sub    = array( );
        foreach ( $pieces as $piece ) {
            $v = strtolower(addslashes(trim($piece)));
            $v = $wildcard ? "%$v%" : $v;
            $sub[] = " LOWER(civicrm_activity_history.activity_type) $op '$v'";
        } 
        $this->_where[$grouping][] = ' ( ' . implode( '  OR ', $sub ) . ' ) ';
        $this->_tables['civicrm_activity_history'] = $this->_whereTables['civicrm_activity_history'] = 1; 
        $this->_qill[$grouping][]  = ts( "Activity Type %2 - '%1'", array( 2 => $op, 1 => $name ) );
    }
    

    function activityDate( &$values ) {
        $this->_useDistinct = true;
        $this->dateQueryBuilder( $values,
                                 'civicrm_activity_history', 'activity_date', 'activity_date', 'Activity Date' );
    }

    function privacy( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        $this->_where[$grouping][] = "contact_a.{$name} $op $value";

        $field = CRM_Utils_Array::value( $name, $this->_fields );
        if ( $field ) {
            $title = $field['title'];
        } else {
            $title = $name;
        }
        $this->_qill[$grouping][]  = ts( "%1 %2 %3", array( 1 => $title, 2 => $op, 3 => $value ) );
    }

    /**
     * where / qill clause for relationship
     *
     * @return void
     * @access public
     */
    function relationship( &$values ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        // also get values array for relation_target_name
        $targetName = $this->getWhereValues( 'relation_target_name', $grouping );
        if ( ! $targetName ) {
            return;
        }

        $name = trim( $targetName[2] );
        $name = strtolower( addslashes( $name ) );
        $name = $targetName[4] ? "%$name%" : $name;

        $rel = explode( '_' , $value );

        self::$_relType = $rel[1];
        if ( $rel[1] == 'a') {
            $this->_where[$grouping][] = "LOWER( contact_b.sort_name ) {$targetName[1]} '$name'";
            $this->_where[$grouping][] = 'civicrm_relationship.relationship_type_id = '.$rel[0];
        } else if ( $rel[1] == 'b')  {
            $this->_where[$grouping][] = "LOWER( contact_b.sort_name ) {$targetName[1]} '$name'";
            $this->_where[$grouping][] = 'civicrm_relationship.relationship_type_id = '.$rel[0];
        }
        $this->_tables['civicrm_relationship'] = $this->_whereTables['civicrm_relationship'] = 1; 

        require_once 'CRM/Contact/BAO/Relationship.php';
        $relTypeInd =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Individual');
        $relTypeOrg =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Organization');
        $relTypeHou =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Household');
        $allRelationshipType =array();
        $allRelationshipType = array_merge(  $relTypeInd , $relTypeOrg);
        $allRelationshipType = array_merge( $allRelationshipType, $relTypeHou);
        $this->_qill[$grouping][]  = ts( $allRelationshipType[$value] ." ". $name );
    }

    /**
     * default set of return properties
     *
     * @return void
     * @access public
     */
    static function &defaultReturnProperties( $mode = 1 ) {
        if ( ! isset( self::$_defaultReturnProperties ) ) {
            self::$_defaultReturnProperties = CRM_Core_Component::defaultReturnProperties( $mode );

            if ( empty( self::$_defaultReturnProperties ) ) {
                self::$_defaultReturnProperties = array( 
                                                        'home_URL'               => 1, 
                                                        'image_URL'              => 1, 
                                                        'legal_identifier'       => 1, 
                                                        'external_identifier'    => 1,
                                                        'contact_type'           => 1,
                                                        'contact_sub_type'       => 1,
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
                                                        'geo_code_1'             => 1,
                                                        'geo_code_2'             => 1,
                                                        'email'                  => 1, 
                                                        'phone'                  => 1, 
                                                        'im'                     => 1, 
                                                        ); 
            }
        }
        return self::$_defaultReturnProperties;
    }

    /**
     * get primary condition for a sql clause
     *
     * @param int $value
     *
     * @return void
     * @access public
     */
    static function getPrimaryCondition( $value ) {
        if ( is_numeric( $value ) ) {
            $value = (int ) $value;
            return ( $value == 1 ) ?'is_primary = 1' : 'is_primary = 0';
        }
        return null;
    }

    /**
     * wrapper for a simple search query
     *
     * @param array $params
     * @param array $returnProperties
     * @param bolean $count
     *
     * @return void 
     * @access public 
     */
    static function getQuery( $params = null, $returnProperties = null, $count = false ) {
        $query =& new CRM_Contact_BAO_Query( $params, $returnProperties );
        list( $select, $from, $where ) = $query->query( );
        return "$select $from $where";
    }

    /**
     * wrapper for a api search query
     *
     * @param array  $params
     * @param array  $returnProperties
     * @param string $sort
     * @param int    $offset
     * @param int    $row_count
     *
     * @return void 
     * @access public 
     */
    static function apiQuery( $params = null, $returnProperties = null, $options = null ,$sort = null, $offset = 0, $row_count = 25 ) {
        $query = new CRM_Contact_BAO_Query( $params, $returnProperties, null );
        list( $select, $from, $where ) = $query->query( );
        $options = $query->_options;
        $sql = "$select $from $where";
        if ( ! empty( $sort ) ) {
            $sql .= " ORDER BY $sort ";
        }
        if ( $row_count > 0 && $offset >= 0 ) {
            $sql .= " LIMIT $offset, $row_count ";
        }

        $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );

        $values = array( );
        while ( $dao->fetch( ) ) {
            $values[$dao->contact_id] = $query->store( $dao );
        }
        
        return array($values, $options);
    }



    /**
     * create and query the db for an contact search
     *
     * @param int      $offset   the offset for the query
     * @param int      $rowCount the number of rows to return
     * @param string   $sort     the order by string
     * @param boolean  $count    is this a count only query ?
     * @param boolean  $includeContactIds should we include contact ids?
     * @param boolean  $sortByChar if true returns the distinct array of first characters for search results
     * @param boolean  $groupContacts if true, use a single mysql group_concat statement to get the contact ids
     * @param boolean  $returnQuery   should we return the query as a string
     * @param string   $additionalWhereClause if the caller wants to further restrict the search (used in contributions)
     *
     * @return CRM_Contact_DAO_Contact 
     * @access public
     */
    function searchQuery( $offset = 0, $rowCount = 0, $sort = null, 
                          $count = false, $includeContactIds = false,
                          $sortByChar = false, $groupContacts = false,
                          $returnQuery = false,
                          $additionalWhereClause = null ) {
        require_once 'CRM/Core/Permission.php';

        if ( $includeContactIds ) {
            $this->_includeContactIds = true;
            $this->includeContactIds( );
        }

        // hack for now, add permission only if we are in search
        $permission = ' ( 1 ) ';
        if ( $this->_search ) {
            $permission = CRM_Core_Permission::whereClause( CRM_Core_Permission::VIEW, $this->_tables, $this->_whereTables );
            
            // regenerate fromClause since permission might have added tables
            if ( $permission ) {
                $this->_fromClause  = self::fromClause( $this->_tables, null, null, $this->_primaryLocation, $this->_mode ); 
                $this->_simpleFromClause = self::fromClause( $this->_whereTables, null, null, $this->_primaryLocation, $this->_mode );
            }
        }

        list( $select, $from, $where ) = $this->query( $count, $sortByChar, $groupContacts );
        
        if ( empty( $where ) ) {
            $where = 'WHERE ' . $permission;
        } else {
            $where = $where . ' AND ' . $permission;
        }

        if ( $additionalWhereClause ) {
            $where = $where . ' AND ' . $additionalWhereClause;
        }
        
        $order = $limit = '';

        if ( ! $count ) {
            $config =& CRM_Core_Config::singleton( );
            if ( $config->includeOrderByClause ) {
                if ($sort) {
                    $orderBy = trim( $sort->orderBy() );
                    if ( ! empty( $orderBy ) ) {
                        $order = " ORDER BY $orderBy";
                    }
                } else if ($sortByChar) { 
                    $order = " ORDER BY LEFT(contact_a.sort_name, 1) ";
                }
            }

            if ( $rowCount > 0 && $offset >= 0 ) {
                $limit = " LIMIT $offset, $rowCount ";
            }
        }

        // building the query string
        $query = "$select $from $where $order $limit";
        //CRM_Core_Error::debug( 'q', $query );

        if ( $returnQuery ) {
            return $query;
        }
        
        if ( $count ) {
            return CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
        }

        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        if ( $groupContacts ) {
            $ids = array( );
            while ( $dao->fetch( ) ) {
                $ids[] = $dao->id;
            }
            return implode( ',', $ids );
        }

        return $dao;
    }

    function &summaryContribution( ) {
        list( $select, $from, $where ) = $this->query( true );

        // hack $select
        $select = "
SELECT COUNT( civicrm_contribution.total_amount ) as total_count,
       SUM(   civicrm_contribution.total_amount ) as total_amount,
       AVG(   civicrm_contribution.total_amount ) as total_avg";

        $additionalWhere = "civicrm_contribution.cancel_date IS NULL";
        if ( ! empty( $where ) ) {
            $newWhere = "$where AND $additionalWhere";
        } else {
            $newWhere = " AND $additionalWhere";
        }

        $summary = array( );
        $summary['total'] = array( );
        $summary['total']['count'] = $summary['total']['amount'] = $summary['total']['avg'] = "n/a";

        $query  = "$select $from $newWhere";
        $params = array( );

        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        if ( $dao->fetch( ) ) {
            $summary['total']['count']  = $dao->total_count;
            $summary['total']['amount'] = $dao->total_amount;
            $summary['total']['avg']    = $dao->total_avg;
        }
        
        // hack $select
        $select = "
SELECT COUNT( civicrm_contribution.total_amount ) as cancel_count,
       SUM(   civicrm_contribution.total_amount ) as cancel_amount,
       AVG(   civicrm_contribution.total_amount ) as cancel_avg";

        $additionalWhere = "civicrm_contribution.cancel_date IS NOT NULL";
        if ( ! empty( $where ) ) {
            $newWhere = "$where AND $additionalWhere";
        } else {
            $newWhere = " AND $additionalWhere";
        }

        $summary['cancel'] = array( );
        $summary['cancel']['count'] = $summary['cancel']['amount'] = $summary['cancel']['avg'] = "n/a";

        $query = "$select $from $newWhere";
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        if ( $dao->fetch( ) ) {
            $summary['cancel']['count']  = $dao->cancel_count;
            $summary['cancel']['amount'] = $dao->cancel_amount;
            $summary['cancel']['avg']    = $dao->cancel_avg;
        }

        return $summary;
    }

    /**
     * getter for the qill object
     *
     * @return string
     * @access public
     */
    function qill( ) {
        return $this->_qill;
    }


    /**
     * default set of return default hier return properties
     *
     * @return void
     * @access public
     */
    static function &defaultHierReturnProperties( ) {
        if ( ! isset( self::$_defaultHierReturnProperties ) ) {
            self::$_defaultHierReturnProperties = array(
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
                                                        'individual_prefix'      => 1, 
                                                        'individual_suffix'      => 1,
                                                        'birth_date'             => 1,
                                                        'gender'                 => 1,
                                                        'preferred_communication_method' => 1,
                                                        'do_not_phone'                   => 1, 
                                                        'do_not_email'                   => 1, 
                                                        'do_not_mail'                    => 1, 
                                                        'do_not_trade'                   => 1, 
                                                        'location'                       => 
                                                        array( '1' => array ( 'location_type'      => 1,
                                                                              'street_address'     => 1,
                                                                              'city'               => 1,
                                                                              'state_province'     => 1,
                                                                              'postal_code'        => 1, 
                                                                              'postal_code_suffix' => 1, 
                                                                              'country'            => 1,
                                                                              'phone-Phone'        => 1,
                                                                              'phone-Mobile'       => 1,
                                                                              'phone-Fax'          => 1,
                                                                              'phone-1'            => 1,
                                                                              'phone-2'            => 1,
                                                                              'phone-3'            => 1,
                                                                              'im-1'               => 1,
                                                                              'im-2'               => 1,
                                                                              'im-3'               => 1,
                                                                              'email-1'            => 1,
                                                                              'email-2'            => 1,
                                                                              'email-3'            => 1,
                                                                              ),
                                                               '2' => array ( 
                                                                             'location_type'      => 1,
                                                                             'street_address'     => 1, 
                                                                             'city'               => 1, 
                                                                             'state_province'     => 1, 
                                                                             'postal_code'        => 1, 
                                                                             'postal_code_suffix' => 1, 
                                                                             'country'            => 1, 
                                                                             'phone-Phone'        => 1,
                                                                             'phone-Mobile'       => 1,
                                                                             'phone-1'            => 1,
                                                                             'phone-2'            => 1,
                                                                             'phone-3'            => 1,
                                                                             'im-1'               => 1,
                                                                             'im-2'               => 1,
                                                                             'im-3'               => 1,
                                                                             'email-1'            => 1,
                                                                             'email-2'            => 1,
                                                                             'email-3'            => 1,
                                                                             ) 
                                                               ),
                                                        );
            
        }
        return self::$_defaultHierReturnProperties;
    }

    function dateQueryBuilder( &$values,
                               $tableName, $fieldName, $dbFieldName, $fieldTitle ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        if ( $name == $fieldName . '_low' ) {
            $op     = '>=';
            $phrase = 'greater than';
        } else if ( $name == $fieldName . '_high' ) {
            $op     = '<=';
            $phrase = 'less than';
        } else if ( $name == $fieldName ) {
            $op     = '=';
            $phrase = '=';
        } else {
            return;
        }

        if ( $value['M'] ) {
            $revDate = array_reverse( $value );
            $date    = CRM_Utils_Date::format( $revDate );
            $format  = CRM_Utils_Date::customFormat( CRM_Utils_Date::format( $revDate, '-' ) );
            if ( $date ) {
                $this->_where[$grouping][] = $tableName . '.' . $dbFieldName . " $op '$date'";
                $this->_tables[$tableName] = $this->_whereTables[$tableName] = 1;
                $this->_qill[$grouping][]  = ts( '%2 - %3 "%1"', array( 1 => $format, 2 => $fieldTitle, 3 => $phrase ) );
            }
        }
    }

    function numberRangeBuilder( &$values,
                                 $tableName, $fieldName, $dbFieldName, $fieldTitle ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        if ( $name == $fieldName . '_low' ) {
            $op     = '>=';
            $phrase = 'greater than';
        } else if ( $name == $fieldName . '_high' ) {
            $op     = '<=';
            $phrase = 'less than';
        } else if ( $name == $fieldName ) {
            $op     = '=';
            $phrase = '=';
        } else {
            return;
        }

        $this->_where[$grouping][] = "{$tableName}.{$dbFieldName} $op {$value}";
        $this->_tables[$tableName] = $this->_whereTables[$tableName] = 1;
        $this->_qill[$grouping][]  = ts( '%2 - %3 "%1"', array( 1 => $value, 2 => $fieldTitle, 3 => $phrase ) );
    }

}
