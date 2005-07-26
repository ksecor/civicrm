<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 * Class to abstract token replacement 
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */
 
/*** TODO: figure out some sort of default replacement scheme ***/

class CRM_Utils_Token {
    
    static $_requiredTokens = null;

    static $_tokens = array(
        'action' => array( 
                        'donate', 'forward', 'optOut', 'reply', 'unsubscribe'
                    ),
        'contact' => null,  // populate this dynamically
        'domain' => array( 
                        'phone', 'address', 'email'
                    ),
    );

    
    /**
     * Check a string (mailing body) for required tokens.
     *
     * @param string $str           The message
     * @return true|array           true if all required tokens are found,
     *                              else an array of the missing tokens
     * @access public
     * @static
     */
    public static function &requiredTokens(&$str) {
        if (self::$_requiredTokens == null) {
            self::$_requiredTokens = array(    
                'domain.address'    => ts("Your organization's postal address"),
                'action.optOut'     => ts("Link to allow contacts to opt out of your organization"), 
                'action.unsubscribe'    => ts("Link to allow contacts to unsubscribe from target groups of this mailing."),
            );
        }

        $missing = array();
        foreach (self::$_requiredTokens as $token => $description) {
            if (strpos("{$token}", $str) === false) {
                $missing[$token] = $description;
            }
        }
        if (empty($missing)) {
            return true;
        }
        return $missing;
    }
    
    /**
     * Replace all the domain-level tokens in $str
     *
     * @param string $str       The string with tokens to be replaced
     * @param array $domain     The domain
     * @param boolean $html     Replace tokens with HTML or plain text
     * @return string           The processed string
     * @access public
     * @static
     */
    public static function &replaceDomainTokens($str, &$domain, $html = false)
    {
        
        return $str;
    }

    /**
     * Replace all action tokens in $str
     *
     * @param string $str       The string with tokens to be replaced
     * @param array $addresses  Assoc. array of VERP event addresses
     * @param boolean $html     Replace tokens with HTML or plain text
     * @return string           The processed string
     * @access public
     * @static
     */
    public static function &replaceActionTokens($str, &$addresses, $html = false) {
        foreach (self::$_tokens['action'] as $token) {
            if (strpos($token, $str) === false) {
                continue;
            }

            /* If the token is an email action, use it.  Otherwise, find the
             * appropriate URL */
            if (($value = CRM_Core_Utils_Array::value($token, $addresses)) == null) {
                /* Get $value from the URL constructor */
            } else {
                if ($html) {
                    $value = "mailto:$value";
                }
            }
            $str = preg_replace('/'.preg_quote("{action.$token}").'/', 
                                $value, $str);
        }
        return $str;
    }


    /**
     * Replace all the contact-level tokens in $str with information from
     * $contact.
     *
     * @param string $str       The string with tokens to be replaced
     * @param array $contact    Associative array of contact properties
     * @param boolean $html     Replace tokens with HTML or plain text
     * @return string           The processed string
     * @access public
     * @static
     */
    public static function &replaceContactTokens($str, &$contact, $html = false) {
        
        if (self::$_tokens['contact'] == null) {
            /* This should come from UF */
            self::$_tokens['contact'] =& 
                array_keys(CRM_Contact_BAO_Contact::importableFields());
        }
        
        $cv =& CRM_Core_BAO_CustomValue::getContactValues($contact['id']);
        foreach (self::$_tokens['contact'] as $token) {
            if ($token == '') {
                continue;
            }
            /* If the string doesn't contain this token, skip it. */
            if (strpos($str, "{contact.$token}") === false) {
                continue;
            }

            /* Construct value from $token and $contact */
            $value = null;
            
            if ($cfID = CRM_Core_BAO_CustomField::getKeyID($token)) {
                foreach ($cv as $customValue) {
                    if ($customValue->custom_field_id == $cfID) {
                        $value = $customValue->getValue();
                        break;
                    }
                }
            } else {
                $value = CRM_Contact_BAO_Contact::retrieveValue(
                            $contact, $token);
            }
            
            if ($value) {
                $str = preg_replace('/'.preg_quote("{contact.$token}").'/', 
                                    $value, $str);
            }
        }

        return $str;
    }

    /**
     * Find unprocessed tokens (call this last)
     *
     * @param string $str       The string to search
     * @return array            Array of tokens that weren't replaced
     * @access public
     * @static
     */
    public static function &unmatchedTokens(&$str) {
        preg_match_all('/\{(.*?)\}/', $str, $match);
        return $match[1];
    }
}

?>
