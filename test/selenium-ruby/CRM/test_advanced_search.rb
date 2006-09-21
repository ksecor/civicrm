# This is a test case of using Selenium and its Ruby bindings
# Information' find contacts using advanced search option
# This test case allows you to perform operations by finding contacts 

require 'crm_page_controller'
require '../selenium'

class TC_TestAdvancedSearch < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_advanced_search
    move_to_advanced_search()
    
    #search with multiple groups
   # search_groups()
   # search_tags()
   # search_location()
    search_activity_history()
  end
  
  def move_to_advanced_search
    # Click find contacts link
    assert @selenium.is_text_present("Find Contacts")
    assert_equal "Find Contacts", @selenium.get_text("link=Find Contacts")
    @page.click_and_wait "link=Find Contacts"

    #Click Advanced search link
    assert_equal "Advanced Search", @selenium.get_text("link=Advanced Search")
    @page.click_and_wait "link=Advanced Search"
  end
  
  def search_click
    #click search button
    assert_equal "Search", @selenium.get_value("//div[@id='searchForm']/descendant::input[@type='submit' and @name='_qf_Advanced_refresh']")    
    @page.click_and_wait "//div[@id='searchForm']/descendant::input[@type='submit' and @name='_qf_Advanced_refresh']"
  end

  def print_result
    if @selenium.get_text("//div[@id='search-status']")
      # print the result on command prompt
      print(@selenium.get_text("//div[@id='search-status']"))
    else
      #check if no contact found
      # @selenium.is_element_present("//div[@class='messages status']")
      print(@selenium.get_text("//div[@class='messages status']"))
    end
  end

  def search_groups()
    # search through multiple groups
    @selenium.check "group[3]"
    @selenium.check "group[2]"
    
    #submit form
    search_click()

    #print the result on command prompt
    print_result()
  end

  def search_tags
    #search through multiple tags
    @selenium.check "tag[3]"
    @selenium.check "tag[4]"

    #submit form
    search_click()

    #print the result on command prompt
    print_result()
  end

  def search_location
    #select location
    @selenium.click "//a[@onclick=\"hide('location_show'); show('location'); return false;\"]"
    @selenium.select "state_province", "label=California"
    @selenium.select "country", "label=United States"

    #submit form
    search_click()

    #print the result on command prompt
    print_result()
  end
 
  def search_activity_history
    #select an activity and date
    @selenium.type "activity_type", "Meeting"
    @selenium.select "activity_date_low[M]", "label=Jan"
    @selenium.select "activity_date_low[d]", "label=15"
    @selenium.select "activity_date_low[Y]", "label=2005"
    @selenium.select "activity_date_high[M]", "label=Aug"
    @selenium.select "activity_date_high[d]", "label=15"
    @selenium.select "activity_date_high[Y]", "label=2006"

    #submit form
    search_click()

    #print the result on command prompt
    print_result()
  end

end
