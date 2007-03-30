<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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



/**
 * Money utilties
 */
class CRM_Utils_Money {
    static $_currencySymbols = null;

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
        if ( CRM_Utils_System::isNull( $amount ) ) {
            return '';
        }

        $config = CRM_Core_Config::singleton( );

        if ( !self::$_currencySymbols ) {
            require_once "CRM/Core/PseudoConstant.php";
            $currencySymbolName = CRM_Core_PseudoConstant::currencySymbols( 'name' );
            $currencySymbol     = CRM_Core_PseudoConstant::currencySymbols( );
            
            self::$_currencySymbols =
                CRM_Utils_Array::combine( $currencySymbolName, $currencySymbol );
        }

        if (!$currency) {
            $currency = $config->defaultCurrency;
        }

        if (!$format) {
            $format = $config->moneyformat;
        }

        $money = $amount;
        // this function exists only in certain php install (CRM-650)
        if ( function_exists( 'money_format' ) ) {
            $money = money_format('%!i', $amount);
        }

        $replacements = array(
                              '%a' => $money,
                              '%C' => $currency,
                              '%c' => CRM_Utils_Array::value($currency, self::$_currencySymbols, $currency),
                              );

        return strtr($format, $replacements);
    }

}

?>
