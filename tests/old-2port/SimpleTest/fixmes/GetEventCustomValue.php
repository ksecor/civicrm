<?php

require_once 'api/crm.php';

class TestOfGetEventCustomValue extends UnitTestCase 
{
    protected $_contact;
    protected $_customField;
    protected $_event;
    protected $_customValue;
    
    function setUp() 
    {
        $this->createCustomData( );
        $this->createEvent( );
        $this->createCustomValue( );
    }
    
    function tearDown() 
    {
        crm_delete_event( $this->_event['id'] );
        
        crm_delete_custom_field( $this->_customField->id );
        
        crm_delete_custom_group( $this->_customGroup->id );
    }
        
    /**************************************
     * Testing the get_custom_value api
     *************************************/
    function testGetCustomValue( )
    {
        $getParams = array( 'entity_table'    => 'civicrm_event',
                            'entity_id'       => $this->_event['id'],
                            'custom_field_id' => $this->_customField->id
                            );
        $customValue = crm_get_custom_value( $getParams );
        
        CRM_Core_Error::debug( 'Custom Values', $customValue );
        
        $this->assertEqual( $customValue['entity_id'], $this->_event['id'] );
    }
    
    /**************************************
     * Create Custom Group and Custom Fields
     *************************************/
    function createCustomData()
    {
        $groupParams = array('domain_id'        => 1,
                             'title'            => 'Event Custom Group',
                             'weight'           => 3,
                             'style'            => 'Inline',
                             'collapse_display' => 0,
                             'is_active'        => 1,
                             'help_post'        => 'Custom group created for testing out issue reported by Matt Corks'
                             );
        
        $class_name = 'Event';
        $this->_customGroup =& crm_create_custom_group( $class_name, $groupParams );
        
        $fieldParams = array('label'         => 'Test Field',
                             'data_type'     => 'String',
                             'html_type'     => 'Text',
                             'is_searchable' => '1', 
                             'is_active'     => 1,
                             'weight'        => 1
                             );
        $this->_customField = & crm_create_custom_field( $this->_customGroup, $fieldParams );
    }
    
    /**************************************
     * Create event
     *************************************/    
    function createEvent( ) 
    {
        $eventParams = array(
                        'title'                    => 'Annual Function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'end_date'                 => '20071210',
                        'is_online_registration'   => '0',
                        'max_participants'         => '150',
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1'
                        );
	
        $this->_event = & crm_create_event( $eventParams );
    }
    
    /**************************************
     * Create custom value
     *************************************/
    function createCustomValue()
    {
        $value       = array( 'value' => 'Adding value for the Test Custom Field created for testing the issue by the Matt Corks' );
        $this->_customValue = 
            crm_create_custom_value('civicrm_event', $this->_event['id'], $this->_customField, $value);
    }
}
