<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Admin/Form/Setting.php';
require_once 'CRM/Utils/Mail.php';
require_once "CRM/Core/BAO/Preferences.php";
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
        
        $outBoundOption = array( '0' => ts('SMTP'), '1' => ts('Sendmail'), '2' => ts('Disable Outbound Email') );
        $outBoundOptionExtra = array('onclick' =>"showHideMailOptions();",'onload' =>"showHideMailOptions();");
        
        $this->addRadio('outBound_option', ts('Select Mailer'),  $outBoundOption, $outBoundOptionExtra );

        CRM_Utils_System::setTitle(ts('Settings - Outbound Mail'));
        $this->add('text','sendmail_path', ts('Sendmail Path'));
        $this->add('text','sendmail_args', ts('Sendmail Argument'));
        $this->add('text','smtpServer', ts('SMTP Server'));
        $this->add('text','smtpPort', ts('SMTP Port'));  
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

        if ( CRM_Utils_Array::value( 'sendTestEmail', $formValues ) ) {
            if ( $formValues['outBound_option'] == 2 ) {
                CRM_Core_Session::setStatus( ts('You have selected "Disable Outbound Email". A test email can not be sent.') );
            } else {
                $session =& CRM_Core_Session::singleton( );
                $session->pushUserContext( CRM_Utils_System::url('civicrm/admin/setting/smtp', 'reset=1') );
                $userID  =  $session->get( 'userID' );
                require_once 'CRM/Contact/BAO/Contact.php';
                list( $fromDisplayName, $fromEmail, $fromDoNotEmail ) = CRM_Contact_BAO_Contact::getContactDetails( $userID );
                
                if ( ! $fromEmail ) {
                    CRM_Core_Error::statusBounce( ts('Cannot send a test email because your user record does not have a valid email address.' ));
                }
                
                if ( ! trim($fromDisplayName) ) {
                    $fromDisplayName = $fromEmail;
                }
                
                $from = '"' . $fromDisplayName . '"' . "<$fromEmail>";
          
                if ($formValues['outBound_option'] == 0) {
                    $subject = "Test for SMTP settings";
                    $message = "SMTP settings are correct.";
                    
                    $params['host'] = $formValues['smtpServer'];
                    $params['port'] = $formValues['smtpPort'];
                    
                    if ( $formValues['smtpAuth'] ) {
                        $params['username'] = $formValues['smtpUsername'];
                        $params['password'] = $formValues['smtpPassword'];
                        $params['auth']     = true;
                    } else {
                        $params['auth']     = false;
                    }
                    $mailerName = 'smtp';
                } elseif ($formValues['outBound_option'] == 1) {
                    $subject = "Test for Sendmail settings";
                    $message = "Sendmail settings are correct.";
                    $params['sendmail_path'] = $formValues['sendmail_path'];
                    $params['sendmail_args'] = $formValues['sendmail_args'];
                    $mailerName = 'sendmail';
                }

                $headers = array(   
                                 'From'                      => $from,
                                 'To'                        => $from,
                                 'Subject'                   => CRM_Utils_Mail::encodeSubjectHeader($subject),  
                                 );
                
                $mailer =& Mail::factory( $mailerName, $params );
                
                CRM_Core_Error::ignoreException( );
                $result = $mailer->send( $fromEmail, $headers, $message );
                if ( !is_a( $result, 'PEAR_Error' ) ) {
                    CRM_Core_Session::setStatus( ts('Your %1 settings are correct. A test email has been sent to your email address.', array(1 => strtoupper( $mailerName ) ) ) ); 
                } else {
                    CRM_Core_Session::setStatus( ts('Oops. Your %1 settings are incorrect. No test mail has been sent.', array(1 => strtoupper( $mailerName ) ) ) . '<p class="font-red">' . ts('Error message') . ':<br />' . $result->message . '</p>' );
                }
            }
        } 
        $mailingDomain =& new CRM_Core_DAO_Preferences();
        $mailingDomain->find(true);
        if ( $mailingDomain->mailing_backend ) {
            $values = unserialize( $mailingDomain->mailing_backend );
            CRM_Core_BAO_Setting::formatParams( $formValues, $values );
        }
        $mailingDomain->mailing_backend = serialize( $formValues );
        $mailingDomain->save();
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
        if ($fields['outBound_option'] == 0) {
            if ( !$fields['smtpServer'] ) {
                $errors['smtpServer'] = 'SMTP Server name is a required field.';
            } 
            if ( !$fields['smtpPort'] ) {
                $errors['smtpPort'] = 'SMTP Port is a required field.';
            }
            if ( $fields['smtpAuth'] ) {
                if (!$fields['smtpUsername']){
                    $errors['smtpUsername'] = 'If your SMTP server requires authentication please provide a valid user name.';
                }
                if (!$fields['smtpPassword']) {
                    $errors['smtpPassword'] = 'If your SMTP server requires authentication, please provide a password.';
                }
            }
        }
        if ($fields['outBound_option'] == 1) {
            if ( !$fields['sendmail_path'] ) {
                $errors['sendmail_path'] = 'Sendmail Path is a required field.';
            } 
            if ( !$fields['sendmail_args'] ) {
                $errors['sendmail_args'] = 'Sendmail Argument is a required field.';
            }
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * This function sets the default values for the form.
     * default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        if ( ! $this->_defaults ) {
            $this->_defaults = array( );

            require_once "CRM/Core/DAO/Preferences.php";
            $mailingDomain =& new CRM_Core_DAO_Preferences();
            $mailingDomain->find(true);
            if ( $mailingDomain->mailing_backend ) {
                $this->_defaults = unserialize( $mailingDomain->mailing_backend );     
            } else {
                if ( ! isset( $this->_defaults['smtpServer'] ) ) {
                    $this->_defaults['smtpServer'] = 'localhost';
                    $this->_defaults['smtpPort'  ] = 25;
                    $this->_defaults['smtpAuth'  ] = 0;
                }
                
                if ( ! isset( $this->_defaults['sendmail_path'] ) ) {
                    $this->_defaults['sendmail_path'] = '/usr/bin/sendmail';
                    $this->_defaults['sendmail_args'] = '-i';
                }
            }
        }
        return $this->_defaults;
    }
}


