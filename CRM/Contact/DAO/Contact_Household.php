<?php

require_once 'CRM/Contact/DAO/ContactBase.php';

class CRM_Contact_DAO_Contact_Household extends CRM_Contact_DAO_ContactBase 
{
    
    /**
     * name and other related things for the household
     * @var string
     */
    public $household_name;
    public $nick_name;
    
    /**
     * FK to who the primary contact for the organization is
     * @var int
     */
    public $primary_contact_id;
    
    function __construct() 
    {
        parent::__construct();
    }
    
    function links() 
    {
        static $links;
        if ($links === null) {
            $links = array_merge(parent::links(),
                                 array('primary_contact_id' => 'crm_contact:id'));
        }
        return $links;
    }
    
    function dbFields() 
    {
        static $fields;
        if ($fields === null) {
            $fields = array_merge(
                                  parent::dbFields(),
                                  array(
                                        'household_name'     => array(CRM_Type::T_STRING, null),
                                        'nick_name'          => array(CRM_Type::T_STRING, null),
                                        'primary_contact_id' => array(CRM_Type::T_INT, null)
                                        )
                                  );
        }
        return $fields;
    }
    
}

?>
