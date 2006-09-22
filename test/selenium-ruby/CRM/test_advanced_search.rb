# This is a test case of using Selenium and its Ruby bindings
# Information' find contacts using advanced search option
# This test case allows you to perform operations by finding contacts 

require 'crm_page_controller'
require '../selenium'
require 'common_search_cases'

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
    
    # Find Contacts with Given Search criteria 
    
    #Search with multiple groups
    search_groups()
    
    #Search with multiple tags
    search_tags()
    
    #Search with location
    search_location()
    
    #Search with Activity type
    search_activity_history()
    
    #Search with Scheduled Activities
    search_scheduled_activities()
    
    #Search with change log
    search_change_log()
    
    #Search with Voters info
    search_voter_info()

    #Search with Contribution
    search_contribution()

    #Search with Membership
    search_membership()

    #Search with Multiple options
    search_with_multiple_options_1()
    search_with_multiple_options_2()
    search_with_multiple_options_3()

    #Search with Relationship type
    search_relationship_type()
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

  def print_result(search_query)
    #print search criteria
    puts "Search Criteria  : " + search_query
    print("*****************************************\n")
    print("Result of Test => \n")
    print("*****************************************\n")
    
    if @selenium.is_element_present("//div[@id='search-status']")
      # print the result on command prompt
      print(@selenium.get_text("//div[@id='search-status']"))
    else
      #check if no contact found
      print(@selenium.get_text("//div[@class='messages status']"))
    end
    print("\n***************************************\n\n")
  end

  def search_groups()
    # search through multiple groups
    @selenium.check "group[3]"
    @selenium.check "group[2]"
    
    #submit form
    search_click()

    #print the result on command prompt
    search_query = "Search Contacts where Groups= Advisory Board and Summer Program Volunteers" 
    print_result(search_query)
  end

  def search_tags
    #search through multiple tags
    @selenium.check "tag[3]"
    @selenium.check "tag[4]"

    #submit form
    search_click()

    #print the result on command prompt
    search_query = "Search Contacts where, Tags= Government Entity and Major doner"
    print_result(search_query)
  end

  def search_location
    #select location
    @selenium.click "//a[@onclick=\"hide('location_show'); show('location'); return false;\"]"
    @selenium.select "state_province", "label=California"
    @selenium.select "country", "label=United States"

    #submit form
    search_click()

    #print the result on command prompt
    search_query = "Search Contacts where, state=California and country=United States"
    print_result(search_query)
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
    search_query = "Search Contacts where, Activity History Type= Meeting with Date= From: 15/01/2005 To: 15/08/2006"
    print_result(search_query)
  end

  def search_scheduled_activities
    #select an activity and date
    @selenium.click "//a[@onclick=\"hide('openAtcivity_show'); show('openAtcivity'); return false;\"]"
    @selenium.select "open_activity_type_id", "label=Phone Call"
    @selenium.select "open_activity_date_low[M]", "label=May"
    @selenium.select "open_activity_date_low[d]", "label=08"
    @selenium.select "open_activity_date_low[Y]", "label=2006"
    @selenium.select "open_activity_date_high[M]", "label=Sep"
    @selenium.select "open_activity_date_high[Y]", "label=2006"
    @selenium.select "open_activity_date_high[d]", "label=22"

    search_query = "Search Contacts where, Activity Type= Phone Call with Date= From: 08/05/2006 To: 22/09/2006"
    #submit form
    search_click()

    #print the result on command prompt
    print_result(search_query)
  end

  def search_change_log
    @selenium.click "//a[@onclick=\"hide('changelog_show'); show('changelog'); return false;\"]"
    @selenium.type "changed_by", "Smith"
    @selenium.select "modified_date_low[M]", "label=Jul"
    @selenium.select "modified_date_low[d]", "label=10"
    @selenium.select "modified_date_low[Y]", "label=2006"
    @selenium.select "modified_date_high[M]", "label=Aug"
    @selenium.select "modified_date_high[d]", "label=10"
    @selenium.select "modified_date_high[Y]", "label=2006"

    #submit form
    search_click()

    #print the result on command prompt
    search_query = "Search Contacts where, Changes logged by= Smith with Date= From: 10/07/2006 To: 10/08/2006"
    print_result(search_query)
  end
  
  def search_voter_info
    @selenium.check "custom_1"
    @selenium.select "custom_3[M]", "label=Mar"
    @selenium.select "custom_3[d]", "label=25"
    @selenium.select "custom_3[Y]", "label=2006"
    @selenium.check "custom_5"
    @selenium.check "custom_6[PB]"
    @selenium.check "custom_6[SB]"
    @selenium.select "custom_7", "label=Single"

    #submit form
    search_click()

    #print the result on command prompt
    search_query = "Search Contacts where, Voters info= Registered voter, voted on 25/03/2006, having most important issue=education and GOTV Experience = Phone Banking, Speakers Bureau"
    print_result(search_query)
  end
  
  def search_relationship_type
    @selenium.click "//a[@onclick=\"hide('relationship_show'); show('relationship'); return false;\"]"

    @selenium.select "relation_type_id", "label=Employee of"
    @selenium.type "relation_target_name", "Cisco Systems"

    #submit form
    search_click()

    #print the result on command prompt
    search_query = "Search Contacts where, Relationship type=Employee of, in relation to Cisco Systems"
    print_result(search_query)
  end
  
  def search_contribution
    #add details 
    @contribution = CRMContributionSearchDetails.new
    @contribution.searchContribution @selenium
    
    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts with given contribution criteria"
    print_result(search_query)
  end

  def search_membership
    #add details
    @membership = CRMMembershipSearchDetails.new
    @membership.searchMember @selenium
    
    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts with given membership criteria "
    print_result(search_query)
  end
  
  def search_with_multiple_options_1
    @selenium.check "group[3]"
    @selenium.check "group[2]"
    @selenium.check "tag[4]"
    @selenium.check "contact_type[Individual]"
    @selenium.click "//a[@onclick=\"hide('location_show'); show('location'); return false;\"]"
    @selenium.select "country", "label=United States"
    @selenium.check "__privacy[do_not_phone]"
    @selenium.check "__privacy[do_not_mail]"

    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts Where,Groups=Advisory Board and Summer Program Volunteers, Tags=Major Donor, Employee of cisco systems, Country = United States"
    print_result(search_query)
  end

  def search_with_multiple_options_2
    @selenium.check "contact_type[Individual]"
    @selenium.type "activity_type", "Meeting"
    @selenium.select "custom_7", "label=Single"
    @selenium.check "member_membership_type_id[1]"
    @selenium.check "member_status_id[2]"

    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts Where,Activity type=Meeting, Marital status=single,Membership type=General, Memebrship status= General"
    print_result(search_query)
  end

  def search_with_multiple_options_3
    @selenium.type "contribution_amount_low", "100"
    @selenium.select "contribution_type_id", "label=Campaign Contribution"
    @selenium.select "contribution_payment_instrument_id", "label=Cash"
    @selenium.check "contribution_status"
    @selenium.check "contact_type[Individual]"
    @selenium.check "tag[2]"

    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts Where,Minimum contribution amount=100, contribution type=Campaign contribution, payment type=cash"
    print_result(search_query)
  end

end
