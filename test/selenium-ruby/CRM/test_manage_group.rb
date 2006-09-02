#!/usr/bin/env ruby
# This is a test case of using Selenium and its Ruby bindings
# Information' Gender definition

require 'crm_page_controller'
require '../selenium'
 
class TC_TestManageGroup < Test::Unit::TestCase
  
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.click_and_wait "link=CiviCRM"
  end
  
  def teardown
    @page.logout
  end
  
  def test_add_group
    assert_equal "New Group", @selenium.get_text("link=New Group")
    @page.click_and_wait "link=Manage Groups"
    assert @selenium.is_text_present("Manage Groups")
    assert_equal "» New Group", @selenium.get_text("link=» New Group")
    @page.click_and_wait "link=» New Group"
    assert @selenium.is_text_present("Create New Group")
    @selenium.type "title", "Test Group"
    @selenium.type "description", "This is test group"
    @selenium.select "visibility", "label=Public User Pages and Listings"
    @page.click_and_wait "//input[@type='submit' and @value='Continue']"
    assert @selenium.is_text_present("The Group \"Test Group\" has been saved.")
  end
  
end
