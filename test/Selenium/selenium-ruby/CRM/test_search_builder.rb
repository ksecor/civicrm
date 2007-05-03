# This is a test case of using Selenium and its Ruby bindings
# Information' find contacts using Search Builder option
# This test case allows you to perform operations by finding contacts 

require 'crm_page_controller'
require '../selenium'

class TC_TestSearchBuilder < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_search_builder
    move_to_search_builder()
    
    #Find Contacts with Given Search criteria 
    multiple_field_search()
    search_with_field_contribution()
    search_with_field_household()
    search_with_multiple_field_household()
    search_with_field_organization()
  end
  
  def move_to_search_builder
    # Click find contacts link
    assert @selenium.is_text_present("Find Contacts")
    assert_equal "Find Contacts", @selenium.get_text("link=Find Contacts")
    @page.click_and_wait "link=Find Contacts"

    #Click Search builder link
    assert_equal "Search Builder", @selenium.get_text("link=Search Builder")
    @page.click_and_wait "link=Search Builder"
  end
  
  def search_click
    #click search button
    assert_equal "Search", @selenium.get_value("//div[@id='crm-submit-buttons']/descendant::input[@type='submit' and @name='_qf_Builder_refresh']")
    @page.click_and_wait "//div[@id='crm-submit-buttons']/descendant::input[@type='submit' and @name='_qf_Builder_refresh']"
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

  def multiple_field_search
    @selenium.select "mapper[1][0][0]", "label=Individuals"
    @selenium.select "mapper[1][0][1]", "label=State"
    @selenium.select "operator[1][0]", "label=LIKE"
    @selenium.type "value[1][0]", "California"
    
    @page.click_and_wait "addMore[1]"

    @selenium.select "mapper[1][1][0]", "label=Individuals"
    @selenium.select "mapper[1][1][1]", "label=Country"
    @selenium.select "operator[1][1]", "label=LIKE"
    @selenium.type "value[1][1]", "United States"
    
    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts where City=California and Country= United States" 
    print_result(search_query)
  end
  
  def search_with_field_contribution
    @selenium.select "mapper[1][0][0]", "label=Contribution"
    @selenium.select "mapper[1][0][1]", "label=Contribution Type"
    @selenium.select "operator[1][0]", "label=="
    @selenium.type "value[1][0]", "Donation"
    
    @page.click_and_wait "addMore[1]"
    
    @selenium.select "mapper[1][1][0]", "label=Contribution"
    @selenium.select "mapper[1][1][1]", "label=Total Amount"
    @selenium.select "operator[1][1]", "label=="
    @selenium.type "value[1][1]", "250"
    
    @page.click_and_wait "addMore[1]"
    
    @selenium.select "mapper[1][2][0]", "label=Contribution"
    @selenium.select "mapper[1][2][1]", "label=Contribution Source"
    @selenium.select "operator[1][2]", "label=LIKE"
    @selenium.type "value[1][2]", "online"
    
    
    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts where Contribution type=Donation, Contribution Source=Online, Total Amount= $250 and Transaction ID=P20193L6"
    print_result(search_query)
  end

  def search_with_field_household
    @selenium.refresh()
    @selenium.select "mapper[1][0][0]", "label=Households"
    @selenium.select "operator[1][0]", "label=="
    @selenium.type "value[1][0]", "Sheila Roberts's home"

    @page.click_and_wait "addMore[1]"

    @selenium.select "mapper[1][1][0]", "label=Households"
    @selenium.select "mapper[1][1][1]", "label=Email"
    @selenium.select "mapper[1][1][2]", "label=Home"
    @selenium.select "operator[1][1]", "label=="
    @selenium.type "value[1][1]", "SheilaRobertsshome@amazon.net"

    @page.click_and_wait "addMore[1]"
    
    @selenium.select "mapper[1][2][0]", "label=Households"
    @selenium.select "mapper[1][2][1]", "label=Country"
    @selenium.select "mapper[1][2][2]", "label=Home"
    @selenium.select "operator[1][2]", "label=="
    @selenium.type "value[1][2]", "United States"
    
    @selenium.select "mapper[2][0][0]", "label=Households"
    @selenium.select "mapper[2][0][1]", "label=State"
    @selenium.select "mapper[2][0][2]", "label=Home"
    @selenium.select "operator[2][0]", "label=="
    @selenium.type "value[2][0]", "South Carolina"
    
    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts where Household information includes,Name=Sheila Roberts's home, Email=SheilaRobertsshome@amazon.net, State = South Carolina and Country=United States"
    print_result(search_query)
  end
  
  def search_with_field_organization
    @selenium.refresh()
    @selenium.select "mapper[1][0][0]", "label=Organizations"
    @selenium.select "operator[1][0]", "label=="
    @selenium.type "value[1][0]", "Magic Bus"
    
    #click 'Also include contacts where' link
    assert_equal "Also include contacts where", @selenium.get_value("//input[@id='addBlock']")
    @page.click_and_wait "//input[@id='addBlock']"
    
    @selenium.select "mapper[2][0][0]", "label=Organizations"
    @selenium.select "mapper[2][0][1]", "label=City"
    @selenium.select "mapper[2][0][2]", "label=Main"
    @selenium.select "operator[2][0]", "label=="
    @selenium.type "value[2][0]", "Crystal Lake"
    
     #click 'Also include contacts where' link
    assert_equal "Also include contacts where", @selenium.get_value("//input[@id='addBlock']")
    @page.click_and_wait "//input[@id='addBlock']"

    @selenium.select "mapper[3][0][0]", "label=Organizations"
    @selenium.select "mapper[3][0][1]", "label=Do Not Email"
    @selenium.select "operator[3][0]", "label=="
    @selenium.type "value[3][0]", "1"
    
    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts where organization information includes,Organization name=Magic Bus, City=Crystal Lake and Do not Email"
    print_result(search_query)
  end

  def search_with_multiple_field_household
    @selenium.refresh()
    @selenium.select "mapper[1][0][0]", "label=Households"
    @selenium.select "operator[1][0]", "label=="
    @selenium.type "value[1][0]", "Sheila Roberts's home"

    #click 'Also include contacts where' link
    assert_equal "Also include contacts where", @selenium.get_value("//input[@id='addBlock']")
    @page.click_and_wait "//input[@id='addBlock']"

    @selenium.select "mapper[2][0][0]", "label=Households"
    @selenium.select "mapper[2][0][1]", "label=State"
    @selenium.select "mapper[2][0][2]", "label=Home"
    @selenium.select "operator[2][0]", "label=="
    @selenium.type "value[2][0]", "South Carolina"

    #click 'Also include contacts where' link
    assert_equal "Also include contacts where", @selenium.get_value("//input[@id='addBlock']")
    @page.click_and_wait "//input[@id='addBlock']"

    @selenium.select "mapper[3][0][0]", "label=Households"
    @selenium.select "mapper[3][0][1]", "label=City"
    @selenium.select "mapper[3][0][2]", "label=Home"
    @selenium.select "operator[3][0]", "label=="
    @selenium.type "value[3][0]", "Crocketville"
    
    #submit form
    search_click()
    
    #print the result on command prompt
    search_query = "Search Contacts where Household information includes,Name=Sheila Roberts's home, State= South Carolina and City= Crocketville"
    print_result(search_query)
  end
end
