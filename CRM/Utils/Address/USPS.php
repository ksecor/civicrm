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
class CRM_Utils_Address_USPS {
    
    static function checkAddress(&$values) {
        if ( ! isset($values['street_address'])     || 
              (! isset($values['city'])      &&
               ! isset($values['state_province'])     &&
               ! isset($values['postal_code']) ) ) {
            return false;
        }
        
        $config = new CRM_Core_Config();
        $userID = $config->AddressStdUserID;
        $url = $config->AddressStdURL;

        $address2 = str_replace( ',', '', $values['street_address'] );
        
        $XMLQuery = '<AddressValidateRequest USERID="'.$userID.'"><Address ID="0"><Address1>'.$values['supplemental_address_1'].'</Address1><Address2>'.$address2.'</Address2><City>'.$values['city'].'</City><State>'.$values['state_province'].'</State><Zip5>'.$values['postal_code'].'</Zip5><Zip4>'.$values['postal_code_suffix'].'</Zip4></Address></AddressValidateRequest>';
                
//        $url = 'http://testing.shippingapis.com/ShippingAPITest.dll';
        
        require_once 'HTTP/Request.php';
        $request =& new HTTP_Request( );
        
        $request->setURL($url);
        
        $request->addQueryString('API', 'Verify');
        $request->addQueryString('XML', $XMLQuery);
        
        $response = $request->sendRequest( );
        
        $responseBody = $request->getResponseBody( );
        
        $xml = simplexml_load_string( $responseBody );
        
        if (array_key_exists('Error', $xml->Address)) {
            $session =& CRM_Core_Session::singleton( );
            $session->setStatus( ts( 'Address not found in USPS database.' ) );
            return false;
        }
        
        require_once 'CRM/Core/BAO/Address.php';
        
        $addressBAO = new CRM_Core_BAO_Address();
        $addressBAO->id = $values['address_id'];
        
        if ($addressBAO->find(true)) {
            if (array_key_exists('Address1', $xml->Address)) {
                $addressBAO->supplemental_address_1 = (string)$xml->Address->Address1;
            }
            
            $addressBAO->street_address     = (string)$xml->Address->Address2;
            $addressBAO->city               = (string)$xml->Address->City;
            $addressBAO->state_province     = (string)$xml->Address->State;
            $addressBAO->postal_code        = (string)$xml->Address->Zip5;
            $addressBAO->postal_code_suffix = (string)$xml->Address->Zip4;
            
            $modifiedValues = $addressBAO->save();
            
            $values['street_address']     = $modifiedValues->street_address;
            $values['city']               = $modifiedValues->city;
            $values['state_province']     = $modifiedValues->state_province;
            $values['postal_code']        = $modifiedValues->postal_code;
            $values['postal_code_suffix'] = $modifiedValues->postal_code_suffix;
        }
        
        return true;
    }
}
?>