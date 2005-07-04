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

/**
 * This class holds all the Pseudo constants that are specific to Mass mailing. This avoids
 * polluting the core class and isolates the mass mailer class
 */
class CRM_Mailing_PseudoConstant extends CRM_Core_PseudoConstant {

    /**
     * mailing templates
     * @var array
     * @static
     */
    private static $mailingTemplate;

    /**
     * completed mailings
     * @var array
     * @static
     */
    private static $completedMailing;

    /**
     * mailing components
     * @var array
     * @static
     */
    private static $components;

    /**
     * Get all the mailing templates
     *
     * @access public
     * @return array - array reference of all mailing templates if any
     * @static
     */
    public static function &mailingTemplate( ) {
        if ( ! self::$mailingTemplate ) {
            self::populate( self::$mailingTemplate, 'CRM_Mailing_DAO_Mailing', true, 'name', 'is_template' );
        }
        return self::$mailingTemplate;
    }

    /**
     * Get all the completed mailing
     *
     * @access public
     * @return array - array reference of all mailing templates if any
     * @static
     */
    public static function &completedMailing( ) {
        if ( ! self::$completedMailing ) {
            self::populate( self::$completedMailing, 'CRM_Mailing_DAO_Mailing', true, 'name', 'is_completed' );
        }
        return self::$completedMailing;
    }


}

?>
