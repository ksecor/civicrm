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
 *******************************************************/

/*******************************************************
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


require_once '../modules/config.inc.php';
require_once 'CRM/Config.php';

class CRM_GCD {

    /*******************************************************
     * constants
     *******************************************************/
    const DATA_FILENAME="sample_data.xml";

    const NUM_DOMAIN = 10;
    const NUM_CONTACT = 100;

    const INDIVIDUAL_PERCENT = 75;
    const HOUSEHOLD_PERCENT = 15;
    const ORGANIZATION_PERCENT = 10;
    const NUM_INDIVIDUAL_PER_HOUSEHOLD = 4;


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
    // const ADD_TO_DB=FALSE;
    const DEBUG_LEVEL=1;


    
    /*********************************
     * private members
     *********************************/
    
    // enum's from database
    private $preferred_communication_method = array('Phone', 'Email', 'Post');
    private $greeting_type = array('Formal', 'Informal', 'Honorific', 'Custom', 'Other');
    private $contact_type = array('Individual', 'Household', 'Organization');
    private $gender = array('Female', 'Male', 'Transgender');    
    private $phone_type = array('Phone', 'Mobile', 'Fax', 'Pager');    

    // almost enums
    private $prefix = array('Mr', 'Mrs', 'Ms', 'Dr');
    private $suffix = array('Jr', 'Sr');

    // store domain id's
    private $domain = array();

    // store contact id's
    private $contact = array();
    private $individual = array();
    private $household = array();
    private $organization = array();
    

    // store names, firstnames, street 1, street2
    private $first_name = array();
    private $last_name = array();
    private $street_name = array();
    private $supplemental_address_1 = array();
    private $address_direction = array();
    private $street_type = array();
    private $email_domain = array();
    private $email_tld = array();
    private $organization_name = array();
    private $organization_field = array();
    private $organization_type = array();

    // stores the strict individual id and household id to individual id mapping
    private $strict_individual = array();
    private $household_individual = array();
    
    // stores location id's
    private $location = array();
    private $strict_individual_location = array();
    private $household_location = array();
    private $organization_location = array();

    // sample data in xml format
    private $sample_data = NULL;
    
    // private vars
    private $num_individual = 0;
    private $num_household = 0;
    private $num_organization = 0;
    private $num_strict_individual = 0;

    private $num_location = 0;
    private $num_strict_individual_location = 0;
    private $num_household_location = 0;
    private $num_organization_location = 0;



  /*********************************
   * private methods
   *********************************/

  // log entry for "entering" a function
    private function lee()
    {
        $array1 = debug_backtrace();
        $string1 = "\n\nentering " . $array1[1]['class'] . "::" . $array1[1]['function'] . "()\n";
        echo($string1);
    }

    // log entry for "leaving" a function
    private function lel()
    {
        $array1 = debug_backtrace();
        $string1 = "leaving  " . $array1[1]['class'] . "::" . $array1[1]['function'] . "()\n";
        echo($string1);
    }

    function debug_var($variable_name, &$variable)
    {
        // check if variable is set
        if(!isset($variable)) {
            $out = "\$$variable_name is not set";
        } else {
            $out = print_r($variable, true);
            $out = "\$$variable_name = $out";
            // reset if it is an array
            if(is_array($variable)) {
                reset($variable);
            }
        }
        echo '$out';
    }


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



    /*******************************************************
     *
     * Start of public functions
     *
     *******************************************************/
    // constructor
    function __construct()
    {
        $this->lee();

        // initialize all the vars
        $this->num_individual = self::INDIVIDUAL_PERCENT * self::NUM_CONTACT / 100;
        $this->num_household = self::HOUSEHOLD_PERCENT * self::NUM_CONTACT / 100;
        $this->num_organization = self::ORGANIZATION_PERCENT * self::NUM_CONTACT / 100;
        $this->num_strict_individual = $this->num_individual - ($this->num_household * self::NUM_INDIVIDUAL_PER_HOUSEHOLD);

        $this->num_strict_individual_location = $this->num_strict_individual;
        $this->num_household_location = $this->num_household;
        $this->num_organization_location = $this->num_organization;

        $this->num_location = $this->num_strict_individual_location + $this->num_household_location + $this->num_organization_location;

        $this->lel();
    }


    public function parseDataFile()
    {

        $this->lee();
        
        $sample_data = simplexml_load_file(self::DATA_FILENAME);

        // first names
        foreach ($sample_data->first_names->first_name as $first_name) {
            $this->first_name[] = trim($first_name);
        }

        // last names
        foreach ($sample_data->last_names->last_name as $last_name) {
            $this->last_name[] = trim($last_name);
        }

        //  street names
        foreach ($sample_data->street_names->street_name as $street_name) {
            $this->street_name[] = trim($street_name);
        }

        //  supplemental address 1
        foreach ($sample_data->supplemental_addresses_1->supplemental_address_1 as $supplemental_address_1) {
            $this->supplemental_address_1[] = trim($supplemental_address_1);
        }

        //  address directions
        foreach ($sample_data->address_directions->address_direction as $address_direction) {
            $this->address_direction[] = trim($address_direction);
        }

        // street types
        foreach ($sample_data->street_types->street_type as $street_type) {
            $this->street_type[] = trim($street_type);
        }

        // email domains
        foreach ($sample_data->email_domains->email_domain as $email_domain) {
            $this->email_domain[] = trim($email_domain);
        }

        // email top level domain
        foreach ($sample_data->email_tlds->email_tld as $email_tld) {
            $this->email_tld[] = trim($email_tld);
        }

        // organization name
        foreach ($sample_data->organization_names->organization_name as $organization_name) {
            $this->organization_name[] = trim($organization_name);
        }

        // organization field
        foreach ($sample_data->organization_fields->organization_field as $organization_field) {
            $this->organization_field[] = trim($organization_field);
        }

        // organization type
        foreach ($sample_data->organization_types->organization_type as $organization_type) {
            $this->organization_type[] = trim($organization_type);
        }

        $this->lel();
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
        $config = CRM_Config::singleton();
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

        $this->lee();

        // may use this function in future if needed to get
        // a consistent pattern of random numbers.

        // get the domain and contact id arrays
        $this->domain = range(1, self::NUM_DOMAIN);
        shuffle($this->domain);
        $this->contact = range(1, self::NUM_CONTACT);
        shuffle($this->contact);

        // get the individual, household  and organizaton contacts
        $offset = 0;
        $this->individual = array_slice($this->contact, $offset, $this->num_individual);
        $offset += $this->num_individual;
        $this->household = array_slice($this->contact, $offset, $this->num_household);
        $offset += $this->num_household;
        $this->organization = array_slice($this->contact, $offset, $this->num_organization);

        // get the strict individual contacts (i.e individual contacts not belonging to any household)
        $this->strict_individual = array_slice($this->individual, 0, $this->num_strict_individual);
        
        // get the household to individual mapping array
        $this->household_individual = array_diff($this->individual, $this->strict_individual);
        $this->household_individual = array_chunk($this->household_individual, self::NUM_INDIVIDUAL_PER_HOUSEHOLD);
        $this->household_individual = array_combine($this->household, $this->household_individual);


        // contact location generation
        $this->location = range(1, $this->num_location);
        shuffle($this->location);

        $offset = 0;
        $this->strict_individual_location = array_slice($this->location, $offset, $this->num_strict_individual_location);
        $offset += $this->num_strict_individual_location;
        $this->household_location = array_slice($this->location, $offset, $this->num_household_location);
        $offset += $this->num_household_location;
        $this->organization_location = array_slice($this->location, $offset, $this->num_organization_location);

        $this->lel();

    } // end of method initID



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

        $this->lee();
        $domain = new CRM_DAO_Domain();
        for ($id=2; $id<=self::NUM_DOMAIN; $id++) {
            // domain name is pretty simple. it is "Domain $id"
            $domain->name = "Domain $id";
            $domain->description = "Description $id";
            
            // insert domain
            $this->_insert($domain);
        }

        $this->lel();

    } // end of method addDomain

    /*******************************************************
     *
     * addContact()
     *
     * This method adds data to the contact table
     *
     * id - from $contact
     * domain_id (fkey into domain) (random - 1 to num_domain)
     * contact_type 'Individual' 'Household' 'Organization'
     * sort_name (Name + id)
     * preferred_communication (random 1 to 3)
     *
     *******************************************************/
    public function addContact()
    {
        $this->lee();

        // add contacts
        $contact = new CRM_Contact_DAO_Contact();

        for ($id=1; $id<=self::NUM_CONTACT; $id++) {
            $contact->domain_id = mt_rand(1, self::NUM_DOMAIN);            
            $contact->contact_type = $this->getContactType($id);
            $contact->do_not_phone = mt_rand(0, 1);
            $contact->do_not_email = mt_rand(0, 1);
            $contact->do_not_post = mt_rand(0, 1);
            $contact->hash = crc32($contact->sort_name);
            
            // choose randomly from phone, email and snail mail
            $contact->preferred_communication_method = $this->_getRandomElement($this->preferred_communication_method);

            $this->_insert($contact);
        }
        
        $this->lel();

    } // end of method addContact


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

        $this->lee();

        $individual = new CRM_Contact_DAO_Individual();
        $contact = new CRM_Contact_DAO_Contact();

        for ($id=1; $id<=$this->num_individual; $id++) {
            $individual->contact_id = $this->individual[($id-1)];
            $individual->first_name = ucfirst($this->_getRandomElement($this->first_name));
            $individual->middle_name = ucfirst($this->_getRandomChar());
            $individual->last_name = ucfirst($this->_getRandomElement($this->last_name));
            $individual->prefix = $this->_getRandomElement($this->prefix);
            $individual->suffix = $this->_getRandomElement($this->suffix);
            $individual->display_name = "$individual->first_name $individual->middle_name $individual->last_name";
            $individual->greeting_type = $this->_getRandomElement($this->greeting_type);
            $individual->gender = $this->_getRandomElement($this->gender);
            //$individual->birth_date = date("Y-m-d", mt_rand(0, time()));
            // there's some bug or irrational logic in DB_DataObject hence the above iso format does not work
            $individual->birth_date = date("Ymd", mt_rand(0, time()));
            $individual->is_deceased = mt_rand(0, 1);
            // $individual->phone_to_household_id = mt_rand(0, 1);
            // $individual->email_to_household_id = mt_rand(0, 1);
            // $individual->mail_to_household_id = mt_rand(0, 1);
            $this->_insert($individual);

            // also update the sort name for the contact id.
            $contact->id = $individual->contact_id;
            $contact->sort_name = $individual->display_name;
            $this->_update($contact);
        }
        
        $this->lel();
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

        $this->lee();

        $household = new CRM_Contact_DAO_Household();
        $contact = new CRM_Contact_DAO_Contact();
        
        for ($id=1; $id<=$this->num_household; $id++) {
            $household->contact_id = $this->household[($id-1)];
            $household->primary_contact_id = $this->household_individual[$household->contact_id][0];

            // get the last name of the primary contact id
            $individual = new CRM_Contact_DAO_Individual();
            $individual->contact_id = $household->primary_contact_id;
            $individual->find(true);
            $first_name = $individual->first_name;
            $last_name = $individual->last_name;

            // need to name the household and nick name appropriately
            $household->household_name = "$first_name $last_name" . "'s home";
            $household->nick_name = "$last_name" . "'s home";
            $this->_insert($household);

            // need to update the sort name for the main contact table
            $contact->id = $household->contact_id;
            $contact->sort_name = $household->household_name;
            $this->_update($contact);
        }

        $this->lel();
    } // end of method addHousehold




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

        $this->lee();

        $organization = new CRM_Contact_DAO_Organization();
        $contact = new CRM_Contact_DAO_Contact();       

        for ($id=1; $id<=$this->num_organization; $id++) {
            $organization->contact_id = $this->organization[($id-1)];
            $name = $this->_getRandomElement($this->organization_name) . " " . $this->_getRandomElement($this->organization_field) . " " . $this->_getRandomElement($this->organization_type);
            $organization->organization_name = $name;
            $organization->primary_contact_id = $this->_getRandomElement($this->strict_individual);
            $this->_insert($organization);

            // need to update the sort name for the main contact table
            $contact->id = $organization->contact_id;
            $contact->sort_name = $organization->organization_name;
            $this->_update($contact);
        }

        $this->lel();

    } // end of method addOrganization



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

        $this->lee();

        $relationship = new CRM_Contact_DAO_Relationship();

        foreach ($this->household_individual as $household_id => $household_member) {
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
        $this->lel();
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

        $this->lee();

        $location = new CRM_Contact_DAO_Location();
        $address = new CRM_Contact_DAO_Address();
        $email = new CRM_Contact_DAO_Email();
        $phone = new CRM_Contact_DAO_Phone();
        
        // primary location
        $location->is_primary = 1;

        for ($location_id=1; $location_id<=$this->num_location; $location_id++) {

            // get the index of the location in the location array
            $index = array_search($location_id, $this->location);

            if ($index < $this->num_strict_individual) {
                // this belongs to the individual
                $location->location_type_id = self::HOME;
                $location->contact_id = $this->strict_individual[$index];

            } else if ($index < ($this->num_strict_individual+$this->num_household)) {
                // belongs to household 
                $location->location_type_id = self::MAIN;
                $location->contact_id = $this->household[$index-$this->num_strict_individual];

            } else {
                // belongs to organization
                $location->location_type_id = self::MAIN;
                $location->contact_id = $this->organization[$index-$this->num_strict_individual-$this->num_household];
            }

            $this->_insert($location);

            // add addresses now currently we are adding only 1 address for each location
            $address->location_id = $location_id;
            $address->street_number = mt_rand(1, 1000);
            $address->street_number_suffix = ucfirst($this->_getRandomChar());
            $address->street_number_predirectional = $this->_getRandomElement($this->address_direction);
            $address->street_name = ucwords($this->_getRandomElement($this->street_name));
            $address->street_type = $this->_getRandomElement($this->street_type);
            $address->street_number_postdirectional = $this->_getRandomElement($this->address_direction);

            $address->street_address = $address->street_number_predirectional . " " . $address->street_number .  $address->street_number_suffix .  " " . $address->street_name .  " " . $address->street_type . " " . $address->street_number_postdirectional;

            $address->supplemental_address_1 = ucwords($this->_getRandomElement($this->supplemental_address_1));
            $address->city = "Mumbai";
            $address->county_id = 1;
            $address->state_province_id = 1004;
            $address->postal_code = mt_rand(400001, 499999);
            $address->country_id = 1228;
            $address->geo_coord_id = 1;

            $this->_insert($address);

            // need a new object here otherwise the find will use values of the previous results.
            $contact = new CRM_Contact_DAO_Contact();
            $contact->id = $location->contact_id;
            $contact->find(true);
            $sort_name = $contact->sort_name;
            $sort_name = strtolower(str_replace(" ", "", $sort_name));

            // add 3 email for each location
            for ($email_id=1; $email_id<=3; $email_id++) {
                ($email_id == 1) ? $email->is_primary = 1:$email->is_primary=0;
                $email->location_id = $location_id;
                // get the sort name of the contact
                $email_domain = $this->_getRandomElement($this->email_domain);
                $tld = $this->_getRandomElement($this->email_tld);
                $email->email = $sort_name . "@" . $email_domain . "." . $tld;
                $this->_insert($email);
            }

            // add 3 phones for each location
            for ($phone_id=1; $phone_id<=3; $phone_id++) {
                ($phone_id == 1) ? $phone->is_primary = 1:$phone->is_primary=0;
                $phone->location_id = $location_id;
                $phone->phone = mt_rand(11111111, 99999999);
                $phone->phone_type = $this->_getRandomElement($this->phone_type);
                $this->_insert($phone);
            }            
        }

        $this->lel();
    }


    public function printID() {

        $this->lee();

        echo("\n*******************************************************\ndomain\n");
        print_r($this->domain);
        echo("\n");

        echo("\n*******************************************************\ncontact\n");
        print_r($this->contact);
        echo("\n");

        echo("\n*******************************************************\nindividual\n");
        print_r($this->individual);
        echo("\n");

        echo("\n*******************************************************\nhousehold\n");
        print_r($this->household);
        echo("\n");

        echo("\n*******************************************************\norganization\n");
        print_r($this->organization);
        echo("\n");

        echo("\n*******************************************************\nstrict_individual\n");
        print_r($this->strict_individual);
        echo("\n");

        echo("\n*******************************************************\nhousehold_individual\n");
        print_r($this->household_individual);
        echo("\n");

        echo("\n*******************************************************\nlocation\n");
        print_r($this->location);
        echo("\n");

        echo("\n*******************************************************\nstrict_individual_location\n");
        print_r($this->strict_individual_location);
        echo("\n");

        echo("\n*******************************************************\nhousehold_location\n");
        print_r($this->household_location);
        echo("\n");

        echo("\n*******************************************************\norganization_location\n");
        print_r($this->organization_location);
        echo("\n");

        $this->lel();

    } // end of method printID



} // end of class CRM_GenerateContactData


echo("Starting on " . date("F dS h:i:s A") . "\n");

$obj1 = new CRM_GCD();

$obj1->parseDataFile();
$obj1->initID();
$obj1->initDB();
$obj1->printID();
$obj1->addDomain();
$obj1->addContact();
$obj1->addIndividual();
$obj1->addHousehold();
$obj1->addOrganization();
$obj1->addRelationship();
$obj1->addLocation();

echo("Ending on " . date("F dS h:i:s A") . "\n");

?>
