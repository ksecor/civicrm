# This is a test case of using Selenium and its Ruby bindings
# Information' find participants
# This test case allows you to perform operations by finding contacts 

require 'crm_page_controller'
require '../selenium'

class TC_TestEventFindParticipants < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_event_find_participant
    move_to_find_participant( )
    
    search( )
    move_to_find_participant( )
    search_by_name( )
    move_to_find_participant( )
    search_by_date( )
    move_to_find_participant( )
    search_test_participant( )
    move_to_find_participant( )
    search_participant( )
  end
  
  def move_to_find_participant
    # Click CiviEvent link
    assert_equal "CiviEvent", @selenium.get_text("link=CiviEvent")
    @page.click_and_wait "link=CiviEvent"
    
    #Find Participant
    assert_equal "Find Participants", @selenium.get_text("link=Find Participants")
    @page.click_and_wait "link=Find Participants"
  end
  
  def search_click
    #click search link
    assert_equal "Search", @selenium.get_value("//input[@type='submit' and @name='_qf_Search_refresh']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Search_refresh']"
  end

  def print_result
    if @selenium.is_element_present("//div[@id='search-status']")
      # print the result on command prompt
      print(@selenium.get_text("//div[@id='search-status']"))
      print("\n")
    else
      #check if no contact found
      print(@selenium.get_text("//div[@class='messages status']"))
      print("\n")
    end
  end
  
  def search( )
    #click search link
    search_click( )

    #print the result on command prompt
    print_result( )
  end

  def search_by_name
    #search by name
    @selenium.type "sort_name", "zope"
    
    #click search link
    search_click( )
    
    #print the result on command prompt
    print_result( )
  end

  def search_by_date
    #search by date
    @selenium.select "event_start_date_low[M]", "label=Jan"
    @selenium.select "event_start_date_low[d]", "label=10"
    @selenium.select "event_start_date_low[Y]", "label=2004"
    @selenium.select "event_end_date_high[M]", "label=Aug"
    @selenium.select "event_end_date_high[d]", "label=15"
    @selenium.select "event_end_date_high[Y]", "label=2007"
    
    #click search link
    search_click( )

    #print the result on command prompt
    print_result( )
  end

  def search_test_participant
    #search test participant
    @selenium.check "//input[@type='checkbox' and @name='event_participant_test']"
    
    #click search link
    search_click( )
    
    #print the result on command prompt
    print_result( )
  end
  
  def search_participant
    @selenium.type "sort_name", "zope"
    @selenium.check "//input[@type='checkbox' and @name='event_participant_status[4]' and @value='1']"

    @selenium.select "event_start_date_low[M]", "label=Jan"
    @selenium.select "event_start_date_low[d]", "label=10"
    @selenium.select "event_start_date_low[Y]", "label=2004"
    @selenium.select "event_end_date_high[M]", "label=Aug"
    @selenium.select "event_end_date_high[d]", "label=15"
    @selenium.select "event_end_date_high[Y]", "label=2007"
    
    #click search link
    search_click( )
    
    #print the result on command prompt
    print_result( )
  end
end
