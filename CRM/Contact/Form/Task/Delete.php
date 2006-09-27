<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';
require_once 'CRM/Core/Menu.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Contact/BAO/Contact.php';
/**
 * This class provides the functionality to delete a group of
 * contacts. This class provides functionality for the actual
 * deletion.
 */
class CRM_Contact_Form_Task_Delete extends CRM_Contact_Form_Task {

    /** 
     * Are we operating in "single mode", i.e. sending email to one 
     * specific contact? 
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
    function preProcess( ) { 
        $cid = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                            $this, false ); 

         if ( $cid ) { 
             // not sure why this is needed :(
             // also add the cid params to the Menu array 
             CRM_Core_Menu::addParam( 'cid', $cid ); 
             
             // create menus .. 
             $startWeight = CRM_Core_Menu::getMaxWeight('civicrm/contact/view'); 
             $startWeight++; 
             CRM_Core_BAO_CustomGroup::addMenuTabs(CRM_Contact_BAO_Contact::getContactType($cid), 'civicrm/contact/view/cd', $startWeight); 
             $this->_contactIds = array( $cid ); 
             $this->_single     = true; 
             $this->assign( 'totalSelectedContacts', 1 );
         } else {
             parent::preProcess( );
         }
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        if ( $this->_single ) {
            // also fix the user context stack in case the user hits cancel
            $session =& CRM_Core_Session::singleton( );
            $session->replaceUserContext( CRM_Utils_System::url('civicrm/contact/view/basic',
                                                                'reset=1&cid=' . $this->_contactIds[0] ) );
            $this->addDefaultButtons( ts('Delete Contacts'), 'done', 'cancel' );
        } else {
            $this->addDefaultButtons( ts('Delete Contacts'), 'done' );
        }
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $session =& CRM_Core_Session::singleton( );
        $currentUserId = $session->get( 'userID' );
        
        $selfDelete = false;
        $deletedContacts = 0;
        foreach ( $this->_contactIds as $contactId ) {
            if ($currentUserId == $contactId) {
                $selfDelete = true;
                continue;
            }
            if ( CRM_Contact_BAO_Contact::deleteContact( $contactId ) ) {
                $deletedContacts++;
            }
        }

        if ( ! $this->_single ) {
            $status = array( );
            $status = array(
                            ts( 'Deleted Contact(s): %1', array(1 => $deletedContacts)),
                            ts('Total Selected Contact(s): %1', array(1 => count($this->_contactIds))),
                            );
            
            if ( $selfDelete ) {
                $display_name = CRM_Contact_BAO_Contact::displayName($currentUserId);
                $status[] = ts('The contact record which is linked to the currently logged in user account - "%1" - can not be deleted.', array(1 => $display_name));
            }
        } else {
            if ( $deletedContacts ) {
                $session->replaceUserContext( CRM_Utils_System::url( 'civicrm/contact/search',
                                                                     'force=1' ) );
                $status = ts('Selected contact was deleted sucessfully.');
            } else {
                $status = array(
                                ts('Selected contact cannot be deleted.')
                                ); 
                if ( $selfDelete ) {
                    $display_name = CRM_Contact_BAO_Contact::displayName($currentUserId);
                    $status[] = ts('This contact record is linked to the currently logged in user account - "%1" - and can not be deleted.', array(1 => $display_name));
                }
            }
        }

        CRM_Core_Session::setStatus( $status );
    }//end of function


}

?>
