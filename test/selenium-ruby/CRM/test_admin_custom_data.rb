# This is a test case of using Selenium and its Ruby bindings
# Information' Custom data definition
# This test case allows you to add/edit/disable/enable/delete custom data information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminCustomData < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Custom data
    assert_equal "Custom\nData", @selenium.get_text("//a[@id='id_CustomData']")
    @page.click_and_wait "//a[@id='id_CustomData']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Custom Data information
  def test_1_addCustomData
    @page.click_and_wait "//a[contains(text(),'»  New Custom Data Group')]"
    
    # Read new Custom Data information
    @selenium.type "title", "Custom Data1"
    @selenium.select "extends[0]", "label=Individuals"
    @selenium.type "weight", "0"
    @selenium.select "style", "label=Tab"
    @selenium.click "collapse_display"
    @selenium.type "help_pre", "Pre-form Help"
    @selenium.type "help_post", "Post-form Help"
           
    # Submit the form 
    @page.click_and_wait "_qf_Group_next"
    assert @selenium.is_text_present("Your Group \"Custom Data1\" has been added. You can add custom fields to this group now.")
  end

  # Add new Custom Field information
  def test_2_1_addCustomField
    assert_equal "View and Edit Custom Fields", @selenium.get_text("link=View and Edit Custom Fields")
    @page.click_and_wait "link=View and Edit Custom Fields"
    
    # add new custom field in a group
    
    assert @selenium.is_text_present("There are no custom fields for custom group \"Custom Data1\", add one.") 
    assert_equal "add one", @selenium.get_text("link=add one")
    @page.click_and_wait "link=add one"
         
    # Insert information for new custom field  
    @selenium.type   "//div[@class='form-item']/descendant::input[@type='text' and @id='label']", "Field1"
    @selenium.select "data_type[0]", "label=Integer"
    @selenium.select "data_type[1]", "label=Text"
    @selenium.type   "weight", "0"
    @selenium.type   "default_value", "0"
    @selenium.type   "help_post", "Field Help"
    @selenium.click  "is_required"
    @selenium.click  "is_searchable"
    @selenium.click  "//input[@type='radio' and @value='1']"
    @selenium.uncheck  "is_active"
    
    # Submit the Custom field form
    @page.click_and_wait "_qf_Field_next"
    assert @selenium.is_text_present("Your custom field \"Field1\" has been saved")
  end 

  # Editing Custom Field information
  def test_2_2_editCustomField
    # Click View and edit Custom Field 
    assert_equal "View and Edit Custom Fields", @selenium.get_text("link=View and Edit Custom Fields")
    @page.click_and_wait "link=View and Edit Custom Fields"
    
    # Click Edit Field
    assert_equal "Edit Field", @selenium.get_text("link=Edit Field")
    @page.click_and_wait "link=Edit Field"
    
    @selenium.type "weight", "0"
    @selenium.type "default_value", "1"
    @selenium.check "is_required"
    @selenium.check "is_active"

    # Submit the Custom field form    
    @page.click_and_wait "_qf_Field_next"
    assert @selenium.is_text_present("Your custom field \"Field1\" has been saved")
  end
  
  # Preview Custom field 
  def test_2_3_previewCustomField
    # Click View and edit Custom Field 
    assert_equal "View and Edit Custom Fields", @selenium.get_text("link=View and Edit Custom Fields")
    @page.click_and_wait "link=View and Edit Custom Fields"
    
    # Click Preview Field
    assert_equal "Preview Field Display", @selenium.get_text("link=Preview Field Display")
    @page.click_and_wait "link=Preview Field Display"
  
    assert @selenium.is_text_present("Preview of this field as it will be displayed in an edit form.")
    @page.click_and_wait "_qf_Preview_cancel"
  end
  
  # Disable Custom Field 
  def test_2_4_disableCustomField
   # Click View and edit Custom Field 
    assert_equal "View and Edit Custom Fields", @selenium.get_text("link=View and Edit Custom Fields")
    @page.click_and_wait "link=View and Edit Custom Fields"
    
    # Click Disable Field
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this custom data field?", @selenium.get_confirmation()
  end

  # Enable Custom Field
  def test_2_5_enableCustomField
   # Click View and edit Custom Field 
    assert_equal "View and Edit Custom Fields", @selenium.get_text("link=View and Edit Custom Fields")
    @page.click_and_wait "link=View and Edit Custom Fields"
    
    # Click Disable Field
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end

  # Delete Custom Field
  def test_2_6_deleteCustomField
     # Click View and edit Custom Field 
    assert_equal "View and Edit Custom Fields", @selenium.get_text("link=View and Edit Custom Fields")
    @page.click_and_wait "link=View and Edit Custom Fields"
    
    # Click Delete Field
    @page.click_and_wait "link=Delete"
    assert_equal "Are you sure you want to delete this custom data field?", @selenium.get_confirmation()
    assert @selenium.is_text_present("WARNING: Deleting this custom field will result in the loss of all \"Field1\" data. Any Profile form and listings field(s) linked with \"Field1\" will also be deleted. This action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_DeleteField_next"
    assert @selenium.is_text_present("The custom field \"Field1\" has been deleted.")
  end
  
  # Editing Custom Data information
  def test_3_settingCustomData
    assert_equal "Settings", @selenium.get_text("link=Settings")
    @page.click_and_wait "link=Settings"
    
    @selenium.select "style", "label=Inline"
    
    #Edit custom data
    @page.click_and_wait "_qf_Group_next"
  end

  # Disable Custom Data 
  def test_4_disableCustomData
   # Click View and edit Custom Data 
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this custom data group? Any profile fields that are linked to custom fields of this group will be disabled.", @selenium.get_confirmation()
  end

  # Enable Custom Field
  def test_5_enableCustomField
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end

  # Delete Custom Data
  def test_6_deleteCustomData
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this custom group will result in the loss of all Custom Data1 data. This action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_DeleteGroup_next"
    if /\w*"Custom Data1"/ 
      assert @selenium.is_text_present("The Group \"Custom Data1\" has not been deleted! You must Delete all custom fields in this group prior to deleting the group")
    else
      assert @selenium.is_text_present("The Group \"Custom Data1\" has been deleted.")
    end
  end
end
