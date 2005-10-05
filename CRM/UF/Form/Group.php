<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
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
     * the title for group
     *
     * @var int
     * @access protected
     */
    protected $_title;


    /**
     * Function to set variables up before form is built
     *
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
        } else if($this->_action == CRM_Core_Action::DELETE ) {
            $title = CRM_Core_BAO_UFGroup::getTitle($this->_id);
            CRM_Utils_System::setTitle( ts( 'Delete %1', array(1 => $title ) ) );
            $this->_title = $title;
            $this-> assign('title',$title);
        } else {
            CRM_Utils_System::setTitle( ts('New CiviCRM Profile Group') );
        }
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        if($this->_action & CRM_Core_Action::DELETE) {
            $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Delete Profile Group '),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
            return;

        }
        
        $this->applyFilter('__ALL__', 'trim');

        // title
        $this->add('text', 'title', ts('Profile Name'),
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFGroup', 'title'), true);
        $this->addRule('title', ts('Please enter a valid name.'), 'title');

        // help text
        $this->add('textarea', 'help_pre',  ts('Pre-form Help'),  CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFGroup', 'help_pre'));
        $this->add('textarea', 'help_post',  ts('Post-form Help'),  CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFGroup', 'help_post'));

        // weight
        $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'weight'), true);
        $this->addRule('weight', ts(' is a numeric field') , 'numeric');

        // is this group active ?
        $this->addElement('checkbox', 'is_active', ts('Is this CiviCRM Profile active?') );

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
     * @access public
     * @return void
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
     * @return void
     * @access public
     */
    public function postProcess()
    {
        if($this->_action & CRM_Core_Action::DELETE) {
            if (CRM_Core_BAO_UFGroup::del($this->_id)) {
                CRM_Core_Session::setStatus(ts('Your CiviCRM Profile Group "%1" has been deleted.', array(1 => $this->_title)));
            } else {
                CRM_Core_Session::setStatus(ts('You must delete all profile fields for "%1" prior to deleting the profile.', array(1 => $this->_title)));
            }
            
            return;
        }
        // get the submitted form values.
        $params = $this->controller->exportValues('Group');

        // create custom group dao, populate fields and then save.
        $ufGroup            =& new CRM_Core_DAO_UFGroup();
        $ufGroup->title     = $params['title'];
        $ufGroup->weight    = $params['weight'];
        $ufGroup->help_pre  = $params['help_pre'];
        $ufGroup->help_post = $params['help_post'];
        $ufGroup->is_active = CRM_Utils_Array::value('is_active', $params, false);
        $ufGroup->domain_id = CRM_Core_Config::domainID( );
        
        if ($this->_action & CRM_Core_Action::UPDATE) {
            $ufGroup->id = $this->_id;
        }
        $ufGroup->save();

        if ($this->_action & CRM_Core_Action::UPDATE) {
            CRM_Core_Session::setStatus(ts('Your CiviCRM Profile Group "%1" has been saved.', array(1 => $ufGroup->title)));
        } else {
            $url = CRM_Utils_System::url( 'civicrm/admin/uf/group/field', 'reset=1&action=add&gid=' . $ufGroup->id);
            CRM_Core_Session::setStatus(ts('Your CiviCRM Profile Group "%1" has been added. You can <a href="%2">add fields</a> to this group now.',
                                           array(1 => $ufGroup->title, 2 => $url)));
        }
    }

}

?>
