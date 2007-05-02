# This is a test case of using Selenium and its Ruby bindings
# Information' Preferred communication method definition
# This test case allows you to perform operations on information given

require 'crm_page_controller'
require '../selenium'

class TC_TestPrefCommMethod < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_pref_comm_method
    move_to_pref_comm_method()
    
    add_pref_comm_method()
    edit_pref_comm_method()
    enable_pref_comm_method()
    disable_pref_comm_method()
    delete_pref_comm_method()
  end
  
  def move_to_pref_comm_method
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Preferred communication method
    assert_equal "Preferred\nCommunication\nOptions", @selenium.get_text("//a[@id='id_PreferredCommunicationOptions']")
    @page.click_and_wait "//a[@id='id_PreferredCommunicationOptions']"
  end
  
  # Add new Communication method information
  def add_pref_comm_method
    if @selenium.is_text_present("There are no option values entered. You can add one")
      @page.click_and_wait "link=add one"
    else
      assert @selenium.is_text_present("» New Preferred Communication Method")
      @page.click_and_wait "link=» New Preferred Communication Method"
    end
            
    # Read method information
    @selenium.type "label", "testMethod"
        
    # Submit the form 
    @page.click_and_wait "_qf_Options_next"
    assert @selenium.is_text_present("The Preferred Communication Method \"testMethod\" has been saved.")
  end
  
  # Editing communication method information
  def edit_pref_comm_method
    assert_equal "Edit", @selenium.get_text("//div[@id='preferred_communication_method']/descendant::tr[td[contains(.,'testMethod')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='preferred_communication_method']/descendant::tr[td[contains(.,'testMethod')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_Options_next"
    assert @selenium.is_text_present("The Preferred Communication Method \"testMethod\" has been saved.")
  end
  
  # Enable communication method
  def enable_pref_comm_method
    assert_equal "Enable", @selenium.get_text("//div[@id='preferred_communication_method']/descendant::tr[td[contains(.,'testMethod')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='preferred_communication_method']/descendant::tr[td[contains(.,'testMethod')]]/descendant::a[contains(.,'Enable')]"
  end
  # Disable activity
  def disable_pref_comm_method
    assert_equal "Disable", @selenium.get_text("//div[@id='preferred_communication_method']/descendant::tr[td[contains(.,'testMethod')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='preferred_communication_method']/descendant::tr[td[contains(.,'testMethod')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Preferred Communication Method?\n\nUsers will no longer be able to select this value when adding or editing Preferred Communication Method.", @selenium.get_confirmation()
  end
  
  # Delete communication method
  def delete_pref_comm_method
    assert_equal "Delete", @selenium.get_text("//div[@id='preferred_communication_method']/descendant::tr[td[contains(.,'testMethod')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='preferred_communication_method']/descendant::tr[td[contains(.,'testMethod')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Preferred Communication Method related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
   
  end
end
