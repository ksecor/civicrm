<?php

/*******************************************************
 * This class generates data for the schema located in Contact.sql
 *
 * each public method generates data for the concerned table.
 * so for example the addContactDomain method generates and adds
 * data to the contact_domain table
 *
 * Data generation is a bit tricky since the data generated
 * randomly in one table could be used as a FKEY in another
 * table.
 *
 * In order to ensure that a randomly generated FKEY matches
 * a field in the referened table, the field in the referenced
 * table is always generated linearly.
 *
 *
 *
 *
 * Some numbers
 *
 * Domain ID's - 1 to NUM_DOMAIN
 *
 * Context - 3/domain
 *
 * Contact - 1 to NUM_CONTACT
 *           75% - Individual
 *           15% - Household
 *           10% - Organization
 *
 *           Contact to Domain distribution should be equal.
 *
 *
 * Contact Individual = 1 to 0.75*NUM_CONTACT
 *
 * Contact Household = 0.75*NUM_CONTACT to 0.9*NUM_CONTACT
 *
 * Contact Organization = 0.9*NUM_CONTACT to NUM_CONTACT
 *
 * Contact Location = 15% for Households, 10% for Organizations, (75-(15*4))% for Individuals.
 *                     (Assumption is that each household contains 4 individuals)
 *
 *******************************************************/

/*******************************************************
 *
 * Note: implication of using of mt_srand(1) in constructor
 * The data generated will be done in a consistent manner
 * so as to give the same data during each run (but this
 * would involve populating the entire db at one go - since
 * mt_srand(1) is in the constructor, if one needs to be able
 * to get consistent random numbers then the mt_srand(1) shld
 * be in each function that adds data to each table.
 *
 *******************************************************/


require_once '../civicrm.config.php';

require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/I18n.php';

require_once 'CRM/Core/DAO/Location.php';
require_once 'CRM/Core/DAO/Address.php';
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Core/DAO/Phone.php';
require_once 'CRM/Core/DAO/Email.php';
require_once 'CRM/Core/DAO/EntityTag.php';
require_once 'CRM/Core/DAO/Note.php';
require_once 'CRM/Core/DAO/Domain.php';
require_once 'CRM/Core/DAO/CustomValue.php';
require_once 'CRM/Core/DAO/ActivityHistory.php';

require_once 'CRM/Contact/DAO/Group.php';
require_once 'CRM/Contact/DAO/GroupContact.php';
require_once 'CRM/Contact/DAO/SubscriptionHistory.php';
require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Contact/DAO/Individual.php';
require_once 'CRM/Contact/DAO/Household.php';
require_once 'CRM/Contact/DAO/Organization.php';
require_once 'CRM/Contact/DAO/Relationship.php';

class CRM_GCD {

    /*******************************************************
     * constants
     *******************************************************/
    const DATA_FILENAME="sample_data.xml";

    const NUM_DOMAIN = 1;
    const NUM_CONTACT = 100;

    const INDIVIDUAL_PERCENT = 75;
    const HOUSEHOLD_PERCENT = 15;
    const ORGANIZATION_PERCENT = 10;
    const NUM_INDIVIDUAL_PER_HOUSEHOLD = 4;

    const NUM_ACTIVITY_HISTORY = 150;

    // relationship types from the table crm_relationship_type
    const CHILD_OF            = 1;
    const SPOUSE_OF           = 2;
    const SIBLING_OF          = 3;
    const HEAD_OF_HOUSEHOLD   = 6;
    const MEMBER_OF_HOUSEHOLD = 7;


    // location types from the table crm_location_type
    const HOME            = 1;
    const WORK            = 2;
    const MAIN            = 3;
    const OTHER           = 4;
    
    const ADD_TO_DB=TRUE;
    //const ADD_TO_DB=FALSE;
    const DEBUG_LEVEL=1;

    
    /*********************************
     * private members
     *********************************/
    
    // enum's from database
    private $preferredCommunicationMethod = array('1', '2', '3','4','5');
    private $greetingType = array('Formal', 'Informal', 'Honorific', 'Custom', 'Other');
    private $contactType = array('Individual', 'Household', 'Organization');
    private $phoneType = array('Phone', 'Mobile', 'Fax', 'Pager');    

    // customizable enums (foreign keys)
    private $prefix = array(1 => 'Mrs', 2 => 'Ms', 3 => 'Mr', 4 => 'Dr');
    private $suffix = array(1 => 'Jr', 2 => 'Sr');
    private $gender = array(1 => 'Female', 2 =>'Male');    

    // store domain id's
    private $domain = array();

    // store contact id's
    private $contact = array();
    private $individual = array();
    private $household = array();
    private $organization = array();
    

    // store names, firstnames, street 1, street2
    private $firstName = array();
    private $lastName = array();
    private $streetName = array();
    private $supplementalAddress1 = array();
    private $city = array();
    private $state = array();
    private $country = array();
    private $addressDirection = array();
    private $streetType = array();
    private $emailDomain = array();
    private $emailTLD = array();
    private $organizationName = array();
    private $organizationField = array();
    private $organizationType = array();
    private $group = array();
    private $note = array();
    private $activity_type = array();
    private $module = array();
    private $callback = array();
    private $party_registration = array();
    private $degree = array();
    private $school = array();

    // stores the strict individual id and household id to individual id mapping
    private $strictIndividual = array();
    private $householdIndividual = array();
    
    // sample data in xml format
    private $sampleData = NULL;
    
    // private vars
    private $numIndividual = 0;
    private $numHousehold = 0;
    private $numOrganization = 0;
    private $numStrictIndividual = 0;

    private $CSC = array(
                         1228 => array( // united states
                                       1004 => array ('San Francisco', 'Los Angeles', 'Palo Alto'), // california
                                       1031 => array ('New York', 'Albany'), // new york
                                       ),
                         1101 => array( // india
                                       1113 => array ('Mumbai', 'Pune', 'Nasik'), // maharashtra
                                       1114 => array ('Bangalore', 'Mangalore', 'Udipi'), // karnataka
                                       ),
                         1172 => array( // poland
                                       1115 => array ('Warszawa', 'Płock'), // mazowieckie
                                       1116 => array ('Gdańsk', 'Gdynia'), // pomorskie 
                                       ),
                         );
    
    private $groupMembershipStatus = array('Added', 'Removed', 'Pending');
    private $subscriptionHistoryMethod = array('Admin', 'Email');


  /*********************************
   * private methods
   *********************************/

    // get a randomly generated string
    private function _getRandomString($size=32)
    {
        $string = "";

        // get an ascii code for each character
        for($i=0; $i<$size; $i++) {
            $random_int = mt_rand(65,122);
            if(($random_int<97) && ($random_int>90)) {
                // if ascii code between 90 and 97 substitute with space
                $random_int=32;
            }
            $random_char=chr($random_int);
            $string .= $random_char;
        }
        return $string;
    }

    private function _getRandomChar()
    {
        return chr(mt_rand(65, 90));
    }        

    private function getRandomBoolean()
    {
        return mt_rand(0,1);

    }

    private function _getRandomElement(&$array1)
    {
        return $array1[mt_rand(1, count($array1))-1];
    }
    
    private function _getRandomIndex(&$array1)
    {
        return mt_rand(1, count($array1));
    }
    
    
    // country state city combo
    private function _getRandomCSC()
    {
        $array1 = array();

        // $c = array_rand($this->CSC);
        $c = 1228;

        // the state array now
        $s = array_rand($this->CSC[$c]);

        // the city
        $ci = array_rand($this->CSC[$c][$s]);
        $city = $this->CSC[$c][$s][$ci];

        $array1[] = $c;
        $array1[] = $s;
        $array1[] = $city;

        return $array1;
    }



    /**
     * Generate a random date. 
     *
     *   If both $startDate and $endDate are defined generate
     *   date between them.
     *
     *   If only startDate is specified then date generated is
     *   between startDate + 1 year.
     *
     *   if only endDate is specified then date generated is
     *   between endDate - 1 year.
     *
     *   if none are specified - date is between today - 1year 
     *   and today
     *
     * @param  int $startDate Start Date in Unix timestamp
     * @param  int $endDate   End Date in Unix timestamp
     * @access private
     * @return string randomly generated date in the format "Ymd"
     *
     */
    private function _getRandomDate($startDate=0, $endDate=0)
    {
        
        // number of seconds per year
        $numSecond = 31536000;
        $dateFormat = "Ymdhis";
        $today = time();

        // both are defined
        if ($startDate && $endDate) {
            return date($dateFormat, mt_rand($startDate, $endDate));
        }

        // only startDate is defined
        if ($startDate) {
            // $nextYear = mktime(0, 0, 0, date("m", $startDate),   date("d", $startDate),   date("Y")+1);
            return date($dateFormat, mt_rand($startDate, $startDate+$numSecond));
        }

        // only endDate is defined
        if ($startDate) {
            return date($dateFormat, mt_rand($endDate-$numSecond, $endDate));
        }        
        
        // none are defined
        return date($dateFormat, mt_rand($today-$numSecond, $today));
    }


    // insert data into db's
    private function _insert($dao)
    {
        if (self::ADD_TO_DB) {
            if (!$dao->insert()) {
                echo mysql_error() . "\n";
                exit(1);
            }
        }
    }

    // update data into db's
    private function _update($dao)
    {
        if (self::ADD_TO_DB) {
            if (!$dao->update()) {
                echo mysql_error() . "\n";
                exit(1);
            }
        }
    }


    /**
     * Insert a note 
     *
     *   Helper function which randomly populates "note" and 
     *   "date_modified" and inserts it.
     *
     * @param  CRM_DAO_Note DAO object for Note
     * @access private
     * @return none
     *
     */
    private function _insertNote($note) {
        $note->note = $this->_getRandomElement($this->note);
        $note->modified_date = $this->_getRandomDate();                
        $this->_insert($note);        
    }


    /*******************************************************
     *
     * Start of public functions
     *
     *******************************************************/
    // constructor
    function __construct()
    {

        //// CRM_Core_Error::le_method();
        //// CRM_Core_Error::ll_method();

        // initialize all the vars
        $this->numIndividual = self::INDIVIDUAL_PERCENT * self::NUM_CONTACT / 100;
        $this->numHousehold = self::HOUSEHOLD_PERCENT * self::NUM_CONTACT / 100;
        $this->numOrganization = self::ORGANIZATION_PERCENT * self::NUM_CONTACT / 100;
        $this->numStrictIndividual = $this->numIndividual - ($this->numHousehold * self::NUM_INDIVIDUAL_PER_HOUSEHOLD);


    }

    public function parseDataFile()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        $sampleData = simplexml_load_file(self::DATA_FILENAME);

        // first names
        foreach ($sampleData->first_names->first_name as $first_name) {
            $this->firstName[] = trim($first_name);
        }

        // last names
        foreach ($sampleData->last_names->last_name as $last_name) {
            $this->lastName[] = trim($last_name);
        }

        //  street names
        foreach ($sampleData->street_names->street_name as $street_name) {
            $this->streetName[] = trim($street_name);
        }

        //  supplemental address 1
        foreach ($sampleData->supplemental_addresses_1->supplemental_address_1 as $supplemental_address_1) {
            $this->supplementalAddress1[] = trim($supplemental_address_1);
        }

        //  cities
        foreach ($sampleData->cities->city as $city) {
            $this->city[] = trim($city);
        }

        //  address directions
        foreach ($sampleData->address_directions->address_direction as $address_direction) {
            $this->addressDirection[] = trim($address_direction);
        }

        // street types
        foreach ($sampleData->street_types->street_type as $street_type) {
            $this->streetType[] = trim($street_type);
        }

        // email domains
        foreach ($sampleData->email_domains->email_domain as $email_domain) {
            $this->emailDomain[] = trim($email_domain);
        }

        // email top level domain
        foreach ($sampleData->email_tlds->email_tld as $email_tld) {
            $this->emailTLD[] = trim($email_tld);
        }

        // organization name
        foreach ($sampleData->organization_names->organization_name as $organization_name) {
            $this->organization_name[] = trim($organization_name);
        }

        // organization field
        foreach ($sampleData->organization_fields->organization_field as $organization_field) {
            $this->organizationField[] = trim($organization_field);
        }

        // organization type
        foreach ($sampleData->organization_types->organization_type as $organization_type) {
            $this->organizationType[] = trim($organization_type);
        }

        // group
        foreach ($sampleData->groups->group as $group) {
            $this->group[] = trim($group);
        }

        // notes
        foreach ($sampleData->notes->note as $note) {
            $this->note[] = trim($note);
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

        // custom data - party registration
        foreach ($sampleData->party_registrations->party_registration as $party_registration) {
            $this->party_registration[] = trim($party_registration); 
        }

        // custom data - degrees
        foreach ($sampleData->degrees->degree as $degree) {
            $this->degree[] = trim($degree); 
        }

        // custom data - schools
        foreach ($sampleData->schools->school as $school) {
            $this->school[] = trim($school); 
        }

        // custom data - issue
        foreach ($sampleData->issue->status as $status) {
            $this->issue[] = trim($status); 
        }

        // custom data - gotv
        require_once 'CRM/Core/BAO/CustomOption.php';
        foreach ($sampleData->gotv->status as $status) {
            $this->gotv[] = CRM_Core_BAO_CustomOption::VALUE_SEPERATOR.trim($status).CRM_Core_BAO_CustomOption::VALUE_SEPERATOR; 
        }

        // custom data - marital_status
        foreach ($sampleData->marital_status->status as $status) {
            $this->marital_status[] = trim($status); 
        }
    }

    public function getContactType($id)
    {
        if(in_array($id, $this->individual))
            return 'Individual';
        if(in_array($id, $this->household))
            return 'Household';
        if(in_array($id, $this->organization))
            return 'Organization';
    }


    public function initDB()
    {
        $config = CRM_Core_Config::singleton();
    }


    /*******************************************************
     *
     * this function creates arrays for the following
     *
     * domain id
     * contact id
     * contact_location id
     * contact_contact_location id
     * contact_email uuid
     * contact_phone_uuid
     * contact_instant_message uuid
     * contact_relationship uuid
     * contact_task uuid
     * contact_note uuid
     *
     *******************************************************/
    public function initID()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        // may use this function in future if needed to get
        // a consistent pattern of random numbers.

        // get the domain and contact id arrays
        $this->domain = range(1, self::NUM_DOMAIN);
        shuffle($this->domain);
        $this->contact = range(1, self::NUM_CONTACT);
        shuffle($this->contact);

        // get the individual, household  and organizaton contacts
        $offset = 0;
        $this->individual = array_slice($this->contact, $offset, $this->numIndividual);
        $offset += $this->numIndividual;
        $this->household = array_slice($this->contact, $offset, $this->numHousehold);
        $offset += $this->numHousehold;
        $this->organization = array_slice($this->contact, $offset, $this->numOrganization);

        // get the strict individual contacts (i.e individual contacts not belonging to any household)
        $this->strictIndividual = array_slice($this->individual, 0, $this->numStrictIndividual);
        
        // get the household to individual mapping array
        $this->householdIndividual = array_diff($this->individual, $this->strictIndividual);
        $this->householdIndividual = array_chunk($this->householdIndividual, self::NUM_INDIVIDUAL_PER_HOUSEHOLD);
        $this->householdIndividual = array_combine($this->household, $this->householdIndividual);
    }


    /*******************************************************
     *
     * addDomain()
     *
     * This method adds NUM_DOMAIN domains and then adds NUM_REVISION
     * revisions for each domain with the latest revision being the last one..
     *
     *******************************************************/
    public function addDomain()
    {

        /* Add a location for domain 1 */
        $this->_addLocation(self::MAIN, 1, true);

        $domain =& new CRM_Core_DAO_Domain();
        for ($id=2; $id<=self::NUM_DOMAIN; $id++) {
            // domain name is pretty simple. it is "Domain $id"
            $domain->name = "Domain $id";
            $domain->description = "Description $id";
            $domain->contact_name = $this->randomName();
            $domain->email_domain = 
                $this->_getRandomElement($this->emailDomain) . ".fixme";

            // insert domain
            $this->_insert($domain);
            $this->_addLocation(self::MAIN, $id, true);
        }
    }
    
    public function randomName() {
        $prefix = $this->_getRandomIndex($this->prefix);
        $first_name = ucfirst($this->_getRandomElement($this->firstName));
        $middle_name = ucfirst($this->_getRandomChar());
        $last_name = ucfirst($this->_getRandomElement($this->lastName));
        $suffix = $this->_getRandomIndex($this->suffix);

        return $this->prefix[$prefix] . " $first_name $middle_name $last_name " .  $this->suffix[$suffix];
    }
    /*******************************************************
     *
     * addContact()
     *
     * This method adds data to the contact table
     *
     * id - from $contact
     * domain_id (fkey into domain) (always 1)
     * contact_type 'Individual' 'Household' 'Organization'
     * preferred_communication (random 1 to 3)
     *
     *******************************************************/
    public function addContact()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        // add contacts
        $contact =& new CRM_Contact_DAO_Contact();

        for ($id=1; $id<=self::NUM_CONTACT; $id++) {
            $contact->domain_id = 1;
            $contact->contact_type = $this->getContactType($id);
            $contact->do_not_phone = mt_rand(0, 1);
            $contact->do_not_email = mt_rand(0, 1);
            $contact->do_not_post  = mt_rand(0, 1);
            $contact->do_not_trade = mt_rand(0, 1);
            $contact->preferred_communication_method = $this->_getRandomElement($this->preferredCommunicationMethod);
            $this->_insert($contact);
        }
    }


    /*******************************************************
     *
     * addIndividual()
     *
     * This method adds data to the contact_individual table
     *
     * The following fields are generated and added.
     *
     * contact_uuid - individual
     * contact_rid - latest one
     * first_name 'First Name $contact_uuid'
     * middle_name 'Middle Name $contact_uuid'
     * last_name 'Last Name $contact_uuid'
     * job_title 'Job Title $contact_uuid'
     * greeting_type - randomly select from the enum values
     * custom_greeting - "custom greeting $contact_uuid'
     *
     *******************************************************/
    public function addIndividual()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        $individual =& new CRM_Contact_DAO_Individual();
        $contact =& new CRM_Contact_DAO_Contact();

        for ($id=1; $id<=$this->numIndividual; $id++) {
            $individual->contact_id = $this->individual[($id-1)];
            $individual->first_name = ucfirst($this->_getRandomElement($this->firstName));
            $individual->middle_name = ucfirst($this->_getRandomChar());
            $individual->last_name = ucfirst($this->_getRandomElement($this->lastName));
            $individual->prefix_id = $this->_getRandomIndex($this->prefix);
            $individual->suffix_id = $this->_getRandomIndex($this->suffix);
            $individual->greeting_type = $this->_getRandomElement($this->greetingType);
            $individual->gender_id = $this->_getRandomIndex($this->gender);
            $individual->birth_date = date("Ymd", mt_rand(0, time()));
            $individual->is_deceased = mt_rand(0, 1);
            $this->_insert($individual);

            // also update the sort name for the contact id.
            $contact->id = $individual->contact_id;
            $contact->display_name = trim( $this->prefix[$individual->prefix_id] . " $individual->first_name $individual->middle_name $individual->last_name " . $this->suffix[$individual->suffix_id] );
            $contact->sort_name = $individual->last_name . ', ' . $individual->first_name;
            $contact->hash = crc32($contact->sort_name);
            $this->_update($contact);

            $this->addCustomDataValue($contact->id);
        }
    }


    /******************************************************
     * addCustomDataValue()
     *
     * This method adds custom data for individuals
     *
     ******************************************************/
    private function addCustomDataValue($contact_id) {
                
        $randLength = mt_rand(0, 7);
        $done = array( );

        for($cnt = 0; $cnt <= $randLength; $cnt++) {

            $item = mt_rand(0, 7);
            if ( $done[$item] ) {
                continue;
            }
            $done[$item] = true;

            switch($item) {
            case 0:
                //do nothing
                break;

            case 1:
                $customData =& new CRM_Core_DAO_CustomValue();
                $customData->entity_table = 'civicrm_contact';
                $customData->entity_id = $contact_id;
                $customData->custom_field_id = 1;
                $customData->int_data = mt_rand(0, 1);
                $this->_insert($customData);
                
                $customData =& new CRM_Core_DAO_CustomValue();
                $customData->entity_table = 'civicrm_contact';
                $customData->entity_id = $contact_id;
                $customData->custom_field_id = 2;
                $customData->char_data = $this->_getRandomElement($this->party_registration);
                $this->_insert($customData);
                break;

            case 2:
                $customData =& new CRM_Core_DAO_CustomValue();
                $customData->entity_table = 'civicrm_contact';
                $customData->entity_id = $contact_id;
                $customData->custom_field_id = 8;
                $customData->char_data = $this->_getRandomElement($this->degree);
                $this->_insert($customData);
                break;

            case 3:
                $customData =& new CRM_Core_DAO_CustomValue();
                $customData->entity_table = 'civicrm_contact';
                $customData->entity_id = $contact_id;
                $customData->custom_field_id = 9;
                $customData->char_data = $this->_getRandomElement($this->school);
                $this->_insert($customData);
                break;

            case 5: 
                $customData =& new CRM_Core_DAO_CustomValue(); 
                $customData->entity_table = 'civicrm_contact'; 
                $customData->entity_id = $contact_id; 
                $customData->custom_field_id = 5; 
                $customData->char_data = $this->_getRandomElement($this->issue); 
                $this->_insert($customData); 
                break; 

            case 6: 
                $customData =& new CRM_Core_DAO_CustomValue(); 
                $customData->entity_table = 'civicrm_contact'; 
                $customData->entity_id = $contact_id; 
                $customData->custom_field_id = 6; 
                $customData->char_data = $this->_getRandomElement($this->gotv); 
                $this->_insert($customData); 
                break; 

            case 7: 
                $customData =& new CRM_Core_DAO_CustomValue(); 
                $customData->entity_table = 'civicrm_contact'; 
                $customData->entity_id = $contact_id; 
                $customData->custom_field_id = 7; 
                $customData->char_data = $this->_getRandomElement($this->marital_status); 
                $this->_insert($customData); 
                break; 
            }
        }
    }


    /*******************************************************
     *
     * addHousehold()
     *
     * This method adds data to the contact_household table
     *
     * The following fields are generated and added.
     *
     * contact_uuid - household_individual
     * contact_rid - latest one
     * household_name 'household $contact_uuid primary contact $primary_contact_uuid'
     * nick_name 'nick $contact_uuid'
     * primary_contact_uuid = $household_individual[$contact_uuid][0];
     *
     *******************************************************/
    public function addHousehold()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        $household =& new CRM_Contact_DAO_Household();
        $contact =& new CRM_Contact_DAO_Contact();
        
        for ($id=1; $id<=$this->numHousehold; $id++) {
            $household->contact_id = $this->household[($id-1)];
            $household->primary_contact_id = $this->householdIndividual[$household->contact_id][0];

            // get the last name of the primary contact id
            $individual =& new CRM_Contact_DAO_Individual();
            $individual->contact_id = $household->primary_contact_id;
            $individual->find(true);
            $firstName = $individual->first_name;
            $lastName = $individual->last_name;

            // need to name the household and nick name appropriately
            $household->household_name = "$firstName $lastName" . "'s home";
            $household->nick_name = "$lastName" . "'s home";
            $this->_insert($household);

            // need to update the sort name for the main contact table
            $contact->id = $household->contact_id;
            $contact->display_name = $contact->sort_name = $household->household_name;
            $contact->hash = crc32($contact->sort_name);
            $this->_update($contact);
        }
    }



    /*******************************************************
     *
     * addOrganization()
     *
     * This method adds data to the contact_organization table
     *
     * The following fields are generated and added.
     *
     * contact_uuid - organization
     * contact_rid - latest one
     * organization_name 'organization $contact_uuid'
     * legal_name 'legal  $contact_uuid'
     * nick_name 'nick $contact_uuid'
     * sic_code 'sic $contact_uuid'
     * primary_contact_id - random individual contact uuid
     *
     *******************************************************/
    public function addOrganization()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        $organization =& new CRM_Contact_DAO_Organization();
        $contact =& new CRM_Contact_DAO_Contact();       

        for ($id=1; $id<=$this->numOrganization; $id++) {
            $organization->contact_id = $this->organization[($id-1)];
            $name = $this->_getRandomElement($this->organization_name) . " " . $this->_getRandomElement($this->organization_field) . " " . $this->_getRandomElement($this->organization_type);
            $organization->organization_name = $name;
            $organization->primary_contact_id = $this->_getRandomElement($this->strict_individual);
            $this->_insert($organization);

            // need to update the sort name for the main contact table
            $contact->id = $organization->contact_id;
            $contact->display_name = $contact->sort_name = $organization->organization_name;
            $contact->hash = crc32($contact->sort_name);
            $this->_update($contact);
        }
    }


    /*******************************************************
     *
     * addRelationship()
     *
     * This method adds data to the contact_relationship table
     *
     * it adds the following fields
     *
     *******************************************************/
    public function addRelationship()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        $relationship =& new CRM_Contact_DAO_Relationship();

        $relationship->is_active = 1; // all active for now.

        foreach ($this->householdIndividual as $household_id => $household_member) {
            // add child_of relationship
            // 2 for each child
            $relationship->relationship_type_id = self::CHILD_OF;
            $relationship->contact_id_a = $household_member[2];
            $relationship->contact_id_b = $household_member[0];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[3];
            $relationship->contact_id_b = $household_member[0];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[2];
            $relationship->contact_id_b = $household_member[1];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[3];
            $relationship->contact_id_b = $household_member[1];
            $this->_insert($relationship);

            // add spouse_of relationship 1 for both the spouses
            $relationship->relationship_type_id = self::SPOUSE_OF;
            $relationship->contact_id_a = $household_member[1];
            $relationship->contact_id_b = $household_member[0];
            $this->_insert($relationship);

            // add sibling_of relationship 1 for both the siblings
            $relationship->relationship_type_id = self::SIBLING_OF;
            $relationship->contact_id_a = $household_member[3];
            $relationship->contact_id_b = $household_member[2];
            $this->_insert($relationship);

            // add head_of_household relationship 1 for head of house
            $relationship->relationship_type_id = self::HEAD_OF_HOUSEHOLD;
            $relationship->contact_id_a = $household_member[0];
            $relationship->contact_id_b = $household_id;
            $this->_insert($relationship);

            // add member_of_household relationship 3 for all other members
            $relationship->relationship_type_id = self::MEMBER_OF_HOUSEHOLD;
            $relationship->contact_id_a = $household_member[1];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[2];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[3];
            $this->_insert($relationship);
        }
    }


    /*******************************************************
     *
     * addLocation()
     *
     * This method adds data to the location table
     *
     *******************************************************/
    public function addLocation()
    {
        // strict individuals
        foreach ($this->strictIndividual as $contactId) {
            $this->_addLocation(self::HOME, $contactId);
        }
        
        //household
        foreach ($this->household as $contactId) {
            $this->_addLocation(self::HOME, $contactId);
        }
        
        //organization
        foreach ($this->organization as $contactId) {
            $this->_addLocation(self::MAIN, $contactId);
        }

        // some individuals.
        $someIndividual = array_diff($this->individual, $this->strictIndividual);
        $someIndividual = array_slice($someIndividual, 0, (int)(75*($this->numIndividual-$this->numStrictIndividual)/100));
        foreach ($someIndividual as $contactId) {
            $this->_addLocation(self::HOME, $contactId);
        }

        // CRM_Core_Error::ll_method();
    }

    private function _addLocation($locationType, $contactId, $domain = false)
    {
        $locationDAO =& new CRM_Core_DAO_Location();

        $locationDAO->is_primary = 1; // primary location for now
        $locationDAO->location_type_id = $locationType;
        if ($domain) {
            $locationDAO->entity_id = $contactId;
            $locationDAO->entity_table = 'civicrm_domain';
        } else {
            $locationDAO->entity_id    = $contactId;
            $locationDAO->entity_table = 'civicrm_contact';
        }

        $this->_insert($locationDAO);
        $this->_addAddress($locationDAO->id);        

        // add two phones for each location
        $this->_addPhone($locationDAO->id, 'Phone', true);
        $this->_addPhone($locationDAO->id, 'Mobile', false);

        // need to get sort name to generate email id
        $contact =& new CRM_Contact_DAO_Contact();
        $contact->id = $contactId;
        $contact->find(true);
        // get the sort name of the contact
        $sortName  = $contact->sort_name;
        if ( ! empty( $sortName ) ) {
            // add 2 email for each location
            for ($emailId=1; $emailId<=2; $emailId++) {
                $this->_addEmail($locationDAO->id, $sortName, ($emailId == 1));
            }
        }
    }

    private function _addAddress($locationId)
    {

        // CRM_Core_Error::le_method();
        $addressDAO =& new CRM_Core_DAO_Address();

        // add addresses now currently we are adding only 1 address for each location
        $addressDAO->location_id = $locationId;


        if ($locationId % 5) {
            $addressDAO->street_number = mt_rand(1, 1000);
            $addressDAO->street_number_suffix = ucfirst($this->_getRandomChar());
            $addressDAO->street_number_predirectional = $this->_getRandomElement($this->addressDirection);
            $addressDAO->street_name = ucwords($this->_getRandomElement($this->streetName));
            $addressDAO->street_type = $this->_getRandomElement($this->streetType);
            $addressDAO->street_number_postdirectional = $this->_getRandomElement($this->addressDirection);
            $addressDAO->street_address = $addressDAO->street_number_predirectional . " " . $addressDAO->street_number .  $addressDAO->street_number_suffix .  " " . $addressDAO->street_name .  " " . $addressDAO->street_type . " " . $addressDAO->street_number_postdirectional;
            $addressDAO->supplemental_address_1 = ucwords($this->_getRandomElement($this->supplementalAddress1));
        }
        
        // some more random skips
        // if ($locationId % 7) {
        if ($locationId) {
            // hack add lat / long for US based addresses
            list( $addressDAO->country_id, $addressDAO->state_province_id, $addressDAO->city, 
                  $addressDAO->postal_code, $addressDAO->geo_code_1, $addressDAO->geo_code_2 ) = 
                self::getZipCodeInfo( );
        }        

        $addressDAO->county_id = 1;
        $addressDAO->geo_coord_id = 1;
        
        $this->_insert($addressDAO);

        // CRM_Core_Error::ll_method();
    }

    private function _sortNameToEmail($sortName)
    {
        $email = preg_replace("([^a-zA-Z0-9_-]*)", "", $sortName);
        return $email;
    }

    private function _addPhone($locationId, $phoneType, $primary=false)
    {
        // CRM_Core_Error::le_method();
        if ($locationId % 3) {
            $phone =& new CRM_Core_DAO_Phone();
            $phone->location_id = $locationId;
            $phone->is_primary = $primary;
            $phone->phone = mt_rand(11111111, 99999999);
            $phone->phone_type = $phoneType;
            $this->_insert($phone);
        }
        // CRM_Core_Error::ll_method();
    }

    private function _addEmail($locationId, $sortName, $primary=false)
    {
        // CRM_Core_Error::le_method();
        if ($locationId % 7) {
            $email =& new CRM_Core_DAO_Email();
            $email->location_id = $locationId;
            $email->is_primary = $primary;
            
            $emailName = $this->_sortNameToEmail($sortName);
            $emailDomain = $this->_getRandomElement($this->emailDomain);
            $tld = $this->_getRandomElement($this->emailTLD);
            $email->email = $emailName . "@" . $emailDomain . "." . $tld;
            $this->_insert($email);
        }
        // CRM_Core_Error::ll_method();

    }


    /*******************************************************
     *
     * addTagEntity()
     *
     * This method populates the crm_entity_tag table
     *
     *******************************************************/
    public function addEntityTag()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        $entity_tag =& new CRM_Core_DAO_EntityTag();
        
        // add categories 1,2,3 for Organizations.
        for ($i=0; $i<$this->numOrganization; $i+=2) {
            $org_id = $this->organization[$i];
            // echo "org_id = $org_id\n";
            $entity_tag->entity_table = 'civicrm_contact';
            $entity_tag->entity_id = $this->organization[$i];
            $entity_tag->tag_id = mt_rand(1, 3);
            $this->_insert($entity_tag);
        }

        // add categories 4,5 for Individuals.        
        for ($i=0; $i<$this->numIndividual; $i+=2) {
            $entity_tag->entity_table = 'civicrm_contact';
            $entity_tag->entity_id = $this->individual[$i];
            if(($entity_tag->entity_id)%3) {
                $entity_tag->tag_id = mt_rand(4, 5);
                $this->_insert($entity_tag);
            } else {
                // some of the individuals are in both categories (4 and 5).
                $entity_tag->tag_id = 4;
                $this->_insert($entity_tag);                
                $entity_tag->tag_id = 5;
                $this->_insert($entity_tag);                
            }
        }
    }

    /*******************************************************
     *
     * addGroup()
     *
     * This method populates the crm_entity_tag table
     *
     *******************************************************/
    public function addGroup()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        $group =& new CRM_Contact_DAO_Group();

        
        // add the 3 groups first
        $numGroup = count($this->group);

        for ($i=0; $i<$numGroup; $i++) {
            $group->domain_id  = 1;
            $group->name       = $this->group[$i];
            $group->title      = $this->group[$i];
            $group->visibility = 'Public User Pages and Listings';
            $group->is_active  = 1;
            $this->_insert($group);
        }

        // 60 are for newsletter
        for ($i=0; $i<60; $i++) {
            $groupContact =& new CRM_Contact_DAO_GroupContact();
            $groupContact->group_id = 1;                                                     // newsletter subscribers
            $groupContact->contact_id = $this->individual[$i];
            $groupContact->status = $this->_getRandomElement($this->groupMembershipStatus);  // membership status


            $subscriptionHistory =& new CRM_Contact_DAO_SubscriptionHistory();
            $subscriptionHistory->contact_id = $groupContact->contact_id;
            $subscriptionHistory->group_id = $groupContact->group_id;
            $subscriptionHistory->status = $groupContact->status;
            $subscriptionHistory->method = $this->_getRandomElement($this->subscriptionHistoryMethod); // method
            $subscriptionHistory->date = $this->_getRandomDate();
            if ($groupContact->status != 'Pending') {
                $this->_insert($groupContact);
            }
            $this->_insert($subscriptionHistory);
        }

        // 15 volunteers
        for ($i=0; $i<15; $i++) {
            $groupContact =& new CRM_Contact_DAO_GroupContact();
            $groupContact->group_id = 2; // Volunteers
            $groupContact->contact_id = $this->individual[$i+60];
            $groupContact->status = $this->_getRandomElement($this->groupMembershipStatus);  // membership status

            $subscriptionHistory =& new CRM_Contact_DAO_SubscriptionHistory();
            $subscriptionHistory->contact_id = $groupContact->contact_id;
            $subscriptionHistory->group_id = $groupContact->group_id;
            $subscriptionHistory->status = $groupContact->status;
            $subscriptionHistory->method = $this->_getRandomElement($this->subscriptionHistoryMethod); // method
            $subscriptionHistory->date = $this->_getRandomDate();

            if ($groupContact->status != 'Pending') {
                $this->_insert($groupContact);
            }
            $this->_insert($subscriptionHistory);
        }

        // 8 advisory board group
        for ($i=0; $i<8; $i++) {
            $groupContact =& new CRM_Contact_DAO_GroupContact();
            $groupContact->group_id = 3; // advisory board group
            $groupContact->contact_id = $this->individual[$i*7];
            $groupContact->status = $this->_getRandomElement($this->groupMembershipStatus);  // membership status

            $subscriptionHistory =& new CRM_Contact_DAO_SubscriptionHistory();
            $subscriptionHistory->contact_id = $groupContact->contact_id;
            $subscriptionHistory->group_id = $groupContact->group_id;
            $subscriptionHistory->status = $groupContact->status;
            $subscriptionHistory->method = $this->_getRandomElement($this->subscriptionHistoryMethod); // method
            $subscriptionHistory->date = $this->_getRandomDate();

            if ($groupContact->status != 'Pending') {
                $this->_insert($groupContact);
            }
            $this->_insert($subscriptionHistory);
        }
    }



    
    /*******************************************************
     *
     * addNote()
     *
     * This method populates the crm_note table
     *
     *******************************************************/
    public function addNote()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        $note =& new CRM_Core_DAO_Note();
        $note->entity_table = 'civicrm_contact';
        $note->contact_id   = 1;

        for ($i=0; $i<self::NUM_CONTACT; $i++) {
            $note->entity_id = $this->contact[$i];
            if ($this->contact[$i] % 5 || $this->contact[$i] % 3 || $this->contact[$i] % 2) {
                $this->_insertNote($note);
            }
        }
    }




    /*******************************************************
     *
     * addActivityHistory()
     *
     * This method populates the crm_activity_history table
     *
     *******************************************************/
    public function addActivityHistory()
    {

        // CRM_Core_Error::le_method();
        // CRM_Core_Error::ll_method();

        $contactDAO =& new CRM_Contact_DAO_Contact();
        $contactDAO->contact_type = 'Individual';
        $contactDAO->selectAdd();
        $contactDAO->selectAdd('id');
        $contactDAO->orderBy('sort_name');
        $contactDAO->find();

        $count = 0;

        while($contactDAO->fetch()) {
            if ($count++ > 2) {
                break;
            }
            for ($i=0; $i<self::NUM_ACTIVITY_HISTORY; $i++) {
                $activityHistoryDAO =& new CRM_Core_DAO_ActivityHistory();
                $activityHistoryDAO->entity_table  = 'civicrm_contact';
                $activityHistoryDAO->entity_id     = $contactDAO->id;
                $activityHistoryDAO->activity_type = $this->_getRandomElement($this->activity_type);
                $activityHistoryDAO->module = $this->_getRandomElement($this->module);
                
                if ($i % 2) {
                    $activityHistoryDAO->callback = $this->_getRandomElement($this->callback);
                }
                $activityHistoryDAO->activity_id = mt_rand(1,1111);
                $activityHistoryDAO->activity_summary = $this->_getRandomString(mt_rand(55, 222));
                $activityHistoryDAO->activity_date = $this->_getRandomDate();
                $this->_insert($activityHistoryDAO);
            }
        }
    }

    static function getZipCodeInfo( ) {
        static $stateMap;
        
        if ( ! isset( $stateMap ) ) {
            $query = 'SELECT id, abbreviation from civicrm_state_province where country_id = 1228';
            $dao =& new CRM_Core_DAO( );
            $dao->query( $query );
            $stateMap = array( );
            while ( $dao->fetch( ) ) {
                $stateMap[$dao->abbreviation] = $dao->id;
            }
        }

        $offset = mt_rand( 1, 43000 );
        $query = "SELECT city, state, zip, latitude, longitude FROM zipcodes LIMIT $offset, 1";
        $dao =& new CRM_Core_DAO( );
        $dao->query( $query );
        while ( $dao->fetch( ) ) {
            if ( $stateMap[$dao->state] ) {
                $stateID = $stateMap[$dao->state];
            } else {
                $stateID = 1004;
            }
            
            $zip = str_pad($dao->zip, 5, '0', STR_PAD_LEFT);
            return array( 1228, $stateID, $dao->city, $zip, $dao->latitude, $dao->longitude );
        }
    }

    static function getLatLong( $zipCode ) {
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
    
    function addMembershipTypeStatus()
    {
        $organizationDAO = new CRM_Contact_DAO_Organization();
        $organizationDAO->id = 5;
        $organizationDAO->find(true);
        $contact_id = $organizationDAO->contact_id;
        
        $membershipType = "INSERT INTO civicrm_membership_type
        (name, description, member_of_contact_id, contribution_type_id, minimum_fee, duration_unit, duration_interval, period_type, fixed_period_start_day, fixed_period_rollover_day, relationship_type_id, visibility, weight, is_active)
        VALUES
        ('Trainee', 'Register for this membership if you are a trainee', ". $contact_id .", 3, 
                                              100, 1, 50, 2, 0101, 0104, 7, 'Admin', 1, 1),
        ('Permanent Employee', 'Register for this membership if you are a permanent employee', ". $contact_id .", 1, 
                                              500, 3, 30, 1, 0101, 1231, 7, 'Public', 2, 1),
        ('Share Holder', 'Register for this membership if you are a share holder', ". $contact_id .", 2, 
                                             1000, 2, 4, 2, 0101, 0105, 7, 'Public', 3, 1);
        ";
        CRM_Core_DAO::executeQuery( $membershipType, CRM_Core_DAO::$_nullArray );
        
        $membershipStatus = "INSERT INTO civicrm_membership_status
        (name, start_event, start_event_adjust_unit, start_event_adjust_interval, end_event, end_event_adjust_unit, end_event_adjust_interval, is_current_member, is_admin, weight, is_active)
        VALUES
        ('Temporary', 1, 1, 1, 3, 1, 50, 1, 1, 1, 1),
        ('Permanent', 3, 3, 0, 2, 3, 30, 1, 1, 1, 1),
        ('Grace', 2, 1, 0, 3, 2, 10, 1, 1, 1, 1);
        ";
        CRM_Core_DAO::executeQuery( $membershipStatus, CRM_Core_DAO::$_nullArray );
    }
    
    function addMembership()
    {
        $contact = new CRM_Contact_DAO_Contact();
        $contact->query("SELECT id FROM civicrm_contact");
        while ( $contact->fetch() ) {
            $contacts[] = $contact->id;
        }
        shuffle($contacts);
        $randomContacts = array_slice($contacts, 20, 25);
        
        $membership = "
INSERT INTO civicrm_membership
        (contact_id, membership_type_id, join_date, start_date, end_date, source, calculated_status_id)
VALUES
        ( ". $randomContacts[0]  .", 1, null, '2004-10-21', '2005-01-20', 'Payment', 1),
        ( ". $randomContacts[1]  .", 2, '2004-01-10', null,'2033-12-09', 'Donation', 2),
        ( ". $randomContacts[2]  .", 2, null,'2004-03-05', '2005-01-04', 'Check', 3),
        ( ". $randomContacts[3]  .", 1, null,'2005-10-21', '2006-01-20', 'Payment', 1),
        ( ". $randomContacts[4]  .", 2, '2005-01-10', null,'2034-12-09', 'Donation', 2),
        ( ". $randomContacts[5]  .", 2, null,'2005-03-05', '2034-01-04', 'Check', 3),
        ( ". $randomContacts[6]  .", 1, null, '2006-10-21', '2007-01-20', 'Payment', 1),
        ( ". $randomContacts[7]  .", 2, '2006-01-10', null,'2035-12-09', 'Donation', 2),
        ( ". $randomContacts[8]  .", 2, null,'2005-03-05', '2006-01-04', 'Check', 3),
        ( ". $randomContacts[9]  .", 1, null, '2004-10-21', '2005-01-20', 'Payment', 1),
        ( ". $randomContacts[10]  .", 2, '2006-01-10', null,'2035-12-09', 'Donation', 2),
        ( ". $randomContacts[11]  .", 2, null,'2006-03-05', '2007-01-04', 'Check', 3),
        ( ". $randomContacts[12]  .", 1, null, '2005-10-21', '2006-01-20', 'Payment', 1),
        ( ". $randomContacts[13]  .", 2, '2004-01-10', null,'2033-12-09', 'Donation', 2),
        ( ". $randomContacts[14]  .", 2, null,'2005-03-05', '2006-01-04', 'Check', 3),
        ( ". $randomContacts[15]  .", 1, null, '2004-10-21', '2005-01-20', 'Payment', 1),
        ( ". $randomContacts[16]  .", 2, '2005-01-10', null,'2034-12-09', 'Donation', 2),
        ( ". $randomContacts[17]  .", 2, null,'2004-03-05', '2005-01-04', 'Check', 3),
        ( ". $randomContacts[18]  .", 1, null, '2005-10-21', '2006-01-20', 'Payment', 1),
        ( ". $randomContacts[19]  .", 2, '2006-01-10', null,'2035-12-09', 'Donation', 2),
        ( ". $randomContacts[20]  .", 2, null,'2005-03-05', '2006-01-04', 'Check', 3),
        ( ". $randomContacts[21]  .", 1, null, '2004-10-21', '2005-01-20', 'Payment', 1),
        ( ". $randomContacts[22]  .", 2, '2005-01-10', null,'2034-12-09', 'Donation', 2),
        ( ". $randomContacts[23]  .", 2, null,'2004-03-05', '2005-01-04', 'Check', 3),
        ( ". $randomContacts[24]  .", 2, null,'2005-03-05', '2006-01-04', 'Check', 3);
";
        CRM_Core_DAO::executeQuery( $membership, CRM_Core_DAO::$_nullArray );
    }
}

function user_access( $str = null ) {
    return true;
}

function add_contributions( ) {

    $query = "
INSERT INTO civicrm_contribution
    (domain_id, contact_id, contribution_type_id, payment_instrument_id, receive_date, non_deductible_amount, total_amount, trxn_id, currency, cancel_date, cancel_reason, receipt_date, thankyou_date, source)
VALUES
    (1, 2, 1, 4, '2005-10-11 00:00:00', 0.00, 125.00, 'check #1041', 'USD', NULL, NULL, NULL, NULL, 'Oct 2005 Mailer 1'),
    (1, 4, 1, 1, '2005-09-21 00:00:00', 0.00, 50.00, 'P20901X1', 'USD', NULL, NULL, NULL, NULL, 'Online: Save the Penguins'),
    (1, 6, 1, 4, '2005-10-29 00:00:00', 0.00, 25.00, 'check #2095', 'USD', NULL, NULL, NULL, NULL, 'Oct 2005 Mailer 1'),
    (1, 8, 1, 4, '2005-10-11 00:00:00', 0.00, 50.00, 'check #10552', 'USD', NULL, NULL, NULL, NULL, 'Oct 2005 Mailer 1'),
    (1, 16, 1, 4, '2005-10-15 00:00:00', 0.00, 500.00, 'check #509', 'USD', NULL, NULL, NULL, NULL, 'Oct 2005 Mailer 1'),
    (1, 19, 1, 4, '2005-10-11 00:00:00', 0.00, 175.00, 'check #102', 'USD', NULL, NULL, NULL, NULL, 'Oct 2005 Mailer 1'),
    (1, 82, 1, 1, '2005-09-27 00:00:00', 0.00, 50.00, 'P20193L2', 'USD', NULL, NULL, NULL, NULL, 'Online: Save the Penguins'),
    (1, 92, 1, 1, '2005-09-08 00:00:00', 0.00, 10.00, 'P40232Y3', 'USD', NULL, NULL, NULL, NULL, 'Online: Save the Penguins'),
    (1, 34, 1, 1, '2005-10-22 00:00:00', 0.00, 250.00, 'P20193L6', 'USD', NULL, NULL, NULL, NULL, 'Online: Save the Penguins');
";
    CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

    $query = "
INSERT INTO civicrm_activity_history
    (entity_table, entity_id, activity_type, module, callback, activity_id, activity_summary, activity_date)
VALUES
    ('civicrm_contact', 2, 'Donation', 'CiviContribute', 'CRM_Contribute_Page_Contribution::details', 1, '125.00 USD - Donation (from import on Tue, 29 Nov 2005 13:36:16)', '2005-10-11 00:00:00'),
    ('civicrm_contact', 4, 'Donation', 'CiviContribute', 'CRM_Contribute_Page_Contribution::details', 2, '50.00 USD - Donation (from import on Tue, 29 Nov 2005 13:36:16)', '2005-09-21 00:00:00'),
    ('civicrm_contact', 6, 'Donation', 'CiviContribute', 'CRM_Contribute_Page_Contribution::details', 3, '25.00 USD - Donation (from import on Tue, 29 Nov 2005 13:36:16)', '2005-10-29 00:00:00'),
    ('civicrm_contact', 8, 'Donation', 'CiviContribute', 'CRM_Contribute_Page_Contribution::details', 4, '50.00 USD - Donation (from import on Tue, 29 Nov 2005 13:36:16)', '2005-10-11 00:00:00'),
    ('civicrm_contact', 16, 'Donation', 'CiviContribute', 'CRM_Contribute_Page_Contribution::details', 5, '500.00 USD - Donation (from import on Tue, 29 Nov 2005 13:36:16)', '2005-10-15 00:00:00'),
    ('civicrm_contact', 19, 'Donation', 'CiviContribute', 'CRM_Contribute_Page_Contribution::details', 6, '175.00 USD - Donation (from import on Tue, 29 Nov 2005 13:36:16)', '2005-10-11 00:00:00'),
    ('civicrm_contact', 82, 'Donation', 'CiviContribute', 'CRM_Contribute_Page_Contribution::details', 7, '50.00 USD - Donation (from import on Tue, 29 Nov 2005 13:36:16)', '2005-09-27 00:00:00'),
    ('civicrm_contact', 92, 'Donation', 'CiviContribute', 'CRM_Contribute_Page_Contribution::details', 8, '10.00 USD - Donation (from import on Tue, 29 Nov 2005 13:36:16)', '2005-09-08 00:00:00'),
    ('civicrm_contact', 34, 'Donation', 'CiviContribute', 'CRM_Contribute_Page_Contribution::details', 9, '250.00 USD - Donation (from import on Tue, 29 Nov 2005 13:36:16)', '2005-10-22 00:00:00');
";
    CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

}

echo("Starting data generation on " . date("F dS h:i:s A") . "\n");
$obj1 =& new CRM_GCD();
$obj1->initID();
$obj1->parseDataFile();
$obj1->initDB();
$obj1->addDomain();
$obj1->addContact();
$obj1->addIndividual();
$obj1->addHousehold();
$obj1->addOrganization();
$obj1->addRelationship();
$obj1->addLocation();
$obj1->addEntityTag();
$obj1->addGroup();
$obj1->addNote();
$obj1->addActivityHistory();
add_contributions( );
$obj1->addMembershipTypeStatus();
$obj1->addMembership();

echo("Ending data generation on " . date("F dS h:i:s A") . "\n");

?>
