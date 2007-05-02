# This is a test case of using Selenium and its Ruby bindings
# Information' find contribution
# This test case allows you to perform operations by finding contributions 

require 'crm_page_controller'
require '../selenium'

class TC_TestFindMembership < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end

  def test_find_membership
    move_to_find_membership()
    find_membership_test1()
    move_to_find_membership()
    find_membership_test2()
    move_to_find_membership()
    find_membership_test3()
  end
  
  def move_to_find_membership
    #click on CiviMemebrship
    assert @selenium.is_text_present("CiviMember")
    @page.click_and_wait "link=CiviMember"

    #click Find Membership
    assert @selenium.is_text_present("Find Members")
    @page.click_and_wait "link=Find Members"
  end
  
  def search_click
    #click search button
    assert_equal "Search", @selenium.get_value("//input[@type='submit' and @name='_qf_Search_refresh']")    
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Search_refresh']"
  end

  def find_membership_test1
    @selenium.check "member_membership_type_id[1]"
    @selenium.check "member_status_id[2]"
    @selenium.check "member_status_id[1]"
    @selenium.type "member_source", "Donation"
    @selenium.select "member_start_date_low[M]", "label=Jan"
    @selenium.select "member_start_date_low[d]", "label=01"
    @selenium.select "member_start_date_low[Y]", "label=2005"
    @selenium.select "member_start_date_high[M]", "label=Dec"
    @selenium.select "member_start_date_high[d]", "label=31"
    @selenium.select "member_start_date_high[Y]", "label=2006"
    @selenium.select "member_end_date_low[M]", "label=Dec"
    @selenium.select "member_start_date_high[M]", "label=Jan"
    @selenium.select "member_start_date_high[d]", "label=30"
    @selenium.select "member_end_date_low[d]", "label=31"
    @selenium.select "member_end_date_low[Y]", "label=2006"
    @selenium.select "member_end_date_high[M]", "label=Dec"
    @selenium.select "member_end_date_high[d]", "label=31"
    @selenium.select "member_end_date_high[Y]", "label=2006"
    
    #click search 
    search_click()
  end
  
  def find_membership_test2
    @selenium.select "member_start_date_low[M]", "label=Jan"
    @selenium.select "member_start_date_low[d]", "label=01"
    @selenium.select "member_start_date_low[Y]", "label=2000"
    @selenium.select "member_start_date_high[M]", "label=Jan"
    @selenium.select "member_start_date_high[d]", "label=31"
    @selenium.select "member_start_date_high[Y]", "label=2000"
    @selenium.select "member_end_date_low[M]", "label=Dec"
    @selenium.select "member_end_date_low[d]", "label=01"
    @selenium.select "member_end_date_low[Y]", "label=2005"
    @selenium.select "member_end_date_high[M]", "label=Dec"
    @selenium.select "member_end_date_high[d]", "label=31"
    @selenium.select "member_end_date_high[Y]", "label=2005"

    #click search 
    search_click()
  end

  def find_membership_test3
    @selenium.check "member_status_id[2]"
    @selenium.check "member_status_id[4]"
    @selenium.check "member_membership_type_id[1]"
    @selenium.check "member_membership_type_id[3]"

    #click search 
    search_click()
  end
end
