# This is a test case of using Selenium and its Ruby bindings
# Information' notes definition
# This test case allows you to add a note and perform operations on information available

require 'crm_page_controller'
require '../selenium'


class TC_TestContactNotes < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_contact_note
    #require 'test_new_individual'
    #add an Individual (using file test_new_individual)
    # @addIndividual = TC_TestNewIndividual.new

    # @addIndividual.go_to_new_individual
    # @addIndividual.add_new_individual

    #find a particular record
    search_individual()
 
    #select Relationship
    note_click()
    
    add_note()
    view_note()
    edit_note()
    delete_note()
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

  def note_click
    #click Note link to add a note
    assert_equal "Notes", @selenium.get_text("link=Notes")
    @page.click_and_wait "link=Notes"
  end
  
  def add_note
    #click add one
    assert @selenium.is_text_present("There are no Notes for this contact. You can add one")
    assert_equal "add one", @selenium.get_text("link=add one")
    @page.click_and_wait "link=add one"
    
    #add description
    @selenium.type "subject", "Organize the Terry Fox run"
    @selenium.type "note", "Hello"
    
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Note_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Note_next']"
    assert @selenium.is_text_present("Your Note has been saved.")
  end
  
  def view_note
    assert @selenium.is_element_present("//div[@id='notes']/descendant::tr[td[contains(.,'Hello')]]/descendant::a[contains(.,'View')]")
    @page.click_and_wait "//div[@id='notes']/descendant::tr[td[contains(.,'Hello')]]/descendant::a[contains(.,'View')]"

    #submit form
    assert_equal "Done", @selenium.get_value("//input[@type='button' and @value='Done']")
    @page.click_and_wait "//input[@type='button' and @value='Done']"
  end

  def edit_note
    assert @selenium.is_element_present("//div[@id='notes']/descendant::tr[td[contains(.,'Hello')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='notes']/descendant::tr[td[contains(.,'Hello')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.type "note", "Hello this is a test note"
    
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Note_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Note_next']"
    assert @selenium.is_text_present("Your Note has been saved.")
  end
  
  def delete_note
    assert @selenium.is_element_present("//div[@id='notes']/descendant::tr[td[contains(.,'Hello this is a test note')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='notes']/descendant::tr[td[contains(.,'Hello this is a test note')]]/descendant::a[contains(.,'Delete')]"
    assert_equal "Are you sure you want to delete this note?", @selenium.get_confirmation()
    assert @selenium.is_text_present("Selected Note has been Deleted Successfuly.")
  end
end
