<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2007                        |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once '../../civicrm.config.php';

require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/I18n.php';
require_once 'test/RSTest/Common.php';


class test_RSTest_UpdateContact
{
    private $_startID;
    private $_contactArray = array();
    
    function __construct()
    {
    }
    
    /**
     * Getter for the contacts.
     *
     * This method is used for getting the neccessary contacts 
     * for the particular operation.
     *
     * @param   start        int    gives the id of the first contact from the group of contact 
     * @param   noOfContact  int    gives the no of contacts required for the operations.
     * 
     * @access  private
     * @return  contactArray array  gives the array of contacts on which the operations needs to be carried out. 
     *
     */
    
    private function _getContact($start, $noOfContact)
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $contactDAO = new CRM_Contact_DAO_Contact();
        $contactDAO->selectAdd();
        $contactDAO->selectAdd('id');
        $contactDAO->limit($start, $noOfContact);
        $contactDAO->find();
        
        while ($contactDAO->fetch()) {
           $contactArray[]  = $contactDAO->id;
        }
        return $contactArray;
    }
    
    function getZipCodeInfo( ) {
        $offset = mt_rand( 1, 43000 );
        $query = "SELECT zip, latitude, longitude FROM zipcodes LIMIT $offset, 1";
        require_once 'CRM/Core/DAO.php';
        $dao = new CRM_Core_DAO( );
        $dao->query( $query );
        while ( $dao->fetch( ) ) {
            return array( $dao->zip, $dao->latitude, $dao->longitude );
        }
    }
    
    /**
     * Update Location.
     *
     * This method adds new data to the location table for existing contacts.
     * This method can not be called statically.
     *
     * @return  none
     *
     * @access none
     */
    public function updateLocation($setPrimary=0, $contactArray)
    {
        if (is_array($contactArray)) {
            foreach ($contactArray as $lngkey => $contactId) {
                echo ".";
                ob_flush();
                flush();
                
                $this->_addLocation(test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('locationType'), test_RSTest_Common::ARRAY_DIRECT_USE), $contactId, $setPrimary);
            }
        } else {
            echo "Array provided is not valid array";
        }
    }

    /**
     * Add a location. 
     *
     * This method is used to add location to particular Contact.
     * This method can not be called statically.
     *
     * @param   locationType    type of location
     * @param   contactId       id of the contact to which the location will be added
     *
     * @return  none
     *
     * @access  private
     */
    private function _addLocation($locationType, $contactId, $setPrimary, $domain=false)
    {
        require_once 'CRM/Core/DAO/Location.php';
        $locationDAO                   =& new CRM_Core_DAO_Location();
        $locationDAO->is_primary       = $setPrimary;
        $locationDAO->location_type_id = $locationType;
        if ($domain) {
            $locationDAO->entity_id        = $contactId;
            $locationDAO->entity_table     = 'civicrm_domain';
        } else {
            $locationDAO->entity_id        = $contactId;
            $locationDAO->entity_table     = 'civicrm_contact';
        }
        test_RSTest_Common::_insert($locationDAO);
        $this->_addAddress($locationDAO->id);        
                
        // add two phones for each location
        $this->_addPhone($locationDAO->id, test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('phoneType'), test_RSTest_Common::ARRAY_SHIFT_USE), true);
        $this->_addPhone($locationDAO->id, test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('phoneType'), test_RSTest_Common::ARRAY_SHIFT_USE), false);
        
        require_once 'CRM/Contact/DAO/Contact.php';
        // need to get sort name to generate email id
        $contactDAO     =& new CRM_Contact_DAO_Contact();
        $contactDAO->id = $contactId;
        $contactDAO->find(true);
        // get the sort name of the contact
        $sortName       = $contactDAO->sort_name;
        
        // add 2 email for each location
        for ($emailId=1; $emailId<=2; $emailId++) {
            $this->_addEmail($locationDAO->id, $sortName, ($emailId == 1));
        }
    }
    
    /**
     * Add an address. 
     *
     * This method is used to add address to particular location.
     * This method can not be called statically.
     *
     * @param   locationId    id of the location to which the location will be added
     *
     * @return  none
     *
     * @access  private
     */
    private function _addAddress($locationId)
    {
        require_once 'CRM/Core/DAO/Address.php';
        $addressDAO                                    =& new CRM_Core_DAO_Address();
        // add addresses now currently we are adding only 1 address for each location
        $addressDAO->location_id                       = $locationId;
        if ($locationId % 5) {
            $addressDAO->street_number                 = mt_rand(1, 1000);
            $addressDAO->street_number_suffix          = ucfirst(test_RSTest_Common::getRandomChar());
            $addressDAO->street_number_predirectional  = test_RSTest_Common::getRandomElement($this->addressDirection);
            $addressDAO->street_name                   = ucwords(test_RSTest_Common::getRandomElement($this->streetName));
            $addressDAO->street_type                   = test_RSTest_Common::getRandomElement($this->streetType);
            $addressDAO->street_number_postdirectional = test_RSTest_Common::getRandomElement($this->addressDirection);
            $addressDAO->street_address                = $addressDAO->street_number_predirectional . " " . $addressDAO->street_number .  $addressDAO->street_number_suffix .  " " . $addressDAO->street_name .  " " . $addressDAO->street_type . " " . $addressDAO->street_number_postdirectional;
            $addressDAO->supplemental_address_1        = ucwords(test_RSTest_Common::getRandomElement($this->supplementalAddress1));
        }
        // lets do some good skips
        if ($locationId % 9) {
            $addressDAO->postal_code                   = mt_rand(90000, 99999);
        }
        // some more random skips
        if ($locationId) {
            $array1                                    = test_RSTest_Common::getRandomCSC();
            $addressDAO->city                          = $array1[2];
            $addressDAO->state_province_id             = $array1[1];
            $addressDAO->country_id                    = $array1[0];
            $addressDAO->country_id                    = 1228;
                
            // hack add lat / long for US based addresses
            if ( $addressDAO->country_id == '1228' ) {
                list( $addressDAO->postal_code, $addressDAO->geo_code_1, $addressDAO->geo_code_2 ) = self::getZipCodeInfo( );
            }
        }
        $addressDAO->county_id                         = 1;
        $addressDAO->geo_coord_id                      = 1;
        
        test_RSTest_Common::_insert($addressDAO);
    }
    
    /**
     * Add a phone. 
     *
     * This method is used to add phone entity to particular location.
     * This method can not be called statically.
     *
     * @param   locationId    id of the location to which the phone entity will be added
     * @param   phoneType     type of phone
     * @param   primary       marking for this particular phone entity as primary 
     *
     * @return  none
     *
     * @access  private
     */
    private function _addPhone($locationId, $phoneType, $primary=false)
    {
        if ($locationId % 3) {
            require_once 'CRM/Core/DAO/Phone.php';
            $phoneDAO              =& new CRM_Core_DAO_Phone();
            $phoneDAO->location_id = $locationId;
            $phoneDAO->is_primary  = $primary;
            $phoneDAO->phone       = mt_rand(11111111, 99999999);
            $phoneDAO->phone_type  = $phoneType;
            test_RSTest_Common::_insert($phoneDAO);
        }
    }

    /**
     * Add an email. 
     *
     * This method is used to add an email to particular location.
     * This method can not be called statically.
     *
     * @param   locationId    id of the location to which the email will be added
     * @param   sortName      sort name
     * @param   primary       marking for this particular email as primary 
     *
     * @return  none
     *
     * @access  private
     */
    private function _addEmail($locationId, $sortName, $primary=false)
    {
        if ($locationId % 7) {
            require_once 'CRM/Core/DAO/Email.php';
            $emailDAO              =& new CRM_Core_DAO_Email();
            $emailDAO->location_id = $locationId;
            $emailDAO->is_primary  = $primary;
            $emailName             = $this->_sortNameToEmail($sortName);
            $emailDomain           = test_RSTest_Common::getRandomElement($this->emailDomain);
            $tld                   = test_RSTest_Common::getRandomElement($this->emailTLD);
            $emailDAO->email       = $emailName . "@" . $emailDomain . "." . $tld;
            test_RSTest_Common::_insert($emailDAO);
        }
    }

    /**
     * 
     * 
     * 
     * 
     * 
     * @param   sortName    
     *
     * @return  string
     *
     * @access  private
     */
    private function _sortNameToEmail($sortName)
    {
//         $sortName = strtolower(str_replace(" ", "", $sortName));
//         $sortName = strtolower(str_replace(",", "_", $sortName));
//         $sortName = strtolower(str_replace("'s", "_", $sortName));
        $email = preg_replace("([^a-zA-Z0-9_-]*)", "", $sortName);
        return $sortName;
    }

    function run($start, $noOfContact)
    {
        $this->_contactArray = $this->_getContact($start, $noOfContact);
        $this->updateLocation(0,$this->_contactArray);
    }
}
?>