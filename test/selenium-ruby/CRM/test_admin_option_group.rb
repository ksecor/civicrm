# This is a test case of using Selenium and its Ruby bindings
# Information' option group definition
# This test case allows you to add/edit/disable/enable/delete option group information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminOptionGroup < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_options
    
    move_to_admin_options()
    
    add_option()
    
    multiple_choice_option()
    add_multiple_choice_option()
    edit_multiple_choice
    disable_multiple_choice
    enable_multiple_choice
    delete_multiple_choice
    
    move_to_admin_options()
    edit_option_group()
    enable_option_group()
    disable_option_group()
    delete_option_group()
    
    # multiple_choice_option()
    
  end
  
  def move_to_admin_options
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Options
    assert_equal "Options", @selenium.get_text("link=Options")
    @page.click_and_wait "link=Options"
  end
  
  # Add new Option information
  def add_option
    assert_equal "» New Option Group", @selenium.get_text("link=» New Option Group")
    @page.click_and_wait "link=» New Option Group"
    
    # Read new option information
    @selenium.type  "name",        "New Option Group"
    @selenium.type  "description", "This is test option group"
    @selenium.check "is_active"
    
    # Submit the form 
    @page.click_and_wait "_qf_OptionGroup_next"
    assert @selenium.is_text_present("The Option Group \"New Option Group\" has been saved.")
  end
  
  # Add Multiple choice options
  def multiple_choice_option
    assert_equal "Multiple Choice Options", @selenium.get_text("//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Multiple Choice Options')]")
    @page.click_and_wait "//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Multiple Choice Options')]"
  end
  
  # add a multiple choice option
  def add_multiple_choice_option
    @page.click_and_wait "link=add one"
    assert @selenium.is_element_present("//input[@type='text' and @id='label']")
    
    # Read new multiple choice information
    @selenium.type  "//input[@type='text' and @id='label']", "First Choice"
    @selenium.type  "//input[@type='text' and @id='value']", "01"
    @selenium.type  "//input[@type='text' and @id='name']",  "New option choice one"
    @selenium.type  "description",                           "This is first choice for testing"
    @selenium.type  "weight",                                "3"
    @selenium.check "is_optgroup"
    
    # Submit the form 
    @page.click_and_wait "_qf_OptionValue_next"
    assert @selenium.is_text_present("The Option Value \"First Choice\" has been saved.")
  end
  
  # edit multiple choice option
  def edit_multiple_choice
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    
    # change option value
    @selenium.type  "description", "This is first choice for testing.. Edited"
    @selenium.type "grouping",     "test option group"
    @page.click_and_wait "_qf_OptionValue_next"
    assert @selenium.is_text_present("The Option Value \"First Choice\" has been saved.")
  end
  
  # disable multiple choice option
  def disable_multiple_choice
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this Option Value?", @selenium.get_confirmation()
  end
  
  # enable multiple choice option
  def enable_multiple_choice
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end
  
    # delete multiple choice option
  def delete_multiple_choice
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this option value will result in the loss of all records which use the option value. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "//input[@type='submit' and @value='Delete']"
    assert @selenium.is_text_present("Selected option value has been deleted.")
  end
  
  # Editing option group information
  def edit_option_group
    assert_equal "Edit Group", @selenium.get_text("//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Edit Group')]")
    @page.click_and_wait "//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Edit Group')]"
    
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_OptionGroup_next"
    assert @selenium.is_text_present("The Option Group \"New Option Group\" has been saved.")
  end
    
  # Enable option group
  def enable_option_group
    assert_equal "Enable", @selenium.get_text("//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Disable option group
  def disable_option_group
    assert_equal "Disable", @selenium.get_text("//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Option?", @selenium.get_confirmation()
  end
  
  # Delete option group
  def delete_option_group
    assert_equal "Delete", @selenium.get_text("//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='browseValues']/descendant::tr[td[contains(.,'New Option Group')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option gruop will result in the loss of all records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_OptionGroup_next"
    assert @selenium.is_text_present("Selected option group has been deleted.")
  end
end
