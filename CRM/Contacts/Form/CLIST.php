<?php

require_once 'CRM/Form.php';


/**
 * This class is used for building CLIST.php. This class also has the actions that should be done when form is processed.
 */
class CRM_Contacts_Form_CLIST extends CRM_Form 
{
    
    /**
     * This is the constructor of the class.
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);

    }
    
    /**
     * In this function we build the CLIST.php. All the quickform componenets are defined in this function
     */
    function buildQuickForm()
    {
        $contacts_select = array(
                                 '~All Contacts~ ',
                                 'Board Members',
                                 'Top Donors', 
                                 'Active Volunteers',
                                 );
        $action_select = array(
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
        $this->addElement('text', 'page_no', '&nbsp;Page', array('id' => 'page_no', 'size' => '2'));
        
        

        $this->addElement('submit','change_list_view', 'go');
        $this->addElement('submit','gotopage', 'go', array('onclick' => "return verify_page_number();"));
        $this->addElement('submit','do_action', 'go');
        
        /* Page top links */
        $this->addElement('link', 'export', null, 'linkheader', 'Export');

        /* Table header links */
        $this->addElement('link', 'name', null, 'datagrid', 'Name');
        $this->addElement('link', 'email', null, 'datagrid', 'Email');
        $this->addElement('link', 'phone', null, 'datagrid', 'Phone');
        $this->addElement('link', 'address', null, 'datagrid', 'Address');
        $this->addElement('link', 'city', null, 'datagrid', 'City');
        $this->addElement('link', 'state_province', null, 'datagrid', 'State/Province');

        /* Page crumb links */ 
        $this->addElement('link', 'select_all', 'Select:', 'pagecrumb', 'All', array('onclick' => "return select_checkboxes(true);"));
        $this->addElement('link', 'select_none', '|', 'pagecrumb', 'None', array('onclick' => "return select_checkboxes(false);"));

        /* Implementing the record limit emulation engine :: CODE IS UNDER STRICT REVISION */ 
        $http_base = $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'];
 
        if (strpos($_SERVER['QUERY_STRING'],"record_limit") === false) {
            $number_of_records_to_show = 25;
            $this->addElement('static', 'show_25', 'Rows per page:', '25');

          }
        else {
            $number_of_records_to_show = $_GET['record_limit'];
            $number_of_records_to_show > 0 ? $no_rec = "{$number_of_records_to_show}" : $no_rec = "All" ;
            $number_of_records_to_show == 25 ? $ln_label = "Rows per page:" : $ln_label = "|" ;
            $this->addElement('static', 'show_'.$no_rec, $ln_label, $no_rec);
        }
        
        if ($number_of_records_to_show != 25) {
            $this->addElement('link', 'show_25', 'Rows per page:', $http_base . "&record_limit=25" , '25'); 
        }
        if ($number_of_records_to_show != 50) {
            $this->addElement('link', 'show_50', '|', $http_base . "&record_limit=50" , '50'); 
        }
        if ($number_of_records_to_show != 100) {
            $this->addElement('link', 'show_100', '|', $http_base . "&record_limit=100" , '100'); 
        }
        if ($number_of_records_to_show != -1) {
            $this->addElement('link', 'show_all', '|', $http_base . "&record_limit=-1" , 'All');
        }
        /* ----- emulation engine ----- */


        /* Implementing the record generating mortar */

        $pager_list = new CRM_Contacts_List_Contacts();
        $arr = $pager_list->list_contact();
        $rows_per_page = count($arr[1]);
        $this->addElement('text', 'pager', $arr[0]);
        $lng_row_count = count($arr[1]);
        $this->addElement('text', 'row_count', $lng_row_count);
        $this->addElement('hidden', 'hidden_row_count', $lng_row_count);
        $i = 0;
        foreach ($arr[1] as $key_arr => $value_arr) {
            
            //$this->addElement('link','name',null,"datagrid",$value_arr);
            
            $name_link_group[$i] =& $this->createElement('link','name_'."{$i}", null, "datagrid", $value_arr['first_name']." ".$value_arr['last_name'] );
            $email_link_group[$i] =& $this->createElement('link','email_'."{$i}" ,null, "datagrid",$value_arr['email'] );
            
            //$phone_link_group[$i] =& $this->createElement('link','gender_'."{$i}", null, "datagrid", $value);
            //$address_link_group[$i] =& $this->createElement('link','address_'."{$i}", null, "datagrid", $records[1][$i]['address']);
            //$city_link_group[$i] =& $this->createElement('link','city_'."{$i}", null, "datagrid", $records[$i]['city']);
            //$state_link_group[$i] =& $this->createElement('link','state_province_'."{$i}", null, "datagrid", $records[$i]['state_prov']);
            //$city_link_group[$i] =& $this->createElement('static','city_'."{$i}", null,$records[$i]['city']);
            //$state_link_group[$i] =& $this->createElement('static','state_province_'."{$i}", null,$records[$i]['state_prov']);
            
            $checkbox_group[$i] =& $this->createElement('checkbox','checkrecord_'."{$i}", null, null);
            $i++;
        }

        $this->addGroup($name_link_group,'name_link_group');
        $this->addGroup($email_link_group,'email_link_group');
        $this->addGroup($phone_link_group,'phone_link_group');
        $this->addGroup($address_link_group,'address_link_group');
        $this->addGroup($city_link_group,'city_link_group');
        $this->addGroup($state_link_group,'state_link_group');
        $this->addGroup($checkbox_group,'checkbox_group');
        
        /* End of mortar */
        
        /************************************   End of all DHTML elements ******************************/
        /************************************   End of all DHTML elements ******************************/
        /************************************   End of all DHTML elements ******************************/
        

        if ($_SERVER['REQUEST_METHOD']=='POST') {
            /* print "llk";
           if (isset($_POST['change_list_view'])) { print 'head';}
           if (isset($_POST['gotopage'])) { print 'body';}
           if (isset($_POST['do_action'])) { print 'crumb';}
            */
        }

             if ($this->_mode == self::MODE_VIEW) {
            if ($this->validate()) {
                 $this->freeze();
            }    
        } else {
            if ($this->_mode == self::MODE_VIEW || self::MODE_UPDATE) {
                $this->setDefaultValues();
            }
        }
    }// ##############################################################################  ENDING BUILD FORM 
    
    
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
     * this function is used to add the rules for form
     */
    function addRules() 
    {
        /*$this->applyFilter('household_name', 'trim');
        $this->addRule('household_name', t(' Household name is a required feild.'), 'required', null, 'client');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'numeric', null, 'client');
        $this->registerRule('check_contactid', 'callback', 'valid_contact','CRM_Contacts_Form_CLIST');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'check_contactid');
        $this->addRule('annual_income', t(' Enter valid annual income.'), 'numeric', null, 'client');
        $this->registerRule('check_income', 'callback', 'valid_income','CRM_Contacts_Form_CLIST');
        $this->addRule('annual_income', t(' Enter valid annual income.'), 'check_income');*/
        
        
    }
    
    
    /**
     * this function is called when the form is submitted.
     */
    function process() 
    { 
        
    }
    
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