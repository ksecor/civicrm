<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once "CRM/Core/BAO/Email.php";

/**
 * This class provides the common functionality for sending email to
 * one or a group of contact ids. This class is reused by all the search
 * components in CiviCRM (since they all have send email as a task)
 */
class CRM_Contact_Form_Task_EmailCommon 
{
    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    static function preProcess( &$form ) 
    {
        require_once 'CRM/Core/BAO/MessageTemplates.php';
        $messageText  = array( );
        $messageSubject  = array( );
        $dao =& new CRM_Core_BAO_MessageTemplates( );
        $dao->is_active= 1;
        $dao->find();
        while ( $dao->fetch() ){
            $messageText   [$dao->id] = $dao->msg_text;
            $messageSubject[$dao->id] = $dao->msg_subject;
        }
        
        $form->assign( 'message'       , $messageText    );
        $form->assign( 'messageSubject', $messageSubject );
    }

    static function preProcessSingle( &$form, $cid ) 
    {
        $form->_contactIds = array( $cid );
        $form->_single     = true;
        $emails     = CRM_Core_BAO_Email::allEmails( $cid );
        $form->_emails = array( );
        $form->_onHold = array( );
        
        $toName = CRM_Contact_BAO_Contact::displayName( $cid );
        foreach ( $emails as $emailId => $item ) {
            $email = $item['email'];
            if (!$email && ( count($emails) <= 1 ) ) {
                $form->_emails[$email] = '"' . $toName . '"';
                $form->_noEmails = true;
            } else {
                if ($email) {
                    $form->_emails[$email] = '"' . $toName . '" <' . $email . '> ' . $item['locationType'];
                    $form->_onHold[$email] = $item['on_hold'];
                }
            }
            
            if ( $item['is_primary'] ) {
                $form->_emails[$email] .= ' ' . ts('(preferred)');
            }
            $form->_emails[$email] = htmlspecialchars( $form->_emails[$email] );
        }

        $form->assign( 'single', $form->_single );
    }
    
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    static function buildQuickForm( &$form )
    {
        if ( ! $form->_single ) {
            $toArray          = array();
            $suppressedEmails = 0;
            require_once 'CRM/Contact/BAO/Contact.php';
            foreach ( $form->_contactIds as $contactId ) {
                list($toDisplayName, $toEmail, $toDoNotEmail) = CRM_Contact_BAO_Contact::getContactDetails($contactId);
                if ( ! trim( $toDisplayName ) ) {
                    $toDisplayName = $toEmail;
                }

                if ( $toDoNotEmail || empty( $toEmail ) ) {
                    $suppressedEmails++;
                } else {
                    $toArray[] = "\"$toDisplayName\" <$toEmail>";
                }
            }

            if ( empty( $toArray ) ) {
                CRM_Core_Error::statusBounce( ts('Selected contact(s) do not have a valid email address' ));
            }

            $form->assign('to', implode(', ', $toArray));
            $form->assign('suppressedEmails', $suppressedEmails);
            
        } else {
            if ( $form->_noEmails ) {
                $to = $form->add( 'select', 'to', ts('To'), $form->_emails );
                $form->add('text', 'emailAddress', null, CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'));
                $form->addRule('emailAddress', ts('%1 is a required field.', array(1 => 'To')) , 'required');
                $form->addRule( "emailAddress", ts('Email is not valid.'), 'email' );
            } else {
                $to =& $form->add( 'select', 'to', ts('To'), $form->_emails, true );
            }
            
            if ( count( $form->_emails ) <= 1 ) {
                foreach ( $form->_emails as $email => $dontCare ) {
                    $defaults = array( 'to' => $email );
                    $form->setDefaults( $defaults );
                }
                
                $to->freeze( );
            }
        }
        
        $form->assign('noEmails', $form->_noEmails);
        
        $session =& CRM_Core_Session::singleton( );
        $userID  =  $session->get( 'userID' );
        list( $fromDisplayName, $fromEmail, $fromDoNotEmail ) = CRM_Contact_BAO_Contact::getContactDetails( $userID );
        
        if ( ! $fromEmail ) {
            CRM_Core_Error::statusBounce( ts('Your user record does not have a valid email address' ));
        }

        if ( ! trim($fromDisplayName) ) {
            $fromDisplayName = $fromEmail;
        }
        
        $form->assign('totalSelectedContacts',count($form->_contactIds));
        
        $from = '"' . $fromDisplayName . '"' . "<$fromEmail>";
        $form->assign( 'from', $from );
        
        //Added for CRM-1393
        $form->assign( 'dojoIncludes', "dojo.require('dojo.widget.Select');" );
        $config   =& CRM_Core_Config::singleton( );
        $domainID =  CRM_Core_Config::domainID( );
        
        require_once 'CRM/Core/BAO/MessageTemplates.php';
        $form->_templates = CRM_Core_BAO_MessageTemplates::getMessageTemplates();
        $attributes = array( 'dojoType'       => 'Select',
                             'style'          => 'width: 300px;',
                             'autocomplete'   => 'false',
                             'onValueChanged' => 'selectValue',
                             'dataUrl'        => CRM_Utils_System::url( 'civicrm/ajax/message',
                                                                        "d=$domainID" ),
                             );
        
        //if no template Present then drop down select box and update template should not be displayed
        if (! empty( $form->_templates ) ){
            $form->add('select', 'template', ts('Select Template'), null, false, $attributes );
            $form->add('checkbox','updateTemplate',ts('Update Template'), null);
        }

        //insert message Text by selecting "Select Template option"
        $form->add( 'textarea', 
                    'message', 
                    ts('Message'),
                    array('cols' => '56', 'rows' => '7','onkeyup' => "return verify(this)"));
        $form->add('checkbox',
                   'saveTemplate',
                   ts('Save as New Template'),
                   null,
                   false,
                   array('onclick' => "showSaveDetails(this)"));

        if ( ! $form->get('saveTemplate') ) {
            $form->add('text','saveTemplateName',ts('Template Title'));
        } 

        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_EmailHistory', 'subject' );
        $form->add( 'text', 'subject', ts('Subject'), $attributes, true );

        if ( $form->_single ) {
            // also fix the user context stack
            $url = CRM_Utils_System::url('civicrm/contact/view',
                                         "&show=1&action=browse&cid={$form->_contactIds[0]}&selectedChild=activity" );
             
            $session->replaceUserContext( $url );
            $form->addDefaultButtons( ts('Send Email'), 'next', 'cancel' );
        } else {
            $form->addDefaultButtons( ts('Send Email') );
        }
        
        $form->addFormRule( array( 'CRM_Contact_Form_Task_EmailCommon', 'formRule' ), $form );
    }

    /** 
     * form rule  
     *  
     * @param array $fields    the input form values  
     * @param array $dontCare   
     * @param array $self      additional values form 'this'  
     *  
     * @return true if no errors, else array of errors  
     * @access public  
     * 
     */  
    static function formRule($fields, $dontCare, &$self) 
    {
        $toEmail = CRM_Utils_Array::value( 'to', $fields );
        $errors = array();
        
        if ( CRM_Utils_Array::value($toEmail,$self->_onHold) ) {
            $errors['to'] = ts("The selected email address is On Hold because the maximum number of delivery attempts has failed. If you have been informed that the problem with this address is resolved, you can take the address off Hold by editing the contact record. Otherwise, you will need to try an different email address for this contact.");
        }
        
        //Added for CRM-1393
        if( CRM_Utils_Array::value('saveMessage',$fields) ==1 && empty($fields['saveMessageName']) ){
            $errors['saveMessageName'] = ts("Enter name to save message template");
        }
        return empty($errors) ? true : $errors;
    }
    
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    static function postProcess( &$form ) 
    {
        $emailAddress = null;
        if ( $form->_single ) {
            $emailAddress = $form->controller->exportValue( 'Email', 'to' );
        }
        
        $cid = CRM_Utils_Request::retrieve( 'cid', 'Positive', $form, false );
         
        if ( $form->_noEmails ) {
            $emailAddress = $form->controller->exportValue( 'Email', 'emailAddress' );

            // for adding the email-id to the primary address
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
         
        $subject = $form->controller->exportValue( 'Email', 'subject' );
        $message = $form->controller->exportValue( 'Email', 'message' );

        //added code for CRM-1393
        $messageParams = $form->exportValues( );
        $templateID = array();

        // Update Message template, if new or updated
        require_once 'CRM/Core/BAO/MessageTemplates.php';
        if ( $messageParams['saveTemplate'] || $messageParams['updateTemplate']) {
            if ( $messageParams['saveTemplate'] ) {
                $newMessage = array( 'msg_title'   => $messageParams['saveTemplateName'],
                                     'msg_text'    => $messageParams['message'],
                                     'msg_subject' => $messageParams['subject'],
                                     'is_active'   => true
                                     );
                CRM_Core_BAO_MessageTemplates::add($newMessage, $templateID);
            }
            
            if ( $messageParams['updateTemplate'] && 
                 CRM_Utils_Array::key( $_POST['template_selected'], $form->_templates ) ) {

                $newMessage = array( 'msg_text'    => $messageParams['message'],
                                     'msg_subject' => $messageParams['subject'],
                                     'is_active'   => true );

                require_once 'CRM/Core/BAO/MessageTemplates.php';
                require_once 'CRM/Utils/Array.php';
                $template = CRM_Core_BAO_MessageTemplates::getMessageTemplates();
                $templateID = array('messageTemplate' =>
                                    CRM_Utils_Array::key( $_POST['template_selected'],
                                                          $form->_templates ) );
                CRM_Core_BAO_MessageTemplates::add($newMessage, $templateID);
            }
        }
        
        $status = array(
                        '',
                        ts('Total Selected Contact(s): %1', array(1 => count($form->_contactIds) ))
                        );
        
        $statusOnHold = '';
        foreach ($form->_contactIds as $item => $contactId) {
            $email     = CRM_Contact_BAO_Contact::getEmailDetails($contactId);
            $allEmails = CRM_Core_BAO_Email::allEmails($contactId);

            if ( $allEmails[$email[1]]['is_primary'] && $allEmails[$email[1]]['on_hold'] ) {
                $displayName = CRM_Contact_BAO_Contact::displayName($contactId);
                $contactLink = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid=$contactId");
                unset($form->_contactIds[$item]);
                $statusOnHold .= ts( 'Email was not sent to %1 because primary email address (%2) is On Hold.',
                                     array( 1 => "<a href='$contactLink'>$displayName</a>", 2 => "<strong>{$email[1]}</strong>")) . '<br />';
            }
        }
        
        // replace domain tokens
        $config   = CRM_Core_Config::singleton( );
        $domainId = $config->domainID( );
        require_once 'CRM/Core/BAO/Domain.php';
        $domain = CRM_Core_BAO_Domain::getDomainByID( $domainId );
        require_once 'CRM/Utils/Token.php';
        $text = CRM_Utils_Token::replaceDomainTokens( $message, $domain, false  );
        
        // send the mail
        require_once 'CRM/Core/BAO/EmailHistory.php';
        list( $total, $sent, $notSent ) = CRM_Core_BAO_EmailHistory::sendEmail( $form->_contactIds, $subject, $text, $emailAddress );
        
        if ( $sent ) {
            $status[] = ts('Email sent to Contact(s): %1', array(1 => count($sent)));
        }
        
        //Display the name and number of contacts for those email is not sent.
        if ( $notSent ) {
            $statusDisplay =
                ts('Email not sent to contact(s) (no email address on file or communication preferences specify DO NOT EMAIL): %1  <br />Details : ', 
                   array(1 => count($notSent)));
            foreach($notSent as $cIds=>$cId) {
                $name = new CRM_Contact_DAO_Contact();
                $name->id = $cId;
                $details = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid=$cId");
                $name->find();
                while( $name->fetch() ) {
                    $statusDisplay .= "<a href='$details'>" . $name->display_name . '</a>, ';
                }
            }
            $status[] = $statusDisplay;
        }
        
        if ( strlen($statusOnHold) ) {
            $status[] = $statusOnHold;
        }
               
        CRM_Core_Session::setStatus( $status );
        
    }//end of function
}

?>
