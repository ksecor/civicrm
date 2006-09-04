# This is a test case of using Selenium and its Ruby bindings
# Information' Contribution Types definition
# This test case allows you to add/edit/disable/enable/delete contribution information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminContributionTypes < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    assert_equal "Contribution\nTypes", @selenium.get_text("//a[@id='id_ContributionTypes']")
    @page.click_and_wait "//a[@id='id_ContributionTypes']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Contribution Type information
  def test_1_addContributionTypes
    @page.click_and_wait "link=» New Contribution Type"
    
    # Read new Contribution Type information
    @selenium.type "name", "aContribution1"
    @selenium.type "description", "testing contribution"
    @selenium.click "is_deductible"
    @selenium.click "is_active"
    
    # Submit the form 
    @page.click_and_wait "_qf_ContributionType_next"
    assert @selenium.is_text_present("The contribution type \"aContribution1\" has been saved.")
  end
  
  # Editing Contribution Type information
  def test_2_editContributionTypes
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    
    @selenium.type "description", "testing contribution types"
    
    #Submit the form 
    @page.click_and_wait "_qf_ContributionType_next"
    assert @selenium.is_text_present("The contribution type \"aContribution1\" has been saved.")
  end
  
  # Disable Contribution types
  def test_3_disableContributionTypes
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this contribution type?", @selenium.get_confirmation()
  end

   # Enable Contribution types
  def test_4_enableContributionTypes
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end
  
  # Delete Contribution types
  def test_5_deleteContributionTypes
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"

   assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all contribution records of this type. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_ContributionType_next"
    assert @selenium.is_text_present("Selected contribution type has been deleted.")
  end
end
