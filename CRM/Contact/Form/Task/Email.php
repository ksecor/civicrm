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
        $cid = CRM_Utils_Request::retrieve( 'cid', $this, false );
        if ( $cid ) {
            $this->_contactIds = array( $cid );
            $this->_single     = true;
            $emails     = CRM_Contact_BAO_Contact::allEmails( $cid );
            $this->_emails = array( );
            $toName = CRM_Contact_BAO_Contact::displayName( $cid );
            foreach ( $emails as $email => $item ) {
                $this->_emails[$email] = '"' . $toName . '" <' . $email . '> ' . $item['locationType'];
                if ( $item['is_primary'] ) {
                    $this->_emails[$email] .= ' (preferred)';
                }
                $this->_emails[$email] = htmlentities( $this->_emails[$email] );
            }
        } else {
            parent::preProcess( );
        }
        $this->assign( 'single', $this->_single );
    }
    
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    public function buildQuickForm()
    {

        if ( ! $this->_single ) {
            $toArray = array();
            foreach ( $this->_contactIds as $contactId ) {
                list($toDisplayName, $toEmail) = CRM_Contact_BAO_Contact::getEmailDetails($contactId);
                $toArray[] = "\"$toDisplayName\" <$toEmail>";
            }
            $this->assign('to', implode(', ', $toArray));
        } else {
            $to =& $this->add( 'select', 'to', ts('To'), $this->_emails, true );
            if ( count( $this->_emails ) <= 1 ) {
                foreach ( $this->_emails as $email => $dontCare ) {
                    $defaults = array( 'to' => $email );
                    $this->setDefaults( $defaults );
                }
                $to->freeze( );
            }
        }

        
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
        $emailAddress = null;
        if ( $this->_single ) {
            $emailAddress = $this->controller->exportValue( 'Email', 'to' );
        }
        $subject = $this->controller->exportValue( 'Email', 'subject' );
        $message = $this->controller->exportValue( 'Email', 'message' );

        list( $total, $sent, $notSent ) = CRM_Core_BAO_EmailHistory::sendEmail( $this->_contactIds, $subject, $message, $emailAddress );

        $status = array(
                        '',
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
