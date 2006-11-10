# This is a test case of using Selenium and its Ruby bindings
# Information' Configure Online Contribution Page definition
# This test case allows you to add/edit/disable/enable/delete Configure Online Contribution Page information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminOnlineContribution < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
  end
  
  def teardown
    @page.logout
  end
  
  def test_online_contribution
    move_to_online_contribution_page()

    add_online_contribution()
    configure_click()
    configure_title_settings_page()
    configure_amounts_page()
    configure_membership_settings_page()
    configure_thankyou_page()
    configure_custom_page()
    configure_premiums_page()
    #configure_testdrive_page()
    back_to_online_contribution_page()
    disable_contribution_page()
    enable_contribution_page()
    #contribution_page_testdrive()
    delete_contribution_page()
  end

  def move_to_online_contribution_page
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Clicking online contribution page
    @selenium.get_text("//a[@id='id_ConfigureOnlineContributionPages']")
    @page.click_and_wait "//a[@id='id_ConfigureOnlineContributionPages']"
  end

  def back_to_online_contribution_page
    assert @selenium.is_text_present("Configure Online Contribution Pages")
    assert_equal "Configure Online Contribution Pages", @selenium.get_text("link=Configure Online Contribution Pages")
    @page.click_and_wait "link=Configure Online Contribution Pages"
  end
  def configure_click
    #click Configure to edit details
    assert @selenium.is_element_present("//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Configure')]")
    @page.click_and_wait "//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Configure')]"
  end
  
  # Add new contribution page information (step 1 of 6)
  def add_online_contribution
    assert_equal "» New Contribution Page", @selenium.get_text("//a[contains(text(),'»  New Contribution Page')]")  
    @page.click_and_wait "link=» New Contribution Page"
    
    # Read new contribution page information
    @selenium.type "title", "Test Contribution Page"
    @selenium.select "contribution_type_id", "label=Donation"
    @selenium.type "intro_text", "Introductory Message"
    @selenium.type "footer_text", "Footer Message"
    @selenium.type "goal_amount", "100"
    @selenium.click "is_thermometer"
    @selenium.type "thermometer_title", "test Thermometer"
    
    # Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Settings_next']"

    # Contribution Amounts page (step 2 of 6) 
    @selenium.click "is_allow_other_amount"
    @selenium.type "min_amount", "100"
    @selenium.type "max_amount", "100000"

    # Some Fixed contribution amounts
    @selenium.type "label[1]", "label1"
    @selenium.type "value[1]", "100"
    @selenium.click "default"
    @selenium.type "label[2]", "label2"
    @selenium.type "value[2]", "200"
    @selenium.type "label[3]", "label3"
    @selenium.type "value[3]", "300"
    @selenium.type "label[4]", "label4"
    @selenium.type "value[4]", "400"
    @selenium.type "label[5]", "label5"
    @selenium.type "value[5]", "500"

    # Click button
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Amount_next']"
    
    # Configure Membership page (step 3 of 6)
    @selenium.type "new_title", "New Membership"
    @selenium.type "new_text", "Introductory Message"
    @selenium.type "renewal_title", "Renewals"
    @selenium.type "new_text", "Introductory Message - New Memberships"
    @selenium.type "renewal_text", "Introductory Message - Renewals"
    @selenium.check "//input[@type='checkbox' and @value='1']"
    @selenium.click "is_required"
    @selenium.click "display_min_fee"
    @selenium.click "is_separate_payment"
    @selenium.click "is_active"
    
    # Click button
    @page.click_and_wait "//input[@type='submit' and @name='_qf_MembershipBlock_next']"
    
    # Thank-you and Receipting (step 4 of 6)
    @selenium.type "thankyou_title", "Thank-you Page"
    @selenium.type "thankyou_text", "Thank-you Message"
    @selenium.type "thankyou_footer", "Thank-you Page Footer"
    @selenium.type "receipt_from_name", "Abhilasha"
    @selenium.type "receipt_from_email", "abhilasha@webaccess.co.in"
    @selenium.type "receipt_text", "Receipt Message"
  
    # Click button
    @page.click_and_wait "//input[@type='submit' and @name='_qf_ThankYou_next']"
    
    # Custom Page Elements (step 5 of 6)
    @selenium.select "custom_pre_id", "label=- select -"
    @selenium.select "custom_post_id", "label=- select -"

    # Click button
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Custom_next']"
    
    # Configure Premiums (step 6 of 6)
    @selenium.click "premiums_active"
    @selenium.type "premiums_intro_title", "Premium Section"
    @selenium.type "premiums_intro_text", "Introductory Message"
    @selenium.type "premiums_contact_email", "abhilasha@webaccess.co.in"
    @selenium.type "premiums_contact_phone", "9867564649"
    @selenium.click "premiums_display_min_contribution"

    # Click button
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Premium_next']"
  end
  
  # Editing online contribution information
  def configure_title_settings_page
    # Clicking Title & Settings Page 
    assert_equal "» Title and Settings", @selenium.get_text("link=» Title and Settings")
    @page.click_and_wait "link=» Title and Settings"

    @selenium.select "contribution_type_id", "label=Campaign Contribution"
  
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Settings_next']"
  end

  # Editing online contribution information
  def configure_amounts_page
    # Clicking Configure Support CiviCRM!
    assert_equal "» Contribution Amounts", @selenium.get_text("link=» Contribution Amounts")
    @page.click_and_wait "link=» Contribution Amounts"

    @selenium.type "min_amount", "100"

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Amount_next']"
  end

  # Editing online contribution information
  def configure_membership_settings_page
    # Clicking Membership Settings
    assert_equal "» Membership Settings", @selenium.get_text("link=» Membership Settings")
    @page.click_and_wait "link=» Membership Settings"
    
    # @selenium.check "is_active"
    # @selenium.check "//input[@type='checkbox' and @name='__membership_type[1]']"
    # @selenium.check "membership_type_default"
    
    @page.click_and_wait "//input[@type='submit' and @name='_qf_MembershipBlock_next']"
  end
  
  # Editing online contribution information
  def configure_thankyou_page
    # Clicking Thank-you Message and Receipting
    assert_equal "» Thank-you and Receipting", @selenium.get_text("link=» Thank-you and Receipting")
    @page.click_and_wait "link=» Thank-you and Receipting"
    
    @selenium.check "is_email_receipt"

    assert_equal "Save", @selenium.get_value("_qf_ThankYou_next")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_ThankYou_next']"
  end

  # Editing online contribution information
  def configure_custom_page
    # Clicking Custom Page Elements
    assert_equal "» Custom Page Elements", @selenium.get_text("link=» Custom Page Elements")
    @page.click_and_wait "link=» Custom Page Elements"
    
    @selenium.select "custom_pre_id", "label=- select -"
    @selenium.select "custom_post_id", "label=- select -"

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Custom_next']"
  end

  # Editing online contribution information
  def configure_premiums_page
    # Clicking Premiums Section
    assert_equal "» Premiums", @selenium.get_text("link=» Premiums")
    @page.click_and_wait "link=» Premiums"

    @selenium.click "//a[img/@alt='open section']"
    @selenium.click "premiums_display_min_contribution"
    
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Premium_next']"    
  end

  # Editing online contribution information
  def configure_testdrive_page
    #Clicking Test drive
    assert_equal "» Test-drive", @selenium.get_text("link=» Test-drive")
    @page.click_and_wait "link=» Test-drive"
    
    @selenium.select "credit_card_type", "label=Visa"
    @selenium.type "credit_card_number", "4111111111111111"
    @selenium.type "cvv2", "123"
    @selenium.type "first_name", "Abhilasha"
    @selenium.type "last_name", "Vasu"
    @selenium.select "credit_card_exp_date[M]", "label=Oct"
    @selenium.select "credit_card_exp_date[Y]", "label=2011"
    @selenium.type "street_address", "b-1069"
    @selenium.type "city", "Mumbai"
    @selenium.select "state_province_id", "label=California"
    @selenium.type "postal_code", "400072"
    @selenium.select "country_id", "label=United States"
    @selenium.check "document.Main.amount[1]"

    @page.click_and_wait "//input[@type='image' and @id='_qf_Main_next_express']"    
  end

  #TestDrive to contribution page
  def contribution_page_testdrive
    assert @selenium.is_element_present("//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Test-drive')]")
    @page.click_and_wait "//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Test-drive')]"

     assert @selenium.is_text_present("Test-drive Your Contribution Page\n\nThis page is currently running in test-drive mode. Transactions will be sent to your payment processor's test server. No live financial transactions will be submitted. However, a contact record will be created or updated and a contribution record will be saved to the database. Use obvious test contact names so you can review and delete these records as needed. Refer to your payment processor's documentation for information on values to use for test credit card number, security code, postal code, etc.")
   
    @selenium.select "credit_card_type", "label=Visa"
    @selenium.type "credit_card_number", "4111111111111111"
    @selenium.type "cvv2", "123"
    @selenium.type "first_name", "Abhilasha"
    @selenium.type "last_name", "Vasu"
    @selenium.select "credit_card_exp_date[M]", "label=Oct"
    @selenium.select "credit_card_exp_date[Y]", "label=2011"
    @selenium.type "street_address", "b-1069"
    @selenium.type "city", "Mumbai"
    @selenium.select "state_province_id", "label=California"
    @selenium.type "postal_code", "400072"
    @selenium.select "country_id", "label=United States"
    @selenium.wait_for_page_to_load 15000
    assert @selenium.is_text_present("Your contribution will not be completed until you click the Make Contribution button. Please click the button one time only.")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Confirm_next']"
  end

  # Disable contributionPage
  def disable_contribution_page
     assert @selenium.is_element_present("//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this Contribution page?", @selenium.get_confirmation()
  end
  
  # Enable contribution page
  def enable_contribution_page
    assert @selenium.is_element_present("//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Delete contribution page
  def delete_contribution_page
    assert @selenium.is_element_present("//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Delete')]")
    @page.click_and_wait "//div[@id='configure_contribution_page']/descendant::tr[td[contains(.,'Test Contribution Page')]]/descendant::a[contains(.,'Delete')]"

    assert_equal "Are you sure you want to delete this Contribution page?", @selenium.get_confirmation()
    assert @selenium.is_text_present("Are you sure you want to delete the contribution page \"Test Contribution Page\"?\n")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Delete_next']"
    assert @selenium.is_text_present("The contribution page \"Test Contribution Page\" has been deleted.")
  end
end
