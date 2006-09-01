# This is a test case of using Selenium and its Ruby bindings
# Information' Mobile Phone Provider definition
# This test case allows you to add/edit/disable/enable/delete Mobile Phone Provider information

require 'test/unit'
require 'crm_page_controller'
require '../selenium'

class TestAdminMPProvider < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Mobile Phone Provider 
    assert_equal "Mobile\nPhone\nProviders", @selenium.get_text("//a[@id='id_MobilePhoneProviders']")
    @page.click_and_wait "//a[@id='id_MobilePhoneProviders']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Mobile Phone Service Provider 
  def test_1_addMPProvider
   @page.click_and_wait "link=» New Mobile Phone Provider"
    
    # Read new Mobile Phone Service Name
    @selenium.type "name", "MPProvider1"
    @selenium.check 'is_active'
    
    # Submit the form 
    @page.click_and_wait "_qf_MobileProvider_next"
    assert @selenium.is_text_present("The Mobile Provider \"MPProvider1\" has been saved.")
  end
  
  # Edit Mobile Phone Service Provider
  def test_2_editMPProvider
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    
    # Read new Mobile Phone Provider Service
    @selenium.type "name", "MPProvider2"
    @selenium.uncheck 'is_active'
    
    # Submit the form 
    @page.click_and_wait "_qf_MobileProvider_next"
    assert @selenium.is_text_present("The Mobile Provider \"MPProvider2\" has been saved.")
  end

  # Disable Mobile Phone Service
  def test_4_disableMPProvider
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
   assert_equal "Are you sure you want to disable this Mobile Phone Service Provider?\n\nUsers will no longer be able to select this value when adding or editing contact phone numbers.", @selenium.get_confirmation()
  end
  
   # Enable Mobile Phone Service
  def test_3_enableMPProvider
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end

   # Delete Mobile Phone Provider 
  def test_5_deleteMPProvider
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
   assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Mobile Provider type records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_MobileProvider_next"
    assert @selenium.is_text_present("Selected Mobile Provider has been deleted.")
  end
end
