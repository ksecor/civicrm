<?php

require_once 'api/v2/Contact.php';
require_once 'api/v2/CustomGroup.php';


class TestOfContactAddWithCustomDataAPIV2 extends CiviUnitTestCase 
{

    function testCreateIndividualwithAll() 
    {
       
        //Creating Custom Data
        $params = array( 'domain_id'        => 1,
                         'title'            => 'Test_Group_1',
                         'name'             => 'test_group_1',
                         'class_name'       => 'Individual',
                         'weight'           => 4,
                         'collapse_display' => 1,
                         'style'            => 'Tab',
                         'is_active'        => 1
                         );
        $customGroup =& civicrm_custom_group_create($params);
       
        $fieldParams = array('custom_group_id' => $customGroup['id'],
                             'name'            => 'test_textfield1',
                             'label'           => 'Name1',
                             'html_type'       => 'Select',
                             'data_type'       => 'String',
                             'weight'          => 4,
                             'is_required'     => 1,
                             'is_searchable'   => 0,
                             'is_active'       => 1
                             );

        $optionGroup = array('domain_id' => 1,
                             'name'      => 'option_group1',
                             'label'     => 'option_group_label1',
                             'is_active' => 1
                             );
        
        $optionValue[] = array ('label'     => 'Label1',
                                'value'     => 'value1',
                                'name'      => 'Name1',
                                'weight'    => 1,
                                'is_active' => 1
                                );
        
        $params = array('fieldParams' => $fieldParams,
                        'optionGroup' => $optionGroup,
                        'optionValue' => $optionValue,
                        'customGroup' => $customGroup,
                        );
        
        
        
        //$params = array('fieldParams' => $fieldParams);
        $customField =& civicrm_custom_field_create($params);
        $id1 = $customField['result']['customFieldId'];

        $fieldParams = array('custom_group_id' => $customGroup['id'],
                             'name'            => 'test_textfield2',
                             'label'           => 'Name2',
                             'html_type'       => 'Text',
                             'data_type'       => 'String',
                             'default_value'   => 'xyz',
                             'weight'          => 4,
                             'is_required'     => 1,
                             'is_searchable'   => 0,
                             'is_active'       => 1
                             );
        
        $params = array('fieldParams' => $fieldParams);
        $customField =& civicrm_custom_field_create($params);
        $id2 = $customField['result']['customFieldId'];
    
        // Create contact
        $params = array('first_name'    => 'abc',
                        'last_name'     => 'xyz', 
                        'contact_type'  => 'Individual',
                        'phone'         => '999999',
                        'phone_type'    => 'Phone',
                        'email'         => 'man7@yahoo.com',
                        'prefix'        => 'Mr',
                        'suffix'        => 'VII',
                        'gender'        => 'Male',
                        'do_not_trade'  => 1,
                        'preferred_communication_method' => array(
                                                                  '2' => 1,
                                                                  '3' => 1,
                                                                  '4' => 1,
                                                                  ),
                        strval('custom_'.$id1)     => 'option_group1',
                        strval('custom_'.$id2)     => 'Information for custom field of type alphanumeric - text'
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        
        
        // Get the contact values
        $retrieve = array( 'contact_id'           => $contact['contact_id'],
                           'return.first_name'    => 1,
                           'return.last_name'     => 1,
                           'return.phone'         => 1,
                           'return.email'         => 1,
                           strval('return.custom_'.$id1)  => 1,
                           strval('return.custom_'.$id2)  => 1
                           );
        $getContact = civicrm_contact_get( $retrieve );
               
        $this->assertEqual( $getContact['first_name']   , $params['first_name']    );
        $this->assertEqual( $getContact['last_name']    , $params['last_name']     );
        $this->assertEqual( $getContact[ 'custom_'.$id1], $params['custom_'.$id1]  );
        $this->assertEqual( $getContact[ 'custom_'.$id2], $params['custom_'.$id2]  );


        $this->customFieldDelete( $id1 );
        $this->customFieldDelete( $id2 );
        $this->customGroupDelete( $customGroup['id'] );
        $this->contactDelete( $contact['contact_id'] );
    }
}