# This is a test case of using Selenium and its Ruby bindings
# Information' find contribution
# This test case allows you to perform operations by finding contributions 

require 'crm_page_controller'
require '../selenium'

class TC_TestFindContribution < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_find_contribution
    move_to_find_contribution()
    find_contribution()
  end
  
  def move_to_find_contribution
    #click on CiviContribute
    @selenium.get_text("link=CiviContribute")
    @page.click_and_wait "link=CiviContribute"

    #click Find Contribution
    #assert @selenium.is_text_present("Find Contributions")
    assert_equal "Find Contributions", @selenium.get_text("link=Find Contributions")
    @page.click_and_wait "link=Find Contributions"
        
  end
  
  def find_contribution
    @selenium.select "contribution_date_low[M]", "label=May"
    @selenium.select "contribution_date_low[d]", "label=16"
    @selenium.select "contribution_date_low[Y]", "label=2005"
    @selenium.select "contribution_date_high[M]", "label=Jun"
    @selenium.select "contribution_date_high[d]", "label=27"
    @selenium.select "contribution_date_high[Y]", "label=2006"
    @selenium.type "contribution_amount_low", "50"
    @selenium.type "contribution_amount_high", "150"
    @selenium.select "contribution_type_id", "label=Donation"
    @selenium.select "contribution_payment_instrument_id", "label=CreditCard"
    
    #click search 
    assert_equal "Search", @selenium.get_value("//input[@type='submit' and @name='_qf_Search_refresh']")    
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Search_refresh']"
  end
end
