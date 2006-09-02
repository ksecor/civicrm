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
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Tags
    assert_equal "Tags\n(Categories)", @selenium.get_text("//a[@id='id_Tags_Categories']")
    @page.click_and_wait "//a[@id='id_Tags_Categories']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new tag information
  def test_1_addTag
    assert_equal "» New Tag", @selenium.get_text("link=» New Tag")
    @page.click_and_wait "link=» New Tag"
    
    # Read new tag information
    @selenium.type "name",       "aTag1"
    @selenium.type "description","Testing tags"
      
    # Submit the form 
    @page.click_and_wait "_qf_Tag_next"
    assert @selenium.is_text_present("The tag \"aTag1\" has been saved.")
  end
  
  # Editing tag information
  def test_2_editTag
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"

    @selenium.type "description","Testing working of Tags"
    
    #Submit the form 
    @page.click_and_wait "_qf_Tag_next"
    assert @selenium.is_text_present("The tag \"aTag1\" has been saved.")
  end
  
  # Delete tag
  def test_3_deleteTag
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("Are you sure you want to delete aTag1 Tag?")
    @page.click_and_wait "_qf_Tag_next"
    assert @selenium.is_text_present("Selected Tag has been Deleted Successfuly.")
  end
end
