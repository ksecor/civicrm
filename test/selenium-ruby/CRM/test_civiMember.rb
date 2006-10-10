#!/usr/bin/env ruby
# This is a test case of using Selenium and its Ruby bindings
# Information' CiviMember definition

require 'crm_page_controller'
require '../selenium'
 
class TC_TestCiviMember < Test::Unit::TestCase
  
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_group
    move_to_civiMember()
    
  end
  
  def move_to_civiMemebr
    #click civicrm link
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.click_and_wait "link=CiviCRM"

    #click CiviMember
    assert_equal "CiviMember", @selenium.get_text("link=CiviMember")
    @page.click_and_wait "link=CiviMember"

    #click find members
    assert_equal "Find Members", @selenium.get_text("link=Find Members")
    @page.click_and_wait "link=Find Members"
    
  end
end
