<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

/**
 * This is a dataobject class for Contact Location table.
 */
class CRM_Contacts_DAO_Contact_Location extends CRM_Contacts_DAO_ContactBase 
{
  
  public $context_id;

  /**
   * boolean operator
   * @var boolean
   */
  public $is_primary;

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

  /**
   * @var string
   */
  public $email;
  public $email_secondary;
  public $email_tertiary;

  public $phone_1;
  public $phone_type_1;
  public $mobile_provider_id_1;

  public $phone_2;
  public $phone_type_2;
  public $mobile_provider_id_2;

  public $phone_3;
  public $phone_type_3;
  public $mobile_provider_id_3;

  public $im_screenname_1;
  public $im_service_id_1;
  public $im_screenname_2;
  public $im_service_id_2;

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
				  'context_id'           => array(CRM_Type::T_INT),
				  'is_primary'           => array(CRM_Type::T_BOOLEAN),
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
				  'email'                => array(CRM_Type::T_STRING),
				  'email_secondary'      => array(CRM_Type::T_STRING),
				  'email_tertiary'       => array(CRM_Type::T_STRING),

				  'phone_1'              => array(CRM_Type::T_STRING),
				  'phone_type_1'         => array(CRM_Type::T_ENUM),
				  'mobile_provider_id_1' => array(CRM_Type::T_INT),

				  'phone_2'              => array(CRM_Type::T_STRING),
				  'phone_type_2'         => array(CRM_Type::T_ENUM),
				  'mobile_provider_id_2' => array(CRM_Type::T_INT),

				  'phone_3'              => array(CRM_Type::T_STRING),
				  'phone_type_3'         => array(CRM_Type::T_ENUM),
				  'mobile_provider_id_3' => array(CRM_Type::T_INT),

				  'im_screenname_1'      => array(CRM_Type::T_STRING),
				  'im_service_id_1'      => array(CRM_Type::T_INT),

				  'im_screenname_2'      => array(CRM_Type::T_STRING),
				  'im_service_id_2'      => array(CRM_Type::T_INT),

				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields

  function links() {
    static $links;
    if ( $links === null ) {
      $links = array_merge(parent::links(),
			   array('context_id'           => 'crm_context:id',
				 'state_province_id'    => 'crm_state_province:id',
				 'country_id'           => 'crm_country:id',
				 'mobile_provider_id_1' => 'crm_phone_mobile_provider:id',
				 'mobile_provider_id_2' => 'crm_phone_mobile_provider:id',
				 'mobile_provider_id_3' => 'crm_phone_mobile_provider:id',
				 'im_service_id_1'      => 'crm_im_service:id',
				 'im_service_id_2'      => 'crm_im_service:id',
				 )
			   );
    }
    return $links;
  } // end of method links()

} // end of class CRM_Contacts_DAO_Contact_Location

?>
