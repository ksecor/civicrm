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
 * This class is used for building the Organization form. This class also has the actions that should be done when form is processed.
 * 
 * This class extends the variables and methods provided by the class CRM_Form which by default extends the HTML_QuickForm_SinglePage.
 * @package CRM.  
 */
class CRM_Contact_Form_Organization extends CRM_Form 
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
     * In this function we build the Organization.php. All the quickform components are defined in this function.
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
            
            // get the values from household table
            $contact_organization = new CRM_Contact_DAO_Organization;

            $contact_organization->get("contact_id",$lng_contact_id);
            
            $defaults['organization_name'] = $contact_organization->organization_name;
            $defaults['legal_name'] =$contact_organization->legal_name;
            $defaults['nick_name'] =$contact_organization->nick_name;
            $defaults['sic_code'] = $contact_organization->sic_code;
            $defaults['primary_contact_id'] = $contact_organization->primary_contact_id;

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
                   $a_Location[$contact_location->id]['supplemental_address_2'] = $$varaddress->supplemental_address_2;
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
                   $a_Location[$contact_location->id]['email'][$lng_email] = $$var_email->email;
                   $lng_email++;
               }
               
               // get data from phone table
               $var_phone = "contact_phone".$lng_key;
               $$var_phone = new CRM_Contact_DAO_Phone();
                      
               $$var_phone->location_id = $contact_location->id;
               $$var_phone->find();

               $lng_phone = 1;
               while ($$var_phone->fetch()) {
                   $a_Location[$contact_location->id]['phone'][$lng_phone] = $$var_phone->phone;
                   $a_Location[$contact_location->id]['phone_type'][$lng_phone] = $$var_phone->phone_type;
                   $lng_phone++;
               }

               // get data from im table
               $var_im = "contact_im" . $lng_i;
               $$var_im = new CRM_Contact_DAO_IM();
               
               $$var_im->location_id = $contact_location->id;
               $$var_im->find();

               $lng_im = 1;
               while ($$var_im->fetch()) {
                   $a_Location[$contact_location->id]['im_service_id'][$lng_im] = $$var_im->im_provider_id;
                   $a_Location[$contact_location->id]['im_screenname'][$lng_im] = $$var_im->im_screenname;
                   $lng_im++;
               }

            }// end of outer while loop

             //print_r($a_Location);                        
            if (is_array($a_Location)) { 
                $lng_count = 1;
                foreach ($a_Location as $lng_key => $var_value) {
                    $defaults['location'][$lng_count]['location_type_id'] = $var_value['location_type_id'];
                    $defaults['location'][$lng_count]['is_primary'] = $var_value['is_primary'];

                    $defaults['location'][$lng_count]['address']['street_address'] = $var_value['street_address'];
                    $defaults['location'][$lng_count]['address']['supplemental_address_1'] = $var_value['supplemental_address_1'];
                    $defaults['location'][$lng_count]['address']['supplemental_address_2'] = $var_value['supplemental_address_2'];
                    $defaults['location'][$lng_count]['address']['city'] = $var_value['city'];
                    $defaults['location'][$lng_count]['address']['state_province_id'] = $var_value['state_province_id'];
                    $defaults['location'][$lng_count]['address']['postal_code'] = $var_value['postal_code'];
                    $defaults['location'][$lng_count]['address']['country_id'] = $var_value['country_id'];
                    
                    for ($lng_i = 1; $lng_i <=3; $lng_i++) {
                        $defaults['location'][$lng_count]['email'][$lng_i]['email'] = $var_value['email'][$lng_i];
                        $defaults['location'][$lng_count]['phone'][$lng_i]['phone'] = $var_value['phone'][$lng_i];
                        $defaults['location'][$lng_count]['phone'][$lng_i]['phone_type_id'] = $var_value['phone_type'][$lng_i];
                        $defaults['location'][$lng_count]['im'][$lng_i]['service_id'] = $var_value['im_service_id'][$lng_i];
                        $defaults['location'][$lng_count]['im'][$lng_i]['screenname'] = $var_value['im_screenname'][$lng_i];
                    }

                    $lng_count++ ;
                }
            }
        }

        // set all elements with values from the database.
        return $defaults;
    }
    
    /**
     * This function is used to validate the contact.
     * 
     * This is a custom validation function used to check if the entered primary contact value exists.
     * 
     * @access public
     * @param int $value This is basically primary contact values entered
     * @internal this is interger value
     * @return Boolean value true or false depending on whether the primary contact exits in database.
     * @see addRules( )     
     */
    function valid_contact($value) 
    {
        $contact = new CRM_Contact_DAO_Contact();
        if ($contact->get('id', $value)) {
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
     * @see valid_contact 
     */
    function addRules( ) 
    {
        
        $this->applyFilter('_ALL_', 'trim');

        switch ($this->_mode) {
        case self::MODE_ADD:
        case self::MODE_UPDATE:

            $this->addRule('organization_name', t(' Organization name is a required feild.'), 'required', null, 'client');
            $this->addRule('primary_contact_id', t(' Contact id is a required feild.'), 'required', null, 'client');
            $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'numeric', null, 'client');
            $this->registerRule('check_contactid', 'callback', 'valid_contact','CRM_Contact_Form_Organization');
            $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'check_contactid');

            for ($lng_i = 1; $lng_i <= 3; $lng_i++) { 
                for ($lng_j = 1; $lng_j <= 3; $lng_j++) { 
                    $str_message = "Please enter valid email ".$lng_j." for primary location";
                    if ($lng_i > 1) {
                        $str_message = "Please enter valid email ".$lng_j." for additional location ".($lng_i-1);
                    }
                    
                    $this->addRule('location['.$lng_i.'][email]['.$lng_j.'][email]', $str_message, 'email', null, 'client');                
                }
            }
            
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
        case self::MODE_UPDATE:
            $this->_addPostProcess();
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_DELETE:
            break;            
        case self::MODE_SEARCH:
            break;            
        }    
    }
    
    /**
     * This function provides the HTML form elements for the add/view/update operation of Organization
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

        // organization_name
        $this->addElement('text', 'organization_name', 'Organization Name:', array('maxlength' => 64));

        // legal_name
        $this->addElement('text', 'legal_name', 'Legal Name:', array('maxlength' => 64));

        // nick_name
        $this->addElement('text', 'nick_name', 'Nick Name:', array('maxlength' => 64));

        // primary_contact_id
        $this->addElement('text', 'primary_contact_id', 'Primary Contact Id:', array('maxlength' => 10));
        
        // sic_code
        $this->addElement('text', 'sic_code', 'SIC Code:', array('maxlength' => 8));
       
        // add the communications block
        CRM_Contact_Form_Contact::buildCommunicationBlock($this);

        $showHideBlocks = new CRM_ShowHideBlocks( array('name'              => 1,
                                                        'commPrefs'         => 1,),
                                                  array('notes'        => 1, ));

        /* Entering the compact location engine */         
        $location =& CRM_Contact_Form_Location::buildLocationBlock($this, 2, $showHideBlocks);
        /* End of locations */

        $this->add('textarea', 'address_note', 'Notes:', array('cols' => '82', 'maxlength' => 255));    

        $showHideBlocks->links( $this, 'notes'       , '[+] show contact notes', '[-] hide contact notes' );
        $showHideBlocks->addToTemplate( );

        if ($this->_mode != self::MODE_VIEW) {
            
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
    }
    
    
    /**
     * This function does all the processing of the form for New Organization
     * Depending upon the mode this function is used to insert or update the Organization
     * @access private
     */
    private function _addPostProcess() 
    { 
        $lng_contact_id = 0; // variable for crm_contact 'id'
        $str_error = ""; // error is recorded  if there are any errors while inserting in database

        // action is taken depending upon the mode
        switch ($this->_mode) {
        case self::MODE_UPDATE:
            $lng_contact_id = $_SESSION['id'];
            break;
        case self::MODE_VIEW:
            break;
        }    
        
        // store the submitted values in an array
        $a_Values = $this->exportValues();
        // print_r($a_Values);
            
        // create a object for inserting data in contact table 
        $contact = new CRM_Contact_DAO_Contact();
        
        $contact->domain_id = 1;
        $contact->contact_type = 'Organization';
        // $contact->legal_id = '';
        //$contact->external_id = '';
        $contact->sort_name = $a_Values['first_name']." ".$a_Values['last_name'];
        //$contact->home_URL = '';
        //$contact->image_URL = '';
        //$contact->source = '';
        $contact->preferred_communication_method = $a_Values['preferred_communication_method'];
        $a_privacy = $a_Values['privacy'];
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
            
            // create a object for inserting data in contact organization table 
            $contact_organization = new CRM_Contact_DAO_Organization();

            $contact_organization->contact_id = $contact->id;
            $contact_organization->organization_name = $a_Values['organization_name'];
            $contact_organization->legal_name = $a_Values['legal_name'];
            $contact_organization->nick_name = $a_Values['nick_name'];
            $contact_organization->sic_code = $a_Values['sic_code'];
            $contact_organization->primary_contact_id = $a_Values['primary_contact_id'];

 
            if ($lng_contact_id) {
                // update the contact_organization for $lng_contact_id
                $contact_organization->whereAdd('contact_id = '.$lng_contact_id);
                if(!$contact_organization->update(DB_DATAOBJECT_WHEREADD_ONLY)) $str_error = mysql_error();
            } else {
                // insert in contact_organization
                if (!$contact_organization->insert()) $str_error = mysql_error();                
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

                if (strlen(trim($a_Values['location'][$lngi]['address']['street_address'])) > 0  || strlen(trim($a_Values['location'][$lngi]['email'][1]['email'])) > 0 || strlen(trim($a_Values['location'][$lngi]['phone'][1]['phone'])) > 0) {  // check for valid location entry
                    if (!strlen($str_error)) { //proceed if there are no errors
                        // create a object of crm location
                        $$varname = new CRM_Contact_DAO_Location();
                        $$varname->contact_id = $contact->id;

                        $$varname->location_type_id = $a_Values['location'][$lngi]['location_type_id'];
                        if ($lngi == 1){
                            if (!strlen($a_Values['location'][2]['is_primary']) && !strlen($a_Values['location'][3]['is_primary'])){
                                $$varname->is_primary = 1;
                            }
                        } else {
                            $$varname->is_primary = $a_Values['location'][$lngi]['is_primary'];
                        }
 
                        if (strlen($a_location_array[$lngi])) {
                            // update the crm_location for $lng_contact_id
                            $$varname->whereAdd('contact_id = '.$lng_contact_id);
                            $$varname->whereAdd('id = '.$a_location_array[$lngi]);
                            if(!$$varname->update(DB_DATAOBJECT_WHEREADD_ONLY)) $str_error = mysql_error();
                            
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
                        if (strlen(trim($a_Values['location'][$lngi]['address']['street_address'])) > 0) { // check for valid address entry
                            //create the object of crm address
                            $varaddress = "contact_address".$lngi;
                            $$varaddress = new CRM_Contact_DAO_Address();
                                     
                            $$varaddress->location_id = $$varname->id;
                            $$varaddress->street_address = $a_Values['location'][$lngi]['address']['street_address'];
                            //$$varaddress->street_number = '';
                            //$$varaddress->street_number_suffix = '';
                            //$$varaddress->street_number_predirectional = '';
                            //$$varaddress->street_name = '';
                            //$$varaddress->street_type = '';
                            //$$varaddress->street_number_postdirectional = '';
                            $$varaddress->supplemental_address_1 = $a_Values['location'][$lngi]['address']['supplemental_address_1'];
                            $$varaddress->supplemental_address_2 = $a_Values['location'][$lngi]['address']['supplemental_address_2'];
                            //$$varaddress->supplemental_address_3 = '';
                            $$varaddress->city = $a_Values['location'][$lngi]['address']['city'];
                            // $$varaddress->county_id = $a_Location['county_id'];
                            $$varaddress->county_id = 1;
                            $$varaddress->state_province_id = $a_Values['location'][$lngi]['address']['state_province_id'];
                            $$varaddress->postal_code = $a_Values['location'][$lngi]['address']['postal_code'];
                            //$$varaddress->postal_code_suffix = '';
                            //$$varaddress->usps_adc = '';
                            $$varaddress->country_id = $a_Values['location'][$lngi]['address']['country_id'];
                            $$varaddress->geo_coord_id = 1;
                            $$varaddress->geo_code1 = $a_Values['location'][$lngi]['address']['geo_code1'];
                            $$varaddress->geo_code2 = $a_Values['location'][$lngi]['address']['geo_code2'];
                            $$varaddress->timezone = $a_Values['location'][$lngi]['address']['timezone'];
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

                        for ($lng_i= 1; $lng_i <= 3; $lng_i++) { // start of for email

                            if (strlen(trim($a_Values['location'][$lngi]['email'][$lng_i]['email'])) > 0) { // check for valid email entry
                                //create the object of crm email
                                $var_email = "contact_email".$lng_i;
                                $$var_email = new CRM_Contact_DAO_Email();
                               
                                $$var_email->location_id = $$varname->id;
                                $$var_email->email = $a_Values['location'][$lngi]['email'][$lng_i]['email'];
                                         
                                if($lng_i == 1) { //make first email entered primary
                                    $$var_email->is_primary = 1;
                                } else {
                                    $$var_email->is_primary = 0;
                                }
                                     
                                if (strlen($a_email[$lng_i])) {
                                    // update the crm_email for $lng_contact_id and id ($a_email[$lng_i])

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

                        for ($lng_i= 1; $lng_i <= 3; $lng_i++) { // start of phone for loop

                            if (strlen(trim($a_Values['location'][$lngi]['phone'][$lng_i]['phone'])) > 0) { //check for valid phone entry
                                //create the object of crm phone
                                $var_phone = "contact_phone".$lng_i;
                                $$var_phone = new CRM_Contact_DAO_Phone();
                      
                                $$var_phone->location_id = $$varname->id;
                                $$var_phone->phone = $a_Values['location'][$lngi]['phone'][$lng_i]['phone'];
                                $$var_phone->phone_type = $a_Values['location'][$lngi]['phone'][$lng_i]['phone_type_id'];
                                
                                if ($lng_i == 1) { //make first phone entered primary
                                    $$var_phone->is_primary = 1;
                                } else {
                                    $$var_phone->is_primary = 0;
                                }
                                
                                $$var_phone->mobile_provider_id = 1;
                                
                                if (strlen($a_phone[$lng_i])) {
                                    // update the crm_phone for $lng_location_id and phone id ($a_phone[$lng_i])
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

                        for ($lng_i= 1; $lng_i <= 3; $lng_i++) { // start of im for loop
                           
                            if (strlen(trim($a_Values['location'][$lngi]['im'][$lng_i]['screenname'])) > 0) { //check for valid im entry
                                //create the object of crm im
                                $var_im = "contact_im" . $lng_i;
                                $$var_im = new CRM_Contact_DAO_IM();
                                         
                                $$var_im->location_id = $$varname->id;
                                $$var_im->im_screenname = $a_Values['location'][$lngi]['im'][$lng_i]['screenname'];
                                $$var_im->im_provider_id = $a_Values['location'][$lngi]['im'][$lng_i]['service_id'];
                                
                                if ($lng_i == 1) { //make first im entered primary
                                    $$var_im->is_primary = 1;
                                } else {
                                    $$var_im->is_primary = 0;
                                }                               

                                //if ($lng_contact_id) {
                                if (strlen($a_im[$lng_i])) {
                                    // update the crm_im for $lng_location_id and im id ($a_im[$lng_i])
                                    
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
            form_set_error('organization_name', t($str_error));
        } else {
            $contact->query('COMMIT');
            form_set_error('organization_name', t('Contact Organization has been saved successfully.'));
        }
        
    }//end of function

}
    
?>