# This is a test case of using Selenium and its Ruby bindings
# Information' option group definition
# This test case allows you to add/edit/disable/enable/delete option group information

require 'crm_page_controller'
require '../selenium'

class TestAdminOptionGroup < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Click Options
    assert_equal "Options", @selenium.get_text("link=Options")
    @page.click_and_wait "link=Options"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Option information
  def test_1_addOptions
    assert_equal "» New Option Group", @selenium.get_text("link=» New Option Group")
    @page.click_and_wait "link=» New Option Group"
    
    # Read new option information
    @selenium.type "name",        "opt1"
    @selenium.type "description", "Test options"
    @selenium.check "is_active" 
    
    # Submit the form 
    @page.click_and_wait "_qf_OptionGroup_next"
    assert @selenium.is_text_present("The option group \"opt1\" has been saved.")
  end
  
  # Add Multiple choice options
 def test_2_MultipleChoice
    assert_equal "Multiple Choice Options", @selenium.get_text("link=Multiple Choice Options")
    @page.click_and_wait "link=Multiple Choice Options"
   
    # add a multiple choice option
   def test_12_addMultipleOptions
     assert @selenium.is_text_present("There are no Option Value entered. You can add one.\n\n")
     @page.click_and_wait "link=add one"
     
     # Read new multiple choice information
     @selenium.type "label",       "choice1"
     @selenium.type "name",        "ch1"
     @selenium.type "grouping",    "opt1"
     @selenium.type "description", "first choice"
     @selenium.type "weight", "0"
     @selenium.click "is_default"
     @selenium.click "is_optgroup"
     
     # Submit the form 
     @page.click_and_wait "_qf_OptionValue_next"
     assert @selenium.is_text_present("The option value \"choice1\" has been saved.")
   end
   
   # edit multiple choice option
   def test__22_editMultipleChoice
     assert_equal "Edit", @selenium.get_text("link=Edit")
     @page.click_and_wait "link=Edit"
     
     # change option value
     @selenium.type "grouping", "optionGrp"
     @page.click_and_wait "_qf_OptionValue_next"
     assert @selenium.is_text_present("The option value \"choice1\" has been saved.")
   end
   
   # disable multiple choice option
   def test_32_disableMultipleChoice
     assert_equal "Disable", @selenium.get_text("link=Disable")
     @page.click_and_wait "link=Disable"
     assert_equal "Are you sure you want to disable this Option Value?", @selenium.get_confirmation()
   end
   
   # enable multiple choice option
   def test__42_enableMultipleChoice
     assert_equal "Enable", @selenium.get_text("link=Enable")
     @page.click_and_wait "link=Enable"
   end
   
   # delete multiple choice option
   def test__52_deleteMultipleChoice
     assert_equal "Delete", @selenium.get_text("link=Delete")
     @selenium.click "link=Delete"
     assert @selenium.is_text_present("WARNING: Deleting this option value will result in the loss of all records which use the option value. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue? ")
     @page.click_and_wait "_qf_OptionValue_next"
     assert @selenium.is_text_present("Selected option value has been deleted.")
   end
   # end of multiple choice operations
 end
 
 # Editing option group information
 def test_3_editOptions
   assert_equal "Edit Group", @selenium.get_text("link=Edit Group")
   @page.click_and_wait "link=Edit Group"
   
   @selenium.uncheck "is_active" 
   
   #Submit the form 
   @page.click_and_wait "_qf_OptionGroup_next"
   assert @selenium.is_text_present("The Option Group \"opt1\" has been saved.")
 end
 
 # Disable option group
 def test_4_disableOptions
   assert_equal "Disable", @selenium.get_text("link=Disable")
   @page.click_and_wait "link=Disable"
   assert_equal "Are you sure you want to disable this Option?", @selenium.get_confirmation()
 end
 
 # Enable option group
 def test_3_enableOptions
   assert_equal "Enable", @selenium.get_text("link=Enable")
   @page.click_and_wait "link=Enable"
 end
 
 # Delete option group
 def test_5_deleteOptions
   assert_equal "Delete", @selenium.get_text("link=Delete")
   @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
   @page.click_and_wait "_qf_OptionGroup_next"
   assert @selenium.is_text_present("Selected option group has been deleted.")
 end
end
