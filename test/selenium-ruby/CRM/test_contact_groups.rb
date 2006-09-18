# This is a test case of using Selenium and its Ruby bindings
# Information' groups definition
# This test case allows you to add groups and perform operations on information available

require 'crm_page_controller'
require '../selenium'


class TC_TestContactGroups < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_contact_groups
    #require 'test_new_individual'
    #add an Individual (using file test_new_individual)
    # @addIndividual = TC_TestNewIndividual.new

    # @addIndividual.go_to_new_individual
    # @addIndividual.add_new_individual

    #find a particular record
    search_individual()
 
    #select Relationship
    groups_click()
    
    add_group()
    remove_group()
    rejoin_group()
  end

  def search_individual
    #search for a particular individual
    assert_equal "Find Contacts", @selenium.get_text("link=Find Contacts")
    @page.click_and_wait "link=Find Contacts"
    assert @selenium.is_text_present("Find Contacts")

    #enter search value
    @selenium.select "document.Search.contact_type", "label=Individuals"
    @selenium.type "document.Search.sort_name", "Abhilasha Vasu"
    
    #click search     
    @page.click_and_wait "document.Search._qf_Search_refresh"

    #click name
    assert @selenium.is_text_present("Vasu, Abhilasha")
    assert_equal "Vasu, Abhilasha", @selenium.get_text("link=Vasu, Abhilasha")
    @page.click_and_wait "link=Vasu, Abhilasha"
  end

  def groups_click
    #click groups link
    assert_equal "Groups", @selenium.get_text("link=Groups")
    @page.click_and_wait "link=Groups"
  end
  
  def add_group
    #add a group   
    assert @selenium.is_text_present("This contact does not currently belong to any groups.")

    #add details
    @selenium.select "group_id", "label=Advisory Board"
   
    #submit form
    assert_equal "Add", @selenium.get_value("//input[@type='submit' and @name='_qf_GroupContact_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_GroupContact_next']"
    assert @selenium.is_text_present("Contact has been added to the selected group.")
  end  
  
  def remove_group
    assert @selenium.is_element_present("//div[@id='groupContact']/descendant::tr[td[contains(.,'Advisory Board')]]/descendant::a[contains(.,'Remove')]")
    @page.click_and_wait "//div[@id='groupContact']/descendant::tr[td[contains(.,'Advisory Board')]]/descendant::a[contains(.,'Remove')]"
    assert_equal "Are you sure you want to remove Abhilasha Vijay Vasu from Advisory Board?", @selenium.get_confirmation()
  end

  def rejoin_group
    assert_equal "[ Rejoin Group ]", @selenium.get_text("link=[ Rejoin Group ]")
    @page.click_and_wait "link=[ Rejoin Group ]"
    assert_equal "Are you sure you want to add Abhilasha Vijay Vasu back into Advisory Board?", @selenium.get_confirmation()
  end
end
