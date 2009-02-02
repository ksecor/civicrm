<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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

require_once 'CRM/Core/DAO/CustomGroup.php';

/**
 * Business object for managing custom data groups
 *
 */
class CRM_Core_BAO_CustomGroup extends CRM_Core_DAO_CustomGroup 
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }

  
   /**
     * takes an associative array and creates a custom group object
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Core_DAO_CustomGroup object
     * @access public
     * @static
     */
    static function create( &$params )
    {
        $fieldLength =  CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'name');
              
        // create custom group dao, populate fields and then save.           
        $group =& new CRM_Core_DAO_CustomGroup();
        $group->title            = $params['title'];
        $group->name             = CRM_Utils_String::titleToVar($params['title'], $fieldLength['maxlength'] );
        if ( in_array( $params['extends'][0],
                       array( 'ParticipantRole',
                              'ParticipantEventName',
                              'ParticipantEventType' ) ) ) {
            $group->extends          = 'Participant';
        } else {
            $group->extends          = $params['extends'][0];
        }

        $group->extends_entity_column_id = null;
        if ( ($params['extends'][0] == 'Relationship') && !empty($params['extends'][1])) {
            $group->extends_entity_column_value = str_replace( array('_a_b', '_b_a'), array('', ''), $params['extends'][1]);
        } elseif ( empty($params['extends'][1]) ) {
            $group->extends_entity_column_value = null;
        } else {
            $group->extends_entity_column_value = $params['extends'][1];
            if ( $params['extends'][0] == 'ParticipantRole' ||
                 $params['extends'][0] == 'ParticipantEventName' ||
                 $params['extends'][0] == 'ParticipantEventType' ) {
                $group->extends_entity_column_id  = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $params['extends'][0], 'value', 'name' );
            } 
        }

        $group->style            = CRM_Utils_Array::value('style', $params, false);
        $group->collapse_display = CRM_Utils_Array::value('collapse_display', $params, false);


        if ( isset( $params['id'] ) ) {
            $oldWeight = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup', $params['id'], 'weight', 'id' );
        } else {
            $oldWeight = 0;
        }
        require_once 'CRM/Utils/Weight.php';
        $group->weight =
            CRM_Utils_Weight::updateOtherWeights( 'CRM_Core_DAO_CustomGroup', $oldWeight, CRM_Utils_Array::value('weight', $params, false) );

        $group->help_pre         = CRM_Utils_Array::value('help_pre', $params, false);
        $group->help_post        = CRM_Utils_Array::value('help_post', $params, false);
        $group->is_active        = CRM_Utils_Array::value('is_active', $params, false);

        $group->is_multiple      = CRM_Utils_Array::value('is_multiple'    , $params, false );
        $group->min_multiple     = CRM_Utils_Array::value('min_multiple'   , $params, 0 );
        $group->max_multiple     = CRM_Utils_Array::value('max_multiple'   , $params, 0 );

        $tableName = null;
        if ( isset( $params['id'] ) ) {
            $group->id = $params['id'] ;
            //check whether custom group was changed from single-valued to multiple-valued
            if ($params['is_multiple'] && $params['is_multiple'] != CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup', $params['id'], 'is_multiple' ) ) {
                $oldTableName = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup', $params['id'], 'table_name' );
            }
        } else {
            // lets create the table associated with the group and save it
            $tableName = $group->table_name = "civicrm_value_" .
                strtolower( CRM_Utils_String::munge( $group->title, '_', 32 ) );
        }

        // enclose the below in a transaction
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        $group->save();
        if ( $tableName ) {
            // now append group id to table name, this prevent any name conflicts
            // like CRM-2742
            $tableName .= "_{$group->id}";
            $group->table_name = $tableName;
            CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_CustomGroup',
                                         $group->id,
                                         'table_name',
                                         $tableName );

            // now create the table associated with this group
            self::createTable( $group );
        } elseif ( $oldTableName ) {
            require_once 'CRM/Core/BAO/SchemaHandler.php';
            CRM_Core_BAO_SchemaHandler::changeUniqueToIndex( $oldTableName );
        }
        $transaction->commit( );

        // reset the cache
        require_once 'CRM/Core/BAO/Cache.php';
        CRM_Core_BAO_Cache::deleteGroup( 'contact fields' );
    
        return $group;
    }
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Core_DAO_CustomGroup object
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults)
    {
        return CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_CustomGroup', $params, $defaults );
    }

    /**
     * update the is_active flag in the db
     *
     * @param  int      $id         id of the database record
     * @param  boolean  $is_active  value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     * @access public
     */
    static function setIsActive($id, $is_active) {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_CustomGroup', $id, 'is_active', $is_active );
    }

    /**
     * Get custom groups/fields for type of entity.
     *
     * An array containing all custom groups and their custom fields is returned.
     *
     * @param string $entityType - of the contact whose contact type is needed
     * @param int    $entityId   - optional - id of entity if we need to populate the tree with custom values. 
     * @param int    $groupId    - optional group id (if we need it for a single group only)
     *                           - if groupId is 0 it gets for inline groups only
     *                           - if groupId is -1 we get for all groups
     *
     * @return array $groupTree  - array consisting of all groups and fields and optionally populated with custom data values.
     *
     * @access public
     *
     * @static
     *
     */
    public static function &getTree( $entityType,
                                     &$form,
                                     $entityID = null,
                                     $groupID  = null,
                                     $subType  = null,
                                     $subName  = null )
    {
        // create a new tree
        $groupTree = array();
        $strSelect = $strFrom = $strWhere = $orderBy = ''; 
        $tableData = array();

        // using tableData to build the queryString 
        $tableData =
            array(
                  'civicrm_custom_field' =>
                  array('id',
                        'label',
                        'column_name',
                        'data_type',
                        'html_type',
                        'default_value',
                        'attributes',
                        'is_required',
                        'is_view',
                        'help_post',
                        'options_per_line',
                        'start_date_years',
                        'end_date_years',
                        'date_parts',
                        'option_group_id' ),
                  'civicrm_custom_group' =>
                  array('id',
                        'name',
                        'table_name',
                        'title',
                        'help_pre',
                        'help_post',
                        'collapse_display',
                        'is_multiple',
 						'extends',
						'extends_entity_column_id',
						'extends_entity_column_value',
						'max_multiple'),
                  );

       // create select
        $select = array( );
        foreach ($tableData as $tableName => $tableColumn) {
            foreach ($tableColumn as $columnName) {
                $alias = $tableName . "_" . $columnName;
                $select[] = "{$tableName}.{$columnName} as {$tableName}_{$columnName}";
            }
        }
        $strSelect = "SELECT " . implode( ', ', $select );

        // from, where, order by
        $strFrom = "
FROM     civicrm_custom_group
LEFT JOIN civicrm_custom_field ON (civicrm_custom_field.custom_group_id = civicrm_custom_group.id)
";

        // if entity is either individual, organization or household pls get custom groups for 'contact' too.
        if ($entityType == "Individual" || $entityType == 'Organization' || $entityType == 'Household') {
            $in = "'$entityType', 'Contact'";
        } else {
            $in = "'$entityType'";
        }

        if ( $subType ) {
            $strWhere = "
WHERE civicrm_custom_group.is_active = 1 
  AND civicrm_custom_field.is_active = 1 
  AND civicrm_custom_group.extends IN ($in)
  AND ( civicrm_custom_group.extends_entity_column_value = '$subType'
   OR   civicrm_custom_group.extends_entity_column_value IS NULL )
";
            if ( $subName ) {
                $strWhere .= " AND civicrm_custom_group.extends_entity_column_id = {$subName} ";
            }
        } else {
            $strWhere = "
WHERE civicrm_custom_group.is_active = 1 
  AND civicrm_custom_field.is_active = 1 
  AND civicrm_custom_group.extends IN ($in)
  AND civicrm_custom_group.extends_entity_column_value IS NULL
";
        }
 
        $params = array( );
        if ( $groupID > 0 ) {
            // since we want a specific group id we add it to the where clause
            $strWhere .= " AND civicrm_custom_group.id = %1";
            $params[1] = array( $groupID, 'Integer' );
        } else if ( ! $groupID ){
            // since groupID is false we need to show all Inline groups
            $strWhere .= " AND civicrm_custom_group.style = 'Inline'";
        }

        require_once 'CRM/Core/Permission.php';
        // ensure that the user has access to these custom groups
        $strWhere .= 
            " AND " .
            CRM_Core_Permission::customGroupClause( CRM_Core_Permission::VIEW,
                                                    'civicrm_custom_group.' );
        
        $orderBy = "
ORDER BY civicrm_custom_group.weight,
         civicrm_custom_group.title,
         civicrm_custom_field.weight,
         civicrm_custom_field.label
";

        // final query string
        $queryString = "$strSelect $strFrom $strWhere $orderBy";
        //crm_core_error::debug('$queryString', $queryString );
        // dummy dao needed
        $crmDAO =& CRM_Core_DAO::executeQuery( $queryString, $params );
        
        $customValueTables = array( );

        // process records
        while( $crmDAO->fetch( ) ) {
            // get the id's 
            $groupID = $crmDAO->civicrm_custom_group_id;
            $fieldId = $crmDAO->civicrm_custom_field_id;

            // create an array for groups if it does not exist
            if ( ! array_key_exists( $groupID, $groupTree ) ) {
                $groupTree[$groupID]       = array();
                $groupTree[$groupID]['id'] = $groupID;
                
                // populate the group information
                foreach ( $tableData['civicrm_custom_group'] as $fieldName ) {
                    $fullFieldName = "civicrm_custom_group_$fieldName";
                    if ( $fieldName == 'id' ||
                         is_null( $crmDAO->$fullFieldName ) ) {
                        continue;
                    }
                    $groupTree[$groupID][$fieldName] = $crmDAO->$fullFieldName;
                }
                $groupTree[$groupID]['fields'] = array();
                
                $customValueTables[$crmDAO->civicrm_custom_group_table_name] = array( );
            }

            // add the fields now (note - the query row will always contain a field)
            // we only reset this once, since multiple values come is as multiple rows
            if ( ! array_key_exists( $fieldId, $groupTree[$groupID]['fields'] ) ) {
                $groupTree[$groupID]['fields'][$fieldId] = array();
            }
			
            $customValueTables[$crmDAO->civicrm_custom_group_table_name][$crmDAO->civicrm_custom_field_column_name] = 1;
            $groupTree[$groupID]['fields'][$fieldId]['id'] = $fieldId;
            // populate information for a custom field
            foreach ($tableData['civicrm_custom_field'] as $fieldName) {
                $fullFieldName = "civicrm_custom_field_$fieldName";
                if ( $fieldName == 'id' ||
                     is_null( $crmDAO->$fullFieldName ) ) {
                    continue;
                } 
                $groupTree[$groupID]['fields'][$fieldId][$fieldName] = $crmDAO->$fullFieldName;                    
            }
        }

        // now that we have all the groups and fields, lets get the values
        // since we need to know the table and field names
        if ( $entityID ) {
            $entityID = CRM_Utils_Type::escape( $entityID, 'Integer' );
        }

        // add info to groupTree
        if ( ! empty( $customValueTables ) ) {
            $groupTree['info'] = array( 'tables' => $customValueTables );
            $select = $from = $where = array( );
            foreach ( $groupTree['info']['tables'] as $table => $fields ) {
                $from[]   = $table;
                $select[] = "{$table}.id as {$table}_id";
                $select[] = "{$table}.entity_id as {$table}_entity_id";

				foreach ( $fields as $column => $dontCare ) {
					$select[] = "{$table}.{$column} as {$table}_{$column}";
				}

                if ( $entityID ) {
                    $where[]  = "{$table}.entity_id = $entityID";
                }
            }

            $groupTree['info']['select'] = $select;
            $groupTree['info']['from'  ] = $from  ;
            $groupTree['info']['where' ] = null   ;

            if ( $entityID ) {
                $groupTree['info']['where' ] = $where ;
                $select = implode( ', '   , $select );

                // this is a hack to find a table that has some values for this
                // entityID to make the below LEFT JOIN work (CRM-2518)
                $firstTable = null;
                foreach ( $from as $table ) {
                    $query = "
SELECT id
FROM   $table
WHERE  entity_id = $entityID
";
                    $recordExists = CRM_Core_DAO::singleValueQuery( $query );
                    if ( $recordExists ) {
                        $firstTable = $table;
                        break;
                    }
                }

                if ( $firstTable ) {
                    $fromSQL    = $firstTable;
                    foreach ( $from as $table ) {
                        if ( $table != $firstTable ) {
                            $fromSQL .= "\nLEFT JOIN $table USING (entity_id)";
                        }
                    }

                    $query = "
SELECT $select
  FROM $fromSQL
 WHERE {$firstTable}.entity_id = $entityID
";

                    $dao = CRM_Core_DAO::executeQuery( $query );
                
                    while ( $dao->fetch( ) ) {
                        foreach ( $groupTree as $groupID => $group ) {
                            if ( $groupID === 'info' ) {
                                continue;
                            }
                            $table = $groupTree[$groupID]['table_name'];
                            foreach ( $group['fields'] as $fieldID => $dontCare ) {
                                $column    = $groupTree[$groupID]['fields'][$fieldID]['column_name'];
                                $idName    = "{$table}_id";
                                $fieldName = "{$table}_{$column}";
                                
                                $dataType  = $groupTree[$groupID]['fields'][$fieldID]['data_type'];
                                if ( $dataType == 'File' ) {
                                    if ( isset( $dao->$fieldName ) ) { 
                                        require_once 'CRM/Core/DAO/File.php';
                                        $config =& CRM_Core_Config::singleton( );
                                        $fileDAO =& new CRM_Core_DAO_File();
                                        $fileDAO->id = $dao->$fieldName;
                                        
                                        if ( $fileDAO->find(true) ) {
                                            $entityIDName = "{$table}_entity_id";
                                            $customValue['data']    = $fileDAO->uri;
                                            $customValue['fid']     = $fileDAO->id;
                                            $customValue['fileURL'] = 
                                                CRM_Utils_System::url( 'civicrm/file', "reset=1&id={$fileDAO->id}&eid={$dao->$entityIDName}" );
                                            $customValue['displayURL'] = null;
                                            $deleteExtra = ts('Are you sure you want to delete attached file.');
                                            $deleteURL =
                                                array( CRM_Core_Action::DELETE  =>
                                                       array(
                                                             'name'  => ts('Delete Attached File'),
                                                             'url'   => 'civicrm/file',
                                                             'qs'    => 'reset=1&id=%%id%%&eid=%%eid%%&fid=%%fid%%&action=delete',
                                                             'extra' => 
                                                             'onclick = "if (confirm( \''. $deleteExtra .'\' ) ) this.href+=\'&amp;confirmed=1\'; else return false;"'
                                                             ) 
                                                       );
                                            $customValue['deleteURL'] = 
                                                CRM_Core_Action::formLink( $deleteURL,
                                                                           CRM_Core_Action::DELETE,
                                                                           array( 'id'  => $fileDAO->id,
                                                                                  'eid' => $dao->$entityIDName,
                                                                                  'fid' => $fieldID ) );
                                            $customValue['fileName'] = 
                                                CRM_Utils_File::cleanFileName( basename( $fileDAO->uri ) );
                                            if ( $fileDAO->mime_type =="image/jpeg"  ||
                                                 $fileDAO->mime_type =="image/pjpeg" ||
                                                 $fileDAO->mime_type =="image/gif"   ||
                                                 $fileDAO->mime_type =="image/x-png" ||
                                                 $fileDAO->mime_type =="image/png" ) {
                                                $customValue['displayURL'] = $customValue['fileURL'];
                                            }
                                        }
                                    } else {
                                        $customValue = array( 'id'   => $dao->$idName,
                                                              'data' => '' );
                                    }
                                } else {
                                    $customValue = array( 'id'   => $dao->$idName,
                                                          'data' => $dao->$fieldName );
                                }
                                if ( ! array_key_exists( 'customValue', $groupTree[$groupID]['fields'][$fieldID] ) ) {
                                    $groupTree[$groupID]['fields'][$fieldID]['customValue'] = array( );
                                }
                                if (  empty( $groupTree[$groupID]['fields'][$fieldID]['customValue'] ) ) {
                                    $groupTree[$groupID]['fields'][$fieldID]['customValue'] = array( 1 => $customValue );
                                } else {
                                    $groupTree[$groupID]['fields'][$fieldID]['customValue'][] = $customValue;
                                }                                     
                            }
                        }
                    }
                }
            }
        }

        return $groupTree;
    }
   
    /**
     * Get the group title.
     *
     * @param int $id id of group.
     * @return string title 
     *
     * @access public
     * @static
     *
     */
    public static function getTitle( $id )
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup', $id, 'title' );
    }

    /**
     * Get custom group details for a group.
     *
     * An array containing custom group details (including their custom field) is returned.
     *
     * @param int     $groupId    - group id whose details are needed
     * @param boolean $searchable - is this field searchable
     * @param array   $extends    - which table does it extend if any 
     * @return array $groupTree - array consisting of all group and field details
     *
     * @access public
     *
     * @static
     *
     */
    public static function &getGroupDetail($groupId = null, $searchable = null, &$extends = null)
    {
        // create a new tree
        $groupTree = array();
        $select = $from = $where = $orderBy = ''; 

        $tableData = array();

        // using tableData to build the queryString 
        $tableData =
            array(
                  'civicrm_custom_field' =>
                  array('id',
                        'label',
                        'data_type',
                        'html_type',
                        'default_value',
                        'attributes',
                        'is_required',
                        'help_post',
                        'options_per_line',
                        'is_searchable',
                        'start_date_years',
                        'end_date_years',
                        'is_search_range',
                        'date_parts',
                        'note_columns',
                        'note_rows',
                        'column_name',
                        'is_view' ),
                  'civicrm_custom_group' =>
                  array('id',
                        'name',
                        'title',
                        'help_pre',
                        'help_post',
                        'collapse_display',
                        'extends',
                        'extends_entity_column_value',
                        'table_name' ),
                  );

        // create select
        $select = "SELECT"; 
        $s = array( );
        foreach ($tableData as $tableName => $tableColumn) {
            foreach ($tableColumn as $columnName) {
                $s[] = "{$tableName}.{$columnName} as {$tableName}_{$columnName}";
            }
        }
        $select = 'SELECT ' . implode( ', ', $s );
        $params     = array( );
        // from, where, order by
        $from = " FROM civicrm_custom_field, civicrm_custom_group";
        $where = " WHERE civicrm_custom_field.custom_group_id = civicrm_custom_group.id
                            AND civicrm_custom_group.is_active = 1
                            AND civicrm_custom_field.is_active = 1 ";
        if ( $groupId ) {
            $params[1] = array( $groupId, 'Integer' );
            $where .= " AND civicrm_custom_group.id = %1";
        }

        if ( $searchable ) {
            $where .= " AND civicrm_custom_field.is_searchable = 1";
        }

        if ( $extends ) {
            $clause = array( );
            foreach ( $extends as $e ) {
                $clause[] = "civicrm_custom_group.extends = '$e'";
            }
            $where .= " AND ( " . implode( ' OR ', $clause ) . " ) ";

            //include case activitiescustomdata if case is enabled
            if ( in_array('Activity', $extends) ) {
                $extendValues = implode( ',', array_keys(CRM_Core_PseudoConstant::activityType( true, true )) );
                $where .= " AND ( civicrm_custom_group.extends_entity_column_value IN ($extendValues) OR civicrm_custom_group.extends_entity_column_value IS NULL ) ";
            } 
        }

        $orderBy = " ORDER BY civicrm_custom_group.weight, civicrm_custom_field.weight";

        // final query string
        $queryString = $select . $from . $where . $orderBy;
             
        // dummy dao needed
        $crmDAO =& CRM_Core_DAO::executeQuery( $queryString, $params );
        
        // process records
        while($crmDAO->fetch()) {
            $groupId = $crmDAO->civicrm_custom_group_id;
            $fieldId = $crmDAO->civicrm_custom_field_id;

            // create an array for groups if it does not exist
            if (!array_key_exists($groupId, $groupTree)) {
                $groupTree[$groupId] = array();
                $groupTree[$groupId]['id'] = $groupId;
                
                foreach ($tableData['civicrm_custom_group'] as $v) {
                    $fullField = "civicrm_custom_group_" . $v;
                    
                    if ($v == 'id' || is_null($crmDAO->$fullField)) {
                        continue;
                    }
                    
                    $groupTree[$groupId][$v] = $crmDAO->$fullField;                    
                }
                
                $groupTree[$groupId]['fields'] = array();
                
            }
            
            // add the fields now (note - the query row will always contain a field)
            $groupTree[$groupId]['fields'][$fieldId] = array();
            $groupTree[$groupId]['fields'][$fieldId]['id'] = $fieldId;

            foreach ($tableData['civicrm_custom_field'] as $v) {
                $fullField = "civicrm_custom_field_" . $v;
                if ($v == 'id' || is_null($crmDAO->$fullField)) {
                    continue;
                }
                $groupTree[$groupId]['fields'][$fieldId][$v] = $crmDAO->$fullField;                    
            }
        }

        return $groupTree;
    }


    public static function &getActiveGroups( $entityType, $path, $cidToken = '%%cid%%' ) 
    {
        // for Group's
        $customGroupDAO =& new CRM_Core_DAO_CustomGroup();
       
        // get only 'Tab' groups
        $customGroupDAO->whereAdd("style = 'Tab'");
        $customGroupDAO->whereAdd("is_active = 1");

        // add whereAdd for entity type
        self::_addWhereAdd($customGroupDAO, $entityType);

        $groups = array( );

        $permissionClause = CRM_Core_Permission::customGroupClause( null, null, true );
        $customGroupDAO->whereAdd( $permissionClause );
        
        // order by weight
        $customGroupDAO->orderBy('weight');
        $customGroupDAO->find();
        
        // process each group with menu tab
        while ($customGroupDAO->fetch( ) ) { 
            $group = array();
            $group['id']      = $customGroupDAO->id;
            $group['path']    = $path;
            $group['title']   = "$customGroupDAO->title";
            $group['query']   = "reset=1&gid={$customGroupDAO->id}&cid={$cidToken}";
            $group['extra' ]  = array( 'gid' => $customGroupDAO->id );
            $groups[] = $group;
        }
       
        return $groups;
    }

    /**
     * Get the table name for the entity type
     * currently if entity type is 'Contact', 'Individual', 'Household', 'Organization'
     * tableName is 'civicrm_contact'
     * 
     * @param string $entityType  what entity are we extending here ?
     *
     * @return string $tableName
     *
     * @access private
     * @static
     *
     */
    private static function _getTableName($entityType)
    {
        $tableName = '';
        switch($entityType) {
        case 'Contact':
        case 'Individual':
        case 'Household':
        case 'Organization':
            $tableName = 'civicrm_contact';
            break;
        case 'Contribution':
            $tableName = 'civicrm_contribution';
            break;
        case 'Group':
            $tableName = 'civicrm_group';
            break;
        // DRAFTING: Verify if we cannot make it pluggable
        case 'Activity':  
            $tableName = 'civicrm_activity';
            break;
        case 'Relationship':  
            $tableName = 'civicrm_relationship';
            break;
        case 'Membership':
            $tableName = 'civicrm_membership';
            break;
        case 'Participant':
            $tableName = 'civicrm_participant';
            break;
        case 'Event':
            $tableName = 'civicrm_event';
            break;
        case 'Grant':
            $tableName = 'civicrm_grant';
            break;  
            // need to add cases for Location, Address
        }
       
        return $tableName;
    }
    
    /**
     * Add the whereAdd clause for the DAO depending on the type of entity
     * the custom group is extending.
     *
     * @param object CRM_Core_DAO_CustomGroup (reference) - Custom Group DAO.
     * @param string $entityType    - what entity are we extending here ?
     *
     * @return void
     *
     * @access private
     * @static
     *
     */
    private static function _addWhereAdd(&$customGroupDAO, $entityType)
    {
        switch($entityType) {
        case 'Contact':
            // if contact, get all related to contact
            $customGroupDAO->whereAdd("extends IN ('Contact', 'Individual', 'Household', 'Organization')");
            break;
        case 'Individual':
        case 'Household':
        case 'Organization':
            // is I/H/O then get I/H/O and contact
            $customGroupDAO->whereAdd("extends IN ('Contact', '$entityType')");
            break;
        case 'Location':
        case 'Address':
            $customGroupDAO->whereAdd("extends IN ('$entityType')");
            break;
        }
    }


    /**
     * Delete the Custom Group.
     *
     * @param $group object   the DAO custom group object
     * @param $force boolean  whether to force the deletion, even if there are custom fields
     * 
     * @return boolean   false if field exists for this group, true if group gets deleted.
     *
     * @access public
     * @static
     *
     */
    public static function deleteGroup( $group, $force = false )
    { 
        require_once 'CRM/Core/BAO/CustomField.php';

        //check wheter this contain any custom fields
        $customField = & new CRM_Core_DAO_CustomField();
        $customField->custom_group_id = $group->id;
        $customField->find();

        // return early if there are custom fields and we're not 
        // forcing the delete, otherwise delete the fields one by one
        while ($customField->fetch()) {
            if (!$force) return false;
            CRM_Core_BAO_CustomField::deleteField($customField);
        }

        // drop the table associated with this custom group
        require_once 'CRM/Core/BAO/SchemaHandler.php';
        CRM_Core_BAO_SchemaHandler::dropTable( $group->table_name );

        //delete  custom group
        $group->delete();
        return true;
    }

    static function setDefaults( &$groupTree, &$defaults, $viewMode = false, $inactiveNeeded = false ) 
    {
        require_once 'CRM/Core/BAO/CustomOption.php';
        foreach ( $groupTree as $id => $group ) {
            $groupId = CRM_Utils_Array::value('id', $group);
            foreach ($group['fields'] as $field) {
                if ( CRM_Utils_Array::value( 'element_value', $field ) !== null ) {
                    $value = $field['element_value'];
                } else if ( CRM_Utils_Array::value( 'default_value', $field ) !== null ) {
                    $value = $viewMode ? null : $field['default_value'];
                } else {
                    continue;
                }

                $fieldId     = $field['id'];
                $elementName = $field['element_name'];
                switch($field['html_type']) {

                case 'CheckBox':
                    $defaults[$elementName] = array( );
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id'], $inactiveNeeded);
                    if ($viewMode) {
                        $checkedData = explode(CRM_Core_DAO::VALUE_SEPARATOR, substr($value,1,-1));
                        if ( isset($value) ) {
                            foreach( $customOption as $val ) {
                                if ( in_array( $val['value'], $checkedData ) ) {
                                    $defaults[$elementName][$val['value']] = 1;
                                } else {
                                    $defaults[$elementName][$val['value']] = 0;
                                }
                            }
                        }
                    } else {
                        if ( isset( $field['customValue']['data'] ) ) {
                            $checkedData = explode(CRM_Core_DAO::VALUE_SEPARATOR,substr($field['customValue']['data'],1,-1));
                            foreach( $customOption as $val ) {
                                if ( in_array( $val['value'], $checkedData ) ) {
                                    $defaults[$elementName][$val['value']] = 1;
                                } else {
                                    $defaults[$elementName][$val['value']] = 0;
                                }
                            }
                        } else {
                            $checkedValue = explode(CRM_Core_DAO::VALUE_SEPARATOR, substr($value,1,-1));
                            foreach($customOption as $val) {
                                if ( in_array($val['value'], $checkedValue) ) {
                                    $defaults[$elementName][$val['value']] = 1;
                                } else {
                                    $defaults[$elementName][$val['value']] = 0;
                                }
                            }                            
                        }
                    }
                    break;
                    
                //added a case for Multi-Select option  
                case 'Multi-Select':
                    if ($viewMode) {
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id'], $inactiveNeeded);
                        $checkedData = explode(CRM_Core_DAO::VALUE_SEPARATOR, substr($value,1,-1));
                        $defaults[$elementName] = array();
                        if(isset($value)) {
                            foreach($customOption as $val) {
                                if (in_array($val['value'], $checkedData)) {
                                    $defaults[$elementName][$val['value']] = $val['value'];
                                }
                            }
                        }
                    } else {
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id'], $inactiveNeeded);
                        $defaults[$elementName] = array();
                        if (isset($field['customValue']['data'])) {
                            $checkedData = explode(CRM_Core_DAO::VALUE_SEPARATOR, substr($field['customValue']['data'],1,-1));
                            foreach($customOption as $val) {
                                if (in_array($val['value'], $checkedData)) {
                                    //$defaults[$elementName][$val['value']] = 1;
                                    $defaults[$elementName][$val['value']] = $val['value'];
                                } 
                            }
                        } else {
                            $checkedValue = explode(CRM_Core_DAO::VALUE_SEPARATOR, substr($value,1,-1));
                            foreach($customOption as $val) {
                                if ( in_array($val['value'], $checkedValue) ) {
                                    $defaults[$elementName][$val['value']] = $val['value'];
                                }
                            }                            
                        }
                    }
                    break;
                    
                case 'Select Date':
                    if (isset($value)) {
                        $defaults[$elementName] = CRM_Utils_Date::unformat( $value, '-' );
                    }
                   
                    break;
                case 'Multi-Select Country':
                case 'Multi-Select State/Province':
                     if (isset($value)) {
                         $checkedValue = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$value );
                         foreach($checkedValue as $val) {
                             if ( $val ) {
                                 $defaults[$elementName][$val]  =  $val;
                             }	
                         } 
                     }
                     break;
                case 'Select Country':
                    if ( $value ) {
                        $defaults[$elementName] = $value;
                    } else {
                        $config          =& CRM_Core_Config::singleton( );
                        $defaults[$elementName] = $config->defaultContactCountry;
                    }
                    break;

                default:
                    if ($field['data_type'] == "Float") {
                        $defaults[$elementName] = (float)$value;
                    } elseif ($field['data_type'] == 'Money') {
                        require_once 'CRM/Utils/Money.php';
                        $defaults[$elementName] = CRM_Utils_Money::format($value, null, '%a');
                    } else { 
                        $defaults[$elementName] = $value;
                    }
                } 
            }
        }
    }

    static function postProcess( &$groupTree, &$params, $skipFile = false ) 
    {
        // Get the Custom form values and groupTree        
        // first reset all checkbox and radio data
        foreach ($groupTree as $groupID => $group) {
            if ( $groupID === 'info' ) {
                continue;
            }              
            foreach ($group['fields'] as $field) {
                $groupId = $group['id'];
                $fieldId = $field['id'];

                //added Multi-Select option in the below if-statement
                if ( $field['html_type'] == 'CheckBox' || $field['html_type'] == 'Radio' || $field['html_type'] == 'Multi-Select' ) {
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = 'NULL';
                }

                $v = CRM_Utils_Array::value( 'custom_' . $field['id'], $params );

                if ( ! isset($groupTree[$groupId]['fields'][$fieldId]['customValue'] ) ) {
                    // field exists in db so populate value from "form".
                    $groupTree[$groupId]['fields'][$fieldId]['customValue'] = array();
                }

                switch ( $groupTree[$groupId]['fields'][$fieldId]['html_type'] ) {

                //added for CheckBox
                case 'CheckBox':  
                    if ( ! empty( $v ) ) {
                        $customValue = array_keys( $v );
                        $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = 
                            CRM_Core_DAO::VALUE_SEPARATOR .
                            implode(CRM_Core_DAO::VALUE_SEPARATOR, $customValue) .
                            CRM_Core_DAO::VALUE_SEPARATOR;
                    } else {
                        $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = null;
                    }
                    break;

                //added for Multi-Select
                case 'Multi-Select':  
                    if ( ! empty( $v ) ) {
                        $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = 
                            CRM_Core_DAO::VALUE_SEPARATOR .
                            implode(CRM_Core_DAO::VALUE_SEPARATOR, $v) .
                            CRM_Core_DAO::VALUE_SEPARATOR;
                    } else {
                        $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = null;
                    }
                    break;

                case 'Select Date':
                    $date = CRM_Utils_Date::format( $v );
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $date;
                    break;
         
                case 'File':
                    if ( $skipFile ) {
                        continue;
                    }

                    //store the file in d/b
                    $entityId   = explode( '=', $groupTree['info']['where'][0] );
                    $fileParams = array( 'upload_date'=> date('Ymdhis') );
                    
                    if ( $groupTree[$groupId]['fields'][$fieldId]['customValue']['fid'] ) {
                        $fileParams['id'] = $groupTree[$groupId]['fields'][$fieldId]['customValue']['fid'];
                    }     
                    if ( ! empty( $v ) ) {
                        require_once 'CRM/Core/BAO/File.php';
                        $fileParams['uri'] = $v['name'];
                        $fileParams['mime_type'] = $v['type'];
                        CRM_Core_BAO_File::filePostProcess($v['name'], 
                                                           $groupTree[$groupId]['fields'][$fieldId]['customValue']['fid'], 
                                                           $groupTree[$groupId]['table_name'],
                                                           trim( $entityId[1] ),
                                                           false,
                                                           true,
                                                           $fileParams,
                                                           'custom_' . $fieldId,
                                                           $v['type']
                                                           );
                    }
                    $defaults   = array( );
                    $paramsFile =  array( 'entity_table' => $groupTree[$groupId]['table_name'],
                                          'entity_id'    => $entityId[1] );
                    
                    CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_EntityFile',
                                                 $paramsFile,
                                                 $defaults);
                    
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $defaults['file_id'];
                    break;
                    
                default:
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $v;
                    break;
                }
            }
        }
    }

    /**
     * generic function to build all the form elements for a specific group tree
     *
     * @param CRM_Core_Form $form      the form object
     * @param array         $groupTree the group tree object
     * @param string        $showName  
     * @param string        $hideName
     *
     * @return void
     * @access public
     * @static
     */
    static function buildQuickForm( &$form,
                                    &$groupTree,
                                    $inactiveNeeded = false,
                                    $groupCount = 1 ) 
    {
        require_once 'CRM/Core/BAO/CustomField.php';
        require_once 'CRM/Core/BAO/CustomOption.php';

        //this is fix for calendar for date field  
        foreach ( $groupTree as $id => $group ) { 
            foreach ( $group['fields'] as $key2 => $field ) {
                if ( $field['data_type'] == 'Date' && $field['date_parts'] ) {
                    $datePart = explode( CRM_Core_DAO::VALUE_SEPARATOR , $field['date_parts']);
                    $datePart = array_flip( $datePart);
                    
                    if (( !array_key_exists( 'M', $datePart))||
                        ( !array_key_exists( 'd', $datePart))||
                        ( !array_key_exists( 'Y', $datePart))) {
                        $groupTree[$key1]['fields'][$key2]['skip_calendar'] = true;
                    }
                    if (array_key_exists( 'H', $datePart)){
                        $groupTree[$key1]['fields'][$key2]['skip_ampm'] = true; 
                    }
                }
            }
        }

        $form->assign_by_ref( 'groupTree', $groupTree );
        $sBlocks = array( );
        $hBlocks = array( );

        // this is fix for date field
        $form->assign('currentYear',date('Y'));
       
        require_once 'CRM/Core/ShowHideBlocks.php'; 
        foreach ($groupTree as $id => $group) { 

            CRM_Core_ShowHideBlocks::links( $form, $group['title'], '', ''); 

            $groupId = CRM_Utils_Array::value('id', $group);
            foreach ($group['fields'] as $field) { 
                // skip all view fields
                if ( CRM_Utils_Array::value( 'is_view', $field ) ) {
                    continue;
                }
                
                $required = $field['is_required'];
                //fix for CRM-1620
                if ( $field['data_type']  == 'File') {
                    if ( isset($field['customValue']['data']) ) {
                        $required = 0;
                    }
                }

                $fieldId     = $field['id'];                 
                $elementName = $field['element_name'];
                require_once "CRM/Core/BAO/CustomField.php";
                CRM_Core_BAO_CustomField::addQuickFormElement($form, $elementName, $fieldId, $inactiveNeeded, $required);
            } 
         } 
    }

    /**
     * Function to extract the get params from the url, validate
     * and store it in session
     *
     * @param CRM_Core_Form $form the form object
     * @param string        $type the type of custom group we are using
	 *
     * @return void
     * @access public
     * @static
     */
    static function extractGetParams( &$form, $type ) 
    {
        // if not GET params return
        if ( empty( $_GET ) ) {
            return;
        }
        
        $groupTree    =& CRM_Core_BAO_CustomGroup::getTree( $type, $form );
        $customFields =& CRM_Core_BAO_CustomField::getFields( $type );

        $customValue  = array();
        $htmlType     = array('CheckBox','Multi-Select','Select','Radio');

        foreach ($groupTree as $group) {
            if ( ! isset( $group['fields'] ) ) {
                continue;
            }
            foreach( $group['fields'] as $key => $field) {
                $fieldName = 'custom_' . $key;
                $value = CRM_Utils_Request::retrieve( $fieldName, 'String',
                                                      $form );

                if ( $value ) {
                    if ( ! in_array( $customFields[$key]['html_type'], $htmlType ) ||
                         $customFields[$key]['data_type'] =='Boolean' ) {
                        $valid = CRM_Core_BAO_CustomValue::typecheck( $customFields[$key]['data_type'], $value);
                    }
                    if ( $customFields[$key]['html_type'] == 'CheckBox' ||
                         $customFields[$key]['html_type'] =='Multi-Select' ) {
                        $value = str_replace("|",",",$value);
                        $mulValues = explode( ',' , $value );
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption( $key, true );
                        $val = array (); 
                        foreach( $mulValues as $v1 ) {
                            foreach( $customOption as $v2 ) {
                                if ( strtolower(trim($v2['label'])) == strtolower(trim($v1)) ) {
                                    $val[$v2['value']] = 1;
                                }
                            }
                        }
                        if (! empty ($val) ) {
                            $value = $val;
                            $valid = true; 
                        } else {
                            $value = null;
                        }
                    } else if ($customFields[$key]['html_type'] == 'Select' || 
                               ( $customFields[$key]['html_type'] =='Radio' &&
                                 $customFields[$key]['data_type'] !='Boolean' ) ) {
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption($key, true );
                        foreach( $customOption as $v2 ) {
                            if ( strtolower(trim($v2['label'])) == strtolower(trim($value)) ) {
                                $value = $v2['value'];
                                $valid = true; 
                            }
                        }
                    } else if ( $customFields[$key]['data_type'] == 'Date' ) {
                        require_once 'CRM/Utils/Date.php';
                        if( is_numeric( $value ) ){
                            $value = CRM_Utils_Date::unformat( $value , null );
                        } else {
                            $value = CRM_Utils_Date::unformat( $value , $separator = '-' );
                        }
                        $valid = true; 
                    }
                    if ( $valid ) {
                        $customValue[$fieldName] = $value;
                    }
                }
            }
        }

        $form->set( 'customGetValues', $customValue );
        $form->set( 'groupTree', $groupTree );
    }

    /**
     * Function to check the type of custom field type (eg: Used for Individual, Contribution, etc) 
     * this function is used to get the custom fields of a type (eg: Used for Individual, Contribution, etc )
     *
     * @param  int     $customFieldId          custom field id
     * @param  array   $removeCustomFieldTypes remove custom fields of a type eg: array("Individual") ;
     *
     *
     * @return boolean false if it matches else true      
     * @static
     * @access public
     */
    static function checkCustomField($customFieldId, &$removeCustomFieldTypes ) 
    {
        $query = "SELECT cg.extends as extends
                  FROM civicrm_custom_group as cg, civicrm_custom_field as cf
                  WHERE cg.id = cf.custom_group_id
                    AND cf.id =" . CRM_Utils_Type::escape($customFieldId, 'Integer');

        $extends = CRM_Core_DAO::singleValueQuery( $query );
        
        if ( in_array( $extends, $removeCustomFieldTypes ) ) {
            return false;
        }
        return true;
    }

    static function mapTableName( $table ) 
    {
        switch ( $table ) {
        case 'Contact':
        case 'Individual':
        case 'Household':
        case 'Organization':
            return 'civicrm_contact';

        case 'Activity':
            return 'civicrm_activity';

        case 'Group':
            return 'civicrm_group';

        case 'Contribution':
            return 'civicrm_contribution';
            
        case 'Relationship':
            return 'civicrm_relationship';
            
        case 'Event':
            return 'civicrm_event';
        
        case 'Membership':
            return 'civicrm_membership';
        
        case 'Participant':
            return 'civicrm_participant';
            
        case 'Grant':
            return 'civicrm_grant';
            
        case 'Pledge':
            return 'civicrm_pledge';    
            
        default:
            CRM_Core_Error::fatal( );
        }
    }

    static function createTable( $group ) 
    {
        $params = array(
                        'name'           => $group->table_name,
                        'is_multiple'    => $group->is_multiple ? 1 : 0,
                        'extends_name'   => self::mapTableName( $group->extends ),
                        );

        require_once 'CRM/Core/BAO/CustomField.php';
        $tableParams =& CRM_Core_BAO_CustomField::defaultCustomTableSchema( $params );

        require_once 'CRM/Core/BAO/SchemaHandler.php';
        CRM_Core_BAO_SchemaHandler::createTable( $tableParams );
    }

    /**
     * Function returns formatted groupTree, sothat form can be easily build in template
     *
     * @param array  $groupTree associated array
     * @param int    $groupCount group count by default 1, but can varry for multiple value custom data 
     * @param object form object 
	 *
     * @return array $formattedGroupTree
     */
    static function formatGroupTree( &$groupTree, $groupCount = 1, &$form ) 
    {
        $formattedGroupTree = array( );
        $uploadNames = array();

        foreach ( $groupTree as $key => $value ) {
            if ( $key === 'info' ) {
                continue;
            }

            // add group information
            $formattedGroupTree[$key]['name'     ]        = CRM_Utils_Array::value('name', $value);
            $formattedGroupTree[$key]['title'    ]        = CRM_Utils_Array::value('title', $value);
            $formattedGroupTree[$key]['help_pre' ]        = CRM_Utils_Array::value('help_pre', $value);
            $formattedGroupTree[$key]['help_post']        = CRM_Utils_Array::value('help_post', $value);
            $formattedGroupTree[$key]['collapse_display'] = CRM_Utils_Array::value('collapse_display', $value);

            // this params needed of bulding multiple values	
            $formattedGroupTree[$key]['is_multiple']                 = CRM_Utils_Array::value('is_multiple', $value);
            $formattedGroupTree[$key]['extends']                     = CRM_Utils_Array::value('extends', $value);
            $formattedGroupTree[$key]['extends_entity_column_id']    = CRM_Utils_Array::value('extends_entity_column_id', $value);
            $formattedGroupTree[$key]['extends_entity_column_value'] = CRM_Utils_Array::value('extends_entity_column_value', $value);
            $formattedGroupTree[$key]['max_multiple']                = CRM_Utils_Array::value('max_multiple', $value);

            // add field information
            foreach ( $value['fields'] as $k => $properties ) {
                $properties['element_name']  = "custom_{$k}_-{$groupCount}";
                if ( !empty( $properties['customValue'] ) ) {
                    if ( isset( $properties['customValue'][$groupCount] ) ) {
                        $properties['element_name'] = "custom_{$k}_{$properties['customValue'][$groupCount]['id']}";
                        if ( $properties['data_type'] == 'File' ) {
                            $properties['element_value'] = $properties['customValue'][$groupCount];
                            $uploadNames[]    = $properties['element_name']; 
                        } else {
                            $properties['element_value'] = $properties['customValue'][$groupCount]['data'];
                        }
                    }                    
                }
                unset( $properties['customValue'] );
                $formattedGroupTree[$key]['fields'][$k] = $properties;
            }
        }

        if ( $form ) {
            // hack for field type File
            $formUploadNames = $form->get( 'uploadNames' );
            if ( is_array( $formUploadNames ) ) {
                $uploadNames = array_unique( array_merge( $formUploadNames, $uploadNames ) );
            }

            $form->set('uploadNames', $uploadNames);
        }

        return $formattedGroupTree;
    }

    /**
     * Build custom data view
	 *  @param object  $form page object
     *  @param array   $groupTree associated array  
	 *  @param boolean $returnCount true if customValue count needs to be returned
     */
    static function buildCustomDataView ( &$form, &$groupTree, $returnCount = false, $groupID = null )
    {
        foreach ( $groupTree as $key => $group ) {
            if ( $key === 'info' ) {
                continue;
            }
			
            foreach ( $group['fields'] as $k => $properties ) {
				$groupID = $group['id'];
                if ( !empty( $properties['customValue'] ) ) {
                    foreach ( $properties['customValue'] as $values ) {
	                    $details[$groupID][$values['id']]['title']            = CRM_Utils_Array::value('title', $group);
                        $details[$groupID][$values['id']]['name']             = CRM_Utils_Array::value('name', $group); 
                        $details[$groupID][$values['id']]['help_pre']         = CRM_Utils_Array::value('help_pre', $group );  
                        $details[$groupID][$values['id']]['help_post']        = CRM_Utils_Array::value('help_post', $group); 
                        $details[$groupID][$values['id']]['collapse_display'] = CRM_Utils_Array::value('collapse_display', $group);
                        
                        $details[$groupID][$values['id']]['fields'][$k] = 
                            array( 'field_title'      => CRM_Utils_Array::value('label', $properties) ,
                                   'field_type'       => CRM_Utils_Array::value('html_type',
                                                                                $properties),
                                   'field_value'      =>
                                   self::formatCustomValues( $values,
                                                             CRM_Utils_Array::value('html_type',
                                                                                    $properties), 
                                                             CRM_Utils_Array::value('data_type',
                                                                                    $properties),
                                                             CRM_Utils_Array::value('option_group_id', 
                                                                                    $properties),
                                                             CRM_Utils_Array::value('date_parts',
                                                                                    $properties),
                                                             CRM_Utils_Array::value('options_per_line',
                                                                                    $properties)
                                                             ),
                                   'options_per_line' => CRM_Utils_Array::value('options_per_line',
                                                                                $properties) ) ;
                    }
                } else {
                    $details[$groupID][0]['title']             = CRM_Utils_Array::value('title', $group);  
                    $details[$groupID][0]['name']              = CRM_Utils_Array::value('name', $group); 
                    $details[$groupID][0]['help_pre']          = CRM_Utils_Array::value('help_pre', $group ); 
                    $details[$groupID][0]['help_post']         = CRM_Utils_Array::value('help_post', $group); 
                    $details[$groupID][0]['collapse_display']  = CRM_Utils_Array::value('collapse_display', $group);				
                    $details[$groupID][0]['fields'][$k]        = array( 'field_title' => CRM_Utils_Array::value('label', $properties) );
                }
            }	
        }

		if ( $returnCount ) {
			return count( $details[$groupID]);
		} else {
			$form->assign_by_ref( 'viewCustomData', $details );
		}
    }

    /** 
     * Format custom value according to data, view mode
     *
     */
    static function formatCustomValues( &$values, $htmlType, $dataType, $option_group_id, $dateParts, $optionPerLine = null )
    {
        $value = $values['data'];

        if ( !isset( $value ) ) {
            return; 
        }
        $freezeString = "";
        $freezeStringChecked = "";

        switch ( $dataType ) {

        case 'Date':
            $parts = explode(CRM_Core_DAO::VALUE_SEPARATOR, $dateParts );
            $retValue = CRM_Utils_Date::customFormat( $value, null, $parts );
            break;	

        case 'Boolean':
            if ( $value == '1' ) {
                $retValue = $freezeStringChecked . ts('Yes') . "\n";
            } else {
                $retValue = $freezeStringChecked . ts('No') . "\n";
            }
            break;

        case 'Link': 
            $retValue = CRM_Utils_System::formatWikiURL( $value );
            break;	  

        case 'File':
            $retValue = $values;
            break;

        case 'Memo': 
            $retValue = $value;
            break;	

        case 'Float':
            if ( $htmlType == 'Text' ) {
                $retValue = (float)$value;
                break;
            }

        case 'Money':
            if ($htmlType == 'Text') {
                require_once 'CRM/Utils/Money.php';
                $retValue = CRM_Utils_Money::format($value, null, '%a');
                break;
            }

        case 'String':
        case 'Int':
            if ( in_array($htmlType, array('Text', 'TextArea')) ) {
                $retValue = $value;
                break;
            }

        case 'StateProvince':
        case 'Country':
            //added check for Multi-Select in the below if-statement
            $customData[] = $value;

            //form custom data for multiple-valued custom data
            switch ( $htmlType ) {
            case 'Multi-Select Country':	 
            case 'Select Country':	 
                $customData = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value );
                $query = "
                    SELECT id as value, name as label  
                    FROM civicrm_country";
                $coDAO  = CRM_Core_DAO::executeQuery( $query );
                break;

            case 'Select State/Province':  
            case 'Multi-Select State/Province':
                $customData = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value );
                $query = "
                    SELECT id as value, name as label  
                    FROM civicrm_state_province";
                $coDAO  = CRM_Core_DAO::executeQuery( $query );
                break;

            case 'Select': 
                $customData = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value );
                if ( $option_group_id ) {
                    $query = "
                        SELECT label, value
                        FROM civicrm_option_value
                        WHERE option_group_id = %1
                        ORDER BY weight ASC, label ASC";
                    $params = array( 1 => array( $option_group_id, 'Integer' ) );
                    $coDAO  = CRM_Core_DAO::executeQuery( $query, $params );
                }
                break;

            case 'CheckBox': 
            case 'Multi-Select':
                $customData = explode( CRM_Core_DAO::VALUE_SEPARATOR, $value );

            default:
                if ( $option_group_id ) {
                    $query = "
                        SELECT label, value
                        FROM civicrm_option_value
                        WHERE option_group_id = %1
                        ORDER BY weight ASC, label ASC";
                    $params = array( 1 => array( $option_group_id, 'Integer' ) );
                    $coDAO  = CRM_Core_DAO::executeQuery( $query, $params );
                }
            }

            $retValue = null;
            while ( $coDAO->fetch( ) ) {
                //to show only values that are checked
                if ( in_array ( $coDAO->value, $customData ) ) {
                    $checked = in_array ( $coDAO->value, $customData ) ? $freezeStringChecked : $freezeString;
                    if ( !$optionPerLine ) {
                        if ( $retValue ) {
                            $retValue .= ",&nbsp;";
                        }
                        $retValue .= $checked . $coDAO->label;
                    } else {       
                        $retValue[] = $checked . $coDAO->label;
                    }
                }
            }
            break;
        }

        //special case for option per line formatting
        if ( $optionPerLine > 1 && is_array( $retValue ) ) {
            $rowCounter = 0;
            $fieldCounter = 0;
            $displayValues = array( );
            $displayString = null;
            foreach ( $retValue as $val ) {
                if ( $displayString ) {
                    $displayString .=  ",&nbsp;";
                }

                $displayString .= $val;
                $rowCounter++; 
                $fieldCounter++;

                if ( ( $rowCounter == $optionPerLine ) || ( $fieldCounter == count( $retValue ) ) ) {
                    $displayValues[] = $displayString;
                    $displayString = null;
                    $rowCounter = 0;
                }
            }
            $retValue = $displayValues;
        }

        $retValue = isset($retValue) ? $retValue : null ;
        return $retValue;
    }
}
