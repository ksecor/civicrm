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
        

        $this->addElement('submit','change_list_view', 'go');
        $this->addElement('submit','gotopage', 'go');
        $this->addElement('submit','do_action', 'go');
        
        /* Page top links */
        $this->addElement('link', 'export', null, 'linkheader', 'Export');
        /* $this->addElement('link', 'first', null, 'linkheader', '<< First &nbsp;&nbsp;');
        $this->addElement('link', 'previous', null, 'linkheader', '< Previous');
        $this->addElement('link', 'page_serial', null, 'linkheader', '&nbsp;&nbsp;&nbsp; (1-25 of 233) &nbsp;&nbsp;&nbsp;');
        $this->addElement('link', 'next', null, 'linkheader', '> Next &nbsp;&nbsp');
        $this->addElement('link', 'previouspage', null, 'linkheader', '&raquo;&nbsp;Previous &nbsp');
        */

        
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
        
        /* Implementing the record generating mortar CODE UNDER REVISION*/
      
        // start of code added by manish  
        $pager_list = new CRM_Contacts_List_Contacts();
         
        $arr = $pager_list->list_contact();
        //print_r($arr);

        $rows_per_page = count($arr[1]);

        $this->addElement('text', 'pager', $arr[0]);
        $lng_row_count = count($arr[1]);
        $this->addElement('text', 'row_no', $lng_row_count);
        
        // end of code added by manish
        
        /* for ($i = 0; $i < $rows_per_page; $i++) {
            $records[$i]['name'] = "name" . "{$i}";
            $records[$i]['email'] = "email" . "{$i}";
            $records[$i]['phone'] = "phone" . "{$i}";
            $records[$i]['address'] = "address" . "{$i}";
            $records[$i]['city'] = "city" . "{$i}";
            $records[$i]['state_prov'] = "SP" . "{$i}"; 
        }
        
        for ($i = 0; $i < $rows_per_page; $i++) {
            $records[$i]['name'] = $arr[1][$i][0];
            $records[$i]['email'] = $arr[1][$i][1]; 
            $records[$i]['phone'] = $arr[1][$i][2];
            $records[$i]['address'] = $arr[1][$i][3];
            $records[$i]['city'] = $arr[1][$i][4];
            $records[$i]['state_prov'] = $arr[1][$i][5];
        } 
*/
        /*for ($i = 0; $i < $rows_per_page; $i++) {
           $record_group[$i][0] =& $this->createElement('link','name_'."{$i}", null, "datagrid", $records[$i]['name']);
           $record_group[$i][1] =& $this->createElement('link','email_'."{$i}" ,null, "datagrid", $records[$i]['email']);
           $record_group[$i][2] =& $this->createElement('link','phone_'."{$i}", null, "datagrid", $records[$i]['phone']);
           $record_group[$i][3] =& $this->createElement('link','address_'."{$i}", null, "datagrid", $records[$i]['address']);
           $record_group[$i][4] =& $this->createElement('link','city_'."{$i}", null, "datagrid", $records[$i]['city']);
           $record_group[$i][5] =& $this->createElement('link','state_province_'."{$i}", null, "datagrid", $records[$i]['state_prov']);
           $record_group[$i][6] =& $this->createElement('checkbox','checkrecord_'."{$i}", null, null);
       }
        
        for ($i = 0; $i < $rows_per_page; $i++) {
            $name_link_group[$i] =& $this->createElement('link','name_'."{$i}", null, "datagrid", $arr[$i]['name']);
            $email_link_group[$i] =& $this->createElement('link','email_'."{$i}" ,null, "datagrid", $records[$i]['email']);
            $phone_link_group[$i] =& $this->createElement('link','phone_'."{$i}", null, "datagrid", $records[$i]['phone']);
            $address_link_group[$i] =& $this->createElement('link','address_'."{$i}", null, "datagrid", $records[$i]['address']);
            //$city_link_group[$i] =& $this->createElement('link','city_'."{$i}", null, "datagrid", $records[$i]['city']);
            //$state_link_group[$i] =& $this->createElement('link','state_province_'."{$i}", null, "datagrid", $records[$i]['state_prov']);
            
            $city_link_group[$i] =& $this->createElement('static','city_'."{$i}", null,$records[$i]['city']);
            $state_link_group[$i] =& $this->createElement('static','state_province_'."{$i}", null,$records[$i]['state_prov']);
            
            $checkbox_group[$i] =& $this->createElement('checkbox','checkrecord_'."{$i}", null, null);
        }*/


        //        for ($i = 0; $i <= $rows_per_page; $i++) {
        
        // print_r($arr[1]);

        // start of code added by manish
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

        // end of code added by manish
            //}
        
        $this->addGroup($name_link_group,'name_link_group');
        $this->addGroup($email_link_group,'email_link_group');
        $this->addGroup($phone_link_group,'phone_link_group');
        $this->addGroup($address_link_group,'address_link_group');
        $this->addGroup($city_link_group,'city_link_group');
        $this->addGroup($state_link_group,'state_link_group');
        $this->addGroup($checkbox_group,'checkbox_group');
        
        /* $this->_elements[] =& $records;
       $this->_elementIndex['records_list'] = end(array_keys($this->_elements));
        */
        
        /* End of mortar */
        
        /************************************   End of all DHTML elements ******************************/
        /************************************   End of all DHTML elements ******************************/
        /************************************   End of all DHTML elements ******************************/
        
        if ($this->validate() && ($this->_mode == self::MODE_VIEW || self::MODE_CREATE)) {
            $this->freeze();    
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