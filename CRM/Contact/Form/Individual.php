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
require_once 'CRM/Contact/Form/Contact.php';
require_once 'CRM/SelectValues.php';
require_once 'CRM/Contact/Form/Location.php';

/**
 * This class is used for building CRUD.php. This class also has the actions that should be done when form is processed.
 */
class CRM_Contact_Form_Individual extends CRM_Form 
{
    /**
     * This is the constructor of the class.
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        
        // CRM_Error::debug("name", $name);
        // CRM_Error::debug("mode", $mode);
        // CRM_Error::debug_stacktrace();

        if ($mode == self::MODE_CREATE){
            $name = "Create";
            // $name = "Individual";
        } elseif ($mode == self::MODE_CREATE_MINI) {
             $name = "Individual";
            // $name = "MiniCreate";
        } elseif ($mode == self::MODE_SEARCH_MINI) {
            $name = "MiniSearch";
        } elseif ($mode == self::MODE_SEARCH) {
            $name = "Search";
        }

        parent::__construct($name, $state, $mode);
    }
    
    
    /**
     * In this function we build the Individual.php. All the quickform componenets are defined in this function
     */
    function buildQuickForm()
    {
        switch ($this->_mode) {
        case self::MODE_CREATE:
            $this->addElement('text', 'mode', self::MODE_CREATE);
            $this->_buildCreateForm();
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            $this->addElement('text', 'mode', self::MODE_SEARCH);
            $this->_buildSearchForm();
            break;            
        case self::MODE_CREATE_MINI:
            $this->addElement('text', 'mode', self::MODE_CREATE_MINI);
            $this->_buildMiniCreateForm();
            break;            
        case self::MODE_SEARCH_MINI:
            $this->addElement('text', 'mode', self::MODE_SEARCH_MINI);
            $this->_buildMiniSearchForm();
            break;            

        } // end of switch
          
    }//ENDING BUILD FORM 

    
    /**
     * this function sets the default values to the specified form element
     */
    function setDefaultValues() 
    {
        $defaults = array();

        switch ($this->_mode) {
        case self::MODE_CREATE:
            $defaults['first_name'] = 'Dave';
            $defaults['last_name'] = 'Greenberg';
            $defaults['location1[email_1]'] = 'dgg@blackhole.net';
            $this->setDefaults($defaults);
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            break;            
        case self::MODE_CREATE_MINI:
            break;            
        case self::MODE_SEARCH_MINI:

            $defaults['sname'] = ' - full or partial name - ';
            $this->setDefaults($defaults);
            break;            
        }
    }
    
    /**
     * this function is used to validate the date
     */
    function valid_date($value) 
    {
        if(checkdate($value['M'], $value['d'], $value['Y'])) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * this function is used to add the rules for form
     */
    function addRules() 
    {
        
        $this->applyFilter('_ALL_', 'trim');
      
        // rules for searching..
        
        // rules for quick add

        switch ($this->_mode) {
        case self::MODE_CREATE:
            $this->addRule('first_name', t(' First name is a required field.'), 'required', null, 'client');
            $this->addRule('last_name', t(' Last name is a required field.'), 'required', null, 'client');
            $this->registerRule('check_date', 'callback', 'valid_date','CRM_Contact_Form_Individual');
            $this->addRule('birth_date', t(' Select a valid date.'), 'check_date');
            
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
        case self::MODE_CREATE_MINI:
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
    

    function postProcess(){
        switch ($this->_mode) {
        case self::MODE_CREATE:
            $this->_Create_postProcess();
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            break;            
        case self::MODE_CREATE_MINI:
            $this->_MiniCreate_postProcess();
            break;            
        case self::MODE_SEARCH_MINI:
            break;            
        }    
    }
    
    
    /**
     * this function is called when the form is submitted.
     */
    private function _Create_postProcess() 
    { 
        $str_error = ""; // error is recorded  if there are any errors while inserting in database         
          
        // create a object for inserting data in contact table 
        $contact = new CRM_Contact_DAO_Contact();
        
        $contact->domain_id = 1;
        // $contact->contact_type = $this->exportValue('contact_type');
        $contact->contact_type = 'Individual';
        $contact->sort_name = $this->exportValue('first_name')." ".$this->exportValue('last_name');
        $contact->source = $this->exportValue('source');
        $contact->preferred_communication_method = $this->exportValue('preferred_communication_method');
        $contact->do_not_phone = $this->exportValue('do_not_phone');
        $contact->do_not_email = $this->exportValue('do_not_email');
        $contact->do_not_mail = $this->exportValue('do_not_mail');
        $contact->hash = $this->exportValue('hash');
        
        $contact->query('BEGIN'); //begin the database transaction
        
        if (!$contact->insert()) {
            $str_error = mysql_error();
        }
        
        if(!strlen($str_error)){ //proceed if there are no errors
            // create a object for inserting data in contact individual table 
            $contact_individual = new CRM_Contact_DAO_Contact_Individual();
            $contact_individual->contact_id = $contact->id;
            $contact_individual->first_name = $this->exportValue('first_name');
            $contact_individual->middle_name = $this->exportValue('middle_name');
            $contact_individual->last_name = $this->exportValue('last_name');
            $contact_individual->prefix = $this->exportValue('prefix');
            $contact_individual->suffix = $this->exportValue('suffix');
            $contact_individual->job_title = $this->exportValue('job_title');
            
            $contact_individual->greeting_type = $this->exportValue('greeting_type');
            $contact_individual->custom_greeting = $this->exportValue('custom_greeting');
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
                     
            if(!$contact_individual->insert()) {
                $str_error = mysql_error();
            }
        }
                 
                 
        if(!strlen($str_error)){ //proceed if there are no errors  
            // create a object for inserting data in crm_location, crm_email, crm_im, crm_phone table 
            for ($lngi= 1; $lngi <= 3; $lngi++) {
                //create a object of location class
                $varname = "contact_location".$lngi;
                $varname1 = "location".$lngi;
                         
                $a_Location =  $this->exportValue($varname1);
                         
                if (strlen(trim($a_Location['street_address'])) > 0  || strlen(trim($a_Location['email_1'])) > 0 || strlen(trim($a_Location['phone_1'])) > 0) {
                             
                    if(!strlen($str_error)){ //proceed if there are no errors
                        // create a object of crm location
                        $$varname = new CRM_Contact_DAO_Location();
                        $$varname->contact_id = $contact->id;
                        $$varname->is_primary = $a_Location['is_primary'];
                        $$varname->location_type_id = $a_Location['location_type_id'];
                                 
                        if(!$$varname->insert()) {
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
                            $$varaddress->supplemental_address_1 = $a_Location['supplemental_address_1'];
                            $$varaddress->city = $a_Location['city'];
                            // $$varaddress->county_id = $a_Location['county_id'];
                            $$varaddress->county_id = 1;
                            $$varaddress->state_province_id = $a_Location['state_province_id'];
                            $$varaddress->postal_code = $a_Location['postal_code'];
                            $$varaddress->usps_adc = $a_Location['usps_adc'];
                            $$varaddress->country_id = $a_Location['country_id'];
                            $$varaddress->geo_code1 = $a_Location['geo_code1'];
                            $$varaddress->geo_code2 = $a_Location['geo_code2'];
                            $$varaddress->address_note = $a_Location['address_note'];
                            $$varaddress->timezone = $a_Location['timezone'];
                                     
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
                                         
                                if($lng_i == 1) { //make first email entered primary
                                    $$var_email->is_primary = 1;
                                } else {
                                    $$var_email->is_primary = 0;
                                }
                                         
                                $$var_email->location_id = $$varname->id;
                                $$var_email->email = $a_Location[$varemail];
                                         
                                if(!$$var_email->insert()) {
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
                                         
                                if($lng_i == 1) { //make first phone entered primary
                                    $$var_phone->is_primary = 1;
                                } else {
                                    $$var_phone->is_primary = 0;
                                }
                                
                                $$var_phone->location_id = $$varname->id;
                                $$var_phone->phone = $a_Location[$varphone];
                                $$var_phone->phone_type = $a_Location[$varphone_type];
                                // $$var_phone->mobile_provider_id = $a_Location[$varmobile_prov_id];
                                $$var_phone->mobile_provider_id = 1;
                                         
                                         
                                if(!$$var_phone->insert()) {
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
                                         
                                if ($lng_i == 1) { //make first im entered primary
                                    $$var_im->is_primary = 1;
                                } else {
                                    $$var_im->is_primary = 0;
                                }
                                         
                                $$var_im->location_id = $$varname->id;
                                $$var_im->im_service_id = $a_Location[$var_service];
                                $$var_im->im_screenname = $a_Location[$var_screenname];
                                         
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
            } //end of for loop
        } 
        // check if there are any errors while inserting in database
                 
        if(strlen($str_error)){ //proceed if there are no errors
            $contact->query('ROLLBACK');
            form_set_error('first_name', t($str_error));
        } else {
            $contact->query('COMMIT');
            form_set_error('first_name', t('Contact Individual has been added successfully.'));
        }
                 
    }//end of function
         
          
         
    /**
     * 
     *
     */
    private function _buildCreateForm() 
    {
        
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
        CRM_Contact_Form_Contact::bcb($this);

        // radio button for gender
        $this->addElement('radio', 'gender', 'Gender', 'Female','female',
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

        $location = CRM_Contact_Form_Location::blb($this, 3);
        for ($i = 1; $i < 4; $i++) {
            $this->addGroup($location[$i],'location'."{$i}");
            $this->UpdateElementAttr(array($location[$i][0]), array('onchange' => "return validate_selected_locationid('Individual', {$i});"));
            $this->UpdateElementAttr(array($location[$i][1]), array('onchange' => "location_is_primary_onclick('Individual', {$i});"));
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
        
        $this->addElement('hidden', 'mdyx','false');
        
        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'next'  ,
                                                'name'      => 'Save'  ,
                                                'isDefault' => true     ),
                                        array ( 'type'      => 'reset' ,
                                                'name'      => 'Reset'  ),
                                        array ( 'type'       => 'cancel',
                                                'name'      => 'Cancel' ),
                                        )
                                  );
       
        $this->setDefaultValues();
             
    }

         
    /**
     *  Quick add
     */
    private function _buildMiniCreateForm() 
    {
        $this->setFormAction("crm/contact/qadd");
        $this->addElement('text', 'firstname', 'First Name: ');
        $this->addElement('text', 'lastname', 'Last Name: ');
        $this->addElement('text', 'email', 'Email: ');
        $this->addElement('text', 'phone', 'Phone: ');

        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'next'  ,
                                                'name'      => 'Save'  ,
                                                'isDefault' => true     )
                                        )
                                  );
    }
         
    /**
     * Advanced Search
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
                                               'isDefault'  => true)));
    }
         
    /**
     * Quick Search
     */
    private function _buildMiniSearchForm() 
    {
        $this->addElement('text', 'sname', 'Name: ');
        $this->addElement('text', 'semail', 'Email: ');
        $this->addElement('link','search','advsearch','crm/contact/search','>> Advanced Search');
        
        $this->addDefaultButtons( array (
                                         array ('type'      =>  'submit', 
                                                'name'      =>  'Search',
                                                'isDefault' =>   true)));
    }
         


    /**
     * this function is called when the form is submitted.
     */
    private function _MiniCreate_postProcess() 
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
            $contact_individual = new CRM_Contact_DAO_Contact_Individual();
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
            
        } 
        // check if there are any errors while inserting in database
                 
        if(strlen($str_error)){ //proceed if there are no errors
            $contact->query('ROLLBACK');
            form_set_error('first_name', t($str_error));
        } else {
            $contact->query('COMMIT');
            form_set_error('first_name', t('Contact Individual has been added successfully.'));
        }
        
    }


         
         
}
    
?>
