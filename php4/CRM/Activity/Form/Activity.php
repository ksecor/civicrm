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
require_once 'CRM/Core/BAO/Activity.php';
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/DAO/Activity.php';
require_once 'CRM/Utils/Date.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for Activities
 * 
 */
class CRM_Activity_Form_Activity extends CRM_Core_Form
{
    /**
     * The table name, used when editing/creating an activity
     *
     * @var string
     * @access protected
     */
    var $_tableName;

    /**
     * The table id, used when editing/creating an activity
     *
     * @var int
     * @access protected
     */
    var $_tableId;
    
    /**
     * The activity table id, used when editing the activity
     *
     * @var int
     * @access protected
     */
    var $_activityTableId;

    
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
        $this->_activityTableId  = $this->get('activityTableId');

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
        if (isset($this->_activityTableId)) {
            $params = array('id' => $this->_activityTableId);
            CRM_Core_BAO_Activity::retreive($params, $defaults);
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
     function buildQuickForm()
    {
        // form elements
        $this->add('text', 'activity_type', ts('Activity Type'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Activity', 'activity_type'));
        $this->add('text', 'module', ts('Module'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Activity', 'module'));
        $this->add('text', 'callback', ts('Callback'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Activity', 'callback'));
        $this->add('text', 'activity_id', ts('Activity ID'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Activity', 'activity_id'), true);
        $this->add('text', 'activity_summary', ts('Activity Summary'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Activity', 'activity_summary'));
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
     function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->exportValues();

        // CRM_Core_Error::debug_var('params', $params);

        // populate activity DAO and save it
        $activity = new CRM_Core_DAO_Activity();
        $activity->id               = $this->_activityTableId;
        $activity->entity_table     = $this->_tableName;
        $activity->entity_id        = $this->_tableId;
        $activity->activity_type    = $params['activity_type'];
        $activity->module           = $params['module'];
        $activity->callback         = $params['callback'];
        $activity->activity_id      = $params['activity_id'];
        $activity->activity_summary = $params['activity_summary'];

        // need to pad Month and day
        //$params['activity_date']['M'] = ($params['activity_date']['M'] < 10) ? '0' . $params['activity_date']['M'] : $params['activity_date']['M'];
        //$params['activity_date']['d'] = ($params['activity_date']['d'] < 10) ? '0' . $params['activity_date']['d'] : $params['activity_date']['d'];

        //$activity->activity_date    = $params['activity_date']['Y'] . $params['activity_date']['M'] . $params['activity_date']['d']; 
        $activity->activity_date  = CRM_Utils_Date::format($params['activity_date']);
        $activity->save();
        CRM_Core_Session::setStatus(ts("Your Activity has been saved."));
    }
}

?>
