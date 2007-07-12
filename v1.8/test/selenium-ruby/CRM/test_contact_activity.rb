# This is a test case of using Selenium and its Ruby bindings
# Information' add activity definition
# This test case allows you to add activity and perform operations on information available

require 'crm_page_controller'
require '../selenium'


class TC_TestContactActivity < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_contact_activity
    #require 'test_new_individual'
    #add an Individual (using file test_new_individual)
    # @addIndividual = TC_TestNewIndividual.new

    # @addIndividual.go_to_new_individual
    # @addIndividual.add_new_individual

    #find a particular record
    search_individual()
 
    #click Activity link
    activity_click()
    
    #add_activity_email()
    add_activity_meeting()
    add_activity_schedule_call()
    add_activity_log_a_meeting()
    add_activity_log_a_call()
    add_activity_other()

    #edit_activity()
    delete_activity()
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

  #Click Activity
  def activity_click
    #click activities
    assert_equal "Activities", @selenium.get_text("link=Activities")
    @page.click_and_wait "link=Activities"
  end
  
  def add_activity_email
    #click send an email
    assert_equal "Send an Email", @selenium.get_text("link=Send an Email")
    @page.click_and_wait "link=Send an Email"
    
    #add details
    @selenium.type "document.Email.elements[5]", "In reference to your letter"
    
    #submit form
    assert_equal "Send Email", @selenium.get_value("//input[@type='submit' and @name='_qf_Email_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Email_next']"
  end
  
  def add_activity_meeting
    #click meeting
    assert_equal "Schedule a Meeting", @selenium.get_text("link=Schedule a Meeting")
    @page.click_and_wait "link=Schedule a Meeting"

    #add details
    @selenium.type "subject", "Meeting to Handle civiCRM Users"
    @selenium.type "location", "Mumbai"
    @selenium.select "scheduled_date_time[M]", "label=Sep"
    @selenium.select "scheduled_date_time[d]", "label=15"
    @selenium.select "scheduled_date_time[Y]", "label=2006"
    @selenium.select "scheduled_date_time[h]", "label=02"
    @selenium.select "scheduled_date_time[i]", "label=00"
    @selenium.select "scheduled_date_time[A]", "label=PM"
    @selenium.select "duration_hours", "label=2"
    @selenium.select "status", "label=Scheduled"
    @selenium.type "details", "Meeting to handle CiviCRM users in Database"
        
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Meeting_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Meeting_next']"
    assert @selenium.is_text_present("Meeting \"Meeting to Handle civiCRM Users\" has been saved.")
  end
  
  def add_activity_schedule_call
    #click on schedule a call
    assert_equal "Schedule a Call", @selenium.get_text("link=Schedule a Call")
    @page.click_and_wait "link=Schedule a Call"

    #add details
    @selenium.type "subject", "call ABN Amro"
    @selenium.type "phone_number", "022-256010e828"
    @selenium.select "scheduled_date_time[M]", "label=Sep"
    @selenium.select "scheduled_date_time[d]", "label=15"
    @selenium.select "scheduled_date_time[Y]", "label=2006"
    @selenium.select "scheduled_date_time[h]", "label=03"
    @selenium.select "scheduled_date_time[i]", "label=00"
    @selenium.select "scheduled_date_time[A]", "label=PM"
    @selenium.select "duration_hours", "label=1"
    @selenium.select "duration_minutes", "label=15"
    @selenium.select "status", "label=Scheduled"
    @selenium.type "details", "call bank to check the status of Account"
   
   
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Phonecall_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Phonecall_next']"
    assert @selenium.is_text_present("Phone Call \"call ABN Amro\" has been saved.")
  end
   
  def add_activity_log_a_meeting
    #click on log a meeting
    @selenium.get_text("link=Log a Meeting")
    @page.click_and_wait "link=Log a Meeting"
    
    #add details
    @selenium.type "subject", "Meeting to Handle civiCRM Users"
    @selenium.type "location", "Mumbai"
    @selenium.select "scheduled_date_time[d]", "label=15"
    @selenium.select "scheduled_date_time[h]", "label=02"
    @selenium.select "scheduled_date_time[i]", "label=00"
    @selenium.select "scheduled_date_time[A]", "label=PM"
    @selenium.type "details", "Meeting was there to handle number of CRM users all over the world"
    @selenium.select "duration_hours", "label=2"
    @selenium.select "status", "label=Completed"
    @selenium.select "duration_minutes", "label=0"
    
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Meeting_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Meeting_next']"
    assert @selenium.is_text_present("Meeting \"Meeting to Handle civiCRM Users\" has been logged to Activity History.")
  end

  def add_activity_log_a_call
    #click on Log a call
    assert @selenium.is_text_present("Log a Call")
    @page.click_and_wait "link=Log a Call"

    #add details
    @selenium.type "subject", "call ABN Amro"
    @selenium.type "phone_number", "022-256010e828"
   
    @selenium.type "details", "could not be able to talk, as the phone is busy"
    @selenium.select "duration_hours", "label=1"
    @selenium.select "duration_minutes", "label=15"
    @selenium.select "scheduled_date_time[d]", "label=15"
    @selenium.select "scheduled_date_time[h]", "label=03"
    @selenium.select "scheduled_date_time[A]", "label=PM"
    @selenium.select "status", "label=Unreachable"
    
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Phonecall_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Phonecall_next']"
    assert @selenium.is_text_present("Phone Call \"call ABN Amro\" has been saved.")
  end
  
  def add_activity_other
    #click on other activity
    assert @selenium.is_text_present("Other Activities")
    @page.click_and_wait "link=Other Activities"

    #add details
    @selenium.select "activity_type_id", "label=Event"
    @selenium.type "subject", "Annual Day of Company"
    @selenium.type "location", "mumbai"
    @selenium.select "scheduled_date_time[M]", "label=Dec"
    @selenium.select "scheduled_date_time[d]", "label=31"
    @selenium.select "scheduled_date_time[Y]", "label=2006"
    @selenium.select "scheduled_date_time[h]", "label=06"
    @selenium.select "scheduled_date_time[i]", "label=00"
    @selenium.select "scheduled_date_time[A]", "label=PM"
    @selenium.select "duration_hours", "label=6"
    @selenium.type "details", "Annual day of a company is scheduled to be celebrated on 31 dec. night"
    @selenium.select "status", "label=Scheduled"
    
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_OtherActivity_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_OtherActivity_next']"
    assert @selenium.is_text_present("Event \"Annual Day of Company\" has been saved.")
  end

  def edit_activity
    #click on edit link
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"
    
    #modify details
    @selenium.type "phone_number"
    @selenium.select "duration_hours", "label=2"
    @selenium.select "duration_minutes", "label=0"
    @selenium.select "status", "label=Unreachable"

    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @value='Save']")
    @page.click_and_wait "//input[@type='submit' and @value='Save']"
  end
    
  def delete_activity
    #click on delete link
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert_equal "Are you sure you want to delete this activity record?", @selenium.get_confirmation()
  end
end
