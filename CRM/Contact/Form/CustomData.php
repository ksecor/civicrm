<?php
/**
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
 * This class generates form components for custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Contact_Form_CustomData extends CRM_Core_Form
{
    /**
     * The table name, used when editing/creating custom data
     *
     * @var string
     */
    protected $_tableName;

    /**
     * The table id, used when editing/creating custom data
     *
     * @var int
     */
    protected $_tableId;
    
    /**
     * entity type of the table id
     *
     * @var string
     */
    protected $_entityType;

    /**
     * the group tree data
     *
     * @var array
     */
    protected $_groupTree;

    /**
     * what blocks should we show and hide.
     *
     * @var CRM_Core_ShowHideBlocks
     */
    protected $_showHide;

    /**
     * Array of the Group Titles.
     *
     * @var array
     */
    protected $_groupTitle;

    /**
     * pre processing work done here.
     *
     * gets session variables for table name, id of entity in table, type of entity and stores them.
     *
     * @param none
     * @return none
     *
     * @access public
     *
     */
    function preProcess()
    {
        $this->_tableName  = $this->get('tableName');
        $this->_tableId    = $this->get('tableId');
        $this->_entityType = $this->get('entityType');
        
        // gets all details of group tree for entity
        $this->_groupTree  = CRM_Core_BAO_CustomGroup::getTree($this->_entityType, $this->_tableId);
    }

    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param    array    array of Group Titles
     *
     * @return   
     *
     * @access   protected
     */
    
    protected function setShowHide(&$group)
    {
        $this->_showHide =& new CRM_Core_ShowHideBlocks('','');
        
        foreach ($group as $key => $title) {
            $showBlocks = $title . '[show]' ;
            $hideBlocks = $title;
            
            if ($key) {
                $this->_showHide->addShow($showBlocks);
                $this->_showHide->addHide($hideBlocks);
            } else {
                $this->_showHide->addShow($hideBlocks);
                $this->_showHide->addHide($showBlocks);
            }
        }
        $this->_showHide->addToTemplate();
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assign('groupTree', $this->_groupTree);
        
        // add the form elements
        foreach ($this->_groupTree as $group) {
            
            $this->_groupTitle[] = $group['title'];
            $_flag = 0;            
            CRM_Core_ShowHideBlocks::links( $this, $group['title'], '', '');
            
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
                $fieldId = $field['id'];                
                $elementName = $groupId . '_' . $fieldId . '_' . $field['name']; 

                // if custom data exists use it, else use the default value if it exists
                $elementData = isset($field['customValue']['data']) ? $field['customValue']['data'] : $field['default_value'];

                switch($field['html_type']) {

                case 'Text':
                case 'TextArea':
                    $element = $this->add(strtolower($field['html_type']), $elementName, $field['label'],
                                          $field['attributes'], $field['is_required']);
                    break;

                case 'Select Date':
                    $this->add('date', $elementName, $field['label'], CRM_Core_SelectValues::date( 'custom' ), $field['required']);
                    break;

                case 'Radio':
                    $choice = array();
                    if($field['data_type'] != 'Boolean') {
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id']);
                        foreach ($customOption as $v) {
                            $choice[] = $this->createElement('radio', null, '', $v['label'], $v['value'], $field['attributes']);
                        }
                        $this->addGroup($choice, $elementName, $field['label']);
                    } else {
                        $choice[] = $this->createElement('radio', null, '', ts('Yes'), 'yes', $field['attributes']);
                        $choice[] = $this->createElement('radio', null, '', ts('No') , 'no' , $field['attributes']);
                        $this->addGroup($choice, $elementName, $field['label']);
                    }
                    if ($field['is_required']) {
                        $this->addRule($elementName, ts('%1 is a required field.', array(1 => $field['label'])) , 'required');
                    }
                    break;

                case 'Select':
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id']);
                    $selectOption = array();
                    foreach ($customOption as $v) {
                        $selectOption[$v['value']] = $v['label'];
                    }
                    $this->add('select', $elementName, $field['label'], $selectOption);
                    break;

                case 'CheckBox':
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id']);
                    $check = array();
                    foreach ($customOption as $v) {
                        $checked = array();
                        $check[] = $this->createElement('checkbox', $v['value'], null, $v['label']);
                    }
                    $this->addGroup($check, $elementName, $field['label']);
                    break;

                case 'Select State/Province':
                    //Add State
                    
                    $stateOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince();
                    $this->add('select', $elementName, $field['label'], $stateOption);
                    $_flag++;
                    break;

                case 'Select Country':
                    //Add Country
                    $_flag++;
                    $countryOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::country();
                    $this->add('select', $elementName, $field['label'], $countryOption);
                    break;
                }
                
                switch ( $field['data_type'] ) {
                case 'Int':
                    // integers will have numeric rule applied to them.
                    $this->addRule($elementName, ts('%1 must be an integer (whole number).', array(1 => $field['label'])), 'integer');
                    break;

                case 'Date':
                    $this->addRule($elementName, ts('%1 is not a valid date.', array(1 => $field['label'])), 'qfDate');
                    break;

                case 'Float':
                case 'Money':
                    $this->addRule($elementName, ts('%1 must be a number (with or without decimal point).', array(1 => $field['label'])), 'numeric');
                    break;
                }
            }
        }

        $this->setShowHide($this->_groupTitle);

        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'isDefault' => true   ),
                                array ( 'type'      => 'reset',
                                        'name'      => ts('Reset')),
                                array ( 'type'       => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
        if ( $_flag == 2 ) {
            // add a form rule to check default value
            $this->addFormRule( array( 'CRM_Contact_Form_CustomData', 'formRule' ) );
        }

        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $this->freeze();
        }
    }
    
     /**
     * check for correct state / country mapping.
     *
     * @param array reference $fields - submitted form values.
     * @param array reference $errors - if any errors found add to this array. please.
     * @return true if no errors
     *         array of errors if any present.
     *
     * @access protected
     * @static
     */
    static protected function formRule(&$field)
    {
        if ($stateProvinceId && $countryId) {
            $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
            $stateProvinceDAO->id = $stateProvinceId;
            $stateProvinceDAO->find(true);
            
            if ($stateProvinceDAO->country_id != $countryId) {
                // countries mismatch hence display error
            $stateProvinces = CRM_Core_PseudoConstant::stateProvince();
            $countries = CRM_Core_PseudoConstant::country();
            $errors[$field[$elementName]] = "State/Province " . $stateProvinces[$stateProvinceId] . " is not part of ". $countries[$countryId] . ". It belongs to " . $countries[$stateProvinceDAO->country_id] . "." ;
            }
        }
        return empty($errors) ? true : $errors;
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues()
    {
        $defaults = array();
        
        foreach ($this->_groupTree as $group) {
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
 
                $fieldId = $field['id'];
                $elementName = $groupId . '_' . $fieldId . '_' . $field['name'];
                switch($field['html_type']) {
                case 'Radio':
                    if($field['data_type'] != 'Boolean' ) {
                        $defaults[$elementName] = isset($field['customValue']['data']) ? $field['customValue']['data'] : $field['default_value'];
                    } else {
                        if($field['default_value']) {
                            $defaults[$elementName] = $field['default_value'] ? 'yes' : 'no';
                        } else {
                            $defaults[$elementName] = isset($field['customValue']['data']) ? 'yes' : 'no';
                        }
                    }
                    break;
                    
                case 'Select':
                    $defaults[$elementName] = $field['customValue']['data'] ? $field['customValue']['data'] : $field['default_value'];
                    break;
                    
                case 'CheckBox':
                    if(isset($field['customValue']['data'])) {
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id']);
                        $customValues = CRM_Core_BAO_CustomOption::getCustomValues($field['id']);
                        $checkedData = explode(",", $field['customValue']['data']);
                        $defaults[$elementName] = array();
                        foreach($customOption as $val) {
                            if (is_array($customValues)) {
                                if (in_array($val['value'], $checkedData)) {
                                    $defaults[$elementName][$val['value']] = 1;
                                } else {
                                    $defaults[$elementName][$val['value']] = 0;
                                }
                            }
                        }
                    } /*else {
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id']);
                        $defaults[$elementName] = array();
                        foreach($customOption as $val) {
                            if( $field['default_value'] == $val['value']) {
                                $defaults[$elementName][$val['value']] = 1;
                            }
                        }                        
                    }*/
                    break;
                    
                case 'Select Date':
                    if ($date = $field['customValue']['data']) {
                        $defaults[$elementName] = CRM_Utils_Date::unformat( $date );
                    }
                    break;
                default:
                    $defaults[$elementName] = $field['customValue']['data'] ? $field['customValue']['data'] : $field['default_value'];
                } 
            }
        } 
        return $defaults;
    }
    
    /**
     * Process the user submitted custom data values.
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // first reset all checkbox and radio data
        foreach ($this->_groupTree as $group) {
            foreach ($group['fields'] as $field) {
                if ( $field['html_type'] == 'CheckBox' || $field['html_type'] == 'Radio' ) {
                    $this->_groupTree[$group['id']]['fields'][$field['id']]['customValue']['data'] = 'NULL';
                }
            }
        }

        // Get the form values and groupTree
        $fv = $this->controller->exportValues( $this->_name );

        foreach ($fv as $k => $v) {
            list($groupId, $fieldId, $elementName) = explode('_', $k, 3);
            
            // check if field exists (since form values will contain other elements besides the custom data fields.
            if (isset($v) &&
                isset($this->_groupTree[$groupId]['fields'][$fieldId]) &&
                $this->_groupTree[$groupId]['fields'][$fieldId]['name'] == $elementName) {
                
                
                if ( ! isset($this->_groupTree[$groupId]['fields'][$fieldId]['customValue'] ) ) {
                    // field exists in db so populate value from "form".
                    $this->_groupTree[$groupId]['fields'][$fieldId]['customValue'] = array();
                }

                switch ( $this->_groupTree[$groupId]['fields'][$fieldId]['html_type'] ) {
                case 'Radio':
                    if($this->_groupTree[$groupId]['fields'][$fieldId]['data_type'] == 'Boolean') {
                        $this->_groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = ( $v == 'yes' ) ? 1 : 0;
                    } else {
                        $this->_groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] =  $v;
                    }                    
                    break;
                case 'Select':
                    $this->_groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] =  $v;
                    break;
                case 'CheckBox':  
                    $this->_groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] =  implode(",", array_keys($v));
                    break;
                case 'Select Date':
                    $date = CRM_Utils_Date::format( $v );
                    if ( ! $date ) {
                        $date = 'NULL';
                    }
                    $this->_groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $date;
                    break;
                    
                default:
                    $this->_groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $v;
                    break;
                }
            }
        }

        // do the updates/inserts
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree, $this->_tableId);
    }
}

?>
