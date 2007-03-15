 <?php

require_once 'api/crm.php';

class TestOfCreateCustomFieldAPI extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateCustomFieldC()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 1 For Creating Custom Field',
                        'name'  => 'new_group_1',
                        'weight' => 3,
                        'collapse_display' => 0,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For New Group 1.',
                        'help_post' => 'This is for extending CONTACT type of Class.'
                        );
        $class_name = 'Contact';
        $this->customGroupC =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($this->customGroupC, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 2',
                         'weight' => 3,
                         'is_active' => 1
                         );
        $this->customFieldC = & crm_create_custom_field($this->customGroupC, $paramsF);
        $this->assertIsA($this->customFieldC, 'CRM_Core_BAO_CustomField');
    }
    
    function testCreateCustomFieldI()
    {
        $paramsG = array('domain_id' => 1,
                         'title' => 'New Group 2 For Creating Custom Field',
                         'name'  => 'new_group_2',
                         'weight' => 4,
                         'collapse_display' => 1,
                         'style' => 'Tab',
                         'help_pre' => 'This is Pre Help For New Group 2.',
                         'help_post' => 'This is for extending INDIVIDUAL type of Class.'
                         );
        $class_name = 'Individual';
        $this->customGroupI =& crm_create_custom_group($class_name, $paramsG);
        $this->assertIsA($this->customGroupI, 'CRM_Core_BAO_CustomGroup');
     
        $paramsF = array('label' => 'Test Field 1 for Group 2',
                         'name'  => 'test_field_1',
                         'weight' => 3,
                         'data_type' => 'string',
                         'html_type' => 'text',
                         'is_searchable' => '1',
                         'is_active' => 1,
                         'help_pre' => 'Pre Help For Tes Field 1 for Group 2',
                         'help_post'=> 'Post Help For Tes Field 1 for Group 2'
                         );
        $this->customFieldI = & crm_create_custom_field($this->customGroupI, $paramsF);
        $this->assertIsA($this->customFieldI, 'CRM_Core_BAO_CustomField');
    }
    
    function testCreateCustomFieldH()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 3 For Creating Custom Field',
                        'name'  => 'new_group_3',
                        'weight' => 5,
                        'collapse_display' => 0,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For New Group 3.',
                        'help_post' => 'This is for extending HOUSEHOLD type of Class.',
                        'is_active' => 1
                        );
        $class_name = 'Household';
        $this->customGroupH =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($this->customGroupH, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 3',
                         'name'  => 'test_field_1',
                         'weight' => 2,
                         'data_type' => 'Int',
                         'html_type' => 'CheckBox',
                         'is_searchable' => '1',
                         'is_active' => 1,
                         'help_pre' => 'Pre Help For Tes Field 1 for Group 3',
                         'help_post'=> 'Post Help For Tes Field 1 for Group 3'
                         );
        $this->customFieldH =& crm_create_custom_field($this->customGroupH, $paramsF);
        $this->assertIsA($this->customFieldH, 'CRM_Core_BAO_CustomField');
    }
    
    function testCreateCustomFieldOErrorNoWeight()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 4 For Creating Custom Field',
                        'name'  => 'new_group_4',
                        'weight' => 6,
                        'collapse_display' => 1,
                        'help_pre' => 'This is Pre Help For New Group 4.',
                        'help_post' => 'This is for extending ORGANIZATION type of Class.'
                        );
        $class_name = 'Organization';
        $this->customGroupE1 =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($this->customGroupE1, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 4',
                         'name'  => 'test_field_1',
                         'data_type' => 'Int',
                         'html_type' => 'CheckBox',
                         'is_searchable' => 1, 
                         'is_active' => 1,
                         'help_pre' => 'Pre Help For Tes Field 1 for Group 4',
                         'help_post'=> 'Post Help For Tes Field 1 for Group 4'
                         );
        $customField =& crm_create_custom_field($this->customGroupE1, $paramsF);
        $this->assertIsA($customField, 'CRM_Core_Error');
    }
    
    function testCreateCustomFieldErrorEmptyParam()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 5 For Creating Custom Field',
                        'name'  => 'new_group_5',
                        'weight' => 3,
                        'collapse_display' => 0,
                        'help_pre' => 'This is Pre Help For New Group 5.',
                        'help_post' => 'This is for extending CONTACT type of Class.'
                        );
        $class_name = 'Activity';
        $this->customGroupE2 =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($this->customGroupE2, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array();
        $customField =& crm_create_custom_field($this->customGroupE2, $paramsF);
        $this->assertIsA($customField, 'CRM_Core_Error');
    }
    
    function testCreateCustomFieldG()
    {
        $paramsG = array('domain_id' => 1,
                         'title' => 'New Group 6 For Creating Custom Field',
                         'name'  => 'new_group_6',
                         'weight' => 6,
                         'collapse_display' => 1,
                         'style' => 'Inline',
                         'is_active' => 1,
                         'help_pre' => 'This is Pre Help For New Group 6.',
                         'help_post' => 'This is for extending GROUP type of Class.'
                         );
        $class_name = 'Group';
        $this->customGroupG =& crm_create_custom_group($class_name, $paramsG);
        $this->assertIsA($this->customGroupG, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 6',
                         'name'  => 'test_field_1',
                         'weight' => 3,
                         'data_type' => 'string',
                         'html_type' => 'text',
                         'is_searchable' => '1', 
                         'is_active' => 1,
                         'help_pre' => 'Pre Help For Test Field 1 for Group 6',
                         'help_post'=> 'Post Help For Test Field 1 for Group 6'
                         );
        $this->customFieldG =& crm_create_custom_field($this->customGroupG, $paramsF);
        $this->assertIsA($this->customFieldG, 'CRM_Core_BAO_CustomField');
    }
    
    function testCreateCustomFieldPC()
    {
        $paramsG = array('domain_id' => 1,
                         'title' => 'New Group 7 For Creating Custom Field',
                         'name'  => 'new_group_7',
                         'weight' => 7,
                         'collapse_display' => 1,
                         'style' => 'Inline',
                         'is_active' => 1,
                         'help_pre' => 'This is Pre Help For New Group 7.',
                         'help_post' => 'This is for extending PHONECALL type of Class.'
                         );
        $class_name = 'Phonecall';
        $this->customGroupPC =& crm_create_custom_group($class_name, $paramsG);
        $this->assertIsA($this->customGroupPC, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 7',
                         'name'  => 'test_field_1',
                         'weight' => 3,
                         'data_type' => 'string',
                         'html_type' => 'text',
                         'is_searchable' => '1', 
                         'is_active' => 1,
                         'help_pre' => 'Pre Help For Test Field 1 for Group 7',
                         'help_post'=> 'Post Help For Test Field 1 for Group 7'
                         );
        $this->customFieldPC =& crm_create_custom_field($this->customGroupPC, $paramsF);
        $this->assertIsA($this->customFieldPC, 'CRM_Core_BAO_CustomField');
    }
    
    function testCreateCustomFieldM()
    {
        $paramsG = array('domain_id' => 1,
                         'title' => 'New Group 8 For Creating Custom Field',
                         'name'  => 'new_group_8',
                         'weight' => 8,
                         'collapse_display' => 1,
                         'style' => 'Inline',
                         'is_active' => 1,
                         'help_pre' => 'This is Pre Help For New Group 8.',
                         'help_post' => 'This is for extending MEETING type of Class.'
                         );
        $class_name = 'Meeting';
        $this->customGroupM =& crm_create_custom_group($class_name, $paramsG);
        $this->assertIsA($this->customGroupM, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 8',
                         'name'  => 'test_field_1',
                         'weight' => 3,
                         'data_type' => 'string',
                         'html_type' => 'text',
                         'is_searchable' => '1', 
                         'is_active' => 1,
                         'help_pre' => 'Pre Help For Test Field 1 for Group 8',
                         'help_post'=> 'Post Help For Test Field 1 for Group 8'
                         );
        $this->customFieldM =& crm_create_custom_field($this->customGroupM, $paramsF);
        $this->assertIsA($this->customFieldM, 'CRM_Core_BAO_CustomField');
    }

    function testDeleteCustomFieldBeforeCustomGroup()
    {        
        $val =&  crm_delete_custom_field($this->customFieldC->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->customFieldI->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->customFieldH->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->customFieldG->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->customFieldPC->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->customFieldM->id);
        $this->assertNull($val);
               
        $val =&  crm_delete_custom_group($this->customGroupC->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->customGroupI->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->customGroupH->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->customGroupG->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->customGroupPC->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->customGroupM->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->customGroupE1->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->customGroupE2->id);
        $this->assertNull($val);
    }
}
?>