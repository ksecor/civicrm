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
            $groupID = $group['id'];
            foreach ($group['fields'] as $field) {
                $fieldID = $field['id'];                
                $elementName = $groupID . '_' . $fieldID . '_' . $field['name']; 
                switch($field['html_type']) {
                case 'Text':
                case 'TextArea':
                    $this->add(strtolower($field['html_type']), $elementName, $field['label'], $field['attributes'], $field['required']);
                    break;
                case 'Select Date':
                    $this->add('text', $elementName, $field['label'], $field['attributes'], $field['required']);
                    break;
                case 'Radio':
                    $this->addElement(strtolower($field['html_type']), $elementName, $field['label'], 'Yes', 'yes', $field['attributes']);
                    $this->addElement(strtolower($field['html_type']), $elementName, '', 'No', 'No', $field['attributes']);
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
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues()
    {
        //CRM_Core_Error::le_method();

        $defaults = array();

        $groupTree = CRM_Core_BAO_CustomGroup::getTree($this->_entityType, $this->_tableId);

        foreach ($groupTree as $group) {
            $groupID = $group['id'];
            foreach ($group['fields'] as $field) {
                $fieldID = $field['id'];
                $elementName = $groupID . '_' . $fieldID . '_' . $field['name'];
                if (isset($field['customValue'])) {
                    
                    //CRM_Core_Error::debug_log_message("setting default value for $elementName");

                    $defaults[$elementName] = $field['customValue']['data'];
                }
            }
        }

        //CRM_Core_Error::ll_method();
        return $defaults;
    }

       
    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        CRM_Core_Error::le_method();

        // get the form values and groupTree
        $fv = $this->exportValues();
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($this->_entityType, $this->_tableId);

//         CRM_Core_Error::debug_var('tableID', $this->_tableId);
        CRM_Core_Error::debug_var('fv', $fv);
        CRM_Core_Error::debug_var('groupTree', $groupTree);

        // update group tree with form values

        foreach ($fv as $k => $v) {
            list($groupID, $fieldID, $elementName) = explode('_', $k, 3);

            CRM_Core_Error::debug_var('groupID', $groupID);
            CRM_Core_Error::debug_var('fieldID', $fieldID);
            CRM_Core_Error::debug_log_message("checking for $elementName");

//             CRM_Core_Error::debug_var('elementName', $elementName);



            if (isset($groupTree[$groupID]['fields'][$fieldID]) && $groupTree[$groupID]['fields'][$fieldID]['name'] == $elementName) {
                // new element or old one ?

                CRM_Core_Error::debug_log_message("field is set");
                
                if (isset($groupTree[$groupID]['fields'][$fieldID]['customValue'])) {



                    $groupTree[$groupID]['fields'][$fieldID]['customValue']['data'] = $v;


                    CRM_Core_Error::debug_var('value', $groupTree[$groupID]['fields'][$fieldID]['customValue']['data']);

                    //CRM_Core_Error::debug_log_message("field is set");

                } else {
                    $groupTree[$groupID]['fields'][$fieldID]['customValue'] = array();
                    $groupTree[$groupID]['fields'][$fieldID]['customValue']['data'] = $v;
                }
            }
        }

        CRM_Core_BAO_CustomGroup::updateCustomData($groupTree, $this->_tableID);

        CRM_Core_Error::ll_method();
    }

}

?>