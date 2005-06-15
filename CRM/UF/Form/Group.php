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
require_once 'CRM/Core/BAO/UFGroup.php';

/**
 *
 */
class CRM_UF_Form_Group extends CRM_Core_Form {

    /**
     * the form id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_id;

    /**
     * Function to set variables up before form is built
     *
     * @param none
     * @return void
     * @access public
     */
    public function preProcess()
    {
        // current form id
        $this->_id = $this->get('id');
        $this-> assign('gid',$this->_id);
        
        // setting title for html page
        if ($this->_action == CRM_Core_Action::UPDATE) {
            $title = CRM_Core_BAO_UFGroup::getTitle($this->_id);
            CRM_Utils_System::setTitle( ts( 'Edit %1', array(1 => $title ) ) );
        } else {
            CRM_Utils_System::setTitle( ts('New User Sharing Group') );
        }
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @return none
     * @access public
     */
    public function buildQuickForm()
    {
        $this->applyFilter('__ALL__', 'trim');

        // title
        $this->add('text', 'title', ts('Group Name'),
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFGroup', 'title'), true);
        $this->addRule('title', ts('Please enter a valid name.'), 'title');

        // is this group active ?
        $this->addElement('checkbox', 'is_active', ts('Is this User Sharing Group active?') );

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
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onClick' => "location.href='civicrm/admin/uf/group?reset=1&action=browse'"));
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

        if ( isset($this->_id ) ) {
            $params = array('id' => $this->_id);
            CRM_Core_BAO_UFGroup::retrieve($params, $defaults);
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
    public function postProcess()
    {
        // get the submitted form values.
        $params = $this->controller->exportValues('Group');

        // create custom group dao, populate fields and then save.
        $ufGroup            =& new CRM_Core_DAO_UFGroup();
        $ufGroup->title     = $params['title'];
        $ufGroup->is_active = CRM_Utils_Array::value('is_active', $params, false);
        $ufGroup->domain_id = CRM_Core_Config::domainID( );
        
        if ($this->_action & CRM_Core_Action::UPDATE) {
            $ufGroup->id = $this->_id;
        }
        $ufGroup->save();

        if ($this->_action & CRM_Core_Action::UPDATE) {
            CRM_Core_Session::setStatus(ts('Your User Sharing Group "%1" has been saved.', array(1 => $ufGroup->title)));
        } else {
            $url = CRM_Utils_System::url( 'civicrm/admin/uf/group/field', 'reset=1&action=add&id=' . $ufGroup->id);
            CRM_Core_Session::setStatus(ts('Your User Sharing Group "%1" has been added. You can <a href="%2">add fields</a> to this group now.',
                                           array(1 => $ufGroup->title, 2 => $url)));
        }
    }

}

?>
