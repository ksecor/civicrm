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
                    if ( is_array( $value ) ) {
                        $value = implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value );    
                        $type  = 'String';
                    } else {
                        if ( ! is_numeric( $value ) ) {
                            $states = array( );
                            $states['state_province'] = $value;
                            require_once 'CRM/Contact/BAO/Contact.php';
                            CRM_Contact_BAO_Contact::lookupValue( $states, 'state_province', 
                                                                  CRM_Core_PseudoConstant::stateProvince(), true );
                            if ( !$states['state_province_id'] ) {
                                CRM_Contact_BAO_Contact::lookupValue( $states, 'state_province',
                                                                      CRM_Core_PseudoConstant::stateProvinceAbbreviation(), true );
                            }
                            $value = $states['state_province_id'];
                        }
                        $type = 'Integer';
                    }
                    break;
                    
                case 'Country':
                    
                    if ( is_array( $value ) ) {
                        $value = implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value );    
                        $type  = 'String';
                    } else {
                        if ( !is_numeric( $value ) ) {
                            $countries = array( );
                            $countries['country'] = $value;
                            
                            CRM_Contact_BAO_Contact::lookupValue( $countries, 'country', 
                                                                  CRM_Core_PseudoConstant::country(), true );
                            if ( ! $countries['country_id'] ) {
                                CRM_Contact_BAO_Contact::lookupValue( $countries, 'country',
                                                                      CRM_Core_PseudoConstant::countryIsoCode(), true );
                            }
                            $value = $countries['country_id'];
                        }
                        $type = 'Integer';
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
                $set[] = "`{$field['column_name']}` = %{$count}";
                $params[$count] = array( $value, $type );
                $count++;
            }

            if ( ! empty( $set ) ) {
                $set[] = "entity_id = %{$count}";
                $params[$count] = array( $entityID, 'Integer' );
                $count++;
                $set   = implode( ", ", $set );
                $query = "$sqlOP SET $set $where";
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
    public static function fieldToSQLType($type) 
    {
        switch ($type) {
        case 'String':
        case 'Link':
            return 'varchar(255)';
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
                if ( in_array ( $val[3], array ('CheckBox','Multi-Select') )&&
                     ! CRM_Utils_Array::value( $k, $customData ) ) {
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
     *                                   
     * @return array      $fields        Array of custom values for the entity with key=>value 
     *                                   pairs specified as civicrm_custom_field.id => custom value.
     *                                   Empty array if no custom values found.
     * @access public
     * @static
     */
     public static function &getEntityValues( $entityID, $entityType = null ) {
        if ( ! $entityID ) {
            // adding this here since an empty contact id could have serious repurcussions
            // like looping forever
            CRM_Core_Error::fatal( 'Please file an issue with the backtrace' );
            return null;
        }

        if ( ! $entityType ) {
            $entityType = "'Contact', 'Individual', 'Household', 'Organization'";
        }

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
AND    cg.extends IN ( $entityType )
";
        $dao = CRM_Core_DAO::executeQuery( $query,
                                           CRM_Core_DAO::$_nullArray );

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
            $dao = CRM_Core_DAO::executeQuery( $query,
                                               CRM_Core_DAO::$_nullArray );
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
      * Function to take in an array of entityID, fieldID, value
      * and set the value in the appropriate table. Should also be able
      * to set the value to null. Follows api parameter/return conventions
      * Sometime real soon we should extend the function to take in an
      * entityID, array of (fieldID, value) pairs and set multiple values
      *
      * @array $params
      *
      * @return array 
      * @static
      */
     static function setValue( &$params ) {

         if ( ! isset( $params['entityID'] ) ||
              CRM_Utils_Type::escape( $params['entityID'],
                                      'Integer', false ) === null ) {
             return CRM_Core_Error::createAPIError( ts( 'entityID needs to be set and of type Integer' ) );
         }

         if ( ! isset( $params['value'] ) ) {
             return CRM_Core_Error::createAPIError( ts( 'value needs to be set' ) );
         }

         if ( ! isset( $params['fieldID'] ) ||
              CRM_Utils_Type::escape( $params['fieldID'],
                                      'Integer', false ) === null ) {
             return CRM_Core_Error::createAPIError( ts( 'fieldID needs to be set and of type Integer' ) );
         }


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
AND    cf.id = %1
";
         $sqlParams = array( 1 => array( $params['fieldID'], 'Integer' ) );
         $dao       = CRM_Core_DAO::executeQuery( $sql, $sqlParams );
         $cvParams  = array( );
         
         if ( $dao->fetch( ) ) {
             // ensure that value is of the right data type
             if ( CRM_Utils_Type::escape( $params['value'], $dao->data_type, false ) === null ) {
                 return CRM_Core_Error::createAPIError( ts( 'value: %1 is not of the right field data type: %2',
                                                            array( 1 => $params['value'],
                                                                   2 => $dao->data_type ) ) );
             }

             $cvParam = array(
                              'entity_id'       => $params['entityID'],
                              'value'           => $params['value'],
                              'type'            => $dao->data_type == 'Date' ? 'Timestamp' : $dao->data_type,
                              'custom_field_id' => $dao->cf_id,
                              'custom_group_id' => $dao->cg_id,
                              'table_name'      => $dao->table_name,
                              'column_name'     => $dao->column_name,
                              );                              

            if ( ! array_key_exists( $dao->table_name, $cvParams ) ) {
                $cvParams[$dao->table_name] = array( );
            }

            $cvParams[$daa->table_name][] = $cvParam;
         }

         if ( ! empty( $cvParams ) ) {
             self::create( $cvParams );
             return CRM_Core_Error::createAPISuccess( );
         }

         return CRM_Core_Error::createAPIError( ts( 'Unknown error' ) );
     }

}


