<?php

require_once 'CRM/Contacts/DAO/Contact.php';

require_once 'CRM/Contacts/DAO/Contact_Individual.php';

require_once 'CRM/Contacts/DAO/Contact_Location.php';

class CRM_Contacts_BAO_Contact_Individual extends CRM_Contacts_DAO_Contact_Individual 
{
    
    protected $_contactDAO;
    
    /**
     * This is a contructor of the class.
     */
    function __construct() 
    {
        parent::__construct();
        
        $this->_contactDAO = new CRM_Contacts_DAO_Contact();
    }
    
    /**
     * This function sets the values in the form.
     */
    function setContactValues() 
    {
        //    log_message("============= add contact 9  =============");    
        //echo "============= add contact 9  =============<br>";
        $dbFields  = $this->_contactDAO->dbFields();
        //    print_r($dbFields);
        foreach ($dbFields as $fieldName => $dontCare) {
            $this->_contactDAO->$fieldName = isset($this->$fieldName) ? $this->$fieldName : null;
        }
    }
    
    function fillContactValues() 
    {
        $dbFields  = $this->_contactDAO->dbFields();
        $tableName = $this->_contactDAO->tableName();
        
        foreach ($dbFields as $fieldName => $dontCare) {
            $selectFieldName = $tableName . '_' . $fieldName;
            $this->_contactDAO->$fieldName = isset($this->$selectFieldName) ? $this->$selectFieldName : null;
        }
    }
    
    function getContactValues() 
    {
        $dbFields  = $this->_contactDAO->dbFields();
        
        foreach ($dbFields as $fieldName => $dontCare) {
            $this->$fieldName = $this->_contactDAO->$fieldName;
        }
    }
    
    function insertContact() 
    {
        $this->setContactValues();
        
        $this->_contactDAO->insert();
        
        /* above insertion triggers setting the contact_id */
        $this->contact_id = $this->_contactDAO->id;
    }
    
    function insert() 
    {
        /**
         * first insert a contact record
         **/
        $this->insertContact();
        
        parent::insert();
    }
    
    function find($get = false) 
    {
        //log_message("============= add contact 8  =============");    
        //echo "============= add contact 8  =============<br>";
        
        $this->setContactValues();
        $this->joinAdd( $this->_contactDAO );
        $this->selectAdd();
        $this->selectAs( $this, '%s' );
        $this->selectAs( $this->_contactDAO, $this->_contactDAO->getTableName() . '_%s' );
        parent::find($get);
        
        if ($get) {
            $this->fillContactValues();
        }
        
    }
    
    function fetch() 
    {
        $result = parent::fetch();
        if ($result) {
            $this->fillContactValues();
        }
        return $result;
    }
    
}

?>
