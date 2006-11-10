# This is a test case of using Selenium and its Ruby bindings
# Information' New Organization definition
# This test case allows you to add/edit/delete new organization information

require 'crm_page_controller'
require '../selenium'

class TC_TestNewOrganization < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_new_organization
    go_to_civicrm()
    go_to_new_organization()

    #add a new organization
    add_new_organization()

    #select save & new option
    go_to_new_organization()
    save_and_new_organization()

    #edit organization
    click_cancel()
    go_to_civicrm()
    search_organization()
    edit_organization()
    
    #delete organization
    delete_organization()
    
    #export vCard
    #vCard_new_organization()
  end

  def go_to_civicrm
    #click CiviCRM
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.click_and_wait "link=CiviCRM"
  end

  def go_to_new_organization
    #click New Organization
    assert_equal "New Organization", @selenium.get_text("link=New Organization")
    @page.click_and_wait "link=New Organization"
  end

   def click_cancel
     #goto Contact Summary after clicking cancel button of form
     assert_equal "Cancel", @selenium.get_value("_qf_Edit_cancel")
     @page.click_and_wait "//input[@type='submit' and @value='Cancel']"
   end

  def add_new_organization
    #add details of an organizations
    @selenium.type "organization_name", "CiviCRM Organization"
    @selenium.type "legal_name", "Web Access"
    @selenium.type "sic_code", "s101"
    @selenium.type "home_URL", "http://www.webaccess.co.in"
    @selenium.type "nick_name", "WA"
    @selenium.check  "//input[@type='checkbox' and @name='privacy[do_not_phone]']"
    @selenium.check  "//input[@type='checkbox' and @name='privacy[do_not_mail]']"     
   
    @selenium.check  "//input[@type='checkbox' and @name='preferred_communication_method[2]']"
    @selenium.check  "//input[@type='checkbox' and @name='preferred_communication_method[4]']"
    @selenium.select "preferred_mail_format", "label=HTML"
    @selenium.select "document.Edit.elements['location[1][location_type_id]']", "label=Work"
    @selenium.type "location[1][name]", "Web Access India Pvt Ltd"
    @selenium.select "location[1][phone][1][phone_type]", "label=Phone"
    @selenium.type "location[1][phone][1][phone]", "022-2374-2377378"
    @selenium.type "document.Edit.elements['location[1][email][1][email]']", "mail@webaccess.co.in"
    @selenium.select "location[1][im][1][provider_id]", "label=MSN"
    @selenium.type "location[1][address][street_address]", "chandivali"
    @selenium.type "location[1][address][supplemental_address_1]", "andheri (e)"
    @selenium.type "location[1][address][street_address]", "oberoi garden estate, chandivali"
    @selenium.type "location[1][address][city]", "mumbai"
    @selenium.select "location[1][address][state_province_id]", "label=California" 
    @selenium.type "location[1][address][postal_code]", "400072"
    
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Edit_next_view']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Edit_next_view']"

    if @selenium.is_text_present("Your Organization contact record has been saved.")
      assert @selenium.is_text_present("Your Organization contact record has been saved.")
    else
      assert @selenium.is_text_present("One matching contact was found. You can edit it here: CiviCRM Organization, or click Save Duplicate Contact button below.")
    end
  end
  
  def save_and_new_organization
    #add details of an organizations
    @selenium.type "organization_name", "New Organization"
    @selenium.type "legal_name", "New Org"
    @selenium.type "sic_code", "s102"
    @selenium.type "home_URL", "http://www.newOrg.co.in"
    @selenium.type "nick_name", "newOrg"
    @selenium.check  "//input[@type='checkbox' and @name='privacy[do_not_mail]']"     
    @selenium.check  "//input[@type='checkbox' and @name='preferred_communication_method[2]']"
    @selenium.check  "//input[@type='checkbox' and @name='preferred_communication_method[4]']"
    @selenium.select "preferred_mail_format", "label=HTML"
    @selenium.select "document.Edit.elements['location[1][location_type_id]']", "label=Work"
    @selenium.type "location[1][name]", "New Organization Pvt Ltd"
    @selenium.select "location[1][phone][1][phone_type]", "label=Phone"
    @selenium.type "location[1][phone][1][phone]", "022-2374-2377378"
    @selenium.type "document.Edit.elements['location[1][email][1][email]']", "mail@newOrg.co.in"
    @selenium.select "location[1][im][1][provider_id]", "label=Yahoo"
    @selenium.type "location[1][address][street_address]", "chandivali"
    @selenium.type "location[1][address][supplemental_address_1]", "andheri (e)"
    @selenium.type "location[1][address][street_address]", "oberoi garden estate, chandivali"
    @selenium.type "location[1][address][city]", "mumbai"
    @selenium.select "location[1][address][state_province_id]", "label=California" 
    @selenium.type "location[1][address][postal_code]", "400072"

    #submit form
    assert_equal "Save and New", @selenium.get_value("//input[@type='submit' and @name='_qf_Edit_next_new']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Edit_next_new']"
   
    if @selenium.is_text_present("Your Organization contact record has been saved.")
      assert @selenium.is_text_present("Your Organization contact record has been saved.")
    else
      assert @selenium.is_text_present("One matching contact was found. You can edit it here: New Organization, or click Save Duplicate Contact button below.")
    end
  end

  #edit organization contact
  def edit_organization 
    assert_equal "Edit", @selenium.get_value("//input[@type='button' and @value='Edit']")
    @page.click_and_wait "//input[@type='button' and @value='Edit']"
    
    #add/modify details
    @selenium.click "//a[img/@alt='open section']"
    @selenium.click "group[1]"
    @selenium.click "tag[3]"
    @selenium.click "tag[4]"
   
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Edit_next_view']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Edit_next_view']"
    assert @selenium.is_text_present("Your Organization contact record has been saved.")
  end

  #delete organization
  def delete_organization
    assert_equal "Delete", @selenium.get_value("//input[@type='button' and @value='Delete']")
    @page.click_and_wait "//input[@type='button' and @value='Delete']"
    
    #confirm delete
    assert_equal "Delete Contacts", @selenium.get_value("//input[@type='submit' and @name='_qf_Delete_done']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Delete_done']"
    assert @selenium.is_text_present("Selected contact was deleted sucessfully.")
  end
  
  #vCard 
  def vCard_organization
    assert_equal "vCard", @selenium.get_value("vCard_export")
    @page.click_and_wait "vCard_export"
  end
  
  def search_organization
    #search for a particular organization
    assert_equal "Find Contacts", @selenium.get_text("link=Find Contacts")
    @page.click_and_wait "link=Find Contacts"
    assert @selenium.is_text_present("Find Contacts")
    
    #enter search value
    @selenium.select "document.Search.contact_type", "label=Organizations"
    @selenium.type "document.Search.sort_name", "CiviCRM Organization"
    
    #click search     
    @page.click_and_wait "document.Search._qf_Search_refresh"
    
    #click name
    assert @selenium.is_text_present("CiviCRM Organization")
    assert_equal "CiviCRM Organization", @selenium.get_text("link=CiviCRM Organization")
    @page.click_and_wait "link=CiviCRM Organization"
  end
end
