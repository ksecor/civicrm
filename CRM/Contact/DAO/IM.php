<?php

require_once 'CRM/Contact/DAO/LocationBase.php';

/**
 * This is a dataobject class for crm_im table.
 */
class CRM_Contact_DAO_IM extends CRM_Contact_DAO_LocationBase 
{

    /**
     * boolean operator
     * @var boolean
     */
    public $is_primary;
    
    /**
     * @var string
     */
    
    public $im_screenname;
    public $im_service_id;
    
    /**
   * This the constructor of the class
   */
    
    function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * This function is used to create the array of the feilds from crm_im table.
     * @return array array contains the feilds of the table
     */
    function dbFields() 
    {
        static $fields;
        if ($fields === null) {
            $fields = array_merge(
                                  parent::dbFields(),
                                  array(
                                        'is_primary'       => array(CRM_Type::T_BOOLEAN),
                                        'im_screenname'    => array(CRM_Type::T_STRING),
                                        'im_service_id'    => array(CRM_Type::T_INT)
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
                                       'im_service_id'      => 'crm_im_service:id',
                                       )
                                 );
        }
        return $links;
    } // end of method links()
    
} // end of class CRM_Contact_DAO_Contact_IM

?>
