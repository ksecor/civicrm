# This is a test case of using Selenium and its Ruby bindings
# Information' Profile definition
# This test case allows you to add/edit/disable/enable/delete profile information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminProfile < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    assert_equal "CiviCRM\nProfile", @selenium.get_text("//a[@id='id_CiviCRMProfile']")
    @page.click_and_wait "//a[@id='id_CiviCRMProfile']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Profile information
  def test_1_addProfile
    @page.click_and_wait "link=» New CiviCRM Profile"
    
    # Read new Profile information
    @selenium.type "title", "profile1"
    @selenium.click "document.Group.elements['uf_group_type[User Registration]']"
    @selenium.click "document.Group.elements['uf_group_type[User Account]']"
    @selenium.click "uf_group_type[Profile]"
    @selenium.click "document.Group.elements['uf_group_type[Search Profile]']"
    @selenium.type  "weight", "0"
    @selenium.select "group", "label=Advisory Board"
    @selenium.select "add_contact_to_group", "label=Newsletter Subscribers"
    @selenium.type "help_pre", "Pre-Form Help"
    @selenium.type "help_post", "Post-Form Help"
    @selenium.type "post_URL", ""
    @selenium.type "cancel_URL", ""
    @selenium.click "add_captcha"
    @selenium.click "is_map"
    @selenium.click "collapse_display"
    @selenium.click "is_active"
   
    # Submit the form 
     @page.click_and_wait "_qf_Group_next"
    assert @selenium.is_text_present("Your CiviCRM Profile Group \"profile1\" has been added. You can add fields to this group now.")
  end

  # Enable Profile
  def test_4_enableProfile
    @page.click_and_wait "link=Enable"
  end
  
  # Disable Profile
  def test_4_disableProfile
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this CiviCRM Profile group?", @selenium.get_confirmation
  end

  # Delete Profile
  def test_5_deleteProfile
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("Delete \"profile1\" profile?")
    @page.click_and_wait "_qf_Group_next"
    assert @selenium.is_text_present("Your CiviCRM profile group \"profile1\"has been deleted.")
  end
end
