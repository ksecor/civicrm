<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * This class provides the functionality to delete a group of
 * contacts. This class provides functionality for the actual
 * deletion.
 */
class CRM_Contact_Form_Task_Delete extends CRM_Contact_Form_Task {

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        $this->addDefaultButtons( ts('Delete Contacts'), 'done' );
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
        
        $contactStatus = 0; // status is set if current contact is selected for deletion
        foreach ( $this->_contactIds as $contactId ) {
            if ($currentUserId == $contactId) {
                $contactStatus++;
                continue;
            }
            CRM_Contact_BAO_Contact::deleteContact( $contactId );
        }
        
        $totalContacts = count( $this->_contactIds );

        if ($contactStatus) {
            $display_name = CRM_Contact_BAO_Contact::displayName($currentUserId);
            $status = "Your contact record - $display_name - was not deleted.";
            $totalContacts--;
        }
        
        $status .= " Total Contact(s) deleted: " . $totalContacts;
        CRM_Core_Session::setStatus( $status );
    }//end of function


}

?>
