# This is a test case of using Selenium and its Ruby bindings
# Information' Domain Information definition
# This test case allows you to add/edit/disable/enable/delete domain information information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminRelationshipType < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_domain_information
    #Click CiviCRM
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.click_and_wait "link=CiviCRM"
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Edit Domain Information
    assert_equal "Edit\nDomain\nInformation", @selenium.get_text("//a[@id='id_EditDomainInformation']")
    @page.click_and_wait "//a[@id='id_EditDomainInformation']"
    
    edit_domain_information()
  end   
  
  # Edit Domain Information
  def edit_domain_information
    # Read domain information
    @selenium.type "description", "New Domain"
    
    # Submit the form 
    @page.click_and_wait "_qf_Domain_next_view"
    assert @selenium.is_text_present("The Domain \"Domain Name 1\" has been saved.")
  end
end
