# This is a test case of using Selenium and its Ruby bindings
# Information' relationship definition
# This test case allows you to add relationship and perform operations on information available

require 'crm_page_controller'
require '../selenium'


class TC_TestContactRelationship < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_contact_relationship
    #require 'test_new_individual'
    #add an Individual (using file test_new_individual)
    # @addIndividual = TC_TestNewIndividual.new

    # @addIndividual.go_to_new_individual
    # @addIndividual.add_new_individual

    #find a particular record
    search_individual()
 
    #select Relationship
    relationship_click()
    
    add_relationship()
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

  def relationship_click
    #click relationship link
    assert @selenium.is_text_present("Relationships")
    @page.click_and_wait "link=Relationships"
  end
  
  def add_relationship
    #add relationship   
    assert @selenium.is_text_present("There are no Relationships entered for this contact. You can add one.")
    @page.click_and_wait "link=add one"
    
    #add details
    @selenium.select "relationship_type_id", "label=Employee of"
    assert_equal "Search", @selenium.get_value("//input[@type='submit' and @name='_qf_Relationship_refresh']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Relationship_refresh']"
   
    # @selenium.click "\w(//input[@type='checkbox' and @value=1])"
    @selenium.select "start_date[M]", "label=Jun"
    @selenium.select "start_date[d]", "label=01"
    @selenium.select "start_date[Y]", "label=2003"
    @selenium.select "end_date[M]", "label=Aug"
    @selenium.select "end_date[d]", "label=20"
    @selenium.select "end_date[Y]", "label=2005"
    @selenium.type "description", "Employee of this company"
    @selenium.type "note", "  "

    #submit form
    assert_equal "Save Relationship", @selenium.get_value("//input[@type='submit' and @name='_qf_Relationship_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Relationship_next']"
    assert @selenium.is_text_present("1 new relationship record created.")
  end  
end
