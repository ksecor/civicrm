<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';
require_once 'CRM/Dedupe/DAO/RuleGroup.php';

class CRM_Admin_Page_DedupeRules extends CRM_Core_Page_Basic
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName()
    {
        return 'CRM_Dedupe_BAO_RuleGroup';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
          if (!(self::$_links)) {
              $disableExtra = ts('Are you sure you want to disable this Rule?');
              
              // helper variable for nicer formatting
              self::$_links = array(
                  CRM_Core_Action::VIEW  => array(
                      'name'  => ts('Use Rule'),
                      'url'   => 'civicrm/admin/deduperules',
                      'qs'    => 'reset=1&action=view&rgid=%%id%%',
                      'title' => ts('Use DedupeRule'),
                      ),
                  CRM_Core_Action::UPDATE  => array(
                      'name'  => ts('Edit Rule'),
                      'url'   => 'civicrm/admin/deduperules',
                      'qs'    => 'action=update&id=%%id%%',
                      'title' => ts('Edit DedupeRule'),
                  ),
                  CRM_Core_Action::ENABLE  => array(
                      'name'  => ts('Enable'),
                      'url'   => 'civicrm/admin/deduperules',
                      'qs'    => 'action=enable&rgid=%%id%%',
                      'title' => ts('Enable DedupeRule'),
                      ),
                  CRM_Core_Action::DISABLE  => array(
                      'name'  => ts('Disable'),
                      'url'   => 'civicrm/admin/deduperules',
                      'qs'    => 'action=disable&id=%%id%%',
                      'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                      'title' => ts('Disable DedupeRule'),
                  ),
              );
        }
        return self::$_links;
    }

    /**
     * Run the page
     *
     * This method is called after the page is created. It checks for the type
     * of action and executes that action. Finally it calls the parent's run
     * method.
     *
     * @return void
     * @access public
     *
     */
    function run()
    {
        // get the requested action, default to 'browse'
        $action = CRM_Utils_Request::retrieve('action', 'String', $this, false, 'browse');

        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', 'Positive', $this, false, 0);

        // which action to take?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            $this->edit($action, $id);
        }

        // browse the rules
        $this->browse();

        // parent run
        parent::run();
    }

    /**
     * Browse all rule groups
     *  
     * @return void
     * @access public
     */
    function browse()
    {
        // get all rule groups
        $ruleGroups = array();
        $dao =& new CRM_Dedupe_DAO_RuleGroup();

        // set the domain_id parameter
        $config =& CRM_Core_Config::singleton( );
        $dao->domain_id = $config->domainID( );

        $dao->find();
        
        while ($dao->fetch()) {
            $ruleGroups[$dao->id] = array();
            CRM_Core_DAO::storeValues($dao, $ruleGroups[$dao->id]);
     
            // form all action links
            $action = array_sum(array_keys($this->links()));
            $links = self::links();
            if ($dao->is_active) {
                unset($links[32]);
            } else {
                unset($links[64]);
            }
            $ruleGroups[$dao->id]['action'] = CRM_Core_Action::formLink($links, $action, array('id' => $dao->id));
            CRM_Dedupe_DAO_RuleGroup::addDisplayEnums($ruleGroups[$dao->id]);
        }
        $this->assign('rows', $ruleGroups);
    }

    /**
     * Get name of edit form
     *
     * @return string  classname of edit form
     */
    function editForm()
    {
        if ($this->_action == 4) {
            return 'CRM_Admin_Form_DedupeFind';
        } else { 
            return 'CRM_Admin_Form_DedupeRules';
        }    
    }
    
    /**
     * Get edit form name
     *
     * @return string  name of this page
     */
    function editName()
    {
        if ($this->_action == 4) {
            return 'DedupeFind';
        } else { 
            return 'DedupeRules';
        }    
    }
    
    /**
     * Get user context
     *
     * @return string  user context
     */
    function userContext($mode = null)
    {
        return 'civicrm/admin/deduperules';
    }
}


