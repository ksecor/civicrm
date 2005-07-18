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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Mailing_BAO_BouncePattern extends CRM_Mailing_DAO_BouncePattern {

    /**
     * Pseudo-constant pattern array
     */
    static $_patterns = null;
    
    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Build the static pattern array
     *
     * @param void
     * @return void
     * @access public
     * @static
     */
    public static function buildPatterns() {
        $bp =& new CRM_Mailing_BAO_BouncePattern();
        $bp->find();
        
        if (! is_array(self::$_patterns)) {
            self::$_patterns = array();
        }

        while ($bp->fetch()) {
            if (! is_array(self::$_patterns[$bp->bounce_type])) {
                self::$_patterns[$bp->bounce_type] = array();
            }
            self::$_patterns[$bp->bounce_type][] = $bp->pattern;
        }

        foreach (self::$_patterns as $type => $patterns) {
            if (count($patterns) == 1) {
                self::$_patterns[$type] = '/' . $patterns[0] . '/';
                continue;
            }
            self::$_patterns[$type] = '/(' . implode(')|(', $patterns) . ')/';
        }
    }

    /**
     * Try to match the string to a bounce type.
     *
     * @param string $message       The message to be matched
     * @return array                Tuple (bounce_type, bounce_reason)
     * @access public
     * @static
     */
    public static function &match($message) {
        if (self::$_patterns == null) {
            self::buildPatterns();
        }
        foreach (self::$_patterns as $type => $re) {
            if (preg_match($re, $string, $matches) {
                return  array(   'bounce_type' => $type, 
                                'bounce_reason' => $matches[0]
                        );
            }
        }
        return array( 'bounce_type' => 'Unknown', 'bounce_reason' => null );
    }

}

?>
