<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
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

require_once 'CRM/Admin/Form/Setting.php';
require_once 'CRM/Utils/Mail.php';

/**
 * This class generates form components for Smtp Server
 * 
 */
class CRM_Admin_Form_Setting_Smtp extends CRM_Admin_Form_Setting
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        CRM_Utils_System::setTitle(ts('Settings - SMTP Configuration'));
          
        $this->add('text','smtpServer', ts('SMTP Server'), null, true);
        $this->add('text','smtpPort', ts('SMTP Port'), null, true);  
        $this->addYesNo( 'smtpAuth', ts( 'Authentication?' ));
        $this->addElement('text','smtpUsername', ts('SMTP Username')); 
        $this->addElement('password','smtpPassword', ts('SMTP Password')); 
        $this->add('submit','sendTestEmail', ts('Save & Send Test Email') ); 
        $this->addFormRule( array( 'CRM_Admin_Form_Setting_Smtp', 'formRule' ));
        parent::buildQuickForm();
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $formValues   = $this->controller->exportValues($this->_name);

        if ( $formValues['sendTestEmail'] ) {
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext( CRM_Utils_System::url('civicrm/admin/setting/smtp', 'reset=1') );
            $userID  =  $session->get( 'userID' );
            require_once 'CRM/Contact/BAO/Contact.php';
            list( $fromDisplayName, $fromEmail, $fromDoNotEmail ) = CRM_Contact_BAO_Contact::getContactDetails( $userID );
            
            if ( ! $fromEmail ) {
                CRM_Core_Error::statusBounce( ts('Your user record does not have a valid email address' ));
            }
            
            if ( ! trim($fromDisplayName) ) {
                $fromDisplayName = $fromEmail;
            }
            
            $from = '"' . $fromDisplayName . '"' . "<$fromEmail>";
            
            $subject = "Test for SMTP settings";
            $message = "SMTP settings are correct.";
      
            $headers = array(   
                             'From'                      => $from,
                             'To'                        => $from,
                             'Subject'                   => CRM_Utils_Mail::encodeSubjectHeader($subject),  
                             );
            $params['host'] = $formValues['smtpServer'];
            $params['port'] = $formValues['smtpPort'];
            
            if ( $formValues['smtpAuth'] ) {
                $params['username'] = $formValues['smtpUsername'];
                $params['password'] = $formValues['smtpPassword'];
                $params['auth']     = true;
            } else {
                $params['auth']     = false;
            }
            
            $mailer =& Mail::factory( 'smtp', $params );
            CRM_Core_Error::ignoreException( );
            $result = $mailer->send( $fromEmail, $headers, $message );
            if ( !is_a( $result, 'PEAR_Error' ) ) {
                CRM_Core_Session::setStatus( ts('Your SMTP server settings are correct. A test email has been sent to your email address.') ); 
            } else {
                CRM_Core_Session::setStatus( ts('Your SMTP server settings are incorrect. No test mail has been sent.') );
            }
            
        } 
        parent::postProcess();
    }
    
    /**
     * global validation rules for the form
     *
     * @param   array  $fields   posted values of the form
     *
     * @return  array  list of errors to be posted back to the form
     * @static
     * @access  public
     */
    static function formRule( &$fields ) 
    {
        if ( $fields['smtpAuth'] ) {
            if (!$fields['smtpUsername']){
                $errors['smtpUsername'] = 'If your SMTP server require authentication please provide user name.';
            }
            if (!$fields['smtpPassword']) {
                $errors['smtpPassword'] = 'If your SMTP server require authentication please provide password.';
            }
        }
        return empty($errors) ? true : $errors;
    }
}


