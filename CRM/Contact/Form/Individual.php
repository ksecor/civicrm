<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Form.php';
require_once 'CRM/SelectValues.php';
require_once 'CRM/ShowHideBlocks.php';

require_once 'CRM/Contact/Form/Contact.php';

require_once 'CRM/Contact/Form/Location.php';

/**
 * This class is used for building the Addcontact form. This class also has the actions that should be done when form is processed.
 * 
 * This class extends the variables and methods provided by the class CRM_Form which by default extends the HTML_QuickForm_SinglePage.
 * @package CRM.  
 */
class CRM_Contact_Form_Individual extends CRM_Form 
{
    /**
     * This is the constructor of the class.
     *
     * @access public
     * @param string $name Contains name of the form.
     * @param string $state Used to access the current state of the form.
     * @param constant $mode Used to access the type of the form. Default value is MODE_NONE.
     * @return None
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);
    }
    
    /**
     * In this function we build the Individual.php. All the quickform components are defined in this function.
     * 
     * This function implements a switch strategy using the form public variable $mode to identify the type of mode rendered by the 
     * form. The types of mode could be either Contact CREATE, VIEW, UPDATE, DELETE or SEARCH. 
     * CREATE and SEARCH forms can be implemented in a mini format using mode MODE_CREARE_MINI and MODE_SEARCH_MINI. 
     * This function adds a default text element to the form whose value indicates the mode rendered by the form. It further calls the 
     * corresponding function build<i>mode</i>Form() to provide dynamic HTML content and is passed to the renderer in an  array format.
     * 
     * @access public
     * @return None
     * @see _buildAddForm( ) 
     */
    function buildQuickForm( )
    {
        $this->_buildAddForm();

        if ($this->_mode == self::MODE_VIEW) {
            $this->freeze();
        }

    }//ENDING BUILD FORM 

    
    /**
     * This function sets the default values to the specified form element.
     * 
     * The function uses the $default array to load default values for element names provided as keys. It further calls the setDefaults 
     * method of the HTML_QuickForm by passing to it the array. 
     * This function differentiates between different mode types of the form by implementing the switch strategy based on the form mode 
     * variable.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array();

        if ( $this->_mode & ( self::MODE_VIEW | self::MODE_UPDATE ) ) {
            // get the id from the session that has to be modified
            // get the values for $_SESSION['id']
            $lng_contact_id = $_SESSION['id'];

            // get values from contact table
            $contact = new CRM_Contact_DAO_Contact();
        
            $contact->get("id",$lng_contact_id);

            $defaults['preferred_communication_method'] = $contact->preferred_communication_method;
            $defaults['privacy[do_not_phone]'] = $contact->do_not_phone;
            $defaults['privacy[do_not_email]'] = $contact->do_not_email;
            $defaults['privacy[do_not_mail]'] = $contact->do_not_mail;
            
            // get the values from individual table
            $contact_individual = new CRM_Contact_DAO_Individual;

            $contact_individual->get("contact_id",$lng_contact_id);

            $defaults['first_name'] = $contact_individual->first_name;
            $defaults['last_name'] = $contact_individual->last_name;
            $defaults['prefix'] =  $contact_individual->prefix; 
            $defaults['suffix'] = $contact_individual->suffix;
            $defaults['greeting_type'] = $contact_individual->greeting_type;
            $defaults['job_title'] = $contact_individual->job_title;
            $defaults['gender[gender]'] = $contact_individual->gender;

            $defaults['birth_date'] = $contact_individual->birth_date;

            $defaults['is_deceased'] = $contact_individual->is_deceased;

            // create DAO object of location
            $contact_location = new CRM_Contact_DAO_Location();

            $contact_location->contact_id = $lng_contact_id;
            $contact_location->find();

            while ($contact_location->fetch()) {
                // we are are building $a_Location array, which has deatails for each location

                // values from location table 
               $a_Location[$contact_location->id]['location_type_id'] = $contact_location->location_type_id;
               $a_Location[$contact_location->id]['is_primary'] = $contact_location->is_primary;

               //get values from address table
               $varaddress = "contact_address".$lng_key;
               $$varaddress = new CRM_Contact_DAO_Address();
               
               $$varaddress->location_id = $contact_location->id;
               $$varaddress->find();
               
               while ($$varaddress->fetch()) {
                   $a_Location[$contact_location->id]['street_address'] = $$varaddress->street_address;
                   $a_Location[$contact_location->id]['supplemental_address_1'] = $$varaddress->supplemental_address_1;
                   $a_Location[$contact_location->id]['city'] = $$varaddress->city;
                   $a_Location[$contact_location->id]['state_province_id'] = $$varaddress->state_province_id;
                   $a_Location[$contact_location->id]['postal_code'] = $$varaddress->postal_code;
                   $a_Location[$contact_location->id]['country_id'] = $$varaddress->country_id;
               }
               
               // get data from email table
               $var_email = "contact_email".$lng_key;
               $$var_email = new CRM_Contact_DAO_Email();

               $$var_email->location_id = $contact_location->id;
               $$var_email->find();

               $lng_email = 1;
               while ($$var_email->fetch()) {
                   $a_Location[$contact_location->id]['email_'.$lng_email] = $$var_email->email;
                   $lng_email++;
               }
               
               // get data from phone table
               $var_phone = "contact_phone".$lng_key;
               $$var_phone = new CRM_Contact_DAO_Phone();
                      
               $$var_phone->location_id = $contact_location->id;
               $$var_phone->find();

               $lng_phone = 1;
               while ($$var_phone->fetch()) {
                   $a_Location[$contact_location->id]['phone_'.$lng_phone] = $$var_phone->phone;
                   $a_Location[$contact_location->id]['phone_type_'.$lng_phone] = $$var_phone->phone_type;
                   $lng_phone++;
               }

               // get data from im table
               $var_im = "contact_im" . $lng_i;
               $$var_im = new CRM_Contact_DAO_IM();
               
               $$var_im->location_id = $contact_location->id;
               $$var_im->find();

               $lng_im = 1;
               while ($$var_im->fetch()) {
                   $a_Location[$contact_location->id]['im_service_id_'.$lng_im] = $$var_im->im_provider_id;
                   $a_Location[$contact_location->id]['im_screenname_'.$lng_im] = $$var_im->im_screenname;
                   $lng_im++;
               }

            }// end of outer while loop

             //print_r($a_Location);                        
            if (is_array($a_Location)) { 
                $lng_count = 1;
                foreach ($a_Location as $lng_key => $var_value) {
                    $defaults['location'.$lng_count.'[location_type_id]'] = $var_value['location_type_id'];
                    $defaults['location'.$lng_count.'[is_primary]'] = $var_value['is_primary'];
                    $defaults['location'.$lng_count.'[street_address]'] = $var_value['street_address'];
                    $defaults['location'.$lng_count.'[supplemental_address_1]'] = $var_value['supplemental_address_1'];
                    $defaults['location'.$lng_count.'[city]'] = $var_value['city'];
                    $defaults['location'.$lng_count.'[state_province_id]'] = $var_value['state_province_id'];
                    $defaults['location'.$lng_count.'[postal_code]'] = $var_value['postal_code'];
                    $defaults['location'.$lng_count.'[country_id]'] = $var_value['country_id'];
                    $defaults['location'.$lng_count.'[email_1]'] = $var_value['email_1'];
                    $defaults['location'.$lng_count.'[email_2]'] = $var_value['email_2'];
                    $defaults['location'.$lng_count.'[email_3]'] = $var_value['email_3'];
                    $defaults['location'.$lng_count.'[phone_1]'] = $var_value['phone_1'];
                    $defaults['location'.$lng_count.'[phone_type_1]'] = $var_value['phone_type_1'];
                    $defaults['location'.$lng_count.'[phone_2]'] = $var_value['phone_2'];
                    $defaults['location'.$lng_count.'[phone_type_2]'] = $var_value['phone_type_2'];
                    $defaults['location'.$lng_count.'[phone_3]'] = $var_value['phone_3'];
                    $defaults['location'.$lng_count.'[phone_type_3]'] = $var_value['phone_type_3'];
                    $defaults['location'.$lng_count.'[im_service_id_1]'] = $var_value['im_service_id_1'];
                    $defaults['location'.$lng_count.'[im_screenname_1]'] = $var_value['im_screenname_1'];
                    $defaults['location'.$lng_count.'[im_service_id_2]'] = $var_value['im_service_id_2'];
                    $defaults['location'.$lng_count.'[im_screenname_2]'] = $var_value['im_screenname_2'];
                    $defaults['location'.$lng_count.'[im_service_id_3]'] = $var_value['im_service_id_3'];
                    $defaults['location'.$lng_count.'[im_screenname_3]'] = $var_value['im_screenname_3'];
                    $lng_count++ ;
                }
            }
        }

        // set all elements with values from the database.
        return $defaults;
    }
    
    /**
     * This function is used to validate the date.
     * 
     * This is a custom validation function used to implement server side validation of the date entered by the user.
     * It accepts an array $value whose keys 'M', 'd' & 'Y' contain the month day and the year information. It then calls the checkdate
     * php function to verify the date entered.
     * 
     * @access public
     * @param array $value This is a zero-based, one-dimensional, 3-rows array containing the date information. 
     * @internal A typical date array pattern is : $value = array( 'M' => '6, 'd' => '20', 'Y' => '1990').
     * @return Boolean value true or false depending on whether the date enterred is valid or invalid.
     * @see addRules( )     
     */
    function valid_date($value) 
    {

        if (checkdate($value['M'], $value['d'], $value['Y'])) {
            return true;
        } else {
            return false;
        }
    }
    

    /**
     * This function is used to add the rules for form.
     * 
     * This function is used to add filters using applyFilter(), which filters the element value on being submitted. 
     * Rules of validation for form elements are established using addRule() or addGroupRule() QuickForm methods. Validation can either
     * be at the client by default javascript added by QuickForm, or at the server.  
     * Any custom rule of validation is set here using the registerRule() method. In this file, the custom validation function for 
     * birth date is set by registering the rule check_date which calls the valid_date function for date validation.
     * This function differentiates between different mode types of the form by implementing the switch functionality based on the
     * value of the class variable $mode.  
     * 
     * @return None
     * @access public
     * @see valid_date 
     */
    function addRules( ) 
    {

        
        $this->applyFilter('_ALL_', 'trim');
        
        // rules for searching..
        
        // rules for quick add
        
        switch ($this->_mode) {
        case self::MODE_ADD:
            $this->registerRule('check_date', 'callback', 'valid_date','CRM_Contact_Form_Individual');
            $this->registerRule('check_date', 'callback', CRM_RULE::date(),'CRM_Contact_Form_Individual');

            // $this->addRule('birth_date', t(' Select a valid date.'), 'check_date');
            
            for ($i = 1; $i <= 3; $i++) { 
                $this->addGroupRule('location'."{$i}", array('email_1' => array( 
                                                                                array(t( 'Please enter valid email for location').$i.'.', 'email', null)),                                                 'email_2' => array( 
                                                                                                                                                                                                                                        array(t( ' Please enter valid secondary email for location').$i.'.', 'email', null)),
                                                             'email_3' => array( 
                                                                                array(t( ' Please enter valid tertiary email for location' ).$i.'.', 'email', null))
                                                             )
                                    ); 
            }
            
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            break;            
        }    
        
    }

    function preProcess( ) {
    }

    /**
     * This function is used to call appropriate process function when a form is submitted.
     * 
     * The function implements a switch functionality to differentiate between different mode types of the form. It is based on the 
     * value of the $mode form class variable. 
     * @internal Possible mode options being MODE_ADD, MODE_VIEW, MODE_UPDATE, MODE_DELETE
     * The process works as follows:
     * <code>
     * great example of BAD code, please use classes and virtual function if u want to do something like this
     * integrate forms only if a large part of the functionality is the same
     * switch ($this->_mode) {
     *        case self::MODE_ADD:
     *             $this->_Add_postProcess();
     *             break;
     *        case self::MODE_VIEW:
     *             $this->_view_postProcess();
     *             break; 
     *        case ..
     *        ..
     *        ..
     * }         
     * </code>
     * 
     * @access public
     * @return None
     */
    function postProcess( ) 
    {
        switch ($this->_mode) {
        case self::MODE_ADD:
            $this->_addPostProcess();
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            $this->_addPostProcess();
            break;
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            break;            
        }    
    }
    
    /**
     * This function provides the HTML form elements for the add operation of individual contact form.
     * 
     * This function is called by the buildQuickForm method, when the value of the $mode class variable is set to MODE_ADD
     * The addElement and addGroup method of HTML_QuickForm is used to add HTML elements to the form which is referenced using the $this 
     * form handle. Also the default values for the form elements are set in this function.
     * 
     * @access private
     * @return None 
     * @uses CRM_SelectValues Used to obtain static array content for setting select values for select element.
     * @uses CRM_Contact_Form_Location::buildLocationBlock($this, 3) Used to obtain the HTML element for pulgging the Location block. 
     * @uses CRM_Contact_Form_Contact::buildCommunicationBlock($this) Used to obtain elements for plugging the Communication preferences.
     * @see buildQuickForm()         
     * 
     */
    private function _buildAddForm( ) 
    {
        
        $form_name = $this->getName();

        
        // prefix
        $this->addElement('select', 'prefix', null, CRM_SelectValues::$prefixName);
        
        // first_name
        $this->addElement('text', 'first_name', 'First / Last :', array('maxlength' => 64));
        
        // last_name
        $this->addElement('text', 'last_name', null, array('maxlength' => 64));
        
        // suffix
        $this->addElement('select', 'suffix', null, CRM_SelectValues::$suffixName);
        
        // greeting type
        $this->addElement('select', 'greeting_type', 'Greeting type :', CRM_SelectValues::$greeting);
        
        // job title
        $this->addElement('text', 'job_title', 'Job title :', array('maxlength' => 64));
        
        // add the communications block
        CRM_Contact_Form_Contact::buildCommunicationBlock($this);

        // radio button for gender
        $genderOptions = array( );
        $genderOptions[] = HTML_QuickForm::createElement('radio', 'gender', 'Gender', 'Female', 'Female');
        $genderOptions[] = HTML_QuickForm::createElement('radio', 'gender', 'Gender', 'Male', 'Male');
        $genderOptions[] = HTML_QuickForm::createElement('radio', 'gender', 'Gender', 'Transgender','Transgender');
        $this->addGroup( $genderOptions, 'gender', 'Gender' );
        
        $this->addElement('checkbox', 'is_deceased', null, 'Contact is deceased');
        
        $this->addElement('date', 'birth_date', 'Date of birth', CRM_SelectValues::$date);

        /* Entering the compact location engine */ 
        $showHideBlocks = new CRM_ShowHideBlocks( array('name'              => 1,
                                                        'commPrefs'         => 1,),
                                                  array('notes'        => 1,
                                                        'demographics' => 1,) );
        
        $location =& CRM_Contact_Form_Location::buildLocationBlock($this, 3, $showHideBlocks);

        for ($i = 1; $i < 4; $i++) {
            $this->updateElementAttr(array($location[$i]['is_primary']), array('onchange' => "location_is_primary_onclick(\"$form_name\", {$i});"));
        }
        /* End of locations */

        $this->add('textarea', 'address_note', 'Notes:', array('cols' => '82', 'maxlength' => 255));    
        

        $showHideBlocks->links( $this, 'demographics', '[+] show demographics' , '[-] hide demographics'  );
        $showHideBlocks->links( $this, 'notes'       , '[+] show contact notes', '[-] hide contact notes' );
        $showHideBlocks->addToTemplate( );

        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'next',
                                                'name'      => 'Save',
                                                'isDefault' => true   ),
                                        array ( 'type'      => 'reset',
                                                'name'      => 'Reset'),
                                        array ( 'type'       => 'cancel',
                                                'name'      => 'Cancel' ),
                                        )
                                  );
    }

       
    /**
     * This function provides the HTML form elements for an advanced search operation of an individual.
     * 
     * This function is called by the buildQuickForm method, when the value of the $mode class variable is set to MODE_SEARCH
     * The addElement and addGroup method of HTML_QuickForm is used to add HTML elements to the form which is referenced using the $this 
     * form handle. Also the default values for the form elements are set in this function.
     * 
     * @access private
     * @return None
     * @see buildQuickForm() 
     * @see _buildAddForm()
     * 
     */  
    private function _buildSearchForm() 
    {
        $this->addElement('text', 'domain_id', 'Domain Id:', array('maxlength' => 10));
        $this->addElement('text', 'sort_name', 'Name:  ', array('maxlength' => 64));
        $this->addElement('select', 'contact_type', 'Contact type:', CRM_SelectValues::$contactType);
        $this->addElement('select', 'preferred_communication_method', 'Prefers:', CRM_SelectValues::$pcm);
        
        $this->addDefaultButtons(array (
                                        array (
                                               'type'       => 'next', 
                                               'name'       => 'Search',
                                               'isDefault'  =>  true )
                                        )
                                 );
    }
         
    /**
     * This function does all the processing of the form for New Contact Individual.
     * Depending upon the mode this function is used to insert or update the Individual
     * @access private
     */
    private function _addPostProcess() 
    { 
        $lng_contact_id = 0; // variable for crm_contact 'id'
        $str_error = ""; // error is recorded  if there are any errors while inserting in database
        // print_r($_POST);        
        // action is taken depending upon the mode
        switch ($this->_mode) {
        case self::MODE_UPDATE:
            $lng_contact_id = $_SESSION['id'];
            break;
        case self::MODE_VIEW:
            break;
        }    
        
        // create a object for inserting data in contact table 
        $contact = new CRM_Contact_DAO_Contact();
        
        $contact->domain_id = 1;
        $contact->contact_type = 'Individual';
        // $contact->legal_id = '';
        //$contact->external_id = '';
        $contact->sort_name = $this->exportValue('first_name')." ".$this->exportValue('last_name');
        //$contact->home_URL = '';
        //$contact->image_URL = '';
        //$contact->source = $this->exportValue('source');
        $contact->preferred_communication_method = $this->exportValue('preferred_communication_method');
        $a_privacy = $this->exportValue('privacy');
        $contact->do_not_phone = (strlen($a_privacy['do_not_phone'])) ? $a_privacy['do_not_phone'] : 0 ;
        $contact->do_not_email = (strlen($a_privacy['do_not_email'])) ? $a_privacy['do_not_email'] : 0 ;
        $contact->do_not_mail = (strlen($a_privacy['do_not_mail'])) ? $a_privacy['do_not_mail'] : 0 ;
        //$contact->hash = $this->exportValue('hash');
        
        $contact->query('BEGIN'); //begin the database transaction
       
        if ($lng_contact_id) {
            // update the contact $lng_contact_id
            $contact->id = $lng_contact_id;
            if(!$contact->update()) $str_error = mysql_error();
        } else {
            // insert new contact
           if (!$contact->insert())  $str_error = mysql_error();
        }
        
        if (!strlen($str_error)) { //proceed if there are no errors
            // create a object for contact individual table 
            $contact_individual = new CRM_Contact_DAO_Individual();
            $contact_individual->contact_id = $contact->id;
            $contact_individual->first_name = $this->exportValue('first_name');
            //$contact_individual->middle_name = $this->exportValue('middle_name');
            $contact_individual->last_name = $this->exportValue('last_name');
            $contact_individual->prefix = $this->exportValue('prefix');
            $contact_individual->suffix = $this->exportValue('suffix');
            //$contact_individual->display_name = '';
            $contact_individual->greeting_type = $this->exportValue('greeting_type');
            $contact_individual->custom_greeting = $this->exportValue('custom_greeting');
            $contact_individual->job_title = $this->exportValue('job_title');
            $a_gender = $this->exportValue('gender');
            $contact_individual->gender = $a_gender['gender'];
            
            $a_date = $this->exportValue('birth_date');
            
            if ($a_date['d'] < 10) {
                $day = "0".$a_date['d'];
            } else {
                $day = $a_date['d'];
            }
                     
            if ($a_date['M'] < 10) {
                $mnt = "0".$a_date['M'];
            } else {
                $mnt = $a_date['M'];
            }
                     
            $contact_individual->birth_date = $a_date['Y'].$mnt.$day;
            $contact_individual->is_deceased = $this->exportValue('is_deceased');
            //$contact_individual->phone_to_household_id = '';
            //$contact_individual->email_to_household_id = '';
            //$contact_individual->mail_to_household_id = '';
 
            if ($lng_contact_id) {
                // update the crm_individual for $lng_contact_id
                $contact_individual->whereAdd('contact_id = '.$lng_contact_id);
                if(!$contact_individual->update(DB_DATAOBJECT_WHEREADD_ONLY)) $str_error = mysql_error();
            } else {
                // insert in crm_individual
                if (!$contact_individual->insert()) $str_error = mysql_error();                
            }
        }
                 
        if (!strlen($str_error)) { //proceed if there are no errors  
            if ($lng_contact_id) {
                // get the existing locations for $lng_contact_id
                $contact_location = new CRM_Contact_DAO_Location();
                $contact_location->get();
                $contact_location->contact_id = $lng_contact_id;
                $contact_location->find();
                
                $lng_location = 1;
                while ($contact_location->fetch()) {
                    // build the array with locations id as value 
                    $a_location_array[$lng_location] = $contact_location->id;
                    $lng_location++;
                }
            }
            
            for ($lngi= 1; $lngi <= 3; $lngi++) { // start of for loop for location
                //create a object of location class
                $varname = "contact_location".$lngi;
                $varname1 = "location".$lngi;
                
                // build an array with the values posted for location 
                $a_Location =  $this->exportValue($varname1);
                
                if (strlen(trim($a_Location['street_address'])) > 0  || strlen(trim($a_Location['email_1'])) > 0 || strlen(trim($a_Location['phone_1'])) > 0) {  // check for valid location entry
                    
                    if (!strlen($str_error)) { //proceed if there are no errors
                        // create a object of crm location
                        $$varname = new CRM_Contact_DAO_Location();
                        $$varname->contact_id = $contact->id;
                        $$varname->location_type_id = $a_Location['location_type_id'];
                        $$varname->is_primary = $a_Location['is_primary'];
 
                        if (strlen($a_location_array[$lngi])) {
                            // update the crm_location for $lng_contact_id
                            $$varname->whereAdd('contact_id = '.$lng_contact_id);
                            $$varname->whereAdd('id = '.$a_location_array[$lngi]);
                            if(!$$varname->update(DB_DATAOBJECT_WHEREADD_ONLY)) $str_error = mysql_error();
                            
                            // $$varname->reset();
                            //$$varname->get("contact_id",$lng_contact_id);
                            $lng_location_id = $a_location_array[$lngi];
                            $$varname->id = $a_location_array[$lngi];
                        } else {
                            // insert new record
                            if (!$$varname->insert()) {
                                $str_error = mysql_error();
                                break;
                            }
                            $lng_location_id = $$varname->id;   
                        }
                    }
                             
                    if (!strlen($str_error)) { //proceed if there are no errors
                        if (strlen(trim($a_Location['street_address'])) > 0) { // check for valid address entry
                            //create the object of crm address
                            $varaddress = "contact_address".$lngi;
                            $$varaddress = new CRM_Contact_DAO_Address();
                                     
                            $$varaddress->location_id = $$varname->id;
                            $$varaddress->street_address = $a_Location['street_address'];
                            //$$varaddress->street_number = '';
                            //$$varaddress->street_number_suffix = '';
                            //$$varaddress->street_number_predirectional = '';
                            //$$varaddress->street_name = '';
                            //$$varaddress->street_type = '';
                            //$$varaddress->street_number_postdirectional = '';
                            $$varaddress->supplemental_address_1 = $a_Location['supplemental_address_1'];
                            //$$varaddress->supplemental_address_2 = '';
                            //$$varaddress->supplemental_address_3 = '';
                            $$varaddress->city = $a_Location['city'];
                            // $$varaddress->county_id = $a_Location['county_id'];
                            $$varaddress->county_id = 1;
                            $$varaddress->state_province_id = $a_Location['state_province_id'];
                            $$varaddress->postal_code = $a_Location['postal_code'];
                            //$$varaddress->postal_code_suffix = '';
                            //$$varaddress->usps_adc = '';
                            $$varaddress->country_id = $a_Location['country_id'];
                            $$varaddress->geo_coord_id = 1;
                            $$varaddress->geo_code1 = $a_Location['geo_code1'];
                            $$varaddress->geo_code2 = $a_Location['geo_code2'];
                            $$varaddress->timezone = $a_Location['timezone'];
                            // $$varaddress->address_nite = '';

                            // if ($lng_contact_id) {
                            if (strlen($a_location_array[$lngi])) {
                                // create an object of crm_address table
                                // get the address for $lng_location_id
                                $contact_address = new CRM_Contact_DAO_Address();
                                $contact_address->get('location_id',$lng_location_id);
                                
                                // check if there are any existing address for $lng_location_id
                                if ($contact_address->id){
                                    // update the crm_address for $lng_contact_id
                                    $$varaddress->whereAdd('location_id = '.$lng_location_id);
                                    if(!$$varaddress->update(DB_DATAOBJECT_WHEREADD_ONLY))  $str_error = mysql_error();
                                } else {
                                    // insert new record
                                    if (!$$varaddress->insert()) {
                                        $str_error = mysql_error();
                                        break;
                                    }
                                }
                            } else {
                                // insert new record
                                if (!$$varaddress->insert()) {
                                    $str_error = mysql_error();
                                    break;
                                }
                            }
                        }              
                    }
                             
                    if (!strlen($str_error)) { //proceed if there are no errors
                        unset($a_email);
                        if ($lng_location_id) {
                            // get the list of all the email for $lng_location_id
                            // create object of crm_email table
                            $contact_email = new CRM_Contact_DAO_Email();
                            $contact_email->get();
                            $contact_email->location_id = $lng_location_id;
                            $contact_email->find();
                            
                            $lng_email = 1;
                            while ($contact_email->fetch()) { 
                                //build an array with email table id ad value
                                $a_email[$lng_email] = $contact_email->id;
                                $lng_email++;
                            }
                        }
                        // my_print_r($a_email);

                        for ($lng_i= 1; $lng_i <= 3; $lng_i++) { // start of for email
                            $varemail = "email_".$lng_i;
                            if (strlen(trim($a_Location[$varemail])) > 0) { // check for valid email entry
                                //create the object of crm email
                                $var_email = "contact_email".$lng_i;
                                $$var_email = new CRM_Contact_DAO_Email();
                               
                                $$var_email->location_id = $$varname->id;
                                $$var_email->email = $a_Location[$varemail];
                                         
                                if($lng_i == 1) { //make first email entered primary
                                    $$var_email->is_primary = 1;
                                } else {
                                    $$var_email->is_primary = 0;
                                }
                                     
                                //if ($lng_contact_id) {

                                if (strlen($a_email[$lng_i])) {
                                    // update the crm_email for $lng_contact_id and id ($a_email[$lng_i])
                                    //$$var_email->whereAdd('location_id = '.$lng_location_id);
                                    $$var_email->whereAdd('id = '.$a_email[$lng_i]);
                                    
                                    if(!$$var_email->update(DB_DATAOBJECT_WHEREADD_ONLY)) {
                                        if (strlen($str_error = mysql_error())) {
                                            break;
                                        }
                                    }    
                                } else {
                                    // insert new record
                                    if (!$$var_email->insert()) {
                                        $str_error = mysql_error();
                                        break;
                                    }    
                                }
                            }  
                        }// end of for for email
                    }
                             
                    if (!strlen($str_error)) { //proceed if there are no errors
                        unset($a_phone);
                        if ($lng_contact_id) {
                            // get the list of phones for $lng_location_id
                            //create object of crm_phone table
                            $contact_phone = new CRM_Contact_DAO_Phone();
                            $contact_phone->get();
                            $contact_phone->location_id = $lng_location_id;
                            $contact_phone->find();
                            
                            $lng_phone = 1;
                            while ($contact_phone->fetch()) {
                                // bulid an array with phone id as value
                                $a_phone[$lng_phone] = $contact_phone->id;
                                $lng_phone++;
                            }
                        }                          
                        // my_print_r($a_phone);

                        for ($lng_i= 1; $lng_i <= 3; $lng_i++) { // start of phone for loop
                            $varphone = "phone_".$lng_i;
                            $varphone_type = "phone_type_".$lng_i;
                            $varmobile_prov_id = "mobile_provider_id_".$lng_i;
                            if (strlen(trim($a_Location[$varphone])) > 0) { //check for valid phone entry
                                //create the object of crm phone
                                $var_phone = "contact_phone".$lng_i;
                                $$var_phone = new CRM_Contact_DAO_Phone();
                      
                                $$var_phone->location_id = $$varname->id;
                                $$var_phone->phone = $a_Location[$varphone];
                                $$var_phone->phone_type = $a_Location[$varphone_type];
                                
                                if ($lng_i == 1) { //make first phone entered primary
                                    $$var_phone->is_primary = 1;
                                } else {
                                    $$var_phone->is_primary = 0;
                                }
                                
                                // $$var_phone->mobile_provider_id = $a_Location[$varmobile_prov_id];
                                $$var_phone->mobile_provider_id = 1;
                                
                                //if ($lng_contact_id) {
                                if (strlen($a_phone[$lng_i])) {
                                    // update the crm_phone for $lng_location_id and phone id ($a_phone[$lng_i])
                                    $$var_phone->whereAdd('location_id = '.$lng_location_id);
                                    $$var_phone->whereAdd('id = '.$a_phone[$lng_i]);
                                    
                                    if(!$$var_phone->update(DB_DATAOBJECT_WHEREADD_ONLY)) {
                                        if (strlen($str_error = mysql_error())) {
                                            break;
                                        }
                                    }    
                                } else {
                                    // insert new record
                                    if (!$$var_phone->insert()) {
                                        $str_error = mysql_error();
                                        break;
                                    }    
                                }
                            }  
                        }// end of phone for loop
                    }
                             
                    if (!strlen($str_error)) { //proceed if there are no errors
                        unset($a_im);
                        if ($lng_contact_id) {
                            // get the ims for the location lng_location_id
                            //create the object of crm im
                            $contact_im = new CRM_Contact_DAO_IM();
                            $contact_im->get();
                            $contact_im->location_id = $lng_location_id;
                            $contact_im->find();
                            
                            $lng_im = 1;
                            while ($contact_im->fetch()) {
                                // build an array with im id as value
                                $a_im[$lng_im] = $contact_im->id;
                                $lng_im++;
                            }
                        }

                        // my_print_r($a_im);
                        for ($lng_i= 1; $lng_i <= 3; $lng_i++) { // start of im for loop
                            $var_service = "im_service_id_".$lng_i;
                            $var_screenname = "im_screenname_".$lng_i;
                            if (strlen(trim($a_Location[$var_screenname])) > 0) { // check for valid im entry
                                //create the object of crm im
                                $var_im = "contact_im" . $lng_i;
                                $$var_im = new CRM_Contact_DAO_IM();
                                         
                                $$var_im->location_id = $$varname->id;
                                $$var_im->im_screenname = $a_Location[$var_screenname];
                                $$var_im->im_provider_id = $a_Location[$var_service];
                                
                                if ($lng_i == 1) { //make first im entered primary
                                    $$var_im->is_primary = 1;
                                } else {
                                    $$var_im->is_primary = 0;
                                }                               

                                //if ($lng_contact_id) {
                                if (strlen($a_im[$lng_i])) {
                                    // update the crm_im for $lng_location_id and im id ($a_im[$lng_i])
                                    
                                    $$var_im->whereAdd('location_id = '.$lng_location_id);
                                    $$var_im->whereAdd('id = '.$a_im[$lng_i]);
                                    
                                    if(!$$var_im->update(DB_DATAOBJECT_WHEREADD_ONLY)) {
                                        if (strlen($str_error = mysql_error())) {
                                            break;
                                        }
                                    }    
                                } else {
                                    // insert new record
                                    if (!$$var_im->insert()) {
                                        $str_error = mysql_error();
                                        break;
                                    }    
                                }
                            }  
                        } // end of im for loop
                    }  
                }// end of if block    
                         
                if (strlen($str_error)) { //proceed if there are no errors
                    break;
                }
            } //end of for loop for location
        } 
        
        // check if there are any errors while inserting in database
        
        if (strlen($str_error)) { //commit if there are no errors else rollback
            $contact->query('ROLLBACK');
            form_set_error('first_name', t($str_error));
        } else {
            $contact->query('COMMIT');
            form_set_error('first_name', t('Contact Individual has been saved successfully.'));
        }
        
    }//end of function


   
    /**
     * This function does all the processing of the form for Quick add for Contact Individual.
     *
     * @access private
     */
    private function _miniAddPostProcess() 
    { 
        
        $str_error = ""; // error is recorded  if there are any errors while inserting in database         
        
        // create a object for inserting data in contact table 
        $contact = new CRM_Contact_DAO_Contact();
        
        $contact->domain_id = 1;
        $contact->contact_type = 'Individual';
        // $contact->contact_type = $this->exportValue('contact_type');
        $contact->sort_name = $this->exportValue('firstname')." ".$this->exportValue('lastname');
        
        $contact->query('BEGIN'); //begin the database transaction
        
        if (!$contact->insert()) {
            $str_error = mysql_error();
        }
        
        if(!strlen($str_error)){ //proceed if there are no errors
            // create a object for inserting data in contact individual table 
            $contact_individual = new CRM_Contact_DAO_Individual();
            $contact_individual->contact_id = $contact->id;
            $contact_individual->first_name = $this->exportValue('firstname');
            $contact_individual->last_name = $this->exportValue('lastname');
            
            if(!$contact_individual->insert()) {
                $str_error = mysql_error();
            }
        }
        
        
        if(!strlen($str_error)){ //proceed if there are no errors  
            
            if(!strlen($str_error)){ //proceed if there are no errors
                // create a object of crm location
                $contact_location = new CRM_Contact_DAO_Location();
                $contact_location->contact_id = $contact->id;
                $contact_location->is_primary = 1;
                //contact_location->location_type_id = $a_Location['location_type_id'];
                
                if(!$contact_location->insert()) {
                    $str_error = mysql_error();
                    break;
                }
            }
            
            if(!strlen($str_error)){ //proceed if there are no errors
                //create the object of crm email
                if (strlen(trim($this->exportValue('email')))) {
                    
                    $contact_email = new CRM_Contact_DAO_Email();
                    $contact_email->is_primary = 1;
                    
                    $contact_email->location_id = $contact_location->id;
                    $contact_email->email = $this->exportValue('email');
                    
                    if(!$contact_email->insert()) {
                        $str_error = mysql_error();
                        break;
                    }    
                    
                }
            }
            
            if(!strlen($str_error)){ //proceed if there are no errors
                //create the object of crm phone
                if (strlen(trim($this->exportValue('phone')))) {
                    
                    $contact_phone = new CRM_Contact_DAO_Phone();
                    $contact_phone->is_primary = 1;
                    
                    $contact_phone->location_id = $contact_location->id;
                    $contact_phone->phone = $this->exportValue('phone');
                    //$contact_phone->phone_type = $a_Location[$varphone_type];
                    //$contact_phone->mobile_provider_id = 1;
                    
                    
                    if(!$contact_phone->insert()) {
                        $str_error = mysql_error();
                        break;
                    }    
                }  
                
            }
            
        }// end of if
 
        // check if there are any errors while inserting in database
                 
        if(strlen($str_error)){ //commit if there are no errors else rollback
            $contact->query('ROLLBACK');
            form_set_error('first_name', t($str_error));
        } else {
            $contact->query('COMMIT');
            form_set_error('first_name', t('Contact Individual has been saved successfully.'));
        }
        
    }// end of function

}
    
?>