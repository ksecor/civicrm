<?php

require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Contact/DAO/Location.php';
require_once 'CRM/Contact/DAO/Address.php';
require_once 'CRM/Contact/DAO/Phone.php';
require_once 'CRM/Contact/DAO/Email.php';

/**
 * rare case where because of inheritance etc, we actually store a reference
 * to the dao object rather than inherit from it
 */

class CRM_Contact_BAO_Contact extends CRM_Contact_DAO_Contact 
{
 
    function __construct()
    {
        parent::__construct();
    }
    
  function find1($get = false)
  {
    //$this->selectAs($this, '%s');
    
    // select rows
    $this->selectAdd();
    $this->selectAs($this->_contact_DAO, $this->_contact_DAO->getTableName() . '_%s');
    $this->selectAs($this->_location_DAO, $this->_location_DAO->getTableName() . '_%s');
    $this->selectAs($this->_email_DAO, $this->_email_DAO->getTableName() . '_%s');
        // 
    $this->joinAdd($this->_location_DAO);
    $this->joinAdd($this->_email_DAO);

    parent::find($get);
  }
    

  function getSearchRows($offset, $rowCount, $sort)
  {

    //
    // create the DAO's
    // all trash code... will clean it up in next commit... --- yvb
    //
    $location_DAO = new CRM_Contact_DAO_Location();
    $address_DAO = new CRM_Contact_DAO_Address();
    $email_DAO = new CRM_Contact_DAO_Email();
    $phone_DAO = new CRM_Contact_DAO_Phone();


    // we need to run the loop thru the num rows with offset in mind.
    $rows = array();

    // clear the select query
    // $this->selectAdd();
    //    $this->selectAdd('id', 'sort_name');
    //    $this->selectAs($this, $this->getTableName() . '_%s');
    //    $this->selectAs(array('id', 'sort_name'), $this->getTableName() . '_%s');
    //    $this->joinAdd($location_DAO);
    //    $this->limit($offset, $rowCount);
    //    $this->orderBy($sort->orderBy());

$query_string = <<<QS
SELECT crm_contact.id as crm_contact_id, crm_contact.sort_name as crm_contact_sort_name,
       crm_address.street_address as crm_address_street_address, crm_address.city as crm_address_city,
       crm_state_province.name as crm_state_province_name,
       crm_email.email as crm_email_email,
       crm_phone.phone as crm_phone_phone
FROM crm_contact, crm_location, crm_address, crm_phone, crm_email, crm_state_province
WHERE crm_contact.id = crm_location.contact_id AND
      crm_location.id = crm_address.location_id AND
      crm_location.id = crm_phone.location_id AND
      crm_location.id = crm_email.location_id AND
      crm_address.state_province_id = crm_state_province.id
QS;

 $query_string .= " ORDER BY " . $sort->orderBy(); 
 $query_string .= " LIMIT $offset, $rowCount ";

 CRM_Error::debug_var("query_string", $query_string); 

 // parent::find();

 $this->query($query_string);
	
 while($this->fetch())
   {
     $row = array();
     $row['contact_id'] = $this->crm_contact_id;
     $row['sort_name'] = $this->crm_contact_sort_name;
     $row['email'] = $this->crm_email_email;
     $row['phone'] = $this->crm_phone_phone;
     $row['street_address'] = $this->crm_address_street_address;
     $row['city'] = $this->crm_address_city;
     $row['state'] = $this->crm_state_province_name;
     $row['edit']  = 'index.php?q=/crm/contact/edit/'.$this->crm_contact_id;
     $rows[] = $row;
     CRM_Error::debug_var("row", $row);
   }
 return $rows;
  }



  function fetch() 
  {

    CRM_Error::le_method();

    $result = parent::fetch();

    CRM_Error::debug_var("result", $result);

    if ($result) {
      // $this->fillContactValues();
    }

    CRM_Error::ll_method();

    return $result;
  }
  
}

?>
