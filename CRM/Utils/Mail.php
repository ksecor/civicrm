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

    static function send( $from, $toDisplayName, $toEmail, $subject, $message, $cc = null, $bcc = null ) {

        require_once 'CRM/Core/DAO/Domain.php';
        $dao = new CRM_Core_DAO_Domain();
        $dao->id = 1;
        $dao->find(true);
        $returnPath = $dao->email_return_path;

        if (!$returnPath) {
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
        $headers['Reply-To']                  = $from;

        $to = array( $toEmail );
        if ( $cc ) {
            $to[] = $cc;
        }
        if ( $bcc ) {
            $to[] = $bcc;
        }

        $mailer =& CRM_Core_Config::getMailer( );  
        if ($mailer->send($to, $headers, $message) !== true) {  
            return false;                                                    
        } 
        
        return true;
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
