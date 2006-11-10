# This is a test case of using Selenium and its Ruby bindings
# Information' Membership Types definition
# This test case allows you to add/edit/disable/enable/delete Membership Types information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminMembershipType < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_membership
    move_to_membership_type_page()

    add_membership_type()
    edit_membership_type()
    disable_membership_type()
    enable_membership_type()
    delete_membership_type()
  end

  def move_to_membership_type_page
    # Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Membership Types
    assert_equal "Membership\nTypes", @selenium.get_text("//a[@id='id_MembershipTypes']")
    @page.click_and_wait "//a[@id='id_MembershipTypes']"
  end
  
  # Add new Membership Types information
  def add_membership_type
    if @selenium.is_text_present("There are no custom membership types entered. You can add one.")
      @page.click_and_wait "link=add one"
    else
      @selenium.get_text("link=» New Membership Type")
      @page.click_and_wait "link=» New Membership Type"
    end
    
    # Read new Membership information
    
    @selenium.type   "name", "New Membership"
    @selenium.type   "description", "testing membersip type"
    @selenium.type   "minimum_fee", "100"
    @selenium.select "contribution_type_id", "label=Campaign Contribution"
    @selenium.type   "duration_interval", "1"
    @selenium.select "duration_unit", "label=year"
    @selenium.select "period_type", "label=rolling"
    # @selenium.select "relationship_type_id", "label=Employee of"
    @selenium.type   "weight", "4"
    
    @selenium.select "visibility", "label=Public"
    @page.click_and_wait  "//div[@id='membership_type_form']/descendant::input[@id='_qf_MembershipType_refresh']"
          
    # Submit the form 
    @page.click_and_wait "_qf_MembershipType_next"
    assert @selenium.is_text_present("The membership type \"New Membership\" has been saved.")
  end
  
  # Edit Membership type
  def edit_membership_type
     assert @selenium.is_element_present("//div[@id='membership_type']/descendant::tr[td[contains(.,'New Membership')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='membership_type']/descendant::tr[td[contains(.,'New Membership')]]/descendant::a[contains(.,'Edit')]"
      
    @selenium.select "period_type", "label=fixed"
    @selenium.select "fixed_period_start_day[d]", "label=15"
    @selenium.select "fixed_period_start_day[M]", "label=Apr"
    @selenium.select "fixed_period_rollover_day[M]", "label=Mar"
    @selenium.select "fixed_period_rollover_day[d]", "label=30"
    
    #Submit the form
    @page.click_and_wait "_qf_MembershipType_next"
  end
  
  # Disable Membership type
  def disable_membership_type
    assert_equal "Disable", @selenium.get_text("//div[@id='membership_type']/descendant::tr[td[contains(.,'New Membership')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='membership_type']/descendant::tr[td[contains(.,'New Membership')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this membership type?", @selenium.get_confirmation()
  end
  
  # Enable Membership type
  def enable_membership_type
    assert_equal "Enable", @selenium.get_text("//div[@id='membership_type']/descendant::tr[td[contains(.,'New Membership')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='membership_type']/descendant::tr[td[contains(.,'New Membership')]]/descendant::a[contains(.,'Enable')]"
  end

  def delete_membership_type
    assert_equal "Delete", @selenium.get_text("//div[@id='membership_type']/descendant::tr[td[contains(.,'New Membership')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='membership_type']/descendant::tr[td[contains(.,'New Membership')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all membership records of this type. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait  "_qf_MembershipType_next"
  end
end
