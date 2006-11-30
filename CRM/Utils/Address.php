<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

/**
 * Address utilties
 */
class CRM_Utils_Address {

    /**
     * format an address string from address fields and a format string
     *
     * Format an address basing on the address fields provided.
     * Use $config->addressFormat if there's no format specified.
     *
     * @param array   $fields       the address fields
     * @param string  $format       the desired address format
     * @param boolean $microformat  if true indicates, the address to be built in hcard-microformat standard.
     *
     * @return string  formatted address string
     *
     * @static
     */
    static function format($fields, $format = null, $microformat = false)
    {
        static $config = null;
        
        if ( ! $format ) {
            if ( ! $config ) {
                $config =& CRM_Core_Config::singleton();
            }
            $format = $config->addressFormat;
        }
                
        $formatted = $format;
        
        $fullPostalCode = $fields['postal_code'];
        if (isset( $fields['postal_code_suffix'] ) ) {
            $fullPostalCode .= "-$fields[postal_code_suffix]";
        }
        
        if (! $microformat) {
            $replacements = array(
                                  'street_address'         => $fields['street_address'],
                                  'supplemental_address_1' => $fields['supplemental_address_1'],
                                  'supplemental_address_2' => $fields['supplemental_address_2'],
                                  'city'                   => $fields['city'],
                                  'state_province'         => $fields['state_province'],
                                  'postal_code'            => $fullPostalCode,
                                  'country'                => $fields['country']
                                  );
        } else {
            $replacements = array(
                                  'street_address'         => "<span class=\"street-address\">" .   $fields['street_address'] . "</span>",
                                  'supplemental_address_1' => "<span class=\"extended-address\">" . $fields['supplemental_address_1'] . "</span>",
                                  'supplemental_address_2' => $fields['supplemental_address_2'],
                                  'city'                   => "<span class=\"locality\">" .         $fields['city'] . "</span>",
                                  'state_province'         => "<span class=\"region\">" .           $fields['state_province'] . "</span>",
                                  'postal_code'            => "<span class=\"postal-code\">" .      $fullPostalCode . "</span>",
                                  'country'                => "<span class=\"country-name\">" .     $fields['country'] . "</span>"
                                  );
            // erase all empty ones, so we dont get blank lines
            foreach ( array_keys( $replacements ) as $key ) {
                if ( $key != 'postal_code' &&
                     CRM_Utils_Array::value( $key, $fields ) == null ) {
                    $replacements[$key] = '';
                }
            }
            if ( empty( $fullPostalCode ) ) {
                $replacements['postal_code'] = '';
            }
        }

        // for every token, replace {fooTOKENbar} with fooVALUEbar if
        // the value is not empty, otherwise drop the whole {fooTOKENbar}
        foreach ($replacements as $token => $value) {
            if ($value) {
                $formatted = preg_replace("/{([^{}]*){$token}([^{}]*)}/u", "\${1}{$value}\${2}", $formatted);
            } else {
                $formatted = preg_replace("/{[^{}]*{$token}[^{}]*}/u", '', $formatted);
            }
        }

        // drop any {...} constructs from lines' ends
        if (! $microformat) {
            $formatted = "\n$formatted\n";
        } else {
            $formatted = "\n<div class=\"vcard\"><span class=\"adr\">$formatted</span></div>\n";
        }
        $formatted = preg_replace('/\n{[^{}]*}/u', "\n", $formatted);
        $formatted = preg_replace('/{[^{}]*}\n/u', "\n", $formatted);

        // if there are any 'sibling' {...} constructs, replace them with the
        // contents of the first one; for example, when there's no state_province:
        // 1. {city}{, }{state_province}{ }{postal_code}
        // 2. San Francisco{, }{ }12345
        // 3. San Francisco, 12345
        $formatted = preg_replace('/{([^{}]*)}({[^{}]*})+/u', '\1', $formatted);

        // drop any remaining curly braces leaving their contents
        $formatted = str_replace(array('{', '}'), '', $formatted);

        // drop any empty lines left after the replacements
        $lines = array();
        foreach (explode("\n", $formatted) as $line) {
            $line = trim($line);
            if ( $line ) {
                $lines[] = $line;
            }
        }
        $formatted = implode("\n", $lines);

        return $formatted;
    }

}

?>
