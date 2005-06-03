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
 * This class generates form components for History
 * 
 */
class CRM_History_Form_History extends CRM_Core_Form
{
    /**
     * The table name, used when editing/creating an history
     *
     * @var string
     * @access protected
     */
    protected $_tableName;

    /**
     * The table id, used when editing/creating an history
     *
     * @var int
     * @access protected
     */
    protected $_tableId;
    
    /**
     * The history table id, used when editing the history
     *
     * @var int
     * @access protected
     */
    protected $_historyTableId;

    
    /**
     * Performing all the form preprocessing. Setting values for
     * all members
     *
     * @param none
     * @return none
     *
     * @access public
     */
    function preProcess()
    {

        //CRM_Core_Error::le_method();

        $this->_tableName = $this->get('tableName');
        $this->_tableId   = $this->get('tableId');
        $this->_historyTableId  = $this->get('historyTableId');

        //CRM_Core_Error::ll_method();
    }


    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues()
    {
        $defaults = array();
        if (isset($this->_historyTableId)) {
            $params = array('id' => $this->_historyTableId);
            CRM_Core_BAO_History::retreive($params, $defaults);
        }
        return $defaults;
    }

    /**
     * Function to actually build the form
     *
     * @param none
     * @return none
     * @access public
     */
    public function buildQuickForm()
    {
        // form elements
        $this->add('text', 'activity_type', ts('Activity Type'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_History', 'activity_type'));
        $this->add('text', 'module', ts('Module'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_History', 'module'));
        $this->add('text', 'callback', ts('Callback'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_History', 'callback'));
        $this->add('text', 'activity_id', ts('Activity ID'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_History', 'activity_id'), true);
        $this->add('text', 'activity_summary', ts('Activity Summary'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_History', 'activity_summary'));
        $this->add('date', 'activity_date', ts('Activity Date'), CRM_Core_SelectValues::date('relative'));

        // some rules
        $this->addRule('activity_id', ts(' is a numeric field') , 'numeric');
        $this->addRule('activity_date', ts(' is not a valid date') , 'qfDate');


        // finally the buttons
        $this->addButtons(array(
                                array('type'      => 'next',
                                      'name'      => ts('Save'),
                                      'isDefault' => true),
                                array('type'      => 'cancel',
                                      'name'      => ts('Cancel')),
                                )
                          );
    }

       
    /**
     * Process the submitted form.
     *
     * @access public
     * @return none
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->exportValues();

        // populate history DAO and save it
        $historyDAO = new CRM_Core_DAO_History();
        $historyDAO->id               = $this->_historyTableId;
        $historyDAO->entity_table     = $this->_tableName;
        $historyDAO->entity_id        = $this->_tableId;
        $historyDAO->activity_type    = $params['activity_type'];
        $historyDAO->module           = $params['module'];
        $historyDAO->callback         = $params['callback'];
        $historyDAO->activity_id      = $params['activity_id'];
        $historyDAO->activity_summary = $params['activity_summary'];
        $historyDAO->activity_date  = CRM_Utils_Date::format($params['activity_date']);
        $historyDAO->save();
        CRM_Core_Session::setStatus(ts('Your History has been saved.'));
    }
}

?>
