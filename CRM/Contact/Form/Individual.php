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
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 1;
    
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

        switch ($this->_mode) {
        case self::MODE_ADD:
        case self::MODE_UPDATE:
            // print_r($_POST);
            $this->registerRule('check_date', 'callback', 'valid_date','CRM_Contact_Form_Individual');
            // $this->registerRule('check_date', 'callback', CRM_RULE::date(),'CRM_Contact_Form_Individual');

            $this->addRule('birth_date', t(' Select a valid date.'), 'check_date');
            
            for ($lng_i = 1; $lng_i <= 3; $lng_i++) { 
                for ($lng_j = 1; $lng_j <= 3; $lng_j++) { 
                    $str_message = "Please enter valid email ".$lng_j." for primary location";
                    if ($lng_i > 1) {
                        $str_message = "Please enter valid email ".$lng_j." for additional location ".($lng_i-1);
                    }
                    
                    $this->addRule('location['.$lng_i.'][email]['.$lng_j.'][email]', $str_message, 'email', null);                
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

        if ( $this->_mode == self::MODE_ADD || $this->_mode == self::MODE_UPDATE ) {
            $this->_addPostProcess();
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
        
        // prefix
        $this->addElement('select', 'prefix', null, CRM_SelectValues::$prefixName);

        $attributes =& $this->getFormAttributes('CRM_Contact_DAO_Individual');

        // first_name
        $this->add('text', 'first_name', 'First Name', $attributes['first_name'], true );
        
        // last_name
        $this->add('text', 'last_name', 'Last Name', $attributes['last_name'], true ); 
        
        // suffix
        $this->addElement('select', 'suffix', null, CRM_SelectValues::$suffixName);
        
        // greeting type
        $this->addElement('select', 'greeting_type', 'Greeting type :', CRM_SelectValues::$greeting);
        
        // job title
        $this->addElement('text', 'job_title', 'Job title :', $attributes['job_title'] );
        
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
        
        $location =& CRM_Contact_Form_Location::buildLocationBlock($this, self::LOCATION_BLOCKS, $showHideBlocks);

        /* End of locations */

        $this->add('textarea', 'address_note', 'Notes:', array('cols' => '82', 'maxlength' => 255));    
        

        $showHideBlocks->links( $this, 'demographics', '[+] show demographics' , '[-] hide demographics'  );
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
     * This function does all the processing of the form for New Contact Individual.
     * Depending upon the mode this function is used to insert or update the Individual
     * @access private
     */
    private function _addPostProcess() 
    { 

        $lng_contact_id = 0; // variable for crm_contact 'id'
        $str_error = ""; // error is recorded  if there are any errors while inserting in database

        // store the submitted values in an array
        $a_Values = $this->exportValues();

        // action is taken depending upon the mode
        if ($this->_mode) {
            $lng_contact_id = $_SESSION['id'];
            $a_Values['contact_id'] = $_SESSION['id'];
        }    

        $tempDB = new CRM_Contact_DAO_Contact( );
        $tempDB->query('BEGIN');

        $a_Values['contact_type'] = 'Individual';
        $contact = CRM_Contact_BAO_Contact::add( $a_Values );
        // need to check for error here and abort / rollback if error
        
        $a_Values['contact_id'] = $contact->id;
        
        $individual = CRM_Contact_BAO_Individual::add( $a_Values );
        // need to check for error here and abort / rollback if error

        for ($locationId= 1; $locationId <= self::LOCATION_BLOCKS; $locationId++) { // start of for loop for location
            $location = CRM_Contact_BAO_Location::add( $a_Values, $locationId );
        }

        $tempDB->query('COMMIT');

    }//end of function


}


    
?>