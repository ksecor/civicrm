<?php

require_once 'CRM/Form.php';

/**
 * This class is used for building CRUD.php. This class also has the actions that should be done when form is processed.
 */
class CRM_Contacts_Form_CRUD extends CRM_Form 
{
    
    /**
     * This is the constructor of the class.
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);
    }
    

    /**
     * In this function we build the CRUD.php. All the quickform componenets are defined in this function
     */
    function buildQuickForm()
    {
        
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
    
        $pcm_select = array(
                            ' '      => '-no preference-',
                            'Phone'  => 'by phone', 
                            'Email'  => 'by email', 
                            'Post' => 'by postal email',
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
        
        $this->addElement('text','offsetter',label_offset("",28));

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
        
        // checkboxes for DO NOT phone, email, mail
        $this->addElement('checkbox', 'do_not_phone', 'Privacy:', 'Do not call');
        $this->addElement('checkbox', 'do_not_email', null, 'Do not contact by email');
        $this->addElement('checkbox', 'do_not_mail', null, 'Do not contact by postal mail');
        
        // preferred communication method 
        $this->add('select','preferred_communication_method', 'Prefers:',$pcm_select);
        
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
        

        /* End of locationas */
        
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

        $this->addRules();
        $this->setDefaultValues();

        if ($this->_mode == self::MODE_CREATE || self::MODE_UPDATE || self::MODE_DELETE) {
            if ($this->validate()) {
                $this->process();
            } 
        } else {
            if ($this->_mode == self::MODE_VIEW ) {
                $this->freeze();    
            }
        }
        
          
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
        $this->registerRule('check_date', 'callback', 'valid_date','CRM_Contacts_Form_CRUD');
        //$this->addRule('birth_date', t(' Select a valid date.'), 'check_date');
    
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
    function process() 
    { 
        $str_error = ""; // error is recorded  if there are any errors while inserting in database         
        
        // create a object for inserting data in contact table 
        $contact = new CRM_Contacts_DAO_Contact();
        
        $contact->domain_id = 1;
        $contact->contact_type = $_POST['contact_type'];
        $contact->sort_name = $_POST['first_name']." ".$_POST['last_name'];
        $contact->source = $_POST['source'];
        $contact->preferred_communication_method = $_POST['preferred_communication_method'];
        $contact->do_not_phone = $_POST['do_not_phone'];
        $contact->do_not_email = $_POST['do_not_email'];
        $contact->do_not_mail = $_POST['do_not_mail'];
        $contact->hash = $_POST['hash'];

        $contact->query('BEGIN'); //begin the database transaction
        
        if (!$contact->insert()) {
            $str_error = mysql_error();
        }
        
        if(!strlen($str_error)){ //proceed if there are no errors
            // create a object for inserting data in contact individual table 
            $contact_individual = new CRM_Contacts_DAO_Contact_Individual();
            $contact_individual->contact_id = $contact->id;
            $contact_individual->first_name = $_POST['first_name'];
            $contact_individual->middle_name = $_POST['middle_name'];
            $contact_individual->last_name = $_POST['last_name'];
            $contact_individual->prefix = $_POST['prefix'];
            $contact_individual->suffix = $_POST['suffix'];
            $contact_individual->job_title = $_POST['job_title'];
            
            $contact_individual->greeting_type = $_POST['greeting_type'];
            $contact_individual->custom_greeting = $_POST['custom_greeting'];
            $contact_individual->gender = $_POST['gender'];
            
            if ($_POST['birth_date']['d'] < 10) {
                $day = "0".$_POST['birth_date']['d'];
            } else {
                $day = $_POST['birth_date']['d'];
            }
            
            if ($_POST['birth_date']['M'] < 10) {
                $mnt = "0".$_POST['birth_date']['M'];
            } else {
                $mnt = $_POST['birth_date']['M'];
            }
            
            $contact_individual->birth_date = $_POST['birth_date']['Y'].$mnt.$day;
            $contact_individual->is_deceased = $_POST['is_deceased'];
            
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
                
                if (strlen(trim($_POST[$varname1]['street'])) > 0  || strlen(trim($_POST[$varname1]['email_1'])) > 0 || strlen(trim($_POST[$varname1]['phone_1'])) > 0) {
                    
                    if(!strlen($str_error)){ //proceed if there are no errors
                        // create a object of crm location
                        $$varname = new CRM_Contacts_DAO_Location();
                        $$varname->contact_id = $contact->id;
                        $$varname->is_primary = $_POST[$varname1]['is_primary'];
                        $$varname->location_type_id = $_POST[$varname1]['location_type_id'];
                        
                        if(!$$varname->insert()) {
                            $str_error = mysql_error();
                            break;
                        }
                    }
                
                    if(!strlen($str_error)){ //proceed if there are no errors
                        if (strlen(trim($_POST[$varname1]['street'])) > 0) {
                            //create the object of crm address
                            $varaddress = "contact_address".$lngi;
                            $$varaddress = new CRM_Contacts_DAO_Address();
                            
                            $$varaddress->location_id = $$varname->id;
                            $$varaddress->street_address = $_POST[$varname1]['street'];
                            $$varaddress->supplemental_address_1 = $_POST[$varname1]['supplemental_address'];
                            $$varaddress->city = $_POST[$varname1]['city'];
                            // $$varaddress->county_id = $_POST[$varname1]['county_id'];
                            $$varaddress->county_id = 1;
                            $$varaddress->state_province_id = $_POST[$varname1]['state_province_id'];
                            $$varaddress->postal_code = $_POST[$varname1]['postal_code'];
                            $$varaddress->usps_adc = $_POST[$varname1]['usps_adc'];
                            $$varaddress->country_id = $_POST[$varname1]['country_id'];
                            $$varaddress->geo_code1 = $_POST[$varname1]['geo_code1'];
                            $$varaddress->geo_code2 = $_POST[$varname1]['geo_code2'];
                            $$varaddress->address_note = $_POST[$varname1]['address_note'];
                            $$varaddress->timezone = $_POST[$varname1]['timezone'];
                            
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
                            if (strlen(trim($_POST[$varname1][$varemail])) > 0) {
                                $var_email = "contact_email".$lng_i;
                                $$var_email = new CRM_Contacts_DAO_Email();
                                
                                if($lng_i == 1) { //make first email entered primary
                                    $$var_email->is_primary = 1;
                                } else {
                                    $$var_email->is_primary = 0;
                                }
                            
                                $$var_email->location_id = $$varname->id;
                                $$var_email->email = $_POST[$varname1][$varemail];
                                
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
                            if (strlen(trim($_POST[$varname1][$varphone])) > 0) {
                                $var_phone = "contact_phone".$lng_i;
                                $$var_phone = new CRM_Contacts_DAO_Phone();
                                
                                if($lng_i == 1) { //make first phone entered primary
                                    $$var_phone->is_primary = 1;
                                } else {
                                    $$var_phone->is_primary = 0;
                                }
                                
                                $$var_phone->location_id = $$varname->id;
                                $$var_phone->phone = $_POST[$varname1][$varphone];
                                $$var_phone->phone_type = $_POST[$varname1][$varphone_type];
                                // $$var_phone->mobile_provider_id = $_POST[$varname1][$varmobile_prov_id];
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
                            if (strlen(trim($_POST[$varname1][$var_screenname])) > 0) {
                                $var_im = "contact_im" . $lng_i;
                                $$var_im = new CRM_Contacts_DAO_Im();
                                
                                if ($lng_i == 1) { //make first im entered primary
                                    $$var_im->is_primary = 1;
                                } else {
                                    $$var_im->is_primary = 0;
                                }
                                
                                $$var_im->location_id = $$varname->id;
                                $$var_im->im_service_id =$_POST[$varname1][$var_service];
                                $$var_im->im_screenname =$_POST[$varname1][$var_screenname];
                                
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
            $error['first_name'] = t('Database error, please try again.');
        } else {
            $contact->query('COMMIT');
        }
        
    }//end of function
     
}

    function label_offset($str,$num)
    {
        $return_string = "";
        for ($i = 0; $i < $num; $i++) {
            $return_string = $return_string . " &nbsp;"; 
        }
        return $str . $return_string;
    }

?>
