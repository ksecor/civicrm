<?php

require_once 'CRM/Contacts/DAO/ContactBase.php';

class CRM_Contacts_DAO_Contact_Location extends CRM_Contacts_DAO_ContactBase {

  public $context_id;
  public $is_primary;

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


  function __construct() {
    parent::__construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
			    parent::dbFields(),
			    array(
				  'context_id'           => array(self::TYPE_INT),
				  'is_primary'           => array(self::TYPE_BOOLEAN),
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
				  'email'                => array(self::TYPE_STRING),
				  'email_secondary'      => array(self::TYPE_STRING),
				  'email_tertiary'       => array(self::TYPE_STRING),

				  'phone_1'              => array(self::TYPE_STRING),
				  'phone_type_1'         => array(self::TYPE_ENUM),
				  'mobile_provider_id_1' => array(self::TYPE_INT),

				  'phone_2'              => array(self::TYPE_STRING),
				  'phone_type_2'         => array(self::TYPE_ENUM),
				  'mobile_provider_id_2' => array(self::TYPE_INT),

				  'phone_3'              => array(self::TYPE_STRING),
				  'phone_type_3'         => array(self::TYPE_ENUM),
				  'mobile_provider_id_3' => array(self::TYPE_INT),

				  'im_screenname_1'      => array(self::TYPE_STRING),
				  'im_service_id_1'      => array(self::TYPE_INT),

				  'im_screenname_2'      => array(self::TYPE_STRING),
				  'im_service_id_2'      => array(self::TYPE_INT),

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
