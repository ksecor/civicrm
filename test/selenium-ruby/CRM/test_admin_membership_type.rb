# This is a test case of using Selenium and its Ruby bindings
# Information' Membership Types definition
# This test case allows you to add/edit/disable/enable/delete Membership Types information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminMembershipType < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    # Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Membership Types
    assert_equal "Membership\nTypes", @selenium.get_text("//a[@id='id_MembershipTypes']")
    @page.click_and_wait "//a[@id='id_MembershipTypes']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Membership Types information
  def test_1_add_membership_type
    @page.click_and_wait "link=» New Membership Type"
    
    # Read new Membership information
    @selenium.type "name", "aMembership1"
    @selenium.type "description", "new Membership Type" 
    @selenium.type "member_org", "Cisco Systems"
    # assert_equal "Search Again", @selenium.get_value("_qf_MembershipType_refresh")
    # @selenium.type "//input[@id=member_org]", "Cisco Systems"
    @selenium.click "_qf_MembershipType_refresh"
    # @selenium.click "document.getElementsByName('contact_check')[0]"
    @selenium.check "contact_check"
    @selenium.type "minimum_fee", "100"
    @selenium.select "contribution_type_id", "label=Campaign Contribution"
    @selenium.type "duration_interval", "1"
    @selenium.select "duration_unit", "label=year"
    @selenium.select "period_type", "label=rolling"
    @selenium.select "relationship_type_id", "label=Employee of"
    @selenium.type "weight", "0"
    @selenium.select "visibility", "label=Public"
    @selenium.click "is_active"

    # Submit the form 
    @page.click_and_wait "_qf_MembershipType_next"
    assert @selenium.is_text_present("The membership type \"Membership1\" has been saved.")
  end
  
 
end
