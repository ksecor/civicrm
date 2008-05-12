<?php

require_once 'api/crm.php';

class TestOfGetContactHierachical extends UnitTestCase  
{ 
 
     
    function setUp()  
    { 
    } 
 
    function tearDown()  
    { 
    } 
 
    function testGetContactFlat() 
    { 
        $params = array( 'id' => 102 );
        $returnProperties = array('home_URL'               => 1, 
                                  'image_URL'              => 1, 
                                  'legal_identifier'       => 1, 
                                  'external_identifier'    => 1,
                                  'contact_type'           => 1,
                                  'sort_name'              => 1,
                                  'display_name'           => 1,
                                  'nick_name'              => 1, 
                                  'first_name'             => 1, 
                                  'middle_name'            => 1, 
                                  'last_name'              => 1, 
                                  'individual_prefix'      => 1, 
                                  'individual_suffix'      => 1,
                                  'birth_date'             => 1,
                                  'gender'                 => 1,
//                                   'custom_1'               => 1,
//                                   'custom_2'               => 1,
//                                   'custom_3'               => 1,
//                                   'custom_4'               => 1,
//                                   'custom_5'               => 1,
//                                   'custom_6'               => 1,
//                                   'custom_7'               => 1,
//                                   'custom_8'               => 1,                                  
                                  'location'   => array( 
                                                        'Home' => array (
                                                                         'location_type'  => 1,
                                                                         'street_address' => 1,
                                                                         'city'           => 1,
                                                                         'state_province' => 1,
                                                                         'country'        => 1,
                                                                         'postal_code'    => 1,
                                                                         'phone-Phone'    => 1,
                                                                         'phone-Mobile'   => 1,
                                                                         'phone-Fax'      => 1,
                                                                         'phone-1'        => 1,
                                                                         'phone-2'        => 1,
                                                                         'phone-3'        => 1,
                                                                         'im-1'           => 1,
                                                                         'im-2'           => 1,
                                                                         'im-3'           => 1,
                                                                         'email-1'        => 1,
                                                                         'email-2'        => 1,
                                                                         'email-3'        => 1,
                                                                         ),
                                                        '2' => array ( 
                                                                      'location_type'  => 1,
                                                                      'street_address' => 1, 
                                                                      'city'           => 1, 
                                                                      'state_province' => 1, 
                                                                      'country'        => 1,
                                                                      'postal_code'    => 1, 
                                                                      'phone-Phone'    => 1,
                                                                      'phone-Mobile'   => 1,
                                                                      'phone-1'        => 1,
                                                                      'phone-2'        => 1,
                                                                      'phone-3'        => 1,
                                                                      'im-1'           => 1,
                                                                      'im-2'           => 1,
                                                                      'im-3'           => 1,
                                                                      'email-1'        => 1,
                                                                      'email-2'        => 1,
                                                                      'email-3'        => 1,
                                                                      ) 
                                                        ),
                                  );
        
        list( $contacts, $options ) = crm_contact_search( $params, $returnProperties );
        CRM_Core_Error::debug( 'q', $contacts );
    } 
    
} 


