# This is a test case of using Selenium and its Ruby bindings
# Information' Prefix definition
# This test case allows you to add/edit/disable/enable/delete prefix information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminPrefix < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Individual prefixes
    assert_equal "Individual\nPrefixes\n(Ms,\nMr...)", @selenium.get_text("//a[@id='id_IndividualPrefixes_Ms_Mr...']")
    @page.click_and_wait "//a[@id='id_IndividualPrefixes_Ms_Mr...']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Prefix
  def test_1_addPrefix
    @page.click_and_wait "link=» New Individual Prefix Option"
    
    # Read new Prefix information
    @selenium.type "name",       "Lt."
    @selenium.type "weight",     "0"
    @selenium.check "is_active" 
    
    # Submit the form 
    @page.click_and_wait "_qf_IndividualPrefix_next"
    assert @selenium.is_text_present("The Individual Prefix \"Lt.\" has been saved.")
  end
  
  # Editing Prefix information
  def test_2_editPrefix
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_IndividualPrefix_next"
    assert @selenium.is_text_present("The Individual Prefix \"Lt.\" has been saved.")
  end

   # Enable prefix
  def test_3_enablePrefix
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end

  # Disable Prefix
  def test_4_disablePrfix
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
   assert_equal "Are you sure you want to disable this Individual Prefix?\n\nUsers will no longer be able to select this value when adding or editing Individual Prefix.", @selenium.get_confirmation()
  end
    
  # Delete Prefix
  def test_5_deletePrefix
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this option will change all Individual records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_IndividualPrefix_next"
    assert @selenium.is_text_present("Selected Individual Prefix has been deleted.")
  end
end
