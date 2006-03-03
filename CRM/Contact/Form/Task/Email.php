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

require_once 'CRM/Contact/Form/Task.php';
require_once 'CRM/Utils/Menu.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Contact/BAO/Contact.php';
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
     * Are we operating in "single mode", i.e. sending email to one
     * specific contact?
     *
     * @var boolean
     */
    protected $_noEmails = false;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) {
        $cid = CRM_Utils_Request::retrieve( 'cid', $this, false );

        if ( $cid ) {
            // not sure why this is needed :(
            // also add the cid params to the Menu array
            CRM_Utils_Menu::addParam( 'cid', $cid );
            
            // create menus ..
            $startWeight = CRM_Utils_Menu::getMaxWeight('civicrm/contact/view');
            $startWeight++;
            CRM_Core_BAO_CustomGroup::addMenuTabs(CRM_Contact_BAO_Contact::getContactType($cid), 'civicrm/contact/view/cd', $startWeight);

            $this->_contactIds = array( $cid );
            $this->_single     = true;
            $emails     = CRM_Contact_BAO_Contact::allEmails( $cid );
            $this->_emails = array( );
            $toName = CRM_Contact_BAO_Contact::displayName( $cid );
            foreach ( $emails as $email => $item ) {
                if (!$email && ( count($emails) <= 1 ) ) {
                    $this->_emails[$email] = '"' . $toName . '"';
                    $this->_noEmails = true;
                } else {
                    if ($email) {
                        $this->_emails[$email] = '"' . $toName . '" <' . $email . '> ' . $item['locationType'];
                    }
                }

                if ( $item['is_primary'] ) {
                    $this->_emails[$email] .= ' ' . ts('(preferred)');
                }
                $this->_emails[$email] = htmlspecialchars( $this->_emails[$email] );
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
            $validMails = array();
            $suppressedEmails = 0;
            foreach ( $this->_contactIds as $contactId ) {
                list($toDisplayName, $toEmail, $toDoNotEmail) = CRM_Contact_BAO_Contact::getContactDetails($contactId);

                if ( ! trim( $toDisplayName ) ) {
                    $toDisplayName = $toEmail;
                }
                // not sure why we have separate $validMails and $toArray and
                // why we assign $toArray and not $validMails below... [Shot]
                if ( ! empty( $toEmail ) and ! $doNotEmail ) {
                    $validMails[] = "\"$toDisplayName\" <$toEmail>";
                }
                if ($doNotEmail) {
                    $suppressedEmails++;
                } else {
                    $toArray[] = "\"$toDisplayName\" <$toEmail>";
                }
            }
            if ( empty( $validMails ) ) {
                CRM_Utils_System::statusBounce( ts('Selected contact(s) does not have a valid email address' ));
            }
            $this->assign('to', implode(', ', $toArray));
            $this->assign('suppressedEmails', $suppressedEmails);
        } else {
            
            if ( $this->_noEmails ) {
                $to = $this->add( 'select', 'to', ts('To'), $this->_emails );
                $this->add('text', 'emailAddress', null, CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'));
                $this->addRule('emailAddress', ts('%1 is a required field.', array(1 => 'To')) , 'required');
                $this->addRule( "emailAddress", ts('Email is not valid.'), 'email' );
            } else {
                $to =& $this->add( 'select', 'to', ts('To'), $this->_emails, true );
            }
            
            if ( count( $this->_emails ) <= 1 ) {
                foreach ( $this->_emails as $email => $dontCare ) {
                    $defaults = array( 'to' => $email );
                    $this->setDefaults( $defaults );
                }
                $to->freeze( );
            }
        }
        $this->assign('noEmails', $this->_noEmails);
        
        $session =& CRM_Core_Session::singleton( );
        $userID  =  $session->get( 'userID' );
        list( $fromDisplayName, $fromEmail, $fromDoNotEmail ) = CRM_Contact_BAO_Contact::getContactDetails( $userID );
        
        if ( ! $fromEmail ) {
            CRM_Utils_System::statusBounce( ts('Your user record does not have a valid email address' ));
        }

        if ( ! trim($fromDisplayName) ) {
            $fromDisplayName = $fromEmail;
        }
        
        $from = '"' . $fromDisplayName . '"' . "<$fromEmail>";
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
        if ( $this->_noEmails ) {
            $emailAddress = $this->controller->exportValue( 'Email', 'emailAddress' );

            // for adding the email-id to the primary address
            $cid = CRM_Utils_Request::retrieve( 'cid', $this, false );
            if ( $cid ) {
                $location =& CRM_Contact_BAO_Contact::getEmailDetails($cid);
                if ( $location[3] ) {
                    $locationID = $location[3];
                    $email =& new CRM_Core_DAO_Email();
                    $email->location_id = $locationID;
                    $email->is_primary  = 1;
                    $email->email       = $emailAddress; 
                    $email->save( );
                    
                } else {
                    require_once 'CRM/Core/BAO/LocationType.php';
                    $ids = $params = $locID = array();
                    $params['contact_id'] = $cid;
                    $locType = CRM_Core_BAO_LocationType::getDefault();
                    $params['location'][1]['location_type_id'] = $locType->id;
                    $params['location'][1]['is_primary'] = 1;
                    $params['location'][1]['email'][1]['email'] = $emailAddress;
                    CRM_Core_BAO_Location::add($params, $ids, 1);
                }
            }
        }

        $subject = $this->controller->exportValue( 'Email', 'subject' );
        $message = $this->controller->exportValue( 'Email', 'message' );

        require_once 'CRM/Core/BAO/EmailHistory.php';
        list( $total, $sent, $notSent ) = CRM_Core_BAO_EmailHistory::sendEmail( $this->_contactIds, $subject, $message, $emailAddress );

        $status = array(
                        '',
                        ts('Total Selected Contact(s): %1', array(1 => $total))
                        );
        if ( $sent ) {
            $status[] = ts('Email sent to contact(s): %1', array(1 => $sent));
        }
        if ( $notSent ) {
            $status[] = ts('Email not sent to contact(s) (no email address on file or communication preferences specify DO NOT EMAIL): %1', array(1 => $notSent));
        }
        CRM_Core_Session::setStatus( $status );
        
    }//end of function


}

?>
