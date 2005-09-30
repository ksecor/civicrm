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
 *
 * 
 * 
 * 
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */
require_once '../../modules/config.inc.php';
require_once '../../CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'test/RSTest/Common.php';
require_once 'CRM/Core/I18n.php';

class test_RSTest_GenDataset
{
    // private variables
    private $numContact           = 0;
    // number of particular contacts out of total number of contacts 
    private $numIndividual        = 0;
    private $numHousehold         = 0;
    private $numOrganization      = 0;

    private $numStrictIndividual  = 0;

    private $_startID;

    // sample data in XML format
    private $sampleData           = null;
    
    // store domain ids
    private $domain               = array();

    // store contact ids
    private $contact              = array();
    private $individual           = array();
    private $household            = array();
    private $organization         = array();

    // store names, firstnames, street 1, street 2
    private $firstName            = array();
    private $lastName             = array();
    private $streetName           = array();
    private $supplementalAddress1 = array();
    private $city                 = array();
    private $state                = array();
    private $country              = array();
    private $addressDirection     = array();
    private $streetType           = array();
    private $emailDomain          = array();
    private $emailTLD             = array();
    private $oraganizationName    = array();
    private $oraganizationField   = array();
    private $organizationType     = array();
    private $group                = array();
    private $note                 = array();
    private $activity_type        = array();
    private $module               = array();
    private $callback             = array();


    // store strict individual id and household id to individual id mapping
    private $strictIndividual     = array();
    private $householdIndividual  = array();
    
    /*****************************
     *  Constructor of this class
     ****************************/
    function __construct($datasetSize = 1000)
    {
        $this->numContact          = $datasetSize;
        $this->numIndividual       = $this->numContact * 80 / 100;
        $this->numHousehold        = $this->numContact * 10 / 100;
        $this->numOrganization     = $this->numContact * 10 / 100;
        $this->numStrictIndividual = $this->numIndividual - ($this->numHousehold * test_RSTest_Common::NUM_INDIVIDUAL_PER_HOUSEHOLD); 
    }

    /**
     * Insert a note 
     *
     * This is a helper method which randomly populates "note" and 
     * "date_modified" and inserts it.
     * This method can not be called statically.
     *
     * @param   CRM_DAO_Note    DAO object for Note
     * 
     * @return  none
     *
     * @access  private
     */
    private function _insertNote($note) 
    {
        $note->note          = test_RSTest_Common::getRandomElement($this->note, test_RSTest_Common::ARRAY_DIRECT_USE);
        $note->modified_date = test_RSTest_Common::getRandomDate();                
        test_RSTest_Common::_insert($note);        
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
        //print_r($locationType);
        //print_r($contactId);
        $locationDAO                   =& new CRM_Core_DAO_Location();
        $locationDAO->is_primary       = $setPrimary; // primary location for now
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

        // need to get sort name to generate email id
        $contactDAO     =& new CRM_Contact_DAO_Contact();
        $contactDAO->id = $contactId;
        $contactDAO->find(true);
        // get the sort name of the contact
        $sortName       = $contactDAO->sort_name;

        if (! empty ($sortName)) {
            // add 2 email for each location
            for ($emailId=1; $emailId<=2; $emailId++) {
                $this->_addEmail($locationDAO->id, $sortName, ($emailId == 1));
            }
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
        return $email;
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
     * Adding Group Contact Status Details. 
     *
     * This helper method is used to add details for group contact details.
     * This method can not be called statically.
     *
     * @param   groupContactDAO    group Contact DAO object
     *
     * @return  none
     *
     * @access  private
     */
    /*private function _setGroupContactStatus($groupContactDAO)
    {
        switch ($groupContactDAO->status) {
        case 'Pending':
            $groupContactDAO->pending_date   = test_RSTest_Common::getRandomDate();
            $groupContactDAO->pending_method = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupMethod), test_RSTest_Common::ARRAY_DIRECT_USE);
            break;
        case 'Added':
            $groupContactDAO->in_date        = test_RSTest_Common::getRandomDate();
            $groupContactDAO->in_method      = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupMethod), test_RSTest_Common::ARRAY_DIRECT_USE);
            break;
        case 'Removed':
            $groupContactDAO->out_date       = test_RSTest_Common::getRandomDate();
            $groupContactDAO->in_date        = test_RSTest_Common::getRandomDate(0, strtotime($groupContactDAO->out_date));
            $groupContactDAO->out_method     = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupMethod), test_RSTest_Common::ARRAY_DIRECT_USE);
            break;
        } 
        //return;
    }*/

    /**
     * Setter method to add Relationship
     *
     * This helper method is used to add relationship to contacts.
     * This method can not be called statically.
     *
     * @param   $dao            DAO object for relationship
     * @param   $type           id for corresponding type of relationship
     * @param   $contactID      array of respective contact IDs
     * @param   $ContactMember  array of respective contact members
     *
     * @return  none
     *
     * @access  private
     */
    private function _setRelationship($dao, $type, $contactID=null , $contactMember=null)
    {
        switch ($type) {
        case 1:
            // add child_of relationship
            // 2 for each child
            if ($contactID) {
                $dao->relationship_type_id = 1;
                $dao->contact_id_a = $contactMember[2];
                $dao->contact_id_b = $contactMember[0];
                test_RSTest_Common::_insert($dao);
                $dao->contact_id_a = $contactMember[3];
                $dao->contact_id_b = $contactMember[0];
                test_RSTest_Common::_insert($dao);
                $dao->contact_id_a = $contactMember[1];
                $dao->contact_id_b = $contactMember[2];
                test_RSTest_Common::_insert($dao);
                $dao->contact_id_a = $contactMember[1];
                $dao->contact_id_b = $contactMember[3];
                test_RSTest_Common::_insert($dao);
            } else {
                $dao->relationship_type_id = 1;
                $dao->contact_id_a = $contactMember;
                $dao->contact_id_b = test_RSTest_Common::getRandomElement($this->individual, test_RSTest_Common::ARRAY_DIRECT_USE);;
                test_RSTest_Common::_insert($dao);
            }
            break;
            
        case 2: 
            // add spouse_of relationship 1 for both the spouses
            if ($contactID) {
                $dao->relationship_type_id = 2;
                $dao->contact_id_a = $contactMember[1];
                $dao->contact_id_b = $contactMember[0];
                test_RSTest_Common::_insert($dao);
            } else {
                $dao->relationship_type_id = 2;
                $dao->contact_id_a = $contactMember;
                $dao->contact_id_b = test_RSTest_Common::getRandomElement($this->individual, test_RSTest_Common::ARRAY_DIRECT_USE);;
                test_RSTest_Common::_insert($dao);
            }
            break;
            
        case 3:
            // add sibling_of relationship 1 for both the siblings
            if ($contactID) {
                $dao->relationship_type_id = 3;
                $dao->contact_id_a = $contactMember[3];
                $dao->contact_id_b = $contactMember[2];
                test_RSTest_Common::_insert($dao);
            } else {
                $dao->relationship_type_id = 3;
                $dao->contact_id_a = $contactMember;
                $dao->contact_id_b = test_RSTest_Common::getRandomElement($this->individual, test_RSTest_Common::ARRAY_DIRECT_USE);;
                test_RSTest_Common::_insert($dao);
            }
            break;
            
        case 4:
            $dao->relationship_type_id = 4;
            $dao->contact_id_a = test_RSTest_Common::getRandomElement($this->individual, test_RSTest_Common::ARRAY_DIRECT_USE);
            $dao->contact_id_b = $contactID;
            test_RSTest_Common::_insert($dao);
            break;
            
        case 5:
            $dao->relationship_type_id = 5;
            $dao->contact_id_b = $contactID;
            $firstIndividual  = test_RSTest_Common::getRandomElement($this->individual, test_RSTest_Common::ARRAY_DIRECT_USE);
            $dao->contact_id_a = $firstIndividual;
            test_RSTest_Common::_insert($dao);
            $secondIndividual = test_RSTest_Common::getRandomElement($this->individual, test_RSTest_Common::ARRAY_DIRECT_USE);
            if ($secondIndividual == $firstIndividual) {
                continue;
            } else {
                $dao->contact_id_a = $secondIndividual;
                test_RSTest_Common::_insert($dao);
            }
            break;        

        case 6:
            // add head_of_household relationship : 1 member as head of household.
            $dao->relationship_type_id = 6;
            $dao->contact_id_a = test_RSTest_Common::getRandomElement($contactMember, test_RSTest_Common::ARRAY_DIRECT_USE);
            $dao->contact_id_b = $contactID;
            test_RSTest_Common::_insert($dao);
            break;

        case 7:
            // add member_of_household relationship : 2 members as household members
            $dao->relationship_type_id = 7;
            $dao->contact_id_b = $contactID;
            
            $firstHMember = test_RSTest_Common::getRandomElement($contactMember, test_RSTest_Common::ARRAY_DIRECT_USE);
            $dao->contact_id_a = $firstHMember;
            test_RSTest_Common::_insert($dao);
            
            $secondHMember = test_RSTest_Common::getRandomElement($contactMember, test_RSTest_Common::ARRAY_DIRECT_USE);
            if ($firstHMember == $secondHMember) {
                continue;
            } else {
                $dao->contact_id_a = $secondHMember;
            }
            test_RSTest_Common::_insert($dao);
            break;
        }
    }
    
    function getZipCodeInfo( ) {
        $offset = mt_rand( 1, 43000 );
        $query = "SELECT zip, latitude, longitude FROM zipcodes LIMIT $offset, 1";
        $dao = new CRM_Core_DAO( );
        $dao->query( $query );
        while ( $dao->fetch( ) ) {
            return array( $dao->zip, $dao->latitude, $dao->longitude );
        }
    }

    function getLatLong( $zipCode ) {
        $query     = "http://maps.google.com/maps?q=$zipCode&output=js";
        $userAgent = "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0";
        
        $ch        = curl_init( );
        curl_setopt( $ch, CURLOPT_URL, $query );
        curl_setopt( $ch, CURLOPT_HEADER, false);
        curl_setopt( $ch, CURLOPT_USERAGENT, $userAgent );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // grab URL and pass it to the browser
        $outstr = curl_exec($ch);
        
        // close CURL resource, and free up system resources
        curl_close($ch);
        
        $preg = "/'(<\?xml.+?)',/s";
        preg_match( $preg, $outstr, $matches );
        if ( $matches[1] ) {
            $xml = simplexml_load_string( $matches[1] );
            $attributes = $xml->center->attributes( );
            if ( !empty( $attributes ) ) {
                return array( (float ) $attributes['lat'], (float ) $attributes['lng'] );
            }
        }
        return array( null, null );
    }

    /**
     * Parsing the data file.
     *
     * This is method parses the data file 
     * and builds array for particular element's data.
     * ( The elements : First Name, Last Name, Street Name, 
     *   Supplemental Address, City, Street, Email, Groups, 
     *   Note, Oraganization Name, Organization Type. )
     * This method can not be called statically.
     *
     * @return  none
     *
     * @access  public
     */
    public function parseDataFile()
    {
        
        $sampleData = simplexml_load_file(test_RSTest_Common::DATA_FILENAME);

        // first names
        foreach ($sampleData->first_names->first_name as $first_name) {
            $this->firstName[]            = trim($first_name);
        }

        // last names
        foreach ($sampleData->last_names->last_name as $last_name) {
            $this->lastName[]             = trim($last_name);
        }

        //  street names
        foreach ($sampleData->street_names->street_name as $street_name) {
            $this->streetName[]           = trim($street_name);
        }

        //  supplemental address 1
        foreach ($sampleData->supplemental_addresses_1->supplemental_address_1 as $supplemental_address_1) {
            $this->supplementalAddress1[] = trim($supplemental_address_1);
        }

        //  cities
        foreach ($sampleData->cities->city as $city) {
            $this->city[]                 = trim($city);
        }

        //  address directions
        foreach ($sampleData->address_directions->address_direction as $address_direction) {
            $this->addressDirection[]     = trim($address_direction);
        }

        // street types
        foreach ($sampleData->street_types->street_type as $street_type) {
            $this->streetType[]           = trim($street_type);
        }

        // email domains
        foreach ($sampleData->email_domains->email_domain as $email_domain) {
            $this->emailDomain[]          = trim($email_domain);
        }

        // email top level domain
        foreach ($sampleData->email_tlds->email_tld as $email_tld) {
            $this->emailTLD[]             = trim($email_tld);
        }

        // organization name
        foreach ($sampleData->organization_names->organization_name as $organization_name) {
            $this->organizationName[]    = trim($organization_name);
        }

        // organization field
        foreach ($sampleData->organization_fields->organization_field as $organization_field) {
            $this->organizationField[]    = trim($organization_field);
        }

        // organization type
        foreach ($sampleData->organization_types->organization_type as $organization_type) {
            $this->organizationType[]     = trim($organization_type);
        }

        // group
        foreach ($sampleData->groups->group as $group) {
            $this->group[]                = trim($group);
        }

        // notes
        foreach ($sampleData->notes->note as $note) {
            $this->note[]                 = trim($note);
        }

        // activity type
        foreach ($sampleData->activity_types->activity_type as $activity_type) {
            $this->activity_type[] = trim($activity_type);
        }

        // module
        foreach ($sampleData->modules->module as $module) {
            $this->module[] = trim($module);
        }

        // callback
        foreach ($sampleData->callbacks->callback as $callback) {
            $this->callback[] = trim($callback);
        }
    }

    /**
     *  Getter for type of contact
     *  
     *  This method gives the type of contact depending on the id.
     *  This method can not be called statically.
     *
     *  @param  $id   id for contact
     *
     *  @return ContactType
     *  @access Public
     */
    public function getContactType($id)
    {
        if(in_array($id, $this->individual))
            return 'Individual';
        if(in_array($id, $this->household))
            return 'Household';
        if(in_array($id, $this->organization))
            return 'Organization';
    }
    
    /**
     *  Initialize Database
     *
     *  @return  none
     *  @access  public
     */
    public function initDB()
    {
        $config = CRM_Core_Config::singleton();
    }
    
    /**
     *  Initialization of IDs. 
     *  
     *  This method initializes IDs for all contact types.
     *
     *  @return  none
     *  @access  public
     */
    public function initID($startID=0)
    {
        $this->_startID = $startID;
        // may use this function in future if needed to get
        // a consistent pattern of random numbers.
        
        // get the domain and contact id arrays
        
        if ($startID) {
            $this->contact             = range($this->_startID + 1, $this->_startID + $this->numContact);
        } else {
            $this->domain              = range(1, test_RSTest_Common::NUM_DOMAIN);
            $this->contact             = range(1, $this->numContact);
        }
        shuffle($this->domain);
        shuffle($this->contact);

        
        // get the individual, household  and organizaton contacts
        $offset = 0;
        $this->individual          = array_slice($this->contact, $offset, $this->numIndividual);
        $offset                   += $this->numIndividual;
        $this->household           = array_slice($this->contact, $offset, $this->numHousehold);
        $offset                   += $this->numHousehold;
        $this->organization        = array_slice($this->contact, $offset, $this->numOrganization);

        // get the strict individual contacts (i.e individual contacts not belonging to any household)
        $this->strictIndividual    = array_slice($this->individual, 0, $this->numStrictIndividual);

        // get the household to individual mapping array
        $this->householdIndividual = array_diff($this->individual, $this->strictIndividual);
        $this->householdIndividual = array_chunk($this->householdIndividual, test_RSTest_Common::NUM_INDIVIDUAL_PER_HOUSEHOLD);
        $this->householdIndividual = array_combine($this->household, $this->householdIndividual);
    }

    /**
     *  Adding Domain
     *
     *  This method adds domains and then adds 
     *  revisions for each domain with the latest revision being the last one..  
     * 
     *  @return   none
     *  @access   public
     */
    public function addDomain()
    {
        $this->_addLocation(test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('locationType'), test_RSTest_Common::ARRAY_DIRECT_USE), 1, true);
        $domainDAO =& new CRM_Core_DAO_Domain();
        for ($id=2; $id<=test_RSTest_Common::NUM_DOMAIN; $id++) {
            $domainDAO->name         = "Domain $id";
            $domainDAO->description  = "Description $id";
            $domainDAO->contact_name = test_RSTest_Common::getRandomName($this->firstName, $this->lastName);
            $domainDAO->email_domain = test_RSTest_Common::getRandomElement($this->emailDomain, test_RSTest_Common::ARRAY_DIRECT_USE) . ".fixme";
            // insert domain
            test_RSTest_Common::_insert($domainDAO);
            $this->_addLocation(test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('locationType'), test_RSTest_Common::ARRAY_DIRECT_USE), $id, true);
        }
    }

    /**
     * Adding Contact
     * 
     *
     * This method adds general contact information 
     * to contact table in the database.
     * This method can not be called statically.  
     *
     * @return none
     * @access public 
     *
     */
    public function addContact()
    {
        // add contacts
        $contactDAO =& new CRM_Contact_DAO_Contact();
        
        for ($id=1; $id<=$this->numContact; $id++) {
            echo ".";
            ob_flush();
            flush();
            
            $contactDAO->domain_id                      = 1;
            $contactDAO->contact_type                   = $this->getContactType($this->_startID + $id);
            $contactDAO->do_not_phone                   = mt_rand(0, 1);
            $contactDAO->do_not_email                   = mt_rand(0, 1);
            $contactDAO->do_not_post                    = mt_rand(0, 1);
            $contactDAO->do_not_trade                   = mt_rand(0, 1);
            $contactDAO->preferred_communication_method = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('PCMType'), test_RSTest_Common::ARRAY_SHIFT_USE);

            test_RSTest_Common::_insert($contactDAO);
        }
    }

    /**
     *  Adding Individual Contact.
     * 
     *  This method is used to add Individual type of contact 
     *  to individual table of database.
     *  This method can not be called statically.
     *  
     *  @return   none
     *  @access   public
     */
    public function addIndividual()
    {
        $prefixArray   = test_RSTest_Common::getPrefixArray();
        $suffixArray   = test_RSTest_Common::getSuffixArray();
        $genderArray   = test_RSTest_Common::getGenderArray();
        
        $individualDAO =& new CRM_Contact_DAO_Individual();
        $contactDAO    =& new CRM_Contact_DAO_Contact();
        
        for ($id=1; $id<=$this->numIndividual; $id++) {
            echo ".";
            ob_flush();
            flush();
            
            $individualDAO->contact_id    = $this->individual[($id-1)];
            $individualDAO->first_name    = ucfirst(test_RSTest_Common::getRandomElement($this->firstName, test_RSTest_Common::ARRAY_DIRECT_USE));
            $individualDAO->middle_name   = ucfirst(test_RSTest_Common::getRandomChar());
            $individualDAO->last_name     = ucfirst(test_RSTest_Common::getRandomElement($this->lastName, test_RSTest_Common::ARRAY_DIRECT_USE));
            $individualDAO->prefix_id     = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('prefixType'), test_RSTest_Common::ARRAY_SHIFT_USE);
            $individualDAO->suffix_id     = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('suffixType'), test_RSTest_Common::ARRAY_SHIFT_USE);
            $individualDAO->greeting_type = ucfirst(test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('greetingType'), test_RSTest_Common::ARRAY_SHIFT_USE));
            $individualDAO->gender        = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('gender'), test_RSTest_Common::ARRAY_DIRECT_USE);
            $individualDAO->birth_date    = date("Ymd", mt_rand(0, time()));
            $individualDAO->is_deceased   = mt_rand(0, 1);
            test_RSTest_Common::_insert($individualDAO);
            
            // also update the sort name for the contact id.
            $contactDAO->id               = $individualDAO->contact_id;
            $contactDAO->display_name     = trim($prefixArray[$individualDAO->prefix_id] . " $individualDAO->first_name $individualDAO->middle_name $individualDAO->last_name " . $suffixArray[$individualDAO->suffix_id]);
            $contactDAO->sort_name        = $individualDAO->last_name . ', ' . $individualDAO->first_name;
            $contactDAO->hash             = crc32($contactDAO->sort_name);
            test_RSTest_Common::_update($contactDAO);
        }
    }

    /**
     *
     *  Adding Household Contact.
     *
     *  This method is used to add Household type of contact 
     *  to hosehold table of database.
     *  This method can not be called statically.
     *
     *  @return  none
     *  @access  public
     */
    public function addHousehold()
    {
        $householdDAO =& new CRM_Contact_DAO_Household();
        $contactDAO   =& new CRM_Contact_DAO_Contact();
        
        for ($id=1; $id<=$this->numHousehold; $id++) {
            echo ".";
            ob_flush();
            flush();

            $householdDAO->contact_id         = $this->household[($id-1)];
            $householdDAO->primary_contact_id = $this->householdIndividual[$householdDAO->contact_id][0];

            // get the last name of the primary contact id
            $individualDAO                    =& new CRM_Contact_DAO_Individual();
            $individualDAO->contact_id        = $householdDAO->primary_contact_id;
            $individualDAO->find(true);
            $firstName                        = $individualDAO->first_name;
            $lastName                         = $individualDAO->last_name;

            // need to name the household and nick name appropriately
            $householdDAO->household_name     = "$firstName $lastName" . "'s home";
            $householdDAO->nick_name          = "$lastName" . "'s home";
            test_RSTest_Common::_insert($householdDAO);

            // need to update the sort name for the main contact table
            $contactDAO->id                   = $householdDAO->contact_id;
            $contactDAO->display_name         = $contactDAO->sort_name = $householdDAO->household_name;
            $contactDAO->hash                 = crc32($contactDAO->sort_name);
            test_RSTest_Common::_update($contactDAO);
        }
    }
    
    /**
     *  Adding Organization Contact.
     *
     *  This method is used to add Organization type of contact 
     *  to organization table of database.
     *  This method can not be called statically.
     *
     *  @return  none
     *  @access  public
     *
     */
    public function addOrganization()
    {
        $organizationDAO =& new CRM_Contact_DAO_Organization();
        $contactDAO      =& new CRM_Contact_DAO_Contact();       

        for ($id=1; $id<=$this->numOrganization; $id++) {
            echo ".";
            ob_flush();
            flush();

            $organizationDAO->contact_id         = $this->organization[($id-1)];
            $name                                = test_RSTest_Common::getRandomElement($this->organizationName, test_RSTest_Common::ARRAY_DIRECT_USE) . " " . test_RSTest_Common::getRandomElement($this->organizationField, test_RSTest_Common::ARRAY_DIRECT_USE) . " " . test_RSTest_Common::getRandomElement($this->organizationType, test_RSTest_Common::ARRAY_DIRECT_USE);
            $organizationDAO->organization_name  = $name;
            $organizationDAO->primary_contact_id = test_RSTest_Common::getRandomElement($this->strictIndividual, test_RSTest_Common::ARRAY_DIRECT_USE);
            test_RSTest_Common::_insert($organizationDAO);

            // need to update the sort name for the main contact table
            $contactDAO->id                      = $organizationDAO->contact_id;
            $contactDAO->display_name            = $contactDAO->sort_name = $organizationDAO->organization_name;
            $contactDAO->hash                    = crc32($contactDAO->sort_name);
            test_RSTest_Common::_update($contactDAO);
        }
    }
    
    /**
     *  Adding Relationship for Contact.
     *
     *  This method is used to add relationship for contact 
     *  to relationship table of database.
     *  This method can not be called statically.
     *
     *  @return  none
     *  @access  public
     *
     */
    public function addRelationship()
    {
        $relationshipDAO            =& new CRM_Contact_DAO_Relationship();
        
        $relationshipDAO->is_active = 1; // all active for now.
        
        foreach ($this->strictIndividual as $strictIndiID ) {
            echo ".";
            ob_flush();
            flush();
            
            $relType = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('relationshipType'), test_RSTest_Common::ARRAY_DIRECT_USE);
            if ($relType == 1 or $relType == 2 or $relType == 3) {
                $this->_setRelationship($relationshipDAO, $relType, 0, $strictIndiID);
            } else {
                $this->_setRelationship($relationshipDAO, mt_rand(1, 3), 0, $strictIndiID); 
            }
        }
        
        foreach ($this->householdIndividual as $householdID => $householdMember) {
            echo ".";
            ob_flush();
            flush();

            $relType = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('relationshipType'), test_RSTest_Common::ARRAY_DIRECT_USE);
            if ($relType == 4 or $relType == 5) {
                $this->_setRelationship($relationshipDAO, mt_rand(6, 7), $householdID, $householdMember);
            } else {
                $this->_setRelationship($relationshipDAO, $relType, $householdID, $householdMember);
            }
        }

        foreach ($this->organization as $organizationID) {
            echo ".";
            ob_flush();
            flush();

            $relType = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('relationshipType'), test_RSTest_Common::ARRAY_DIRECT_USE);
            if ($relType == 4 or $relType == 5) {
                $this->_setRelationship($relationshipDAO, $relType, $organizationID);
            } else {
                $this->_setRelationship($relationshipDAO, mt_rand(4, 5), $organizationID);
            }
        }
    }
    
    /**
     * Add a location.
     *
     * This method adds data to the location table
     * This method can not be called statically.
     *
     * @return  none
     *
     * @access none
     */
    public function addLocation($setPrimary=0)
    {
        // strict individuals
        foreach ($this->strictIndividual as $contactId) {
            echo ".";
            ob_flush();
            flush();
            $this->_addLocation(test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('locationType'), test_RSTest_Common::ARRAY_DIRECT_USE), $contactId, $setPrimary);
        }
        
        //household
        foreach ($this->household as $contactId) {
            echo ".";
            ob_flush();
            flush();
            $this->_addLocation(test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('locationType'), test_RSTest_Common::ARRAY_DIRECT_USE), $contactId, $setPrimary);
        }
        
        //organization
        foreach ($this->organization as $contactId) {
            echo ".";
            ob_flush();
            flush();
            $this->_addLocation(test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('locationType'), test_RSTest_Common::ARRAY_DIRECT_USE), $contactId, $setPrimary);
        }

        // some individuals
        $someIndividual = array_diff($this->individual, $this->strictIndividual);
        $someIndividual = array_slice($someIndividual, 0, (int)(75 * ($this->numIndividual - $this->numStrictIndividual) / 100));
        foreach ($someIndividual as $contactId) {
            echo ".";
            ob_flush();
            flush();
            $this->_addLocation(test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('locationType'), test_RSTest_Common::ARRAY_DIRECT_USE), $contactId, $setPrimary);
        }
    }

    /**
     * Add a note
     * 
     * This method adds Note to contact.
     * This method can not be called statically.
     *
     * @return  none
     *
     * @access  public
     */
    public function addNote()
    {
        $noteDAO               =& new CRM_Core_DAO_Note();
        $noteDAO->entity_table = 'civicrm_contact';
        $noteDAO->contact_id   = 1;

        for ($i=0; $i<$this->numContact; $i++) {
            echo ".";
            ob_flush();
            flush();
            $noteDAO->entity_id  = $this->contact[$i];
            if ($this->contact[$i] % 5) {
                $this->_insertNote($noteDAO);
            }
            if ($this->contact[$i] % 3) {
                $this->_insertNote($noteDAO);
            }
            if ($this->contact[$i] % 2) {
                $this->_insertNote($noteDAO);
            }
        }
    }

    /**
     *
     * Adding activity History
     *
     * This method populates the crm_activity_history table
     *
     * @access  public
     *
     * 
     */
    public function addActivityHistory()
    {
        $contactDAO = new CRM_Contact_DAO_Contact();
        $contactDAO->contact_type = 'Individual';
        $contactDAO->selectAdd();
        $contactDAO->selectAdd('id');
        $contactDAO->orderBy('sort_name');
        $contactDAO->find();

        $count = 0;

        while($contactDAO->fetch()) {
            echo ".";
            ob_flush();
            flush();
            if ($count++ > 2) {
                break;
            }
            for ($i=0; $i<test_RSTest_Common::NUM_ACTIVITY_HISTORY; $i++) {
                $activityHistoryDAO                   =& new CRM_Core_DAO_ActivityHistory();
                $activityHistoryDAO->entity_table     = 'civicrm_contact';
                $activityHistoryDAO->entity_id        = $contactDAO->id;
                $activityHistoryDAO->activity_type    = test_RSTest_Common::getRandomElement($this->activity_type);
                $activityHistoryDAO->module           = test_RSTest_Common::getRandomElement($this->module);
                if ($i % 2) {
                    $activityHistoryDAO->callback     = test_RSTest_Common::getRandomElement($this->callback);
                }
                $activityHistoryDAO->activity_id      = mt_rand(1,1111);
                $activityHistoryDAO->activity_summary = test_RSTest_Common::getRandomString(mt_rand(55, 222));
                $activityHistoryDAO->activity_date    = test_RSTest_Common::getRandomDate();
                test_RSTest_Common::_insert($activityHistoryDAO);
            }
        }
    }

    
    /**
     * Add Tag to Entity
     * 
     * This method is used to add tag to entities in the database.
     * This method can not be called statically.
     *
     */
    public function addEntityTag()
    {
        $entityTagDAO =& new CRM_Core_DAO_EntityTag();
        for ($i=0; $i<$this->numContact; $i+=2) {
            echo ".";
            ob_flush();
            flush();
            $entityTagDAO->entity_table = 'civicrm_contact';
            $entityTagDAO->entity_id    = $this->contact[$i];
            $entityTagDAO->tag_id       = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('tag'), test_RSTest_Common::ARRAY_DIRECT_USE);

            test_RSTest_Common::_insert($entityTagDAO);
            $secondTag                  = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('tag'), test_RSTest_Common::ARRAY_DIRECT_USE);
            if (!(($entityTagDAO->entity_id) % 3 && $entityTagDAO->tag_id == $secondTag)) {
                $entityTagDAO->tag_id   = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('tag'), test_RSTest_Common::ARRAY_DIRECT_USE);
                test_RSTest_Common::_insert($entityTagDAO);
            } 
        }
    }

    /**
     * Add group to a contact
     * 
     *
     * This method populates the crm_entity_category table
     *
     */
    public function addGroup($flag=true)
    {
        if ($flag) {
            $groupDAO =& new CRM_Contact_DAO_Group();
            // add the 3 groups first
            $numGroup = count($this->group);
            for ($i=0; $i<$numGroup; $i++) {
                //$groupDAO->domain_id = test_RSTest_Common::getRandomElement($this->domain);
                $groupDAO->domain_id = 1;
                $groupDAO->name      = $this->group[$i];
                $groupDAO->title     = $this->group[$i];
                $groupDAO->is_active = 1;
                test_RSTest_Common::_insert($groupDAO);
            }
        }

        $newsLetter    = $this->numContact * 70 / 100;
        $volunteers    = $this->numContact * 15 / 100;
        $advisoryBoard = $this->numContact * 10 / 100;

        for ($i=0; $i<$newsLetter; $i++) {
            echo ".";
            ob_flush();
            flush();
            
            $groupContactDAO             =& new CRM_Contact_DAO_GroupContact();
            $groupContactDAO->group_id   = 1;
            $groupContactDAO->contact_id = test_RSTest_Common::getRandomElement($this->contact, test_RSTest_Common::ARRAY_DIRECT_USE);;
            $groupContactDAO->status     = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupStatus), test_RSTest_Common::ARRAY_DIRECT_USE);
            
            $subscriptionHistoryDAO             = new CRM_Contact_DAO_SubscriptionHistory();
            $subscriptionHistoryDAO->contact_id = $groupContactDAO->contact_id;
            $subscriptionHistoryDAO->group_id   = $groupContactDAO->group_id;
            $subscriptionHistoryDAO->status     = $groupContactDAO->status;
            $subscriptionHistoryDAO->method     = test_RSTest_Common::getRandomElement((test_RSTest_Common::$subscriptionHistoryMethod), test_RSTest_Common::ARRAY_DIRECT_USE); // method
            $subscriptionHistoryDAO->date       = test_RSTest_Common::getRandomDate();
            if ($groupContactDAO->status != 'Pending') {
                test_RSTest_Common::_insert($groupContactDAO);
            }
            test_RSTest_Common::_insert($subscriptionHistoryDAO);
        }
        
        for ($i=0; $i<$volunteers; $i++) {
            echo ".";
            ob_flush();
            flush();
            
            $groupContactDAO             =& new CRM_Contact_DAO_GroupContact();
            $groupContactDAO->group_id   = 2;
            $groupContactDAO->contact_id = test_RSTest_Common::getRandomElement($this->contact, test_RSTest_Common::ARRAY_DIRECT_USE);
            $groupContactDAO->status     = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupStatus), test_RSTest_Common::ARRAY_DIRECT_USE);
            
            $subscriptionHistoryDAO             = new CRM_Contact_DAO_SubscriptionHistory();
            $subscriptionHistoryDAO->contact_id = $groupContactDAO->contact_id;
            $subscriptionHistoryDAO->group_id   = $groupContactDAO->group_id;
            $subscriptionHistoryDAO->status     = $groupContactDAO->status;
            $subscriptionHistoryDAO->method     = test_RSTest_Common::getRandomElement((test_RSTest_Common::$subscriptionHistoryMethod), test_RSTest_Common::ARRAY_DIRECT_USE); // method
            $subscriptionHistoryDAO->date       = test_RSTest_Common::getRandomDate();
            if ($groupContactDAO->status != 'Pending') {
                test_RSTest_Common::_insert($groupContactDAO);
            }
            test_RSTest_Common::_insert($subscriptionHistoryDAO);
        }
        
        for ($i=0; $i<$advisoryBoard; $i++) {
            echo ".";
            ob_flush();
            flush();
            
            $groupContactDAO             =& new CRM_Contact_DAO_GroupContact();
            $groupContactDAO->group_id   = 3;
            $groupContactDAO->contact_id = test_RSTest_Common::getRandomElement($this->contact, test_RSTest_Common::ARRAY_DIRECT_USE);;
            $groupContactDAO->status     = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupStatus), test_RSTest_Common::ARRAY_DIRECT_USE);
            
            $subscriptionHistoryDAO             = new CRM_Contact_DAO_SubscriptionHistory();
            $subscriptionHistoryDAO->contact_id = $groupContactDAO->contact_id;
            $subscriptionHistoryDAO->group_id   = $groupContactDAO->group_id;
            $subscriptionHistoryDAO->status     = $groupContactDAO->status;
            $subscriptionHistoryDAO->method     = test_RSTest_Common::getRandomElement((test_RSTest_Common::$subscriptionHistoryMethod), test_RSTest_Common::ARRAY_DIRECT_USE); // method
            $subscriptionHistoryDAO->date       = test_RSTest_Common::getRandomDate();
            if ($groupContactDAO->status != 'Pending') {
                test_RSTest_Common::_insert($groupContactDAO);
            }
            test_RSTest_Common::_insert($subscriptionHistoryDAO);
        }
        
    }
    
    
    /**
     * Execute data generation.
     * 
     * This method is used for data generation.
     * This method can not be called statically.
     *
     * @return  none
     *
     * @access  public
     */
    public function run($ID=0) 
    {
//         $this->initID($ID);
//         $this->parseDataFile();
//         $this->initDB();
        
//         //$try1 = test_RSTest_Common::getRandomElement(test_RSTest_Common::$genderType, test_RSTest_Common::ARRAY_DIRECT_USE);
//         //echo " Hello Gender is : " . $try1 . "\n";
        
//         $try2 = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('gender'), test_RSTest_Common::ARRAY_DIRECT_USE);
//         //$try2 = test_RSTest_Common::getValue('gender');
//         echo " Hello Gender is : " . $try2 . "\n";
//         $try3 = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('prefixType'), test_RSTest_Common::ARRAY_DIRECT_USE);
//         echo " Hello Prefix is : " . $try3 . "\n";
//         $try4 = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('suffixType'), test_RSTest_Common::ARRAY_DIRECT_USE);
//         echo " Hello Suffix is : " . $try4 . "\n";
        
        
//         $prefixArray   = test_RSTest_Common::getPrefixArray();
        
//         $suffixArray   = test_RSTest_Common::getSuffixArray();
        
//         $genderArray   = test_RSTest_Common::getGenderArray();
        
//         $display_name     = $prefixArray[$try3] . " first_name middle_name last_name " . $suffixArray[$try4];
//         echo " " . $display_name . "\n";
        
//         echo " " . test_RSTest_Common::getRandomName('manish', 'zope') . "\n";
        
        $this->initID($ID);
        //echo "Hello 1 \n";
        $this->parseDataFile();
        //echo "Hello 2 \n";
        $this->initDB();
        if (!($ID)) {
            //echo "Hello 3 \n";
            $this->addDomain();
        }
        //echo "Hello 4 \n";
        $this->addContact();
        //echo "Hello 5 \n";
        $this->addIndividual();
        //echo "Hello 6 \n";
        $this->addHousehold();
        //echo "Hello 7 \n";
        $this->addOrganization();
        //echo "Hello 8 \n";
        $this->addRelationship();
        //echo "Hello 9 \n";
        $this->addLocation(1);
        //echo "Hello 10 \n";
        $this->addEntityTag();
        if ($ID) {
            //echo "Hello 11 \n";
            $this->addGroup(false);
        } else {
            //echo "Hello 11 \n";
            $this->addGroup(true);
        }
        //echo "Hello 12 \n";
        $this->addNote();
        //echo "Hello 13 \n";
        $this->addActivityHistory();
    }
}
?>
