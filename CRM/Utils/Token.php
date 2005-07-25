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

class CRM_Utils_Token {


    static $_contactTokens = null;
    
    
    
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
        
        if (self::$_contactTokens == null) {
            self::$_contactTokens =& 
            /* This should come from UF */
                CRM_Contact_BAO_Contact::importableFields();
        }
        
        $cv =& CRM_Core_BAO_CustomValue::getContactValues($contact['id']);
        foreach (self::$_contactTokens as $token => $property) {
            if ($token == '') {
                continue;
            }

            /* Construct value from $token and $contact */
            $value = null;
            
            if (preg_match('/custom_(\d+)/', $token, $match)) {
                foreach ($cv as $customValue) {
                    if ($customValue->custom_field_id == $match[0]) {
                        $value = $customValue->getValue();
                        break;
                    }
                }
            } else {
                $value = self::dfsMatch($contact, $token);
            }
            
            if ($value) {
                $str = preg_replace("/\{$token\}/", $str, $value);
            }
        }

        return $str;
    }


    public static function dfsMatch(&$values, $key) {
        if (! is_array($values)) {
            return null;
        } else if ($value = CRM_Core_Utils_Array::value($key, $values)) {
            return $value;
        } else {
            foreach ($values as $v) {
                if ($value = self::dfsMatch($v, $key)) {
                    return $value;
                }
            }
        }
        return null;
    }
}

?>
