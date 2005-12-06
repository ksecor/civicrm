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
     * format a monetary string
     *
     * Format a monetary string basing on the amount provided,
     * ISO currency code provided and a format string consisting of:
     *
     * %a - the formatted amount
     * %C - the currency ISO code (e.g., 'USD') if provided
     * %c - the currency symbol (e.g., '$') if available
     *
     * @param float  $amount    the monetary amount to display (1234.56)
     * @param string $currency  the three-letter ISO currency code ('USD')
     * @param string $format    the desired currency format
     *
     * @return string  formatted monetary string
     *
     * @static
     */
    static function format($amount, $currency = null, $format = null)
    {
        static $config          = null;
        static $currencySymbols = null;

        if (!$currencySymbols) {
            $currencySymbols = array('EUR' => '€', 'GBP' => '£', 'ILS' => '₪', 'JPY' => '¥', 'KRW' => '₩', 'LAK' => '₭',
                                     'MNT' => '₮', 'NGN' => '₦', 'PLN' => 'zł', 'THB' => '฿', 'USD' => '$', 'VND' => '₫');
        }

        if (!$currency) {
            if (!$config) $config =& CRM_Core_Config::singleton();
            $currency = $config->defaultCurrency;
        }

        if (!$format) {
            if (!$config) $config =& CRM_Core_Config::singleton();
            $format = $config->moneyformat;
        }

        $replacements = array(
            '%a' => money_format('%!i', $amount),
            '%C' => $currency,
            '%c' => CRM_Utils_Array::value($currency, $currencySymbols, $currency),
        );

        return strtr($format, $replacements);
    }

}

?>
