<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * form to process actions on the field aspect of Custom
 */
class CRM_Custom_Form_Field extends CRM_Core_Form {

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
     * Array for valid combinations of data_type & html_type
     *
     * @var array
     * @static
     */
    private static $_dataTypeValues = null;
    private static $_dataTypeKeys = null;
    
    private static $_dataToHTML = array(
                                        array('Text', 'Select', 'Radio', 'Checkbox'),
                                        array('Text', 'Select', 'Radio'),
                                        array('Text', 'Select', 'Radio'),
                                        array('Text', 'Select', 'Radio'),
                                        array('TextArea'),
                                        array('Select Date'),
                                        array('Radio'),
                                        array('Select State/Province'),
                                        array('Select Country'),
                                        );
    

    /**
     * Function to set variables up before form is built
     *
     * @param none
     * @return void
     * @access public
     */
    public function preProcess()
    {
        if (!(self::$_dataTypeKeys)) {
            self::$_dataTypeKeys   = array_keys  (CRM_Core_BAO_CustomField::dataType());
            self::$_dataTypeValues = array_values(CRM_Core_BAO_CustomField::dataType());
        }

        $this->_gid = CRM_Utils_Request::retrieve('gid', $this);
        $this->_id  = CRM_Utils_Request::retrieve('id' , $this);
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @param none
     * @access public
     * @return None
     */
    function setDefaultValues()
    {
        $defaults = array();
       
        // is it an edit operation ?
        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            $this->assign('id',$this->_id);
            CRM_Core_BAO_CustomField::retrieve($params, $defaults);
            $this->_gid = $defaults['custom_group_id'];

            if (CRM_Utils_Array::value('data_type', $defaults)) {
                $defaults['data_type'] = array('0' => array_search($defaults['data_type'], self::$_dataTypeKeys));
            }

            if (CRM_Utils_Array::value('data_type', $defaults)) {
                $this->_rebuildHTMLType($defaults['data_type'][0]);
                $this->assign('html_type',$defaults['html_type']);
                $defaults['html_type'] = array('0' => array_search($defaults['html_type'], self::$_dataToHTML[$defaults['data_type'][0]]));
            }

        } else {
            $defaults['is_active'] = 1;
            for($i=1; $i<=self::NUM_OPTION; $i++) {
                $defaults['option_status['.$i.']'] = 1;
            }
        }
        return $defaults;
    }


    private function _rebuildHTMLType($dataType)
    {
        $this->removeElement('html_type');
        $this->addElement('select', 'html_type', ts('HTML Type'), self::$_dataToHTML[$dataType], array('onchange' => 'custom_option_html_type(this)'));
        $this->freeze('html_type');
    }


    
    /**
     * Function to actually build the form
     *
     * @param none
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        // lets trim all the whitespace
        $this->applyFilter('__ALL__', 'trim');

        // label
        $this->add('text', 'label', ts('Field Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'label'), true);
        $this->addRule('label', ts('Please enter a valid label for this field.'), 'title');

        // data_type, html_type
        $this->addElement('select', 'data_type', ts('Data Type'), self::$_dataTypeValues, array('onchange' => 'custom_option_data_type(this)'));
        $this->addElement('select', 'html_type', ts('Input Field Type'), self::$_dataToHTML[0], array('onchange' => 'custom_option_html_type(this)'));

        // need to freeze data_type, html_type for edit mode
        if ($this->_action == CRM_Core_Action::UPDATE) { 
            $this->freeze(array('data_type', 'html_type'));
        }
        
        // form fields of Custom Option rows
        $defaultOption = array();
        //$_link = ""; 
        for($i = 1; $i <= self::NUM_OPTION; $i++) {
            /*for ($index = $i; $index <= self::NUM_OPTION; $index++) {
                $_link .=  "hide('optionField[$index]'); hide('optionField[$index][show]'); ";
            }*/
                
            //$hideLink = $_link . "show('optionField[$i][show]'); return false;";

            //CRM_Core_ShowHideBlocks::linksForArray($this, $i, self::NUM_OPTION, 'optionField', ts('Add option'), ts('Hide row'), 'table-row', $hideLink);
            
            //Reset the value for fresh next hide link cascade string
            //$_link = "";
            
            // label
            $this->add('text','option_label['.$i.']', ts('Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'label'));

            // value
            $this->add('text', 'option_value['.$i.']', ts('Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'value'));

            // weight
            $this->add('text', 'option_weight['.$i.']', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'weight'));

            // is active ?
            $this->add('checkbox', 'option_status['.$i.']', ts('Active?'));
            $defaultOption[$i] = $this->createElement('radio', null, null, null, $i);
        }
        
        //set the default show/hide blocks
        /*$_showHideBlocks =& new CRM_Core_ShowHideBlocks('','');
        for($count = 2; $count <= self::NUM_OPTION; $count++) {
            $showBlocks = 'optionField['.$count.']';
            $hideBlocks = 'optionField['.$count.'][show]';
            
            if ($count > 2) {
                $_showHideBlocks->addHide($showBlocks);
                $_showHideBlocks->addHide($hideBlocks);
            } else {
                $_showHideBlocks->addShow($showBlocks);
                $_showHideBlocks->addHide($hideBlocks);
            }
        }
        $_showHideBlocks->addTotemplate();*/
        
        //default option selection
        $tt =& $this->addGroup($defaultOption, 'default_option');
		
        // weight
        $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'weight'), true);
        $this->addRule('weight', ts(' is a numeric field') , 'numeric');
        
        // is required ?
        $this->add('checkbox', 'is_required', ts('Required?') );

        // default value, help pre, help post, mask, attributes, javascript ?
        $this->add('text', 'default_value', ts('Default Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'default_value'));
        $this->add('textarea', 'help_post', ts('Field Help'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'help_post'));        
        $this->add('text', 'mask', ts('Mask'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'mask'));        

        // is active ?
        $this->add('checkbox', 'is_active', ts('Active?'));

        // is searchable ?
        $this->add('checkbox', 'is_searchable', ts('Searchable?'));
        
        // add buttons
        $this->addButtons(array(
                                array ('type'      => 'next',
                                       'name'      => ts('Save'),
                                       'isDefault' => true),
                                array ('type'      => 'cancel',
                                       'name'      => ts('Cancel')),
                                )
                          );

        // add a form rule to check default value
        $this->addFormRule( array( 'CRM_Custom_Form_Field', 'formRule' ) );

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onClick' => "location.href='civicrm/admin/custom/group/field?reset=1&action=browse&gid=" . $this->_gid . "'"));
        }
    }
    
         
    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$fields ) {
        $default = CRM_Utils_Array::value( 'default_value', $fields );
        $errors  = array( );

        if ( $default ) {
            $dataType = self::$_dataTypeKeys[$fields['data_type']];
            switch ( $dataType ) {
            case 'Int':
                if ( ! CRM_Utils_Rule::integer( $default ) ) {
                    $errors['default_value'] = 'Please enter a valid integer as default value.';
                }
                break;

            case 'Float':
            case 'Money':
                if ( ! CRM_Utils_Rule::numeric( $default ) ) {
                    $errors['default_value'] = 'Please enter a valid number as default value.';
                }
                break;
                    
            case 'Date':
                if ( ! CRM_Utils_Rule::date( $default ) ) {
                    $errors['default_value'] = 'Please enter a valid date as default value using YYYY-MM-DD format. Example: 2004-12-31.';
                }
                break;
            }
        }
        
        /** Check the option values entered
         *  Appropriate values are required for the selected datatype
         *  Incomplete row checking is also required.
         */
        if (CRM_Core_Action::ADD) {

            $_flagOption = $_rowError = 0;
            $_showHide =& new CRM_Core_ShowHideBlocks('','');
            $dataType = self::$_dataTypeKeys[$fields['data_type']];

            for($i=1; $i<= self::NUM_OPTION; $i++) {
                if (!$fields['option_label'][$i]) {
                    if ($fields['option_value'][$i]) {
                        $errors['option_label['.$i.']'] = 'Option label cannot be empty';
                        $_flagOption = 1;
                    } else {
                        if ($fields['option_weight'][$i]) {
                            $errors['option_label['.$i.']'] = 'Option label cannot be empty';
                            $errors['option_value['.$i.']'] = 'Option value cannot be empty';
                            $_flagOption = 1;
                        } else {
                            //The row is empty
                            $_emptyRow = 1;
                        }
                    }
                } else {
                    if (!$fields['option_value'][$i]) {
                        $errors['option_value['.$i.']'] = 'Option value cannot be empty';
                            $_flagOption = 1;
                    }
                }
                if ($fields['option_value'][$i] && $dataType != 'String') {
                    if ( $dataType == 'Int') {
                        if ( ! CRM_Utils_Rule::integer( $fields['option_value'][$i] ) ) {
                            $_flagOption = 1;
                            $errors['option_value['.$i.']'] = 'Please enter a valid integer.';
                        }
                    } else {
                        if ( ! CRM_Utils_Rule::numeric( $fields['option_value'][$i] ) ) {
                            $_flagOption = 1;
                            $errors['option_value['.$i.']'] = 'Please enter a valid number.';
                        }
                    }
                }
                $showBlocks = 'optionField['.$i.']';
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
            
            if ($_rowError) {
                $_showHide->addToTemplate();
                CRM_CORE_Page::assign('optionRowError', $_rowError);
            } else {
                switch (self::$_dataToHTML[$fields['data_type']][$fields['html_type']]) {
                case 'Radio':
                    $_fieldError = 1;
                    CRM_CORE_Page::assign('fieldError', $_fieldError);
                    break; 
                
                case 'Checkbox':
                    $_fieldError = 1;
                    CRM_CORE_Page::assign('fieldError', $_fieldError);
                    break; 
                
                case 'Select':
                    $_fieldError = 1;
                    CRM_CORE_Page::assign('fieldError', $_fieldError);
                    break;
                default:
                    $_fieldError = 0;
                    CRM_CORE_Page::assign('fieldError', $_fieldError);
                }
            }
        }
        
        //Check for duplicate Field Label
        $fieldLabel = $fields['label'];
        $dao =& new CRM_Core_DAO();
        $query = "SELECT * FROM crm_custom_field WHERE label = '$fieldLabel'";
        $dao->query($query);
        
        $result = $dao->getDatabaseResult();
        $row    = $result->fetchRow();
        if ($row > 0)
            $errors['label'] = "There is a Custom Field with same name.";
            
        
        return empty($errors) ? true : $errors;
    }

    /**
     * Process the form
     *
     * @param none
     * @return void
     * @access public
     */
    public function postProcess()
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues('Field');

        // set values for custom field properties and save
        $customField                =& new CRM_Core_DAO_CustomField();
        $customField->label         = $params['label'];
        $customField->name          = CRM_Utils_String::titleToVar($params['label']);
        $customField->data_type     = self::$_dataTypeKeys[$params['data_type']];
        $customField->html_type     = self::$_dataToHTML[$params['data_type']][$params['html_type']];
        $customField->weight        = $params['weight'];
        $customField->default_value = $params['default_value'];
        $customField->help_post     = $params['help_post'];
        $customField->mask          = $params['mask'];
        $customField->is_required   = CRM_Utils_Array::value( 'is_required', $params, false );
        $customField->is_searchable   = CRM_Utils_Array::value( 'is_searchable', $params, false );
        $customField->is_active     = CRM_Utils_Array::value( 'is_active', $params, false );
        if ( strtolower( $customField->html_type ) == 'textarea' ) {
            $customField->attributes = 'rows=4, cols=80';
        }

        if ($this->_action & CRM_Core_Action::UPDATE) {
            $customField->id = $this->_id;
        }

        // need the FKEY - custom group id
        $customField->custom_group_id = $this->_gid;

        $customField->save();

        //Start Storing the values of Option field if the selected option is Multi Select
         if ($this->_action & CRM_Core_Action::ADD) {
             if($customField->data_type == 'String' || $customField->data_type == 'Int' || $customField->data_type == 'Float' || $customField->data_type == 'Money') {
                 if($customField->html_type != 'Text') {                
                     foreach ($params['option_value'] as $k => $v) {
                         if ($v) {
                             $customOptionDAO =& new CRM_Core_DAO_CustomOption();
                             $customOptionDAO->custom_field_id = $customField->id;
                             $customOptionDAO->label      = $params['option_label'][$k];
                             $customOptionDAO->value      = $v;
                             $customOptionDAO->weight     = $params['option_weight'][$k];
                             $customOptionDAO->is_active  = $params['option_status'][$k];
                             $customOptionDAO->save();
                         }
                     }
                     $customField->default_value = $params['option_value'][$params['default_option']];
                     $customField->save();
                 }
             }
         }
        CRM_Core_Session::setStatus(ts('Your custom field "%1" has been saved', array(1 => $customField->label)));
    }
}
?>
