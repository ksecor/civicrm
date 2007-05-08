# This is a test case of using Selenium and its Ruby bindings
# Information' Paypal API Profile definition
# This test case allows you to add/edit/disable/enable/delete Paypal API Profile information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminPaypalAPIProfile < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
   
  end
  
  def teardown
    @page.logout
  end
  
 def test_paypal_API_Profile
   #Click CiviCRM
   assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
   @page.click_and_wait "link=CiviCRM"

   #Click Administer CiviCRM
   assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
   @page.click_and_wait "link=Administer CiviCRM"

   #Click Paypal API Profile
   assert_equal "Create\nPayPal\nAPI\nProfile", @selenium.get_text("//a[@id='id_CreatePayPalAPIProfile']")
   @page.click_and_wait "//a[@id='id_CreatePayPalAPIProfile']"

   create_paypal_API_Profile()
 end   
  
  # Create Paypal API Profile
  def create_paypal_API_Profile

    # Read new Relationship information
    @selenium.select "api_environment", "label=sandbox"
    @selenium.type "api_username", "Abhilasha Vau"
    @selenium.type "","/opt/PayPal/87019dc7af7ab414ae1239ddc85c6cc6.ppd"
    @selenium.type "uploadFile", "/opt/PayPal/87019dc7af7ab414ae1239ddc85c6cc6.cert"
    @selenium.type "api_subject", "API"
    
    # Submit the form 
    @page.click_and_wait "_qf_CreatePPD_upload"
  end
end
