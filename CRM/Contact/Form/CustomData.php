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
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        // gets all details of group tree for entity
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($this->_entityType, $this->_tableId);
        $this->assign('groupTree', $groupTree);

        // add the form elements
        foreach ($groupTree as $group) {
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
                    if ($elementData) {
                        $element->setValue($elementData);
                    }
                    break;
                case 'Select Date':
                    $this->add('text', $elementName, $field['label'], $field['attributes'], $field['is_required']);
                    break;
                case 'Radio':
                    $radioYes = $this->addElement(strtolower($field['html_type']), $elementName, $field['label'], 'Yes', '1',
                                                  $field['attributes']);
                    $radioNo = $this->addElement(strtolower($field['html_type']), $elementName, '', 'No', '0', $field['attributes']);
                    
                    // element data for radio button is a special case
                    if (!is_null($elementData)) {
                        if ($elementData) {
                            $radioYes->setChecked(1);
                        } else {
                            $radioNo->setChecked(1);
                        }
                    }
                    if ($field['is_required']) {
                        $this->addRule($elementName, ' is a required field' , 'required');
                    }
                    break;
                case 'Select':
                case 'CheckBox':
                case 'Select State / Province':
                case 'Select Country':
                }
            }
        }

        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => 'Save',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'reset',
                                        'name'      => 'Reset'),
                                array ( 'type'       => 'cancel',
                                        'name'      => 'Cancel' ),
                                )
                          );
    }
    

    /**
     * Process the user submitted custom data values.
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // get the form values and groupTree
        $fv = $this->exportValues();
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($this->_entityType, $this->_tableId);
        
        // update group tree with form values
        foreach ($fv as $k => $v) {
            list($groupId, $fieldId, $elementName) = explode('_', $k, 3);
            
            // check if field exists (since form values will contain other elements besides the custom data fields.
            if (isset($groupTree[$groupId]['fields'][$fieldId]) && $groupTree[$groupId]['fields'][$fieldId]['name'] == $elementName) {
                if (isset($groupTree[$groupId]['fields'][$fieldId]['customValue'])) {
                    // field exists in db so populate value from "form".
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $v;
                } else if (strlen($v)) {
                    // field does not exist in db but is data is entered by user
                    // hence create an array for customValue and populate it.
                    $groupTree[$groupId]['fields'][$fieldId]['customValue'] = array();
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $v;
                }
            }
        }
        // do the updates/inserts
        CRM_Core_BAO_CustomGroup::updateCustomData($groupTree, $this->_tableId);
    }
}
?>