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
require_once 'CRM/Core/OptionGroup.php';
require_once 'CRM/Core/DAO/CustomField.php';
require_once 'CRM/Core/DAO/CustomGroup.php';
require_once 'CRM/Core/BAO/CustomOption.php';

/**
 * Business objects for managing custom data fields.
 *
 */
class CRM_Core_BAO_CustomField extends CRM_Core_DAO_CustomField 
{
    /**
     * Array for valid combinations of data_type & descriptions
     *
     * @var array
     * @static
     */
    public static $_dataType = null;

    /**
     * Array to hold (formatted) fields for import
     *
     * @var array
     * @static
     */
    public static $_importFields = null;

    /**
     * Build and retrieve the list of data types and descriptions
     *
     * @param NULL
     * @return array        Data type => Description
     * @access public
     * @static
     */
    static function &dataType()
    {
        if ( !(self::$_dataType) ) {
            self::$_dataType = array(
                                     'String'        => ts('Alphanumeric'),
                                     'Int'           => ts('Integer'),
                                     'Float'         => ts('Number'),
                                     'Money'         => ts('Money'),
                                     'Memo'          => ts('Note'),
                                     'Date'          => ts('Date'),
                                     'Boolean'       => ts('Yes or No'),
                                     'StateProvince' => ts('State/Province'),
                                     'Country'       => ts('Country'),
                                     'File'          => ts('File'),
                                     'Link'          => ts('Link')
                                     );
        }
        return self::$_dataType;
    }
    
    /**
     * takes an associative array and creates a custom field object
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Core_DAO_CustomField object
     * @access public
     * @static
     */
    static function create( &$params )
    {
        if ( !isset($params['id']) && !isset($params['column_name']) ) {
            // if add mode & column_name not present, calculate it.
            require_once 'CRM/Utils/String.php';
            $params['column_name'] = strtolower( CRM_Utils_String::munge( $params['label'], '_', 32 ) );
        } else if ( isset($params['id']) ) {
            $params['column_name'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',
                                                                  $params['id'],
                                                                  'column_name' );
        }
        
        $indexExist = false;
        //as during create if field is_searchable we had created index.
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            $indexExist = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField', $params['id'], 'is_searchable' );
        }
        
        if ( ( $params['html_type'] == 'CheckBox' ||
               $params['html_type'] == 'Multi-Select' ) &&
             isset($params['default_checkbox_option'] ) ) {
            $tempArray = array_keys($params['default_checkbox_option']);
            $defaultArray = array();
            foreach ($tempArray as $k => $v) {
                if ( $params['option_value'][$v] ) {
                    $defaultArray[] = $params['option_value'][$v];
                }
            }
            
            if ( ! empty( $defaultArray ) ) {
                // also add the seperator before and after the value per new conventio (CRM-1604)
                $params['default_value'] = 
                    CRM_Core_BAO_CustomOption::VALUE_SEPERATOR .
                    implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $defaultArray) .
                    CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
            }
        } else {
            if ( CRM_Utils_Array::value( 'default_option', $params ) 
                 && isset($params['option_value'][$params['default_option']] ) ) {
                $params['default_value'] = $params['option_value'][$params['default_option']];
            }
        }
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        // create any option group & values if required
        if ( $params['html_type'] != 'Text' &&
             in_array( $params['data_type'], array('String', 'Int', 'Float', 'Money') ) &&
             ! empty($params['option_value']) && is_array($params['option_value']) ) {

            $tableName = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup',
                                                      $params['custom_group_id'],
                                                      'table_name' );
            
                                                                        
            if ( $params['option_type'] == 1 ) {
                // first create an option group for this custom group
                require_once 'CRM/Core/BAO/OptionGroup.php';
                $optionGroup            =& new CRM_Core_DAO_OptionGroup( );
                $optionGroup->name      =  "{$params['column_name']}_". date( 'YmdHis' );
                $optionGroup->label     =  $params['label'];
                $optionGroup->is_active = 1;
                $optionGroup->save( );
                $params['option_group_id'] = $optionGroup->id;
                
                require_once 'CRM/Core/BAO/OptionValue.php';
                require_once 'CRM/Utils/Rule.php';
                $moneyField = false;
                if ( $params['data_type'] == 'Money' ) {
                    $moneyField = true;
                }
                foreach ($params['option_value'] as $k => $v) {
                    if (strlen(trim($v))) {
                        $optionValue                  =& new CRM_Core_DAO_OptionValue( );
                        $optionValue->option_group_id =  $optionGroup->id;
                        $optionValue->label           =  $params['option_label'][$k];
                        $optionValue->value           =  $moneyField ? number_format(CRM_Utils_Rule::cleanMoney( $v ),2) : $v;
                        $optionValue->weight          =  $params['option_weight'][$k];
                        $optionValue->is_active       =  CRM_Utils_Array::value( $k, $params['option_status'], false );
                        $optionValue->save( );
                    }
                }
            }
        }

        // check for orphan option groups
        if ( CRM_Utils_Array::value( 'option_group_id', $params ) ) {
            if ( CRM_Utils_Array::value( 'id', $params ) ) {
                self::fixOptionGroups( $params['id'], $params['option_group_id'] ) ;
            }

            // if we dont have a default value
            // retrive it from one of the other custom fields which use this option group
            if ( ! CRM_Utils_Array::value( 'default_value', $params ) ) {
                $params['default_value'] = self::getOptionGroupDefault( $params['option_group_id'],
                                                                        $params['html_type'] );
            }
        }

        // since we need to save option group id :)
        if ( !isset($params['attributes']) && strtolower( $params['html_type'] ) == 'textarea' ) {
            $params['attributes'] = 'rows=4, cols=60';
        }

        // process data params
        if ( is_array( CRM_Utils_Array::value( 'date_parts', $params ) ) ) {
            $params['date_parts'] = implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,
                                             array_keys($params['date_parts']) );
        } else {
            $params['date_parts'] = "";
        }

        $customField =& new CRM_Core_DAO_CustomField();
        $customField->copyValues( $params );
        $customField->is_required      = CRM_Utils_Array::value( 'is_required'    , $params, false );
        $customField->is_searchable    = CRM_Utils_Array::value( 'is_searchable'  , $params, false );
        $customField->is_search_range  = CRM_Utils_Array::value( 'is_search_range', $params, false );
        $customField->is_active        = CRM_Utils_Array::value( 'is_active'      , $params, false );
        $customField->is_view          = CRM_Utils_Array::value( 'is_view'        , $params, false );
        $customField->save( );
        
        // make sure all values are present in the object for further processing
        $customField->find(true);
        
        //create/drop the index when we toggle the is_searchable flag
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            self::createField( $customField, 'modify', $indexExist );
        } else {
            $customField->column_name .= "_{$customField->id}";
            $customField->save();
            // make sure all values are present in the object
            $customField->find(true);
            
            self::createField( $customField, 'add' );
        }
        
        // complete transaction
        $transaction->commit( );

        return $customField;
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
     * @return object CRM_Core_DAO_CustomField object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults )
    {
        return CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_CustomField', $params, $defaults );
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id         Id of the database record
     * @param boolean  $is_active  Value we want to set the is_active field
     *
     * @return   Object            DAO object on sucess, null otherwise
     * 
     * @access public
     * @static
     */
    static function setIsActive( $id, $is_active )
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_CustomField', $id, 'is_active', $is_active );
    }
    
    /**
     * Get the field title.
     *
     * @param int $id id of field.
     * @return string name 
     *
     * @access public
     * @static
     *
     */
    public static function getTitle( $id )
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField', $id, 'label' );
    }
    
    /**
     * Store and return an array of all active custom fields.
     *
     * @param string      $customDataType      type of Custom Data
     * @param boolean     $showAll             If true returns all fields (includes disabled fields)
     * @param boolean     $inline              If true returns all inline fields (includes disabled fields)
     * @param int         $customDataSubType   Custom Data sub type value
	 * @param int         $customDataSubName   Custom Data sub name value
     * @param boolean     $onlyParent          return only top level custom data, for eg, only Participant and ignore subname and subtype  
	 *
     * @return array      $fields - an array of active custom fields.
     *
     * @access public
     * @static
     */
    public static function &getFields( $customDataType = 'Individual',
                                       $showAll = false,
                                       $inline = false,
                                       $customDataSubType = null,
 									   $customDataSubName = null,
 									   $onlyParent = false ) 
    {
        $cacheKey  = $customDataType;
        $cacheKey .= $customDataSubType ? "{$customDataSubType}_" : "_0";
		$cacheKey .= $customDataSubName ? "{$customDataSubName}_" : "_0";
        $cacheKey .= $showAll ? "_1" : "_0";
        $cacheKey .= $inline  ? "_1_" : "_0_";
		$cacheKey .= $onlyParent  ? "_1_" : "_0_";

        $cgTable = CRM_Core_DAO_CustomGroup::getTableName();

        // also get the permission stuff here
        require_once 'CRM/Core/Permission.php';
        $permissionClause = CRM_Core_Permission::customGroupClause( CRM_Core_Permission::VIEW,
                                                                    "{$cgTable}." );

        // lets md5 permission clause and take first 8 characters
        $cacheKey .= substr( md5( $permissionClause ), 0, 8 );

        if ( ! self::$_importFields ||
             CRM_Utils_Array::value( $cacheKey, self::$_importFields ) === null ) { 
            if ( ! self::$_importFields ) {
                self::$_importFields = array( );
            }

            // check if we can retrieve from database cache
            require_once 'CRM/Core/BAO/Cache.php'; 
            $fields =& CRM_Core_BAO_Cache::getItem( 'contact fields', "custom importableFields $cacheKey" );

            if ( $fields === null ) {
                $cfTable = self::getTableName();

                $extends = '';
                if ( $customDataType ) {
                    if ( in_array( $customDataType, array( 'Individual', 'Household', 'Organization' ) ) ) {
                        $value = "'" . CRM_Utils_Type::escape($customDataType, 'String') . "', 'Contact' ";
                    } else {
                        $value = "'" . CRM_Utils_Type::escape($customDataType, 'String') . "'";
                    }
                    $extends = "AND   $cgTable.extends IN ( $value ) ";

					if ( $onlyParent ) {
						$extends .= " AND $cgTable.extends_entity_column_value IS NULL AND $cgTable.extends_entity_column_id IS NULL ";
					}
                }
                
                $query ="SELECT $cfTable.id, $cfTable.label,
                            $cgTable.title,
                            $cfTable.data_type, $cfTable.html_type,
                            $cfTable.options_per_line,
                            $cgTable.extends, $cfTable.is_search_range,
                            $cgTable.extends_entity_column_value,
                            $cfTable.is_view,
                            $cgTable.is_multiple
                     FROM $cfTable
                     INNER JOIN $cgTable
                     ON $cfTable.custom_group_id = $cgTable.id
                     WHERE ( 1 ) ";

                if (! $showAll) {
                    $query .= " AND $cfTable.is_active = 1 AND $cgTable.is_active = 1 ";
                }

                if ( $inline ) {
                    $query .= " AND $cgTable.style = 'Inline' ";
                }
                
                //get the custom fields for specific type in
                //combination with fields those support any type.
                if ( $customDataSubType ) {
                    $query .= " AND ( $cgTable.extends_entity_column_value = $customDataSubType 
                                      OR $cgTable.extends_entity_column_value IS NULL )";
                }
                
                if ( $customDataSubName ) {
                    $query .= " AND ( $cgTable.extends_entity_column_id = $customDataSubName ) "; 
                }

                // also get the permission stuff here
                require_once 'CRM/Core/Permission.php';
                $permissionClause = CRM_Core_Permission::customGroupClause( CRM_Core_Permission::VIEW,
                                                                            "{$cgTable}." );

                $query .= " $extends AND $permissionClause
                        ORDER BY $cgTable.weight, $cgTable.title,
                                 $cfTable.weight, $cfTable.label";
         
                $dao =& CRM_Core_DAO::executeQuery( $query );
        
                $fields = array( );
                while ( ( $dao->fetch( ) ) != null) {
                    $fields[$dao->id]['label']                       = $dao->label;
                    $fields[$dao->id]['groupTitle']                  = $dao->title;
                    $fields[$dao->id]['data_type']                   = $dao->data_type;
                    $fields[$dao->id]['html_type']                   = $dao->html_type;
                    $fields[$dao->id]['options_per_line']            = $dao->options_per_line;
                    $fields[$dao->id]['extends']                     = $dao->extends;
                    $fields[$dao->id]['is_search_range']             = $dao->is_search_range;
                    $fields[$dao->id]['extends_entity_column_value'] = $dao->extends_entity_column_value;
                    $fields[$dao->id]['is_view']                     = $dao->is_view;
                    $fields[$dao->id]['is_multiple']                 = $dao->is_multiple;
                }

                CRM_Core_BAO_Cache::setItem( $fields,
                                             'contact fields',
                                             "custom importableFields $cacheKey" );
            }
            self::$_importFields[$cacheKey] = $fields;
        }
        
        return self::$_importFields[$cacheKey];
    }

    /**
     * Return the field ids and names (with groups) for import purpose.
     *
     * @param int      $contactType   Contact type
     * @param boolean  $showAll       If true returns all fields (includes disabled fields)
     *
     * @return array   $fields - 
     *
     * @access public
     * @static
     */
    public static function &getFieldsForImport($contactType = 'Individual', $showAll = false) 
    {
        $fields =& self::getFields($contactType, $showAll);
        
        $importableFields = array();
        foreach ($fields as $id => $values) {
            /* generate the key for the fields array */
            $key = "custom_$id";
            $regexp = preg_replace('/[.,;:!?]/', '', $values[0]);
            $importableFields[$key] = array(
                                            'name'             => $key,
                                            'title'            => CRM_Utils_Array::value('label', $values),
                                            'headerPattern'    => '/' . preg_quote($regexp, '/') . '/',
                                            'import'           => 1,
                                            'custom_field_id'  => $id,
                                            'options_per_line' => CRM_Utils_Array::value('options_per_line', $values),
                                            'data_type'        => CRM_Utils_Array::value('data_type', $values),
                                            'html_type'        => CRM_Utils_Array::value('html_type', $values),
                                            'is_search_range'  => CRM_Utils_Array::value('is_search_range', $values),
                                            );
        }
         
        return $importableFields;
    }

    /**
     * Get the field id from an import key
     *
     * @param string $key       The key to parse
     * @return int|null         The id (if exists)
     * @access public
     * @static
     */
    public static function getKeyID($key, $all = false) 
    {
        $match = array( );
        if (preg_match('/^custom_(\d+)_?(-?\d+)?$/', $key, $match)) {
            if ( ! $all ) {
                return $match[1];
            } else {
                return array( $match[1],
                              CRM_Utils_Array::value( 2, $match ) );
            } 
        }
        return null;
    }
    
    
    /**
     * This function for building custom fields
     * 
     * @param object  $qf             form object (reference)
     * @param string  $elementName    name of the custom field
     * @param boolean $inactiveNeeded 
     * @param boolean $userRequired   true if required else false
     * @param boolean $search         true if used for search else false
     * @param string  $label          label for custom field        
     *
     * @access public
     * @static
     */
    public static function addQuickFormElement( &$qf,
                                                $elementName,
                                                $fieldId,
                                                $inactiveNeeded = false,
                                                $useRequired = true,
                                                $search = false,
                                                $label = null ) 
    {
        if( isset( $qf->_submitValues['_qf_Relationship_refresh'] ) && 
            ( $qf->_submitValues['_qf_Relationship_refresh'] == 'Search' || 
              $qf->_submitValues['_qf_Relationship_refresh'] == 'Search Again') ) {
            $useRequired = 0;
        }
        
        $field =& new CRM_Core_DAO_CustomField();
        
        $field->id = $fieldId;
        if (! $field->find(true)) {
            CRM_Core_Error::fatal( );

        }
        // Fixed for Issue CRM-2183
        if ( $field->html_type == 'TextArea' && $search ){
            $field->html_type = 'Text';
        }
        if (!isset($label)) {
            $label = $field->label;
        }

        /**
         * at some point in time we might want to split the below into small functions
         **/
        switch ( $field->html_type ) {
        case 'Text':
            if ($field->is_search_range && $search) {
                $qf->add('text', $elementName.'_from', $label . ' ' . ts('From'), $field->attributes);
                $qf->add('text', $elementName.'_to', ts('To'), $field->attributes);
            } else {
                $element =& $qf->add(strtolower($field->html_type), $elementName, $label,
                                     $field->attributes, (( $useRequired ||( $useRequired && $field->is_required) ) && !$search));
            }
            break;

        case 'TextArea':
            $attributes = '';
            if( $field->note_rows ) {
                $attributes .='rows='.$field->note_rows; 
            } else {
                $attributes .='rows=4';
            }
            
            if( $field->note_columns ) {
                $attributes .=' cols='.$field->note_columns;
            } else {
                $attributes .=' cols=60';
            }
            $element =& $qf->add(strtolower($field->html_type), $elementName, $label,
                                 $attributes, (( $useRequired ||( $useRequired && $field->is_required) ) && !$search));
            break;

        case 'Select Date':
            if ( $field->is_search_range && $search) {
                $qf->add('date',
                         $elementName.'_from',
                         $label . ' - ' . ts('From'),
                         CRM_Core_SelectValues::date( 'custom' ,
                                                      $field->start_date_years,
                                                      $field->end_date_years,
                                                      $field->date_parts ),
                         (($useRequired && $field->is_required) && !$search)); 
                $qf->add('date',
                         $elementName.'_to',
                         ts('To'), 
                         CRM_Core_SelectValues::date( 'custom' , 
                                                      $field->start_date_years,
                                                      $field->end_date_years,
                                                      $field->date_parts ),
                         (($useRequired && $field->is_required) && !$search)); 
            } else {
                $qf->add('date',
                         $elementName,
                         $label,
                         CRM_Core_SelectValues::date( 'custom', 
                                                      $field->start_date_years,
                                                      $field->end_date_years,
                                                      $field->date_parts ),
                         ( ( $useRequired ||( $useRequired && $field->is_required ) ) && !$search ) );
            }
            break;

        case 'Radio':
            $choice = array();
            if($field->data_type != 'Boolean') {
                $customOption =& CRM_Core_OptionGroup::valuesByID( $field->option_group_id );
                foreach ($customOption as $v => $l ) {
                    $choice[] = $qf->createElement('radio', null, '', $l, $v, $field->attributes);
                }
                $qf->addGroup($choice, $elementName, $label);
            } else {
                $choice[] = $qf->createElement('radio', null, '', ts('Yes'), '1', $field->attributes);
                $choice[] = $qf->createElement('radio', null, '', ts('No') , '0' , $field->attributes);
                $qf->addGroup($choice, $elementName, $label);
            }
            if (( $useRequired ||( $useRequired && $field->is_required) ) && !$search) {
                $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $label)) , 'required');
            }
            break;
            
        case 'Select':
            $selectOption =& CRM_Core_OptionGroup::valuesByID( $field->option_group_id );
            $qf->add('select', $elementName, $label,
                     array( '' => ts('- select -')) + $selectOption,
                     ( ( $useRequired || ($useRequired && $field->is_required) ) && !$search));
            break;

            //added for select multiple
        case 'Multi-Select':
            $selectOption =& CRM_Core_OptionGroup::valuesByID( $field->option_group_id );
            if ( $search &&
                 count( $selectOption ) > 1 ) {
                $selectOption['CiviCRM_OP_OR'] = ts( 'Use SQL OR' );
            }
            $qf->addElement('select', $elementName, $label, $selectOption,  array("size"=>"5","multiple"));
            
            if (( $useRequired ||( $useRequired && $field->is_required) ) && !$search) {
                $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $label)) , 'required');
            }
            break;

        case 'CheckBox':
            $customOption = CRM_Core_OptionGroup::valuesByID( $field->option_group_id );
            $check = array();
            foreach ($customOption as $v => $l) {
                $check[] =& $qf->createElement('checkbox', $v, null, $l); 
            }
            if ( $search &&
                 count( $check ) > 1 ) {
                $check[] =& $qf->createElement('checkbox', 'CiviCRM_OP_OR', null, ts( 'Use SQL OR' ) ); 
            }
            $qf->addGroup($check, $elementName, $label);
            if (( $useRequired ||( $useRequired && $field->is_required) ) && !$search) {
                $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $label)) , 'required');
            }
            break;
            
        case 'File':
            // we should not build upload file in search mode
            if ( $search ) {
                return;
            }
            $element =& $qf->add( strtolower($field->html_type), $elementName, $label,
                                  $field->attributes,
                                  ( ( $useRequired && $field->is_required ) && ! $search ) );
            $qf->addUploadElement( $elementName );
            break;

        case 'Select State/Province':
            //Add State
            if ($qf->getAction() & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
                $stateOption = array('' => '') + CRM_Core_PseudoConstant::stateProvince();
            } else { 
                $stateOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince();
            }
            $qf->add('select', $elementName, $label, $stateOption, (($useRequired && $field->is_required) && !$search));
            break;
        case 'Multi-Select State/Province':
            //Add Multi-select State/Province
            if ($qf->getAction() & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
                $stateOption = array('' => '') + CRM_Core_PseudoConstant::stateProvince();
            } else {
                $stateOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince();
            }
            $qf->addElement('select', $elementName, $label, $stateOption, array("size"=>"5","multiple"));
            break;
            
        case 'Select Country':
            //Add Country
            if ($qf->getAction() & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
                $countryOption = array('' => '') + CRM_Core_PseudoConstant::country();
	    } else {
                $countryOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::country();
            }
            $qf->add('select', $elementName, $label, $countryOption, (($useRequired && $field->is_required) && !$search));
            break;

        case 'Multi-Select Country':
            //Add Country
            if ($qf->getAction() & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
                $countryOption = array('' => '') + CRM_Core_PseudoConstant::country();
            } else {
                $countryOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::country();
            }
            $qf->addElement('select', $elementName, $label, $countryOption, array("size"=>"5","multiple"));
            break;
        
        case 'RichTextEditor':
            $element =& $qf->addWysiwyg( $elementName, $label, CRM_Core_DAO::$_nullArray, $search );
        }
        
        switch ( $field->data_type ) {

        case 'Int':
            // integers will have numeric rule applied to them.
            if ( $field->is_search_range && $search) {
                $qf->addRule($elementName.'_from', ts('%1 From must be an integer (whole number).', array(1 => $label)),'integer');
                $qf->addRule($elementName.'_to', ts('%1 To must be an integer (whole number).', array(1 => $label)), 'integer');
            } else {
                $qf->addRule($elementName, ts('%1 must be an integer (whole number).', array(1 => $label)), 'integer');
            }
            break;
            
        case 'Date':
            if ( $field->is_search_range && $search) {
                $qf->addRule($elementName.'_from', ts('%1 From is not a valid date.', array(1 => $label)), 'qfDate');
                $qf->addRule($elementName.'_to', ts('%1 To is not a valid date.', array(1 => $label)), 'qfDate');
            } else {
                $qf->addRule($elementName, ts('%1 is not a valid date.', array(1 => $label)), 'qfDate');
            }
            break;
            
        case 'Float':
            if ( $field->is_search_range && $search) {
                $qf->addRule($elementName.'_from', ts('%1 From must be a number (with or without decimal point).', array(1 => $label)), 'numeric');
                $qf->addRule($elementName.'_to', ts('%1 To must be a number (with or without decimal point).', array(1 => $label)), 'numeric');
            } else {
                $qf->addRule($elementName, ts('%1 must be a number (with or without decimal point).', array(1 => $label)), 'numeric');
            }
            break;

        case 'Money':
            if ( $field->is_search_range && $search) {
                $qf->addRule($elementName.'_from', ts('%1 From must in proper money format. (decimal point/comma/space is allowed).', array(1 => $label)), 'money');
                $qf->addRule($elementName.'_to', ts('%1 To must in proper money format. (decimal point/comma/space is allowed).', array(1 => $label)), 'money');
            } else {
                $qf->addRule($elementName, ts('%1 must be in proper money format. (decimal point/comma/space is allowed).', array(1 => $label)), 'money');
            }
            break;

        case 'Link':
            $element =& $qf->add('text',
                                 $elementName,
                                 $label,
                                 array('onfocus' => "if (!this.value) this.value='http://'; else return false",
                                       'onblur'  => "if ( this.value == 'http://') this.value=''; else return false"),
                                 (( $useRequired ||( $useRequired && $field->is_required) ) && !$search));
            $qf->addRule( $elementName, ts('Enter a valid Website.'),'wikiURL');
                    
            break;
        }
    }
    
    /**
     * Delete the Custom Field.
     *
     * @param   object $field - the field object
     * 
     * @return  boolean
     *
     * @access public
     * @static
     *
     */
    public static function deleteField( $field )
    { 
        // reset the cache
        require_once 'CRM/Core/BAO/Cache.php';
        CRM_Core_BAO_Cache::deleteGroup( 'contact fields' );

        // first delete the custom option group and values associated with this field
        if ( $field->option_group_id ) {
            //check if option group is related to any other field, if
            //not delete the option group and related option values
            self::checkOptionGroup(  $field->option_group_id );
        }

        // next drop the column from the custom value table
        self::createField( $field, 'delete' );

        $field->delete( );
        return;
    }

    /**
     * Given a custom field value, its id and the set of options
     * find the display value for this field
     *
     * @param mixed  $value     the custom field value
     * @param int    $id        the custom field id
     * @param int    $options   the assoc array of option name/value pairs
     *
     * @return  string   the display value
     * 
     * @static
     * @access public
     */
    static function getDisplayValue( $value, $id, &$options, $contactID = null )
    {
        $option     =& $options[$id];
        $attributes =& $option['attributes'];
        $html_type  =  $attributes['html_type'];
        $data_type  =  $attributes['data_type'];
        $index      =  $attributes['label'];

        $display = $value;

        switch ( $html_type ) {

        case "Radio":
            if ( $data_type == 'Boolean' ) {
                $display = $value ? ts('Yes') : ts('No');
            } else {
                $display = $option[$value];
            }
            break;

        case "Select":
            $display = CRM_Utils_Array::value( $value, $option );
            break;
        
        case "CheckBox":
        case "Multi-Select":
            if ( is_array( $value ) ) {
                $checkedData = $value;
            } else {
                $checkedData = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, substr($value,1,-1));
                if ( $html_type == 'CheckBox' ) {
                    $checkedData = array_flip( $checkedData );
                }
            }

            $v = array( );
            $p = array( );
            foreach ( $checkedData as $key => $val ) {
                if ( $key === 'CiviCRM_OP_OR' ) {
                    continue;
                }
                if ( $html_type == 'CheckBox' ) {
                    $p[] = $key;
                    $v[] = $option[$key];
                } else {
                    $p[] = $val;
                    $v[] = $option[$val];
                }
            }
            if ( ! empty( $v ) ) {
                $display = implode( ', ', $v );
            }
            break;

        case "Select Date":
            $dao = & new CRM_Core_DAO_CustomField();
            $dao->id = $id;
            $dao->find(true);
            $parts = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $dao->date_parts);
            $display = CRM_Utils_Date::customFormat($value, null, $parts);
            break;

        case 'Select State/Province':
            if ( empty( $value ) ) {
                $display = '';
            } else {
                $display = CRM_Core_PseudoConstant::stateProvince($value);
            }
            break;
        case 'Multi-Select State/Province':
            if ( empty( $value ) ) {
                $display = '';
            } else {
                $display = CRM_Core_PseudoConstant::stateProvince($value);
            }
            break;
            
        case 'Select Country':
            if ( empty( $value ) ) {
                $display = '';
            } else {
                $display = CRM_Core_PseudoConstant::country($value);
            }
            break;
            
        case 'Multi-Select Country':
            if ( empty( $value ) ) {
                $display = '';
            } else {
                $display = CRM_Core_PseudoConstant::country($value);
            }
            break;

        case 'File':
            if ( $contactID ) {
                $url = self::getFileURL( $contactID, $id, $value );
                if ( $url ) {
                    $display = $url['file_url'];
                }
            }
            break;

        case 'Link':
            if ( empty( $value ) ) {
                $display='';
            } else {
                $display = $value;
            }  
                
        }
        
        return $display ? $display : $value;
    }
    
    /**
     * Given a custom field value, its id and the set of options
     * find the default value for this field
     *
     * @param  mixed  $value     the custom field value
     * @param  int    $id        the custom field id
     * @param  int    $options   the assoc array of option name/value pairs
     *
     * @return   mixed   the default value
     * @static
     * @access public
     */
    function getDefaultValue( $value, $id, &$options ) 
    { 
        $option     =& $options[$id]; 
        $attributes =& $option['attributes']; 
        $html_type  =  $attributes['html_type']; 
        $index      =  $attributes['label'];

        $default = $value;

        switch ( $html_type ) {

        case "CheckBox":
            $checkedData = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, substr($value,1,-1));
            $default = array( );
            foreach ( $checkedData as $val ) {
                $default[$val] = 1;
            }
            break;

        case "Select Date":
            $default = CRM_Utils_Date::unformat($value);
            break;
        }

        return $default;
    }

    /**
     * Function to set default values for custom data used in profile
     *
     * @params int    $customFieldId custom field id
     * @params string $elementName   custom field name
     * @params array  $defaults      associated array of fields
     * @params int    $contactId     contact id
     * @param  int    $mode          profile mode
     * 
     * @static
     * @access public
     */
    static function setProfileDefaults( $customFieldId,
                                        $elementName,
                                        &$defaults,
                                        $contactId = null,
                                        $mode = null ) 
    {
        //get the type of custom field
        $customField =& new CRM_Core_BAO_CustomField();
        
        $customField->id = $customFieldId;
        
        $customField->find(true);
        
        require_once "CRM/Profile/Form.php";
        
        $value = null;
        $value = null;
        if ( ! $contactId ) {
            if ($mode == CRM_Profile_Form::MODE_CREATE ) {
                $value = $customField->default_value;
            }
        } else {
            $info   = self::getTableColumnGroup( $customFieldId );
            
            $query  = "SELECT {$info[0]}.{$info[1]} as value FROM {$info[0]} WHERE {$info[0]}.entity_id = {$contactId}";
            
            $result = CRM_Core_DAO::executeQuery( $query );
            
            if ( $result->fetch( ) ) {
                $value = $result->value;
            }
            
            if ( $customField->data_type == 'Country' ) {
                if ( ! $value ) {
                    $config =& CRM_Core_Config::singleton();
                    if ( $config->defaultContactCountry ) {
                        $value = $config->defaultContactCountry( );
                    }
                }
            }
        }
        
        //set defaults if mode is registration / edit
        if ( ! trim( $value ) &&
             ( $value !== 0 ) &&
             ( $mode != CRM_Profile_Form::MODE_SEARCH ) ) {
            $value = $customField->default_value;
        }

        switch ($customField->html_type) {
            
        case 'CheckBox':
            $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldId, false);

            $defaults[$elementName] = array();

            $checkedValue = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, substr($value,1,-1));
            foreach($customOption as $val) {
                if ( in_array($val['value'], $checkedValue) ) {
                    $defaults[$elementName][$val['value']] = 1;
                } else {
                    $defaults[$elementName][$val['value']] = 0;
                }
            }                            
            break;
            
            //added a case for Multi-Select option                    
        case 'Multi-Select':
            $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id'], false);
            $defaults[$elementName] = array();
            $checkedValue = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, substr($value,1,-1));
            foreach($customOption as $val) {
                if ( in_array($val['value'], $checkedValue) ) {
                    $defaults[$elementName][$val['value']] = $val['value'];
                }
            }                            
            break;

        case 'File':
            //$defaults["custom_value_{$customFieldId}_id"] = $cv->id; 
            $defaults[$elementName] = $value;
            break;
            
        default:
            $defaults[$elementName] = $value;
        }
    }

    static function getFileURL( $contactID, $cfID, $fileID = NULL ) 
    {
        if ( $contactID ) {
            if ( ! $fileID ) {
                $params   = array( 'id' => $cfID );
                $defaults = array();
                CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_CustomField', $params, $defaults );
                $columnName = $defaults['column_name'];
            
                //table name of custom data
                $tableName  = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup', 
                                                           $defaults['custom_group_id'], 
                                                           'table_name', 'id' );
                
                //query to fetch id from civicrm_file
                $query = "SELECT {$columnName} FROM {$tableName} where entity_id = {$contactID}";
                $fileID = CRM_Core_DAO::singleValueQuery( $query );
            }
            
            $result = array();
            if ( $fileID ) {
                $fileType = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_File',
                                                         $fileID,
                                                         'mime_type',
                                                         'id' );
                $result['file_id'] = $fileID;
                
                if ( $fileType == "image/jpeg"  ||
                     $fileType == "image/pjpeg" ||
                     $fileType == "image/gif"   ||
                     $fileType == "image/x-png" ||
                     $fileType == "image/png" ) { 
                    $url = CRM_Utils_System::url( 'civicrm/file', "reset=1&id=$fileID&eid=$contactID" );
                    $result['file_url'] = "<a href='javascript:popUp(\"$url\");'><img src=\"$url\" width=100 height=100/></a>";
                } else { // for non image files
                    $uri = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_File',
                                                         $fileID,
                                                         'uri'
                                                        );
                    $url = CRM_Utils_System::url( 'civicrm/file', "reset=1&id=$fileID&eid=$contactID" );
                    $result['file_url'] = "<a href=\"$url\">{$uri}</a>";
                }                                    
            }
            return $result;
        }
    }
    
    /**
     * Format custom fields before inserting
     *
     * @param int    $customFieldId       custom field id
     * @param array  $customFormatted     formatted array
     * @param mix    $value               value of custom field
     * @param string $customFieldExtend   custom field extends
     * @param int    $customValueId custom option value id
     * @param int    $entityId            entity id (contribution, membership...)
     *
     * @return array $customFormatted formatted custom field array
     * @static
     */
    static function formatCustomField( $customFieldId, &$customFormatted, $value, 
                                       $customFieldExtend, $customValueId = null,
                                       $entityId = null, 
                                       $inline = false ) 
    {
        //get the custom fields for the entity
        $customFields = CRM_Core_BAO_CustomField::getFields( $customFieldExtend, false, $inline );

        if ( ! array_key_exists( $customFieldId, $customFields )) {
            return;
        }

        // return if field is a 'code' field
        if ( CRM_Utils_Array::value( 'is_view', $customFields[$customFieldId] ) ) {
            return;
        }

        list( $tableName, $columnName, $groupID ) = self::getTableColumnGroup( $customFieldId );
        
        if ( ! $customValueId &&
             ! $customFields[$customFieldId]['is_multiple'] && // we always create new entites for is_multiple unless specified
             $entityId ) {
            //get the entity table for the custom field
            require_once "CRM/Core/BAO/CustomQuery.php";
            $entityTable = CRM_Core_BAO_CustomQuery::$extendsMap[$customFieldExtend];

            $query = "
SELECT id 
  FROM $tableName
 WHERE entity_id={$entityId}";

            $customValueId = CRM_Core_DAO::singleValueQuery( $query );
        }

        //fix checkbox
        if ( $customFields[$customFieldId]['html_type'] == 'CheckBox' ) {
            if ( $value ) {
                $value =
                    CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . 
                    implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,
                             array_keys( $value ) ) .
                    CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
            } else {
                $value = '';
            }
        } 
        
        if ( $customFields[$customFieldId]['html_type'] == 'Multi-Select' ) {
            if ( $value ) {
                $value = 
                    CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . 
                    implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,
                             array_values( $value ) ) .
                    CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
            } else {
                $value = '';
            }
        }

        // fix the date field 
        if ( $customFields[$customFieldId]['data_type'] == 'Date' ) {
            if ( ! CRM_Utils_System::isNull( $value ) ) {
                if ( is_string( $value ) ) {
                    // it might be a string, so lets do an unformat
                    // check if the seperator exists in string
                    $separator = '-';
                    if ( strpos( $value, $separator ) === false ) {
                        $separator = '';
                    }
                    $unformat = CRM_Utils_Date::unformat( $value, $separator );
                    if ( $unformat ) {
                        $value = $unformat;
                    }
                }

                //convert date to timestamp
                $time = array( 'H', 'i', 's' );
                foreach ( $time as $v ) {
                    if ( ! isset( $value[$v] ) ) {
                        $value[$v] = '00';
                    }                    
                    $date = CRM_Utils_Date::format( $value );                    
                }
            }
            if ( ! $date ) {
                $date = null;
            }
            $value = $date;
        }

        if ( $customFields[$customFieldId]['data_type'] == 'Float' || 
             $customFields[$customFieldId]['data_type'] == 'Money' || 
             $customFields[$customFieldId]['data_type'] == 'Int' ) {
            if ( !$value ) {
                $value = 0;  
            }

            if ( $customFields[$customFieldId]['data_type'] == 'Money' ) {
                require_once 'CRM/Utils/Rule.php';
                $value = CRM_Utils_Rule::cleanMoney( $value );
            }
        }
               
        if ( ( $customFields[$customFieldId]['data_type'] == 'StateProvince' || 
               $customFields[$customFieldId]['data_type'] == 'Country') &&
             empty( $value ) ) {
            // CRM-3415
            $value = 0;
        }

        $fileId = null;

        if ( $customFields[$customFieldId]['data_type'] == 'File' ) {
            if ( empty($value) ) {
                return;
            }


            require_once 'CRM/Core/DAO/File.php';
            $config = & CRM_Core_Config::singleton();

            $fName    = $value['name']; 
            $mimeType = $value['type']; 

            $path = explode( '/', $fName );
            $filename = $path[count($path) - 1];
            
            // rename this file to go into the secure directory
            if ( ! rename( $fName, $config->customFileUploadDir . $filename ) ) {
                CRM_Core_Error::statusBounce( ts( 'Could not move custom file to custom upload directory' ) );
                break;
            }

            if ( $customValueId ) {
                $query = "
SELECT $columnName
  FROM $tableName
 WHERE id = %1";
                $params = array( 1 => array( $customValueId, 'Integer' ) );
                $fileId = CRM_Core_DAO::singleValueQuery( $query, $params );
            }
            
            $fileDAO =& new CRM_Core_DAO_File();
            
            if ( $fileId ) {
                $fileDAO->id = $fileId;
            }
            
            $fileDAO->uri         = $filename;
            $fileDAO->mime_type   = $mimeType; 
            $fileDAO->upload_date = date('Ymdhis'); 
            $fileDAO->save();
            $fileId = $fileDAO->id;
            $value  =  $filename;
        }

		if ( !is_array( $customFormatted ) ) {
			$customFormatted = array( );
		}
		
        if ( ! array_key_exists( $customFieldId, $customFormatted ) ) {
            $customFormatted[$customFieldId] = array( );
        }

        $index = -1;
        if ( $customValueId ) {
            $index = $customValueId;
        }

        if ( ! array_key_exists( $index, $customFormatted[$customFieldId] ) ) {
            $customFormatted[$customFieldId][$index] = array( );
        }
        $customFormatted[$customFieldId][$index] = array('id'              => $customValueId > 0 ? $customValueId : null,
                                                         'value'           => $value,
                                                         'type'            => $customFields[$customFieldId]['data_type'],
                                                         'custom_field_id' => $customFieldId, 
                                                         'custom_group_id' => $groupID,
                                                         'table_name'      => $tableName,
                                                         'column_name'     => $columnName,
                                                         'file_id'         => $fileId,
                                                         'is_multiple'     => $customFields[$customFieldId]['is_multiple'],
                                                         );

        return $customFormatted;
    }

    static function &defaultCustomTableSchema( &$params ) {
        // add the id and extends_id
        $table = array( 'name'        => $params['name'],
                        'is_multiple' => $params['is_multiple'],
                        'attributes'  => "ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci",
                        'fields'      => array(
                                               array( 'name'          => 'id',
                                                      'type'          => 'int unsigned',
                                                      'primary'       => true,
                                                      'required'      => true,
                                                      'attributes'    => 'AUTO_INCREMENT',
                                                      'comment'       => 'Default MySQL primary key' ),
                                               array( 'name'          => 'entity_id',
                                                      'type'          => 'int unsigned',
                                                      'required'      => true,
                                                      'comment'       => 'Table that this extends',
                                                      'fk_table_name' => $params['extends_name'],
                                                      'fk_field_name' => 'id',
                                                      'fk_attributes' => 'ON DELETE CASCADE' )
                                               )
                        );
        
        if ( ! $params['is_multiple'] ) {
            $table['indexes'] = array(
                                      array( 'unique'        => true,
                                             'field_name_1'  => 'entity_id' )
                                      );
        }
        return $table;
    }

    static function createField( $field, $operation, $indexExist = false ) {
        require_once 'CRM/Core/BAO/CustomValueTable.php';
        $params = array( 'table_name' => CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup',
                                                                      $field->custom_group_id,
                                                                      'table_name' ),
                         'operation'  => $operation,
                         'name'       => $field->column_name,
                         'type'       => CRM_Core_BAO_CustomValueTable::fieldToSQLType( $field->data_type,
                                                                                        $field->text_length ),
                         'required'   => $field->is_required,
                         'searchable' => $field->is_searchable,
                        );
         
        if ( $field->data_type == 'Country' && $field->html_type == 'Select Country' ) {
            $params['fk_table_name'] = 'civicrm_country';
            $params['fk_field_name'] = 'id';
            $params['fk_attributes'] = 'ON DELETE SET NULL';
        } else if ( $field->data_type == 'Country' && $field->html_type == 'Multi-Select Country' ) {
            $params['type'] ='varchar(255)';
        } else if ( $field->data_type == 'StateProvince' && $field->html_type == 'Select State/Province' ) {
            $params['fk_table_name'] = 'civicrm_state_province';
            $params['fk_field_name'] = 'id';
            $params['fk_attributes'] = 'ON DELETE SET NULL';
        } else if ( $field->data_type == 'StateProvince' && $field->html_type == 'Multi-Select State/Province' ) {
            $params['type'] ='varchar(255)';
        } else if ( $field->data_type == 'File' ) {
            $params['fk_table_name'] = 'civicrm_file';
            $params['fk_field_name'] = 'id';
            $params['fk_attributes'] = 'ON DELETE SET NULL';
        }
        if ( $field->default_value ) {
            $params['default'] = "'{$field->default_value}'";
        }

        require_once 'CRM/Core/BAO/SchemaHandler.php';
        CRM_Core_BAO_SchemaHandler::alterFieldSQL( $params, $indexExist );
    }

    static function getTableColumnGroup( $fieldID ) {
        static $cache = array( );

        if ( ! array_key_exists( $fieldID, $cache ) ) {
            $query = "
SELECT cg.table_name, cf.column_name, cg.id
FROM   civicrm_custom_group cg,
       civicrm_custom_field cf
WHERE  cf.custom_group_id = cg.id
AND    cf.id = %1";
            $params = array( 1 => array( $fieldID, 'Integer' ) );
            $dao = CRM_Core_DAO::executeQuery( $query, $params );
            
            if ( ! $dao->fetch( ) ) {
                CRM_Core_Error::fatal( );
            }
            $dao->free( );
            $cache[$fieldID] = array( $dao->table_name, $dao->column_name, $dao->id );
        }
        return $cache[$fieldID];
    }

    public static function &customOptionGroup( )
    {
        static $customOptionGroup = null;
        
        if ( ! $customOptionGroup ) {
            $query = "
SELECT g.id, f.label
FROM   civicrm_option_group g,
       civicrm_custom_field f
WHERE  g.id = f.option_group_id
AND    g.is_active = 1
AND    f.is_active = 1";
            $dao = CRM_Core_DAO::executeQuery( $query );
            $customOptionGroup = array( );
            while ( $dao->fetch( ) ) {
                $customOptionGroup[$dao->id] = $dao->label;
            }
        }
        return $customOptionGroup;
    }

    /**
     * Function to fix orphan groups
     * 
     * @params int $customFieldId custom field id
     * @params int $optionGroupId option group id
     *
     * @access public
     * @return void
     * @static
     */
    static function fixOptionGroups( $customFieldId, $optionGroupId )
    {
        // check if option group belongs to any custom Field else delete
        // get the current option group
        $currentOptionGroupId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',
                                                             $customFieldId,
                                                             'option_group_id' );
        // get the updated option group
        // if both are same return
        if ( $currentOptionGroupId == $optionGroupId ) {
            return;
        }

        // check if option group is related to any other field
        self::checkOptionGroup( $currentOptionGroupId );
    }

    /**
     * Function to check if option group is related to more than one
     * custom field
     *
     * @params int $optionGroupId option group id
     *
     * @return
     * @static
     */
    static function checkOptionGroup( $optionGroupId )
    {
        $query = "
SELECT count(*)
FROM   civicrm_custom_field
WHERE  option_group_id = {$optionGroupId}";
        
        $count = CRM_Core_DAO::singleValueQuery( $query );

        if ( $count < 2 ) {
            //delete the option group
            require_once "CRM/Core/BAO/OptionGroup.php";
            CRM_Core_BAO_OptionGroup::del( $optionGroupId );
        }
    }

    static function getOptionGroupDefault( $optionGroupId, $htmlType ) {
        $query = "
SELECT   default_value, html_type
FROM     civicrm_custom_field
WHERE    option_group_id = {$optionGroupId}
AND      default_value IS NOT NULL
ORDER BY html_type";

        $dao = CRM_Core_DAO::executeQuery( $query );
        $defaultValue    = null;
        $defaultHTMLType = null;
        while ( $dao->fetch( ) ) {
            if ( $dao->html_type == $htmlType ) {
                return $dao->default_value;
            }
            if ( $defaultValue == null ) {
                $defaultValue    = $dao->default_value;
                $defaultHTMLType = $dao->html_type;
            }
        }

        // some conversions are needed if either the old or new has a html type which has potential
        // multiple default values.
        if ( ( $htmlType == 'CheckBox' || $htmlType == 'Multi-Select' ) &&
             ( $defaultHTMLType != 'CheckBox' && $defaultHTMLType != 'Multi-Select' ) ) {
            $defaultValue =
                CRM_Core_BAO_CustomOption::VALUE_SEPERATOR .
                $defaultValue .
                CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
        } else if ( ( $defaultHTMLType == 'CheckBox' || $defaultHTMLType == 'Multi-Select' ) &&
                    ( $htmlType != 'CheckBox' && $htmlType != 'Multi-Select' ) ) {
            $defaultValue = substr( $defaultValue, 1, -1 );
            $values = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,
                               substr($defaultValue, 1, -1 ) );
            $defaultValue = $values[0];
        }

        return $defaultValue;
    }

    function postProcess( &$params,
                          &$customFields,
                          $entityID,
                          $customFieldExtends,
                          $inline = false ) {
        $customData = array( );
        foreach ( $params as $key => $value ) {
            if ( $customFieldInfo = CRM_Core_BAO_CustomField::getKeyID( $key, true ) ) {
                CRM_Core_BAO_CustomField::formatCustomField( $customFieldInfo[0],
                                                             $customData,
                                                             $value,
                                                             $customFieldExtends,
                                                             $customFieldInfo[1],
                                                             $entityID,
                                                             $inline );
            }
        }

        if ( ! empty( $customFields ) ) {
            foreach ( $customFields as $k => $val ) {
                if ( ! CRM_Utils_Array::value( $k, $customData ) &&
                     in_array ( $val['html_type'],
                                array ('CheckBox','Multi-Select', 'Radio') ) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $k,
                                                                 $customData,
                                                                 '',
                                                                 $customFieldExtends,
                                                                 null,
                                                                 $entityID );
                }
            }
        }
        return $customData;
    }

}


