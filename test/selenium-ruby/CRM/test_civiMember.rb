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
    move_to_civicrm()
    move_to_civiMember()
    find_members()
    move_to_civiMember()
    view_members()
  end
  
  def move_to_civicrm
    #click civicrm link
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.click_and_wait "link=CiviCRM"
  end
  
  def move_to_civiMember
    #click CiviMember
    assert_equal "CiviMember", @selenium.get_text("link=CiviMember")
    @page.click_and_wait "link=CiviMember"
  end
  
  def find_members
    #click find members
    assert_equal "Find Members", @selenium.get_text("link=Find Members")
    @page.click_and_wait "link=Find Members"

    @selenium.check "member_status_id[2]"
    @selenium.check "member_status_id[4]"
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Search_refresh']"
  end
  def view_members
    assert @selenium.is_text_present("Cisco Systems")
  end
end
