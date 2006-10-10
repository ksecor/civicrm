# This is a test case of using Selenium and its Ruby bindings
# Information' Activity definition
# This test case allows you to add/edit/disable/enable/delete activity information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminActivity < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_activity
    move_to_admin_activity()
    
    add_activity()
    edit_activity()
    enable_activity()
    disable_activity()
    delete_activity()
  end
  
  def move_to_admin_activity
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Activity Type
    assert_equal "Activity\nTypes", @selenium.get_text("//a[@id='id_ActivityTypes']")
    @page.click_and_wait "//a[@id='id_ActivityTypes']"
  end
  
  # Add new Activity information
  def add_activity
    if @selenium.is_text_present("There are no custom Activity Types entered")
      @page.click_and_wait "link=add one"
    else
      @page.click_and_wait "link=» New Activity Type Option"
    end
    
    # Read new activity information
    @selenium.type "name","New Activity"
    @selenium.check "is_active" 
    
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Activity Type \"New Activity\" has been saved.")
  end
  
  # Editing activity information
  def edit_activity
    assert_equal "Edit", @selenium.get_text("//div[@id='activity_type']/descendant::tr[td[contains(.,'New Activity')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='activity_type']/descendant::tr[td[contains(.,'New Activity')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Activity Type \"New Activity\" has been saved.")
  end
  
  # Enable activity
  def enable_activity
    assert_equal "Enable", @selenium.get_text("//div[@id='activity_type']/descendant::tr[td[contains(.,'New Activity')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='activity_type']/descendant::tr[td[contains(.,'New Activity')]]/descendant::a[contains(.,'Enable')]"
  end

  # Disable activity
  def disable_activity
     assert_equal "Disable", @selenium.get_text("//div[@id='activity_type']/descendant::tr[td[contains(.,'New Activity')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='activity_type']/descendant::tr[td[contains(.,'New Activity')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Activity Type?\n\nUsers will no longer be able to select this value when adding or editing Activity Type.", @selenium.get_confirmation()
  end
  
  # Delete activity
  def delete_activity
    assert_equal "Delete", @selenium.get_text("//div[@id='activity_type']/descendant::tr[td[contains(.,'New Activity')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='activity_type']/descendant::tr[td[contains(.,'New Activity')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Activity Type related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("Selected Activity Type type has been deleted.")
  end
end
