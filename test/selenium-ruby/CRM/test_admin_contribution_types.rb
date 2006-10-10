# This is a test case of using Selenium and its Ruby bindings
# Information' Contribution Types definition
# This test case allows you to add/edit/disable/enable/delete contribution information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminContributionTypes < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_contribution
    move_to_contribution_types()

    add_contribution_types()
    edit_contribution_types()
    enable_contribution_types()
    disable_contribution_types()
    delete_contribution_types()
  end
  
  def move_to_contribution_types
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #click Contribution types link
    assert_equal "Contribution\nTypes", @selenium.get_text("//a[@id='id_ContributionTypes']")
    @page.click_and_wait "//a[@id='id_ContributionTypes']"
  end

  # Add new Contribution Type information
  def add_contribution_types
    @page.click_and_wait "link=» New Contribution Type"
    
    # Read new Contribution Type information
    @selenium.type "name", "New Contribution"
    @selenium.type "description", "testing contribution"
    @selenium.click "is_deductible"
    @selenium.check "is_active"
    
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_ContributionType_next']"
    assert @selenium.is_text_present("The contribution type \"New Contribution\" has been saved.")
  end
  
  # Editing Contribution Type information
  def edit_contribution_types
    assert_equal "Edit", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Contribution')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Contribution')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.type "description", "testing contribution types"
    @selenium.uncheck "is_active"
    
    #Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_ContributionType_next']"
    assert @selenium.is_text_present("The contribution type \"New Contribution\" has been saved.")
  end
  
  # Enable Contribution types
  def enable_contribution_types
    assert_equal "Enable", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Contribution')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Contribution')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Disable Contribution types
  def disable_contribution_types
    assert_equal "Disable", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Contribution')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Contribution')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this contribution type?", @selenium.get_confirmation()
  end
    
  # Delete Contribution types
  def delete_contribution_types
    assert_equal "Delete", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Contribution')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Contribution')]]/descendant::a[contains(.,'Delete')]"
    
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all contribution records of this type. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_ContributionType_next']"
    assert @selenium.is_text_present("Selected contribution type has been deleted.")
  end
end
