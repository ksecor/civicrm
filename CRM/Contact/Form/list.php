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

       $contacts_select = array(
                               '~All Contacts~ ',
                               'Board Members',
                               'Top Donors', 
                               'Active Volunteers',
                               );
       $action_action = array(
                              '~For selected records~',
                              'Delete',
                              'Print',
                              'Export',
                              'Add to a group',
                              'Add to household',
                              'Define Relationship'
                              );
                              
    
       $this->addElement('select', 'contact_select', 'List:', $contacts_select);
       $this->addElement('select', 'action_select', 'Action:', $action_select); 
       $this->addElement('text', 'page_no', '&nbsp;Page',
                         array( 'size' => '1'));

       /*$this->addDefaultButtons( array(
                                        array ( 'type'      => 'submit'  ,
                                                'name'      => 'change_list_view' ,
                                                'label'     => 'Of 9'),
                                        array ( 'type'      => 'submit' ,
                                                'name'      => 'goToPage'  ),
                                        array ( 'type'       => 'submit',
                                                'name'      => 'do_action' ),
                                       )
                                 );*/

       $this->addElement('submit','change_list_view', 'go');
       $this->addElement('submit','gotopage', 'go');
       $this->addElement('submit','do_action', 'go');

       /* Page top links */
       $this->addElement('link', 'export', null, 'linkheader', 'Export');
       $this->addElement('link', 'first', null, 'linkheader', '<< First &nbsp;&nbsp;');
       $this->addElement('link', 'previous', null, 'linkheader', '< Previous');
       $this->addElement('link', 'page_serial', null, 'linkheader', '&nbsp;&nbsp;&nbsp; (1-25 of 233) &nbsp;&nbsp;&nbsp;');
       $this->addElement('link', 'next', null, 'linkheader', '> Next &nbsp;&nbsp');
       $this->addElement('link', 'previouspage', null, 'linkheader', '&raquo;&nbsp;Previous &nbsp');

       /* Table header links */
       $this->addElement('link', 'name', null, 'datagrid', 'Name');
       $this->addElement('link', 'email', null, 'datagrid', 'Email');
       $this->addElement('link', 'phone', null, 'datagrid', 'Phone');
       $this->addElement('link', 'address', null, 'datagrid', 'Address');
       $this->addElement('link', 'city', null, 'datagrid', 'City');
       $this->addElement('link', 'state_province', null, 'datagrid', 'State/Province');

       /* Page crumb links */
       $this->addElement('link', 'show_25', 'Rows per page:', 'pagecrumb', '25');
       $this->addElement('link', 'show_50', '|', 'pagecrumb', '50');
       $this->addElement('link', 'show_100', '|', 'pagecrumb', '100');
       $this->addElement('link', 'show_all', '|', 'pagecrumb', 'All');
       $this->addElement('link', 'select_all', '|', 'pagecrumb', 'All');
       $this->addElement('link', 'select_none', '|', 'pagecrumb', 'None');

       $rows_per_page = 10;
       
       (for $i = 0; $i < $record_count; $i++) {
           $record_group[$i] =&   $this->createElement('checkbox','checkrecord' . "{$i}" ,null,null);}

       
        /* End of all DHTML elements */
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
        
        $this->addGroupRule('location', array('email_1' => array( 
                                                               array(t( 'Please enter valid email for location'), 'email', null, 'client')),
                                              'email_2' => array( 
                                                               array(t( ' Please enter valid secondary email for location'), 'email', null, 'client')),
                                              'email_3' => array( 
                                                               array(t( ' Please enter valid tertiary email for location' ), 'email', null, 'client'))
                                              )
                            ); 
    }
    
    
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
    */
}

   function label_offset($str, $num, $dir)
    {
        $return_string = "";
        for ($i = 0; $i < $num; $i++) {
            $return_string = $return_string . " &nbsp;"; 
        }
        if ($dir > 0) {
        return $str . $return_string;
        }
        else {
        return $return_string . $str;
        }
    }
    
?>
