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
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Accept Credit card
    assert_equal "Accepted\nCredit\nCards", @selenium.get_text("//a[@id='id_AcceptedCreditCards']")
    @page.click_and_wait "//a[@id='id_AcceptedCreditCards']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Credit Card information
  def test_1_addCreditCard
    @page.click_and_wait "link=» New Credit Card"
    
    # Read new Credit Card information
    @selenium.type "name", "aCreditCard1"
    @selenium.type "title", "ACC"
    @selenium.click "is_active"
    
    # Submit the form 
    @page.click_and_wait "_qf_AcceptCreditCard_next"
    assert @selenium.is_text_present("The Credit Card \"aCreditCard1\" has been saved.")
  end
  
  # Editing Credit Card information
  def test_2_editCreditCard
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_AcceptCreditCard_next"
    assert @selenium.is_text_present("The Credit Card \"aCreditCard1\" has been saved.")
  end
    
  # Enable Credit Card
  def test_3_enableCreditCard
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end

  # Disable Credit Card
  def test_4_disableCreditCard
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this Credit Card? Your contributors will no longer be able to use this card type for online contributions.", @selenium.get_confirmation()
  end
  
  # Delete Credit Card
  def test_5_deleteCreditCard
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: If you delete this option, contributors will not be able to use this credit card type on your Online Contribution pages. Do you want to continue?")
    @page.click_and_wait "_qf_AcceptCreditCard_next"
    assert @selenium.is_text_present("Selected Credit Card has been deleted.")
  end
end
