

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
                            'Postal' => 'by postal email',
                            );
        
        $this->addDefaultButtons(array(1 => array ('next', 'Save', true),
                                       2 => array ('reset' , 'Reset', false),
                                       3 => array ('cancel', 'Cancel', false)
                                       )
                                 );
        
        // prefix
        $this->addElement('select', 'prefix', null, $prefix_select);
        
        // first_name
        $this->addElement('text', 'first_name', null, array('id'=>'firstname'));
        
        // last_name
        $this->addElement('text', 'last_name', null, array('id'=>'lastname'));
        
        // suffix
        $this->addElement('select', 'suffix', null, $suffix_select);
        
        // greeting type
        $this->addElement('select', 'greeting_type', null, $greeting_select);
        
        // job title
        $this->addElement('text', 'job_title', null);
        
        // checkboxes for DO NOT phone, email, mail
        $this->addElement('checkbox', 'do_not_phone', null);
        $this->addElement('checkbox', 'do_not_email', null);
        $this->addElement('checkbox', 'do_not_mail', null);
        
        // preferred communication method 
        $this->add('select','preferred_communication_method',null,$pcm_select);
        
        // radio button for gender
        $this->addElement('radio', 'gender', 'femalex', 'Female','female',
                          array('id'=>'fem', 'onclick' => "document.getElementById('mdy').value = 'click';"));
        $this->addElement('radio', 'gender', 'malex', 'Male', 'male', 
                          array('id'=>'male', 'onclick' => "document.getElementById('mdy').value = 'click';"));
        $this->addElement('radio', 'gender', 'malex', 'Transgender','transgender', 
                          array('id'=>'trans', 'onclick' => "document.getElementById('mdy').value = 'click';"));
        
        $this->addElement('checkbox', 'is_deceased', null, null, array('onclick' => "document.getElementById('mdy').value = 'click';"));
        
        $options = array(
                         'language'  => 'en',
                         'format'    => 'dMY',
                         'minYear'   => 1900,
                         'maxYear'   => date('Y'),
                         );  
        
        $this->addElement('date', 'birth_date', 'Choose date', $options, 
                          array('onclick' => "document.getElementById('mdy').value = 'click';"));
        
        $context_select = array(
                                1 => 'Home', 
                                'Work', 
                                'Play'
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
        
        /* Entering location cabin 1 */
        $loc1[0] = & $this->createElement('select', 'context_id', null, $context_select);
        $loc1[1] = & $this->createElement('checkbox', 'is_primary', null);
        $loc1[2] = & $this->createElement('select', 'phone_type_1', null, $phone_select);
        $loc1[3] = & $this->createElement('text', 'phone_1', null, array('size' => '37px','id' => 'ph11'));
        $loc1[4] = & $this->createElement('select','phone_type_2', null, $phone_select);
        $loc1[5] = & $this->createElement('text', 'phone_2', null, array('size' => '37px','id' => 'ph21'));
        $loc1[6] = & $this->createElement('select', 'phone_type_3', null, $phone_select);
        $loc1[7] = & $this->createElement('text', 'phone_3', null, array('size' => '37px','id' => 'ph31'));
        $loc1[8] = & $this->createElement('text', 'email', null, array('size' => '47px','id' => 'em11'));
        $loc1[9] = & $this->createElement('text', 'email_secondary', null, array('size' => '47px','id' => 'em21'));
        $loc1[10] = & $this->createElement('text', 'email_tertiary', null, array('size' => '47px','id' => 'em31'));
        $loc1[11] = & $this->createElement('select', 'im_service_id_1', null, $im_select);
        $loc1[12] = & $this->createElement('text', 'im_screenname_1', null, array('size' => '37px','id' => 'im11'));
        $loc1[13] = & $this->createElement('select', 'im_service_id_2', null, $im_select);
        $loc1[14] = & $this->createElement('text', 'im_screenname_2', null,array('size' => '37px','id' => 'im21'));
        $loc1[15] = & $this->createElement('select','im_service_id_3', null, $im_select);
        $loc1[16] = & $this->createElement('text', 'im_screenname_3', null, array('size' => '37px','id' => 'im31'));
        $loc1[17] = & $this->createElement('text', 'street', null, array('size' => '47px'));
        $loc1[18] = & $this->createElement('textarea', 'supplemental_address', null, array('cols' => '47'));
        $loc1[19] = & $this->createElement('text', 'city', null);
        $loc1[20] = & $this->createElement('text', 'postal_code', null);
        $loc1[21] = & $this->createElement('select', 'state_province_id', null, $state_select);
        $loc1[22] = & $this->createElement('select', 'country_id', null, $country_select);
        
        /* Entering location cabin 2 */
        $loc2[0] = & $this->createElement('select', 'context_id', null, $context_select);
        $loc2[1] = & $this->createElement('checkbox', 'is_primary', null);
        $loc2[2] = & $this->createElement('select', 'phone_type_1', null, $phone_select);
        $loc2[3] = & $this->createElement('text', 'phone_1', null, array('size' => '37px','id' => 'ph12'));
        $loc2[4] = & $this->createElement('select','phone_type_2', null, $phone_select);
        $loc2[5] = & $this->createElement('text', 'phone_2', null, array('size' => '37px','id' => 'ph22'));
        $loc2[6] = & $this->createElement('select', 'phone_type_3', null, $phone_select);
        $loc2[7] = & $this->createElement('text', 'phone_3', null, array('size' => '37px','id' => 'ph32'));
        $loc2[8] = & $this->createElement('text', 'email', null, array('size' => '47px','id' => 'em12'));
        $loc2[9] = & $this->createElement('text', 'email_secondary', null, array('size' => '47px','id' => 'em22'));
        $loc2[10] = & $this->createElement('text', 'email_tertiary', null, array('size' => '47px','id' => 'em32'));
        $loc2[11] = & $this->createElement('select', 'im_service_id_1', null, $im_select);
        $loc2[12] = & $this->createElement('text', 'im_screenname_1', null, array('size' => '37px','id' => 'im12'));
        $loc2[13] = & $this->createElement('select', 'im_service_id_2', null, $im_select);
        $loc2[14] = & $this->createElement('text', 'im_screenname_2', null,array('size' => '37px','id' => 'im22'));
        $loc2[15] = & $this->createElement('select','im_service_id_3', null, $im_select);
        $loc2[16] = & $this->createElement('text', 'im_screenname_3', null, array('size' => '37px','id' => 'im32'));
        $loc2[17] = & $this->createElement('text', 'street', null, array('size' => '47px'));
        $loc2[18] = & $this->createElement('textarea', 'supplemental_address', null, array('cols' => '47'));
        $loc2[19] = & $this->createElement('text', 'city', null);
        $loc2[20] = & $this->createElement('text', 'postal_code', null);
        $loc2[21] = & $this->createElement('select', 'state_province_id', null, $state_select);
        $loc2[22] = & $this->createElement('select', 'country_id', null, $country_select);
        
        /* Entering location cabin 3 */
        $loc3[0] = & $this->createElement('select', 'context_id', null, $context_select);
        $loc3[1] = & $this->createElement('checkbox', 'is_primary', null);
        $loc3[2] = & $this->createElement('select', 'phone_type_1', null, $phone_select);
        $loc3[3] = & $this->createElement('text', 'phone_1', null, array('size' => '37px','id' => 'ph13'));
        $loc3[4] = & $this->createElement('select','phone_type_2', null, $phone_select);
        $loc3[5] = & $this->createElement('text', 'phone_2', null, array('size' => '37px','id' => 'ph23'));
        $loc3[6] = & $this->createElement('select', 'phone_type_3', null, $phone_select);
        $loc3[7] = & $this->createElement('text', 'phone_3', null, array('size' => '37px','id' => 'ph33'));
        $loc3[8] = & $this->createElement('text', 'email', null, array('size' => '47px','id' => 'em13'));
        $loc3[9] = & $this->createElement('text', 'email_secondary', null, array('size' => '47px','id' => 'em23'));
        $loc3[10] = & $this->createElement('text', 'email_tertiary', null, array('size' => '47px','id' => 'em33'));
        $loc3[11] = & $this->createElement('select', 'im_service_id_1', null, $im_select);
        $loc3[12] = & $this->createElement('text', 'im_screenname_1', null, array('size' => '37px','id' => 'im13'));
        $loc3[13] = & $this->createElement('select', 'im_service_id_2', null, $im_select);
        $loc3[14] = & $this->createElement('text', 'im_screenname_2', null,array('size' => '37px','id' => 'im23'));
        $loc3[15] = & $this->createElement('select','im_service_id_3', null, $im_select);
        $loc3[16] = & $this->createElement('text', 'im_screenname_3', null, array('size' => '37px','id' => 'im33'));
        $loc3[17] = & $this->createElement('text', 'street', null, array('size' => '47px'));
        $loc3[18] = & $this->createElement('textarea', 'supplemental_address', null, array('cols' => '47'));
        $loc3[19] = & $this->createElement('text', 'city', null);
        $loc3[20] = & $this->createElement('text', 'postal_code', null);
        $loc3[21] = & $this->createElement('select', 'state_province_id', null, $state_select);
        $loc3[22] = & $this->createElement('select', 'country_id', null, $country_select);
    
        for ($i = 1; $i <= 3; $i++) {    
            $this->addElement('link', 'exph02_'."{$i}", null, 'phone0_2_'."{$i}", '[+] another phone',
                              array('onclick' => "show('phone0_2_{$i}'); hide('expand_phone0_2_{$i}'); show('expand_phone0_3_{$i}'); return false;"));
            $this->addElement('link', 'hideph02_'."{$i}", null, 'phone0_2_'."{$i}", '[-] hide phone',
                              array('onclick' => "hide('phone0_2_{$i}'); hide('expand_phone0_3_{$i}'); show('expand_phone0_2_{$i}');hide('phone0_3_{$i}'); return false;"));
            $this->addElement('link', 'exph03_'."{$i}", null, 'phone0_3_'."{$i}", '[+] another phone',
                              array('onclick'=> "show('phone0_3_{$i}'); hide('expand_phone0_3_{$i}'); return false;"));
            $this->addElement('link', 'hideph03_'."{$i}", null, 'phone0_3_'."{$i}", '[-] hide phone',
                              array( 'onclick' => "hide('phone0_3_{$i}'); show('expand_phone0_3_{$i}'); return false;"));
            $this->addElement('link', 'exem02_'."{$i}", null, 'email0_2_'."{$i}", '[+] another email',
                              array('onclick' => "show('email0_2_{$i}'); hide('expand_email0_2_{$i}'); show('expand_email0_3_{$i}'); return false;"));
            $this->addElement('link','hideem02_'."{$i}", null, 'email0_2_'."{$i}", '[-] hide email',
                              array('onclick' => "hide('email0_2_{$i}'); hide('expand_email0_3_{$i}'); show('expand_email0_2_{$i}'); hide('email0_3_{$i}'); return false;"));
            $this->addElement('link', 'exem03_'."{$i}", null, 'email0_3_'."{$i}", '[+] another email',
                              array('onclick' => "show('email0_3_{$i}'); hide('expand_email0_3_{$i}'); return false;"));
            $this->addElement('link', 'hideem03_'."{$i}", null, 'email0_3_'."{$i}", '[-] hide email',
                              array('onclick' => "hide('email0_3_{$i}'); show('expand_email0_3_{$i}'); return false;"));
            $this->addElement('link', 'exim02_'."{$i}", null, 'IM0_2_'."{$i}",'[+] another instant message',
                              array('onclick' => "show('IM0_2_{$i}'); hide('expand_IM0_2_{$i}'); show('expand_IM0_3_{$i}'); return false;"));
            $this->addElement('link', 'hideim02_'."{$i}", null, 'IM0_2_'."{$i}", '[-] hide instant message',
                              array('onclick' => "hide('IM0_2_{$i}'); hide('expand_IM0_3_{$i}'); show('expand_IM0_2_{$i}'); hide('IM0_3_{$i}'); return false;"));
            $this->addElement('link', 'exim03_'."{$i}", null, 'IM0_3_'."{$i}", '[+] another instant message',
                              array('onclick' => "show('IM0_3_{$i}'); hide('expand_IM0_3_{$i}'); return false;"));
            $this->addElement('link', 'hideim03_'."{$i}", null, 'IM0_3_'."{$i}", '[-] hide instant message',
                              array('onclick' => "hide('IM0_3_{$i}'); show('expand_IM0_3_{$i}'); return false;"));
        }
        
        $this->addElement('link', 'exloc2', null, 'location2', '[+] another location',
                          array( 'onclick' => "hide('expand_loc2'); show('location2'); show('expand_loc3'); return false;"));
        $this->addElement('link', 'hideloc2', null, 'location2', '[-] hide location',
                          array('onclick' => "hide('location2'); show('expand_loc2'); hide('expand_loc3');return false;"));
        $this->addElement('link', 'exloc3', null, 'location2', '[+] another location ',
                          array('onclick' => "hide('expand_loc3'); show('location3'); return false;"));
        $this->addElement('link', 'hideloc3', null, 'location3', '[-] hide location',
                          array('onclick' => "hide('location3'); show('expand_loc3'); hide('expand_loc2');return false;"));
        
        $this->addGroup($loc1,'location1');
        $this->addGroup($loc2,'location2');
        $this->addGroup($loc3,'location3');
    
        /* End of locationas */
    
        $this->add('textarea', 'address_note', null, array('cols' => '82', 'id' => 'addnote'));    

        $this->addElement('link', 'exdemo', null, 'demographics', '[+] show demographics',
                          array('onclick' => "show('demographics'); hide('expand_demographics'); return false;"));
    
        $this->addElement('link', 'exnotes', null, 'notes', '[+] contact notes',
                          array('onclick' => "show('notes'); hide('expand_notes'); return false;"));
    
        $this->addElement('link', 'hidedemo', null,'demographics', '[-] hide demographics',
                          array('onclick' => "hide('demographics'); show('expand_demographics'); return false;"));
    
        $this->addElement('link', 'hidenotes', null, 'notes', '[-] hide contact notes',
                          array('onclick' => "hide('notes'); show('expand_notes'); return false;"));
    
        $this->addElement('hidden', 'mdyx', null, array('id' => 'mdy', 'value' => ''));
    
        if ($this->validate() && ($this->_mode == self::MODE_VIEW || self::MODE_CREATE)) {
            //$this->freeze();    
      
        } else {
            if ($this->_mode == self::MODE_VIEW || self::MODE_UPDATE) {
                $this->setDefaultValues();
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
        $defaults['location1[email]'] = 'dgg@blackhole.net';
        
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
        $this->applyFilter('first_name', 'trim');
        $this->addRule('first_name', t(' First name is a required feild.'), 'required', null, 'client');
        $this->applyFilter('last_name', 'trim');
        $this->addRule('last_name', t(' Last name is a required feild.'), 'required', null, 'client');
        $this->registerRule('check_date', 'callback', 'valid_date','CRM_Contacts_Form_CRUD');
        $this->addRule('birth_date', t(' Select a valid date.'), 'check_date');
    
        for ($i = 1; $i <= 3; $i++) { 
            $this->addGroupRule('location'."{$i}", array('email' => array( 
                                                                          array(t( 'Please enter valid email for location').$i.'.', 'email', null, 'client')),
                                                         'email_secondary' => array( 
                                                                                    array(t( ' Please enter valid secondary email for location').$i.'.', 'email', null, 'client')),
                                                         'email_tertiary' => array( 
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
        // print_r($_POST);
        // write your insert statements here
        // create a object for inserting data in contact table 
        $contact = new CRM_Contacts_DAO_Contact();
    
        $contact->domain_id = 1;
        $contact->contact_type = $_POST['contact_type'];
        $contact->sort_name = $_POST['sort_name'];
        $contact->source = $_POST['source'];
        $contact->preferred_communication_method = $_POST['preferred_communication_method'];
        $contact->do_not_phone = $_POST['do_not_phone'];
        $contact->do_not_email = $_POST['do_not_email'];
        $contact->do_not_mail = $_POST['do_not_mail'];
        $contact->hash = $_POST['hash'];
    
        if (!$contact->insert()) {
            die ("Cannot insert data in contact table.");
            // $contact->raiseError("Cannot insert","","continue");
        }
    
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
            $contact->delete($contact->id);
            die ("Cannot insert data in contact individual table.");
            //$contact->raiseError("Cannot insert data in contact individual table...");
        }
    
        // create a object for inserting data in contact location table 
    
        for ($lngi= 1; $lngi <= 3; $lngi++) {
            $varname = "contact_location" . $lngi;
            $varname1 = "location" . $lngi;
      
            if (strlen(trim($_POST[$varname1]['street'])) > 0  || strlen(trim($_POST[$varname1]['email'])) > 0 || strlen(trim($_POST[$varname1]['phone_1'])) > 0) {
	
                // create a object of contact location
                $$varname = new CRM_Contacts_DAO_Contact_Location();
	
                $$varname->contact_id = $contact->id;
                $$varname->context_id = $_POST[$varname1]['context_id'];
                $$varname->is_primary = $_POST[$varname1]['is_primary'];
                $$varname->street = $_POST[$varname1]['street'];
                $$varname->supplemental_address = $_POST[$varname1]['supplemental_address'];
                $$varname->address_note = $_POST[$varname1]['address_note'];
                $$varname->city = $_POST[$varname1]['city'];
                $$varname->county = $_POST[$varname1]['county'];
                $$varname->state_province_id = $_POST[$varname1]['state_province_id'];
                $$varname->postal_code = $_POST[$varname1]['postal_code'];
                $$varname->usps_adc = $_POST[$varname1]['usps_adc'];
                $$varname->country_id = $_POST[$varname1]['country_id'];
                $$varname->geo_code1 = $_POST[$varname1]['geo_code1'];
                $$varname->geo_code2 = $_POST[$varname1]['geo_code2'];
                $$varname->address_note = $_POST[$varname1]['address_note'];
                $$varname->email = $_POST[$varname1]['email'];
                $$varname->email_secondary = $_POST[$varname1]['email_secondary'];
                $$varname->email_tertiary = $_POST[$varname1]['email_tertiary'];    
                $$varname->phone_1 = $_POST[$varname1]['phone_1'];
                $$varname->phone_type_1 = $_POST[$varname1]['phone_type_1'];
                //    $$varname->mobile_provider_id_1 = $_POST[$varname1]['mobile_provider_id_1'];
                $$varname->mobile_provider_id_1 = 1;    
	
                $$varname->phone_2 = $_POST[$varname1]['phone_2'];
                $$varname->phone_type_2 = $_POST[$varname1]['phone_type_2'];
                //    $$varname->mobile_provider_id_2 = $_POST[$varname1]['mobile_provider_id_2'];
                $$varname->mobile_provider_id_2 = 2;
	
                $$varname->phone_3 = $_POST[$varname1]['phone_3'];
                $$varname->phone_type_3 = $_POST[$varname1]['phone_type_3'];
                // $$varname->mobile_provider_id_3 = $_POST[$varname1]['mobile_provider_id_3'];
                $$varname->mobile_provider_id_3 = 3;    
	
                $$varname->im_screenname_1 = $_POST[$varname1]['im_screenname_1'];
                $$varname->im_service_id_1 = $_POST[$varname1]['im_service_id_1'];
                $$varname->im_screenname_2 = $_POST[$varname1]['im_screenname_2'];
                $$varname->im_service_id_2 = $_POST[$varname1]['im_service_id_2'];
      
                if(!$$varname->insert()) {
                    //	  echo mysql_error();
                    $contact->delete($contact->id);
                    $contact_individual->delete( $contact_individual->id );
                    die ( "Cannot insert data in contact location table." );
                }
            }// end of if
        }//end of for
    }// end of function
  
}

?>

