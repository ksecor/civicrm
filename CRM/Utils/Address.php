<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/


/**
 * Address utilties
 */
class CRM_Utils_Address 
{

    /**
     * format an address string from address fields and a format string
     *
     * Format an address basing on the address fields provided.
     * Use Preferences::address_format if there's no format specified.
     *
     * @param array   $fields            the address fields
     * @param string  $format            the desired address format
     * @param boolean $microformat       if true indicates, the address to be built in hcard-microformat standard.
     * @param boolean $mailing           if true indicates, the call has been made from mailing label
     * @param boolean $individualFormat  if true indicates, the call has been made for the contact of type 'individual'
     *
     * @return string  formatted address string
     *
     * @static
     */
    static function format($fields,
                           $format = null,
                           $microformat = false,
                           $mailing = false,
                           $individualFormat = false,
                           $tokenFields = null )
    {
        static $config = null;
        require_once 'CRM/Core/BAO/Preferences.php';
        
        if ( ! $format ) {
            $format = CRM_Core_BAO_Preferences::value( 'address_format' );
            $format = str_replace('contact.',"",$format);
        }

        if ( $mailing ) {
            $format = CRM_Core_BAO_Preferences::value( 'mailing_format' );
            $format = str_replace('contact.',"",$format);
        }

        $formatted = $format;

        $fullPostalCode = CRM_Utils_Array::value( 'postal_code', $fields );
        if (!empty( $fields['postal_code_suffix'] ) ) {
            $fullPostalCode .= "-$fields[postal_code_suffix]";
        }

        // make sure that some of the fields do have values
        $emptyFields = array( 'supplemental_address_1',
                              'supplemental_address_2',
                              'state_province_name',
                              'county' );
        foreach ( $emptyFields as $f ) {
            if ( ! isset( $fields[$f] ) ) {
                $fields[$f] = null;
            }
        }

        $contactName = CRM_Utils_Array::value( 'display_name', $fields );
        if ( ! $individualFormat ) {  
            require_once "CRM/Contact/BAO/Contact.php"; 
            if ( isset( $fields['id'] ) ) {
                $type = CRM_Contact_BAO_Contact::getContactType($fields['id']);
            } else {
                $type = 'Individual';
            }

            if ( $type == 'Individual' ) {
                $format = CRM_Core_BAO_Preferences::value( 'individual_name_format' );
                $format = str_replace('contact.',"",$format);
                $contactName = self::format($fields, $format, null, null, true);
            }
        }

        if (! $microformat) {
            $replacements =
                array( // replacements in case of Individual Name Format
                      'contact_name'           => $contactName,
                      'display_name'           => CRM_Utils_Array::value( 'display_name', $fields ),
                      'individual_prefix'      => CRM_Utils_Array::value( 'individual_prefix', $fields ),
                      'first_name'             => CRM_Utils_Array::value( 'first_name', $fields ),
                      'middle_name'            => CRM_Utils_Array::value( 'middle_name', $fields ),
                      'last_name'              => CRM_Utils_Array::value( 'last_name', $fields ),
                      'individual_suffix'      => CRM_Utils_Array::value( 'individual_suffix', $fields ),
                      'street_address'         => CRM_Utils_Array::value( 'street_address', $fields ),
                      'supplemental_address_1' => CRM_Utils_Array::value( 'supplemental_address_1', $fields ),
                      'supplemental_address_2' => CRM_Utils_Array::value( 'supplemental_address_2', $fields ),
                      'city'                   => CRM_Utils_Array::value( 'city', $fields ),
                      'state_province_name'    => CRM_Utils_Array::value( 'state_province_name', $fields ),
                      'county'                 => CRM_Utils_Array::value( 'county', $fields ),
                      'state_province'         => CRM_Utils_Array::value( 'state_province', $fields ),
                      'postal_code'            => $fullPostalCode,
                      'country'                => CRM_Utils_Array::value( 'country', $fields ),
                      'world_region'           => CRM_Utils_Array::value( 'world_region', $fields ),
                      'current_employer'       => CRM_Utils_Array::value( 'current_employer', $fields )
                      );
        } else {
            $replacements =
                array(
                      'address_name'           => "<span class=\"address-name\">" .     $fields['address_name'] . "</span>",
                      'street_address'         => "<span class=\"street-address\">" .   $fields['street_address'] . "</span>",
                      'supplemental_address_1' => "<span class=\"extended-address\">" . $fields['supplemental_address_1'] . "</span>",
                      'supplemental_address_2' => $fields['supplemental_address_2'],
                      'city'                   => "<span class=\"locality\">" .         $fields['city'] . "</span>",
                      'state_province_name'    => "<span class=\"region\">" .           $fields['state_province_name'] . "</span>",
                      'county'                 => "<span class=\"region\">" .           $fields['county'],
                      'state_province'         => "<span class=\"region\">" .           $fields['state_province'] . "</span>",
                      'postal_code'            => "<span class=\"postal-code\">" .      $fullPostalCode . "</span>",
                      'country'                => "<span class=\"country-name\">" .     $fields['country'] . "</span>",
                      'world_region'           => "<span class=\"region\">" .           $fields['world_region'] . "</span>"
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
        
        // replacements in case of Custom Token
        if ( stristr( $formatted ,'custom_' ) ) {
            $customToken = array_keys( $fields );
            foreach( $customToken as $value ) {
                if ( substr( $value,0,7 ) == 'custom_' ) {
                    $replacements["{$value }"] = $fields["{$value}"];  
                }
            }
        }

        // also sub all token fields
        if ( $tokenFields ) {
            foreach ( $tokenFields as $token ) {
                $replacements["{$token}"] = CRM_Utils_Array::value( "{$token}", $fields );
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
            if( $microformat === 1) {
                $formatted = "\n<div class=\"location vcard\"><span class=\"adr\">$formatted</span></div>\n";
            } else {
                $formatted = "\n<div class=\"vcard\"><span class=\"adr\">$formatted</span></div>\n";
            }
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


