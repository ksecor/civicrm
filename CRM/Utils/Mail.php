<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
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


class CRM_Utils_Mail
{
    static function send( $from,
                          $toDisplayName,
                          $toEmail,
                          $subject,
                          $text_message = null,
                          $cc = null,
                          $bcc = null,
                          $replyTo = null,
                          $html_message = null,
                          $attachments = null ) {
        $returnPath = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_MailSettings', 1, 'return_path', 'is_default');
        if ( ! $returnPath ) {
            $returnPath = self::pluckEmailFromHeader($from);
        }

        $headers = array( );  
        $headers['From']                      = $from;
        $headers['To']                        = "$toDisplayName <$toEmail>";
        $headers['Cc']                        = $cc;
        $headers['Subject']                   = $subject;
        $headers['Content-Type']              = $html_message ? 'multipart/mixed; charset=utf-8' : 'text/plain; charset=utf-8';
        $headers['Content-Disposition']       = 'inline';  
        $headers['Content-Transfer-Encoding'] = '8bit';  
        $headers['Return-Path']               = $returnPath;
        $headers['Reply-To']                  = isset($replyTo) ? $replyTo : $from;
        $headers['Date']                      = date('r');

        $to = array( $toEmail );
        if ( $cc ) {
            $to[] = $cc;
        }

        if ( $bcc ) {
            $to[] = $bcc;
        }

        // we need to wrap Mail_mime because PEAR is apparently unable to fix
        // a six-year-old bug (PEAR bug #30) in Mail_mime::_encodeHeaders()
        // this fixes CRM-4631
        require_once 'CRM/Utils/Mail/FixedMailMIME.php';
        $msg = new CRM_Utils_Mail_FixedMailMIME("\n");
        $msg->setTxtBody( $text_message );
        $msg->setHTMLBody( $html_message );

        if ( ! empty( $attachments ) ) {
            foreach ( $attachments as $fileID => $attach ) {
                $msg->addAttachment( $attach['fullPath'],
                                     $attach['mime_type'],
                                     $attach['cleanName'] );
            }
        }
        
        $message =  self::setMimeParams( $msg );
        $headers =& $msg->headers($headers);

        $result = null;
        $mailer =& CRM_Core_Config::getMailer( );
        CRM_Core_Error::ignoreException( );
        if ( is_object( $mailer ) ) {
            $result = $mailer->send($to, $headers, $message);
            CRM_Core_Error::setCallback();
            if ( is_a( $result, 'PEAR_Error' ) ) {
                $message = self::errorMessage ($mailer, $result );
                CRM_Core_Session::setStatus( $message, false );
                return false;
            }
            return true;
        }
        return false;
    }

    static function errorMessage( $mailer, $result ) {
        $message =
        '<p>'  . ts('An error occurred when CiviCRM attempted to send an email (via %1). If you received this error after submitting on online contribution or event registration - the transaction was completed, but we were unable to send the email receipt.', array(1 => 'SMTP')) . '</p>' .
        '<p>'  . ts('The mail library returned the following error message:') . '<br /><span class="font-red"><strong>' . $result->getMessage() . '</strong></span></p>' .
        '<p>'  . ts('This is probably related to a problem in your Outbound Email Settings (Administer CiviCRM &raquo; Global Settings &raquo; Outbound Email), OR the FROM email address specifically configured for your contribution page or event. Possible causes are:') . '</p>';

        if ( is_a( $mailer , 'Mail_smtp' ) ) {
            $message .=
            '<ul>' .
            '<li>' . ts('Your SMTP Username or Password are incorrect.')                                                                            . '</li>' .
            '<li>' . ts('Your SMTP Server (machine) name is incorrect.')                                                                            . '</li>' .
            '<li>' . ts('You need to use a Port other than the default port 25 in your environment.')                                               . '</li>' .
            '<li>' . ts('Your SMTP server is just not responding right now (it is down for some reason).')                                          . '</li>';
        } else {
            $message .=
            '<ul>' .
            '<li>' . ts('Your Sendmail path is incorrect.')     . '</li>' .
            '<li>' . ts('Your Sendmail argument is incorrect.') . '</li>';
        }
        
        $message .=
            '<li>' . ts('The FROM Email Address configured for this feature may not be a valid sender based on your email service provider rules.') . '</li>' .
            '</ul>' .
            '<p>' . ts('Check <a href="%1">this page</a> for more information.', array(1 => CRM_Utils_System::docURL2('Outbound Email (SMTP)', true))) . '</p>';
        
        return $message;
    }
    
    function logger( &$to, &$headers, &$message ) {
        if ( is_array( $to ) ) {
            $toString = implode( ', ', $to ); 
            $fileName = $to[0];
        } else {
            $toString = $fileName = $to;
        }
        $content = "To: " . $toString . "\n";
        foreach ( $headers as $key => $val ) {
            $content .= "$key: $val\n";
        }
        $content .= "\n" . $message . "\n";

        if ( is_numeric( CIVICRM_MAIL_LOG ) ) {
            $config =& CRM_Core_Config::singleton( );
            // create the directory if not there
            $dirName = $config->uploadDir . 'mail' . DIRECTORY_SEPARATOR;
            CRM_Utils_File::createDir( $dirName );
            $fileName = md5( uniqid( CRM_Utils_String::munge( $fileName ) ) ) . '.txt';
            file_put_contents( $dirName . $fileName,
                               $content );
        } else {
            $current = file_get_contents( CIVICRM_MAIL_LOG );
            $current .= $content;
            file_put_contents( CIVICRM_MAIL_LOG, $current);
        }
    }

    /**
     * Get the email address itself from a formatted full name + address string
     *
     * Ugly but working.
     *
     * @param  string $header  the full name + email address string
     * @return string          the plucked email address
     */
    function pluckEmailFromHeader($header) {
        preg_match('/<([^<]*)>$/', $header, $matches);
        return $matches[1];
    }
    
    /**
     * Get the Active outBound email 
     * @return boolean true if valid outBound email configuration found, false otherwise
     * @access public
     * @static
     */
    static function validOutBoundMail() {
        require_once "CRM/Core/BAO/Preferences.php";
        $mailingInfo =& CRM_Core_BAO_Preferences::mailingPreferences();
        if ( $mailingInfo['outBound_option'] == 0 ) {
            if ( !isset( $mailingInfo['smtpServer'] ) || $mailingInfo['smtpServer'] == '' || 
                 $mailingInfo['smtpServer'] == 'YOUR SMTP SERVER'|| 
                 ( $mailingInfo['smtpAuth'] && ( $mailingInfo['smtpUsername'] == '' || $mailingInfo['smtpPassword'] == '' ) ) ) {
                return false;
            }
            return true;
        } else if ( $mailingInfo['outBound_option'] == 1 ) {
            if ( ! $mailingInfo['sendmail_path'] || ! $mailingInfo['sendmail_args'] ) {
                return false;
            }
            return true;
        }
        return false;        
    }

    static function &setMimeParams( &$message, $params = null ) {
        static $mimeParams = null;
        if ( ! $params ) {
            if ( ! $mimeParams ) {
                $mimeParams = array(
                                    'text_encoding' => '8bit',
                                    'html_encoding' => '8bit',
                                    'head_charset'  => 'utf-8',
                                    'text_charset'  => 'utf-8',
                                    'html_charset'  => 'utf-8',
                                    );
            }
            $params = $mimeParams;
        }
        return $message->get( $params );
    }

}


