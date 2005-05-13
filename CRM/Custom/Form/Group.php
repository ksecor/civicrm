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

/**
 * form to process actions on the group aspect of Custom
 */
class CRM_Custom_Form_Group extends CRM_Core_Form {

    /**
     * the group id saved to the session for an update
     *
     * @var int
     */
    protected $_id;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_id = $this->get('id');
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        // title
        $this->add('text', 'title', 'Group Name', CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'title'), true);
        $this->addRule('title', 'Please enter a valid name.', 'title');

        // description
        //$this->add('text', 'description', 'Group Description', CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'description'), true);
        // which entity is this custom data group for ?
        // for update action only allowed if there are no custom values present for this group.
        $extendsElement = $this->add('select', 'extends', 'Used For', CRM_Core_SelectValues::$customGroupExtends);

        //CRM_Core_Error::debug_var('this', $this);
        if ($this->_mode == CRM_Core_Form::MODE_UPDATE && CRM_Core_BAO_CustomGroup::getNumValue($this->_id)) { 
            $extendsElement->freeze();
        }

        // how do we want to display this custom data group ?
        $this->add('select', 'style',   'Display Style', CRM_Core_SelectValues::$customGroupStyle);

        // help text
        $this->add('textarea', 'help_pre',  'Help Pre',  CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'help_pre'));
        $this->add('textarea', 'help_post', 'Help Post', CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'help_post'));

        // weight
        $this->add('text', 'weight', 'Weight', CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'weight'), true);

        $this->addElement('checkbox', 'is_active', 'Is this Custom Data Group active?');

        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => 'Save',
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => 'Cancel' ),
                                )
                          );
        if ($this->_mode & self::MODE_VIEW) {
            $this->freeze();
        }
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
     * @return void
     * @access public
     */
    public function postProcess()
    {
        $params = $this->controller->exportValues('Group');

        $group = new CRM_Core_DAO_CustomGroup();
        $group->title       = $params['title'];
        $group->name        = CRM_Utils_String::titleToVar($params['title']);
        //$group->description = $params['description'];
        $group->extends     = $params['extends'];
        $group->style       = $params['style'];
        $group->weight      = $params['weight'];
        $group->help_pre    = $params['help_pre'];
        $group->help_post   = $params['help_post'];
        $group->is_active   = CRM_Utils_Array::value('is_active', $params, false);
        $group->domain_id   = 1;

        if ($this->_mode & self::MODE_UPDATE) {
            $group->id = $this->_id;
        }
        $group->save();
        CRM_Core_Session::setStatus('Your Group "' . $group->title . '" has been saved');
    }
}
?>