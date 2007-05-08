# This is a test case of using Selenium and its Ruby bindings
# Information' New Household definition
# This test case allows you to add/edit/delete new household information

require 'crm_page_controller'
require '../selenium'

class TC_TestNewHousehold < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_new_household
    go_to_civicrm()
    go_to_new_household()

    #add a new individual
    add_new_household()

    #select save & new option
    go_to_new_household()
    save_and_new_household()

    #edit household
    click_cancel()
    go_to_civicrm()
    search_household()
    edit_household()
    
    #delete household
    delete_household()
    
    #export vCard
    #vCard_new_household()
  end

  def go_to_civicrm
    #click CiviCRM
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.click_and_wait "link=CiviCRM"
  end

  def go_to_new_household
    #click New Household
    assert_equal "New Household", @selenium.get_text("link=New Household")
    @page.click_and_wait "link=New Household"
  end

  def click_cancel
    assert_equal "Cancel", @selenium.get_value("_qf_Edit_cancel")
    @page.click_and_wait "//input[@type='submit' and @value='Cancel']"
  end
  
  def add_new_household
    #add details of a household
    @selenium.type   "household_name", "CiviCRM Household"
    @selenium.type   "nick_name", "HS"
    @selenium.check  "//input[@type='checkbox' and @name='privacy[do_not_phone]']"
    @selenium.check  "//input[@type='checkbox' and @name='privacy[do_not_email]']"     
    @selenium.check  "//input[@type='checkbox' and @name='privacy[do_not_mail]']"     
    @selenium.check  "//input[@type='checkbox' and @name='privacy[do_not_trade]']"     
    @selenium.check  "//input[@type='checkbox' and @name='preferred_communication_method[1]']"
    @selenium.check  "//input[@type='checkbox' and @name='preferred_communication_method[3]']"
    @selenium.check  "//input[@type='checkbox' and @name='preferred_communication_method[5]']"
    @selenium.select "preferred_mail_format", "label=HTML"
    @selenium.select "location[1][phone][1][phone_type]", "label=Phone"
    @selenium.select "location[1][phone][1][phone_type]", "label=Mobile"
    @selenium.type   "document.Edit.elements['location[1][email][1][email]']", "newHouseHold@nhs.org"
    @selenium.select "location[1][im][1][provider_id]", "label=MSN"
    @selenium.type   "location[1][name]", "Mumbai"
    @selenium.type   "location[1][address][street_address]", "NE 297P Green Rd SE"
    @selenium.type   "location[1][address][supplemental_address_1]", "Editorial Dept"
    @selenium.type   "location[1][address][city]", "East Saint Louis"
    @selenium.select "location[1][address][state_province_id]", "label=California"
    @selenium.type   "location[1][address][postal_code]", "2312124234"
    @selenium.type   "location[1][address][geo_code_1]", "38.6151"
    @selenium.type   "location[1][address][geo_code_2]", "-80.7814"
    
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Edit_next_view']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Edit_next_view']"
    assert @selenium.is_text_present("Your Household contact record has been saved.")
  end

  def save_and_new_household
    #add details of a household
    @selenium.type "household_name", "My Household"
    @selenium.type "nick_name", "MHS"
    @selenium.click "_qf_Edit_refresh_dedupe"
    @selenium.check "//input[@type='checkbox' and @name='privacy[do_not_phone]']"     
    @selenium.check "//input[@type='checkbox' and @name='privacy[do_not_email]']"     
    @selenium.check "//input[@type='checkbox' and @name='preferred_communication_method[1]']"
    @selenium.check "//input[@type='checkbox' and @name='preferred_communication_method[3]']"
    @selenium.select "preferred_mail_format", "label=HTML"
    @selenium.select "document.Edit.elements['location[1][location_type_id]']", "label=Main"
    @selenium.type "location[1][address][street_address]", "SE 81N Second Ave S"
    @selenium.type "location[1][address][supplemental_address_1]", "C/o PO Plus"
    @selenium.type "location[1][address][supplemental_address_2]", "Mountain Home, Arkansas 72653"
    @selenium.type "location[1][address][city]", "Arkansas"
    @selenium.type "location[1][address][postal_code]", "72653"
    @selenium.select "location[1][address][state_province_id]", "label=Arkansas"
    @selenium.type "location[1][address][city]", "Mountain Home"
    
    #submit form
    assert_equal "Save and New", @selenium.get_value("//input[@type='submit' and @value='Save and New']")
    @page.click_and_wait "//input[@type='submit' and @value='Save and New']"
    assert @selenium.is_text_present("Your Household contact record has been saved.")
  end

  #edit household information
  def edit_household
    #click Edit button
    assert_equal "Edit", @selenium.get_value("//input[@type='button' and @value='Edit']")
    @page.click_and_wait "//input[@type='button' and @value='Edit']"
    
    #add/modify details
    @selenium.type "nick_name", "My Household CiviCRM"
    @selenium.select "location[1][im][1][provider_id]", "label=Jabber"
    @selenium.select "location[1][phone][1][phone_type]", "label=Mobile"
    
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Edit_next_view']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Edit_next_view']"
    assert @selenium.is_text_present("Your Household contact record has been saved.")
  end
  
  #delete household
  def delete_household
    #click Delete button
    assert_equal "Delete", @selenium.get_value("//input[@type='button' and @value='Delete']")
    @page.click_and_wait "//input[@type='button' and @value='Delete']"
    
    assert_equal "Delete Contacts", @selenium.get_value("//input[@type='submit' and @name='_qf_Delete_done']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Delete_done']"
    assert @selenium.is_text_present("Selected contact was deleted sucessfully.")
  end
  
  #vCard 
  def vCard_household
    assert_equal "vCard", @selenium.get_value("vCard_export")
    @page.click_and_wait "vCard_export"
  end
  
  def search_household
    #search for a particular household
    assert_equal "Find Contacts", @selenium.get_text("link=Find Contacts")
    @page.click_and_wait "link=Find Contacts"
    assert @selenium.is_text_present("Find Contacts")
    
    #enter search value
    @selenium.select "document.Search.contact_type", "label=Households"
    @selenium.type "document.Search.sort_name", "CiviCRM Household"
    
    #click search     
    @page.click_and_wait "document.Search._qf_Search_refresh"
    
    #click name
    assert @selenium.is_text_present("CiviCRM Household")
    assert_equal "CiviCRM Household", @selenium.get_text("link=CiviCRM Household")
    @page.click_and_wait "link=CiviCRM Household"
  end
end
