<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Member/Form/Task.php';
require_once 'CRM/Member/BAO/Membership.php';

/**
 * This class provides the functionality to delete a group of
 * members. This class provides functionality for the actual
 * deletion.
 */
class CRM_Member_Form_Task_Delete extends CRM_Member_Form_Task {

    /**
     * Are we operating in "single mode", i.e. deleting one
     * specific membership?
     *
     * @var boolean
     */
    protected $_single = false;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess() {
        parent::preProcess();
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm() {
        $this->addDefaultButtons(ts('Delete Members'), 'done');
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $session =& CRM_Core_Session::singleton();

        $deletedMemberss = 0;
        foreach ($this->_memberIds as $memberId) {
            if (CRM_Member_BAO_Membership::deleteMembership($memberId)) {
                $deletedMemberss++;
            }
        }

        $status = array(
                        ts('Deleted Member(s): %1', array(1 => $deletedMembers)),
                        ts('Total Selected Membership(s): %1', array(1 => count($this->_memberIds))),
                        );
        CRM_Core_Session::setStatus($status);
    }


}

?>
