# This is a test case of using Selenium and its Ruby bindings
# Information' manage Premium definition
# This test case allows you to add/edit/disable/enable/delete event type information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminEventType < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_manage_events
    move_to_event_type()

    add_event_type()
    edit_event_type()
    #disable_event_type()
    enable_event_type()
    delete_event_type()
  end
  
 def move_to_event_type
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Event types link
    assert_equal "Event\nTypes", @selenium.get_text("//a[@id='id_EventTypes']")
    @page.click_and_wait "//a[@id='id_EventTypes']"
  end

  # Add new Event type information
  def add_event_type
    assert_equal "» New Event Type", @selenium.get_text("new")
    @page.click_and_wait "//a[@id='new']"

    @selenium.type "label", "New Event 1"
    @selenium.type "description", "Testing new event"

    assert !60.times{ break if ("Save" == @selenium.get_value("_qf_Options_next") rescue false); sleep 1 }
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
  end

  # Edit new Event type information
  def edit_event_type
    assert_equal "Edit", @selenium.get_text("//div[@id='event_type']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='event_type']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.type "description", "Testing new event 1"
    
    assert !60.times{ break if ("Save" == @selenium.get_value("_qf_Options_next") rescue false); sleep 1 }
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
  end

  # Disable new Event type information
  def disable_event_type
    assert_equal "Disable", @selenium.get_text("//div[@id='event_type']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='event_type']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Disable')]"

    assert_equal "/^Are you sure you want to disable this Event Type[ S]\n   Users will no longer be able to select this value when adding or editing Event Type.$/ ", @selenium.get_confirmation()
  end

  # Enable new Event type information
  def enable_event_type
    if  assert @selenium.is_text_present("Enable")
      assert_equal "Enable", @selenium.get_text("//div[@id='event_type']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Enable')]")
      @page.click_and_wait "//div[@id='event_type']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Enable')]"
    end
  end

  # Delete new Event type information
  def delete_event_type
    assert_equal "Delete", @selenium.get_text("//div[@id='event_type']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='event_type']/descendant::tr[td[contains(.,'New Event 1')]]/descendant::a[contains(.,'Delete')]"

    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Event Type related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
  end
end
