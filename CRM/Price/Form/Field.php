<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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
 * form to process actions on the field aspect of Price
 */
class CRM_Price_Form_Field extends CRM_Core_Form {

    /**
     * Constants for number of options for data types of multiple option.
     */
    const NUM_OPTION = 11;


    /**
     * the custom set id saved to the session for an update
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
     * valid html types and descriptions
     *
     * @var array
     * @static
     */
    private static $_htmlTypes = null;

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
        require_once 'CRM/Core/BAO/PriceField.php';

        if ( ! self::$_htmlTypes ) {
            self::$_htmlTypes = array(
                'Text' => ts('Text'),
                'Select' => ts('Select'),
                'Radio' => ts('Radio'),
                'CheckBox' => ts('CheckBox')
            );
        }

        $this->_gid = CRM_Utils_Request::retrieve('gid', 'Positive', $this);
        $this->_id  = CRM_Utils_Request::retrieve('id' , 'Positive', $this);

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
        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            $this->assign('id',$this->_id);
            CRM_Core_BAO_PriceField::retrieve($params, $defaults);
            $this->_gid = $defaults['price_set_id'];

            // if text, retrieve price
            if ( $defaults['html_type'] == 'Text' ) {
                require_once 'CRM/Core/BAO/CustomOption.php';
                $optionParams = array(
                    'entity_table' => 'civicrm_price_field',
                    'entity_id' => $this->_id
                );
                $optionValues = array();
                CRM_Core_BAO_CustomOption::retrieve( $optionParams, $optionValues );
                $defaults['price'] = $optionValues['value'];
            }
        } else {
            $defaults['is_active'] = 1;
            for($i=1; $i<=self::NUM_OPTION; $i++) {
                $defaults['option_status['.$i.']'] = 1;
                $defaults['option_weight['.$i.']'] = $i;
            }
        }

        if ($this->_action & CRM_Core_Action::ADD) {
            require_once 'CRM/Utils/Weight.php';
            $fieldValues = array('price_set_id' => $this->_gid);
            $defaults['weight'] = CRM_Utils_Weight::getMax('CRM_Core_DAO_PriceField', $fieldValues);
            $defaults['is_display_amounts'] = 1;
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
        $this->add('text', 'label', ts('Field Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_PriceField', 'label'), true);
        $this->addRule( 'label', ts('Name already exists in Database.'), 
                        'objectExists', array( 'CRM_Core_DAO_PriceField', $this->_id, 'label' ) );

        // html_type
        //$sel = $this->add('select', 'html_type', ts('Input Field Type'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_PriceField', 'html_type'), true);
        $attributes = CRM_Core_DAO::getAttribute('CRM_Core_DAO_PriceField', 'html_type');
        $javascript = 'onChange="option_html_type(this.form)";';
        $sel = $this->add('select', 'html_type', ts('Input Field Type'), self::$_htmlTypes,  true, $javascript);

        // price (for text inputs)
        $this->add( 'text', 'price', ts('Price') );
        $this->addRule( 'price', ts(' must be a monetary value'), 'money' );

        require_once 'CRM/Core/BAO/CustomOption.php';
        $Options = CRM_Core_BAO_CustomOption::getCustomOption($this->_id);
        if ($this->_action == CRM_Core_Action::UPDATE) {
            $this->freeze('html_type');
        }

        // form fields of Custom Option rows
        $_showHide =& new CRM_Core_ShowHideBlocks('','');
        $labelAttribute = CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'label');
        $valueAttribute = CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'value');
        $weightAttribute = CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'weight');
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
            $this->add('text','option_label['.$i.']', ts('Label'), $labelAttribute);

            // value
            $this->add('text', 'option_value['.$i.']', ts('Value'), $valueAttribute);

            // Below rule is uncommented for CRM-1313
            $this->addRule('option_value['.$i.']', ts('Please enter a valid value for this field.'), 'qfVariable');
            
            // weight
            $this->add('text', 'option_weight['.$i.']', ts('Weight'), $weightAttribute);

            // is active ?
            $this->add('checkbox', 'option_status['.$i.']', ts('Active?'));
            
            //for checkbox handling of default option
            $this->add('checkbox', 'default_checkbox_option['.$i.']', null);
        }
        
        $_showHide->addToTemplate();                

        // is_enter_qty
        $this->add('checkbox', 'is_enter_qty', ts('Enter Quantity?') );

        // is_display_amounts
        $this->add('checkbox', 'is_display_amounts', ts('Display Amount?') );

        // weight
        $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_PriceField', 'weight'), true);
        $this->addRule('weight', ts(' is a numeric field') , 'numeric');

        // checkbox / radio options per line
        $this->add('text', 'options_per_line', ts('Options Per Line'));
        $this->addRule('options_per_line', ts(' must be a numeric value') , 'numeric');

        // help post, mask, attributes, javascript ?
        $this->add('textarea', 'help_post', ts('Field Help'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_PriceField', 'help_post'));        

        // active_on
        $date_options = array(
            //'format' => 'dmY His',
            'minYear' => date('Y'),
            'maxYear' => date('Y') + 5,
            'addEmptyOption' => true
        );
        $this->add('date', 'active_on', ts('Active On'), $date_options );

        // expire_on
        $this->add('date', 'expire_on', ts('Expire On'), $date_options );

        // is required ?
        $this->add('checkbox', 'is_required', ts('Required?') );

        // is active ?
        $this->add('checkbox', 'is_active', ts('Active?'));
        
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
        $this->addFormRule( array( 'CRM_Price_Form_Field', 'formRule' ) );

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $url = CRM_Utils_System::url( 'civicrm/admin/price/field', 'reset=1&action=browse&gid=' . $this->_gid );
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
    static function formRule( &$fields ) {
        // all option fields are of type "money"
        $errors = array( );

        /** Check the option values entered
         *  Appropriate values are required for the selected datatype
         *  Incomplete row checking is also required.
         */
        if (CRM_Core_Action::ADD) {

            $_showHide =& new CRM_Core_ShowHideBlocks('','');

            // do not process if no option rows were submitted
            if ( empty( $fields['option_value'] ) && empty( $fields['option_label'] ) ) {
                return true;
            }

            if ( empty( $fields['option_value'] ) ) {
                $fields['option_value'] = array( );
            }

            if ( empty( $fields['option_label'] ) ) {
                $fields['option_label'] = array( );
            }

            $dupeLabels = array();

            for ( $idx = 1; $idx <= self::NUM_OPTION; $idx++ ) {
                $_flagOption = 0;
                $_rowError = 0;

                $showBlocks = 'optionField_'.$i;

                // both value and label are empty
                if ( empty( $fields['option_value'][$idx] ) && empty( $fields['option_label'] ) ) {
                    $_showHide->addHide($showBlocks);
                    continue;
                }

                $_showHide->addShow($showBlocks);

                if ( ! empty( $fields['option_value'][$idx] ) ) {
                    // check for empty label
                    if ( empty( $fields['option_label'][$idx] ) ) {
                        $errors['option_label]['.$idx.']'] = ts( 'Option label cannot be empty' );
                    }
                    // all fields are money fields
                    if ( ! CRM_Utils_Rule::money( $fields['option_value'][$idx] ) ) {
                        $_flagOption = 1;
                        $errors['option_value['.$idx.']'] = ts( 'Please enter a valid money value.' );
                        
                    }
                }

                if ( ! empty( $fields['option_label'][$idx] ) ) {
                    // check for empty value
                    if ( empty( $fields['option_value'][$idx] ) ) {
                        $errors['option_value]['.$idx.']'] = ts( 'Option value cannot be empty' );
                    }
                    // check for duplicate labels, if not already done
                    if ( isset( $dupeLabels[$idx] ) ) {
                        continue;
                    }
                    $also_in = array_keys( $fields['option_label'], $fields['option_label'][$idx] );
                    // first match is always the current key
                    unset( $also_in[0] );
                    if ( !empty( $also_in ) ) {
                        $_flagOption = 1;
                        $errors['option_label]['.$idx.']'] = ts( 'Duplicate Option label' );
                        foreach ( $also_in as $also_in_key ) {
                            $errors['option_value]['.$also_in_key.']'] = ts( 'Duplicate Option label' );
                            $dupeValues[$also_in_key] = true;
                        }
                    }
                }
                
                if ($_flagOption) {
                    $_showHide->addShow($showBlocks);
                    $_rowError = 1;
                }

                // last row - hide "Additional Option" option
                if ($idx == self::NUM_OPTION) {
                    $hideBlock = 'additionalOption';
                    $_showHide->addHide($hideBlock);
                }
                
            }

            /* what do rowError and fieldError do?
            if ($_rowError) {
                $_showHide->addToTemplate();
                CRM_Core_Page::assign('optionRowError', $_rowError);
            } else {
                switch ($fields['html_type']) {
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
                
            }
             */
            $_showHide->addToTemplate();
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
        $params = $this->controller->exportValues('Field');
        // set values for custom field properties and save
        $priceField                =& new CRM_Core_DAO_PriceField();
        $priceField->label         = $params['label'];
        $priceField->name          = CRM_Utils_String::titleToVar($params['label']);
        $priceField->html_type     = $params['html_type'];
        
        require_once 'CRM/Utils/Weight.php';
        $fieldValues = array( 'price_set_id' => $this->_gid );
        if ($this->_action & CRM_Core_Action::UPDATE) {

            $pf =& new CRM_Core_DAO_PriceField();
            $pf->id = $this->_id;
            $pf->find();
            
            if ( $pf->fetch() && $pf->weight != $params['weight'] ) {
                $params['weight'] = CRM_Utils_Weight::updateOtherWeights( 'CRM_Core_DAO_PriceField', $pf->weight, $params['weight'], $fieldValues );
            }                
                        
        } else {

            $params['weight'] = CRM_Utils_Weight::addWeight( 'CRM_Core_DAO_PriceField', $params['weight'], $fieldValues );

        }

        $priceField->weight            = $params['weight'];             
        $priceField->help_post         = $params['help_post'];
        // if html type is Text, force is_enter_qty on
        if ( $priceField->html_type == 'Text' ) {
            $priceField->is_enter_qty = 1;
        } else {
            $priceField->is_enter_qty      = CRM_Utils_Array::value( 'is_enter_qty', $params, false );
        }
        $priceField->is_display_amounts = CRM_Utils_Array::value( 'is_display_amounts', $params, false );
        $priceField->is_required       = CRM_Utils_Array::value( 'is_required', $params, false );
        $priceField->is_active         = CRM_Utils_Array::value( 'is_active', $params, false );
        $priceField->options_per_line  = (int)$params['options_per_line'];
        $priceField->active_on         = CRM_Utils_Date::format( $params['active_on'] );
        $priceField->expire_on         = CRM_Utils_Date::format( $params['expire_on'] );
        $priceField->javascript        = $params['javascript'];
        
        if ($this->_action & CRM_Core_Action::UPDATE) {
            $priceField->id = $this->_id;
            // update price if it's a text field
            if ($priceField->html_type == 'Text') {
                $customOptionDAO =& new CRM_Core_DAO_CustomOption();
                $customOptionDAO->entity_table = 'civicrm_price_field';
                $customOptionDAO->entity_id = $this->_id;
                $customOptionDAO->weight = 1;
                $customOptionDAO->find(true);
                $customOptionDAO->value = $params['price'];
                $customOptionDAO->label = $params['label'];
                $customOptionDAO->save();
            }
        }

        // need the FKEY - custom group id
        $priceField->price_set_id = $this->_gid;
        
        $priceField->save();

        if ($this->_action & CRM_Core_Action::ADD) {

            if ( $priceField->html_type == 'Text' ) {
                $params['option_value'] = array( 1 => $params['price'] );
                $params['option_label'] = array( 1 => $params['label'] );
                $params['weight'] = array( 1 => 1 );
                $params['is_active'] = array( 1 => 1 );
            }

            foreach ( $params['option_value'] as $key => $value ) {
                if ( strlen( trim ( $value ) ) ) {
                    $customOptionDAO =& new CRM_Core_DAO_CustomOption();
                    $customOptionDAO->entity_id     = $priceField->id;
                    $customOptionDAO->entity_table  = 'civicrm_price_field';
                    $customOptionDAO->label         = $params['option_label'][$key];
                    $customOptionDAO->value         = $value;
                    $customOptionDAO->weight        = $params['option_weight'][$key];
                    $customOptionDAO->is_active     = $params['option_status'][$key];
                    $customOptionDAO->save();
                }
            }
        }
        CRM_Core_Session::setStatus(ts('Price field "%1" has been saved', array(1 => $priceField->label)));
    }
}
?>
