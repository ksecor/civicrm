# This is a test case of using Selenium and its Ruby bindings
# Information' CiviContribute definition
# This test case allows you to perform operation on information provided

require 'crm_page_controller'
require '../selenium'

class TC_TestCiviContribute < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_civi_contribution
    move_to_civi_contribute()
    
    find_contribution()
  end
  
  def move_to_civi_contribute
    #Clicking CiviContribute
    assert_equal "CiviContribute", @selenium.get_text("link=CiviContribute")
    @page.click_and_wait "link=CiviContribute"
    assert @selenium.is_text_present("CiviContribute")    
  end
  
  def find_contribution
    #click find contribution link
    assert @selenium.is_text_present("Find Contributions")
    #    assert_equal "Find Contributions", @selenium.get_text("link=Find Contributions")
    @page.click_and_wait "link=Find Contributions"
    
    #search by name
    @selenium.type "contribution_amount_low", "50"
    
    #click search
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Search_refresh']"
  end
  
  def view_contribution
    #click on View link
    
    
  end
  
  def edit_contribution
    #click on edit link

  end

  def delete_contribution
    #click on delete link

  end
end
