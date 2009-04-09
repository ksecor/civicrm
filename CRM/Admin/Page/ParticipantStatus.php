<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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
require_once 'CRM/Event/DAO/ParticipantStatusType.php';

class CRM_Admin_Page_ParticipantStatus extends CRM_Core_Page_Basic
{
    function getBAOName()
    {
        return 'CRM_Event_BAO_ParticipantStatusType';
    }

    function &links()
    {
        static $links = null;
        if ($links === null) {
            $links = array(
                CRM_Core_Action::UPDATE => array(
                    'name'  => ts('Edit'),
                    'url'   => 'civicrm/admin/participant_status',
                    'qs'    => 'action=update&id=%%id%%&reset=1',
                    'title' => ts('Edit Status'),
                ),
                CRM_Core_Action::DELETE => array(
                    'name'  => ts('Delete'),
                    'url'   => 'civicrm/admin/participant_status',
                    'qs'    => 'action=delete&id=%%id%%',
                    'title' => ts('Delete Status'),
                ),
            );
        }
        return $links;
    }

    function browse()
    {
        $statusTypes = array();

        $dao = new CRM_Event_DAO_ParticipantStatusType;
        $dao->find();

        while ($dao->fetch()) {
            CRM_Core_DAO::storeValues($dao, $statusTypes[$dao->id]);
            $action = array_sum(array_keys($this->links()));
            if ($dao->is_reserved) $action &= ~CRM_Core_Action::DELETE;
            $statusTypes[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action, array('id' => $dao->id));
        }
        $this->assign('rows', $statusTypes);
    }

    function editForm()
    {
        return 'CRM_Admin_Form_ParticipantStatus';
    }

    function editName()
    {
        return 'Participant Status';
    }

    function userContext($mode = null)
    {
        return 'civicrm/admin/participant_status';
    }
}
