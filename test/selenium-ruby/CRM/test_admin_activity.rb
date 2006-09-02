# This is a test case of using Selenium and its Ruby bindings
# Information' Activity definition
# This test case allows you to add/edit/disable/enable/delete activity information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminActivity < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Activity Type
    assert_equal "Activity\nTypes", @selenium.get_text("//a[@id='id_ActivityTypes']")
    @page.click_and_wait "//a[@id='id_ActivityTypes']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Activity information
  def test_1_addActivity
    @page.click_and_wait "link=» New Activity Type"
    
    # Read new activity information
    @selenium.type "name",        "activity1"
    @selenium.type "description", "Test activity type"
    @selenium.check "is_active" 
    
    # Submit the form 
    @page.click_and_wait "_qf_ActivityType_next"
    assert @selenium.is_text_present("The activity type \"activity1\" has been saved.")
  end
  
  # Editing activity information
  def test_2_editActivity
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_ActivityType_next"
    assert @selenium.is_text_present("The activity type \"activity1\" has been saved.")
  end
    
  # Enable activity
  def test_3_enableActivity
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end
  # Disable activity
  def test_4_disableActivity
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this activity type?\n\nUsers will no longer be able to select this value when adding or editing activities.", @selenium.get_confirmation()
  end
  
  # Delete activity
  def test_5_deleteActivity
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all activity type records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_ActivityType_next"
    assert @selenium.is_text_present("Selected activity type has been deleted.")
  end
end
