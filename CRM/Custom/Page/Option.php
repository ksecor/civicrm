<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Create a page for displaying Custom Options.
 *
 * Heart of this class is the run method which checks
 * for action type and then displays the appropriate
 * page.
 *
 */
class CRM_Custom_Page_Option extends CRM_Core_Page {
     
    /**
     * The Group id of the option
     *
     * @var int
     * @access protected
     */
    protected $_gid;
    
    /**
     * The field id of the option
     *
     * @var int
     * @access protected
     */
    protected $_fid;

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @access private
     */
    private static $_actionLinks;


    /**
     * Get the action links for this page.
     * 
     * @param null
     * 
     * @return array  array of action links that we need to display for the browse screen
     * @access public
     */
    function &actionLinks()
    {
       
        if (!isset(self::$_actionLinks)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this custom data multiple choice option?');
            self::$_actionLinks = array(
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Edit Option'),
                                                                          'url'   => 'civicrm/admin/custom/group/field/option',
                                                                          'qs'    => 'reset=1&action=update&id=%%id%%&fid=%%fid%%&gid=%%gid%%',
                                                                          'title' => ts('Edit Multiple Choice Option') 
                                                                          ),
                                        CRM_Core_Action::VIEW    => array(
                                                                          'name'  => ts('View'),
                                                                          'url'   => 'civicrm/admin/custom/group/field/option',
                                                                          'qs'    => 'action=view&id=%%id%%',
                                                                          'title' => ts('View Multiple Choice Option'),
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/custom/group/field/option',
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable Mutliple Choice Option') 
                                                                          ),
                                        CRM_Core_Action::DISABLE  => array(
                                                                           'name'  => ts('Disable'),
                                                                           'url'   => 'civicrm/admin/custom/group/field/option',
                                                                           'qs'    => 'action=disable&id=%%id%%',
                                                                           'title' => ts('Disable Multiple Choice Option'),
                                                                           'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"'
                                                                           ),
                                        CRM_Core_Action::DELETE  => array(
                                                                           'name'  => ts('Delete'),
                                                                           'url'   => 'civicrm/admin/custom/group/field/option',
                                                                           'qs'    => 'action=delete&id=%%id%%',
                                                                           'title' => ts('Disable Multiple Choice Option'),
                                                                           
                                                                           ),
                                        );
        }
        return self::$_actionLinks;
    }

    /**
     * Browse all custom group fields.
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    function browse()
    {
        $customOption = array();
        $customOptionBAO =& new CRM_Core_BAO_CustomOption();
        
        // fkey is fid
        //$customOptionBAO->custom_field_id = $this->_fid;
        $customOptionBAO->entity_id    = $this->_fid;
        $customOptionBAO->entity_table = 'civicrm_custom_field';

        $customOptionBAO->orderBy('weight, label');
        $customOptionBAO->find();
        
        //get the default value from custom fields
        $customFieldBAO =& new CRM_Core_BAO_CustomField();
        $customFieldBAO->id = $this->_fid;
        $customFieldBAO->find();
        while($customFieldBAO->fetch()) {
            $defaultValue = $customFieldBAO->default_value;
            $fieldHtmlType = $customFieldBAO->html_type; 
        }
        $defVal = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $defaultValue);

        while ($customOptionBAO->fetch()) {
            $customOption[$customOptionBAO->id] = array();
            CRM_Core_DAO::storeValues( $customOptionBAO, $customOption[$customOptionBAO->id]);

            $action = array_sum(array_keys($this->actionLinks()));
	    
	    // update enable/disable links depending on custom_field properties.
            if ( $customOptionBAO->is_active ) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            if ( $fieldHtmlType == 'CheckBox' || $fieldHtmlType == 'Multi-Select' ) {                
                if ( in_array($customOptionBAO->value, $defVal) ) {
                    $customOption[$customOptionBAO->id]['default_value'] = '[x]';
                } else {
                    $customOption[$customOptionBAO->id]['default_value'] = '';
                }
            } else {
                if ( $defaultValue == $customOptionBAO->value ) {
                    $customOption[$customOptionBAO->id]['default_value'] = '[x]';
                } else {
                    $customOption[$customOptionBAO->id]['default_value'] = '';
                }
            }
            
            $customOption[$customOptionBAO->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                                    array('id'  => $customOptionBAO->id,
                                                                                          'fid' => $this->_fid,
                                                                                          'gid' => $this->_gid));
        }
        $this->assign('customOption', $customOption);
    }


    /**
     * edit custom Option.
     *
     * editing would involved modifying existing fields + adding data to new fields.
     *
     * @param string  $action   the action to be invoked
     * 
     * @return void
     * @access public
     */
    function edit($action)
    {
        // create a simple controller for editing custom data
        $controller =& new CRM_Core_Controller_Simple('CRM_Custom_Form_Option', ts('Custom Option'), $action);

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/custom/group/field/option', 'reset=1&action=browse&fid=' . $this->_fid));
       
        $controller->set('gid', $this->_gid);
        $controller->set('fid', $this->_fid);
        $controller->setEmbedded(true);
        $controller->process();
        $controller->run();
        $this->browse();
    }


    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action. 
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    function run()
    {
        require_once 'CRM/Core/BAO/CustomField.php';
        // get the field id
        $this->_fid = CRM_Utils_Request::retrieve('fid', $this, false, 0);
        $this->_gid = CRM_Utils_Request::retrieve('gid', $this, false, 0);

        if ($this->_fid) {
            $fieldTitle = CRM_Core_BAO_CustomField::getTitle($this->_fid);
            $this->assign('fid', $this->_fid);
            $this->assign('fieldTitle', $fieldTitle);
            CRM_Utils_System::setTitle(ts('%1 - Multiple Choice Options', array(1 => $fieldTitle)));
        }

        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);

        $id = CRM_Utils_Request::retrieve('id', $this, false, 0);
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::VIEW | CRM_Core_Action::DELETE)) {
            $this->edit($action);   // no browse for edit/update/view
        } else {
            if ($action & CRM_Core_Action::DISABLE) {
                CRM_Core_BAO_CustomOption::setIsActive($id, 0);
            } else if ($action & CRM_Core_Action::ENABLE) {
                CRM_Core_BAO_CustomOption::setIsActive($id, 1);
            }
           $this->browse();
        }
        // Call the parents run method
        parent::run();
    }
}
?>