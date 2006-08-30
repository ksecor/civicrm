#!/usr/bin/env ruby
# This is a test case of using Selenium and its Ruby bindings
# Information' Gender definition

require 'test/unit'
require 'crm_page_controller'
require '../selenium'
 
class TestManageGroup < Test::Unit::TestCase
  
  def setup
    @page = CRMPageController.new
    @selenium = @page.startCivicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_addGroup
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.clickAndWait "link=CiviCRM"
    assert_equal "New Group", @selenium.get_text("link=New Group")
    @page.clickAndWait "link=New Group"
    assert @selenium.is_text_present("Create New Group")
  end
  
end
