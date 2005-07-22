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
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Commerce_Donation_Form_DonationPage extends CRM_Core_Form {

    /**
     * the group id saved to the session for an update
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
        // current group id
        $this->_id = $this->get('id');

        // setting title for html page
        if ($this->_action == CRM_Core_Action::UPDATE) {
            //$title = CRM_Core_BAO_CustomGroup::getTitle($this->_id);
            //CRM_Utils_System::setTitle(ts('Edit %1', array(1 => $title)));
        } else if ($this->_action == CRM_Core_Action::VIEW) {
            //$title = CRM_Core_BAO_CustomGroup::getTitle($this->_id);
            //CRM_Utils_System::setTitle(ts('Preview %1', array(1 => $title)));
        } else {
            CRM_Utils_System::setTitle(ts('New Donation Page'));
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

        // name
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute('CRM_Commerce_Donation_DAO_DonationPage', 'name'), true);

        // description
        $this->add('textarea', 'description', ts('Description'), CRM_Core_DAO::getAttribute('CRM_Commerce_Donation_DAO_DonationPage', 'description'), true);


        // is this group active ?
        $this->addElement('checkbox', 'is_active', ts('Is this Donation Page active?') );

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
            //CRM_Core_BAO_CustomGroup::retrieve($params, $defaults);
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
        $params = $this->controller->exportValues('Donation Page');

        // create custom group dao, populate fields and then save.
        $donationPage =& new CRM_Commerce_Donation_DAO_DonationPage();
        $donationPage->name          = $params['name'];
        $donationPage->description   = $params['description'];
        $donationPage->is_active     = CRM_Utils_Array::value('is_active', $params, false);
        $donationPage->domain_id     = CRM_Core_Config::domainID( );

        if ($this->_action & CRM_Core_Action::UPDATE) {
            //$donationPage->id = $this->_id;
        }
        $donationPage->save();
        if ($this->_action & CRM_Core_Action::UPDATE) {
            CRM_Core_Session::setStatus(ts('Your Donation Page "%1" has been saved.', array(1 => $donationPage->name)));
        } else {
            $url = CRM_Utils_System::url( 'civicrm/admin/custom/group/field', 'reset=1&action=add&gid=' . $group->id);
            CRM_Core_Session::setStatus(ts('Your Donation Page "%1" has been added. You can <a href="%2">add custom fields</a> to this group now.', array(1 => $donationPage->name, 2 => $url)));
        }
    }
}
?>
