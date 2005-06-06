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
 * This class provides the functionality to email a group of
 * contacts. 
 */
class CRM_Contact_Form_Task_Email extends CRM_Contact_Form_Task {
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        $session =& CRM_Core_Session::singleton( );
        $userID  =  $session->get( 'userID' );
        list( $fromDisplayName, $fromEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $userID );
        if ( ! $fromEmail ) {
            CRM_Core_Error::fatal( 'Your user record does not have a valid email address' );
        }
        $from = "'$fromDisplayName' <$fromEmail>";
        $this->assign( 'from', $from );
        
        $this->add( 'text'    , 'subject', ts('Subject'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_EmailHistory', 'subject' ), true );
        $this->add( 'textarea', 'message', ts('Message'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_EmailHistory', 'message' ), true );

        $this->addDefaultButtons( ts('Email Contacts') );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $subject = $this->controller->exportValue( 'Email', 'subject' );
        $message = $this->controller->exportValue( 'Email', 'message' );

        list( $total, $sent, $notSent ) = CRM_Core_BAO_EmailHistory::sendEmail( $this->_contactIds, $subject, $message );

        $status = array(
                        ts(''),
                        ts('Total Selected Contact(s): %1', array(1 => $total))
                        );
        if ( $sent ) {
            $status[] = ts('Email sent to contact(s): %1', array(1 => $sent));
        }
        if ( $notSent ) {
            $status[] = ts('Email not sent to contact(s): %1', array(1 => $notSent));
        }
        CRM_Core_Session::setStatus( $status );
        
    }//end of function


}

?>
