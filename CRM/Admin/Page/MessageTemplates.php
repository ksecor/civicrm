<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';

/**
 * Page for displaying list of membership types
 */
class CRM_Admin_Page_MessageTemplates extends CRM_Core_Page_Basic 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    // ids of templates which diverted from the default ones and can be reverted
    protected $_revertible = array();

    // set to the id that we’re reverting at the given moment (if we are)
    protected $_revertedId;

    function __construct($title = null, $mode = null) {
        parent::__construct($title, $mode);

        // fetch the ids of templates which diverted from defaults and can be reverted –
        // these templates have the same workflow_id as the defaults; defaults are reserved
        $sql = '
            SELECT diverted.id
            FROM civicrm_msg_template diverted JOIN civicrm_msg_template orig ON (
                diverted.workflow_id = orig.workflow_id AND
                orig.is_reserved = 1                    AND (
                    diverted.msg_subject != orig.msg_subject OR
                    diverted.msg_text    != orig.msg_text    OR
                    diverted.msg_html    != orig.msg_html
                )
            )
        ';
        $dao =& CRM_Core_DAO::executeQuery($sql);
        while ($dao->fetch()) {
            $this->_revertible[] = $dao->id;
        }
    }

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Core_BAO_MessageTemplates';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
        if (!(self::$_links)) {
            $confirm = ts('Are you sure you want to revert this template?', array('escape' => 'js'));
            self::$_links = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/messageTemplates',
                                                                    'qs'    => 'action=update&id=%%id%%&reset=1',
                                                                    'title' => ts('Edit Message Templates') 
                                                                   ),
                                //CRM_Core_Action::DISABLE => array(
                                //                                  'name'  => ts('Disable'),
                                //                                  'extra' => 'onclick = "enableDisable( %%id%%,\''. 'CRM_Core_BAO_MessageTemplates' . '\',\'' . 'enable-disable' . '\' );"',
                                //                                  'ref'   => 'disable-action',
                                //                                  'title' => ts('Disable Message Templates'),
                                //                                  ),
                                //CRM_Core_Action::ENABLE  => array(
                                //                                  'name'  => ts('Enable'),
                                //                                  'extra' => 'onclick = "enableDisable( %%id%%,\''. 'CRM_Core_BAO_MessageTemplates' . '\',\'' . 'disable-enable' . '\' );"',
                                //                                  'ref'   => 'enable-action',
                                //                                  'title' => ts('Enable Message Templates'),
                                //                                  ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/admin/messageTemplates',
                                                                    'qs'    => 'action=delete&id=%%id%%',
                                                                    'title' => ts('Delete Message Templates') 
                                                                    ),
                                  CRM_Core_Action::REVERT  => array(
                                                                    'name'  => ts('Revert'),
                                                                    'extra' => "onclick = 'return confirm(\"$confirm\");'",
                                                                    'url'   => 'civicrm/admin/messageTemplates',
                                                                    'qs'    => 'action=revert&id=%%id%%',
                                                                    'title' => ts('Revert the Template to Default'),
                                                                    ),
                                  );
        }
        return self::$_links;
    }

    function action(&$object, $action, &$values, &$links, $permission)
    {
        // do not expose action link for reverting to default if the template did not diverge or we just reverted it now
        if (!in_array($object->id, $this->_revertible) or
            ($this->_action & CRM_Core_Action::REVERT and $object->id == $this->_revertedId)) {
            $action &= ~CRM_Core_Action::REVERT;
        }

        // default templates shouldn’t be deletable
        if ($object->is_default) {
            $action &= ~CRM_Core_Action::DELETE;
        }

        parent::action($object, $action, $values, $links, $permission);
    }

    function run($args = null, $pageArgs = null, $sort = null)
    {
        // handle the revert action and offload the rest to parent
        if (CRM_Utils_Request::retrieve('action', 'String', $this) & CRM_Core_Action::REVERT) {

            $id = CRM_Utils_Request::retrieve('id', 'Positive', $this);
            if (!$this->checkPermission($id, null)) {
                CRM_Core_Error::fatal(ts('You do not have permission to revert this template.'));
            }

            $this->_revertedId = $id;

            require_once 'CRM/Core/BAO/MessageTemplates.php';
            CRM_Core_BAO_MessageTemplates::revert($id);
        }

        return parent::run($args, $pageArgs, $sort);
    }

    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() 
    {
        return 'CRM_Admin_Form_MessageTemplates';
    }
    
    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() 
    {
        return ts('Message Template');
    }
    
    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/admin/messageTemplates';
    }
}


