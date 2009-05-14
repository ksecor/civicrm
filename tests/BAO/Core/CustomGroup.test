<?php

require_once 'CiviTestCase.php';
require_once 'Contact.php';
require_once 'Custom.php';

class BAO_Core_CustomGroup extends CiviTestCase 
{
    function get_info( ) 
    {
        return array(
                     'name'        => 'CustomGroup BAOs',
                     'description' => 'Test all Core_BAO_CustomGroup methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();
    }

    /**
     * Function to test getTree()
     */
    function testgetTree()
    {
        $params      = array( );
        $contactId   = Contact::createIndividual();
        $customGrouptitle = 'My Custom Group';
        $groupParams = array(
                             'title'      => $customGrouptitle,
                             'name'       => 'my_custom_group',
                             'style'      => 'Tab',
                             'extends'    => 'Individual',
                             'is_active'  => 1
                             );

        $customGroup = Custom::createGroup( $groupParams );
        $customGroupId = $customGroup->id;

        $fields      = array (
                              'groupId'  =>  $customGroupId,
                              'dataType' => 'String',
                              'htmlType' => 'Text'
                              );
        
        $customField = Custom::createField( $params, $fields );
        $formParams = NULL;
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $getTree = CRM_Core_BAO_CustomGroup::getTree('Individual', $formParams, $customGroupId );
              
        $dbCustomGroupTitle = $this->assertDBNotNull( 'CRM_Core_DAO_CustomGroup', $customGroupId, 'title', 'id',
                                                      'Database check for custom group record.' );
        
        Custom::deleteField( $customField );        
        Custom::deleteGroup( $customGroup );
        Contact::delete( $contactId );
        $customGroup->free();
    }
    
    /**
     * Function to test retrieve()
     */
    function testRetrieve()
    {
        $customGrouptitle = 'My Custom Group';
        $groupParams = array(
                             'title'            => $customGrouptitle,
                             'name'             => 'my_custom_group',
                             'style'            => 'Tab',
                             'extends'          => 'Individual',
                             'help_pre'         => 'Custom Group Help Pre',
                             'help_post'        => 'Custom Group Help Post',
                             'is_active'        => 1,
                             'collapse_display' => 1,
                             'weight'           => 2
                             );
        
        $customGroup = Custom::createGroup( $groupParams );
        $customGroupId = $customGroup->id;
        
        $params = array( 'id' => $customGroupId );
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $customGroup = CRM_Core_BAO_CustomGroup::retrieve( $params, $dafaults );
        $dbCustomGroupTitle = $this->assertDBNotNull( 'CRM_Core_DAO_CustomGroup', $customGroupId, 'title', 'id',
                                                      'Database check for custom group record.' );
        
        $this->assertEqual( $customGrouptitle, $dbCustomGroupTitle );
        //check retieve values
        $this->assertAttributesEqual( $groupParams, $dafaults ); 
        
        //cleanup DB by deleting customGroup
        Custom::deleteGroup( $customGroup );
    }
    
    /**
     * Function to test setIsActive()
     */
    function testSetIsActive()
    {
        $customGrouptitle = 'My Custom Group';
        $groupParams = array(
                             'title'      => $customGrouptitle,
                             'name'       => 'my_custom_group',
                             'style'      => 'Tab',
                             'extends'    => 'Individual',
                             'is_active'  => 0
                             );
        
        $customGroup = Custom::createGroup( $groupParams );
        $customGroupId = $customGroup->id;
        
        require_once 'CRM/Core/BAO/CustomGroup.php';
        //update is_active
        $result = CRM_Core_BAO_CustomGroup::setIsActive( $customGroupId, true );
        //check for object update
        $this->assertEqual( true, $result );
        //check for is_active
        $this->assertDBCompareValue( 'CRM_Core_DAO_CustomGroup', $customGroupId, 'is_active', 'id', true, 
                                     'Database check for custom group is_active field.' );
        //cleanup DB by deleting customGroup
        Custom::deleteGroup( $customGroup );
    }
    
    /**
     * Function to test getGroupDetail()
     */
    function testGetGroupDetail()
    {
        $customGrouptitle = 'My Custom Group';
        $groupParams = array(
                             'title'            => $customGrouptitle,
                             'name'             => 'my_custom_group',
                             'extends'          => 'Individual',
                             'help_pre'         => 'Custom Group Help Pre',
                             'help_post'        => 'Custom Group Help Post',
                             'is_active'        => 1,
                             'collapse_display' => 1,
                             );
        
        $customGroup = Custom::createGroup( $groupParams );
        $customGroupId = $customGroup->id;
        
        $fieldParams = array(
                             'custom_group_id' => $customGroupId,
                             'label'           => 'Test Custom Field',
                             'html_type'       => 'Text',
                             'data_type'       => 'String',
                             'is_required'     => 1,
                             'is_searchable'   => 0,
                             'is_active'       => 1
                             );
        
        $customField = Custom::createField( $fieldParams );
        $customFieldId = $customField->id;
        
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupTree = CRM_Core_BAO_CustomGroup::getGroupDetail( $customGroupId );
        $dbCustomGroupTitle = $this->assertDBNotNull( 'CRM_Core_DAO_CustomGroup', $customGroupId, 'title', 'id',
                                                      'Database check for custom group record.' );
        //check retieve values of custom group
        unset( $groupParams['is_active'] );
        $this->assertAttributesEqual( $groupParams, $groupTree[$customGroupId] ); 
        
        //check retieve values of custom field
        unset( $fieldParams['is_active'] );
        unset( $fieldParams['custom_group_id'] );
        $this->assertAttributesEqual( $fieldParams, $groupTree[$customGroupId]['fields'][$customFieldId] ); 
        
        //cleanup DB by deleting customGroup
        Custom::deleteField( $customField ); 
        Custom::deleteGroup( $customGroup );
    }
    
    /**
     * Function to test getTitle()
     */
    function testGetTitle()
    {
        $customGrouptitle = 'My Custom Group';
        $groupParams = array(
                             'title'      => $customGrouptitle,
                             'name'       => 'my_custom_group',
                             'style'      => 'Tab',
                             'extends'    => 'Individual',
                             'is_active'  => 0
                             );
        
        $customGroup = Custom::createGroup( $groupParams );
        $customGroupId = $customGroup->id;
        
        require_once 'CRM/Core/BAO/CustomGroup.php';
        //get the custom group title
        $title = CRM_Core_BAO_CustomGroup::getTitle( $customGroupId );
        
        //check for object update
        $this->assertEqual( $customGrouptitle, $title );
        
        //cleanup DB by deleting customGroup
        Custom::deleteGroup( $customGroup );
    }
    
    /**
     * Function to test deleteGroup()
     */
    function testDeleteGroup( )
    { 
        $customGrouptitle = 'My Custom Group';
        $groupParams = array(
                             'title'      => $customGrouptitle,
                             'name'       => 'my_custom_group',
                             'style'      => 'Tab',
                             'extends'    => 'Individual',
                             'is_active'  => 1
                             );
        
        $customGroup = Custom::createGroup( $groupParams );
        $customGroupId = $customGroup->id;
        
        //get the custom group title
        $dbCustomGroupTitle = $this->assertDBNotNull( 'CRM_Core_DAO_CustomGroup', $customGroupId, 'title', 'id',
                                                      'Database check for custom group record.' );
        //check for group title
        $this->assertEqual( $customGrouptitle, $dbCustomGroupTitle );
        
        require_once 'CRM/Core/BAO/CustomGroup.php';
        //delete the group
        $isDelete = CRM_Core_BAO_CustomGroup::deleteGroup( $customGroup );
        
        //check for delete
        $this->assertEqual( true, $isDelete );
        
        //check the DB
        $this->assertDBNull( 'CRM_Core_DAO_CustomGroup', $customGroupId, 'title', 'id', 
                             'Database check for custom group record.' );
    }
    
    /**
     * Function to test createTable()
     */
    function testCreateTable( )
    { 
        $customGrouptitle = 'My Custom Group';
        $groupParams = array(
                             'title'      => $customGrouptitle,
                             'name'       => 'my_custom_group',
                             'style'      => 'Tab',
                             'extends'    => 'Individual',
                             'is_active'  => 1
                             );
        
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $customGroupBAO =& new CRM_Core_BAO_CustomGroup();
        $customGroupBAO->copyValues( $groupParams );
        $customGroup = $customGroupBAO->save();
        $tableName   = 'civicrm_value_test_group_'.$customGroup->id;
        $customGroup->table_name = $tableName;
        $customGroup = $customGroupBAO->save();
        $customTable = CRM_Core_BAO_CustomGroup::createTable( $customGroup );
        $customGroupId = $customGroup->id;
        
        //check db for custom group.
        $dbCustomGroupTitle = $this->assertDBNotNull( 'CRM_Core_DAO_CustomGroup', $customGroupId, 'title', 'id',
                                                      'Database check for custom group record.' );
        //check for custom group table name
        $this->assertDBCompareValue(  'CRM_Core_DAO_CustomGroup', $customGroupId, 'table_name', 'id',
                                      $tableName,  'Database check for custom group table name.' );
        
        //check for group title
        $this->assertEqual( $customGrouptitle, $dbCustomGroupTitle );
        
        //cleanup DB by deleting customGroup
        Custom::deleteGroup( $customGroup );
    }

    /**
     * Function to test checkCustomField()
     */
    function testCheckCustomField()
    {
        $customGroupTitle = 'My Custom Group';
        $groupParams = array(
                             'title'            => $customGroupTitle,
                             'name'             => 'my_custom_group',
                             'extends'          => 'Individual',
                             'help_pre'         => 'Custom Group Help Pre',
                             'help_post'        => 'Custom Group Help Post',
                             'is_active'        => 1,
                             'collapse_display' => 1,
                             );
        
        $customGroup = Custom::createGroup( $groupParams );
        $customGroupId = $customGroup->id;
        
        $customFieldLabel = 'Test Custom Field';
        $fieldParams = array(
                             'custom_group_id' => $customGroupId,
                             'label'           => $customFieldLabel,
                             'html_type'       => 'Text',
                             'data_type'       => 'String',
                             'is_required'     => 1,
                             'is_searchable'   => 0,
                             'is_active'       => 1
                             );
        
        $customField = Custom::createField( $fieldParams );
        $customFieldId = $customField->id;
        
        //check db for custom group
        $dbCustomGroupTitle = $this->assertDBNotNull( 'CRM_Core_DAO_CustomGroup', $customGroupId, 'title', 'id',
                                                      'Database check for custom group record.' );
        $this->assertEqual( $customGroupTitle, $dbCustomGroupTitle );
        
        //check db for custom field
        $dbCustomFieldLabel = $this->assertDBNotNull( 'CRM_Core_DAO_CustomField', $customFieldId, 'label', 'id',
                                                      'Database check for custom field record.' );
        $this->assertEqual( $customFieldLabel, $dbCustomFieldLabel );
        
        //check the custom field type.
        $params = array ( 'Individual' );
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $usedFor = CRM_Core_BAO_CustomGroup::checkCustomField( $customFieldId, $params );
        $this->assertEqual( false, $usedFor );
        
        $params = array( 'Contribution', 'Membership', 'Participant' );
        $usedFor = CRM_Core_BAO_CustomGroup::checkCustomField( $customFieldId, $params );
        $this->assertEqual( true, $usedFor );
        
        //cleanup DB by deleting customGroup
        Custom::deleteField( $customField ); 
        Custom::deleteGroup( $customGroup );
    }
   
    function testGetActiveGroups()
    {
        $contactId = Contact::createIndividual( );
        $customGrouptitle = 'Test Custom Group';
        $groupParams = array(
                             'title'      => $customGrouptitle,
                             'name'       => 'test_custom_group',
                             'style'      => 'Tab',
                             'extends'    => 'Individual',
                             'weight'     => 10,
                             'is_active'  => 1
                             );

       
        $customGroup = Custom::createGroup( $groupParams );
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $activeGroup = CRM_Core_BAO_CustomGroup::getActiveGroups('Individual', 'civicrm/contact/view/cd', $contactId );
        foreach ( $activeGroup as $key => $value ) {
            if ( $value['id'] == $customGroup->id ) {
                $this->assertEqual( $value['path'] ,'civicrm/contact/view/cd' );
                $this->assertEqual( $value['title'] , $customGrouptitle );
                $query = 'reset=1&gid='.$customGroup->id.'&cid='.$contactId;
                $this->assertEqual( $value['query'] , $query );
            } 
        } 
        
        Custom::deleteGroup( $customGroup );
        Contact::delete( $contactId );

    }

    function testCreate( )
    {

        $params = array( 'title'            => 'Test_Group_1',
                         'name'             => 'test_group_1',
                         'extends'          => array('Individual'),
                         'weight'           => 4,
                         'collapse_display' => 1,
                         'style'            => 'Inline',
                         'help_pre'         => 'This is Pre Help For Test Group 1',
                         'help_post'        => 'This is Post Help For Test Group 1',
                         'is_active'        => 1
                         );
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $customGroup =  CRM_Core_BAO_CustomGroup::create( $params );

        $dbCustomGroupTitle = $this->assertDBNotNull( 'CRM_Core_DAO_CustomGroup', $customGroup->id, 'title', 'id',
                                                      'Database check for custom group record.' );
        $this->assertEqual( $params['title'], $dbCustomGroupTitle );
        Custom::deleteGroup( $customGroup );
    } 
}