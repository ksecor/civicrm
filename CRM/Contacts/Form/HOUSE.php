<?php

require_once 'CRM/Form.php';

/**
 * This class is used for building CRUD.php. This class also has the actions that should be done when form is processed.
 */
class CRM_Contacts_Form_HOUSE extends CRM_Form 
{
    
    /**
     * This is the constructor of the class.
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);

    }
    
    /**
     * In this function we build the HOUSE.php. All the quickform componenets are defined in this function
     */
    function buildQuickForm()
    {

        $pcm_select = array(
                            ' '      => '-no preference-',
                            'Phone'  => 'by phone', 
                            'Email'  => 'by email', 
                            'Postal' => 'by postal email',
                            );

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

        
        $this->addDefaultButtons(array(1 => array ('next', 'Save', true),
                                       2 => array ('reset' , 'Reset', false),
                                       3 => array ('cancel', 'Cancel', false)
                                       )
                                 );
        
        // household_name
        $this->addElement('text', 'household_name', 'Household Name:');

        // nick_name
        $this->addElement('text', 'nick_name',"Nick Name:");

        // primary_contact_id
        $this->addElement('text', 'primary_contact_id', "Primary Contact Id:");
        
        // annual_income
        $this->addElement('text', 'annual_income', "Annual Income:");

   // checkboxes for DO NOT phone, email, mail
        $this->addElement('checkbox', 'do_not_phone', 'Privacy:', 'Do not call');
        $this->addElement('checkbox', 'do_not_email', null, 'Do not contact by email');
        $this->addElement('checkbox', 'do_not_mail', null, 'Do not contact by postal mail');
        
        // preferred communication method 
        $this->add('select','preferred_communication_method',"Preferred communication method:",$pcm_select);
        
 
      $loc1[0] = & $this->createElement('select', 'context_id', null, $context_select);
        $loc1[1] = & $this->createElement('checkbox', 'is_primary', 'Primary location for this contact');
        $loc1[2] = & $this->createElement('select', 'phone_type_1', null, $phone_select);
        $loc1[3] = & $this->createElement('text', 'phone_1', 'Preferred Phone:', array('size' => '37px'));
        $loc1[4] = & $this->createElement('select','phone_type_2', null, $phone_select);
        $loc1[5] = & $this->createElement('text', 'phone_2', 'Other Phone:', array('size' => '37px'));
        $loc1[6] = & $this->createElement('select', 'phone_type_3', null, $phone_select);
        $loc1[7] = & $this->createElement('text', 'phone_3',  'Other Phone:', array('size' => '37px'));
        $loc1[8] = & $this->createElement('text', 'email', 'Email:', array('size' => '47px'));
        $loc1[9] = & $this->createElement('text', 'email_secondary', 'Other Email:', array('size' => '47px'));
        $loc1[10] = & $this->createElement('text', 'email_tertiary', 'Other Email:', array('size' => '47px'));
        $loc1[11] = & $this->createElement('select', 'im_service_id_1', 'Instant Message:', $im_select);
        $loc1[12] = & $this->createElement('text', 'im_screenname_1', null, array('size' => '37px'));
        $loc1[13] = & $this->createElement('select', 'im_service_id_2',  'Instant Message:', $im_select);
        $loc1[14] = & $this->createElement('text', 'im_screenname_2', null,array('size' => '37px'));
        $loc1[15] = & $this->createElement('select','im_service_id_3',  'Instant Message:', $im_select);
        $loc1[16] = & $this->createElement('text', 'im_screenname_3', null, array('size' => '37px'));
        $loc1[17] = & $this->createElement('text', 'street', 'Street Address:', array('size' => '47px'));
        $loc1[18] = & $this->createElement('textarea', 'supplemental_address', 'Address:', array('cols' => '47'));
        $loc1[19] = & $this->createElement('text', 'city', 'City:');
        $loc1[20] = & $this->createElement('text', 'postal_code', 'Zip / Postal Code:');
        $loc1[21] = & $this->createElement('select', 'state_province_id', 'State / Province:', $state_select);
        $loc1[22] = & $this->createElement('select', 'country_id', 'Country:', $country_select);
        
 
        for ($i = 1; $i <= 1; $i++) {    
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

        $this->addGroup($loc1,'location');

        /* End of locationas */
        
        
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
        $defaults['household_name'] = 'CRM Family';
        $this->setDefaults($defaults);
    }
    
    /**
     * this function is used to validate the contact, check if contact is present.
     */
    function valid_contact($value) 
    {
        $contact = new CRM_Contacts_DAO_Contact();
        if ($contact->get('id', $value)) {
            return true;
        } else {
            return false;
        }    
    }

    /**
     * this function is used to validate the annual income.
     */
    function valid_income($value) 
    {
        if ($value >= 0) {
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
        $this->applyFilter('household_name', 'trim');
        $this->addRule('household_name', t(' Household name is a required feild.'), 'required', null, 'client');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'numeric', null, 'client');
        $this->registerRule('check_contactid', 'callback', 'valid_contact','CRM_Contacts_Form_HOUSE');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'check_contactid');
        $this->addRule('annual_income', t(' Enter valid annual income.'), 'numeric', null, 'client');
        $this->registerRule('check_income', 'callback', 'valid_income','CRM_Contacts_Form_HOUSE');
        $this->addRule('annual_income', t(' Enter valid annual income.'), 'check_income');
        
        $this->addGroupRule('location', array('email' => array( 
                                                               array(t( 'Please enter valid email for location'), 'email', null, 'client')),
                                              'email_secondary' => array( 
                                                                         array(t( ' Please enter valid secondary email for location'), 'email', null, 'client')),
                                              'email_tertiary' => array( 
                                                                        array(t( ' Please enter valid tertiary email for location' ), 'email', null, 'client'))
                                              )
                            ); 
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
        $contact_household = new CRM_Contacts_DAO_Contact_Household();
        $contact_household->contact_id = $contact->id;
        $contact_household->household_name = $_POST['household_name'];
        $contact_household->nick_name = $_POST['nick_name'];
        $contact_household->primary_contact_id = $_POST['primary_contact_id'];
        $contact_household->phone_to_household = $_POST['phone_to_household'];
        $contact_household->email_to_household = $_POST['email_to_household'];
        $contact_household->postal_to_household = $_POST['postal_to_household'];
        $contact_household->annual_income = $_POST['annual_income'];
        
        
        if(!$contact_household->insert()) {
            $contact->delete($contact->id);
            die ("Cannot insert data in contact household table.");
        }
        
        // create a object for inserting data in contact location table 
        
        $varname = "contact_location";
        $varname1 = "location";
        
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
            $contact_household->delete( $contact_household->id );
            die ( "Cannot insert data in contact location table." );
        }
        
    }// end of function
    
}

?>
