# This is a test case of using Selenium and its Ruby bindings
# Information' Membership definition
# This test case allows you to add memebrship & perform varoius operations on them

require 'crm_page_controller'
require '../selenium'

class TC_TestContactMembership < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
  end
  
  def teardown
    @page.logout
  end
  
  def test_contact_membership
    #find a particular record
    search_individual()
    
    #click Activity link
    membership_click()
    
    add_membership()
    view_membership()
    edit_membership()
    delete_membership()
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

  #Click Membership
  def membership_click
    #click activities
    assert_equal "Memberships", @selenium.get_text("link=Memberships")
    @page.click_and_wait "link=Memberships"
  end
  
  # Add new Membership information
  def add_membership
    #click on new membership link
    assert @selenium.is_element_present("//div[@id='help']/descendant::a[contains(.,'New Membership')]")
    @page.click_and_wait "//div[@id='help']/descendant::a[contains(.,'New Membership')]"
    #   @page.click_and_wait "link=New Membership"

    #add details
    @selenium.select "membership_type_id", "label=General"
    @selenium.type "source", "website"
    @selenium.select "join_date[M]", "label=Sep"
    @selenium.select "join_date[d]", "label=19"
    @selenium.select "join_date[Y]", "label=2006"
    @selenium.select "start_date[M]", "label=Sep"
    @selenium.select "start_date[d]", "label=25"
    @selenium.select "start_date[Y]", "label=2006"
    @selenium.select "end_date[M]", "label=Aug"
    @selenium.select "end_date[d]", "label=31"
    @selenium.select "end_date[Y]", "label=2007"
    @selenium.click "is_override"
    @selenium.select "status_id", "label=New"
    
    #submit form
    assert_equal "Save", @selenium.get_value("_qf_Membership_next")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Membership_next']"
    assert @selenium.is_text_present("The membership information has been saved.")
  end 
  
  # View Membership information
  def view_membership
    #click on view link
    assert @selenium.is_element_present("//div[@id='memberships']/descendant::tr[td[contains(.,'General')]]/descendant::a[contains(.,'View')]")
    @page.click_and_wait "//div[@id='memberships']/descendant::tr[td[contains(.,'General')]]/descendant::a[contains(.,'View')]"
  
    # Submit form    
    @page.click_and_wait "//input[@type='submit' and @name='_qf_MembershipView_next']"
  end

  # Editing Membership information
  def edit_membership
    assert @selenium.is_element_present("//div[@id='memberships']/descendant::tr[td[contains(.,'General')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='memberships']/descendant::tr[td[contains(.,'General')]]/descendant::a[contains(.,'Edit')]"

    #modify details
    @selenium.select "status_id", "label=Current"
    
    #submit form
    assert_equal "Save", @selenium.get_value("_qf_Membership_next")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Membership_next']"
    assert @selenium.is_text_present("The membership information has been saved.")
  end
  
  # Delete membership 
  def delete_membership
    assert @selenium.is_element_present("//div[@id='memberships']/descendant::tr[td[contains(.,'General')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='memberships']/descendant::tr[td[contains(.,'General')]]/descendant::a[contains(.,'Delete')]"

    assert @selenium.is_text_present("WARNING: Deleting this membership will also delete related membership log and payment records. This action can not be undone. Consider modifying the membership status instead if you want to maintain a record of this membership. Do you want to continue?")

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Membership_next']"
  end
end
