<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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

require_once 'CRM/Contact/Form/Task.php';
require_once 'CRM/Core/Menu.php';
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
     * all the existing templates in the system
     *
     * @var array
     */
    protected $_templates = null;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    
    function preProcess( ) {
        $cid = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                            $this, false );

        //Added for CRM-1393
        //Get Message Text 
        require_once 'CRM/Member/BAO/MessageTemplates.php';
        $messageText  = array( );
        $temp =& new CRM_Member_BAO_MessageTemplates( );
        $temp->is_active= 1;
        $temp->find();
        while ( $temp->fetch() ){
            $messageText[$temp->id]    = $temp->msg_text;
            $messageSubject[$temp->id] = $temp->msg_subject;
        } 
        $this->assign( 'message'       , $messageText    );
        $this->assign( 'messageSubject', $messageSubject );
        
        if ( $cid ) {
            // not sure why this is needed :(
            // also add the cid params to the Menu array
            CRM_Core_Menu::addParam( 'cid', $cid );
            
            $this->_contactIds = array( $cid );
            $this->_single     = true;
            $emails     = CRM_Contact_BAO_Contact::allEmails( $cid );
            $this->_emails = array( );
            $this->_onHold = array( );

           
            $toName = CRM_Contact_BAO_Contact::displayName( $cid );
            foreach ( $emails as $email => $item ) {
                if (!$email && ( count($emails) <= 1 ) ) {
                    $this->_emails[$email] = '"' . $toName . '"';
                    $this->_noEmails = true;
                } else {
                    if ($email) {
                        $this->_emails[$email] = '"' . $toName . '" <' . $email . '> ' . $item['locationType'];
                        $this->_onHold[$email] = $item['on_hold'];
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
                CRM_Core_Error::statusBounce( ts('Selected contact(s) does not have a valid email address' ));
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
            CRM_Core_Error::statusBounce( ts('Your user record does not have a valid email address' ));
        }

        if ( ! trim($fromDisplayName) ) {
            $fromDisplayName = $fromEmail;
        }
        
        $from = '"' . $fromDisplayName . '"' . "<$fromEmail>";
        $this->assign( 'from', $from );
        
        //Added for CRM-1393
        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.Select');" );
        $config   =& CRM_Core_Config::singleton( );
        $domainID =  CRM_Core_Config::domainID( );
        
        require_once 'CRM/Member/BAO/MessageTemplates.php';
        $this->_templates = CRM_Member_BAO_MessageTemplates::getMessageTemplates();
        $attributes = array( 'dojoType'       => 'Select',
                             'style'          => 'width: 300px;',
                             'autocomplete'   => 'false',
                             'onValueChanged' => 'selectValue',
                             'dataUrl'        => $config->userFrameworkResourceURL . "extern/ajax.php?q=civicrm/message&d={$domainID}", );

        //if no template Present then drop down select box and update template should not be displayed
        if (! empty( $this->_templates ) ){
            $this->add('select', 'template', ts('Select Template'), null, false, $attributes );
            $this->add('checkbox','updateTemplate',ts('Update Template'), null);
        }

        //insert message Text by selecting "Select Template option"
        $this->add( 'textarea', 'message', ts('Message'), array('cols' => '56', 'rows' => '7','onkeyup' => "return verify(this)"));
         $this->add('checkbox','saveTemplate', ts('Save as New Template'), null, false, array('onclick' => "showSaveDetails(this)"));

         if (!$this->get('saveTemplate') ) {
             $this->add('text','saveTemplateName',ts('Template Title'));
         } 

         //$attributes = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_EmailHistory', 'message' );         
         //$this->add( 'textarea', 'message', ts('Message'), $attributes, true );

         $attributes = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_EmailHistory', 'subject' );
         $this->add( 'text', 'subject', ts('Subject'), $attributes, true );

         if ( $this->_single ) {
             // also fix the user context stack
             $session->replaceUserContext( CRM_Utils_System::url('civicrm/contact/view',
                                                                 "&show=1&action=browse&cid={$this->_contactIds[0]}&selectedChild=activity" ) );
             $this->addDefaultButtons( ts('Send Email'), 'next', 'cancel' );
         } else {
             $this->addDefaultButtons( ts('Send Email') );
         }
         $this->addFormRule( array( 'CRM_Contact_Form_Task_Email', 'formRule' ), $this );

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
     function formRule($fields, $dontCare, $self) {
         $toEmail = CRM_Utils_Array::value( 'to', $fields );
         $errors = array();

         if ($self->_onHold[$toEmail]) {
             $errors['to'] = ts("The selected email address is On Hold because the maximum number of delivery attempts has failed. If you have been informed that the problem with this address is resolved, you can take the address off Hold by editing the contact record. Otherwise, you will need to try an different email address for this contact.");
         }

         //Added for CRM-1393
         if( $fields['saveMessage'] ==1 && empty($fields['saveMessageName']) ){
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
     public function postProcess() {
         $emailAddress = null;
         if ( $this->_single ) {
             $emailAddress = $this->controller->exportValue( 'Email', 'to' );
         }
         
         $cid = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                             $this, false );
         
         if ( $this->_noEmails ) {
             $emailAddress = $this->controller->exportValue( 'Email', 'emailAddress' );

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
         
         $subject = $this->controller->exportValue( 'Email', 'subject' );
         $message = $this->controller->exportValue( 'Email', 'message' );

         //added code for CRM-1393
         $messageParams = $this->exportValues( );
         $templateID = array();

         // Update Message template, if new or updated
         require_once 'CRM/Member/BAO/MessageTemplates.php';
         if( $messageParams['saveTemplate'] || $messageParams['updateTemplate']) {
             if ( $messageParams['saveTemplate'] ) {
                 $newMessage = array( 'msg_title'   => $messageParams['saveTemplateName'],
                                      'msg_text'    => $messageParams['message'],
                                      'msg_subject' => $messageParams['subject'],
                                      'is_active'   => true
                                      );
                 CRM_Member_BAO_MessageTemplates::add($newMessage, $templateID);
             } 
             if ( $messageParams['updateTemplate'] && 
                  CRM_Utils_Array::key( $_POST['template_selected'], $this->_templates ) ) {

                $newMessage = array( 'msg_text'    => $messageParams['message'],
                                     'msg_subject' => $messageParams['subject'],
                                     'is_active'   => true );
                require_once 'CRM/Member/BAO/MessageTemplates.php';
                require_once 'CRM/Utils/Array.php';
                $template = CRM_Member_BAO_MessageTemplates::getMessageTemplates();
                $templateID = array('messageTemplate' =>
                                    CRM_Utils_Array::key( $_POST['template_selected'],
                                                          $this->_templates ) );
                CRM_Member_BAO_MessageTemplates::add($newMessage, $templateID);
            }
        }
        
        $status = array(
                        '',
                        ts('Total Selected Contact(s): %1', array(1 => count($this->_contactIds) ))
                        );
        
        $statusOnHold = '';
        foreach ($this->_contactIds as $item => $contactId) {
            $email     = CRM_Contact_BAO_Contact::getEmailDetails($contactId);
            $allEmails = CRM_Contact_BAO_Contact::allEmails($contactId);

            if ( $allEmails[$email[1]]['is_primary'] && $allEmails[$email[1]]['on_hold'] ) {
                $displayName = CRM_Contact_BAO_Contact::displayName($contactId);
                $contactLink = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid=$contactId");
                unset($this->_contactIds[$item]);
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
        $text = CRM_Utils_Token::replaceDomainTokens( $message,
                                                      $domain, false  );
        
        // send the mail
        require_once 'CRM/Core/BAO/EmailHistory.php';
        list( $total, $sent, $notSent ) = CRM_Core_BAO_EmailHistory::sendEmail( $this->_contactIds, $subject, $text, $emailAddress );
        
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
