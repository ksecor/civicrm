# This is a test case of using Selenium and its Ruby bindings
# Information' manage Premium definition
# This test case allows you to add/edit/disable/enable/delete event type information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminManageEvents < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_manage_events
    move_to_manage_events( )

    add_events( )
    #configure_events( )
    #events_test_drive( )
    #live_page_events( )
    disable_events( )
    copy_events( )
    delete_events( )
   # show_past_events( )
  end
  
 def move_to_manage_events
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Manage Events link
    assert_equal "Manage\nEvents", @selenium.get_text("//a[@id='id_ManageEvents']")
    @page.click_and_wait "//a[@id='id_ManageEvents']"
  end

  # Add new Event information
  def add_events
    assert_equal "» New Event", @selenium.get_text("//a[@id='newManageEvent']")
    @page.click_and_wait "//a[@id='newManageEvent']"

    # Event Information and Settings (step 1 of 4)
    configure_event_info( )
    
    assert !5.times{ break if ("Continue >>" == @selenium.get_value("_qf_EventInfo_next") rescue false); sleep 1 }
    @page.click_and_wait "//input[@type='submit' and @name='_qf_EventInfo_next']"
    
    # Event Location (step 2 of 4)
    configure_location_info( )
    
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Location_next']"
    
    # Event Fees (step 3 of 4)
    configure_fee_info( )

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Fee_next']"
    
    # Online Registration (step 4 of 4)
    configure_registration_info( )
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Registration_next']"
  end

  def show_past_events
    @page.click_and_wait "//a[@id='pastEvents']"
    
    # if selected new manage event
    @page.click_and_wait "//a[@id='newManageEvent']"
    add_events( )      
    
    @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'Fall Fundraiser Dinner')]]/descendant::a[contains(.,'Configure')]"
    configure_events( )
        
    @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'Fall Fundraiser Dinner')]]/descendant::a[contains(.,'Test-Drive')]"
    
    @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'Fall Fundraiser Dinner')]]/descendant::a[contains(.,'Disable')]"
      assert_equal "Are you sure you want to disable this eventship type?",@selenium.get_confirmation()
    
    @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'Fall Fundraiser Dinner')]]/descendant::a[contains(.,'Copy')]"
    
    @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'Fall Fundraiser Dinner')]]/descendant::a[contains(.,'Delete')]"
    assert_equal "Are you sure you want to delete this Event?", @selenium.get_confirmation()
    
    assert !60.times{ break if (@selenium.is_element_present("currentEvents") rescue false); sleep 1 }
    if @page.click_and_wait "//a[@id='currentEvents']"
      @page.click_and_wait "//a[@id='currentEvents']"
    end
  end

  def configure_events
    if @page.click_and_wait "//a[@id='idEventInformationandSettings']"
      @page.click_and_wait "//a[@id='idEventInformationandSettings']"
      configure_event_info( )
    end
    
    if @page.click_and_wait "//a[@id='idLocation']"
      @page.click_and_wait "//a[@id='idLocation']"
      configure_location_info( )
    end

    if @page.click_and_wait "//a[@id='idFee']"
      @page.click_and_wait "//a[@id='idFee']"
      configure_fee_info( )
    end

    if @page.click_and_wait "//a[@id='idRegistration']"
      @page.click_and_wait "//a[@id='idRegistration']"
      configure_registration_info( )
    end

    if @page.click_and_wait "//a[@id='idDisplayEvent']"
      @page.click_and_wait "//a[@id='idDisplayEvent']"
      configure_display_events( )
    end

    if @page.click_and_wait "//a[@id='idTest-drive']"
      @page.click_and_wait "//a[@id='idTest-drive']"
      configure_test_drive( )      
    end
  end

  def configure_event_info
    @selenium.select "event_type_id", "label=Fundraiser"
    @selenium.wait_for_page_to_load 15000
    @selenium.type "title", "New Event 1"
    @selenium.type "summary", "Event Summary"
    @selenium.type "description", "Complete Description of Event"
    @selenium.select "start_date[M]", "label=Jan"
    @selenium.select "start_date[d]", "label=01"
    @selenium.select "start_date[Y]", "label=2006"
    @selenium.select "start_date[h]", "label=01"
    @selenium.select "start_date[i]", "label=15"
    @selenium.select "start_date[A]", "label=AM"
    @selenium.select "end_date[M]", "label=Dec"
    @selenium.select "end_date[d]", "label=31"
    @selenium.select "end_date[Y]", "label=2007"
    @selenium.select "end_date[h]", "label=02"
    @selenium.select "end_date[i]", "label=15"
    @selenium.select "end_date[A]", "label=PM"
    @selenium.type "max_participants", "50"
    @selenium.type "event_full_text", "Event is Full"
    @selenium.uncheck "//input[@type='checkbox' and @name='is_map']"
    @selenium.check "//input[@type='checkbox' and @name='is_public']"
    @selenium.check "//input[@type='checkbox' and @name='is_active']"
  end

  def configure_location_info
    @selenium.select "location_1_location_type_id", "label=Work"
    @selenium.type "location_1_name", "B-1069"
    @selenium.select "location_1_phone_1_phone_type", "label=Phone"
    @selenium.type "location_1_phone_1_phone", "231131423432"
    @selenium.type "location_1_email_1_email", "abhilasha@webaccess.co.in"
    @selenium.type "location_1_address_street_address", "oberoi garden estate"
    @selenium.type "location_1_address_supplemental_address_1", "chandivali"
    @selenium.type "location_1_address_supplemental_address_2", ""
    @selenium.type "location_1_address_city", "Mumbai"
    @selenium.select "location_1_address_state_province_id", "label=California"
    @selenium.type "location_1_address_postal_code", "89459"
    @selenium.type "location_1_address_postal_code_suffix", "348"
    @selenium.select "location_1_address_country_id", "label=United States"
    @selenium.select "location_1_address_county_id", "label=San Francisco"
    @selenium.type "location_1_address_geo_code_1", "3423"
    @selenium.type "location_1_address_geo_code_2", "43"
    @selenium.type "location_1_address_geo_code_1", "323"
  end

  def configure_fee_info
      @selenium.check "//input[@type='radio' and @name='is_monetary']"
      @selenium.select "contribution_type_id", "label=Event Fee"
      @selenium.type "label_1", "Fee 1"
      @selenium.type "value_1", "100"
      @selenium.type "label_2", "Fee 2"
      @selenium.type "value_2", "200"
      @selenium.type "label_3", "Fee 3"
      @selenium.type "value_3", "300"
      @selenium.type "label_4", "Fee 4"
      @selenium.type "value_4", "400"
      @selenium.type "label_5", "Fee 5"
      @selenium.type "value_5", "500"
  end

  def configure_registration_info
    @selenium.check "//input[@type='checkbox' and @name='is_online_registration']"
    @selenium.type "registration_link_text", "Register Now!"
    @selenium.type "intro_text", "Introductory Registration Text"
    @selenium.type "footer_text", "Registration Footer Text"
    @selenium.select "custom_pre_id", "label=Name and Address"
    @selenium.select "custom_post_id", "label=Name and Address"
    @selenium.type "confirm_title", "New Online Registration"
    @selenium.type "confirm_text", "Introductory Confirmation Text"
    @selenium.type "confirm_footer_text", "Confirmation Footer Text"
    
    # Send Confirmation Email
    @selenium.check "//input[@type='radio' and @name='is_email_confirm']"
    @selenium.type "confirm_email_text", "Sending confirmation email to you"
    @selenium.type "confirm_from_name", "abhilasha"
    @selenium.type "confirm_from_email", "abhilasha@webaccess.co.in"
    @selenium.type "cc_confirm", ""
    @selenium.type "bcc_confirm", ""

    @selenium.type "thankyou_title", "ThankYou"
    @selenium.type "thankyou_text", "Introductory Thank you text"
    @selenium.type "thankyou_footer_text", "Thank you footer text"
  end

  def events_test_drive
    @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Test-drive')]"

    assert @selenium.is_text_present("Test-drive Your Event Registration Page\n\n    This page is currently running in test-drive mode. Transactions will be sent to your payment processor's test server. No live financial transactions will be submitted. However, a contact record will be created or updated and a contribution record will be saved to the database. Use obvious test contact names so you can review and delete these records as needed. Refer to your payment processor's documentation for information on values to use for test credit card number, security code, postal code, etc.")

    assert @selenium.is_text_present("Register Now")
    @page.click_and_wait "link=Register Now"
    
    @selenium.check "//input[@type='radio' and @name='amount' and @value='19']"
    @selenium.type "email", "abhilasha@webaccess.co.in"
    @selenium.select "credit_card_type", "label=Visa"
    @selenium.type "credit_card_number", "4111111111111111"
    @selenium.type "cvv2", "111"
    @selenium.select "credit_card_exp_date[M]", "label=Dec"
    @selenium.select "credit_card_exp_date[Y]", "label=2010"
    @selenium.type "first_name", "Abhilasha"
    @selenium.type "middle_name", "V."
    @selenium.type "last_name", "Vasu"
    @selenium.type "street_address", "b-1069"
    @selenium.type "city", "Mumbai"
    @selenium.select "state_province_id", "label=California"
    @selenium.type "postal_code", "2321412"
    @selenium.select "country_id", "label=United States"

    @page.click_and_wait "//input[@name='_qf_Register_next' and @type='submit']"
  end

  def live_page_events
    @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Live Page')]"
    assert @selenium.is_text_present("Register Now")
    @page.click_and_wait "link=Register Now"

    @selenium.check "//input[@type='radio' and @name='amount' and @value='19']"
    @selenium.type "email", "abhilasha@webaccess.co.in"
    @selenium.select "credit_card_type", "label=Visa"
    @selenium.type "credit_card_number", "4111111111111111"
    @selenium.type "cvv2", "111"
    @selenium.select "credit_card_exp_date[M]", "label=Dec"
    @selenium.select "credit_card_exp_date[Y]", "label=2010"
    @selenium.type "first_name", "Abhilasha"
    @selenium.type "middle_name", "V."
    @selenium.type "last_name", "Vasu"
    @selenium.type "street_address", "b-1069"
    @selenium.type "city", "Mumbai"
    @selenium.select "state_province_id", "label=California"
    @selenium.type "postal_code", "2321412"
    @selenium.select "country_id", "label=United States"

    @page.click_and_wait "//input[@name='_qf_Register_next' and @type='submit']"
    
  end

  def disable_events
    @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this eventship type?",@selenium.get_confirmation()
  end
  
  def copy_events
      @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Copy')]"
  end

  def delete_events
    @page.click_and_wait "//div[@id='event_status_id']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Delete')]"
    assert_equal "Are you sure you want to delete this Event?", @selenium.get_confirmation()
  end
end
