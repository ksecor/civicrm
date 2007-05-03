# This is a test case of using Selenium and its Ruby bindings
# Information' manage Premium definition
# This test case allows you to add/edit/disable/enable/delete participant role information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminParticipantRole < Test::Unit::TestCase

  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_manage_events
    move_to_participant_role( ) 
 
    add_participant_role( )
    edit_participant_role( )
  #  disable_participant_role( )
    enable_participant_role( )
    delete_participant_role( )
  end
  
 def move_to_participant_role
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Participant role link
    assert_equal "Participant\nRole", @selenium.get_text("id_ParticipantRole")
    @page.click_and_wait "//a[@id='id_ParticipantRole']"
  end

  # Add new Participant role information
  def add_participant_role
    assert_equal "» New Participant Role", @selenium.get_text("new")
    @page.click_and_wait "//a[@id='new']"

    @selenium.type "label", "New Participant Role 1"
    @selenium.type "description", "Testing Participant role"
    
    assert !60.times{ break if ("Save" == @selenium.get_value("_qf_Options_next") rescue false); sleep 1 }
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
  end

  # Edit new participant role information
  def edit_participant_role
    assert_equal "Edit", @selenium.get_text("//div[@id='participant_role']/descendant::tr[td[contains(.,'New Participant Role 1')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='participant_role']/descendant::tr[td[contains(.,'New Participant Role 1')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.type "description", "Testing participant"
    
    assert !60.times{ break if ("Save" == @selenium.get_value("_qf_Options_next") rescue false); sleep 1 }
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
  end

  # Disable new participant roleinformation
  def disable_participant_role
    assert_equal "Disable", @selenium.get_text("//div[@id='participant_role']/descendant::tr[td[contains(.,'New Participant Role 1')]]/descendant::a[contains(.,'Disable')]")
      @page.click_and_wait "//div[@id='participant_role']/descendant::tr[td[contains(.,'New Participant Role 1')]]/descendant::a[contains(.,'Disable')]"
    assert /^Are you sure you want to disable this Participant Role[\s\S]

 Users will no longer be able to select this value when adding or editing Participant Role\.$/ =~ @selenium.get_confirmation
    
  end

  # Enable new participant role information
  def enable_participant_role
    if assert @selenium.is_text_present("Enable")
      assert_equal "Enable", @selenium.get_text("//div[@id='participant_role']/descendant::tr[td[contains(.,'New Participant Role 1')]]/descendant::a[contains(.,'Enable')]")
      @page.click_and_wait "//div[@id='participant_role']/descendant::tr[td[contains(.,'New Participant Role 1')]]/descendant::a[contains(.,'Enable')]"
    end
  end

  # Delete new participant role information
  def delete_participant_role
    assert_equal "Delete", @selenium.get_text("//div[@id='participant_role']/descendant::tr[td[contains(.,'New Participant Role 1')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='participant_role']/descendant::tr[td[contains(.,'New Participant Role 1')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Participant Role related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
     @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
  end
end
