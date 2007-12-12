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
                          $message,
                          $cc = null,
                          $bcc = null,
                          $replyTo = null  ) {
        $returnPath = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Domain',
                                                   CRM_Core_Config::domainID( ),
                                                   'email_return_path' );
        if ( ! $returnPath ) {
            $returnPath = self::_pluckEmailFromHeader($from);
        }

        $headers = array( );  
        $headers['From']                      = $from;
        $headers['To']                        = self::encodeAddressHeader($toDisplayName, $toEmail);  
        $headers['Cc']                        = $cc;
        $headers['Bcc']                       = $bcc;
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

        // $to = array( 'dggreenberg@gmail.com', 'donald.lobo@gmail.com' );
        $mailer =& CRM_Core_Config::getMailer( );  
        if ($mailer->send($to, $headers, $message) !== true) {  
            return false;                                                    
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
    private function _pluckEmailFromHeader($header) {
        preg_match('/<([^<]*)>$/', $header, $matches);
        return $matches[1];
    }
}

?>
