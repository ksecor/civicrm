<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

class CRM_Contacts_DAO_LocationBase extends CRM_Contacts_DAO_ContactBase {
    
    /*
     * FK link to id in crm_location table
     * @var int
     */
    public $location_id;

    function __construct() {
        parent::__construct();
    }
    
    function links() {
        static $links = null;
        
        if ($links === null) {
            $links = array('location_id'  => 'crm_location:id');
        }
        return $links;
    }
    
    
    function dbFields() {
        static $fields;
        if ($fields === null) {
            $fields = array_merge(
                                  parent::dbFields(),
                                  array(
                                        'location_id'   => array(self::TYPE_INT, self::NOT_NULL),
                                        )
                                  );
        }
        return $fields;
    }
    
    
}

?>
