# This is a test case of using Selenium and its Ruby bindings
# Information' Gender definition
# This test case allows you to add/edit/disable/enable/delete gender information

require 'test/unit'
require 'crm_page_controller'
require '../selenium'

class TestAdminIMProvider < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Instant Messenger Service Provider 
    assert_equal "Instant\nMessenger\nServices", @selenium.get_text("//a[@id='id_InstantMessengerServices']")
    @page.click_and_wait "//a[@id='id_InstantMessengerServices']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new IM Service Provider
  def test_1_addIMProvider
   @page.click_and_wait "link=» New IM Service Provider"
    
    # Read new IM Service Name
    @selenium.type "name", "IMP1"
    @selenium.check 'is_active'
    
    # Submit the form 
    @page.click_and_wait "_qf_IMProvider_next"
    assert @selenium.is_text_present("The IM Provider \"IMP1\" has been saved.")
  end
  
  # Add new IM Service Provider
  def test_2_editIMProvider
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    
    # Read new IM Service Name
    @selenium.type "name", "IMP2"
    @selenium.uncheck 'is_active'
    
    # Submit the form 
    @page.click_and_wait "_qf_IMProvider_next"
    assert @selenium.is_text_present("The IM Provider \"IMP2\" has been saved.")
  end

  # Disable IM Service
  def test_3_disableIMProvider
    # @temp = @selenium.get_html_source()
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this IM Service Provider?\n\nUsers will no longer be able to select this value when adding or editing contact IM screen names.", @selenium.get_confirmation()
  end
  
   # Enable IM Service
  def test_4_enableIMProvider
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end

   # Delete Gender type
  def test_5_deleteIMProvider
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all IM Service Provider type records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue? ")
    @page.click_and_wait "_qf_IMProvider_next"
    assert @selenium.is_text_present("Selected IMProvider has been deleted.")
  end
end
