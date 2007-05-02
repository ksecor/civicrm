# This is a test case of using Selenium and its Ruby bindings
# Information' Location types definition
# This test case allows you to add/edit/disable/enable/delete location information

require 'test/unit'
require 'crm_page_controller'
require '../selenium'

class TC_TestAdminLocation < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_location
    # Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Location Type
    assert_equal "Location\nTypes\n(Home,\nWork...)", @selenium.get_text("//a[@id='id_LocationTypes_Home_Work...']")
    @page.click_and_wait "//a[@id='id_LocationTypes_Home_Work...']"
    
    assert @selenium.is_text_present("Location Types (Home, Work...)")
    
    add_location()
    edit_location()
    enable_location()
    disable_location()
    delete_location()
  end
  
  # Add new Location information
  def add_location
    assert_equal "» New Location Type", @selenium.get_text("link=» New Location Type")
    @page.click_and_wait "link=» New Location Type"
    
    # Read new Location type information
    @selenium.type "name", "New Location"
    @selenium.type "description", "This location type is for testing purpose."
    
    @location_type = { 'is_active'  => "//input[@type='checkbox' and @name='is_active']",
                       'is_default' => "//input[@type='checkbox' and @name='is_default']" }
    
    @location_type.each{ | key, value |
      if @selenium.get_value(value) == 'off'
        @selenium.check value 
      end
    }
    
    # Submit the form 
    @page.click_and_wait "_qf_LocationType_next"
  end
  
  # Editing location information
  def edit_location
    assert_equal "Edit", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Location')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Location')]]/descendant::a[contains(.,'Edit')]"
    
    @location_type.each{ | key, value |
      if @selenium.get_value(value) == 'on'
        @selenium.uncheck value
      end
    }
    
    @selenium.type "vcard_name", "Test Location"
    
    #Submit the form 
    @page.click_and_wait "_qf_LocationType_next"
    assert @selenium.is_text_present("The location type \"New Location\" has been saved.")
  end
  
  # Enable location type
  def enable_location
    assert_equal "Enable", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Location')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Location')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Disable location type
  def disable_location
    assert_equal "Disable", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Location')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Location')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this location type?\n\nUsers will no longer be able to select this value when adding or editing contact locations.", @selenium.get_confirmation()
  end
  
  # Delete location type
  def delete_location
    assert_equal "Delete", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Location')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Location')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all location type records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_LocationType_next"
    assert @selenium.is_text_present("Selected Location type has been deleted.")
  end
end
