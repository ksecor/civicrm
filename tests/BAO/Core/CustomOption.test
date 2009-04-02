<?php

require_once 'CiviTestCase.php';
require_once 'Contact.php';

class BAO_Core_CustomOption extends CiviTestCase 
{
    function get_info( ) 
    {
        return array(
                     'name'        => 'CustomOption BAOs',
                     'description' => 'Test all Core_BAO_CustomOption methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();
    }
    
    /**
     * retrieve() method - Retrieves a single option_value record as an array when the option_value.id is passed
     */
    function testRetrieve( )
    {
        // Retrieve the option_group_id for sample custom field 'Marital Status'
        $params = array( );
        $params = array( 'label'   => 'Marital Status');
        $field  = array( );
        
        require_once 'CRM/Core/BAO/CustomField.php';
        CRM_Core_BAO_CustomField::retrieve( $params, $field );

        // Now add an option_value for this custom field
        $params = array( );
        $params = array( 'option_group_id' => $field['option_group_id'],
                         'label'           => 'Divorced',
                         'value'           => 'V',
                         'weight'          => 6 );
        $ids = array( );
        require_once 'CRM/Core/BAO/OptionValue.php';
        $option = CRM_Core_BAO_OptionValue::add( $params, $ids );

        // Now use retrieve to get the new option value
        $defaults = array( );
        $params   = array( 'id' => $option->id);
        require_once 'CRM/Core/BAO/CustomOption.php';
        CRM_Core_BAO_CustomOption::retrieve( $params, $defaults );

        $this->assertEqual( $defaults['label'], 'Divorced', 'Verify that label of retrieved option is "Divorced".');

        // Now delete the option value which we added
        CRM_Core_BAO_CustomOption::del( $defaults['id'] );
        $this->assertDBNull( 'CRM_Core_DAO_OptionValue', $defaults['id'], 'id', 'id', 'Verify that inserted option value has been deleted');
        
    }


    /**
     * del () method - Deletes a single option_value record after updating (set to null or 0) any custom data that uses that option_value.
     * For this test we will actually assign our new custom field value to a contact, and then verify that
     * the del() method clears the value.
     */
    function testDel( )
    {
        // Retrieve the option_group_id for sample custom field 'Marital Status'
        $params = array( );
        $params = array( 'label'   => 'Marital Status');
        $field  = array( );
        
        require_once 'CRM/Core/BAO/CustomField.php';
        CRM_Core_BAO_CustomField::retrieve( $params, $field );
        $fieldID = $field['id'];
        
        // Now add an option_value for this custom field
        $params = array( );
        $params = array( 'option_group_id' => $field['option_group_id'],
                        'label'            => 'Divorced',
                        'value'            => 'V',
                        'weight'           => 6 );
        $ids = array( );
        require_once 'CRM/Core/BAO/OptionValue.php';
        $option = CRM_Core_BAO_OptionValue::add( $params, $ids );
        
        // Now use retrieve to get the new option value
        $defaults = array( );
        $params   = array( 'id' => $option->id);
        require_once 'CRM/Core/BAO/CustomOption.php';
        CRM_Core_BAO_CustomOption::retrieve( $params, $defaults );
        
        // CRM_Core_Error::debug('p',$params);

        // Create an individual and assign our custom option to that contact
        $contactID = Contact::createIndividual( );

        require_once 'CRM/Core/BAO/CustomValueTable.php';
        $params = array( 'entityID'           => $contactID,
                         'custom_' . $fieldID => 'V');
        $result = CRM_Core_BAO_CustomValueTable::setValues( $params );
 
        // Check that value is stored
        $params = array( 'entityID'               => $contactID,
                         'custom_' . $field['id'] => 1);
        $values = array( );
        $values = CRM_Core_BAO_CustomValueTable::getValues( $params );
        // CRM_Core_Error::debug('v1',$values);
        $this->assertEqual( $values['custom_' . $fieldID], 'V', 'Verify that option_value.value is stored for contact ' . $contactID);
        
        
        // Now delete the option value which we added
        CRM_Core_BAO_CustomOption::del( $defaults['id'] );
        $this->assertDBNull( 'CRM_Core_DAO_OptionValue', $defaults['id'], 'id', 'id', 'Verify that inserted option value has been deleted');

        // And verify that value is no longer set for our contact.
        $values = CRM_Core_BAO_CustomValueTable::getValues( $params );        
        $this->assertEqual( $values['custom_' . $fieldID], '', 'Verify that option_value.value has been cleared for contact ' . $contactID);

        // CRM_Core_Error::debug('v2',$values);
        
        // Now cleanup our contact
        Contact::delete( $contactID );

    }
    
    /**
     * getCustomOption() method - Retrieves all option_value records ordered by weight for a given field. Default is 'active options' only.
     */
    function testGetCustomOption( )
    {
        // Retrieve the field id for sample custom field 'Marital Status'
        $params = array( );
        $params = array( 'label'   => 'Marital Status');
        $field  = array( );
        
        require_once 'CRM/Core/BAO/CustomField.php';
        CRM_Core_BAO_CustomField::retrieve( $params, $field );
        $fieldID = $field['id'];
        
        // Now get the active options
        require_once 'CRM/Core/BAO/CustomOption.php';
        $options = array( );
        $options = CRM_Core_BAO_CustomOption::getCustomOption( $fieldID );

        // Should have 5 array items (option_value rows)
        $this->assertEqual( count($options), 5, 'Verify that we got 5 option value items back.');
        // Last item should have label = 'Other' (since ordered by weight).
        // might need to fix this. i'm assuming array_pop returns the value and not key
        $lastOption = array_pop( $options );
        $this->assertEqual( $lastOption['label'], 'Other', 'Verify that label for last option is "Other".');
    }
    
}
