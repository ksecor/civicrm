<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Core_BAO_CustomValueTable 
{
    function create( &$customParams ) 
    {
        if ( empty( $customParams ) ||
             ! is_array( $customParams ) ) {
            return;
        }

        foreach ( $customParams as $tableName => $fields ) {
            $sqlOP     = null;
            $hookID    = null;
            $hookOP    = null;
            $entityID  = null;
            $set       = array( );
            $params    = array( );
            $count     = 1;
            foreach ( $fields as $field ) {
                if ( ! $sqlOP ) {
                    $entityID = $field['entity_id'];
                    $hookID   = $field['custom_group_id'];
                    if ( array_key_exists( 'id', $field ) ) {
                        $sqlOP = "UPDATE $tableName ";
                        $where = " WHERE  id = %{$count}";
                        $params[$count] = array( $field['id'], 'Integer' );
                        $count++;
                        $hookOP = 'edit';
                    } else {
                        $sqlOP = "INSERT INTO $tableName ";
                        $where = null;
                        $hookOP    = 'create';
                    }
                }

                // fix the value before we store it
                $value = $field['value'];
                $type  = $field['type'];
                switch( $type ) {

                case 'StateProvince':
                    $type = 'Integer';
                    if ( is_array( $value ) ) {
                        $value = implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value );    
                        $type  = 'String';
                    } else if ( ! is_numeric( $value ) ) {
                        //fix for multi select state, CRM-3437
                        $mulValues = explode( ',', $value );
                        $validStates = array( );
                        foreach ( $mulValues as $key => $stateVal ) {
                            $states = array( );
                            $states['state_province'] = trim($stateVal);
                            
                            CRM_Utils_Array::lookupValue( $states, 'state_province', 
                                                          CRM_Core_PseudoConstant::stateProvince(), true );
                            if ( !$states['state_province_id'] ) {
                                CRM_Utils_Array::lookupValue( $states, 'state_province',
                                                              CRM_Core_PseudoConstant::stateProvinceAbbreviation(), true );
                            }
                            $validStates[] = $states['state_province_id'];
                        }
                        $value = implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $validStates );
                        $type = 'String'; 
                    } else if ( ! $value ) {
                        // CRM-3415
                        // using type of timestamp allows us to sneak in a null into db
                        // gross but effective hack
                        $value = null;
                        $type  = 'Timestamp';
                    }
                    break;
                    
                case 'Country':
                    $type = 'Integer';
                    if ( is_array( $value ) ) {
                        $value = implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value );    
                        $type  = 'String';
                    } else if ( ! is_numeric( $value ) ) {
                        //fix for multi select country, CRM-3437
                        $mulValues = explode( ',', $value );
                        $validCountries = array( );
                        foreach ( $mulValues as $key => $countryVal ) {
                            $countries = array( );
                            $countries['country'] = trim($countryVal);
                            CRM_Utils_Array::lookupValue( $countries, 'country', 
                                                          CRM_Core_PseudoConstant::country(), true );
                            if ( ! $countries['country_id'] ) {
                                CRM_Utils_Array::lookupValue( $countries, 'country',
                                                              CRM_Core_PseudoConstant::countryIsoCode(), true );
                            }
                            $validCountries[] = $countries['country_id'];
                        }
                        $value = implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $validCountries ); 
                        $type = 'String';
                    } else if ( ! $value ) {
                        // CRM-3415
                        // using type of timestamp allows us to sneak in a null into db
                        // gross but effective hack
                        $value = null;
                        $type  = 'Timestamp';
                    }
                    break;

                case 'File':
                    if ( ! $field['file_id'] ) {
                        CRM_Core_Error::fatal( );
                    }

                    // need to add/update civicrm_entity_file
                    require_once 'CRM/Core/DAO/EntityFile.php'; 
                    $entityFileDAO =& new CRM_Core_DAO_EntityFile();
                    $entityFileDAO->file_id = $field['file_id'];
                    $entityFileDAO->find( true );

                    $entityFileDAO->entity_table = $field['table_name'];
                    $entityFileDAO->entity_id    = $field['entity_id'];
                    $entityFileDAO->file_id      = $field['file_id'];
                    $entityFileDAO->save( );
                    $entityFileDAO->free( );
                    $value = $field['file_id'];
                    $type  = 'String';
                    break;
                    
                case 'Date':
                    $value = CRM_Utils_Date::isoToMysql($value);
                    break;

                case 'RichTextEditor':
                    $type  = 'String';
                    break;
                    
                default:
                    break;

                }
                $set[$field['column_name']] = "%{$count}";
                $params[$count] = array( $value, $type );
                $count++;
            }

            if ( ! empty( $set ) ) {
                $setClause = array( );
                foreach ( $set as $n => $v ) {
                    $setClause[] = "$n = $v";
                }
                $setClause = implode( ',', $setClause );
                if ( ! $where ) {
                    // do this only for insert
                    $set['entity_id'] = "%{$count}";
                    $params[$count] = array( $entityID, 'Integer' );
                    $count++;

                    $fieldNames  = implode( ',', array_keys  ( $set ) );
                    $fieldValues = implode( ',', array_values( $set ) );
                    $query = "$sqlOP ( $fieldNames ) VALUES ( $fieldValues ) ON DUPLICATE KEY UPDATE $setClause";
                } else {
                    $query = "$sqlOP SET $setClause $where";
                }
                $dao = CRM_Core_DAO::executeQuery( $query, $params );
                $dao->free( );

                require_once 'CRM/Utils/Hook.php';
                CRM_Utils_Hook::custom( $hookOP,
                                        $hookID,
                                        $entityID,
                                        $fields );
            }
        }
    }

    /**
     * given a field return the mysql data type associated with it
     *
     * @param string $type the civicrm type string
     *
     * @return the mysql data store placeholder
     * @access public
     * @static
     */
    public static function fieldToSQLType( $type,
                                           $maxLength = 255 ) 
    {
        switch ($type) {
        case 'String':
        case 'Link':
            return "varchar($maxLength)";
        case 'Boolean':
            return 'tinyint';
        case 'Int':
            return 'int';
        // the below three are FK's, and have constraints added to them
        case 'StateProvince':
        case 'Country':
        case 'File':
            return 'int unsigned';
        case 'Float':
            return 'double';
        case 'Money':
            return 'decimal(20,2)';
        case 'Memo':
        case 'RichTextEditor':
            return 'text';
        case 'Date':
            return 'datetime';
        default:
            CRM_Core_Error::fatal( );
        }
    }
    
    function store( &$params, $entityTable, $entityID ) 
    {
        $cvParams = array( );
        foreach ($params as $customValue) {
            $cvParam = array(
                             'entity_table'    => $entityTable,
                             'entity_id'       => $entityID,
                             'value'           => $customValue['value'],
                             'type'            => $customValue['type'],
                             'custom_field_id' => $customValue['custom_field_id'],
                             'custom_group_id' => $customValue['custom_group_id'],
                             'table_name'      => $customValue['table_name'],
                             'column_name'     => $customValue['column_name'],
                             'file_id'         => $customValue['file_id'],
                             );
            
            // fix Date type to be timestamp, since that is how we store in db
            if ( $cvParam['type'] == 'Date' ) {
                $cvParam['type'] = 'Timestamp';
            }

            if ($customValue['id']) {
                $cvParam['id'] = $customValue['id'];
            }
            if ( ! array_key_exists( $customValue['table_name'], $cvParams ) ) {
                $cvParams[$customValue['table_name']] = array( );
            }

            $cvParams[$customValue['table_name']][] = $cvParam;
        }

        if ( ! empty( $cvParams ) ) {
            self::create($cvParams);
        }
    }

    function postProcess( &$params, &$customFields, $entityTable, $entityID, $customFieldExtends ) 
    {
        $customData = array( );
        require_once "CRM/Core/BAO/CustomField.php";
        foreach ( $params as $key => $value ) {
            if ( $customFieldID = CRM_Core_BAO_CustomField::getKeyID( $key ) ) {
                CRM_Core_BAO_CustomField::formatCustomField( $customFieldID,
                                                             $customData,
                                                             $value,
                                                             $customFieldExtends,
                                                             null,
                                                             $entityID );
            }
        }

        if ( ! empty( $customFields ) ) {
            foreach ( $customFields as $k => $val ) {
                if ( ( ! CRM_Utils_Array::value( $k, $customData ) ) &&
                     in_array ( CRM_Utils_Array::value( 3, $val ),
                                array ('CheckBox','Multi-Select') ) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $k,
                                                                 $customData,
                                                                 '',
                                                                 $customFieldExtends,
                                                                 null,
                                                                 $entityID );
                }
            }
        }

        if ( ! empty( $customData ) ) {
            self::store( $customData, $entityTable, $entityID );
        }
    }

    /**
     * Return an array of all custom values associated with an entity.
     *
     * @param int         $entityID      Identification number of the entity
     * @param string      $entityType    Type of entity that the entityID corresponds to, specified
     *                                   as a string with format "'<EntityName>'". Comma separated
     *                                   list may be used to specify OR matches. Allowable values
     *                                   are enumerated types in civicrm_custom_group.extends field.
     *                                   Optional. Default value assumes entityID references a 
     *                                   contact entity. 
     * @param array       $fieldIDs      optional list of fieldIDs that we want to retrieve. If this
     *                                   is set the entityType is ignored
     *                                   
     * @return array      $fields        Array of custom values for the entity with key=>value 
     *                                   pairs specified as civicrm_custom_field.id => custom value.
     *                                   Empty array if no custom values found.
     * @access public
     * @static
     */
    public static function &getEntityValues( $entityID, $entityType = null, $fieldIDs = null ) {
        if ( ! $entityID ) {
            // adding this here since an empty contact id could have serious repurcussions
            // like looping forever
            CRM_Core_Error::fatal( 'Please file an issue with the backtrace' );
            return null;
        }

        $cond = array( );
        if ( $entityType ) {
            $cond[] = "cg.extends IN ( '$entityType' )";
        }
        if ( $fieldIDs &&
             is_array( $fieldIDs ) ) {
            $fieldIDList = implode( ',', $fieldIDs );
            $cond[] = "cf.id IN ( $fieldIDList )";
        }
        if ( empty( $cond ) ) {
            $cond[] = "cg.extends IN ( 'Contact', 'Individual', 'Household', 'Organization' )";
        }
        $cond = implode( ' AND ', $cond );

        // first find all the fields that extend this type of entity
        $query = "
SELECT cg.table_name,
       cg.id as groupID,
       cf.column_name,
       cf.id as fieldID
FROM   civicrm_custom_group cg,
       civicrm_custom_field cf
WHERE  cf.custom_group_id = cg.id
AND    cg.is_active = 1
AND    cf.is_active = 1
AND    $cond
";
        $dao = CRM_Core_DAO::executeQuery( $query );

        $select = array( );
        $where  = array( );
        $tables = array( );
        $seen   = array( );
        $fields = array( );
        while ( $dao->fetch( ) ) {
            if ( ! array_key_exists( $dao->groupID, $seen ) ) {
                $where[]               = "{$dao->table_name}.entity_id = $entityID";
                $tables[]              = $dao->table_name;
                $seen[$dao->groupID]   = 1;
            }
            $fields[]                = $dao->fieldID;
            $select[] = "{$dao->table_name}.{$dao->column_name} as custom_{$dao->fieldID}";
        }

        $result = array( );
        if ( ! empty( $tables ) ) {
            $select = implode( ', ', $select );
            $from   = implode( ', ', $tables );
            $where  = implode( ' AND ', $where  );
            $query = "
SELECT $select
FROM   $from
WHERE  $where
";
            $dao = CRM_Core_DAO::executeQuery( $query );
            if ( $dao->fetch( ) ) {
                foreach ( $fields as $fieldID ) {
                    $fieldName = "custom_{$fieldID}";
                    $result[$fieldID] = $dao->$fieldName;
                }
            }
        }
        return $result;
    }

     /**
      * Function to take in an array of entityID, custom_XXX => value
      * and set the value in the appropriate table. Should also be able
      * to set the value to null. Follows api parameter/return conventions
      *
      * @array $params
      *
      * @return array 
      * @static
      */
     static function setValues( &$params ) {

         if ( ! isset( $params['entityID'] ) ||
              CRM_Utils_Type::escape( $params['entityID'],
                                      'Integer', false ) === null ) {
             return CRM_Core_Error::createAPIError( ts( 'entityID needs to be set and of type Integer' ) );
         }

         // first collect all the id/value pairs. The format is:
         // custom_X => value
         $values = array( );
         $fieldValues = array( );
         foreach ( $params as $n => $v ) {
             if ( substr( $n, 0, 7 ) == 'custom_' ) {
                 $idx = substr( $n, 7 );
                 if ( CRM_Utils_Type::escape( $idx,
                                              'Integer', false ) === null ) {
                     return CRM_Core_Error::createAPIError( ts( 'field ID needs to be of type Integer for index %1',
                                                                array( 1 => $idx ) ) );
                 }
                 $fieldValues[(int ) $idx] = $v;
             }
         }

         $fieldIDList = implode( ',', array_keys( $fieldValues ) );

         // format it so that we can just use create
         $sql = "
SELECT cg.table_name  as table_name ,
       cg.id          as cg_id      ,
       cf.column_name as column_name,
       cf.id          as cf_id      ,
       cf.data_type   as data_type 
FROM   civicrm_custom_group cg,
       civicrm_custom_field cf
WHERE  cf.custom_group_id = cg.id
AND    cf.id IN ( $fieldIDList )
";

         $dao       = CRM_Core_DAO::executeQuery( $sql );
         $cvParams  = array( );
         
         if ( $dao->fetch( ) ) {
             // ensure that value is of the right data type
             $dataType = $dao->data_type == 'Date' ? 'Timestamp' : $dao->data_type;
             if ( CRM_Utils_Type::escape( $fieldValues[$dao->cf_id],
                                          $dataType, false ) === null ) {
                 return CRM_Core_Error::createAPIError( ts( 'value: %1 is not of the right field data type: %2',
                                                            array( 1 => $fieldValues[$dao->cf_id],
                                                                   2 => $dao->data_type ) ) );
             }

             $cvParam = array(
                              'entity_id'       => $params['entityID'],
                              'value'           => $fieldValues[$dao->cf_id],
                              'type'            => $dataType,
                              'custom_field_id' => $dao->cf_id,
                              'custom_group_id' => $dao->cg_id,
                              'table_name'      => $dao->table_name,
                              'column_name'     => $dao->column_name,
                              );                              

            if ( ! array_key_exists( $dao->table_name, $cvParams ) ) {
                $cvParams[$dao->table_name] = array( );
            }

            $cvParams[$dao->table_name][] = $cvParam;
         }

         if ( ! empty( $cvParams ) ) {
             self::create( $cvParams );
             return CRM_Core_Error::createAPISuccess( );
         }

         return CRM_Core_Error::createAPIError( ts( 'Unknown error' ) );
     }

     /**
      * Function to take in an array of entityID, custom_ID
      * and gets the value from the appropriate table.
      *
      * @array $params
      *
      * @return array 
      * @static
      */
     static function &getValues( &$params ) {
         if ( ! isset( $params['entityID'] ) ||
              CRM_Utils_Type::escape( $params['entityID'],
                                      'Integer', false ) === null ) {
             return CRM_Core_Error::createAPIError( ts( 'entityID needs to be set and of type Integer' ) );
         }

         // first collect all the ids. The format is:
         // custom_ID
         $fieldsIDs = array( );
         foreach ( $params as $n => $v ) {
             $key = $idx = null;
             if ( substr( $n, 0, 7 ) == 'custom_' ) {
                 $idx = substr( $n, 7 );
                 if ( CRM_Utils_Type::escape( $idx,
                                              'Integer', false ) === null ) {
                     return CRM_Core_Error::createAPIError( ts( 'field ID needs to be of type Integer for index %1',
                                                                array( 1 => $idx ) ) );
                 }
                 $fieldIDs[] = (int ) $idx;
             }
         }

         $values = self::getEntityValues( $params['entityID'],
                                          null,
                                          $fieldIDs );
         if ( empty( $values ) ) {
             return CRM_Core_Error::createAPIError( ts( 'No values found for the specified entity ID and custom field(s).' ) );
         } else {
             $result = array( 'is_error' => 0,
                              'entityID' => $params['entityID'] );
             foreach ( $values as $id => $value ) {
                 $result["custom_{$id}"] = $value;
             }
             return $result;
         }
     }

}