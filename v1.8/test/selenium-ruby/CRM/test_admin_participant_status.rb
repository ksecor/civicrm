# This is a test case of using Selenium and its Ruby bindings
# Information' manage Premium definition
# This test case allows you to add/edit/disable/enable/delete event type information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminParticipantStatus < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_manage_events
    move_to_participant_status( ) 
 
    add_participant_status( )
    edit_participant_status( )
   # disable_participant_status( )
    enable_participant_status( )
    delete_participant_status( )
  end
  
 def move_to_participant_status
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Participant status link
    assert_equal "Participant\nStatus", @selenium.get_text("id_ParticipantStatus")
    @page.click_and_wait "//a[@id='id_ParticipantStatus']"
  end

  # Add new Participant status information
  def add_participant_status
    assert_equal "» New Participant Status", @selenium.get_text("new")
    @page.click_and_wait "//a[@id='new']"

    @selenium.type "label", "New Participant Status 1"
    @selenium.type "description", "Testing Participant status"
    
    assert !60.times{ break if ("Save" == @selenium.get_value("_qf_Options_next") rescue false); sleep 1 }
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
  end

  # Edit new participant status information
  def edit_participant_status
    assert_equal "Edit", @selenium.get_text("//div[@id='participant_status']/descendant::tr[td[contains(.,'New Participant Status 1')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='participant_status']/descendant::tr[td[contains(.,'New Participant Status 1')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.type "description", "Testing participant"
    
    assert !60.times{ break if ("Save" == @selenium.get_value("_qf_Options_next") rescue false); sleep 1 }
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
  end

  # Disable new Participant status information
  def disable_participant_status
    assert_equal "Disable", @selenium.get_text("//div[@id='participant_status']/descendant::tr[td[contains(.,'New Participant Status 1')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='participant_status']/descendant::tr[td[contains(.,'New Participant Status 1')]]/descendant::a[contains(.,'Disable')]"
   # assert_equal "/^Are you sure you want to disable this Participant Status[\s\S]
  #  Users will no longer be able to select this value when adding or editing Participant Status\.$/" =~ @selenium.get_confirmation
  end

  # Enable new Participant status information
  def enable_participant_status
    if assert @selenium.is_text_present("Enable")
      assert_equal "Enable", @selenium.get_text("//div[@id='participant_status']/descendant::tr[td[contains(.,'New Participant Status 1')]]/descendant::a[contains(.,'Enable')]")
      @page.click_and_wait "//div[@id='participant_status']/descendant::tr[td[contains(.,'New Participant Status 1')]]/descendant::a[contains(.,'Enable')]"
    end
  end

  # Delete new Participant status information
  def delete_participant_status
    assert_equal "Delete", @selenium.get_text("//div[@id='participant_status']/descendant::tr[td[contains(.,'New Participant Status 1')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='participant_status']/descendant::tr[td[contains(.,'New Participant Status 1')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Participant Status related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")   
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
  end
end
