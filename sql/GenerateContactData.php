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
    private $preferred_communication_array = array(1=>'Phone', 'Email', 'Post');
    private $greeting_type_array = array(1=>'Formal', 'Informal', 'Honorific', 'Custom', 'Other');
    private $contact_type_array = array(1=>'Individual', 'Household', 'Organization');
    private $gender_array = array(1=>'Female', 'Male', 'Transgender');    
    private $phone_type_array = array(1=>'Phone', 'Mobile', 'Fax', 'Pager');    

    // almost enums
    private $prefix_array = array(1=>'Mr', 'Mrs', 'Ms', 'Dr');
    private $suffix_array = array(1=>'Jr', 'Sr');

    // store domain id's
    private $domain_array = array();

    // store contact id's
    private $contact_array = array();
    private $individual_array = array();
    private $household_array = array();
    private $organization_array = array();
    
    // stores the strict individual id and household id to individual id mapping
    private $strict_individual_array = array();
    private $household_individual_array = array();
    
    // stores location id's
    private $location_array = array();
    private $strict_individual_location_array = array();
    private $household_location_array = array();
    private $organization_location_array = array();
    
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
    private function getRandomString($size=32)
    {
        mt_srand();
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
    } // end of getRandomString


    // get a randomly generated string
    private function getRandomBoolean()
    {
        return mt_rand(0,1);

    } // end of getRandomBoolean



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




    /*******************************************************
     *
     * Start of public functions
     *
     *******************************************************/
    // constructor
    function __construct()
    {
        $this->lee();
        // seed the random to get sequence of users.
        mt_srand(1);

        // initialize all the vars
        $this->num_individual = self::INDIVIDUAL_PERCENT * self::NUM_CONTACT / 100;
        $this->num_household = self::HOUSEHOLD_PERCENT * self::NUM_CONTACT / 100;
        $this->num_organization = self::ORGANIZATION_PERCENT * self::NUM_CONTACT / 100;
        $this->num_strict_individual = $this->num_individual - ($this->num_household * self::NUM_INDIVIDUAL_PER_HOUSEHOLD);

        $this->num_strict_individual_location = $this->num_strict_individual;
        $this->num_household_location = $this->num_household;
        $this->num_organization_location = $this->num_organization;

        $this->num_location = $this->num_strict_individual_location + $this->num_household_location + $this->num_organization_location;


        $this->debug_var("this", $this);
        $this->lel();
    }



    public function getContactType($id)
    {
        if(in_array($id, $this->individual_array))
            return 'Individual';
        if(in_array($id, $this->household_array))
            return 'Household';
        if(in_array($id, $this->organization_array))
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
        $this->domain_array = range(1, self::NUM_DOMAIN);
        shuffle($this->domain_array);
        $this->contact_array = range(1, self::NUM_CONTACT);
        shuffle($this->contact_array);

        // get the individual, household  and organizaton contacts
        $offset = 0;
        $this->individual_array = array_slice($this->contact_array, $offset, $this->num_individual);
        $offset += $this->num_individual;
        $this->household_array = array_slice($this->contact_array, $offset, $this->num_household);
        $offset += $this->num_household;
        $this->organization_array = array_slice($this->contact_array, $offset, $this->num_organization);

        // get the strict individual contacts (i.e individual contacts not belonging to any household)
        $this->strict_individual_array = array_slice($this->individual_array, 0, $this->num_strict_individual);
        
        // get the household to individual mapping array
        $this->household_individual_array = array_diff($this->individual_array, $this->strict_individual_array);
        $this->household_individual_array = array_chunk($this->household_individual_array, self::NUM_INDIVIDUAL_PER_HOUSEHOLD);
        $this->household_individual_array = array_combine($this->household_array, $this->household_individual_array);


        // contact location generation
        $this->location_array = range(1, $this->num_location);
        shuffle($this->location_array);

        $offset = 0;
        $this->strict_individual_location_array = array_slice($this->location_array, $offset, $this->num_strict_individual_location);
        $offset += $this->num_strict_individual_location;
        $this->household_location_array = array_slice($this->location_array, $offset, $this->num_household_location);
        $offset += $this->num_household_location;
        $this->organization_location_array = array_slice($this->location_array, $offset, $this->num_organization_location);

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

        for ($id=1; $id<=self::NUM_DOMAIN; $id++) {
            $domain = new CRM_DAO_Domain();
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
     * id - from $contact_array
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

        for ($id=1; $id<=self::NUM_CONTACT; $id++) {
            $contact = new CRM_Contact_DAO_Contact();
            $contact->domain_id = mt_rand(1, self::NUM_DOMAIN);            
            $contact->contact_type = $this->getContactType($id);

            // brain dead generation :(
            $contact->legal_id = "Legal $id"; 
            $contact->external_id = "External $id";
            $contact->sort_name = "Sort Name $id";
            $contact->home_URL = "http://www.$id.com/";
            $contact->home_URL = "http://www.$id.com/logo.png";
            $contact->source = "Source $id";
            
            $contact->do_not_phone = mt_rand(0, 1);
            $contact->do_not_email = mt_rand(0, 1);
            $contact->do_not_post = mt_rand(0, 1);
            $contact->hash = crc32($contact->sort_name);
            
            // choose randomly from phone, email and snail mail
            $contact->preferred_communication_method = $this->preferred_communication_array[mt_rand(1, count($this->preferred_communication_array))];

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
     * contact_uuid - individual_array
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

        for ($id=1; $id<=$this->num_individual; $id++) {
            $individual = new CRM_Contact_DAO_Individual();
            $individual->contact_id = $this->individual_array[($id-1)];
            $individual->first_name = "First Name $id";
            $individual->middle_name = "Middle Name $id";
            $individual->last_name = "Last Name $id";            
            $individual->prefix = $this->prefix_array[mt_rand(1, count($this->prefix_array))];
            $individual->suffix = $this->suffix_array[mt_rand(1, count($this->suffix_array))];
            $individual->display_name = "$individual->first_name $individual->last_name";
            $individual->greeting_type = $this->greeting_type_array[mt_rand(1, count($this->greeting_type_array))];
            $individual->custom_greeting = "Custom Greeting $id";
            $individual->job_title = "Job Title $id";
            $individual->gender = $this->gender_array[mt_rand(1, count($this->gender_array))];
            //$individual->birth_date = date("Y-m-d", mt_rand(0, time()));
            // there's some bug or irrational logic in DB_DataObject hence the above iso format does not work
            $individual->birth_date = date("Ymd", mt_rand(0, time()));
            $individual->is_deceased = mt_rand(0, 1);
            // $individual->phone_to_household_id = mt_rand(0, 1);
            // $individual->email_to_household_id = mt_rand(0, 1);
            // $individual->mail_to_household_id = mt_rand(0, 1);
            

            $this->_insert($individual);

        }
        
        $this->lel();
        
    } // end of method addIndividual




    /*******************************************************
     *
     * addHousehold()
     *
     * This method adds data to the contact_household table
     *
     * The following fields are generated and added.
     *
     * contact_uuid - household_individual_array
     * contact_rid - latest one
     * household_name 'household $contact_uuid primary contact $primary_contact_uuid'
     * nick_name 'nick $contact_uuid'
     * primary_contact_uuid = $household_individual[$contact_uuid][0];
     *
     *******************************************************/
    public function addHousehold()
    {

        $this->lee();

        var_dump($this->household_array);
        
        for ($id=1; $id<=$this->num_household; $id++) {
            $household = new CRM_Contact_DAO_Household();
            $household->contact_id = $this->household_array[($id-1)];
            $household->household_name = "Household Name $id";
            $household->nick_name = "Nick Name $id";
            $household->primary_contact_id = $this->household_individual_array[$household->contact_id][0];
            $this->_insert($household);
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
     * contact_uuid - organization_array
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
        
        for ($id=1; $id<=$this->num_organization; $id++) {
            $organization = new CRM_Contact_DAO_Organization();
            $organization->contact_id = $this->organization_array[($id-1)];
            $organization->organization_name = "Organization Name $id";
            $organization->legal_name = "Legal Name $id";
            $organization->nick_name = "Nick Name $id";
            $organization->sic_code = "Sic Code $id";
            $organization->primary_contact_id = $this->strict_individual_array[mt_rand(0,$this->num_strict_individual)];
            $this->_insert($organization);
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

        foreach ($this->household_individual_array as $household_id => $household_member) {
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
            $index = array_search($location_id, $this->location_array);

            echo "index = $index\n";

            if ($index < $this->num_strict_individual) {

                echo "in individual\n";

                // this belongs to the individual
                $location->location_type_id = self::HOME;
                $location->contact_id = $this->strict_individual_array[$index];

            } else if ($index < ($this->num_strict_individual+$this->num_household)) {

                echo "in household\n";

                // belongs to household 
                $location->location_type_id = self::MAIN;

                $index1 = $index - $this->num_strict_individual;
                $contact_id = $this->household_array[$index1];

                echo "index1 = $index1    contact_id = $contact_id \n";

                $location->contact_id = $this->household_array[$index-$this->num_strict_individual];

            } else {

                echo "in organization\n";

                // belongs to organization
                $location->location_type_id = self::MAIN;

                $index1 = $index - $this->num_strict_individual - $this->num_household;
                $contact_id = $this->organization_array[$index1];

                echo "index1 = $index1    contact_id = $contact_id \n";

                $location->contact_id = $this->organization_array[$index-$this->num_strict_individual-$this->num_household];
            }

            $this->_insert($location);

            // add addresses now currently we are adding only 1 address for each location
            $address->location_id = $location_id;
            $address->street_address = "Street Address $location_id";
            $address->street_name = "Street Name $location_id";
            $address->supplemental_address_1 = "Supplemental Address 1 $location_id";
            $address->supplemental_address_2 = "Supplemental Address 2 $location_id";
            $address->supplemental_address_3 = "Supplemental Address 3 $location_id";
            $address->city = "City $location_id";
            $address->county_id = 1;
            $address->state_province_id = 1004;
            $address->postal_code = "94401";
            $address->country_id = 1228;
            $address->geo_coord_id = 1;

            $this->_insert($address);

            // add 3 email for each location
            for ($email_id=1; $email_id<=3; $email_id++) {
                $email->location_id = $location_id;
                $email->email = "location_$location_id_$email_id@crm.com";
                $this->_insert($email);
            }
            // add 3 phones for each location
            for ($phone_id=1; $phone_id<=3; $phone_id++) {
                $phone->location_id = $location_id;
                $phone->phone = "phone $location_id $phone_id";
                $phone->phone_type = $this->phone_type_array[mt_rand(1, count($this->phone_type_array))];
                $this->_insert($phone);
            }            
        }


        $this->lel();
    }




    public function printID() {

        $this->lee();

        echo("\n*******************************************************\ndomain_array\n");
        print_r($this->domain_array);
        echo("\n");

        echo("\n*******************************************************\ncontact_array\n");
        print_r($this->contact_array);
        echo("\n");

        echo("\n*******************************************************\nindividual_array\n");
        print_r($this->individual_array);
        echo("\n");

        echo("\n*******************************************************\nhousehold_array\n");
        print_r($this->household_array);
        echo("\n");

        echo("\n*******************************************************\norganization_array\n");
        print_r($this->organization_array);
        echo("\n");

        echo("\n*******************************************************\nstrict_individual_array\n");
        print_r($this->strict_individual_array);
        echo("\n");

        echo("\n*******************************************************\nhousehold_individual_array\n");
        print_r($this->household_individual_array);
        echo("\n");

        echo("\n*******************************************************\nlocation_array\n");
        print_r($this->location_array);
        echo("\n");

        echo("\n*******************************************************\nstrict_individual_location_array\n");
        print_r($this->strict_individual_location_array);
        echo("\n");

        echo("\n*******************************************************\nhousehold_location_array\n");
        print_r($this->household_location_array);
        echo("\n");

        echo("\n*******************************************************\norganization_location_array\n");
        print_r($this->organization_location_array);
        echo("\n");

        $this->lel();

    } // end of method printID



} // end of class CRM_GenerateContactData


echo("Starting on " . date("F dS h:i:s A") . "\n");

$obj1 = new CRM_GCD();

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