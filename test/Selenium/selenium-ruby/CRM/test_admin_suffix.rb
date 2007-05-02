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
    go_to_suffix_option()
    
    add_suffix()
    edit_suffix()
    enable_suffix()
    disable_suffix()
    delete_suffix()
  end
  
  #click suffix link
  def go_to_suffix_option
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Individual Suffix
    assert_equal "Individual\nSuffixes\n(Jr,\nSr...)", @selenium.get_text("//a[@id='id_IndividualSuffixes_Jr_Sr...']")
    @page.click_and_wait "//a[@id='id_IndividualSuffixes_Jr_Sr...']"
  end

  # Add new Suffix
  def add_suffix
    if @selenium.is_text_present("There are no option values entered. You can add one")
      @page.click_and_wait "link=add one"
    else
      assert_equal "» New Individual Suffix", @selenium.get_text("link=» New Individual Suffix")
      @page.click_and_wait "link=» New Individual Suffix"    
    end
        
    # Read new Suffix information
    @selenium.type "label",       "New Suffix"
    @selenium.type "weight",     "2"
    @selenium.check "is_active" 
    
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Individual Suffix \"New Suffix\" has been saved.")
  end
  
  # Editing Suffix information
  def edit_suffix
    assert_equal "Edit", @selenium.get_text("//div[@id='individual_suffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='individual_suffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Edit')]"
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Individual Suffix \"New Suffix\" has been saved.")
  end

   # Enable Suffix
  def enable_suffix
    assert_equal "Enable", @selenium.get_text("//div[@id='individual_suffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='individual_suffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Enable')]"
  end

  # Disable Suffix
  def disable_suffix
    assert_equal "Disable", @selenium.get_text("//div[@id='individual_suffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='individual_suffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Individual Suffix?\n\nUsers will no longer be able to select this value when adding or editing Individual Suffix.", @selenium.get_confirmation()
  end
    
  # Delete Suffix
  def delete_suffix
    assert_equal "Delete", @selenium.get_text("//div[@id='individual_suffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='individual_suffix']/descendant::tr[td[contains(.,'New Suffix')]]/descendant::a[contains(.,'Delete')]"

    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Individual Suffix related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("Selected Individual Suffix type has been deleted.")
  end
end
