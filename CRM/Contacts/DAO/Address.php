<?php

require_once 'CRM/Contacts/DAO/LocationBase.php';

/**
 * This is a dataobject class for Contact Address table.
 */
class CRM_Contacts_DAO_Address extends CRM_Contacts_DAO_LocationBase 
{
    /**
     * @var string
     */
    public $street;
    public $supplemental_address;
    public $city;
    public $county;
    
    public $state_province_id;
    public $postal_code;
    public $usps_adc;
    public $country_id;
    
    public $geo_code1;
    public $geo_code2;
    public $address_note;
    public $timezone;
    
    /**
     * This the constructor of the class
     */
    
    function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * This function is used to create the array of the feilds from Contact Address table.
     * @return array array contains the feilds of the table
     */
    function dbFields() 
    {
        static $fields;
        if ($fields === null) {
            $fields = array_merge(
                                  parent::dbFields(),
                                  array(
                                        'street'               => array(CRM_Type::T_STRING),
                                        'supplemental_address' => array(CRM_Type::T_TEXT),
                                        'city'                 => array(CRM_Type::T_STRING),
                                        'county'               => array(CRM_Type::T_STRING),
                                        'state_province_id'    => array(CRM_Type::T_INT),
                                        'postal_code'          => array(CRM_Type::T_STRING),
                                        'usps_adc'             => array(CRM_Type::T_STRING),
                                        'country_id'           => array(CRM_Type::T_INT),
                                        'geo_code_1'           => array(CRM_Type::T_STRING),
                                        'geo_code_2'           => array(CRM_Type::T_STRING),
                                        'address_note'         => array(CRM_Type::T_STRING),
                                        'timezone'                => array(CRM_Type::T_STRING)
                                        ) // end of array
                                  );
        }
        return $fields;
    } // end of method dbFields
    
    function links() {
        static $links;
        if ( $links === null ) {
            $links = array_merge(parent::links(),
                                 array(
                                       'state_province_id'    => 'crm_state_province:id',
                                       'country_id'           => 'crm_country:id'
                                       )
                                 );
        }
        return $links;
    } // end of method links()

} // end of class CRM_Contacts_DAO_Contact_Address
?>
