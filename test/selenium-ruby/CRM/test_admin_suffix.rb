# This is a test case of using Selenium and its Ruby bindings
# Information' Suffix definition
# This test case allows you to add/edit/disable/enable/delete suffix information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminSuffix < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_suffix
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Individual Suffix
    assert_equal "Individual\nSuffixes\n(Jr,\nSr...)", @selenium.get_text("//a[@id='id_IndividualSuffixes_Jr_Sr...']")
    @page.click_and_wait "//a[@id='id_IndividualSuffixes_Jr_Sr...']"
    
    assert @selenium.is_text_present('Individual Suffixes (Jr, Sr...)')
    
    add_suffix()
    edit_suffix()
    enable_suffix()
    disable_suffix()
    delete_suffix()
  end
  
  # Add new Suffix
  def add_suffix
    @page.click_and_wait "link=» New Individual Suffix Option"
    
    # Read new Suffix information
    @selenium.type "name",       "New Suffix"
    @selenium.type "weight",     "2"
    @selenium.check "is_active" 
    
    # Submit the form 
    @page.click_and_wait "_qf_IndividualSuffix_next"
    assert @selenium.is_text_present("The Individual Suffix \"New Suffix\" has been saved.")
  end
  
  # Editing Suffix information
  def edit_suffix
    assert_equal "Edit", @selenium.get_text("//div[@id='isuffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='isuffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Edit')]"
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_IndividualSuffix_next"
    assert @selenium.is_text_present("The Individual Suffix \"New Suffix\" has been saved.")
  end

   # Enable Suffix
  def enable_suffix
    assert_equal "Enable", @selenium.get_text("//div[@id='isuffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='isuffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Enable')]"
  end

  # Disable Suffix
  def disable_suffix
    assert_equal "Disable", @selenium.get_text("//div[@id='isuffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='isuffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Individual Suffix?\n\nUsers will no longer be able to select this value when adding or editing Individual Suffix.", @selenium.get_confirmation()
  end
    
  # Delete Suffix
  def delete_suffix
    assert_equal "Delete", @selenium.get_text("//div[@id='isuffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='isuffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will change all Individual records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_IndividualSuffix_next"
    assert @selenium.is_text_present("Selected Individual Suffix has been deleted.")
  end
end
