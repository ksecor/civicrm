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
        
        // CRM_Error::debug_stacktrace();
        parent::__construct($name, $state, $mode);
    }
    
    
    /**
     * In this function we build the CRUD.php. All the quickform componenets are defined in this function
     */
    function buildQuickForm()
    {
        switch ($this->_mode) {
        case self::MODE_CREATE:
            $this->_buildCreateForm();
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_EDIT:
            break;
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            break;            
        
        } // end of switch
          
    }//ENDING BUILD FORM 

    
    /**
     * this function sets the default values to the specified form element
     */
    function setDefaultValues() 
    {
        $defaults = array();
        $defaults['first_name'] = 'Dave';
        $defaults['last_name'] = 'Greenberg';
        $defaults['location1[email_1]'] = 'dgg@blackhole.net';
        
        $this->setDefaults($defaults);
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
         
    }
    

    /**
     * this function is called when the form is submitted.
     */
    function postProcess() 
    { 
        $str_error = ""; // error is recorded  if there are any errors while inserting in database         
        

        // create a object for inserting data in contact table 
        $contact = new CRM_Contact_DAO_Contact();
        
        $contact->domain_id = 1;
        $contact->contact_type = $this->exportValue('contact_type');
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
    private function _buildCreateForm() {
        // create the arrays for select elements
        $prefix_select = array(
                               ' '    => '-title-',
                               'Mrs.' => 'Mrs.',
                               'Ms.'  => 'Ms.',
                               'Mr.'  => 'Mr.',
                               'Dr'   => 'Dr.',
                               'none' => '(none)',
                               );
        
        $suffix_select = array(
                               ' '    => '-suffix-',
                               'Jr.'  => 'Jr.',
                               'Sr.'  => 'Sr.', 
                               '||'   =>'||',
                               'none' => '(none)',
                               );
    
        $greeting_select = array(
                                 'Formal'    => 'default - Dear [first] [last]',
                                 'Informal'  => 'Dear [first]', 
                                 'Honorific' => 'Dear [title] [last]',
                                 'Custom'    => 'Customized',
                                 );
        $date_options = array(
                              'language'  => 'en',
                              'format'    => 'dMY',
                              'minYear'   => 1900,
                              'maxYear'   => date('Y'),
                              );  
        
        $context_select = array(
                                1 => 'Home', 
                                'Work', 
                                'Main',
                                'Other'
                                );
        
        $im_select = array( 
                           1 => 'Yahoo', 
                           'MSN', 
                           'AIM', 
                           'Jabber',
                           'Indiatimes'
                           );
        
        $phone_select = array(
                              'Phone' => 'Phone', 
                              'Mobile' => 'Mobile', 
                              'Fax' => 'Fax', 
                              'Pager' => 'Pager'
                              );
        
        
        $state_select = array( 
                              1004 => 'California', 
                              1036 => 'Oregon', 
                              1046 => 'Washington'
                              );
        
        
        $country_select = array( 
                                1039 => 'Canada', 
                                1101 => 'India', 
                                1172 => 'Poland', 
                                1128 => 'United States'
                                );
        
        // prefix
        $this->addElement('select', 'prefix', null, $prefix_select);
        
        // first_name
        $this->addElement('text', 'first_name', 'First / Last :');
        
        // last_name
        $this->addElement('text', 'last_name', null);
        
        // suffix
        $this->addElement('select', 'suffix', null, $suffix_select);
        
        // greeting type
        $this->addElement('select', 'greeting_type', 'Greeting type :', $greeting_select);
        
        // job title
        $this->addElement('text', 'job_title', 'Job title :');
        

        // add the communications block
        //        CRM_Contact_Form_Contact::buildCommuncationBlock($this);
        CRM_Contact_Form_Contact::bcb($this);

        // radio button for gender
        $this->addElement('radio', 'gender', 'Gender', 'Female','female',
                          array('onclick' => "document.CRUD.elements['mdyx'].value = 'true';",'checked' => null));
        $this->addElement('radio', 'gender', 'Gender', 'Male', 'male', 
                          array('onclick' => "document.CRUD.elements['mdyx'].value = 'true';"));
        $this->addElement('radio', 'gender', 'Gender', 'Transgender','transgender', 
                          array('onclick' => "document.CRUD.elements['mdyx'].value = 'true';"));
        $this->addElement('checkbox', 'is_deceased', 'Contact is deceased', null, 
                          array('onclick' => "document.CRUD.elements['mdyx'].value = 'true';"));
        
        $this->addElement('date', 'birth_date', 'Date of birth', $date_options, 
                          array('onclick' => "document.CRUD.elements['mdyx'].value = 'true';"));
        
        /* Entering the compact location engine */ 
        
        $i = 0;
        $loc[1][$i++] = & $this->createElement('select', 'location_type_id', null, $context_select,
                                               array('onchange' => "return validate_selected_locationid(1);"));
        $loc[1][$i++] = & $this->createElement('checkbox', 'is_primary', 'Primary location for this contact', null,
                                               array('onchange' => "location_is_primary_onclick(1);"));
        
        $i = 0;
        $loc[2][$i++] = & $this->createElement('select', 'location_type_id', null, $context_select,
                                               array('onchange' => "return validate_selected_locationid(2);"));
        $loc[2][$i++] = & $this->createElement('checkbox', 'is_primary', 'Primary location for this contact', null,
                                               array('onchange' => "location_is_primary_onclick(2);"));

        $i = 0;
        $loc[3][$i++] = & $this->createElement('select', 'location_type_id', null, $context_select,
                                               array('onchange' => "return validate_selected_locationid(3);"));
        $loc[3][$i++] = & $this->createElement('checkbox', 'is_primary', 'Primary location for this contact', null,
                                               array('onchange' => "location_is_primary_onclick(3);"));
        $forward = $i;
        
        
        for ($i = 1;$i <= 3;$i++) {
            $j = $forward;
            $loc[$i][$j++] = & $this->createElement('select', 'phone_type_1', null, $phone_select);
            $loc[$i][$j++] = & $this->createElement('text', 'phone_1', 'Preferred Phone:', array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('select','phone_type_2', null, $phone_select);
            $loc[$i][$j++] = & $this->createElement('text', 'phone_2', 'Other Phone:', array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('select', 'phone_type_3', null, $phone_select);
            $loc[$i][$j++] = & $this->createElement('text', 'phone_3',  'Other Phone:', array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('text', 'email_1', 'Email:', array('size' => '47px'));
            $loc[$i][$j++] = & $this->createElement('text', 'email_2', 'Other Email:', array('size' => '47px'));
            $loc[$i][$j++] = & $this->createElement('text', 'email_3', 'Other Email:', array('size' => '47px'));
            $loc[$i][$j++] = & $this->createElement('select', 'im_service_id_1', 'Instant Message:', $im_select);
            $loc[$i][$j++] = & $this->createElement('text', 'im_screenname_1', null, array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('select', 'im_service_id_2',  'Instant Message:', $im_select);
            $loc[$i][$j++] = & $this->createElement('text', 'im_screenname_2', null,array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('select','im_service_id_3',  'Instant Message:', $im_select);
            $loc[$i][$j++] = & $this->createElement('text', 'im_screenname_3', null, array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('text', 'street_address', 'Street Address:', array('size' => '47px'));
            $loc[$i][$j++] = & $this->createElement('textarea', 'supplemental_address_1', 'Address:', array('cols' => '47'));
            $loc[$i][$j++] = & $this->createElement('text', 'city', 'City:');
            $loc[$i][$j++] = & $this->createElement('text', 'postal_code', 'Zip / Postal Code:');
            $loc[$i][$j++] = & $this->createElement('select', 'state_province_id', 'State / Province:', $state_select);
            $loc[$i][$j++] = & $this->createElement('select', 'country_id', 'Country:', $country_select);
        }
        
        $this->addGroup($loc[1],'location1');
        $this->addGroup($loc[2],'location2');
        $this->addGroup($loc[3],'location3');
        
        /* Exiting location engine */
        
        
        for ($i = 1; $i <= 3; $i++) {    
            $this->addElement('link', 'exph02_'."{$i}", null, 'phone_'."{$i}".'_2', '[+] another phone',
                              array('onclick' => "show('phone_{$i}_2'); hide('expand_phone_{$i}_2'); show('expand_phone_{$i}_3'); return false;"));
            $this->addElement('link', 'hideph02_'."{$i}", null, 'phone_'."{$i}".'_2', '[-] hide phone',
                              array('onclick' => "hide('phone_{$i}_2'); hide('expand_phone_{$i}_3'); show('expand_phone_{$i}_2');hide('phone_{$i}_3'); return false;"));
            $this->addElement('link', 'exph03_'."{$i}", null, 'phone_'."{$i}".'_3', '[+] another phone',
                              array('onclick'=> "show('phone_{$i}_3'); hide('expand_phone_{$i}_3'); return false;"));
            $this->addElement('link', 'hideph03_'."{$i}", null, 'phone_'."{$i}".'_3', '[-] hide phone',
                              array( 'onclick' => "hide('phone_{$i}_3'); show('expand_phone_{$i}_3'); return false;"));
            $this->addElement('link', 'exem02_'."{$i}", null, 'email_'."{$i}".'_2', '[+] another email',
                              array('onclick' => "show('email_{$i}_2'); hide('expand_email_{$i}_2'); show('expand_email_{$i}_3'); return false;"));
            $this->addElement('link','hideem02_'."{$i}", null, 'email_'."{$i}".'_2', '[-] hide email',
                              array('onclick' => "hide('email_{$i}_2'); hide('expand_email_{$i}_3'); show('expand_email_{$i}_2'); hide('email_{$i}_3'); return false;"));
            $this->addElement('link', 'exem03_'."{$i}", null, 'email_'."{$i}".'_3', '[+] another email',
                              array('onclick' => "show('email_{$i}_3'); hide('expand_email_{$i}_3'); return false;"));
            $this->addElement('link', 'hideem03_'."{$i}", null, 'email_'."{$i}".'_3', '[-] hide email',
                              array('onclick' => "hide('email_{$i}_3'); show('expand_email_{$i}_3'); return false;"));
            $this->addElement('link', 'exim02_'."{$i}", null, 'IM_'."{$i}".'_2','[+] another instant message',
                              array('onclick' => "show('IM_{$i}_2'); hide('expand_IM_{$i}_2'); show('expand_IM_{$i}_3'); return false;"));
            $this->addElement('link', 'hideim02_'."{$i}", null, 'IM_'."{$i}".'_2', '[-] hide instant message',
                              array('onclick' => "hide('IM_{$i}_2'); hide('expand_IM_{$i}_3'); show('expand_IM_{$i}_2'); hide('IM_{$i}_3'); return false;"));
            $this->addElement('link', 'exim03_'."{$i}", null, 'IM_'."{$i}".'_3', '[+] another instant message',
                              array('onclick' => "show('IM_{$i}_3'); hide('expand_IM_{$i}_3'); return false;"));
            $this->addElement('link', 'hideim03_'."{$i}", null, 'IM_'."{$i}".'_3', '[-] hide instant message',
                              array('onclick' => "hide('IM_{$i}_3'); show('expand_IM_{$i}_3'); return false;"));
        }
        
        $this->addElement('link', 'exloc2', null, 'location2', '[+] another location',
                          array( 'onclick' => "hide('expand_loc2'); show('location2'); show('expand_loc3'); return false;"));
        $this->addElement('link', 'hideloc2', null, 'location2', '[-] hide location',
                          array('onclick' => "hide('location2'); show('expand_loc2'); hide('expand_loc3');return false;"));
        $this->addElement('link', 'exloc3', null, 'location2', '[+] another location ',
                          array('onclick' => "hide('expand_loc3'); show('location3'); return false;"));
        $this->addElement('link', 'hideloc3', null, 'location3', '[-] hide location',
                          array('onclick' => "hide('location3'); show('expand_loc3'); hide('expand_loc2');return false;"));
        
        
        /* End of locations */
        
        $this->add('textarea', 'address_note', 'Notes:', array('cols' => '82'));    
        
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

}

?>
