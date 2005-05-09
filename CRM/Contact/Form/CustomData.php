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
    public function buildQuickForm( ) {

        // get the groupTreeForm
        // gets all details of group tree for entity
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($this->_entityType);

        $this->assign('groupTree2', $groupTree);
        
        // this is a complete hack.. need to check for the entitytype and 
        // then invoke the relevant BAO...
        $customData = CRM_Contact_BAO_Contact::getCustomData($this->_tableId);

        //CRM_Core_Error::debug_var('groupTree', $groupTree);
        //CRM_Core_Error::debug_var('customData', $customData);

        // add the form elements
        foreach ($groupTree as $field) {
            //CRM_Core_Error::debug_var('field', $field);
            foreach ($field as $fieldId => $fieldDetail) {
                //CRM_Core_Error::debug_var('fieldId', $fieldId);
                //foreach ($fieldDetail as $k => $v) {
                    // CRM_Core_Error::debug_log_message("$k = $v");
                //}
                switch($fieldDetail['html_type']) {
                case 'Text':
                case 'TextArea':
                    $this->add(strtolower($fieldDetail['html_type']), $fieldDetail['name'], $fieldDetail['label'], $fieldDetail['attributes'], $fieldDetail['required']);
                    break;
                case 'Select Date':
                    $this->add('text', $fieldDetail['name'], $fieldDetail['label'], $fieldDetail['attributes'], $fieldDetail['required']);
                    break;
                case 'Radio':
                    //CRM_Core_Error::debug_log_message("adding radio button");
                    $this->addElement(strtolower($fieldDetail['html_type']), $fieldDetail['name'], $fieldDetail['label'], 'Yes', 'yes', $fieldDetail['attributes']);
                    $this->addElement(strtolower($fieldDetail['html_type']), $fieldDetail['name'], '', 'No', 'No', $fieldDetail['attributes']);
                    //$this->add(strtolower($fieldDetail['html_type']), $fieldDetail['name'], $fieldDetail['label'], 'Yes', 'yes', $fieldDetail['attributes'], $fieldDetail['required']);
                    //$this->add(strtolower($fieldDetail['html_type']), $fieldDetail['name'], '', 'No', 'No', $fieldDetail['attributes'], $fieldDetail['required']);
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
    function &setDefaultValues() {

        CRM_Core_Error::le_method();

        $defaults = array();

        $customData = CRM_Contact_BAO_Contact::getCustomData($this->_tableId);
        CRM_Core_Error::debug_var('customData', $customData);
        


//         $defaults['sort_name'] = $this->_formValues['sort_name'];
//         foreach (self::$csv as $v) {
//             $defaults[$v] = $this->_formValues['cb_' . $v] ? key($this->_formValues['cb_' . $v]) : '';
//         }

//         if ( $this->_context === 'amtg' ) {
//             $defaults['task'] = CRM_Contact_Task::GROUP_CONTACTS;
//         }

        CRM_Core_Error::ll_method();

        return $defaults;
    }


       
    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
    }//end of function


}

?>