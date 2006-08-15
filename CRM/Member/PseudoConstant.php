<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/**
 * This class holds all the Pseudo constants that are specific to Mass mailing. This avoids
 * polluting the core class and isolates the mass mailer class
 */
class CRM_Member_PseudoConstant extends CRM_Core_PseudoConstant {

    /**
     * membership types
     * @var array
     * @static
     */
    private static $membershipType;

    /**
     * membership types
     * @var array
     * @static
     */
    private static $membershipStatus;

    /**
     * Get all the membership types
     *
     * @access public
     * @return array - array reference of all membership types if any
     * @static
     */
    public static function &membershipType($id = null)
    {
        if ( ! self::$membershipType ) {
            CRM_Core_PseudoConstant::populate( self::$membershipType,
                                               'CRM_Member_DAO_MembershipType' );
        }
        if ($id) {
            if (array_key_exists($id, self::$membershipType)) {
                return self::$membershipType[$id];
            } else {
                return null;
            }
        }
        return self::$membershipType;
    }

    /**
     * Get all the membership statuss
     *
     * @access public
     * @return array - array reference of all membership statuss if any
     * @static
     */
    public static function &membershipStatus($id = null)
    {
        if ( ! self::$membershipStatus ) {
            CRM_Core_PseudoConstant::populate( self::$membershipStatus,
                                               'CRM_Member_DAO_MembershipStatus' );
        }
        if ($id) {
            if (array_key_exists($id, self::$membershipStatus)) {
                return self::$membershipStatus[$id];
            } else {
                return null;
            }
        }
        return self::$membershipStatus;
    }
    
}

?>
