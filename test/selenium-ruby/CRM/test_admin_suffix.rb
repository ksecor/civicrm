# This is a test case of using Selenium and its Ruby bindings
# Information' Suffix definition
# This test case allows you to add/edit/disable/enable/delete suffix information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminSuffix < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Individual Suffix
    assert_equal "Individual\nSuffixes\n(Jr,\nSr...)", @selenium.get_text("//a[@id='id_IndividualSuffixes_Jr_Sr...']")
    @page.click_and_wait "//a[@id='id_IndividualSuffixes_Jr_Sr...']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Sufefix
  def test_1_addSuffix
    @page.click_and_wait "link=» New Individual Suffix Option"
    
    # Read new Suffix information
    @selenium.type "name",       "I"
    @selenium.type "weight",     "0"
    @selenium.check "is_active" 
    
    # Submit the form 
    @page.click_and_wait "_qf_IndividualSuffix_next"
    assert @selenium.is_text_present("The Individual Suffix \"I\" has been saved.")
  end
  
  # Editing Suffix information
  def test_2_editSuffix
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_IndividualSuffix_next"
    assert @selenium.is_text_present("The Individual Suffix \"I\" has been saved.")
  end

   # Enable Suffix
  def test_3_enableSuffix
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end

  # Disable Suffix
  def test_4_disableSuffix
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this Individual Suffix?\n\nUsers will no longer be able to select this value when adding or editing Individual Suffix.", @selenium.get_confirmation()
  end
    
  # Delete Suffix
  def test_5_deleteSuffix
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this option will change all Individual records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_IndividualSuffix_next"
    assert @selenium.is_text_present("Selected Individual Suffix has been deleted.")
  end
end
