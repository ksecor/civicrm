# This is a test case of using Selenium and its Ruby bindings
# Information' event definition
# This test case allows you to add event and perform operations on information available

require 'crm_page_controller'
require '../selenium'


class TC_TestContactEvents < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_contact_event
    #find a particular record
    search_individual( )
 
    #select Relationship
    click_events_tab( )
    add_event( )
    view_event( )
    edit_event( )
    delete_event( )
  end
  
  def search_individual
    #search for a particular individual
    assert_equal "Find Contacts", @selenium.get_text("link=Find Contacts")
    @page.click_and_wait "link=Find Contacts"
    assert @selenium.is_text_present("Find Contacts")

    #enter search value
    @selenium.select "contact_type", "label=Individuals"
    @selenium.type "sort_name", "Abhilasha Vasu"
    
    #click search     
    @page.click_and_wait "document.Search._qf_Search_refresh"

    #click name
    assert @selenium.is_text_present("Vasu, Abhilasha")
    assert_equal "Vasu, Abhilasha", @selenium.get_text("link=Vasu, Abhilasha")
    @page.click_and_wait "link=Vasu, Abhilasha"
  end

  def click_events_tab
    #click Events tab
    assert @selenium.is_text_present("//div[@id='participant' and @dojoType='ContentPane' and @label='Events']")
    @page.click_and_wait "//div[@id='participant' and and @dojoType='ContentPane' and @label='Events']"
  end
  
  def add_event
    if assert @selenium.is_text_present("There are no event registrations recorded for this contact. You can enter one now.")
      assert_equal "enter one now", @selenium.get_text("link=enter one now")
      @page.click_and_wait "link=enter one now"
    else
      assert @selenium.is_text_present("» New Event Registration")
      @page.click_and_wait "link=» New Event Registration"
    end

    @selenium.select "event_id", "label=Disney Cup International Youth Tournament - February 27th, 2007 12:00 AM"
    @selenium.wait_for_page_to_load "15000"
    @selenium.select "role_id", "label=Host"
    @selenium.wait_for_page_to_load "15000"
    
    @selenium.select "register_date[M]", "label=Feb"
    @selenium.select "register_date[d]", "label=27"
    @selenium.select "register_date[Y]", "label=2007"
    @selenium.select "register_date[h]", "label=11"
    @selenium.select "register_date[i]", "label=15"
    @selenium.select "register_date[A]", "label=AM"

    @selenium.select "status_id", "label=Attended"
    
    @selenium.check "//input[@type='radio' and @name='amount']"
    @selenium.type "note", "New Event Note"

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Participant_next']"
  end
  
  def edit_event
    assert_equal "Edit", @selenium.get_text("//div[@class='view-content']/descendant::tr[td[contains(.,'Disney Cup International Youth Tournament')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@class='view-content']/descendant::tr[td[contains(.,'Disney Cup International Youth Tournament')]]/descendant::a[contains(.,'Edit')]"

    @selenium.select "role_id", "label=Attendee"
    @selenium.wait_for_page_to_load "30000"
    @selenium.select "status_id", "label=Cancelled"
    
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Participant_next']"
  end

  def view_event
     assert_equal "View", @selenium.get_text("//div[@class='view-content']/descendant::tr[td[contains(.,'Disney Cup International Youth Tournament')]]/descendant::a[contains(.,'View')]")
    @page.click_and_wait "//div[@class='view-content']/descendant::tr[td[contains(.,'Disney Cup International Youth Tournament')]]/descendant::a[contains(.,'View')]"
    
    @page.click_and_wait "//input[@type='submit and @id='_qf_ParticipantView_next']"    
  end

  def delete_event
    assert_equal "Delete", @selenium.get_text("//div[@class='view-content']/descendant::tr[td[contains(.,'Disney Cup International Youth Tournament')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@class='view-content']/descendant::tr[td[contains(.,'Disney Cup International Youth Tournament')]]/descendant::a[contains(.,'Delete')]"

    assert @selenium.is_text_present("WARNING: Deleting this registration will result in the loss of related payment records (if any). Do you want to continue?")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Participant_next']"
  end 
end
