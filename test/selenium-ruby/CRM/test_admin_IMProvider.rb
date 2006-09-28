# This is a test case of using Selenium and its Ruby bindings
# Information' Instant Messenger service definition
# This test case allows you to add/edit/disable/enable/delete gender information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminIMProvider < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_IMProvider
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Instant Messenger Service Provider 
    assert_equal "Instant\nMessenger\nServices", @selenium.get_text("//a[@id='id_InstantMessengerServices']")
    @page.click_and_wait "//a[@id='id_InstantMessengerServices']"
    
    add_IMProvider()
    edit_IMProvider()
    enable_IMProvider()
    disable_IMProvider()
    delete_IMProvider()
  end
  
  # Add new IM Service Provider
  def add_IMProvider
    @page.click_and_wait "link=» New Instant Messenger Service Option"
    
    # Read new IM Service Name
    @selenium.type "name", "New IM Provider"
    
    if @selenium.get_value("//input[@type='checkbox' and @name='is_active']") == 'off'
      @selenium.check 'is_active'
    end
    
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Instant Messenger Service \"New IM Provider\" has been saved.")
  end
  
  # Add new IM Service Provider
  def edit_IMProvider
    assert_equal "Edit", @selenium.get_text("//div[@id='instant_messenger_service']/descendant::tr[td[contains(.,'New IM Provider')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='instant_messenger_service']/descendant::tr[td[contains(.,'New IM Provider')]]/descendant::a[contains(.,'Edit')]"
    
    if @selenium.get_value("//input[@type='checkbox' and @name='is_active']") == 'on'
      @selenium.uncheck 'is_active'
    end
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("The Instant Messenger Service \"New IM Provider\" has been saved.")
  end
  
  # Enable IM Service
  def enable_IMProvider
    assert_equal "Enable", @selenium.get_text("//div[@id='instant_messenger_service']/descendant::tr[td[contains(.,'New IM Provider')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='instant_messenger_service']/descendant::tr[td[contains(.,'New IM Provider')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Disable IM Service
  def disable_IMProvider
    # @temp = @selenium.get_html_source()
    assert_equal "Disable", @selenium.get_text("//div[@id='instant_messenger_service']/descendant::tr[td[contains(.,'New IM Provider')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='instant_messenger_service']/descendant::tr[td[contains(.,'New IM Provider')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Instant Messenger Service?\n\nUsers will no longer be able to select this value when adding or editing Instant Messenger Service.", @selenium.get_confirmation()
  end
  
  # Delete Gender type
  def delete_IMProvider
    assert_equal "Delete", @selenium.get_text("//div[@id='instant_messenger_service']/descendant::tr[td[contains(.,'New IM Provider')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='instant_messenger_service']/descendant::tr[td[contains(.,'New IM Provider')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Instant Messenger Service related records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Options_next']"
    assert @selenium.is_text_present("Selected Instant Messenger Service type has been deleted.")
  end
end
