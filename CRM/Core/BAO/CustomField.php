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
            $params['column_name'] = strtolower( CRM_Utils_String::munge( $params['label'], '_', 32 ) );
        } else if ( isset($params['id']) ) {
            $params['column_name'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',
                                                                  $params['id'],
                                                                  'column_name' );
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
                
                foreach ($params['option_value'] as $k => $v) {
                    if (strlen(trim($v))) {
                        $optionValue                  =& new CRM_Core_DAO_OptionValue( );
                        $optionValue->option_group_id =  $optionGroup->id;
                        $optionValue->label           =  $params['option_label'][$k];
                        $optionValue->value           =  $v;
                        $optionValue->weight          =  $params['option_weight'][$k];
                        $optionValue->is_active       =  CRM_Utils_Array::value( $k, $params['option_status'], false );
                        $optionValue->save( );
                    }
                }
            }
        }

        // check for orphan option groups
        if ( $params['id'] && $params['option_group_id'] ) {
            CRM_Core_BAO_CustomField::fixOptionGroups( $params['id'], $params['option_group_id'] ) ;
        }
        // since we need to save option group id :)
        
        if ( !isset($params['attributes']) && strtolower( $params['html_type'] ) == 'textarea' ) {
            $params['attributes'] = 'rows=4, cols=60';
        }

        // process data params
        if ( is_array($params['date_parts']) ) {
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
        
        // add column / index for custom table
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            $dropIndex  = false;
            // drop the index if it existed (not the most efficient, but the logic is easy)
            if ( $customField->is_searchable ) {
                $dropIndex = true;
            }
            self::createField( $customField, 'modify', $dropIndex );
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
     * @param string      $contactType   Contact type
     * @param boolean     $showAll       If true returns all fields (includes disabled fields)
     *
     * @return array      $fields - an array of active custom fields.
     *
     * @access public
     * @static
     */
    public static function &getFields($contactType = 'Individual', $showAll = false, $inline = false ) 
    {
        $cacheKey = $inline ? "{$contactType}_1" : "{$contactType}_0";
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
                $cgTable = CRM_Core_DAO_CustomGroup::getTableName();

                $extends = '';
                if ( $contactType ) {
                    if ( in_array( $contactType, array( 'Individual', 'Household', 'Organization' ) ) ) {
                        $value = "'" . CRM_Utils_Type::escape($contactType, 'String') . "', 'Contact' ";
                    } else {
                        $value = "'" . CRM_Utils_Type::escape($contactType, 'String') . "'";
                    }
                    $extends = "AND   $cgTable.extends IN ( $value ) ";
                }

                $query ="SELECT $cfTable.id, $cfTable.label,
                            $cgTable.title,
                            $cfTable.data_type, $cfTable.html_type,
                            $cfTable.options_per_line,
                            $cgTable.extends, $cfTable.is_search_range,
                            $cgTable.extends_entity_column_value,
                            $cfTable.is_view
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
            
                // also get the permission stuff here
                require_once 'CRM/Core/Permission.php';
                $permissionClause = CRM_Core_Permission::customGroupClause( CRM_Core_Permission::VIEW,
                                                                            "{$cgTable}." );

                $query .= " $extends AND $permissionClause
                        ORDER BY $cgTable.weight, $cgTable.title,
                                 $cfTable.weight, $cfTable.label";
         
                $crmDAO =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
                $result = $crmDAO->getDatabaseResult();
        
                $fields = array( );
                while (($row = $result->fetchRow()) != null) {
                    $id = array_shift($row);
                    $fields[$id] = $row;
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
                                            'title'            => $values[0],
                                            'headerPattern'    => '/' . preg_quote($regexp, '/') . '/',
                                            'import'           => 1,
                                            'custom_field_id'  => $id,
                                            'options_per_line' => $values[4],
                                            'data_type'        => $values[2],
                                            'html_type'        => $values[3],
                                            'is_search_range'  => $values[6],
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
    public static function getKeyID($key) 
    {
        $match = array( );
        if (preg_match('/^custom_(\d+)$/', $key, $match)) {
            return $match[1];
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
            $element =& $qf->add( strtolower($field->html_type), $elementName, $label,
                                  $field->attributes,
                                  ( ( $useRequired && $field->is_required ) && ! $search ) );

            $uploadNames = $qf->get('uploadNames');
            if ( ! $uploadNames ) {
                $uploadNames = array( );
            }
            if ( ! in_array( $elementName, $uploadNames ) ) {
                $uploadNames[] = $elementName;
            }
            $qf->set( 'uploadNames', $uploadNames );
            $config =& CRM_Core_Config::singleton( );
            $qf->controller->fixUploadAction( $config->customFileUploadDir, $uploadNames );
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
            $element =& $qf->addWysiwyg( $elementName, $label, CRM_Core_DAO::$_nullArray );

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
    static function getDisplayValue( $value, $id, &$options, $contactID = null, $valueID = null ) 
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
            $display = $option[$value];
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
                if ( $key == 'CiviCRM_OP_OR' ) {
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
                $display = CRM_Utils_System::formatWikiURL( $value );
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
            
            $result = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            
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
                $fileID = CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray ); 
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
        if ( $customFields[$customFieldId]['is_view'] ) {
            return;
        }

        list( $tableName, $columnName, $groupID ) = self::getTableColumnGroup( $customFieldId );
        
        if ( ! $customValueId && $entityId ) {
            //get the entity table for the custom field
            require_once "CRM/Core/BAO/CustomQuery.php";
            $entityTable = CRM_Core_BAO_CustomQuery::$extendsMap[$customFieldExtend];

            $query = "
SELECT id 
  FROM $tableName
 WHERE entity_id={$entityId}";

            $customValueId = CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
        }

        //fix checkbox
        if ( $customFields[$customFieldId][3] == 'CheckBox' ) {
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
        
        if ( $customFields[$customFieldId][3] == 'Multi-Select' ) {
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
        if ( $customFields[$customFieldId][2] == 'Date' ) {
            if ( ! CRM_Utils_System::isNull( $value ) ) {
                //convert date to timestamp
                $time = array( 'H', 'i', 's' );
                foreach ( $time as $v ) {
                    if ( ! $value[$v] ) {
                        $value[$v] = '00';
                    }                    
                    $date = CRM_Utils_Date::format( $value );                    
                }
            }
            if ( ! $date ) {
                $date = '00000000000000';
            }
            $value = $date;
        }

        if ( $customFields[$customFieldId][2] == 'Float' || 
             $customFields[$customFieldId][2] == 'Money' || 
             $customFields[$customFieldId][2] == 'Int' ) {
            if ( !$value ) {
                $value = 0;  
            }          
        }
               
        if ( ($customFields[$customFieldId][2] == 'StateProvince' || 
              $customFields[$customFieldId][2] == 'Country') && empty($value) ) {
            return;
        }

        $fileId = null;

        if ( $customFields[$customFieldId][2] == 'File' ) {
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
 WHERE entity_id={$entityId}";
                $fileId = CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
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
       
        $customFormatted[$customFieldId] = array('id'              => $customValueId,
                                                 'value'           => $value,
                                                 'type'            => $customFields[$customFieldId][2],
                                                 'custom_field_id' => $customFieldId, 
                                                 'custom_group_id' => $groupID,
                                                 'class_name'      => $groupClassName,
                                                 'table_name'      => $tableName,
                                                 'column_name'     => $columnName,
                                                 'file_id'         => $fileId
                                                 );
        return $customFormatted;
    }

    static function &defaultCustomTableSchema( &$params ) {
        // add the id and extends_id
        $table = array( 'name'       => $params['name'],
                        'attributes' => "ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci",
                        'fields'     => array(
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
                                              ),
                        'indexes'    => array(
                                              array( 'unique'        => true,
                                                     'field_name_1'  => 'entity_id' )
                                              ),
                                                    
                        );
        return $table;
    }

    static function createField( $field, $operation, $dropIndex = false) {
        require_once 'CRM/Core/BAO/CustomValueTable.php';
        $params = array( 'table_name' => CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup',
                                                                      $field->custom_group_id,
                                                                      'table_name' ),
                         'operation'  => $operation,
                         'name'       => $field->column_name,
                         'type'       => CRM_Core_BAO_CustomValueTable::fieldToSQLType( $field->data_type ),
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
        CRM_Core_BAO_SchemaHandler::alterFieldSQL( $params, $dropIndex );
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
            $dao = CRM_Core_DAO::executeQuery( $query, 
                                               CRM_Core_DAO::$_nullArray );
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
        $currentOptionGroupId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',$customFieldId, 'option_group_id' );
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
        
        $count = CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );

        if ( $count < 2 ) {
            //delete the option group
            require_once "CRM/Core/BAO/OptionGroup.php";
            CRM_Core_BAO_OptionGroup::del( $optionGroupId );
        }
    }
}


