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

require_once 'CRM/Contribute/Form/Task.php';
//require_once 'CRM/Utils/Menu.php';
require_once 'CRM/Contribute/BAO/Contribution.php';
/**
 * This class provides the functionality to delete a group of
 * contributions. This class provides functionality for the actual
 * deletion.
 */
class CRM_Contribute_Form_Task_Delete extends CRM_Contribute_Form_Task {

    /**
     * Are we operating in "single mode", i.e. deleting one
     * specific contribution?
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
// uncomment when adding support for single contrib delete
//      $cid = CRM_Utils_Request::retrieve( 'cid', $this, false );

//       if ( $cid ) {
//           // not sure why this is needed :(
//           // also add the cid params to the Menu array
//           CRM_Utils_Menu::addParam( 'cid', $cid );
//
//           // create menus ..
//           $startWeight = CRM_Utils_Menu::getMaxWeight('civicrm/contact/view');
//           $startWeight++;
//           CRM_Core_BAO_CustomGroup::addMenuTabs(CRM_Contact_BAO_Contact::getContactType($cid), 'civicrm/contact/view/cd', $startWeight);
//           $this->_contactIds = array( $cid );
//           $this->_single     = true;
//           $this->assign( 'totalSelectedContacts', 1 );
//       } else {
             parent::preProcess();
//       }
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm() {
// uncomment when adding support for single contrib delete
//      if ( $this->_single ) {
//          // also fix the user context stack in case the user hits cancel
//          $session =& CRM_Core_Session::singleton( );
//          $session->replaceUserContext( CRM_Utils_System::url('civicrm/contact/view/basic',
//                                                              'reset=1&cid=' . $this->_contactIds[0] ) );
//          $this->addDefaultButtons( ts('Delete Contacts'), 'done', 'cancel' );
//      } else {
            $this->addDefaultButtons(ts('Delete Contributions'), 'done');
//      }
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $session =& CRM_Core_Session::singleton();

        $deletedContributions = 0;
        foreach ($this->_contributionIds as $contributionId) {
            if (CRM_Contribute_BAO_Contribution::deleteContribution($contributionId)) {
                $deletedContributions++;
            }
        }

// uncomment when adding support for single contrib delete
//      if ( ! $this->_single ) {
            $status = array(
                ts('Deleted Contribution(s): %1', array(1 => $deletedContributions)),
                ts('Total Selected Contribution(s): %1', array(1 => count($this->_contributionIds))),
            );
//      } else {
//          if ( $deletedContributions ) {
//              $session->replaceUserContext( CRM_Utils_System::url( 'civicrm/contribute/search',
//                                                                   'force=1' ) );
//              $status = ts( 'Selected contribution was deleted sucessfully.' );
//          } else {
//              $status = array(
//                              ts( 'Selected contribution cannot be deleted.' )
//                              );
//          }
//      }

        CRM_Core_Session::setStatus($status);
    }


}

?>
