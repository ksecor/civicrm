<?php

require_once 'CRM/Form.php';

/**
 * This class is used for building QCADD.php. This class also has the actions that should be done when form is processed.
 */
class CRM_Contacts_Form_SEARCH extends CRM_Form 
{
    
    /**
     * This is the constructor of the class.
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);
    }
    
    /**
     * In this function we build the QCADD.php. All the quickform componenets are defined in this function
     */
    function buildQuickForm()
    {
        $date_options = array(
                              'language'  => 'en',
                              'format'    => 'dMY',
                              'minYear'   => 1900,
                              'maxYear'   => date('Y'),
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

        $this->addElement('text', 'name', 'Name:  ');
        //$this->addElement('text', 'last_name', 'Last Name: ');
        $this->addElement('text', 'email', 'Email:  ');
        $this->addElement('date', 'birth_date', 'Date of birth:  ', $date_options);
        $this->addElement('text', 'phone', 'Phone No / Mobile No.:  ');
        $this->addElement('text', 'street', 'Street Address:  ');
        $this->addElement('text', 'city', 'City:  ');
        $this->addElement('select', 'state_province_id', 'State / Province:  ', $state_select);
        $this->addElement('select', 'country_id', 'Country:  ', $country_select);
      
        $this->addDefaultButtons(array (
                                        array (
                                               'type'       => 'next', 
                                               'name'       => 'Search',
                                               'isDefault'  => true)));
        $this->registerRule('check_date', 'callback', 'valid_date','CRM_Contacts_Form_CRUD');

         if ($this->validate() && ($this->_mode == self::MODE_VIEW)) {
            $this->freeze();    
        }/* else {
            if ($this->_mode == self::MODE_VIEW || self::MODE_UPDATE) {
                $this->setDefaultValues();
            }
        }*/
    }//ENDING BUILD FORM 
  
    /**
     * this function sets the default values to the specified form element
     */
    /*function setDefaultValues() 
    {
        $defaults = array();
        $defaults['first_name'] = 'Dave';
        $defaults['last_name'] = 'Greenberg';
        $defaults['email'] = 'dgg@blackhole.net';
        
        $this->setDefaults($defaults);
    }*/
    
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
        //$this->applyFilter('first_name', 'trim');
        $this->applyFilter('_ALL_', 'trim');
        $this->addRule('name', t(' Enter valid searching criteria.'), 'required', null, 'client');
        // $this->applyFilter('last_name', 'trim');
        //$this->addRule('last_name', t(' Last name is a required field.'), 'required', null, 'client');
        $this->addRule('email', t(' Email address is reuired field.'), 'required', null, 'client');
        $this->addRule('email', t(' Enter valid email address.'), 'email', null, 'client');
        $this->addRule('birth_date', t(' Select a valid date.'), 'check_date');
    }
    
    /**
     * this function is called when the form is submitted.
     */

    /*
    function process() 
    { 
        $lng_error = 0; // this flag is set if there are any errors while inserting in database 
        
        // create a object for inserting data in contact table 
        $contact = new CRM_Contacts_DAO_Contact();
        
        $contact->domain_id = 1;
        $contact->contact_type = $_POST['contact_type'];
        $contact->sort_name = $_POST['first_name']." ".$_POST['last_name'];

        $contact->query('BEGIN'); //begin the database transaction
        
        if (!$contact->insert()) {
            $lng_error++;
        }
        
        if (!$lng_error) { //proceed if there are no errors
            // create a object for inserting data in contact individual table 
            $contact_individual = new CRM_Contacts_DAO_Contact_Individual();
            $contact_individual->contact_id = $contact->id;
            $contact_individual->first_name = $_POST['first_name'];
            $contact_individual->last_name = $_POST['last_name'];
                        
            if(!$contact_individual->insert()) {
                $lng_error++;
            }
        }
        
        if (!$lng_error) { //proceed if there are no errors
            // create a object of crm location
            $contact_location = new CRM_Contacts_DAO_Location();
            $contact_location->contact_id = $contact->id;
            $contact_location->is_primary = 1;
            $contact_location->location_type_id = 1;
            
            if(!$contact_location->insert()) {
                $lng_error++;
                break;
            }
        }
        
        if (!$lng_error) { //proceed if there are no errors        
            //create the object of crm email
            
            $contact_email = new CRM_Contacts_DAO_Email();
            
            $contact_email->is_primary = 1;
            $contact_email->location_id = $contact_location->id;
            $contact_email->email = $_POST['email'];
            
            if(!$contact_email->insert()) {
                $lng_error++;
                break;
            }    
            
            if (!$lng_error) { //proceed if there are no errors
                //create the object of crm phone
                
                $contact_phone = new CRM_Contacts_DAO_Phone();
                $contact_phone->is_primary = 1;
                $contact_phone->location_id = $contact_location->id;
                $contact_phone->phone = $_POST['phone'];
                
                if(!$contact_phone->insert()) {
                    $lng_error++;
                    break;
                }    
            }
            
        }// end of if block    
        
        // check if there are any errors while inserting in database
        if($lng_error){
            $contact->query('ROLLBACK');
            form_set_error('first_name', t('Database error, please try again.'));
            $error['first_name'] = t('Database error, please try again.');
        } else {
            $contact->query('COMMIT');
        }
        
    }//end of function
    */
}
?>
