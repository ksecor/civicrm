# This is a test case of using Selenium and its Ruby bindings
# Information' Gender definition
# This test case allows you to add/edit/disable/enable/delete gender information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminGender < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_gender
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    assert_equal "Gender\nOptions\n(Male,\nFemale...)", @selenium.get_text("//a[@id='id_GenderOptions_Male_Female...']")
    @page.click_and_wait "//a[@id='id_GenderOptions_Male_Female...']"
    
    add_gender()
    edit_gender()
    disable_gender()
    enable_gender()
    delete_gender()
  end
  
  # Add new Gender information
  def add_gender
    if @selenium.is_text_present("There are no option values entered. You can add one.")
      @page.click_and_wait "link=add one"
    else
      assert_equal "» New Gender", @selenium.get_text("link=» New Gender")
      @page.click_and_wait "link=» New Gender"
    end
    
    # Read new Gender information
    @selenium.type  "label",  "New Gender"
    @selenium.type  "weight", "2"
    
    if @selenium.get_value("//input[@type='checkbox' and @name='is_active']") == 'on'
      @selenium.uncheck "//input[@type='checkbox' and @name='is_active']"
    end
        
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Gender \"New Gender\" has been saved.")
  end
  
  # Editing gender information
  def edit_gender
    assert_equal "Edit", @selenium.get_text("//div[@id='gender']/descendant::tr[td[contains(.,'New Gender')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='gender']/descendant::tr[td[contains(.,'New Gender')]]/descendant::a[contains(.,'Edit')]"
    
    if @selenium.get_value("//input[@type='checkbox' and @name='is_active']") == 'off'
      @selenium.check "//input[@type='checkbox' and @name='is_active']"
    end
    
    #Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Gender \"New Gender\" has been saved.")
  end
  
  # Disable Gender type
  def disable_gender
    assert_equal "Disable", @selenium.get_text("//div[@id='gender']/descendant::tr[td[contains(.,'New Gender')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='gender']/descendant::tr[td[contains(.,'New Gender')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Gender?\n\nUsers will no longer be able to select this value when adding or editing Gender.", @selenium.get_confirmation()
  end
  
  # Enable Gender type
  def enable_gender
    assert_equal "Enable", @selenium.get_text("//div[@id='gender']/descendant::tr[td[contains(.,'New Gender')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='gender']/descendant::tr[td[contains(.,'New Gender')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Delete Gender type
  def delete_gender
    assert_equal "Delete", @selenium.get_text("//div[@id='gender']/descendant::tr[td[contains(.,'New Gender')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='gender']/descendant::tr[td[contains(.,'New Gender')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Gender related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("Selected Gender type has been deleted.")
  end
end
