# This is a test case of using Selenium and its Ruby bindings
# Information' Custom data definition
# This test case allows you to add/edit/disable/enable/delete custom data information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminCustomData < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
  end
  
  def teardown
    @page.logout
  end
  
  def test_custom_data
    move_to_custom_group_page()
    
    add_custom_group()
    
    add_custom_field()
    edit_custom_field()
    #preview_custom_field()
    #disable_custom_field()
    #enable_custom_field()
    
    #check_delete_custom_group()
    
    delete_custom_field()
    
    #settings_custom_group()
    #disable_custom_group()
    #enable_custom_group()
    delete_custom_group()
    
  end
  
  def move_to_custom_group_page
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Clicking Custom data
    assert_equal "Custom\nData", @selenium.get_text("//a[@id='id_CustomData']")
    @page.click_and_wait "//a[@id='id_CustomData']"
    
    @selenium.is_text_present("Custom Data")
  end
  
  def move_to_custom_field_page
    assert @selenium.is_element_present("//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'View and Edit Custom Fields')]")
    @page.click_and_wait "//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'View and Edit Custom Fields')]"
  end
  
  # Add new Custom Group
  def add_custom_group
    @page.click_and_wait "link=» New Custom Data Group"
    
    @selenium.type   "title", "New Custom Group"
    @selenium.select "extends[0]", "label=Individuals"
    @selenium.type   "weight", "3"
    @selenium.select "style", "label=Inline"
    
    @custom_group = ["//input[@type='checkbox' and @name='is_active']","//input[@type='checkbox' and @name='collapse_display']"]
    
    @custom_group.each{ | value |
      if @selenium.is_checked(value)
        @selenium.uncheck value
      end
    }
    
    @selenium.type "help_pre", "Pre-form Help"
    @selenium.type "help_post", "Post-form Help"
    
    # Submit the form 
    @page.click_and_wait "_qf_Group_next"
    assert @selenium.is_text_present("Your Group \"New Custom Group\" has been added. You can add custom fields to this group now.")
  end
  
  # Add new Custom Field information
  def add_custom_field
    
    move_to_custom_field_page()
    
    # add new custom field in a group
    assert @selenium.is_text_present("There are no custom fields for custom group \"New Custom Group\", add one.")
    assert_equal "add one", @selenium.get_text("link=add one")
    @page.click_and_wait "link=add one"
         
    # Insert information for new custom field  
    @selenium.type   "//input[@type='text' and @id='label']", "Integer Text Custom Field"
    @selenium.select "data_type[0]", "label=Integer"
    @selenium.select "data_type[1]", "label=Text"
    @selenium.type   "weight", "3"
    @selenium.type   "help_post", "Field Help"
    
    if !@selenium.is_checked("is_required")
      @selenium.check  "is_required"
    end
    
    @custom_field = { 'is_searchable' => "//div[@id='is_searchable']/descendant::input[@type='checkbox']",
                      'ia_active'     => "//input[@type='checkbox' and @name='is_active']" }
    
    @custom_field.each{ | key, value | 
      if @selenium.is_checked(value)
        @selenium.uncheck value
      end
    }
    
    # Submit the Custom field form
    @page.click_and_wait "_qf_Field_next"
    assert @selenium.is_text_present("Your custom field \"Integer Text Custom Field\" has been saved")
  end 
  
  # Editing Custom Field information
  def edit_custom_field
    assert @selenium.is_element_present("//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Edit Field')]")
    @page.click_and_wait "//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Edit Field')]"
       
    if @selenium.is_checked("is_required")
      @selenium.uncheck  "is_required"
    end
        
    @custom_field.each{ | key, value | 
      if !@selenium.is_checked(value)
        @selenium.check value
      end
    }
    
    if is_checked(@custom_field['is_searchable'])
      @selenium.check  "is_search_range"
    end
    
    # Submit the Custom field form    
    @page.click_and_wait "_qf_Field_next"
    assert @selenium.is_text_present("Your custom field \"Integer Text Custom Field\" has been saved")
  end
  
  # Preview Custom field 
  def preview_custom_field
    assert @selenium.is_element_present("//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Preview Field Display')]")
    @page.click_and_wait "//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Preview Field Display')]"
    
    assert @selenium.is_text_present("Preview of this field as it will be displayed in an edit form.")
    @page.click_and_wait "_qf_Preview_cancel"
  end
  
  # Disable Custom Field 
  def disable_custom_field
    # Click Disable Field
    assert_equal "Disable", @selenium.get_text("//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this custom data field?", @selenium.get_confirmation
  end
  
  # Enable Custom Field
  def enable_custom_field
    assert @selenium.is_element_present("//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Delete Custom Field
  def delete_custom_field
    
    move_to_custom_field_page()
    
    assert @selenium.is_element_present("//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='field_page']/descendant::tr[td[contains(.,'Integer Text Custom Field')]]/descendant::a[contains(.,'Delete')]"
    assert_equal "Are you sure you want to delete this custom data field?", @selenium.get_confirmation
    assert @selenium.is_text_present("WARNING: Deleting this custom field will result in the loss of all \"Integer Text Custom Field\" data. Any Profile form and listings field(s) linked with \"Integer Text Custom Field\" will also be deleted. This action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_DeleteField_next"
    assert @selenium.is_text_present("The custom field \"Integer Text Custom Field\" has been deleted.")
  end
  
  # Editing Custom Data information
  def settings_custom_group
    assert @selenium.is_element_present("//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Settings')]")
    @page.click_and_wait "//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Settings')]"
    @custom_group.each{ | value |
      if !@selenium.is_checked(value)
        @selenium.check value
      end
    }
    
    #Edit custom data
    @page.click_and_wait "_qf_Group_next"
  end
  
  # Disable Custom Data 
  def disable_custom_group
    # Click View and edit Custom Data 
    assert @selenium.is_element_present("//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this custom data group? Any profile fields that are linked to custom fields of this group will be disabled.", @selenium.get_confirmation()
  end
  
  # Enable Custom Field
  def enable_custom_group
    assert @selenium.is_element_present("//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Delete Custom Data
  def delete_custom_group
    assert @selenium.is_element_present("//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this custom group will result in the loss of all New Custom Group data. This action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_DeleteGroup_next"
    assert @selenium.is_text_present("The Group \"New Custom Group\" has been deleted.")
  end
  
  def check_delete_custom_group
    
    move_to_custom_group_page()
    
    assert @selenium.is_element_present("//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='custom_group']/descendant::tr[td[contains(.,'New Custom Group')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this custom group will result in the loss of all New Custom Group data. This action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_DeleteGroup_next"
    assert @selenium.is_text_present("The Group \"New Custom Group\" has not been deleted! You must Delete all custom fields in this group prior to deleting the group")
  end  
  
end
