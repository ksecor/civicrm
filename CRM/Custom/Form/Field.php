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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/ShowHideBlocks.php';

/**
 * form to process actions on the field aspect of Custom
 */
class CRM_Custom_Form_Field extends CRM_Core_Form 
{

    /**
     * Constants for number of options for data types of multiple option.
     */
    const NUM_OPTION = 11;


    /**
     * the custom group id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_gid;

    /**
     * The field id, used when editing the field
     *
     * @var int
     * @access protected
     */
    protected $_id;

    /**
     * The default custom data/input types, when editing the field
     *
     * @var array
     * @access protected
     */
    protected $_defaultDataType;
    
    /**
     * Array for valid combinations of data_type & html_type
     *
     * @var array
     * @static
     */
    private static $_dataTypeValues = null;
    private static $_dataTypeKeys = null;
    
    private static $_dataToHTML = array(
            array(  'Text' => 'Text', 'Select' => 'Select', 
                    'Radio' => 'Radio', 'CheckBox' => 'CheckBox', 'Multi-Select' => 'Multi-Select'),
            array('Text' => 'Text', 'Select' => 'Select', 'Radio' => 'Radio'),
            array('Text' => 'Text', 'Select' => 'Select', 'Radio' => 'Radio'),
            array('Text' => 'Text', 'Select' => 'Select', 'Radio' => 'Radio'),
            array('TextArea' => 'TextArea', 'RichTextEditor' => 'RichTextEditor'),
            array('Date'  => 'Select Date'),
            array('Radio' => 'Radio'),
            array('StateProvince' => 'Select State/Province' , 'Multi-Select' => 'Multi-Select State/Province'),
            array('Country' => 'Select Country', 'Multi-Select' => 'Multi-Select Country'),
            array('File' => 'File'),
            array('Link' => 'Link')
    );
    
    private static $_dataToLabels = null;


    /**
     * Function to set variables up before form is built
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function preProcess()
    {
        require_once 'CRM/Core/BAO/CustomField.php';
        if (!(self::$_dataTypeKeys)) {
            self::$_dataTypeKeys   = array_keys  (CRM_Core_BAO_CustomField::dataType());
            self::$_dataTypeValues = array_values(CRM_Core_BAO_CustomField::dataType());
        }

        $this->_gid = CRM_Utils_Request::retrieve('gid', 'Positive',
                                                  $this);
        $this->_id  = CRM_Utils_Request::retrieve('id' , 'Positive',
                                                  $this);

        if (self::$_dataToLabels == null) {
            self::$_dataToLabels 
                = array(
                        array('Text' => ts('Text'), 'Select' => ts('Select'), 
                              'Radio' => ts('Radio'), 'CheckBox' => ts('CheckBox'), 'Multi-Select' => ts('Multi-Select')),
                        array('Text' => ts('Text'), 'Select' => ts('Select'), 
                              'Radio' => ts('Radio')),
                        array('Text' => ts('Text'), 'Select' => ts('Select'), 
                              'Radio' => ts('Radio')),
                        array('Text' => ts('Text'), 'Select' => ts('Select'), 
                              'Radio' => ts('Radio')),
                        array('TextArea' => ts('TextArea'), 'RichTextEditor' => 'RichTextEditor'),
                        array('Date' => ts('Select Date')),
                        array('Radio' => ts('Radio')),
                        array('StateProvince' => ts('Select State/Province'),'Multi-Select' => ts('Multi-Select State/Province')),
                        array('Country' => ts('Select Country'),'Multi-Select' => ts('Multi-Select Country ')),
                        array('File' => ts('Select File')),
                        array('Link' => ts ('Link'))
                        );
        }
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @param null
     * 
     * @return array    array of default values
     * @access public
     */
    function setDefaultValues()
    {
        $defaults = array();
       
        // is it an edit operation ?
        if ( isset( $this->_id ) ) {
            $params = array('id' => $this->_id);
            $this->assign('id',$this->_id);
            CRM_Core_BAO_CustomField::retrieve($params, $defaults);
        
            $this->_gid = $defaults['custom_group_id'];
	   
            if ( $defaults['data_type'] == 'StateProvince' ) {
                require_once 'CRM/Core/DAO/StateProvince.php';
                $daoState =& new CRM_Core_DAO_StateProvince();
                $stateId = CRM_Utils_Array::value( 'default_value', $defaults );
                $daoState->id = $stateId;
                if ( $daoState->find( true ) ) {
                    $defaults['default_value'] = $daoState->name;
                }
            } else if ( $defaults['data_type'] == 'Country' ) {
                require_once 'CRM/Core/DAO/Country.php';
                $daoCountry =& new CRM_Core_DAO_Country();
                $countryId = CRM_Utils_Array::value( 'default_value', $defaults );
                $daoCountry->id = $countryId;
                if ( $daoCountry->find( true ) ) {
                    $defaults['default_value'] = $daoCountry->name;
                }
            }

            if ( CRM_Utils_Array::value( 'data_type', $defaults ) ) {
                $defaults['data_type'] = array( '0' => array_search( $defaults['data_type'],
                                                                     self::$_dataTypeKeys ), 
                                                '1' => $defaults['html_type'] );
                $this->_defaultDataType = $defaults['data_type'];
            }
            
            $date_parts = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,
                                   $defaults['date_parts'] );
            $temp_date_parts = array();
            if (is_array( $date_parts )) {
                foreach($date_parts as $v  ) {
                    if ( $v == 'H') {
                        $temp_date_parts['h'] = 1;
                    } else {
                        $temp_date_parts[$v] = 1;
                    }
                }
                $defaults['date_parts'] = $temp_date_parts;
            }

            $defaults['option_type'] = 2;
        } else {
            $defaults['option_type'] = 1;
        }

        // set defaults for weight and active
        $defaults['is_active'] = 1;
        for($i=1; $i<=self::NUM_OPTION; $i++) {
            $defaults['option_status['.$i.']'] = 1;
            $defaults['option_weight['.$i.']'] = $i;
        }

        if ($this->_action & CRM_Core_Action::ADD) {
            $fieldValues              = array('custom_group_id' => $this->_gid);
            $defaults['weight']       = CRM_Utils_Weight::getDefaultWeight('CRM_Core_DAO_CustomField', $fieldValues);

            $defaults['date_parts']   = array('d' => 1,'M' => 1,'Y' => 1); 
            $defaults['note_columns'] = 60;
            $defaults['note_rows']    = 4;

            $defaults['is_view'] = 0;
        }
        
        return $defaults;
    }

    /**
     * Function to actually build the form
     *
     * @param null
     * 
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        // lets trim all the whitespace
        $this->applyFilter('__ALL__', 'trim');

        // label
        $this->add( 'text',
                    'label',
                    ts('Field Label'),
                    CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'label'),
                    true );
        
        $dt =& self::$_dataTypeValues;
        $it = array();
        foreach ($dt as $key => $value) {
            $it[$key] = self::$_dataToLabels[$key];
        }
        $sel =& $this->addElement('hierselect',
                                  'data_type',
                                  ts('Data and Input Field Type'), 
                                  'onclick="clearSearchBoxes();custom_option_html_type(this.form)"; onBlur="custom_option_html_type(this.form)";', 
                                  '&nbsp;&nbsp;&nbsp;' );
        $sel->setOptions(array($dt, $it));
        
        if ($this->_action == CRM_Core_Action::UPDATE) {
            $this->freeze('data_type');
        }

        $optionGroups = CRM_Core_BAO_CustomField::customOptionGroup( );

        if ( empty( $optionGroups ) ) {
            $optionTypes = array( '1' => ts( 'Create a new set of options' ) );
            $extra = array( 'onclick' => "showOptionSelect();");
            $element =& $this->addRadio( 'option_type',
                                         ts('Option Type'),
                                         $optionTypes,
                                         $extra,
                                         '<br/>', false
                                         );
            $element->freeze( );
        } else {
            $optionTypes = array( '1' => ts( 'Create a new set of options' ),
                                  '2' => ts( 'Reuse an existing set' ) );
            $extra = array( 'onclick' => "showOptionSelect();");
            $this->addRadio( 'option_type',
                             ts('Multipe Choice Options'),
                             $optionTypes,
                             $extra,
                             '<br/>', false
                             );
            
            $this->add( 'select',
                        'option_group_id',
                        ts( 'Multiple Choice Option Sets' ),
                        array( '' => ts( '- select -' ) ) + $optionGroups );
        }

        // form fields of Custom Option rows
        $defaultOption = array();
        $_showHide =& new CRM_Core_ShowHideBlocks('','');
        for($i = 1; $i <= self::NUM_OPTION; $i++) {
            
            //the show hide blocks
            $showBlocks = 'optionField_'.$i;
            if ($i > 2) {
                $_showHide->addHide($showBlocks);
                if ($i == self::NUM_OPTION)
                    $_showHide->addHide('additionalOption');
            } else {
                $_showHide->addShow($showBlocks);
            }
            // label
            $this->add('text','option_label['.$i.']', ts('Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_OptionValue', 'label'));

            // value
            $this->add('text', 'option_value['.$i.']', ts('Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_OptionValue', 'value'));

            // Below rule is uncommented for CRM-1313
            $this->addRule('option_value['.$i.']', ts('Please enter a valid value for this field. You may use a - z, A - Z, 1 - 9, spaces and underline ( _ ) characters.'), 'qfVariable');
            // weight
            $this->add('text', "option_weight[$i]", ts('Order'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_OptionValue', 'weight'));

            // is active ?
            $this->add('checkbox', "option_status[$i]", ts('Active?'));
            
            $defaultOption[$i] = $this->createElement('radio', null, null, null, $i);

            //for checkbox handling of default option
            $this->add('checkbox', "default_checkbox_option[$i]", null);
        }

        //default option selection
        $this->addGroup($defaultOption, 'default_option');
        
        $_showHide->addToTemplate();                

        $this->add('text', 
                   'start_date_years',
                   ts('Dates may be up to'),
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'start_date_years'),
                   false);
        $this->add('text',
                   'end_date_years',
                   ts('Dates may be up to'),
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'end_date_years'),
                   false);
        
        $this->addRule('start_date_years', ts('Value should be a positive number') , 'integer');
        $this->addRule('end_date_years', ts('Value should be a positive number') , 'integer');

        $includedPart[] = $this->createElement('checkbox', 'M',true,ts('Month'));
        $includedPart[] = $this->createElement('checkbox', 'd',true,ts('Day'));
        $includedPart[] = $this->createElement('checkbox', 'Y',true,ts('Year'));
        $includedPart[] = $this->createElement('checkbox', 'h',true,ts('Hour'));
        $includedPart[] = $this->createElement('checkbox', 'i',true,ts('min'));
        $includedPart[] = $this->createElement('checkbox', 'A',true,ts('AM/PM'));

        $this->addGroup($includedPart, 'date_parts',ts('Included date parts'));
        
        // for Note field
        $this->add('text',
                   'note_columns',
                   ts('Width (columns)') . ' ',
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'note_columns'),
                   false);
        $this->add('text',
                   'note_rows',
                   ts('Height (rows)') . ' ',
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'note_rows'),
                   false);
        
        $this->addRule('note_columns', ts('Value should be a positive number') , 'positiveInteger');
        $this->addRule('note_rows', ts('Value should be a positive number') , 'positiveInteger');

        // weight
        $this->add('text', 'weight', ts('Order'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'weight'), true);
        $this->addRule('weight', ts('is a numeric field') , 'numeric');
        
        // is required ?
        $this->add('checkbox', 'is_required', ts('Required?') );

        // checkbox / radio options per line
        $this->add('text', 'options_per_line', ts('Options Per Line'));
        $this->addRule('options_per_line', ts('must be a numeric value') , 'numeric');

        // default value, help pre, help post, mask, attributes, javascript ?
        $this->add('text', 'default_value', ts('Default Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'default_value'));
        $this->add('textarea', 'help_post', ts('Field Help'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'help_post'));        
        $this->add('text', 'mask', ts('Mask'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'mask'));        

        // is active ?
        $this->add('checkbox', 'is_active', ts('Active?'));

        // is active ?
        $this->add('checkbox', 'is_view', ts('View Only?'));

        // is searchable ?
        $this->addElement('checkbox', 'is_searchable', ts('Is this Field Searchable?'), null, array('onclick' =>"showSearchRange(this)"));

        // is searchable by range?
        $searchRange = array( );
        $searchRange[] = $this->createElement( 'radio', null, null, ts( 'Yes' )    , '1'     );
        $searchRange[] = $this->createElement( 'radio', null, null, ts( 'No' ), '0' );
        
        $this->addGroup( $searchRange, 'is_search_range', ts( 'Search by Range?' ));
        
        // add buttons
        $this->addButtons(array(
                                array ('type'      => 'next',
                                       'name'      => ts('Save'),
                                       'isDefault' => true),
                                array ('type'      => 'next',
                                       'name'      => ts('Save and New'),
                                       'subName'   => 'new' ),
                                array ('type'      => 'cancel',
                                       'name'      => ts('Cancel')),
                                )
                          );

        // add a form rule to check default value
        $this->addFormRule( array( 'CRM_Custom_Form_Field', 'formRule' ), $this );

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $url = CRM_Utils_System::url( 'civicrm/admin/custom/group/field', 'reset=1&action=browse&gid=' . $this->_gid );
            $this->addElement( 'button',
                               'done',
                               ts('Done'),
                               array( 'onclick' => "location.href='$url'" ) );
        }
    }
    
    /**
     * global validation rules for the form
     *
     * @param array  $fields   (referance) posted values of the form
     *
     * @return array    if errors then list of errors to be posted back to the form,
     *                  true otherwise
     * @static
     * @access public
     */
    static function formRule( &$fields, &$files, &$self ) 
    {
        $default = CRM_Utils_Array::value( 'default_value', $fields );
        
        $errors  = array( );

        // ensure that the label is not 'id'
        if ( strtolower($fields['label']) == 'id' ) {
            $errors['label'] = ts( "You can not use 'id' as a field label." );
        }
        
        $customField = new CRM_Core_DAO_CustomField( );
        $customField->custom_group_id = $self->_gid;
        $customField->label           = $fields['label'];
        
        $dupeLabel = false;
        if ( $customField->find( true ) ) {
            $dupeLabel = true;
        }
        
        if ( isset( $self->_id ) ) {
            $label = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',
                                                  $self->_id, 'label' );
            if ( $customField->id == $self->_id && $customField->custom_group_id == $self->_gid ) {
                $dupeLabel = ($label == $fields['label']) ? false : true;
            } else if ( !$dupeLabel ) {
                $dupeLabel = ($label == $fields['label']) ? true : false;
            }
        }
        
        if ( $dupeLabel ) {
            $errors['label'] = ts('Name already exists in Database.');
        }
        
        if (!isset($fields['data_type'][0]) || !$fields['data_type'][1]) {
            $errors['_qf_default'] = ts('Please enter valid - Data and Input Field Type.');
        }

        if ( $default ) {
            $dataType = self::$_dataTypeKeys[$fields['data_type'][0]];
            switch ( $dataType ) {
                
            case 'Int':
                if ( ! CRM_Utils_Rule::integer( $default ) ) {
                    $errors['default_value'] = ts( 'Please enter a valid integer as default value.' );
                }
                break;
                
            case 'Float':
                if ( ! CRM_Utils_Rule::numeric( $default ) ) {
                    $errors['default_value'] = ts( 'Please enter a valid number as default value.' );
                }
                break;
                
            case 'Money':
                if ( ! CRM_Utils_Rule::money( $default ) ) {
                    $errors['default_value'] = ts( 'Please enter a valid number value.' );
                }
                break;
                
            case 'Date':
                if ( ! CRM_Utils_Rule::date( $default ) ) {
                    $errors['default_value'] = ts ( 'Please enter a valid date as default value using YYYY-MM-DD format. Example: 2004-12-31.' );
                }
                break;
                
            case 'Boolean':
                if ( $default != '1' && $default != '0' ) {
                    $errors['default_value'] = ts( 'Please enter 1 (for Yes) or 0 (for No) if you want to set a default value.' );
                }
                break;
                
            case 'Country':
                if( !empty($default) ) {
                    $fieldCountry = addslashes( $fields['default_value'] );
                    $query = "SELECT count(*) FROM civicrm_country WHERE name = '$fieldCountry' OR iso_code = '$fieldCountry'";
                    if ( CRM_Core_DAO::singleValueQuery( $query,CRM_Core_DAO::$_nullArray ) <= 0 ) {
                        $errors['default_value'] = ts( 'Invalid default value for country.' );
                    }
                }
                break;

            case 'StateProvince':
                if( !empty($default) ) {
                    $fieldStateProvince = addslashes( $fields['default_value'] );
                    $query = "
SELECT count(*) 
  FROM civicrm_state_province
 WHERE name = '$fieldStateProvince'
    OR abbreviation = '$fieldStateProvince'";
                    if ( CRM_Core_DAO::singleValueQuery( $query,CRM_Core_DAO::$_nullArray ) <= 0 ) {
                        $errors['default_value'] = ts( 'The invalid default value for State/Province data type' );
                    }
                }
                break;
                
            }
        } 

        /**
         * check that date parts is valid
         */
        if ( self::$_dataTypeKeys[$fields['data_type'][0]] == 'Date' ) {
            if ( ! isset( $fields['date_parts']['Y'] ) ) {
                $errors['date_parts'] = ts( 'You must have a year selected for a custom date' );
            } else {
                $orderElements = array( 'M', 'd', 'h', 'i', 'A' );
                $error    = false;
                $okToHave = true;
                foreach ( $orderElements as $order ) {
                    if ( isset( $fields['date_parts'][$order] ) ) {
                        if ( ! $okToHave ) {
                            $error = true;
                        }
                    } else {
                        $okToHave = false;
                    }
                }
                if ( $error ) {
                    $errors['date_parts'] = ts( 'The combination selected does not make a valid date' );
                }
            }
        }

        /** Check the option values entered
         *  Appropriate values are required for the selected datatype
         *  Incomplete row checking is also required.
         */
        $_flagOption = $_rowError = 0;
        $_showHide =& new CRM_Core_ShowHideBlocks('','');
        $dataType = self::$_dataTypeKeys[$fields['data_type'][0]];
        $dataField = $fields['data_type'][1];
        $optionFields = array('Select', 'Multi-Select', 'CheckBox', 'Radio');
        
        if ( $fields['option_type'] == 1 ) {
            //capture duplicate Custom option values
            if ( ! empty($fields['option_value']) ) {
                $countValue = count($fields['option_value']);
                $uniqueCount = count(array_unique($fields['option_value']));
                    
                if ( $countValue > $uniqueCount) {
                        
                    $start=1;
                    while ($start < self::NUM_OPTION) { 
                        $nextIndex = $start + 1;
                            
                        while ($nextIndex <= self::NUM_OPTION) {
                            
                            if ( $fields['option_value'][$start] == $fields['option_value'][$nextIndex] &&
                                 !empty($fields['option_value'][$nextIndex]) ) {

                                $errors['option_value['.$start.']']     = ts( 'Duplicate Option values' );
                                $errors['option_value['.$nextIndex.']'] = ts( 'Duplicate Option values' );
                                $_flagOption = 1;
                            }
                            $nextIndex++;
                        }
                        $start++;
                    }
                }
            }
            
            //capture duplicate Custom Option label
            if ( ! empty( $fields['option_label'] ) ) {
                $countValue = count($fields['option_label']);
                $uniqueCount = count(array_unique($fields['option_label']));
                
                if ( $countValue > $uniqueCount) {
                    
                    $start=1;
                    while ($start < self::NUM_OPTION) { 
                        $nextIndex = $start + 1;
                        
                        while ($nextIndex <= self::NUM_OPTION) {
                            
                            if ( $fields['option_label'][$start] == $fields['option_label'][$nextIndex] && !empty($fields['option_label'][$nextIndex]) ) {
                                
                                $errors['option_label['.$start.']']     =  ts( 'Duplicate Option label' );
                                $errors['option_label['.$nextIndex.']'] = ts( 'Duplicate Option label' );
                                $_flagOption = 1;
                            }
                            $nextIndex++;
                        }
                        $start++;
                    }
                }
            }

            for($i=1; $i<= self::NUM_OPTION; $i++) {
                if (!$fields['option_label'][$i]) {
                    if ($fields['option_value'][$i]) {
                        $errors['option_label['.$i.']'] = ts( 'Option label cannot be empty' );
                        $_flagOption = 1;
                    } else {
                        $_emptyRow = 1;
                    }
                } else {
                    if (!strlen(trim($fields['option_value'][$i]))) {
                        if (!$fields['option_value'][$i]) {
                            $errors['option_value['.$i.']'] = ts( 'Option value cannot be empty' );
                            $_flagOption = 1;
                        }
                    }
                }
               
                if ($fields['option_value'][$i] && $dataType != 'String') {
                    if ( $dataType == 'Int') {
                        if ( ! CRM_Utils_Rule::integer( $fields['option_value'][$i] ) ) {
                            $_flagOption = 1;
                            $errors['option_value['.$i.']'] = ts( 'Please enter a valid integer.' );
                        }
                    } else  if ( $dataType == 'Money') {
                        if ( ! CRM_Utils_Rule::money( $fields['option_value'][$i] ) ) {
                            $_flagOption = 1;
                            $errors['option_value['.$i.']'] = ts( 'Please enter a valid money value.' );
                            
                        }
                    } else {
                        if ( ! CRM_Utils_Rule::numeric( $fields['option_value'][$i] ) ) {
                            $_flagOption = 1;
                            $errors['option_value['.$i.']'] = ts( 'Please enter a valid number.' );
                        }
                    }
                }
                
                $showBlocks = 'optionField_'.$i;
                if ($_flagOption) {
                    $_showHide->addShow($showBlocks);
                    $_rowError = 1;
                } 
                
                if ($_emptyRow) {
                    $_showHide->addHide($showBlocks);
                } else {
                    $_showHide->addShow($showBlocks);
                }
                if ($i == self::NUM_OPTION) {
                    $hideBlock = 'additionalOption';
                    $_showHide->addHide($hideBlock);
                }
                
                $_flagOption = $_emptyRow = 0;
            }
        } elseif ( in_array( $dataField, $optionFields ) &&
                   $dataType != 'Boolean' ) {
            if ( ! $fields['option_group_id'] ) {
                $errors['option_group_id'] = ts( 'You must select a Multiple Choice Option set if you chose Reuse an existing set.' );
            } else {
                $query = "
SELECT count(*)
FROM   civicrm_custom_field
WHERE  data_type != %1
AND    option_group_id = %2";
                $params = array( 1 => array( self::$_dataTypeKeys[$fields['data_type'][0]],
                                             'String' ),
                                 2 => array( $fields['option_group_id'], 'Integer' ) );
                $count = CRM_Core_DAO::singleValueQuery( $query, $params );
                if ( $count > 0 ) {
                    $errors['option_group_id'] = ts( 'The data type of the multiple choice option set you\'ve selected does not match the data type assigned to this field.' );
                }

            }
        }
            
        if ($_rowError) {
            $_showHide->addToTemplate();
            CRM_Core_Page::assign('optionRowError', $_rowError);
        } else {
            switch (self::$_dataToHTML[$fields['data_type'][0]][$fields['data_type'][1]]) {
            case 'Radio':
                $_fieldError = 1;
                CRM_Core_Page::assign('fieldError', $_fieldError);
                break; 
                
            case 'Checkbox':
                $_fieldError = 1;
                CRM_Core_Page::assign('fieldError', $_fieldError);
                break; 

            case 'Select':
                $_fieldError = 1;
                CRM_Core_Page::assign('fieldError', $_fieldError);
                break;
            default:
                $_fieldError = 0;
                CRM_Core_Page::assign('fieldError', $_fieldError);
            }
                
            for ($idx=1; $idx<= self::NUM_OPTION; $idx++) {
                $showBlocks = 'optionField_'.$idx;
                if (!empty($fields['option_label'][$idx])) {
                    $_showHide->addShow($showBlocks);
                } else {
                    $_showHide->addHide($showBlocks);
                }
            }
            $_showHide->addToTemplate();
        }
        
        //checks the given custom field name doesnot start with digit
        $title = $fields['label']; 
        if ( ! empty( $title ) ) {
            $asciiValue = ord($title{0});//gives the ascii value
            if($asciiValue>=48 && $asciiValue<=57) {
                $errors['label'] = ts("Field's Name should not start with digit");
            } 
        }

        return empty($errors) ? true : $errors;
    }
    
    /**
     * Process the form
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function postProcess()
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        if ($this->_action == CRM_Core_Action::UPDATE) {
            $params['data_type'] = self::$_dataTypeKeys[$this->_defaultDataType[0]];
            $params['html_type'] = $this->_defaultDataType[1];
        } else {
            $params['html_type']     = $params['data_type'][1];
            $params['data_type']     = self::$_dataTypeKeys[$params['data_type'][0]];
        }
        
        // fix for CRM-316
        if ($this->_action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            $fieldValues = array( 'custom_group_id' => $this->_gid );
            if ( $this->_id ) {
                $oldWeight = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField', $this->_id, 'weight', 'id' );
            }
            $params['weight'] = 
                CRM_Utils_Weight::updateOtherWeights( 'CRM_Core_DAO_CustomField', $oldWeight, $params['weight'], $fieldValues );
        }

        //store the primary key for State/Province or Country as default value.
        if ( strlen(trim($params['default_value']))) {
            switch (self::$_dataTypeKeys[$params['option_type']]) {
            case 'StateProvince':
                $fieldStateProvince = strtolower($params['default_value']);
                $query = "
SELECT id
  FROM civicrm_state_province 
 WHERE LOWER(name) = '$fieldStateProvince' 
    OR abbreviation = '$fieldStateProvince'";
                $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
                if ( $dao->fetch() ) {
                    $params['default_value'] = $dao->id;
                }
                break;
                
            case 'Country':                
                $fieldCountry = strtolower($params['default_value']);
                $query = "
SELECT id
  FROM civicrm_country
 WHERE LOWER(name) = '$fieldCountry' 
    OR iso_code = '$fieldCountry'";
                $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
                if ( $dao->fetch() ) {
                    $params['default_value'] = $dao->id;
                }
                break;
            }
        }
        
        // for 'is_search_range' field. 
        if ( in_array( $params['option_type'], array( 1, 2, 3, 5 ) ) ) {
            if ( ! $params['is_searchable'] ) {
                $params['is_search_range'] = 0;
            }
        } else {
            $params['is_search_range'] = 0;
        }

        if ( !isset ( $params['date_parts']['A'] ) && isset ( $params['date_parts']['h'] ) ) {
            unset( $params['date_parts']['h'] );
            unset( $params['date_parts']['i'] );
            $params['date_parts']['H'] = 1;
            $params['date_parts']['i'] = 1;
        }
        
        // need the FKEY - custom group id
        $params['custom_group_id'] = $this->_gid;
        
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $params['id'] = $this->_id;
        }

        $customField = CRM_Core_BAO_CustomField::create( $params );
        
        // reset the cache
        require_once 'CRM/Core/BAO/Cache.php';
        CRM_Core_BAO_Cache::deleteGroup( 'contact fields' );

        CRM_Core_Session::setStatus(ts('Your custom field \'%1\' has been saved.', array(1 => $customField->label)));

        $buttonName = $this->controller->getButtonName( );
        $session =& CRM_Core_Session::singleton( );
        if ( $buttonName == $this->getButtonName( 'next', 'new' ) ) {
            CRM_Core_Session::setStatus(ts(' You can add another custom field.'));
            $session->replaceUserContext(CRM_Utils_System::url('civicrm/admin/custom/group/field', 'reset=1&action=add&gid=' . $this->_gid));
        }
    }
}


