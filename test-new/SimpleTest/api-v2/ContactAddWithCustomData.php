<?php

require_once 'api/v2/Contact.php';

class TestOfContactAddWithCustomDataAPIV2 extends CiviUnitTestCase 
{
    function testCreateIndividualwithAll() 
    {
        // Create contact
        $params = array('first_name'    => 'abc7',
                        'last_name'     => 'xyz7', 
                        'contact_type'  => 'Individual',
                        'phone'         => '999999',
                        'phone_type'    => 'Phone',
                        'email'         => 'man7@yahoo.com',
                        'do_not_trade'  => 1,
                        'preferred_communication_method' => array(
                                                                  '2' => 1,
                                                                  '3' => 1,
                                                                  '4' => 1,
                                                                  ),
                        'custom_1'      => 'Env',
                        'custom_3'      => 'Information for custom field of type alphanumeric - text'
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
                
        
        // Get the contact values
        $retrieve = array( 'contact_id' => $contact['contact_id'],
                           'return.first_name' => 1,
                           'return.last_name'  => 1,
                           'return.phone'      => 1,
                           'return.email'      => 1,
                           'return.custom_1'   => 1,
                           'return.custom_3'   => 1
                           );
        $getContact = civicrm_contact_get( $retrieve );
                
        $this->assertEqual( $getContact['first_name'], $params['first_name'] );
        $this->assertEqual( $getContact['last_name'],  $params['last_name']  );
        $this->assertEqual( $getContact['custom_1'],   $params['custom_1']   );
        $this->assertEqual( $getContact['custom_5'],   $params['custom_5']   );
    }
}