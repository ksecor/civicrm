# This is a test case of using Selenium and its Ruby bindings
# Information' Tag definition
# This test case allows you to add/edit/disable/enable/delete tag information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminTag < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_tag
    move_to_admin_tag()
    add_tag()
    edit_tag()
    delete_tag()
  end
  
  def move_to_admin_tag
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Tags
    assert_equal "Tags\n(Categories)", @selenium.get_text("//a[@id='id_Tags_Categories']")
    @page.click_and_wait "//a[@id='id_Tags_Categories']"
    
    assert @selenium.is_text_present('Tags (Categories)')
  end
  
  # Add new tag information
  def add_tag
    assert_equal "» New Tag", @selenium.get_text("link=» New Tag")
    @page.click_and_wait "link=» New Tag"
    
    # Read new tag information
    @selenium.type "name",       "New Tag"
    @selenium.type "description","Tag Created for Testing"
      
    # Submit the form 
    @page.click_and_wait "_qf_Tag_next"
    assert @selenium.is_text_present("The tag \"New Tag\" has been saved.")
  end
  
  # Editing tag information
  def edit_tag
    assert_equal "Edit", @selenium.get_text("//div[@id='cat']/descendant::tr[td[contains(.,'New Tag')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='cat']/descendant::tr[td[contains(.,'New Tag')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.type "description","Tag Created for Testing.. Edited Successfully"
    
    #Submit the form 
    @page.click_and_wait "_qf_Tag_next"
    assert @selenium.is_text_present("The tag \"New Tag\" has been saved.")
  end
  
  # Delete tag
  def delete_tag
    assert_equal "Edit", @selenium.get_text("//div[@id='cat']/descendant::tr[td[contains(.,'New Tag')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='cat']/descendant::tr[td[contains(.,'New Tag')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("Are you sure you want to delete New Tag Tag?")
    @page.click_and_wait "_qf_Tag_next"
    assert @selenium.is_text_present("Selected Tag has been Deleted Successfuly.")
  end
end
