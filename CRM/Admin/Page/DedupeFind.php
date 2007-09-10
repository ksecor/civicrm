<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

class CRM_Admin_Page_DedupeFind extends CRM_Core_Page_Basic
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
        return 'CRM_Dedupe_DAO_RuleGroup';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
          if (!(self::$_links)) {
            // helper variable for nicer formatting
              self::$_links = array(
                  CRM_Core_Action::UPDATE  => array(
                      'name'  => ts('Use Rule'),
                      'url'   => 'civicrm/admin/dedupefind',
                      'qs'    => 'reset=1&action=update&rgid=%%id%%',
                      'title' => ts('Use DedupeRule'),
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
        // FIXME: the whole action/Page-vs.-Form abuse
        // in the case of DedupeFind should be fixed
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

            $ruleGroups[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action, array('id' => $dao->id));
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
        return 'CRM_Admin_Form_DedupeFind';
    }

    /**
     * Get edit form name
     *
     * @return string  name of this page
     */
    function editName()
    {
        return 'DedupeFind';
    }

    /**
     * Get user context
     *
     * @return string  user context
     */
    function userContext($mode = null)
    {
        return 'civicrm/admin/dedupefind';
    }
}

?>
