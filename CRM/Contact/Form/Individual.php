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


        switch ($this->_mode) {
        case self::MODE_ADD:
            //$this->addElement('static', 'display_set_fields', "false"); 
            $this->addElement('text', 'mode', self::MODE_ADD);
            $this->_buildAddForm();
            break;  
        case self::MODE_VIEW:
            break;  
        case self::MODE_UPDATE:
            //$this->addElement('static', 'display_set_fields', "true"); 
            $this->addElement('text', 'mode', self::MODE_UPDATE);
            $this->_buildAddForm();
            break;  
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            $this->addElement('text', 'mode', self::MODE_SEARCH);
            $this->_buildSearchForm();
            break;            
        case self::MODE_ADD_MINI:
            $this->addElement('text', 'mode', self::MODE_ADD_MINI);
            $this->_buildMiniAddForm();
            break;            
        case self::MODE_SEARCH_MINI:
            $this->addElement('text', 'mode', self::MODE_SEARCH_MINI);
            $this->_buildMiniSearchForm();
            break;            
            
        } // end of switch
        
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

        switch ($this->_mode) {
        case self::MODE_ADD:
            $defaults['first_name'] = 'Dave';
            $defaults['last_name'] = 'Greenberg';
            $defaults['location1[email_1]'] = 'dgg@blackhole.net';
            $this->setDefaults($defaults);
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            $defaults['location2[email_1]'] = 'dgg@blackhole.net';
            $this->setDefaults($defaults);
            break;
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            break;            
        case self::MODE_ADD_MINI:
            break;            
        case self::MODE_SEARCH_MINI:
            
            $defaults['sname'] = ' - full or partial name - ';
            $this->setDefaults($defaults);
            break;            
        }
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
            $this->addRule('first_name', t(' First name is a required field.'), 'required', null, 'client');
            $this->addRule('last_name', t(' Last name is a required field.'), 'required', null, 'client');

            $this->registerRule('check_date', 'callback', 'valid_date','CRM_Contact_Form_Individual');
            $this->registerRule('check_date', 'callback', CRM_RULE::date(),'CRM_Contact_Form_Individual');

            // $this->addRule('birth_date', t(' Select a valid date.'), 'check_date');
            
            for ($i = 1; $i <= 3; $i++) { 
                $this->addGroupRule('location'."{$i}", array('email_1' => array( 
                                                                                array(t( 'Please enter valid email for location').$i.'.', 'email', null, 'client')),                                                 'email_2' => array( 
                                                                                                                                                                                                                                        array(t( ' Please enter valid secondary email for location').$i.'.', 'email', null, 'client')),
                                                             'email_3' => array( 
                                                                                array(t( ' Please enter valid tertiary email for location' ).$i.'.', 'email', null, 'client'))
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
        case self::MODE_ADD_MINI:
            $this->addRule('firstname', t(' First name is a required field.'), 'required', null, 'client');
            $this->addRule('lastname', t(' Last name is a required field.'), 'required', null, 'client');
            $this->addRule('email', t(' Email Address is required field.'), 'required', null, 'client');
            $this->addRule('email', t(' Enter valid email address.'), 'email', null, 'client');
            break;            
        case self::MODE_SEARCH_MINI:
            $this->addRule('sname', t(' Enter valid criteria for searching.'), 'required', null, 'client');
            $this->addRule('semail', t(' Enter valid Email Address.'), 'email', null, 'client');
            
            break;            
        }    
        
    }
    

    /**
     * This function is used to call appropriate process function when a form is submitted.
     * 
     * The function implements a switch functionality to differentiate between different mode types of the form. It is based on the 
     * value of the $mode form class variable. 
     * @internal Possible mode options being MODE_ADD, MODE_VIEW, MODE_UPDATE, MODE_DELETE, MODE_ADD_MINI, MODE_SEARCH_MINI.
     * The process works as follows:
     * <code>
     * switch ($this->_mode) {
     *        case self::MODE_ADD:
     *             $this->_Add_postProcess();
     *             break;
     *        case self::MODE_ADD_MINI:
     *             $this->_MiniAdd_postProcess();
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
            break;
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            break;            
        case self::MODE_ADD_MINI:
            $this->_miniAddPostProcess();
            break;            
        case self::MODE_SEARCH_MINI:
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
     * @see _buildMiniAddForm()
     * 
     */
    private function _buildAddForm( ) 
    {
        
        $form_name = $this->getName();

        CRM_SelectValues::$date['maxYear'] = date('Y');
        
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
        $this->addElement('radio', 'gender', 'Gender', 'Female', 'female',
                          array('onclick' => "document.Individual.elements['mdyx'].value = 'true';",'checked' => null));
        $this->addElement('radio', 'gender', 'Gender', 'Male', 'male', 
                          array('onclick' => "document.Individual.elements['mdyx'].value = 'true';"));
        $this->addElement('radio', 'gender', 'Gender', 'Transgender','transgender', 
                          array('onclick' => "document.Individual.elements['mdyx'].value = 'true';"));
        $this->addElement('checkbox', 'is_deceased', 'Contact is deceased', null, 
                          array('onclick' => "document.Individual.elements['mdyx'].value = 'true';"));
        
        $this->addElement('date', 'birth_date', 'Date of birth', CRM_SelectValues::$date, 
                          array('onclick' => "document.Individual.elements['mdyx'].value = 'true';"));

        /* Entering the compact location engine */ 

        $location = CRM_Contact_Form_Location::buildLocationBlock($this, 3);
        for ($i = 1; $i < 4; $i++) {
            $this->addGroup($location[$i],'location'."{$i}");
            $this->UpdateElementAttr(array($location[$i][0]), array('onchange' => "return validate_selected_locationid(\"$form_name\", {$i});"));
            $this->UpdateElementAttr(array($location[$i][1]), array('onchange' => "location_is_primary_onclick(\"$form_name\", {$i});"));
        }
        /* End of locations */

        $this->add('textarea', 'address_note', 'Notes:', array('cols' => '82', 'maxlength' => 255));    
        
        $this->addElement('link', 'exdemo', null, 'demographics', '[+] show demographics',
                          array('onclick' => "show('demographics'); hide('expand_demographics'); return false;"));
        
        $this->addElement('link', 'exnotes', null, 'notes', '[+] contact notes',
                          array('onclick' => "show('notes'); hide('expand_notes'); return false;"));
        
        $this->addElement('link', 'hidedemo', null,'demographics', '[-] hide demographics',
                          array('onclick' => "hide('demographics'); show('expand_demographics'); return false;"));
        
        $this->addElement('link', 'hidenotes', null, 'notes', '[-] hide contact notes',
                          array('onclick' => "hide('notes'); show('expand_notes'); return false;"));
        
        $this->addElement('hidden', 'mdyx', 'false');

        $java_script = "<script type = \"text/javascript\">
                        frm = document." . $form_name ."; frm_name = '" . $form_name ."'; </script>";

        $this->addElement('static', 'my_script', $java_script);

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
     * This function provides the HTML form elements for the add operation of a mini individual contact form.
     * 
     * This function is called by the buildQuickForm method, when the value of the $mode class variable is set to MODE_ADD_MINI
     * The addElement and addGroup method of HTML_QuickForm is used to add HTML elements to the form which is referenced using the $this 
     * form handle. Also the default values for the form elements are set in this function.
     * 
     * @access private
     * @return None
     * @see buildQuickForm() 
     * @see _buildMiniSearchForm()
     * 
     */  
    private function _buildMiniAddForm() 
    {
        $this->setFormAction("crm/contact/qadd/");
        $this->addElement('text', 'firstname', 'First Name: ');
        $this->addElement('text', 'lastname', 'Last Name: ');
        $this->addElement('text', 'email', 'Email: ');
        $this->addElement('text', 'phone', 'Phone: ');

        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'next',
                                                'name'      => 'Save',
                                                'isDefault' =>  true )
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
     * This function provides the HTML form elements for the add operation of a mini individual search form.
     * 
     * This function is called by the buildQuickForm method, when the value of the $mode class variable is set to MODE_SEARCH_MINI
     * The addElement and addGroup method of HTML_QuickForm is used to add HTML elements to the form which is referenced using the $this 
     * form handle. Also the default values for the form elements are set in this function.
     * 
     * @access private
     * @return None
     * @see buildQuickForm() 
     * @see _buildMiniAddForm()
     * 
     */  
    private function _buildMiniSearchForm() 
    {
        $this->addElement('text', 'sname', 'Name: ');
        $this->addElement('text', 'semail', 'Email: ');
        $this->addElement('link','search','advsearch','crm/contact/search','>> Advanced Search');
        
        $this->addDefaultButtons( array (
                                         array ('type'      =>  'submit', 
                                                'name'      =>  'Search',
                                                'isDefault' =>   true
                                                )
                                         )
                                  );
    }
         
    /**
     * This function does all the processing of the form for New Contact Individual.
     * @access private
     */
    private function _addPostProcess() 
    { 

        $str_error = ""; // error is recorded  if there are any errors while inserting in database         
        
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
        $contact->do_not_phone = $this->exportValue('do_not_phone');
        $contact->do_not_email = $this->exportValue('do_not_email');
        $contact->do_not_mail = $this->exportValue('do_not_mail');
        //$contact->hash = $this->exportValue('hash');
        
        $contact->query('BEGIN'); //begin the database transaction
        
        if (!$contact->insert()) {

            CRM_Error::debug_log_message("breakpoint 10");
            
            $str_error = mysql_error();
        }
        
        if(!strlen($str_error)){ //proceed if there are no errors
            // create a object for inserting data in contact individual table 
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
            $contact_individual->gender = $this->exportValue('gender');
            
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
                     
            if(!$contact_individual->insert()) {
                $str_error = mysql_error();
            }
        }
                 
                 
        if(!strlen($str_error)){ //proceed if there are no errors  
            // create a object for inserting data in crm_location, crm_email, crm_im, crm_phone table 
            for ($lngi= 1; $lngi <= 3; $lngi++) {

                // CRM_Error::debug_var("lngi", $lngi);
                            
                //create a object of location class
                $varname = "contact_location".$lngi;
                $varname1 = "location".$lngi;
                         
                $a_Location =  $this->exportValue($varname1);
                         
                if (strlen(trim($a_Location['street_address'])) > 0  || strlen(trim($a_Location['email_1'])) > 0 || strlen(trim($a_Location['phone_1'])) > 0) {
                             
                    if(!strlen($str_error)){ //proceed if there are no errors
                        // create a object of crm location
                        $$varname = new CRM_Contact_DAO_Location();
                        $$varname->contact_id = $contact->id;
                        $$varname->location_type_id = $a_Location['location_type_id'];
                        $$varname->is_primary = $a_Location['is_primary'];
                        
                                 
                        if(!$$varname->insert()) {
                            CRM_Error::debug_log_message("breakpoint 30");
                            $str_error = mysql_error();
                            break;
                        }
                    }
                             
                    if(!strlen($str_error)){ //proceed if there are no errors
                        if (strlen(trim($a_Location['street_address'])) > 0) {
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

                                     
                            if(!$$varaddress->insert()) {
                                $str_error = mysql_error();
                                break;
                            }
                        }              
                    }
                             
                             
                    if(!strlen($str_error)){ //proceed if there are no errors
                        //create the object of crm email
                        for ($lng_i= 1; $lng_i <= 3; $lng_i++) {
                            $varemail = "email_".$lng_i;
                            if (strlen(trim($a_Location[$varemail])) > 0) {
                                $var_email = "contact_email".$lng_i;
                                $$var_email = new CRM_Contact_DAO_Email();
                               
                                $$var_email->location_id = $$varname->id;
                                $$var_email->email = $a_Location[$varemail];
                                         
                                if($lng_i == 1) { //make first email entered primary
                                    $$var_email->is_primary = 1;
                                } else {
                                    $$var_email->is_primary = 0;
                                }
                                         
                                         
                                if(!$$var_email->insert()) {
                                    CRM_Error::debug_log_message("breakpoint 50");
                                    $str_error = mysql_error();
                                    break;
                                }    
                            }  
                        }
                    }
                             
                    if(!strlen($str_error)){ //proceed if there are no errors
                        //create the object of crm phone
                        for ($lng_i= 1; $lng_i <= 3; $lng_i++) {
                            $varphone = "phone_".$lng_i;
                            $varphone_type = "phone_type_".$lng_i;
                            $varmobile_prov_id = "mobile_provider_id_".$lng_i;
                            if (strlen(trim($a_Location[$varphone])) > 0) {
                                $var_phone = "contact_phone".$lng_i;
                                $$var_phone = new CRM_Contact_DAO_Phone();
                      
                                $$var_phone->location_id = $$varname->id;
                                $$var_phone->phone = $a_Location[$varphone];
                                $$var_phone->phone_type = $a_Location[$varphone_type];
                                
                                if($lng_i == 1) { //make first phone entered primary
                                    $$var_phone->is_primary = 1;
                                } else {
                                    $$var_phone->is_primary = 0;
                                }


                                // $$var_phone->mobile_provider_id = $a_Location[$varmobile_prov_id];
                                $$var_phone->mobile_provider_id = 1;
                                         
                                         
                                if(!$$var_phone->insert()) {
                                    CRM_Error::debug_log_message("breakpoint 60");
                                    $str_error = mysql_error();
                                    break;
                                }    
                            }  
                        }
                    }
                             
                             
                    if(!strlen($str_error)){ //proceed if there are no errors
                        //create the object of crm im
                        for ($lng_i= 1; $lng_i <= 3; $lng_i++) {
                            $var_service = "im_service_id_".$lng_i;
                            $var_screenname = "im_screenname_".$lng_i;
                            if (strlen(trim($a_Location[$var_screenname])) > 0) {
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
                                         
                                if (!$$var_im->insert()) {
                                    $str_error = mysql_error();
                                    break;
                                }    
                            }  
                        }
                    }  
                    
                }// end of if block    
                         
                if(strlen($str_error)){ //proceed if there are no errors
                    break;
                }
            } //end of main for loop
        } 
        // check if there are any errors while inserting in database
        
        if(strlen($str_error)){ //commit if there are no errors else rollback
            $contact->query('ROLLBACK');
            form_set_error('first_name', t($str_error));
        } else {
            $contact->query('COMMIT');
            form_set_error('first_name', t('Contact Individual has been added successfully.'));
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
            form_set_error('first_name', t('Contact Individual has been added successfully.'));
        }
        
    }// end of function

}
    
?>