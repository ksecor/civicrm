# This is a test case of using Selenium and its Ruby bindings
# Information' contribution definition
# This test case allows you to add contribution and perform operations on information available

require 'crm_page_controller'
require '../selenium'


class TC_TestContactContribution < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_contact_contribution
    #require 'test_new_individual'
    #add an Individual (using file test_new_individual)
    # @addIndividual = TC_TestNewIndividual.new

    # @addIndividual.go_to_new_individual
    # @addIndividual.add_new_individual

    #find a particular record
    search_individual()
 
    #select Relationship
    contribution_click()
    
    add_contribution()
    view_contribution()
    edit_contribution()
    delete_contribution()
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

  def contribution_click
    #click contribution link
    assert @selenium.is_text_present("Contributions")
    @page.click_and_wait "link=Contributions"
  end
  
  def add_contribution
    #add contribution  
    assert @selenium.is_text_present("There are no contributions recorded for this contact. You can enter one now.")
    assert_equal "enter one now", @selenium.get_text("link=enter one now")
    @page.click_and_wait "link=New Contribution"
    
    #add details
    @selenium.select "contribution_type_id", "label=Donation"
    @selenium.select "receive_date[d]", "label=15"
    @selenium.select "receive_date[M]", "label=Sep"
    # @selenium.select "payment_instrument_id", "label=Credit Card"
    @selenium.type "source", "contribution for old age home"
    @selenium.type "total_amount", "1000"
    @selenium.type "non_deductible_amount", "0"
    @selenium.type "fee_amount", "100"
    @selenium.type "net_amount", "900"
    @selenium.type "invoice_id", "I101"
    @selenium.type "trxn_id", "T101"
    @selenium.select "receipt_date[M]", "label=Sep"
    @selenium.select "receipt_date[d]", "label=20"
    @selenium.select "receipt_date[Y]", "label=2006"
    @selenium.select "receipt_date[d]", "label=18"
    @selenium.select "thankyou_date[M]", "label=Sep"
    @selenium.select "thankyou_date[d]", "label=20"
    @selenium.select "thankyou_date[Y]", "label=2006"
    @selenium.select "receive_date[Y]", "label=2006"
       
    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Contribution_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Contribution_next']"
  end  

  def view_contribution
    assert_equal "View", @selenium.get_text("link=View")
    @page.click_and_wait "link=View"

    assert_equal "Done", @selenium.get_value("//input[@type='submit' and @name='_qf_ContributionView_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_ContributionView_next']"

  end
  
  def edit_contribution
    assert_equal "Edit", @selenium.get_text("link=Edit")
    @page.click_and_wait "link=Edit"

    #modify details
   # @selenium.select "payment_instrument_id", "label=Check"
    @selenium.type   "non_deductible_amount", "50"

    #submit form
    assert_equal "Save", @selenium.get_value("//input[@type='submit' and @name='_qf_Contribution_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Contribution_next']"
  end

  def delete_contribution
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"

    assert @selenium.is_text_present("WARNING: Deleting this contribution will result in the loss of the associated financial transactions (if any). Do you want to continue?")

    #submit form
    assert_equal "Delete", @selenium.get_value("//input[@type='submit' and @name='_qf_Contribution_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Contribution_next']"
  end
end
