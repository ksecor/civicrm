# This is a test case of using Selenium and its Ruby bindings
# Information' find contact
# This test case allows you to perform operations by finding contacts 

require 'crm_page_controller'
require '../selenium'

class TC_TestFindContacts < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_find_contacts
    move_to_find_contact()
    
    search()
    search_by_name()
    search_by_email()
    search_individual()
    search_individual_and_group()
    search_individual_and_tag()
    search_group_and_tag()
    search_individual_all_criteria()
    search_household()
    search_organization()

  end
  
  def move_to_find_contact
    # Click find contacts link
    assert @selenium.is_text_present("Find Contacts")
    assert_equal "Find Contacts", @selenium.get_text("link=Find Contacts")
    @page.click_and_wait "link=Find Contacts"
  end
  
  def search_click
    #click search link
    assert_equal "Search", @selenium.get_value("//div[@id='searchForm']/descendant::input[@type='submit' and @name='_qf_Search_refresh']")
    @page.click_and_wait "//div[@id='searchForm']/descendant::input[@type='submit' and @name='_qf_Search_refresh']"
  end

  def print_result
    if @selenium.get_text("//div[@id='search-status']")
      # print the result on command prompt
      print(@selenium.get_text("//div[@id='search-status']"))
    else
      #check if no contact found
      @selenium.is_element_present("//div[@class='messages status']")
      print(@selenium.get_text("//div[@class='messages status']"))
    end
  end
  
  def search()
    #click search link
    search_click()

    #print the result on command prompt
    print_result()
  end

  def search_by_name
    #search by name
    @selenium.select "contact_type", "label=Individuals"
    @selenium.type "//div[@id='searchForm']/descendant::input[@type='text' and @name='sort_name']", "smith"
    
    #click search link
    search_click()
    
    #print the result on command prompt
    print_result()
  end

  def search_by_email
    #search by email address
    @selenium.type "sort_name", "MichelleSmithshome@indiatimes.co.in"
    
    #click search link
    search_click()

    #print the result on command prompt
    print_result()
  end

  def search_individual
    #select Individual
    @selenium.select "contact_type", "label=Individuals"
        
    #click search link
    search_click()

    #print the result on command prompt
    print_result()
  end

  def search_individual_and_group
    @selenium.select "contact_type", "label=Individuals"
    #group name
    @selenium.select "group", "label=Advisory Board"

    #click search link
    search_click()

    #print the result on command prompt
    print_result()
  end

  def search_individual_and_tag
    @selenium.select "contact_type", "label=Individuals"
    #tag name
    @selenium.select "tag", "label=Company"
    
    #click search link
    search_click()

    #print the result on command prompt
    print_result()
  end
  
  def search_group_and_tag()
    #tag name
    @selenium.select "tag", "label=Major Donor"
    #group name
    @selenium.select "group", "label=Advisory Board"
    
    #click search link
    search_click()

    #print the result on command prompt
    print_result()
  end

  def search_individual_all_criteria
    @selenium.select "contact_type", "label=Organizations"
    @selenium.select "group", "label=Newsletter Subscribers"
    @selenium.select "tag", "label=Major Donor"
    
    #click search link
    search_click()
    
    #print the result on command prompt
    assert @selenium.is_element_present("//div[@class='messages status']")
    print @selenium.get_text("//div[@class='messages status']")
  #  print_result()
  end
  
  def search_household
    #search household
    @selenium.select "contact_type", "label=Households"
    
    #click search link
    search_click()

    #print the result on command prompt
    print_result()
  end
  
  def search_organization
    #select Individual
    @selenium.select "contact_type", "label=Organizations"
    
    #click search link
    search_click()
   
    #print the result on command prompt
    print_result()
  end
end
