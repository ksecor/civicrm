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
    move_to_civicrm()
    
    add_group()
    settings_group()
    disable_group()
    enable_group()
    #members()
    delete_group()
  end
  
  def move_to_civicrm
  assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
  @page.click_and_wait "link=CiviCRM"
  end

  def move_to_manage_group
    @page.click_and_wait "link=Manage Groups"
    assert @selenium.is_text_present("Manage Groups")
  end
  
  def add_group
    @page.click_and_wait "link=Manage Groups"
    assert @selenium.is_text_present("Manage Groups")
    
    assert_equal "» New Group", @selenium.get_text("link=» New Group")
    @page.click_and_wait "link=» New Group"
    assert @selenium.is_text_present("Create New Group")
    @selenium.type "title", "New Group"
    @selenium.type "description", "This is test group"
    @selenium.select "visibility", "label=Public User Pages"
    @page.click_and_wait "//input[@type='submit' and @value='Continue']"
    if @selenium.is_text_present("The Group \"New Group\" has been saved.")
      assert @selenium.is_text_present("The Group \"New Group\" has been saved.")
    else
      assert @selenium.is_text_present("Name already exists in Database.")
    end 
  end
  
  def settings_group
    
    move_to_manage_group()

    assert @selenium.is_text_present("New Group")

    @page.click_and_wait "//div[@id='group']/descendant::tr[td[contains(.,'New Group')]]/descendant::a[contains(.,'Settings')]"
    assert @selenium.is_text_present("Group Settings: New Group")
    @selenium.select "visibility", "label=Public User Pages and Listings"
    @page.click_and_wait "//div[@id='group']/descendant::input[@type='submit' and @value='Save']"
    assert @selenium.is_text_present("The Group \"New Group\" has been saved.")
  end
  
  def disable_group

    move_to_manage_group()

    assert_equal "Disable", @selenium.get_text("//div[@id='group']/descendant::tr[td[contains(.,'New Group')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='group']/descendant::tr[td[contains(.,'New Group')]]/descendant::a[contains(.,'Disable')]"
    assert_equal 'Are you sure you want to disable this Group?' ,@selenium.get_confirmation()
  end
  
  def enable_group
    assert_equal "Enable", @selenium.get_text("//div[@id='group']/descendant::tr[td[contains(.,'New Group')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='group']/descendant::tr[td[contains(.,'New Group')]]/descendant::a[contains(.,'Enable')]"
  end
  
  def delete_group
    assert @selenium.is_text_present("New Group")
    @page.click_and_wait "//div[@id='group']/descendant::tr[td[contains(.,'New Group')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("Are you sure you want to delete the group New Group?")
    @page.click_and_wait "//input[@type='submit' and @value='Delete Group']"
    assert @selenium.is_text_present("The Group \"New Group\" has been deleted.")
  end

  def members
    assert_equal "Members", @selenium.get_text("//div[@id='group']/descendant::tr[td[contains(.,'New Group')]]/descendant::a[contains(.,'Members')]")
    @page.click_and_wait "//div[@id='group']/descendant::tr[td[contains(.,'New Group')]]/descendant::a[contains(.,'Members')]"
    if @selenium.is_text_present("New Group has no members which match your search criteria. You can add members here.")
      @page.click_and_wait "link=add members here."
    else
      @page.click_and_wait "link=» Add Members to New Group"
    end
    
    assert @selenium.is_text_present("Add Members: New Group")
    
    @page.click_and_wait("//div[@id='searchForm']/descendant::input[@type='submit' and @value='Search']")
    
    if @selenium.is_element_present("//input[@type='checkbox' and @name='toggleSelect']")
      @selenium.check "//input[@type='checkbox' and @name='toggleSelect']"
      @page.click_and_wait "//input[@type='submit' and @id='_qf_Search_next_action']"
      @page.click_and_wait "//input[@type='submit' and @id='_qf_AddToGroup_next']"
      @page.click_and_wait "//input[@type='submit' and @id='_qf_Result_done']"
    end
  end

end
