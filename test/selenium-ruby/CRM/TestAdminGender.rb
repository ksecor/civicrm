# This is a test case of using Selenium and its Ruby bindings
# Information' Gender definition
# This test case allows you to add/edit/disable/enable/delete gender information

require 'test/unit'
require 'crm_page_controller'
require '../selenium'

class TestAdminGender < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.startCivicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.clickAndWait "link=Administer CiviCRM"
    
    assert_equal "Gender\nOptions\n(Male,\nFemale...)", @selenium.get_text("//a[@id='id_GenderOptions_Male_Female...']")
    @page.clickAndWait "//a[@id='id_GenderOptions_Male_Female...']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Gender information
  def test_1_addGender
    @page.clickAndWait "link=» New Gender Option"
    
    # Read new Gender information
    @selenium.type "name",       "Gender1"
    @selenium.type "weight",     "0"
    @selenium.click "is_active" 
    
    # Submit the form 
    @page.clickAndWait "_qf_Gender_next"
    assert @selenium.is_text_present("The Gender \"Gender1\" has been saved.")
  end
  
  # Editing gender information
  def test_2_editGender
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.clickAndWait "link=Edit"
    
    # @selenium.type('weight',"8")
    @selenium.click "is_active" 
    
    #Submit the form 
    @page.clickAndWait "_qf_Gender_next"
    assert @selenium.is_text_present("The Gender \"G2\" has been saved.")
  end
  
  # Disable Gender type
  def test_3_disableGender
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.clickAndWait "link=Disable"
    assert_equal "Are you sure you want to disable this Gender?\n\nUsers will no longer be able to select this value when adding or editing Gender.", @selenium.get_confirmation()
  end
  
  # Enable Gender type
  def test_4_enableGender
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.clickAndWait "link=Enable"
  end
  
  # Delete Gender type
  def test_5_deleteGender
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.clickAndWait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Gender related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.clickAndWait "_qf_Gender_next"
    assert @selenium.is_text_present("Selected Gender type has been deleted.")
  end
end
