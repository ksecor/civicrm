<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 * Money utilties
 */
class CRM_Utils_Money {

    /**
     * currency symbols
     * @var array
     * @static
     */
    private static $_currencySymbols;

    /**
     * given a float and ISO currency code, format a monetary string
     *
     * @param float  $amount    the monetary amount to display (1234.56)
     * @param string $currency  the three-letter ISO currency code ('USD')
     *
     * @return string  formatted monetary string
     *
     * @static
     */
    static function format($amount, $currency = 'USD')
    {
        // just a placeholder, not a real solution, of course;
        // we'll use a (sub?)set of LC_MONETARY, setlocale(),
        // money_format(), localeconv() and nuber_format()
        return self::currencySymbols($currency) . ' ' . $amount;
    }

    /**
     * convert ISO currency code to currency symbol (USD => $) or return the conversion table
     *
     * @var string $isoCode  the ISO code of the desired currency symbol
     * @return string|array  the currency symbol, the ISO code (if no symbol
     *                       present) or the whole array (if no ISO code provided)
     *
     * @static
     */
    public static function currencySymbols($isoCode = null) {
        if (!self::$_currencySymbols) {
            self::$_currencySymbols = array('EUR' => '€', 'GBP' => '£', 'ILS' => '₪', 'JPY' => '¥', 'KRW' => '₩', 'LAK' => '₭',
                                            'MNT' => '₮', 'NGN' => '₦', 'THB' => '฿', 'USD' => '$', 'VND' => '₫');
        }
        if ($isoCode) {
            return CRM_Utils_Array::value($isoCode, self::$_currencySymbols, $isoCode);
        }
        return self::$_currencySymbols;
    }


}

?>
