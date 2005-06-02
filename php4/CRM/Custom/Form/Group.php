<?php
/*
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
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/DAO/CustomGroup.php';
require_once 'CRM/Utils/String.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/Form.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Custom_Form_Group extends CRM_Core_Form {

    /**
     * the group id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    var $_id;

    /**
     * Function to set variables up before form is built
     *
     * @param none
     * @return void
     * @access public
     */
     function preProcess()
    {
        // current group id
        $this->_id = $this->get('id');

        // setting title for html page
        if ($this->_action == CRM_CORE_ACTION_UPDATE) {
            $groupTitle = CRM_Core_BAO_CustomGroup::getTitle($this->_id);
            CRM_Utils_System::setTitle("Edit $groupTitle");
        } else {
            CRM_Utils_System::setTitle("New Custom Data Group");
        }
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @return none
     * @access public
     */
     function buildQuickForm()
    {
        $this->applyFilter('__ALL__', 'trim');

        // title
        $this->add('text', 'title', ts('Group Name'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'title'), true);
        $this->addRule('title', ts('Please enter a valid name.'), 'title');

        // which entity is this custom data group for ?
        // for update action only allowed if there are no custom values present for this group.
        $extendsElement = $this->add('select', 'extends', ts('Used For'), $GLOBALS['_CRM_CORE_SELECTVALUES']['customGroupExtends']);

        if ($this->_action == CRM_CORE_ACTION_UPDATE) { 
            $extendsElement->freeze();
            $this->assign('gid', $this->_id);
        }

        // help text
        $this->add('textarea', 'help_pre',  ts('Form Help'),  CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'help_pre'));

        // weight
        $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'weight'), true);
        $this->addRule('weight', ts(' is a numeric field') , 'numeric');


        // is this group active ?
        $this->addElement('checkbox', 'is_active', ts('Is this Custom Data Group active?') );

        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );

        // views are implemented as frozen form
        if ($this->_action & CRM_CORE_ACTION_VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onClick' => "location.href='civicrm/admin/custom/group?reset=1&action=browse'"));
        }
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     *
     * @param none
     * @access public
     * @return None
     */
    function setDefaultValues()
    {
        $defaults = array();
        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            CRM_Core_BAO_CustomGroup::retrieve($params, $defaults);
        } else {
            $defaults['is_active'] = 1;
        }
        return $defaults;
    }

    /**
     * Process the form
     *
     * @param none
     * @return void
     * @access public
     */
     function postProcess()
    {
        // get the submitted form values.
        $params = $this->controller->exportValues('Group');

        // create custom group dao, populate fields and then save.
        $group = new CRM_Core_DAO_CustomGroup();
        $group->title       = $params['title'];
        $group->name        = CRM_Utils_String::titleToVar($params['title']);
        $group->extends     = $params['extends'];
        $group->style       = $params['style'];
        $group->weight      = $params['weight'];
        $group->help_pre    = $params['help_pre'];
        $group->help_post   = $params['help_post'];
        $group->is_active   = CRM_Utils_Array::value('is_active', $params, false);
        $group->domain_id   = 1;

        if ($this->_action & CRM_CORE_ACTION_UPDATE) {
            $group->id = $this->_id;
        }
        $group->save();
        if ($this->_action & CRM_CORE_ACTION_UPDATE) {
            CRM_Core_Session::setStatus('Your Group "' . $group->title . '" has been saved.');
        } else {
            $url = CRM_Utils_System::url( 'civicrm/admin/custom/group/field', 'reset=1&action=add&gid=' . $group->id);
            CRM_Core_Session::setStatus('Your Group "' . $group->title . '" has been added. You can <a href="'. $url .'">add custom fields</a> to this group now.');
        }
    }
}
?>