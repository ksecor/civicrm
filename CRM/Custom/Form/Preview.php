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
 * This class generates form components for previewing custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Custom_Form_Preview extends CRM_Core_Form
{
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        CRM_Core_Error::le_method();

        // get the controller vars
        $groupId  = $this->get('groupId');
        $fieldId  = $this->get('fieldId');
        
        CRM_Core_Error::debug_var('groupId', $groupId);
        CRM_Core_Error::debug_var('fieldId', $fieldId);
        
        if ($fieldId) {
            // field preview
            CRM_Core_Error::debug_log_message('field preview');
            $field  = CRM_Core_BAO_CustomField::getFieldDetail($fieldId);                    
            $this->assign('field', $field);
            $groupTree = array();
            $groupTree[0]['id'] = 0;
            $groupTree[0]['fields'] = array();
            $groupTree[0]['fields'][$fieldId] = $field;
        } else {
            // group preview
            CRM_Core_Error::debug_log_message('group preview');
            $groupTree  = CRM_Core_BAO_CustomGroup::getGroupDetail($groupId);        
            CRM_Core_Error::debug_var('groupTree', $groupTree);
        }

        $this->assign('groupTree', $groupTree);

        // add the form elements
        foreach ($groupTree as $group) {
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
                $fieldId = $field['id'];                
                $elementName = $groupId . '_' . $fieldId . '_' . $field['name']; 

                $elementData = $field['default_value'];

                switch($field['html_type']) {

                case 'Text':
                case 'TextArea':
                    $element = $this->add(strtolower($field['html_type']), $elementName, $field['label'], $field['attributes']);
                    break;

                case 'Select Date':
                    $this->add('date', $elementName, $field['label'], CRM_Core_SelectValues::date('custom'));
                    break;

                case 'Radio':
                    $choice = array();
                    $choice[] = $this->createElement(strtolower($field['html_type']), null, '', ts('Yes'), 'yes', $field['attributes']);
                    $choice[] = $this->createElement(strtolower($field['html_type']), null, '', ts('No') , 'no' , $field['attributes']);
                    $this->addGroup($choice, $elementName, $field['label']);
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
                                array ('type'      => 'next',
                                       'name'      => ts('Done with Preview'),
                                       'isDefault' => true),
                                )
                          );
        

        CRM_Core_Error::ll_method();
    }
}
?>
