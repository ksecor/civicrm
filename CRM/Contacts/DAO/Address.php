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
    public $street_address;
    public $street_number;
    public $street_number_suffix;
    public $street_predirectional;
    public $street_name;
    public $street_type;
    public $street_postdirectional;
    public $street_unit;
    public $street_unit_sort;
    public $supplemental_address_1;
    public $supplemental_address_2;
    public $supplemental_address_3;
    
    public $city;
    public $county_id;
    
    public $state_province_id;
    public $postal_code;
    public $postal_code_suffix;
    public $usps_adc;
    public $country_id;
    
    public $geo_coord_id;
    public $geo_code1;
    public $geo_code2;
    
    public $timezone;
    public $address_note;
    
    
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
                                        'street_address'          => array(CRM_Type::T_STRING),
                                        'street_number'           => array(CRM_Type::T_INT),
                                        'street_number_suffix'    => array(CRM_Type::T_STRING),
                                        'street_predirectional'   => array(CRM_Type::T_STRING),
                                        'street_name'             => array(CRM_Type::T_STRING),
                                        'street_type'             => array(CRM_Type::T_STRING),
                                        'street_postdirectional'  => array(CRM_Type::T_STRING),
                                        'street_unit'             => array(CRM_Type::T_STRING),
                                        'street_unit_sort'        => array(CRM_Type::T_INT),
                                        'supplemental_address _1' => array(CRM_Type::T_TEXT),
                                        'supplemental_address _2' => array(CRM_Type::T_TEXT),
                                        'supplemental_address _3' => array(CRM_Type::T_TEXT),
                                        'city'                    => array(CRM_Type::T_STRING),
                                        'county_id'               => array(CRM_Type::T_INT),
                                        'state_province_id'       => array(CRM_Type::T_INT),
                                        'postal_code'             => array(CRM_Type::T_STRING),
                                        'postal_code_suffix'      => array(CRM_Type::T_STRING),
                                        'usps_adc'                => array(CRM_Type::T_STRING),
                                        'country_id'              => array(CRM_Type::T_INT),
                                        'geo_coord_id'            => array(CRM_Type::T_INT),
                                        'geo_code_1'              => array(CRM_Type::T_STRING),
                                        'geo_code_2'              => array(CRM_Type::T_STRING),
                                        'address_note'            => array(CRM_Type::T_STRING),
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
                                       'country_id'           => 'crm_country:id',
                                       'county_id'            => 'crm_county:id',
                                       'geo_coord_id'         => 'crm_geo_coord:id'
                                       )
                                 );
        }
        return $links;
    } // end of method links()
    
} // end of class CRM_Contacts_DAO_Contact_Address
?>
