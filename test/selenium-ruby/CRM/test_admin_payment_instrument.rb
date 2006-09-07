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
  end
  
  def teardown
    @page.logout
  end
  
  def test_payment_instrument
    # Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Clicking Payment Instrument
    assert_equal "Payment\nInstruments", @selenium.get_text("//a[@id='id_PaymentInstruments']")
    @page.click_and_wait "//a[@id='id_PaymentInstruments']"
    
    add_payment()
    edit_payment()
    enable_payment()
    disable_payment()
    delete_payment()
  end
  
  # Add new Payment Instruments information
  def add_payment
    @page.click_and_wait "link=» New Payment Instrument"
    
    # Read new Payment Instruments information
    @selenium.type  "name",        "Test Instrument"
    @selenium.type  "description", "This is payment instrument for testing purpose"
    @selenium.click "is_active"
    
    # Submit the form 
    @page.click_and_wait "_qf_PaymentInstrument_next"
    assert @selenium.is_text_present("The Payment Instrument \"Test Instrument\" has been saved.")
  end
  
  # Editing Payment Instruments information
  def edit_payment
    assert_equal "Edit", @selenium.get_text("//div[@id='payment_instrument_type']/descendant::tr[td[contains(.,'')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='payment_instrument_type']/descendant::tr[td[contains(.,'')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_PaymentInstrument_next"
    assert @selenium.is_text_present("The Payment Instrument \"Test Instrument\" has been saved.")
  end
    
  # Enable Payment Instruments
  def enable_payment
    assert_equal "Enable", @selenium.get_text("//div[@id='payment_instrument_type']/descendant::tr[td[contains(.,'')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='payment_instrument_type']/descendant::tr[td[contains(.,'')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Disable Payment Instruments
  def disable_payment
    assert_equal "Disable", @selenium.get_text("//div[@id='payment_instrument_type']/descendant::tr[td[contains(.,'')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='payment_instrument_type']/descendant::tr[td[contains(.,'')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Payment Instrument?", @selenium.get_confirmation()
  end
  
  # Delete Payment Instruments
  def delete_payment
    assert_equal "Delete", @selenium.get_text("//div[@id='payment_instrument_type']/descendant::tr[td[contains(.,'')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='payment_instrument_type']/descendant::tr[td[contains(.,'')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all contribution records which use this option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_PaymentInstrument_next"
    assert @selenium.is_text_present("Selected Payment Instrument has been deleted.")
  end
end
