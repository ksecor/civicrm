<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

/**
 * This class holds all the Pseudo constants that are specific to Mass mailing. This avoids
 * polluting the core class and isolates the mass mailer class
 */
class CRM_Contribute_PseudoConstant extends CRM_Core_PseudoConstant {

    /**
     * contribution types
     * @var array
     * @static
     */
    private static $contributionType;

    /**
     * payment instruments
     * @var array
     * @static
     */
    private static $paymentInstrument;

    /**
     * Get all the contribution types
     *
     * @access public
     * @return array - array reference of all contribution types if any
     * @static
     */
    public static function &contributionType($id = null)
    {
        if ( ! self::$contributionType ) {
            CRM_Core_PseudoConstant::populate( self::$contributionType,
                                               'CRM_Contribute_DAO_ContributionType' );
        }
        if ($id) {
            if (array_key_exists($id, self::$contributionType)) {
                return self::$contributionType[$id];
            } else {
                return null;
            }
        }
        return self::$contributionType;
    }

    /**
     * Get all the payment instruments
     *
     * @access public
     * @return array - array reference of all payment instruments if any
     * @static
     */
    public static function &paymentInstrument($id = null)
    {
        if ( ! self::$paymentInstrument ) {
            CRM_Core_PseudoConstant::populate( self::$paymentInstrument,
                                               'CRM_Contribute_DAO_PaymentInstrument' );
        }
        if ($id) {
            if (array_key_exists($id, self::$paymentInstrument)) {
                return self::$paymentInstrument[$id];
            } else {
                return null;
            }
        }
        return self::$paymentInstrument;
    }

}

?>
