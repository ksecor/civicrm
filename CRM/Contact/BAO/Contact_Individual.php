<?php

require_once 'CRM/Contact/DAO/Contact.php';

require_once 'CRM/Contact/DAO/Contact_Individual.php';

require_once 'CRM/Contact/DAO/Location.php';

require_once 'CRM/Contact/DAO/Address.php';

require_once 'CRM/Contact/DAO/Phone.php';

require_once 'CRM/Contact/DAO/IM.php';

require_once 'CRM/Contact/DAO/Email.php';

class CRM_Contact_BAO_Contact_Individual extends CRM_Contact_DAO_Contact_Individual 
{
    
    protected $_contactDAO;
    
    /**
     * This is a contructor of the class.
     */
    function __construct() 
    {
        parent::__construct();
        
        $this->_contactDAO = new CRM_Contact_DAO_Contact();
    }
    
    /**
     * This function sets the values in the form.
     */
    function setContactValues() 
    {
        $dbFields  = $this->_contactDAO->dbFields();
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

    function count($countWhat = false,$whereAddOnly = false) {
        $this->setContactValues();
        $this->joinAdd( $this->_contactDAO );
        return parent::count($countWhat, $whereAddOnly);
    }

    function find($get = false) 
    {
        $this->setContactValues();
        $this->joinAdd( );
        $this->whereAdd( );
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
