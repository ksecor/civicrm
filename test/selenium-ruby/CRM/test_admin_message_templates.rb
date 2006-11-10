# This is a test case of using Selenium and its Ruby bindings
# Information' Message Template definition
# This test case allows you to add/edit/disable/enable/delete message templates

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminMessageTemplates < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_message_templates
    move_to_admin_message_templates()
    add_message_templates()
    edit_message_templates()
    enable_message_templates()
    disable_message_templates()
    delete_message_templates()
  end
  
  def move_to_admin_message_templates
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click Message_Templatess
    assert_equal "Message\nTemplates", @selenium.get_text("//a[@id='id_MessageTemplates']")
    @page.click_and_wait "//a[@id='id_MessageTemplates']"
  end
  
  # Add new message_templates information
  def add_message_templates
    if @selenium.is_text_present("There are no Message Templates entered. You can add one.")
     @page.click_and_wait "link=add one"
    else
      assert @selenium.is_text_present("» New Message Templates")
      @page.click_and_wait "link=» New Message Templates"
    end

    # Read new message_templates information
    @selenium.type  "msg_title", "New Message Template"
    @selenium.type  "msg_subject", "Template Subject"      
    @selenium.type  "msg_text", "Text message for new templates"
    @selenium.type  "msg_html", "HTML Message for new templates"
    @selenium.check "is_active"

    # Submit the form 
    @page.click_and_wait "_qf_MessageTemplates_next"
    assert @selenium.is_text_present("The Message Template \"New Message Template\" has been saved.")
  end
  
  # Editing message templates information
  def edit_message_templates
    assert_equal "Edit", @selenium.get_text("//div[@id='message_status_id']/descendant::tr[td[contains(.,'New Message Template')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='message_status_id']/descendant::tr[td[contains(.,'New Message Template')]]/descendant::a[contains(.,'Edit')]"
    
    @selenium.type "msg_subject", "Editing template subject"
    @selenium.uncheck "is_active"

    #Submit the form 
    @page.click_and_wait "_qf_MessageTemplates_next"
    assert @selenium.is_text_present("The Message Template \"New Message Template\" has been saved.")
  end
  
 # Disable message templates information
  def disable_message_templates
    assert_equal "Disable", @selenium.get_text("//div[@id='message_status_id']/descendant::tr[td[contains(.,'New Message Template')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='message_status_id']/descendant::tr[td[contains(.,'New Message Template')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this membership type?", @selenium.get_confirmation()
  end
  
  # Enable message_templates information
  def enable_message_templates
    assert_equal "Enable", @selenium.get_text("//div[@id='message_status_id']/descendant::tr[td[contains(.,'New Message Template')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='message_status_id']/descendant::tr[td[contains(.,'New Message Template')]]/descendant::a[contains(.,'Enable')]"
  end

  # Delete message_templates
  def delete_message_templates
    assert_equal "Delete", @selenium.get_text("//div[@id='message_status_id']/descendant::tr[td[contains(.,'New Message Template')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='message_status_id']/descendant::tr[td[contains(.,'New Message Template')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("Do you want to delete this message template ?")

    @page.click_and_wait "_qf_MessageTemplates_next"
    assert @selenium.is_text_present("Selected message templates has been deleted.")
  end
end
