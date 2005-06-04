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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */



require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Utils/Date.php';
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
    var $_tableName;

    /**
     * The table id, used when editing/creating custom data
     *
     * @var int
     */
    var $_tableId;
    
    /**
     * entity type of the table id
     *
     * @var string
     */
    var $_entityType;

    /**
     * the group tree data
     *
     * @var array
     */
    var $_groupTree;

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
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
     function buildQuickForm()
    {
        $this->assign('groupTree', $this->_groupTree);

        // add the form elements
        foreach ($this->_groupTree as $group) {
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
                    $choice[] = $this->createElement(strtolower($field['html_type']), null, '', ts('Yes'), 'yes', $field['attributes']);
                    $choice[] = $this->createElement(strtolower($field['html_type']), null, '', ts('No') , 'no' , $field['attributes']);
                    $this->addGroup($choice, $elementName, $field['label']);
                    if ($field['is_required']) {
                        $this->addRule($elementName, ts('%1 is a required field.', array(1 => $field['label'])) , 'required');
                    }
                    break;

                case 'Select':
                case 'CheckBox':
                case 'Select State / Province':
                case 'Select Country':
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

        if ($this->_action & ( CRM_CORE_ACTION_VIEW | CRM_CORE_ACTION_BROWSE ) ) {
            $this->freeze();
        }
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
                if (isset($field['customValue'])) {
                    if ($field['html_type'] == 'Radio') {
                        $defaults[$elementName] = $field['customValue']['data'] ? 'yes' : 'no';
                    } else if ($field['html_type'] == 'Select Date') {
                        if ($date = $field['customValue']['data']) {
                            $defaults[$elementName] = CRM_Utils_Date::unformat( $date );
                        }
                    } else {
                        $defaults[$elementName] = $field['customValue']['data'];
                    }
                } else if (($this->_action == CRM_CORE_ACTION_UPDATE) && isset($field['default_value']) ) {
                    // use default value if present but first preference to customValue
                    if ($field['html_type'] == 'Radio') {
                    } else if ($field['html_type'] == 'Select Date') {
                    } else {
                        // for the rest
                        $defaults[$elementName] = $field['default_value'];
                    }
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
     function postProcess() 
    {

        // Get the form values and groupTree
        $fv = $this->exportValues();

        // update group tree with form values
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
                    $this->_groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = ( $v == 'yes' ) ? 1 : 0;
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
