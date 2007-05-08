# This is a test case of using Selenium and its Ruby bindings
# Information' tags definition
# This test case allows you to add tags and perform operations on information available

require 'crm_page_controller'
require '../selenium'


class TC_TestContactTags < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_contact_tags
    #require 'test_new_individual'
    #add an Individual (using file test_new_individual)
    # @addIndividual = TC_TestNewIndividual.new

    # @addIndividual.go_to_new_individual
    # @addIndividual.add_new_individual

    #find a particular record
    search_individual()
 
    #select Relationship
    tags_click()
    
    add_tags()
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

  def tags_click
    #click tags link
    assert @selenium.is_text_present("Tags")
    @page.click_and_wait "link=Tags"
  end
  
  def add_tags
    #add tag   
    assert_equal "Edit Tags", @selenium.get_text("link=Edit Tags")
    @page.click_and_wait "link=Edit Tags"
    
    #add details
    @selenium.check "tagList[2]"
   
    #submit form
    assert_equal "Update Tags", @selenium.get_value("//input[@type='submit' and @name='_qf_Tag_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Tag_next']"
    assert @selenium.is_text_present("Your update(s) have been saved.")
  end  
end
