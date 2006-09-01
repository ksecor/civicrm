# This is a test case of using Selenium and its Ruby bindings
# Information' Location types definition
# This test case allows you to add/edit/disable/enable/delete location information

require 'test/unit'
require 'crm_page_controller'
require '../selenium'

class TestAdminGender < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    # Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Location Type
    assert_equal "Location\nTypes\n(Home,\nWork...)", @selenium.get_text("//a[@id='id_LocationTypes_Home_Work...']")
    @page.click_and_wait "//a[@id='id_LocationTypes_Home_Work...']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Location information
  def test_1_addLocation
    assert_equal "» New Location Type", @selenium.get_text("link=» New Location Type")
    @page.click_and_wait "link=» New Location Type"
    
    # Read new Location type information
    @selenium.wait_for_page_to_load "30000"
    @selenium.type "name", "aLocation"
    @selenium.type "vcard_name", "AL1"
    @selenium.type "description", "Testing location type"
    @selenium.check "is_active" 
    @selenium.click "is_default"
    
    # Submit the form 
    @page.click_and_wait "_qf_LocationType_next"
  end
  
  # Editing location information
  def test_2_editGender
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    
    @selenium.uncheck "is_active" 
    @selenium.type "vcard_name", "AL"

    #Submit the form 
    @page.click_and_wait "_qf_LocationType_next"
    assert @selenium.is_text_present("The location type \"aLocation\" has been saved.")
  end
  
  # Enable location type
  def test_3_enableGender
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end
  
  # Disable location type
  def test_4_disableGender
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this location type?\n\nUsers will no longer be able to select this value when adding or editing contact locations.", @selenium.get_confirmation()
  end
  
  # Delete location type
  def test_5_deleteGender
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all location type records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_LocationType_next"
    assert @selenium.is_text_present("Selected Location type has been deleted.")
  end
end
