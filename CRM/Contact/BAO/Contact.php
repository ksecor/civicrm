<?php

require_once 'CRM/Contact/DAO/Contact.php';

require_once 'CRM/Contact/BAO/Base.php';

/**
 * rare case where because of inheritance etc, we actually store a reference
 * to the dao object rather than inherit from it
 */

class CRM_Contact_BAO_Contact extends CRM_Contact_DAO_Contact 
{
  
    protected $_contactDAO;
    
    function __construct()
    {
        parent::__construct();
        $this->_contactDAO = new CRM_Contact_DAO_Contact();
    }
    
    function find($get = false) 
    {
        $this->selectAdd();
        $this->selectAs($this, '%s');
        $this->selectAs($this->_contactDAO, $this->_contactDAO->getTableName() . '_%s');
        parent::find($get);
    }
    
    function fetch() 
    {
        $result = parent::fetch();
        if ($result) {
            // $this->fillContactValues();
        }
        return $result;
    }
  
}

?>
