# This is a test case of using Selenium and its Ruby bindings
# Information' Payment instrument definition
# This test case allows you to add/edit/disable/enable/delete payment instrument information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminPaymentInstrument < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Payment Instrument
    assert_equal "Payment\nInstruments", @selenium.get_text("//a[@id='id_PaymentInstruments']")
    @page.click_and_wait "//a[@id='id_PaymentInstruments']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new Payment Instruments information
  def test_1_addPayment
    @page.click_and_wait "link=» New Payment Instrument"
    
    # Read new Payment Instruments information
    @selenium.type "name", "aPayment1"
    @selenium.type "description", "testing payment instruments"
    @selenium.click "is_active"
    
    # Submit the form 
    @page.click_and_wait "_qf_PaymentInstrument_next"
    assert @selenium.is_text_present("The Payment Instrument \"aPayment1\" has been saved.")
  end
  
  # Editing Payment Instruments information
  def test_2_editPayment
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_PaymentInstrument_next"
    assert @selenium.is_text_present("The Payment Instrument \"aPayment1\" has been saved.")
  end
    
  # Enable Payment Instruments
  def test_3_enablePayment
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end

  # Disable Payment Instruments
  def test_4_disablePayment
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this Payment Instrument?", @selenium.get_confirmation()
  end
  
  # Delete Payment Instruments
  def test_5_deletePayment
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all contribution records which use this option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_PaymentInstrument_next"
    assert @selenium.is_text_present("Selected Payment Instrument has been deleted.")
  end
end
