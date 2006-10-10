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
  end
  
  def teardown
    @page.logout
  end
  
  def test_group
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.click_and_wait "link=CiviCRM"
    
    add_group()
    settings_group()
    disable_group()
    enable_group()
    delete_group()
  end
  
  def add_group
    @page.click_and_wait "link=Manage Groups"
    assert @selenium.is_text_present("Manage Groups")
    
    assert_equal "» New Group", @selenium.get_text("link=» New Group")
    @page.click_and_wait "link=» New Group"
    assert @selenium.is_text_present("Create New Group")
    @selenium.type "title", "Insert Group"
    @selenium.type "description", "This is test group"
    @selenium.select "visibility", "label=Public User Pages"
    @page.click_and_wait "//input[@type='submit' and @value='Continue']"
    assert @selenium.is_text_present("The Group \"Insert Group\" has been saved.")
  end
  
  def settings_group
    @page.click_and_wait "link=Manage Groups"
    assert @selenium.is_text_present("Manage Groups")
    
    assert @selenium.is_text_present("Insert Group")
    @page.click_and_wait "//div[@id='group']/descendant::tr[td[contains(.,'Insert Group')]]/descendant::a[contains(.,'Settings')]"
    assert @selenium.is_text_present("Group Settings: Insert Group")
    @selenium.select "visibility", "label=Public User Pages and Listings"
    @page.click_and_wait "//div[@id='group']/descendant::input[@type='submit' and @value='Save']"
    assert @selenium.is_text_present("The Group \"Insert Group\" has been saved.")
  end
  
  def disable_group
    @page.click_and_wait "link=Manage Groups"
    assert @selenium.is_text_present("Manage Groups")
    
    assert_equal "Disable", @selenium.get_text("//div[@id='group']/descendant::tr[td[contains(.,'Insert Group')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='group']/descendant::tr[td[contains(.,'Insert Group')]]/descendant::a[contains(.,'Disable')]"
    assert_equal 'Are you sure you want to disable this Group?' ,@selenium.get_confirmation()
  end
  
  def enable_group
    assert_equal "Enable", @selenium.get_text("//div[@id='group']/descendant::tr[td[contains(.,'Insert Group')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='group']/descendant::tr[td[contains(.,'Insert Group')]]/descendant::a[contains(.,'Enable')]"
  end
  
  def delete_group
    assert @selenium.is_text_present("Insert Group")
    @page.click_and_wait "//div[@id='group']/descendant::tr[td[contains(.,'Insert Group')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("Are you sure you want to delete the group Insert Group?")
    @page.click_and_wait "//input[@type='submit' and @value='Delete Group']"
    assert @selenium.is_text_present("The Group \"Insert Group\" has been deleted.")
  end
end
