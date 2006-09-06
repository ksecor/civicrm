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
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    # Clicking online contribution page
    @selenium.get_text("//a[@id='id_ConfigureOnlineContributionPages']")
    @page.click_and_wait "//a[@id='id_ConfigureOnlineContributionPages']"
  end
  
  def teardown
    @page.logout
  end
  
  # Add new contribution page information (step 1 of 6)
  def test_1_addOnlineContribution
    assert_equal "New Contribution Page", @selenium.get_text("link=New Contribution Page")
    @page.click_and_wait "link=» New Contribution Page"
    
    # Read new contribution page information
    @selenium.type "title", "contribution Page 1"
    @selenium.select "contribution_type_id", "label=Donation"
    @selenium.type "intro_text", "Introductory Message"
    @selenium.type "footer_text", "Footer Message"
    @selenium.type "goal_amount", "100"
    @selenium.click "is_thermometer"
    @selenium.type "thermometer_title", "test Thermometer"
    
    # Submit the form 
    @page.click_and_wait "_qf_Settings_next"

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
    @page.click_and_wait "_qf_Amount_next"
    
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
    @page.click_and_wait "_qf_MembershipBlock_next"
    
    # Thank-you and Receipting (step 4 of 6)
    @selenium.type "thankyou_title", "Thank-you Page"
    @selenium.type "thankyou_text", "Thank-you Message"
    @selenium.type "thankyou_footer", "Thank-you Page Footer"
    @selenium.type "receipt_from_name", "Abhilasha"
    @selenium.type "receipt_from_email", "abhilasha@webaccess.co.in"
    @selenium.type "receipt_text", "Receipt Message"
  
    # Click button
    @page.click_and_wait "_qf_ThankYou_next"
    
    # Custom Page Elements (step 5 of 6)
    @selenium.select "custom_pre_id", "label=Constituent Information"
    @selenium.select "custom_post_id", "label=Contributor Info"

    # Click button
    @page.click_and_wait "_qf_Custom_next"
    
    # Configure Premiums (step 6 of 6)
    @selenium.click "premiums_active"
    @selenium.type "premiums_intro_title", "Premium Section"
    @selenium.type "premiums_intro_text", "Introductory Message"
    @selenium.type "premiums_contact_email", "abhilasha@webaccess.co.in"
    @selenium.type "premiums_contact_phone", "9867564649"
    @selenium.click "premiums_display_min_contribution"

    # Click button
    @page.click_and_wait "_qf_Premium_next"
  end
  
  # Editing online contribution information
  def test_2_1_configure_contribution_page
    # Clicking Configure
    assert_equal "Configure", @selenium.get_text("link=Configure")
    @page.click_and_wait "link=Configure"

    # Clicking Title & Settings Page 
    assert_equal "» Title and Settings", @selenium.get_text("link=» Title and Settings")
    @page.click_and_wait "link=» Title and Settings"
#    @page.click_and_wait "//div[@id='idTitleAndSettings']/descendant::tr[td[contains(.,'contribution Page 1')]]"

    @selenium.select "contribution_type_id", "label=Campaign Contribution"
  
    @page.click_and_wait "_qf_Settings_next"
  end

  # Editing online contribution information
  def test_2_2_configure_contribution_page
    # Clicking Configure
    assert_equal "Configure", @selenium.get_text("link=Configure")
    @page.click_and_wait "link=Configure"

    # Clicking Configure Support CiviCRM!
    assert_equal "» Contribution Amounts", @selenium.get_text("link=» Contribution Amounts")
    @page.click_and_wait "link=» Contribution Amounts"

    @selenium.type "min_amount", "100"

    @page.click_and_wait "_qf_Amount_next"
  end

  # Editing online contribution information
  def test_2_3_configure_contribution_page
    # Clicking Configure
    assert_equal "Configure", @selenium.get_text("link=Configure")
    @page.click_and_wait "link=Configure"
    
    # Clicking Membership Settings
    assert_equal "» Membership Settings", @selenium.get_text("link=» Membership Settings")
    @page.click_and_wait "link=» Membership Settings"
    
    @selenium.click "is_active"
    
    @page.click_and_wait "_qf_MembershipBlock_next"
  end
  
  # Editing online contribution information
  def test_2_4_configure_contribution_page
    # Clicking Configure
    assert_equal "Configure", @selenium.get_text("link=Configure")
    @page.click_and_wait "link=Configure"
    
    # Clicking Thank-you Message and Receipting
    assert_equal "» Thank-you and Receipting", @selenium.get_text("link=» Thank-you and Receipting")
    @page.click_and_wait "link=» Thank-you and Receipting"
    
    @selenium.click "is_email_receipt"
    @page.click_and_wait "_qf_ThankYou_next"
  end

  # Editing online contribution information
  def test_2_5_configure_contribution_page
    # Clicking Configure
    assert_equal "Configure", @selenium.get_text("link=Configure")
    @page.click_and_wait "link=Configure"
    
    # Clicking Custom Page Elements
    assert_equal "» Custom Page Elements", @selenium.get_text("link=» Custom Page Elements")
    @page.click_and_wait "link=» Custom Page Elements"
    
    @selenium.select "custom_pre_id", "label=- select -"
    @selenium.select "custom_post_id", "label=- select -"

    @page.click_and_wait "_qf_Custom_next"
  end

  # Editing online contribution information
  def test_2_6_configure_contribution_page
    # Clicking Configure
    assert_equal "Configure", @selenium.get_text("link=Configure")
    @page.click_and_wait "link=Configure"
    
    # Clicking Premiums Section
    assert_equal "» Premiums", @selenium.get_text("link=» Premiums")
    @page.click_and_wait "link=» Premiums"

    @selenium.click "//a[img/@alt='open section']"
    @selenium.click "premiums_display_min_contribution"
    
    @page.click_and_wait "_qf_Premium_next"    
  end

  # Editing online contribution information
  def test_2_7_configure_contribution_page
    # Clicking Configure
    assert_equal "Configure", @selenium.get_text("link=Configure")
    @page.click_and_wait "link=Configure"
    
    #Clicking Test drive
    assert_equal "» Test-drive", @selenium.get_text("link=» Test-drive")
    @page.click_and_wait "link=» Test-drive"
  end

  # Disable contributionPage
  def test_3_disable_contribution_page
    assert_equal "Disable", @selenium.get_text("link=Disable")
    @page.click_and_wait "link=Disable"
    assert_equal "Are you sure you want to disable this Contribution page?", @selenium.get_confirmation()
  end
  
  # Enable contribution page
  def test_4_enable_contribution_page
    assert_equal "Enable", @selenium.get_text("link=Enable")
    @page.click_and_wait "link=Enable"
  end
  
  # Delete contribution page
  def test_5_delete_contribution_page
    assert_equal "Delete", @selenium.get_text("link=Delete")
    @page.click_and_wait "link=Delete"
    assert_equal "Are you sure you want to delete this Contribution page?", @selenium.get_confirmation()
    assert @selenium.is_text_present("Are you sure you want to delete the contribution page \"contribution Page 1\"?\n")
    @page.click_and_wait "_qf_Delete_next"
    assert @selenium.is_text_present("The contribution page \"contribution Page 1\" has been deleted.")
  end
end
