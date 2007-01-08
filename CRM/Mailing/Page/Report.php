<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';

/**
 * Page to display / edit the header / footer of a mailing
 *
 */
class CRM_Mailing_Page_Report extends CRM_Core_Page_Basic {

    public $_mailing_id;

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO
     */
    function getBAOName()
    {
        return 'CRM_Mailing_BAO_Mailing';
    }

    function &links() {
        return array();
    }

    function editForm() {
        return null;
    }

    function editName() {
        return 'CiviMail Report';
    }

    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/mailing/report';
    }

    function userContextParams($mode = null) {
        return 'reset=1&mid=' . $this->_mailing_id;
    }


    function run() {
        $this->_mailing_id = CRM_Utils_Request::retrieve('mid', 'Positive',
                                                         $this);
        
        require_once 'CRM/Mailing/BAO/Mailing.php';
        $report =& CRM_Mailing_BAO_Mailing::report($this->_mailing_id);
        
        $this->assign('report', $report);
        CRM_Utils_System::setTitle(ts('CiviMail Report: %1', array(1 =>
        $report['mailing']['name'])));

        parent::run();
    }


}

?>
