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
            foreach ($group['fields'] as $field) {
                switch($field['html_type']) {
                case 'Text':
                case 'TextArea':
                    $this->add(strtolower($field['html_type']), $field['name'], $field['label'], $field['attributes'], $field['required']);
                    break;
                case 'Select Date':
                    $this->add('text', $field['name'], $field['label'], $field['attributes'], $field['required']);
                    break;
                case 'Radio':
                    $this->addElement(strtolower($field['html_type']), $field['name'], $field['label'], 'Yes', 'yes', $field['attributes']);
                    $this->addElement(strtolower($field['html_type']), $field['name'], '', 'No', 'No', $field['attributes']);
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

        //CRM_Core_Error::le_method();

        $defaults = array();

        $customData = CRM_Contact_BAO_Contact::getCustomData($this->_tableId);
        //CRM_Core_Error::debug_var('customData', $customData);

        foreach ($customData as $v) {
            $defaults[$v['name']] = $v['data'];
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
        //CRM_Core_Error::le_method();

        $fv = $this->exportValues();

        // get the elements of the contact id

        //        CRM_Core_Error::debug_var('fv', $fv);
        
        //CRM_Core_Error::debug_var('session', $_SESSION);

        //CRM_Core_Error::debug_var('tableid', $this->_tableId);

        // update them with new values
        // hack.. need to update only those values which have changed.
        // but currently updating all fields
        
        $customGroupTree = CRM_Core_BAO_CustomGroup::getTree($this->_entityType, $this->_tableID);

        //CRM_Core_Error::debug_var('customGroupTree', $customGroupTree);
        //CRM_Core_Error::debug_var('customData', $customData);

        //CRM_Core_Error::ll_method();
    }

}

?>