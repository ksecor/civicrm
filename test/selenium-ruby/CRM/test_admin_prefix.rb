# This is a test case of using Selenium and its Ruby bindings
# Information' Prefix definition
# This test case allows you to add/edit/disable/enable/delete prefix information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminPrefix < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end

  def test_prefix
    move_to_prefix_option()

    add_prefix()
    edit_prefix()
    enable_prefix()
    disable_prfix()
    delete_prefix()
  end
  
  #click prefix option link
  def move_to_prefix_option
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Individual prefixes
    assert_equal "Individual\nPrefixes\n(Ms,\nMr...)", @selenium.get_text("//a[@id='id_IndividualPrefixes_Ms_Mr...']")
    @page.click_and_wait "//a[@id='id_IndividualPrefixes_Ms_Mr...']"
  end

  # Add new Prefix
  def add_prefix
    @page.click_and_wait "link=» New Individual Prefix Option"
    
    # Read new Prefix information
    @selenium.type "name",       "Lt."
    @selenium.type "weight",     "2"
    @selenium.check "is_active" 
    
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Individual Prefix \"Lt.\" has been saved.")
  end
  
  # Editing Prefix information
  def edit_prefix
    assert_equal "Edit", @selenium.get_text("//div[@id='individual_prefix']/descendant::tr[td[contains(.,'Lt.')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='individual_prefix']/descendant::tr[td[contains(.,'Lt.')]]/descendant::a[contains(.,'Edit')]"
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Individual Prefix \"Lt.\" has been saved.")
  end

   # Enable prefix
  def enable_prefix
    assert_equal "Enable", @selenium.get_text("//div[@id='individual_prefix']/descendant::tr[td[contains(.,'Lt.')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='individual_prefix']/descendant::tr[td[contains(.,'Lt.')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Disable Prefix
  def disable_prfix
    assert_equal "Disable", @selenium.get_text("//div[@id='individual_prefix']/descendant::tr[td[contains(.,'Lt.')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='individual_prefix']/descendant::tr[td[contains(.,'Lt.')]]/descendant::a[contains(.,'Disable')]"

    assert_equal "Are you sure you want to disable this Individual Prefix?\n\nUsers will no longer be able to select this value when adding or editing Individual Prefix.", @selenium.get_confirmation( )
  end
  
  # Delete Prefix
  def delete_prefix
    assert_equal "Delete", @selenium.get_text("//div[@id='individual_prefix']/descendant::tr[td[contains(.,'Lt.')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='individual_prefix']/descendant::tr[td[contains(.,'Lt.')]]/descendant::a[contains(.,'Delete')]"
    
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Individual Prefix related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue? ")

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("Selected Individual Prefix type has been deleted.")
  end
end
