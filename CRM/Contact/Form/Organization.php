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
 * This class is used for building the Add Organization form. This class also has the actions that should be done when form is processed.
 * 
 * This class extends the variables and methods provided by the class CRM_Form which by default extends the HTML_QuickForm_SinglePage.
 * @package CRM.  
 */
class CRM_Contact_Form_Organization extends CRM_Form 
{
    
    /**
     * This is the constructor of the class.
     *
     * This function calls the constructor for the parent class CRM_Form and implements a switch strategy to identify the mode type 
     * of form to be displayed based on the values contained in the mode parameter.
     *
     * @access public
     * @param string $name Contains name of the form.
     * @param string $state Used to access the current state of the form.
     * @param constant $mode Used to access the type of the form. Default value is MODE_NONE.
     * @return None
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        
        if ($mode == self::MODE_ADD) {
            $name = "Add";
            // $name = "Individual";
        }

        parent::__construct($name, $state, $mode);
    }
    


    /**
     * In this function we build the Organization.php. All the quickform components are defined in this function.
     * 
     * This function implements a switch strategy using the form public variable $mode to identify the type of mode rendered by the 
     * form. The types of mode could be either Contact CREATE, VIEW, UPDATE, DELETE or SEARCH. 
     * CREATE and SEARCH forms can be implemented in a mini format using mode MODE_ADD_MINI and MODE_SEARCH_MINI. 
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
            $this->addElement('text', 'mode', self::MODE_ADD);
            $this->_buildAddForm();
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            

        } // end of switch
    }


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
        $defaults['organization_name'] = 'CRM Organization';
        $this->setDefaults($defaults);
        break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            
        }
    }



    /**
     * This function is used to validate the contact.
     * 
     * The function implements a server side validation of the entered contact_id. This value must have an entry in the contact_id
     * field of the crm_contact table for the validation to succed.
     *
     * @param string $value The value of the contact_id entered in the text field.
     * @return Boolean value true or false depending on whether the contact_id is valid or invalid. 
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
     * contact_id is set by registering the rule check_contactid which calls the valid_contact function for date validation.
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
        $this->applyFilter('organization_name', 'trim');
        $this->addRule('organization_name', t(' Organization name is a required feild.'), 'required', null, 'client');
        $this->addRule('primary_contact_id', t(' Contact id is a required feild.'), 'required', null, 'client');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'numeric', null, 'client');
        $this->registerRule('check_contactid', 'callback', 'valid_contact','CRM_Contact_Form_Organization');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'check_contactid');
        
        $this->addGroupRule('location', array('email_1' => array( 
                                                               array(t( 'Please enter valid email for location'), 'email', null, 'client')),
                                              'email_2' => array( 
                                                                         array(t( ' Please enter valid secondary email for location'), 'email', null, 'client')),
                                              'email_3' => array( 
                                                                        array(t( ' Please enter valid tertiary email for location' ), 'email', null, 'client'))
                                              )
                            ); 

        break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
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
     * @uses CRM_Contact_Form_Location::buildLocationBlock($this, 3) Used to obtain the HTML element for plugging the Location block. 
     * @uses CRM_Contact_Form_Contact::buildCommunicationBlock($this) Used to obtain elements for plugging the Communication preferences.
     * @see buildQuickForm()         
     * @see _buildMiniAddForm()
     * 
     */
    function _buildAddForm()
    {

        $form_name = $this->getName();

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
 
        // Implementing the communication preferences block
        CRM_Contact_Form_Contact::buildCommunicatioBlock($this);

        // Implementing the location block :
        $location = CRM_Contact_Form_Location::buildLocationBlock($this, 1);
        $this->addGroup($location[1],'location');
        /* End of locations */

        $java_script = "<script type = \"text/javascript\">
                        frm = document." . $form_name .";</script>";

        $this->addElement('static', 'my_script', $java_script);

        if ($this->validate() && ($this->_mode == self::MODE_VIEW || self::MODE_ADD)) {
            //$this->freeze();    
            
        } else {
            if ($this->_mode == self::MODE_VIEW || self::MODE_UPDATE) {
                $this->setDefaultValues();
            }
        }
 
    }//ENDING BUILD FORM 


    /**
     * this function is called when the form is submitted.
     */
    /* function process() 
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
        $contact_household = new CRM_Contacts_DAO_Contact_Organization();
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
    */

}

?>