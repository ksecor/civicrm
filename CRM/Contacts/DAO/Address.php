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
                                        'street'               => array(self::TYPE_STRING),
                                        'supplemental_address' => array(self::TYPE_TEXT),
                                        'city'                 => array(self::TYPE_STRING),
                                        'county'               => array(self::TYPE_STRING),
                                        'state_province_id'    => array(self::TYPE_INT),
                                        'postal_code'          => array(self::TYPE_STRING),
                                        'usps_adc'             => array(self::TYPE_STRING),
                                        'country_id'           => array(self::TYPE_INT),
                                        'geo_code_1'           => array(self::TYPE_STRING),
                                        'geo_code_2'           => array(self::TYPE_STRING),
                                        'address_note'         => array(self::TYPE_STRING),
                                        'timezone'                => array(self::TYPE_STRING)
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
