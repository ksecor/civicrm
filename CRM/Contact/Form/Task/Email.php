<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
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

        // also add the cid params to the Menu array
        CRM_Utils_Menu::addParam( 'cid', $cid );

        // create menus ..
        $startWeight = CRM_Utils_Menu::getMaxWeight('civicrm/contact/view');
        $startWeight++;
        CRM_Core_BAO_CustomGroup::addMenuTabs(CRM_Contact_BAO_Contact::getContactType($cid), 'civicrm/contact/view/cd', $startWeight);

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
                if ( ! empty( $toEmail ) ) {
                    $toArray[] = "\"$toDisplayName\" <$toEmail>";
                }
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
            CRM_Utils_System::statusBounce( ts('Your user record does not have a valid email address' ));
        }
        $from = "'$fromDisplayName' <$fromEmail>";
        $this->assign( 'from', $from );
        
        $this->add( 'text'    , 'subject', ts('Subject'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_EmailHistory', 'subject' ), true );
        $this->add( 'textarea', 'message', ts('Message'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_EmailHistory', 'message' ), true );

        if ( $this->_single ) {
            // also fix the user context stack
            $session->replaceUserContext( CRM_Utils_System::url('civicrm/contact/view/activity',
                                                                '&show=1&action=browse&cid=' . $this->_contactIds[0] ) );
            $this->addDefaultButtons( ts('Send Email'), 'next', 'cancel' );
        } else {
            $this->addDefaultButtons( ts('Send Email') );
        }
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
