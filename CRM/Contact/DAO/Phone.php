<?php

require_once 'CRM/Contacts/DAO/LocationBase.php';

/**
 * This is a dataobject class for crm_location table.
 */
class CRM_Contacts_DAO_Phone extends CRM_Contacts_DAO_LocationBase 
{
    
    /**
     * boolean operator
     * @var boolean
     */
    public $is_primary;
    
    /**
     * @var string
     */

    public $phone;
    public $phone_type;
    public $mobile_provider_id;
    
    /**
     * This the constructor of the class
     */
    
    function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * This function is used to create the array of the feilds from crm_phone table.
     * @return array array contains the feilds of the table
     */
    function dbFields() 
    {
        static $fields;
        if ($fields === null) {
            $fields = array_merge(
                                  parent::dbFields(),
                                  array(
                                        'is_primary'         => array(CRM_Type::T_BOOLEAN),
                                        'phone'              => array(CRM_Type::T_STRING),
                                        'phone_type'         => array(CRM_Type::T_ENUM),
                                        'mobile_provider_id' => array(CRM_Type::T_INT)
                                        ) // end of array
                                  );
        }
        return $fields;
    } // end of method dbFields
    
    function links() {
        static $links;
        if ($links === null) {
            $links = array_merge(parent::links(),
                                 array(
                                       'mobile_provider_id' => 'crm_phone_mobile_provider:id'
                                       )
                                 );
        }
        return $links;
    } // end of method links()
    
} // end of class CRM_Contacts_DAO_Contact_Phone

?>
