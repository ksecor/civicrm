<?php

require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Contact/BAO/Base.php';

/**
 * rare case where because of inheritance etc, we actually store a reference
 * to the dao object rather than inherit from it
 */

class CRM_Contact_BAO_Contact extends CRM_Contact_DAO_Contact 
{
  
    private $_contact_DAO;
    private $_location_DAO;
    private $_email_DAO;
    
    function __construct()
    {
        parent::__construct();
        $this->_contact_DAO = new CRM_Contact_DAO_Contact();
        $this->_location_DAO = new CRM_Contact_DAO_Location();
    }
    
    function find($get = false) 
    {
        //$this->selectAs($this, '%s');

        // select rows
        $this->selectAs($this->_contact_DAO, $this->_contact_DAO->getTableName() . '_%s');
        $this->selectAs($this->_location_DAO, $this->_location_DAO->getTableName() . '_%s');

        // 
        $this->joinAdd($this->_location_DAO);


        parent::find($get);


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
