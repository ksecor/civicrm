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

/**
 * This class is used for building Individual.php. This class also has the actions that should be done when form is processed.
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
     * In this function we build the Individual.php. All the quickform componenets are defined in this function
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
                                                                            array(t( 'Please enter valid email for location').$i.'.', 'email', null, 'client')),
                                                         'email_2' => array( 
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
        // create a object for inserting data in contact table 
        $contact = new CRM_Contact_DAO_Contact();
        
        $contact->domain_id = 1;
        $contact->sort_name = $this->exportValue('first_name')." ".$this->exportValue('last_name');
        
        static $contactProps = array( 'contact_type', 'source', 'preferred_communication_method',
                                      'do_not_phone', 'do_not_email', 'do_not_mail', 'hash' );
        foreach ( $contactProps as $prop ) {
            $contact->$prop = $this->exportValue( $prop );
        }

        $contact->query('BEGIN'); //begin the database transaction
        
        if (!$contact->insert()) {
            return $this->error( mysql_error(), 8000, $contact );
        }
        
        // create a object for inserting data in contact individual table 
        $contact_individual = new CRM_Contact_DAO_Contact_Individual();
        $contact_individual->contact_id = $contact->id;
        
        static $individualProps = array( 'first_name', 'middle_name', 'last_name',
                                         'prefix', 'suffix', 'job_title',
                                         'greeting_type', 'custom_greeting', 'gender', 'is_deceased' );
        foreach ( $individualProps as $prop ) {
            $contact_individual->$prop = $this->exportValue( $prop );
        }
        
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
        
        if(! $contact_individual->insert()) {
            return $this->error( mysql_error(), 8000, $contact );
        }
        
        // create a object for inserting data in crm_location, crm_email, crm_im, crm_phone table 
        for ($lngi= 1; $lngi <= 3; $lngi++) {
            //create a object of location class
            $varname = "contact_location".$lngi;
            $varname1 = "location".$lngi;
            
            $a_Location =  $this->exportValue($varname1);
            
            if (strlen(trim($a_Location['street_address'])) > 0  || strlen(trim($a_Location['email_1'])) > 0 || strlen(trim($a_Location['phone_1'])) > 0) {
                
                // create a object of crm location
                $$varname = new CRM_Contact_DAO_Location();
                $$varname->contact_id = $contact->id;
                $$varname->is_primary = $a_Location['is_primary'];
                $$varname->location_type_id = $a_Location['location_type_id'];
                
                if(!$$varname->insert()) {
                    return $this->error( mysql_error(), 8000, $contact );
                }
            }
            
            if (strlen(trim($a_Location['street_address'])) > 0) {
                //create the object of crm address
                $varaddress = "contact_address".$lngi;
                $$varaddress = new CRM_Contact_DAO_Address();
                
                $$varaddress->location_id = $$varname->id;
                            
                static $addressProps = array( 'street_address', 'supplemental_address_1', 'city',
                                              'state_province_id', 'postal_code', 'usps_adc',
                                              'country_id','geo_code1', 'geo_code2',
                                              'address_note', 'timezone' );
                foreach ( $addressProps as $prop ) {
                    $$varaddress->$prop = $a_Location[$prop];
                }
                
                $$varaddress->county_id = 1;
                
                if(!$$varaddress->insert()) {
                    return $this->error( mysql_error(), 8000, $contact );
                }
            }
            
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
                        return $this->error( mysql_error(), 8000, $contact );
                    }
                }  
            }
                    
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
                        return $this->error( mysql_error(), 8000, $contact );
                    }
                }  
            }
                      
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
                        return $this->error( mysql_error(), 8000, $contact );
                    }
                }    
            }
        } //end of for loop

        $contact->query('COMMIT');
        form_set_error('first_name', t('Contact Individual has been added successfully.'));
    }//end of function




    /**
     * 
     *
     */
    private function _buildCreateForm() {

        // prefix
        $this->addElement('select', 'prefix', null, CRM_SelectValues::$prefixName);
        
        // first_name
        $this->addElement('text', 'first_name', 'First / Last :');
        
        // last_name
        $this->addElement('text', 'last_name', null);
        
        // suffix
        $this->addElement('select', 'suffix', null, CRM_SelectValues::$suffixName);
        
        // greeting type
        $this->addElement('select', 'greeting_type', 'Greeting type :', CRM_SelectValues::$greeting);
        
        // job title
        $this->addElement('text', 'job_title', 'Job title :');
        

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
        
        $this->addElement('date', 'birth_date', 'Date of birth', $date_options, 
                          array('onclick' => "document.Individual.elements['mdyx'].value = 'true';"));
        
        /* Entering the compact location engine */ 
        for ( $i = 1; $i <= 3; $i++ ) {
        }
        
        for ($i = 1;$i <= 3;$i++) {
            $j = 0;

            $loc[$i][$j++] = & $this->createElement('select', 'location_type_id', null, CRM_SelectValues::$locationType,
                                                 array('onchange' => "return validate_selected_locationid($i);"));
            $loc[$i][$j++] = & $this->createElement('checkbox', 'is_primary', 'Primary location for this contact', null,
                                                 array('onchange' => "location_is_primary_onclick($i);"));
            $loc[$i][$j++] = & $this->createElement('select', 'phone_type_1', null, CRM_SelectValues::$phone);
            $loc[$i][$j++] = & $this->createElement('text', 'phone_1', 'Preferred Phone:', array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('select','phone_type_2', null, CRM_SelectValues::$phone);
            $loc[$i][$j++] = & $this->createElement('text', 'phone_2', 'Other Phone:', array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('select', 'phone_type_3', null, CRM_SelectValues::$phone);
            $loc[$i][$j++] = & $this->createElement('text', 'phone_3',  'Other Phone:', array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('text', 'email_1', 'Email:', array('size' => '47px'));
            $loc[$i][$j++] = & $this->createElement('text', 'email_2', 'Other Email:', array('size' => '47px'));
            $loc[$i][$j++] = & $this->createElement('text', 'email_3', 'Other Email:', array('size' => '47px'));
            $loc[$i][$j++] = & $this->createElement('select', 'im_service_id_1', 'Instant Message:', CRM_SelectValues::$im);
            $loc[$i][$j++] = & $this->createElement('text', 'im_screenname_1', null, array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('select', 'im_service_id_2',  'Instant Message:', CRM_SelectValues::$im);
            $loc[$i][$j++] = & $this->createElement('text', 'im_screenname_2', null,array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('select','im_service_id_3',  'Instant Message:', CRM_SelectValues::$im);
            $loc[$i][$j++] = & $this->createElement('text', 'im_screenname_3', null, array('size' => '37px'));
            $loc[$i][$j++] = & $this->createElement('text', 'street_address', 'Street Address:', array('size' => '47px'));
            $loc[$i][$j++] = & $this->createElement('textarea', 'supplemental_address_1', 'Address:', array('cols' => '47'));
            $loc[$i][$j++] = & $this->createElement('text', 'city', 'City:');
            $loc[$i][$j++] = & $this->createElement('text', 'postal_code', 'Zip / Postal Code:');
            $loc[$i][$j++] = & $this->createElement('select', 'state_province_id', 'State / Province:', CRM_SelectValues::$state);
            $loc[$i][$j++] = & $this->createElement('select', 'country_id', 'Country:', CRM_SelectValues::$country);

            $this->addGroup($loc[$i], "location$i");
        }
        /* Exiting location engine */


        for ($i = 1; $i <= 3; $i++) {
            $this->addElement('link', 'exph02_'."{$i}", null, 'phone_'."{$i}".'_2', '[+] another phone',
                              array('onclick' => "show('phone_{$i}_2'); hide('expand_phone_{$i}_2'); show('expand_phone_{$i}_3'); return false;")
                              );
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
