<?php

require_once 'api/v2/Contact.php';
require_once 'api/v2/CustomGroup.php';


class TestOfContactAddWithCustomDataAPIV2 extends CiviUnitTestCase 
{
    private $_ids  = array( );
    private $_cgId = null;
    private $_cid  = null; 
    
    function setUp( )
    {
        $cg          = $this->createCustomGroup( );
        $this->_cgId = $cg['id'];
        $this->_ids  = $this->createCustomField( $cg );
    }
    
    function tearDown( )
    {
        $this->customFieldDelete( $this->_ids[0] );
        $this->customFieldDelete( $this->_ids[1] );
        $this->customGroupDelete( $this->_cgId );
        $this->contactDelete( $this->_cid );
    }
    
    function createCustomGroup( )
    {
        $params = array( 'domain_id'        => 1,
                         'title'            => 'Test Custom Group',
                         'class_name'       => 'Individual',
                         'weight'           => 4,
                         'collapse_display' => 1,
                         'style'            => 'Inline',
                         'is_active'        => 1
                         );
        $customGroup =& civicrm_custom_group_create($params);
        
        return $customGroup;
    }
    
    function createCustomField( &$cg )
    {
        $fieldParams = array('custom_group_id' => $cg['id'],
                             'label'           => 'Choose Color',
                             'html_type'       => 'Select',
                             'data_type'       => 'String',
                             'weight'          => 4,
                             'is_required'     => 1,
                             'is_searchable'   => 0,
                             'is_active'       => 1
                             );
        
        $optionGroup = array('domain_id' => 1,
                             'label'     => 'Choose Color',
                             'is_active' => 1
                             );
        
        $optionValue[] = array (
                                'label'     => 'Red',
                                'value'     => 'R',
                                'weight'    => 1,
                                'is_active' => 1
                                );
        $optionValue[] = array (
                                'label'     => 'Yellow',
                                'value'     => 'Y',
                                'weight'    => 2,
                                'is_active' => 1
                                );
        $optionValue[] = array (
                                'label'     => 'Green',
                                'value'     => 'G',
                                'weight'    => 3,
                                'is_active' => 1
                                );
        
        $params = array('fieldParams' => $fieldParams,
                        'optionGroup' => $optionGroup,
                        'optionValue' => $optionValue,
                        'customGroup' => $cg,
                        );
        
        
        
        //$params = array('fieldParams' => $fieldParams);
        $customField  =& civicrm_custom_field_create($params);
        
        $ids = array( );
        $ids[] = $customField['result']['customFieldId'];
        
        $fieldParams  = array('custom_group_id' => $cg['id'],
                              'label'           => 'Enter idea in once sentence',
                              'html_type'       => 'Text',
                              'data_type'       => 'String',
                              'default_value'   => 'xyz',
                              'weight'          => 4,
                              'is_required'     => 1,
                              'is_searchable'   => 0,
                              'is_active'       => 1
                              );
        
        $params       = array('fieldParams' => $fieldParams);
        $customField  =& civicrm_custom_field_create($params);
        $ids[] = $customField['result']['customFieldId'];
        return $ids;
    }
    
    function testCreateIndividualwithAll() 
    {
        // Create contact
        $params = array('first_name'    => 'abc' . time( ),
                        'last_name'     => 'xyz' . time( ), 
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
                        "custom_{$this->_ids[0]}"     => 'R',
                        "custom_{$this->_ids[1]}"     => 'Information for custom field of type alphanumeric - text'
                        );
        $contact    =& civicrm_contact_add($params);
        $this->_cid = $contact['contact_id'];
        $this->assertNotNull( $contact['contact_id'] );
                
        // Get the contact values
        $retrieve = array( 'contact_id'           => $contact['contact_id'],
                           'return.first_name'    => 1,
                           'return.last_name'     => 1,
                           'return.phone'         => 1,
                           'return.email'         => 1,
                           "return.custom_{$this->_ids[0]}"  => 1,
                           "return.custom_{$this->_ids[1]}"  => 1
                           );
        $getContact = civicrm_contact_get( $retrieve );
                
        $this->assertEqual( $getContact['first_name']   , $params['first_name']    );
        $this->assertEqual( $getContact['last_name']    , $params['last_name']     );
        $this->assertEqual( $getContact[ "custom_{$this->_ids[0]}"], $params["custom_{$this->_ids[0]}"]  );
        $this->assertEqual( $getContact[ "custom_{$this->_ids[1]}"], $params["custom_{$this->_ids[1]}"]  );
        
        // Update the contact
        $oParams                                = array( );
        $oParams["custom_{$this->_ids[1]}"]     = 'Different information for custom field of type alphanumeric - text' ;
        $oParams['email']                       = 'man7+custom@yahoo.com' ;
        $oParams['phone']                       = '123-4567' ;
        $oParams['contact_id']                  = $contact['contact_id'] ;
        $oParams['first_name']                  = 'Check';
        $oParams['contact_type']                = 'Individual';
        
        $updated    =& civicrm_contact_add($oParams);
                
        $retrieve = array( 'contact_id'           => $contact['contact_id'],
                           'return.first_name'    => 1,
                           'return.last_name'     => 1,
                           'return.phone'         => 1,
                           'return.email'         => 1,
                           "return.custom_{$this->_ids[0]}"  => 1,
                           "return.custom_{$this->_ids[1]}"  => 1
                           );
        $getContact = civicrm_contact_get( $retrieve );
                
        $this->assertEqual(    $getContact[ "custom_{$this->_ids[0]}"], $params["custom_{$this->_ids[0]}"]  );
        $this->assertEqual(    $getContact[ "custom_{$this->_ids[1]}"], $oParams["custom_{$this->_ids[1]}"]  );
        $this->assertNotEqual( $getContact[ "custom_{$this->_ids[1]}"], $params["custom_{$this->_ids[1]}"]  );
    }
}