<?php

require_once 'CRM/Form.php';

/**
 * This class is used for building ORG.php. This class also has the actions that should be done when form is processed.
 */
class CRM_Contacts_Form_ORG extends CRM_Form 
{
    
    /**
     * This is the constructor of the class.
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);

    }
    
    /**
     * In this function we build the ORG.php. All the quickform componenets are defined in this function
     */
    function buildQuickForm()
    {

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
        
        // organization_name
        $this->addElement('text', 'organization_name', null);

        // legal_name
        $this->addElement('text', 'legal_name', null);

        // nick_name
        $this->addElement('text', 'nick_name', null);

        // primary_contact_id
        $this->addElement('text', 'primary_contact_id', null);
        
        // sic_code
        $this->addElement('text', 'sic_code', null);

        
        // checkboxes for DO NOT phone, email, mail
        $this->addElement('checkbox', 'do_not_phone', null);
        $this->addElement('checkbox', 'do_not_email', null);
        $this->addElement('checkbox', 'do_not_mail', null);
        
        // preferred communication method 
        $this->add('select','preferred_communication_method',null,$pcm_select);
        
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
        $defaults['organization_name'] = 'CRM Organization';
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
     * this function is used to add the rules for form
     */
    function addRules() 
    {
        $this->applyFilter('organization_name', 'trim');
        $this->addRule('organization_name', t(' Organization name is a required feild.'), 'required', null, 'client');
        $this->addRule('primary_contact_id', t(' Contact id is a required feild.'), 'required', null, 'client');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'numeric', null, 'client');
        $this->registerRule('check_contactid', 'callback', 'valid_contact','CRM_Contacts_Form_ORG');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'check_contactid');
        
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
        
        // create a object for inserting data in contact organization table 
        $contact_organization = new CRM_Contacts_DAO_Contact_Organization();
        $contact_organization->contact_id = $contact->id;
        $contact_organization->organization_name = $_POST['organization_name'];
        $contact_organization->legal_name = $_POST['legal_name'];
        $contact_organization->nick_name = $_POST['nick_name'];
        $contact_organization->primary_contact_id = $_POST['primary_contact_id'];
        $contact_organization->sic_code = $_POST['sic_code'];
     
        if(!$contact_organization->insert()) {
            $contact->delete($contact->id);
            die ("Cannot insert data in contact organization table.");
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
            $contact_organization->delete( $contact_organization->id );
            die ( "Cannot insert data in contact location table." );
        }
        
    }// end of function
    
}

?>
