<?php

require_once 'CRM/Contacts/DAO/LocationBase.php';

/**
 * This is a dataobject class for crm_email  table.
 */
class CRM_Contacts_DAO_Email extends CRM_Contacts_DAO_LocationBase 
{
    
    /**
     * boolean operator
     * @var boolean
     */
    public $is_primary;
    
    
    /**
     * @var string
     */
    
    public $email;

    /**
     * This the constructor of the class
     */
    
    function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * This function is used to create the array of the feilds from crm_email table.
     * @return array array contains the feilds of the table
     */
    function dbFields() 
    {
        static $fields;
        if ($fields === null) {
            $fields = array_merge(
                                  parent::dbFields(),
                                  array(
                                        'email'                => array(self::TYPE_STRING),
                                        ) // end of array
                                  );
        }
        return $fields;
    } // end of method dbFields
    
} // end of class CRM_Contacts_DAO_Contact_Email

?>
