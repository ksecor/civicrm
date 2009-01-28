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


class CRM_Utils_Mail {

    /**
     * encodes an UTF-8 string into an RFC-compliant Subject: header's body
     *
     * param string $subject  the subject that should be encoded
     * return string          the encoded Subject: header load
     */
    static function encodeSubjectHeader($subject)
    {
        // encode the subject
        // - if it contains CR, LF or non-US-ASCII
        // - if it contains the string =?
        // - if the full header line would be longer than 998 characters
        if (substr_count($subject, "\r") or substr_count($subject, "\n")
            or preg_match('/[^\x00-\x7f]/', $subject)
            or substr_count($subject, '=?')
            or strlen($subject) > 998 - strlen('Subject: ')) {

            $encoded = base64_encode($subject);

            // the encoded header lines cannot be longer than 76 characters
            // simply do it like mutt does - first line contains 32 characters
            // of the encoded payload, each of the subsequent ones the next
            // 60 characters
            $lines = array();
            $lines[] = '=?utf-8?B?' . substr($encoded, 0, 32) . '?=';
            $rest = substr($encoded, 32);
            while ($rest != '') {
                $lines[] = '=?utf-8?B?' . substr($rest, 0, 60) . '?=';
                $rest = substr($rest, 60);
            }

            return implode("\n\t", $lines);

        } else {
            return $subject;
        }
    }



    /**
     * encodes an UTF-8 name+email pair into an RFC-compliant From:/To: header's body
     *
     * go the easy route and either make the header into
     * "Adressee's Name" <address@example.com>
     * or (if the above is not possible) base64-encode the name
     *
     * param string $name   the name that should be encoded
     * param string $email  the email that should be encoded
     * return string        the encoded To: header load
     */
    static function encodeAddressHeader($name, $email)
    {
        // a 'plain' name can only contain a certain subset of US-ASCII
        if (preg_match('/[^\x01-\x08\x0b\x0c\x0e-\x7f]/', $name)
            or substr_count($name, '=?')) {

            $encoded = base64_encode($name);

            // do what mutt does - split the payload into 60-character
            // parts and separate these with spaces in the final header
            $parts = array();
            $rest = $encoded;
            while ($rest != '') {
                $parts[] = '=?utf-8?B?' . substr($encoded, 0, 60) . '?=';
                $rest = substr($rest, 60);
            }

            return implode(' ', $parts) . " <$email>";

        } else {
            // it's a 'plain' name - escape any backslashes and double quotes
            // found and build an "Adressee's Name" <address@example.com>
            // header
            $name = str_replace('\\', '\\\\', $name);
            $name = str_replace('"', '\"', $name);
            return "\"$name\" <$email>";
        }
    }

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
        $headers['To']                        = self::encodeAddressHeader($toDisplayName, $toEmail);  
        $headers['Cc']                        = $cc;
        $headers['Subject']                   = self::encodeSubjectHeader($subject);  
        $headers['Content-Type']              = 'text/plain; charset=utf-8';  
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
        $msg = & new Mail_Mime("\n");
        $msg->setTxtBody( $text_message );
        $msg->setHTMLBody( $html_message );

        if ( ! empty( $attachments ) ) {
            foreach ( $attachments as $fileID => $attach ) {
                $msg->addAttachment( $attach['fullPath'],
                                     $attach['mime_type'],
                                     $attach['cleanName'] );
            }
        }
        
        $mailMimeParams = array(
                                'text_encoding' => '8bit',
                                'html_encoding' => '8bit',
                                'head_charset'  => 'utf-8',
                                'text_charset'  => 'utf-8',
                                'html_charset'  => 'utf-8',
                                );
        $msg->get($mailMimeParams);
        $msg->headers($headers);
        $message   =& $msg->get();
        $headers =& $msg->headers();
        $result = null;
        $mailer =& CRM_Core_Config::getMailer( );
        CRM_Core_Error::ignoreException( );
        if ( is_object( $mailer ) ) {
            $result = $mailer->send($to, $headers, $message);
            CRM_Core_Error::setCallback()
;
            if ( is_a( $result, 'PEAR_Error' ) ) {
                if ( is_a( $mailer , 'Mail_smtp' ) ) {
                    $message =
                        '<p>' . ts('A error occurred when CiviCRM attempted to send an email (via SMTP). If you received this error after submitted on online contribution or event registration - the transaction was completed, but we were unable to send the email receipt.') . '</p>' .
                        '<p>'  . ts('This is probably related to a problem in your Outbound Email Settings (Administer CiviCRM &raquo; Global Settings &raquo; Outbound Email). Possible causes are:') . '</p>' .
                        '<ul>' .
                            '<li>' . ts('Your SMTP Username or Password are incorrect.')                                   . '</li>' .
                            '<li>' . ts('Your SMTP Server (machine) name is incorrect.')                                   . '</li>' .
                            '<li>' . ts('You need to use an Port other than the default port 25 in your environment.')     . '</li>' .
                            '<li>' . ts('Your SMTP server is just not responding right now (it is down for some reason).') . '</li>' .
                        '</ul>' .
                        '<p>' . ts('Check <a href="%1">this page for more information.</a>', array(1 => CRM_Utils_System::docURL2('Outbound Email (SMTP)', true))) . '</p>' .
                        '<p>' . ts('The mail library returned the following error message:') . $result->getMessage() . '</p>';
                } else {
                    $message =
                        '<p>' . ts('A error occurred when CiviCRM attempted to send an email (via Sendmail. If you received this error after submitted on online contribution or event registration - the transaction was completed, but we were unable to send the email receipt.') . '</p>' .
                        '<p>' . ts('This is probably related to a problem in your Outbound Email Settings (Administer CiviCRM &raquo; Global Settings &raquo; Outbound Email). Possible causes are:') . '</p>' .
                        '<ul>' .
                            '<li>' . ts('Your Sendmail path is incorrect.')     . '</li>' .
                            '<li>' . ts('Your Sendmail argument is incorrect.') . '</li>' .
                        '</ul>' .
                        '<p>' . ts('The mail library returned the following error message:') . $result->getMessage() . '</p>';
                }
                CRM_Core_Session::setStatus( $message );
                return false;
            }
        }
        return true;
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
            file_put_contents( CIVICRM_MAIL_LOG,
                               $content );
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

}


