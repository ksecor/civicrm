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
 * Here are the list of referenced FKEYS.
 *
 * Table                             Field
 * contact_domain                    id
 * contact                           uuid
 * contact_context                   id
 * contact_address                   uuid
 * contact_relationship_types        id
 * contact_phone_mobile_providers    id
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
 * Contact Addresses = 15% for Households, 10% for Organizations, (75-(15*4))% for Individuals.
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


require_once 'config.inc.php';
require_once 'CRM/Config.php';
require_once 'CRM/DAO/Domain.php';

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
    
    const ADD_TO_DB=TRUE;
    // const ADD_TO_DB=FALSE;
    const DEBUG_LEVEL=1;
    
    /*********************************
     * private members
     *********************************/
    
    // enum's from database
    private $preferred_communication_array = array(1=>'Phone', 'Email', 'Post');
    private $greeting_type_array = array(1=>'Formal', 'Informal', 'Honorific', 'Custom', 'Other');
    private $contact_type_array = array(1=>'individual', 'household', 'organization');
    private $gender_array = array(1=>'Female', 'Male', 'Transgender');    
    private $phone_type_array = array(1=>'Phone', 'Mobile', 'Fax', 'Pager');    

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
    
    // stores address id's
    private $address_array = array();
    private $strict_individual_address_array = array();
    private $household_address_array = array();
    private $organization_address_array = array();
    
    // private vars
    private $num_individual = 0;
    private $num_household = 0;
    private $num_organization = 0;
    private $num_strict_individual = 0;

    private $num_address = 0;
    private $num_strict_individual_address = 0;
    private $num_household_address = 0;
    private $num_organization_address = 0;




  /*********************************
   * private methods
   *********************************/

  // log entry for "entering" a function
  private function lee() {
    $array1 = debug_backtrace();
    $string1 = "\n\nentering " . $array1[1]['class'] . "::" . $array1[1]['function'] . "()\n";
    echo($string1);
  }

    // log entry for "leaving" a function
    private function lel() {
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
    private function getRandomString($size=32) {
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
        return mt_srand(0,1);

    } // end of getRandomBoolean



    /*******************************************************
     *
     * Start of public functions
     *
     *******************************************************/
    // constructor
    function __construct() {
        $this->lee();
        // seed the random to get sequence of users.
        mt_srand(1);

        // initialize all the vars
        $this->num_individual = self::INDIVIDUAL_PERCENT * self::NUM_CONTACT / 100;
        $this->num_household = self::HOUSEHOLD_PERCENT * self::NUM_CONTACT / 100;
        $this->num_organization = self::ORGANIZATION_PERCENT * self::NUM_CONTACT / 100;
        $this->num_strict_individual = $this->num_individual - ($this->num_household * self::NUM_INDIVIDUAL_PER_HOUSEHOLD);

        $this->num_strict_individual_address = $this->num_strict_individual;
        $this->num_household_address = $this->num_household;
        $this->num_organization_address = $this->num_organization;

        $this->num_address = $this->num_strict_individual_address + $this->num_household_address + $this->num_organization_address;


        $this->debug_var("this", $this);
        $this->lel();
    }




    public function initDB()
    {
        $config = CRM_Config::singleton();
    }


    /*******************************************************
     *
     * this function creates arrays for the following
     *
     * domain uuid
     * contact uuid
     * contact_address uuid
     * contact_contact_address uuid
     * contact_email uuid
     * contact_phone_uuid
     * contact_instant_message uuid
     * contact_relationship uuid
     * contact_task uuid
     * contact_note uuid
     *
     *******************************************************/
    public function initID() {

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
        $this->individual_array = array_slice($this->contact_array, $offset, $this->num_individual, true);
        $offset += $this->num_individual;
        $this->household_array = array_slice($this->contact_array, $offset, $this->num_household, true);
        $offset += $this->num_household;
        $this->organization_array = array_slice($this->contact_array, $offset, $this->num_organization, true);

        // get the strict individual contacts (i.e individual contacts not belonging to any household)
        $this->strict_individual_array = array_slice($this->individual_array, 0, $this->num_strict_individual, true);

        // get the household to individual mapping array
        $this->household_individual_array = array_diff($this->individual_array, $this->strict_individual_array);
        $this->household_individual_array = array_chunk($this->household_individual_array, self::NUM_INDIVIDUAL_PER_HOUSEHOLD);
        $this->household_individual_array = array_combine($this->household_array, $this->household_individual_array);


        // contact address generation
        $this->address_array = range(1, $this->num_address);
        shuffle($this->address_array);

        $offset = 0;
        $this->strict_individual_address_array = array_slice($this->address_array, $offset, $this->num_strict_individual_address, true);
        $offset += $this->num_strict_individual_address;
        $this->household_address_array = array_slice($this->address_array, $offset, $this->num_household_address, true);
        $offset += $this->num_household_address;
        $this->organization_address_array = array_slice($this->address_array, $offset, $this->num_organization_address, true);

        $this->lel();

    } // end of method initID



    /*******************************************************
     *
     * addContactDomain()
     *
     * This method adds NUM_DOMAIN domains and then adds NUM_REVISION
     * revisions for each domain with the latest revision being the last one..
     *
     *******************************************************/
    public function addContactDomain()
    {

        $this->lee();

        foreach ($this->domain_array as $id) {
            if ($id == 1) continue;

            $domain = new CRM_Contact_DAO_Domain();

            // domain name is pretty simple. it is "Domain $id"
            $domain->id = $id;
            $domain->name = "Domain $id";
            $domain->description = "Description $id";
            
            // insert domain
            if (self::ADD_TO_DB) {
                $result = $domain->insert();
                if (DB::isError($result)) {
                    die($result->getMessage());
                }
            }
        } // end of domain id loop
        $this->lel();
    } // end of method addContactDomain

    /*******************************************************
     *
     * addContactContact()
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
    public function addContactContact() {
        $this->lee();

        // add contacts
        foreach($this->contact_type_array as $type) {
            
            // ensure that 1st character is uppercased (to match enum in database...)
            $contact_type = ucfirst($type);
            
            foreach($this->{"{$type}_array"} as $id) {
                $domain_id = mt_rand(1, self::NUM_DOMAIN);
                
                // brain dead generation :(
                $legal_id = "Legal $id"; 
                $external_id = "External $id";
                $sort_name = "Sort Name $id";
                $home_URL = "http://www.$id.com/";
                $home_URL = "http://www.$id.com/logo.png";
                $source = "Source $id";
                
                $do_not_phone = $this->getRandomBoolean();
                $do_not_email = $this->getRandomBoolean();
                $do_not_post =  $this->getRandomBoolean();
                $hash = crc32($sort_name);
                
                // choose randomly from phone, email and snail mail
                $pcm = $this->preferred_communication_array[mt_rand(1, count($this->preferred_communication_array))];

$query_string = <<<QS
INSERT INTO contact(id, domain_id, contact_type, legal_id, external_id, sort_name, home_URL, image_URL, source, preferred_communication_method, do_not_phone, do_not_email, do_not_mail, hash) 
values ($id, $domain_id, '$contact_type', '$legal_id',        '$sort_name', '$source', '$pcm', $created_by)
QS;

                (self::DEBUG_LEVEL) and print("\n$query_string\n");
                (self::ADD_TO_DB) and ($result = mysql_query($query_string) or die("Query failed: $query_string " . mysql_error()));
            } // end of loop - domain
        } // end of loop - contact_type

        $this->lel();

    } // end of method addContactContact




    /*******************************************************
     *
     * addContactIndividual()
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
    public function addContactIndividual() {

        $this->lee();
        foreach($this->individual_array as $contact_id) {
            $first_name = "First Name $contact_id";
            $middle_name = "Middle Name $contact_id";
            $last_name = "Last Name $contact_id";
            $job_title = "Job Title $contact_id";
            
            // random greeting type
            $greeting_type = $this->greeting_type_array[mt_rand(1, count($this->greeting_type_array))];
            
            $custom_greeting = "custom greeting $contact_id";
            
$query_string = <<<QS
INSERT INTO crm_individual(id, contact_id, first_name, middle_name, last_name,
prefix, suffix, display_name, greeting_type, custom_greeting,
job_title, gender, birth_date, is_deceased, ) 
values ($contact_uuid, $contact_rid, '$first_name', '$middle_name', '$last_name', '$job_title', '$greeting_type', '$custom_greeting')
QS;
            
            (self::DEBUG_LEVEL) and print("\n$query_string\n");
            (self::ADD_TO_DB) and ($result = mysql_query($query_string) or die('Query failed: $query_string ' . mysql_error()));
            
        } // end of loop - individual_array
        
        $this->lel();
        
    } // end of method addContactIndividual




    /*******************************************************
     *
     * addContactHousehold()
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
    public function addContactHousehold() {

        $this->lee();
        $contact_rid = self::NUM_REVISION_PER_CONTACT;

        foreach($this->household_array as $contact_uuid) {
            $primary_contact_uuid = $this->household_individual_array[$contact_uuid][0];
            $household_name = "household $contact_uuid - primary contact $primary_contact_uuid";
            $nick_name = "nick $contact_uuid";

$query_string = <<<QS
INSERT INTO contact_household(contact_uuid, contact_rid, household_name, nick_name, primary_contact_uuid) 
values ($contact_uuid, $contact_rid, '$household_name', '$nick_name', $primary_contact_uuid)
QS;

            (self::DEBUG_LEVEL) and print("\n$query_string\n");
            (self::ADD_TO_DB) and ($result = mysql_query($query_string) or die('Query failed: $query_string ' . mysql_error()));

        } // end of loop - household_array

        $this->lel();

    } // end of method addContactHousehold




    /*******************************************************
     *
     * addContactOrganization()
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
    public function addContactOrganization() {

        $this->lee();
        $contact_rid = self::NUM_REVISION_PER_CONTACT;

        foreach($this->organization_array as $contact_uuid) {
            $primary_contact_uuid = $this->individual_array[mt_rand(1,$this->num_individual)-1];
            $organization_name = "organization $contact_uuid primary contact $primary_contact_uuid";
            $legal_name = "legal $contact_uuid";
            $nick_name = "nick $contact_uuid";
            $sic_code = "sic $contact_uuid";

$query_string = <<<QS
INSERT INTO contact_organization(contact_uuid, contact_rid, organization_name, legal_name, nick_name, sic_code, primary_contact_uuid) 
values ($contact_uuid, $contact_rid, '$organization_name', '$legal_name', '$nick_name', '$sic_code', $primary_contact_uuid)
QS;

            (self::DEBUG_LEVEL) and print("\n$query_string\n");
            (self::ADD_TO_DB) and ($result = mysql_query($query_string) or die('Query failed: $query_string ' . mysql_error()));

        } // end of loop - organization_array

        $this->lel();

    } // end of method addContactOrganization



    /*******************************************************
     *
     * addContactRelationshipTypes()
     *
     * This method adds data to the contact_relationship_types table
     *
     * it adds the following fields
     *
     * domain_uuid - random generation
     * name - 'relationship $domain_uuid'
     * description - 'description $domain_uuid'
     * direction - random
     * contact_type - random
     * created_by - superuser
     *
     *******************************************************/
    public function addContactRelationshipTypes() {

        $this->lee();

        for($i=0; $i<self::NUM_RELATIONSHIP_TYPE; $i++) {

            // domain_uuid
            $domain_uuid = mt_rand(1, self::NUM_DOMAIN);

            // name
            $name = "relationship $domain_uuid";

            // description
            $description = "descrption $domain_uuid";

            // direction
            $direction = $this->relationship_direction_array[mt_rand(1, count($this->relationship_direction_array))];

            // contact_type
            $contact_type = $this->contact_type_array[mt_rand(1, count($this->contact_type_array))];

            // created by superuser
            $created_by=0;

$query_string = <<<QS
INSERT INTO contact_relationship_types(domain_uuid, name, description, direction, contact_type, created_by) 
values ($domain_uuid, '$name', '$description', '$direction', '$contact_type', $created_by)
QS;

            (self::DEBUG_LEVEL) and print("\n$query_string\n");
            (self::ADD_TO_DB) and ($result = mysql_query($query_string) or die('Query failed: $query_string ' . mysql_error()));

        } // end of for loop

        $this->lel();

    } // end of method addContactRelationshipTypes



    /*******************************************************
     *
     * addContactRelationship()
     *
     * This method adds data to the contact_relationship table
     *
     * it adds the following fields
     *
     * uuid - linear
     * rid - 1
     * latest_rev - 1
     * contact_uuid
     * target_contact_uuid
     * relationship_type_id
     * created_by - 0
     *
     *******************************************************/
    public function addContactRelationship() {

        $this->lee();
        $uuid = 1;
        $created_by = 0;
        $rid = 1;
        $latest_rev = 1;

        // get the relationship type id from db
$query_string = <<<QS
SELECT id FROM contact_relationship_types WHERE name='{$this->relationship_type_array[4]}'
QS;
        (self::DEBUG_LEVEL) and print("\n$query_string\n");
        //    (self::ADD_TO_DB) and ($result = mysql_query($query_string) or die('Query failed: $query_string ' . mysql_error()));
        $result = mysql_query($query_string) or die('Query failed: $query_string ' . mysql_error());

        mysql_num_rows($result) or die("Relationship ID does not exist for {$this->relationship_type_array[4]}");

        $row = mysql_fetch_assoc($result);
        $relationship_type_id = $row['id'];

        // foreach household, add the relationship of 'is household member of' for all member
        foreach($this->household_individual_array as $target_contact_uuid => $individual_uuid_array) {
            foreach($individual_uuid_array as $contact_uuid) {

$query_string = <<<QS
INSERT INTO contact_relationship(uuid, rid, latest_rev, contact_uuid, target_contact_uuid, relationship_type_id, created_by) 
VALUES($uuid, $rid, $latest_rev, $contact_uuid, $target_contact_uuid, $relationship_type_id, $created_by)
QS;
                (self::DEBUG_LEVEL) and print("\n$query_string\n");
                (self::ADD_TO_DB) and ($result = mysql_query($query_string) or die('Query failed: $query_string ' . mysql_error()));
                $uuid++;
            } // end of household member loop
        } // end of the household loop

        $this->lel();
    } // end of method addContactRelationship




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

        echo("\n*******************************************************\naddress_array\n");
        print_r($this->address_array);
        echo("\n");

        echo("\n*******************************************************\nstrict_individual_address_array\n");
        print_r($this->strict_individual_address_array);
        echo("\n");

        echo("\n*******************************************************\nhousehold_address_array\n");
        print_r($this->household_address_array);
        echo("\n");

        echo("\n*******************************************************\norganization_address_array\n");
        print_r($this->organization_address_array);
        echo("\n");

        $this->lel();

    } // end of method printID



} // end of class CRM_GenerateContactData


echo("Starting on " . date("F dS h:i:s A") . "\n");

$obj1 = new CRM_GCD();

$obj1->initID();
$obj1->initDB();
//$obj1->printID();
$obj1->addContactDomain();
// $obj1->addContactContact();
// $obj1->addContactIndividual();
// $obj1->addContactHousehold();
// $obj1->addContactOrganization();
// $obj1->addContactRelationshipTypes();
// $obj1->addContactRelationship();


echo("Ending on " . date("F dS h:i:s A") . "\n");

?>
