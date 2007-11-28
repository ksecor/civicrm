<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
 * Address utilties
 */
class CRM_Utils_Address_USPS {
    
    static function checkAddress( &$values ) {
        CRM_Utils_System::checkPHPVersion( 5, true );
        
        if ( ! isset($values['street_address'])     || 
               ( ! isset($values['city']           )   &&
                 ! isset($values['state_province'] )   &&
                 ! isset($values['postal_code']    )      ) ) {
            return false;
        }
        
        require_once 'CRM/Core/BAO/Preferences.php';
        $userID = CRM_Core_BAO_Preferences::value( 'address_standardization_userid' );
        $url    = CRM_Core_BAO_Preferences::value( 'address_standardization_url'    );
        
        $address2 = str_replace( ',', '', $values['street_address'] );
        
        $XMLQuery = '<AddressValidateRequest USERID="'.$userID.'"><Address ID="0"><Address1>'.$values['supplemental_address_1'].'</Address1><Address2>'.$address2.'</Address2><City>'.$values['city'].'</City><State>'.$values['state_province'].'</State><Zip5>'.$values['postal_code'].'</Zip5><Zip4>'.$values['postal_code_suffix'].'</Zip4></Address></AddressValidateRequest>';
                
        require_once 'HTTP/Request.php';
        $request =& new HTTP_Request( );
        
        $request->setURL($url);
        
        $request->addQueryString('API', 'Verify');
        $request->addQueryString('XML', $XMLQuery);
        
        $response = $request->sendRequest( );
        
        $responseBody = $request->getResponseBody( );
        
        $xml = simplexml_load_string( $responseBody );

        $session =& CRM_Core_Session::singleton( );

        if ( $xml->Number == '80040b1a' ) {
            $session->setStatus( ts( 'Your API Authorization is Failed.' ) );
            return false;
        }
        
        if (array_key_exists('Error', $xml->Address)) {
            $session->setStatus( ts( 'Address not found in USPS database.' ) );
            return false;
        }
                
        $values['street_address']     = (string)$xml->Address->Address2;
        $values['city']               = (string)$xml->Address->City;
        $values['state_province']     = (string)$xml->Address->State;
        $values['postal_code']        = (string)$xml->Address->Zip5;
        $values['postal_code_suffix'] = (string)$xml->Address->Zip4;
        
        if (array_key_exists('Address1', $xml->Address)) {
            $values['supplemental_address_1'] = (string)$xml->Address->Address1;
        }
                
        return true;
    }
}
?>
