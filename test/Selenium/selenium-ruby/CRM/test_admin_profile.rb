# This is a test case of using Selenium and its Ruby bindings
# Information' Profile definition
# This test case allows you to add/edit/disable/enable/delete profile information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminProfile < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_profile
    # Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    assert_equal "CiviCRM\nProfile", @selenium.get_text("//a[@id='id_CiviCRMProfile']")
    @page.click_and_wait "//a[@id='id_CiviCRMProfile']"
    
    assert @selenium.is_text_present('CiviCRM Profile')
    
    add_profile()
    settings_profile()
    enable_profile()
    disable_profile()
    delete_profile()
  end
  
  # Add new Profile information
  def add_profile
   if @selenium.is_text_present("No CiviCRM Profiles have been created yet. You can add one now.")
     @page.click_and_wait "link=add one now"
   else
     @page.click_and_wait "link=» New CiviCRM Profile"
   end

    # Read new Profile information
    @selenium.type "title", "New Profile"
    
    if @selenium.get_value("//input[@type='checkbox' and @name='uf_group_type[User Registration]']") == 'off'
      @selenium.check "document.Group.elements['uf_group_type[User Registration]']"
    end
    
    @uf_group_type = { 'user_account'      => "//input[@type='checkbox' and @name='uf_group_type[User Account]']", 
                       'profile'           => "//input[@type='checkbox' and @name='uf_group_type[Profile]']", 
                       'search_profile'    => "//input[@type='checkbox' and @name='uf_group_type[Search Profile]']" }
    
    @uf_group_type.each{ | key, value |
      if @selenium.get_value(value) == 'on'
        @selenium.uncheck value
      end
    }
    
    @selenium.type   "weight", "2"
    @selenium.select "group", "label=Advisory Board"
    @selenium.select "add_contact_to_group", "label=Newsletter Subscribers"
    @selenium.type   "help_pre", "Pre-Form Help"
    @selenium.type   "help_post", "Post-Form Help"
    @selenium.type   "post_URL", ""
    @selenium.type   "cancel_URL", ""
    @selenium.click  "add_captcha"
    @selenium.click  "is_map"
    @selenium.click  "collapse_display"
    @selenium.click  "is_active"
    
    # Submit the form 
    @page.click_and_wait "_qf_Group_next"
    assert @selenium.is_text_present("Your CiviCRM Profile Group \"New Profile\" has been added. You can add fields to this group now.")
  end
  
  def settings_profile
    assert_equal "Settings", @selenium.get_text("//div[@id='uf_profile']/descendant::tr[td[contains(.,'New Profile')]]/descendant::a[contains(.,'Settings')]")
    @page.click_and_wait "//div[@id='uf_profile']/descendant::tr[td[contains(.,'New Profile')]]/descendant::a[contains(.,'Settings')]"
    
    if @selenium.get_value("//input[@type='checkbox' and @name='uf_group_type[User Registration]']") == 'on'
      @selenium.uncheck "document.Group.elements['uf_group_type[User Registration]']"
    end
        
    @uf_group_type.each{ | key, value |
      if @selenium.get_value(value) == 'off'
        @selenium.check value
      end
    }
    
    @page.click_and_wait "_qf_Group_next"
    assert @selenium.is_text_present("Your CiviCRM Profile Group \"New Profile\" has been saved.")
  end
  
  # Enable Profile
  def enable_profile
    assert_equal "Enable", @selenium.get_text("//div[@id='uf_profile']/descendant::tr[td[contains(.,'New Profile')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='uf_profile']/descendant::tr[td[contains(.,'New Profile')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Disable Profile
  def disable_profile
    @page.click_and_wait "//div[@id='uf_profile']/descendant::tr[td[contains(.,'New Profile')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this CiviCRM Profile group?", @selenium.get_confirmation
  end
  
  # Delete Profile
  def delete_profile
    assert_equal "Delete", @selenium.get_text("//div[@id='uf_profile']/descendant::tr[td[contains(.,'New Profile')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='uf_profile']/descendant::tr[td[contains(.,'New Profile')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("Delete New Profile Profile?")
    @page.click_and_wait "_qf_Group_next"
    assert @selenium.is_text_present("Your CiviCRM Profile Group \"New Profile\" has been deleted.")
  end
end
