<?php
/*
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */


require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Contact/DAO/Household.php';

class CRM_Contact_BAO_Household extends CRM_Contact_DAO_Household 
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
    
    function find($get = false) 
    {
        
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
