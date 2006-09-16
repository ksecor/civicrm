# This is a test case of using Selenium and its Ruby bindings
# Information' New Individual definition
# This test case allows you to add/edit/delete new individual profile information

require 'crm_page_controller'
require '../selenium'

class TC_TestNewIndividual < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_new_individual
    go_to_civicrm()
    go_to_new_individual()

    #add a new individual
    add_new_individual()

    #select save & new option
    go_to_new_individual()
    save_and_new_individual()

    #delete individual
    click_cancel()
    go_to_civicrm()
    search_individual()
    delete_individual()
    
    #export vCard
    #vCard_new_individual()
  end

  def go_to_civicrm
    #click CiviCRM
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
    @page.click_and_wait "link=CiviCRM"
  end

  def go_to_new_individual
    #click New Individual
    assert_equal "New Individual", @selenium.get_text("link=New Individual")
    @page.click_and_wait "link=New Individual"
  end

   def click_cancel
     #goto Contact Summary after clicking cancel button of form
     assert_equal "Cancel", @selenium.get_value("_qf_Edit_cancel")
     #assert_equal "Cancel", @selenium.get_value("//input[@type='submit' and @value='Cancel']")
     @page.click_and_wait "//input[@type='submit' and @value='Cancel']"
   end

  def add_new_individual
    #add details of an individuals
    @selenium.type "document.Edit.first_name", "Abhilasha"
    @selenium.type "middle_name", "Vijay"
    @selenium.type "document.Edit.last_name", "Vasu"
    @selenium.type "job_title", "Soft Developer"
    @selenium.select "greeting_type", "label=Dear [first]"
    @selenium.type "nick_name", "Angel"
    @selenium.click "__privacy[do_not_phone]"
    @selenium.click "__privacy[do_not_email]"
    @selenium.click "__preferred_communication_method[4]"
    @selenium.click "__preferred_communication_method[2]"
    @selenium.click "__preferred_communication_method[1]"
    @selenium.type "location[1][name]", "Chandivali"
    @selenium.select "location[1][phone][1][phone_type]", "label=Mobile"
    @selenium.type "location[1][phone][1][phone]", "09867564649"
    @selenium.type "document.Edit.elements['location[1][email][1][email]']", "abhilasha@webaccess.co.in"
    @selenium.type "location[1][im][1][name]", "MSN"
    @selenium.type "location[1][address][street_address]", "S 116W Northpoint Blvd SE"
    @selenium.type "location[1][address][supplemental_address_1]", "Editorial Dept"
    @selenium.type "location[1][address][supplemental_address_2]", "andheri -E"
    @selenium.type "location[1][address][supplemental_address_1]", "chandivali"
    @selenium.type "location[1][address][street_address]", "b-1069,Oberoi Garden"
    @selenium.type "location[1][address][city]", "Mumbai"
    @selenium.select "location[1][address][state_province_id]", "label=California"
    @selenium.type "location[1][address][postal_code]", "400072"
    @selenium.select "location[1][address][country_id]", "label=United States"
    @selenium.type "location[1][address][geo_code_1]", "38.6151"
    @selenium.type "location[1][address][geo_code_2]", "-77.7073"
    @selenium.select "preferred_mail_format", "label=Both"

    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Edit_next_view']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Edit_next_view']"
    assert @selenium.is_text_present("Your Individual contact record has been saved.")
  end

  def save_and_new_individual
     #add details of an individuals
    @selenium.type "document.Edit.first_name", "Vivek"
    @selenium.type "middle_name", "Vijay"
    @selenium.type "document.Edit.last_name", "Vasu"
    @selenium.type "job_title", "Soft Developer"
    @selenium.select "greeting_type", "label=Dear [first]"
    @selenium.type "nick_name", "Vivek"
    @selenium.click "__privacy[do_not_phone]"
    @selenium.click "__privacy[do_not_email]"
    @selenium.click "__preferred_communication_method[4]"
    @selenium.click "__preferred_communication_method[2]"
    @selenium.click "__preferred_communication_method[1]"
    @selenium.type "location[1][name]", "Chandivali"
    @selenium.select "location[1][phone][1][phone_type]", "label=Mobile"
    @selenium.type "location[1][phone][1][phone]", "09867564649"
    @selenium.type "document.Edit.elements['location[1][email][1][email]']", "abhilasha@webaccess.co.in"
    @selenium.type "location[1][im][1][name]", "MSN"
    @selenium.type "location[1][address][street_address]", "S 116W Northpoint Blvd SE"
    @selenium.type "location[1][address][supplemental_address_1]", "Editorial Dept"
    @selenium.type "location[1][address][supplemental_address_2]", "andheri -E"
    @selenium.type "location[1][address][supplemental_address_1]", "chandivali"
    @selenium.type "location[1][address][street_address]", "b-1069,Oberoi Garden"
    @selenium.type "location[1][address][city]", "Mumbai"
    @selenium.select "location[1][address][state_province_id]", "label=California"
    @selenium.type "location[1][address][postal_code]", "400072"
    @selenium.select "location[1][address][country_id]", "label=United States"
    @selenium.type "location[1][address][geo_code_1]", "38.6151"
    @selenium.type "location[1][address][geo_code_2]", "-77.7073"
    @selenium.select "preferred_mail_format", "label=Both"

    #submit form
    assert_equal "Save and New", @selenium.get_value("//input[@type='submit' and @name='_qf_Edit_next_new']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Edit_next_new']"
    assert @selenium.is_text_present("Your Individual contact record has been saved.")
  end

  #delete individual
  def delete_individual
    assert_equal "Delete", @selenium.get_value("contact_delete")
    @page.click_and_wait "contact_delete"
    
    #assert @selenium.is_text_present("Are you sure you want to Delete the selected contacts? A Delete operation cannot be undone.\n\nNumber of selected contacts: \"1\"")
    
    assert_equal "Delete Contacts", @selenium.get_value("//input[@type='submit' and @name='_qf_Delete_done']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Delete_done']"
    assert @selenium.is_text_present("Selected contact was deleted sucessfully.")
  end
  
  #vCard 
  def vCard_individual
    assert_equal "vCard", @selenium.get_value("vCard_export")
    @page.click_and_wait "vCard_export"
  end
  
  def search_individual
    #search for a particular individual
    assert_equal "Find Contacts", @selenium.get_text("link=Find Contacts")
    @page.click_and_wait "link=Find Contacts"
    assert @selenium.is_text_present("Find Contacts")
    
    #enter search value
    @selenium.select "document.Search.contact_type", "label=Individuals"
    @selenium.type "document.Search.sort_name", "Abhilasha Vasu"
    
    #click search     
    @page.click_and_wait "document.Search._qf_Search_refresh"
    
    #click name
    assert @selenium.is_text_present("Vasu, Abhilasha")
    assert_equal "Vasu, Abhilasha", @selenium.get_text("link=Vasu, Abhilasha")
    @page.click_and_wait "link=Vasu, Abhilasha"
  end
end
