#!/usr/bin/env ruby
# This is a test case of using Selenium and its Ruby bindings
# Information' New Group definition

require 'crm_page_controller'
require '../selenium'
 
class TC_TestNewGroup < Test::Unit::TestCase
  
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_new_group
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.click_and_wait "link=CiviCRM"
    
    add_group()
  end
  
  def add_group
    assert_equal "New Group", @selenium.get_text("link=New Group")
    @page.click_and_wait "link=New Group"
    assert @selenium.is_text_present("New Group")
    
    #add details
    @selenium.type "title", "New Group"
    @selenium.type "description", "Group created for testing"
    @selenium.select "visibility", "label=Public User Pages"

    #submit form
    @page.click_and_wait "//input[@type='submit' and @value='Continue']"
    if @selenium.is_text_present("The Group \"New Group\" has been saved.")
      assert @selenium.is_text_present("The Group \"New Group\" has been saved.")
    else
      assert @selenium.is_text_present("Name already exists in Database.")
    end 
  end

end
