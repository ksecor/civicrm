# This is a test case of using Selenium and its Ruby bindings
# Information' Duplicate Matching definition
# This test case allows you to add/edit/disable/enable/delete duplicate matching information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminDuplicateMatching < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Duplicate Matching
    assert_equal "Duplicate\nMatching",@selenium.get_text("//a[@id='id_DuplicateMatching']")
    @page.click_and_wait "//a[@id='id_DuplicateMatching']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Rule
  def test_1_addRule
    @page.click_and_wait "link=Edit Rule"
    
    # Read new rule
    @selenium.select "match_on_1", "label=First Name"
    @selenium.select "match_on_2", "label=Middle Name"
    
    # Submit the form 
    @page.click_and_wait "_qf_DupeMatch_next"
    assert @selenium.is_text_present("The Duplicate Matching rule has been saved.")
  end
end
