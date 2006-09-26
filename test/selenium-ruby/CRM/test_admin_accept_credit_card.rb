# This is a test case of using Selenium and its Ruby bindings
# Information' Accept Credit Card definition
# This test case allows you to add/edit/disable/enable/delete accept credit card information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminCreditCard < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_accept_credit_card
    move_to_accept_credit_card()
    
    add_credit_card()
    edit_credit_card()
    enable_credit_card()
    disable_credit_card()
    delete_credit_card()
  end

  def move_to_accept_credit_card
     #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Accept Credit card
    assert_equal "Accepted\nCredit\nCards", @selenium.get_text("//a[@id='id_AcceptedCreditCards']")
    @page.click_and_wait "//a[@id='id_AcceptedCreditCards']"
  end

  # Add new Credit Card information
  def add_credit_card
    assert_equal "» New Accept Creditcard Option", @selenium.get_text("link=» New Accept Creditcard Option")
    @page.click_and_wait "link=» New Accept Creditcard Option"
    
    # Read new Credit Card information
    @selenium.type "name", "New Credit Card"
    @selenium.check "is_active"
    
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Accept Creditcard \"New Credit Card\" has been saved.")
  end
  
  # Editing Credit Card information
  def edit_credit_card
     assert_equal "Edit", @selenium.get_text("//div[@id='accept_creditcard']/descendant::tr[td[contains(.,'New Credit Card')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='accept_creditcard']/descendant::tr[td[contains(.,'New Credit Card')]]/descendant::a[contains(.,'Edit')]"
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Accept Creditcard \"New Credit Card\" has been saved.")
  end
    
  # Enable Credit Card
  def enable_credit_card
    assert_equal "Enable", @selenium.get_text("//div[@id='accept_creditcard']/descendant::tr[td[contains(.,'New Credit Card')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='accept_creditcard']/descendant::tr[td[contains(.,'New Credit Card')]]/descendant::a[contains(.,'Enable')]"
  end

  # Disable Credit Card
  def disable_credit_card
    assert_equal "Disable", @selenium.get_text("//div[@id='accept_creditcard']/descendant::tr[td[contains(.,'New Credit Card')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='accept_creditcard']/descendant::tr[td[contains(.,'New Credit Card')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Accept Creditcard?\n\nUsers will no longer be able to select this value when adding or editing Accept Creditcard.", @selenium.get_confirmation()
  end
  
  # Delete Credit Card
  def delete_credit_card
     assert_equal "Delete", @selenium.get_text("//div[@id='accept_creditcard']/descendant::tr[td[contains(.,'New Credit Card')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='accept_creditcard']/descendant::tr[td[contains(.,'New Credit Card')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Accept Creditcard related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    
    assert @selenium.is_text_present("Selected Accept Creditcard type has been deleted.")
  end
end
