<?php

require_once 'CRM/Contact/DAO/LocationBase.php';

/**
 * This is a dataobject class for Contact Location table.
 */
class CRM_Contact_DAO_Location extends CRM_Contact_DAO_ContactBase 
{
  
    /**
     * boolean operator
     * @var boolean
     */
    public $is_primary;
    
    /**
     * @param int location_type_id : foreign key from crm_location_type
     */
    
    public $location_type_id ;
    
    /**
     * This the constructor of the class
     */
    
    function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * This function is used to create the array of the feilds from Contact Location table.
     * @return array array contains the feilds of the table
     */
    function dbFields() 
    {
        static $fields;
        if ($fields === null) {
            $fields = array_merge(
                                  parent::dbFields(),
                                  array(
                                        'is_primary'        => array(CRM_Type::T_BOOLEAN),
                                        'location_type_id'  => array(CRM_Type::T_INT),
                                        ) // end of array
                                  );
        }
        return $fields;
    } // end of method dbFields
    
    function links() {
        static $links;
        if ($links === null) {
            $links = array_merge(parent::links(),
                                 array('location_type_id'  => 'crm_location_type:id'
                                       )
                                 );
        }
        return $links;
    } // end of method links()
    
} // end of class CRM_Contact_DAO_Contact_Location

?>
