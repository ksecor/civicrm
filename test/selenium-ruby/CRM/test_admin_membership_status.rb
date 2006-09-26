# This is a test case of using Selenium and its Ruby bindings
# Information' Membership Status definition
# This test case allows you to add/edit/disable/enable/delete Membership Status information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminMembershipStatus < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_membership_status
    move_to_membership_status()

    #add_membership_status()
    #edit_membership_status()
    disable_membership_status()
    enable_membership_status()
    delete_membership_status()
  end

  def move_to_membership_status
    # Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Membership Types
    assert_equal "Membership\nStatus\nRules", @selenium.get_text("//a[@id='id_MembershipStatusRules']")
    @page.click_and_wait "//a[@id='id_MembershipStatusRules']"
  end

  # Add new Membership Status information
  def add_membership_status
    @page.click_and_wait "link=» New Membership Status"
    
    # Read new Membership information
    @selenium.type "name", "New Membership Status"
    @selenium.select "start_event", "label=start date"
    @selenium.type "start_event_adjust_interval", "1"
    @selenium.select "start_event_adjust_unit", "label=year"
    @selenium.select "end_event", "label=end date"
    @selenium.type "end_event_adjust_interval", "1"
    @selenium.select "end_event_adjust_unit", "label=year"
    @selenium.click "is_current_member"
    @selenium.click "is_admin"
    @selenium.type "weight", "6"
    @selenium.click "is_default"
       
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_MembershipStatus_next']"
    assert @selenium.is_text_present("The membership status \"New Membership Status\" has been saved.")
  end
 
  # Edit Membership Status information
  def edit_membership_status
    assert_equal "Edit", @selenium.get_text("//div[@id='membership_status_id']/descendant::tr[td[contains(.,'New Membership Status')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='membership_status_id']/descendant::tr[td[contains(.,'New Membership Status')]]/descendant::a[contains(.,'Edit')]"
   
    @selenium.type "weight", "0"
    @selenium.click "is_admin"

    @page.click_and_wait "//input[@type='submit' and @name='_qf_MembershipStatus_next']"
    assert @selenium.is_text_present("The membership status \"New Membership Status\" has been saved.")
  end

  # Disble Membership Status information
  def disable_membership_status
    assert_equal "Disable", @selenium.get_text("//div[@id='membership_status_id']/descendant::tr[td[contains(.,'New Membership Status')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='membership_status_id']/descendant::tr[td[contains(.,'New Membership Status')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this membership type?", @selenium.get_confirmation()
  end

  # Enable Membership Status information
  def enable_membership_status
    assert_equal "Enable", @selenium.get_text("//div[@id='membership_status_id']/descendant::tr[td[contains(.,'New Membership Status')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='membership_status_id']/descendant::tr[td[contains(.,'New Membership Status')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Enable Membership Status information
  def delete_membership_status
    # Clicking Delete membership status
    assert_equal "Delete", @selenium.get_text("//div[@id='membership_status_id']/descendant::tr[td[contains(.,'New Membership Status')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='membership_status_id']/descendant::tr[td[contains(.,'New Membership Status')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all membership records of this status. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_MembershipStatus_next']"
    assert @selenium.is_text_present("Selected membership status has been deleted.")
  end
end
