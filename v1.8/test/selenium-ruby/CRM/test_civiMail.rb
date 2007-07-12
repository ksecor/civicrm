# This is a test case of using Selenium and its Ruby bindings
# Information' CiviMail definition
# This test case allows you to perform operation on information provided

require 'crm_page_controller'
require '../selenium'

class TC_TestCiviMail < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_civi_mail
    move_to_civiMail()
    check options of mailing headers and footers
    mailing_headers_and_footers()
    edit_mailing_headers_and_footers()
    disable_mailing_headers_and_footers()
    enable_mailing_headers_and_footers()

    #send mails
    send_mail()
    browse_sent_mails()
    report()
    resend_mail()
    cancel_mail()
    delete_mail()
  end
  
  def move_to_civiMail
    #Clicking CiviMail
    assert_equal "CiviMail", @selenium.get_text("link=CiviMail")
    @page.click_and_wait "link=CiviMail"
  end


  def mailing_headers_and_footers
    #clicking Mailing headers and footers
    assert_equal "Mailing Header / Footer", @selenium.get_text("link=Mailing Header / Footer")
    @page.click_and_wait "link=Mailing Header / Footer"

    assert_equal "» New Mailing Component", @selenium.get_text("link=» New Mailing Component")
    @page.click_and_wait "link=» New Mailing Component"

    @selenium.type "name", "New Mailing Component"
    @selenium.select "component_type", "label=Welcome Message"
    @selenium.type "subject", "Test Mailing Component"
    @selenium.type "body_text", "This is trail text for text format"
    @selenium.type "body_html", "\"Hello World\""
    @selenium.check "is_active"
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Component_next']"
    
    #check whether given name is already exist
    if @selenium.is_text_present("Name already exists in Database.")
      @page.click_and_wait "//input[@type='submit' and @name='_qf_Component_cancel']"
    else
      assert @selenium.is_text_present("The mailing component \"New Mailing Component\" has been saved.")
    end
  end

  def edit_mailing_headers_and_footers
    assert_equal "Edit", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Mailing Component')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Mailing Component')]]/descendant::a[contains(.,'Edit')]"

    @selenium.type "body_text", "This is a test component for mail to check the functionality of CiviMail"
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Component_next']"

    assert @selenium.is_text_present("The mailing component \"New Mailing Component\" has been saved.")
  end

  def disable_mailing_headers_and_footers
    assert_equal "Disable", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Mailing Component')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Mailing Component')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this component?", @selenium.get_confirmation()

  end

  def enable_mailing_headers_and_footers
    assert_equal "Enable", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'New Mailing Component')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'New Mailing Component')]]/descendant::a[contains(.,'Enable')]"
  end

  def send_mail
    #Clicking send Mail option
    if @selenium.is_text_present("There are no sent mails. You can send one.")
      assert @selenium.is_text_present("send one")
      @page.click_and_wait "link=send one"
    else
      assert_equal "Send Mailing", @selenium.get_text("link=Send Mailing")
      @page.click_and_wait "link=Send Mailing"
    end

    #Give Name to mailing(step 1 of 5)
    @selenium.type "name", "Test Mail 01"
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Name_next']"
    
    #Select Recipients (step 2 of 5)
    @selenium.add_selection "__includeGroups[]", "label=Advisory Board"
    @selenium.click "//input[@type='button' and @name='add']"
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Group_next']"

    #Upload Message (step 3 of 5)
    @selenium.type "from_name", "Abhilasha"
    @selenium.check "forward_reply"
    @selenium.check "track_urls"
    @selenium.check "track_opens"
    @selenium.select "header_id", "label=Mailing Header"
    @selenium.select "footer_id", "label=Mailing Footer"

    @selenium.type "textFile", "/home/abhilasha/Mydocs/IMPDOCS/civicrm/page1"
    @selenium.type "htmlFile", "/home/abhilasha/Mydocs/IMPDOCS/civicrm/testingMails.html"

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Upload_upload']"

    #Test (step 4 of 5)
    @page.click_and_wait "//a[img/@alt='open section']"
    @selenium.select "test_group", "label=Advisory Board"
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Test_next']"

    @selenium.select "start_date[Y]", "label=2006"
    @selenium.select "start_date[M]", "label=Nov"
    @selenium.select "start_date[d]", "label=13"
    @selenium.select "start_date[H]", "label=04"
    @selenium.select "start_date[i]", "label=00"
    @selenium.check "now"

    @page.click_and_wait "//input[@type='submit' and @name='_qf_Schedule_next']"
  end

  def resend_mail
    send_mail()
  end

  def browse_sent_mails
    assert_equal "Browse Sent Mailings", @selenium.get_text("link=Browse Sent Mailings")
    @page.click_and_wait "link=Browse Sent Mailings"
  end
  
  def report
    assert_equal "Report", @selenium.get_text("//div[@id='mailing']/descendant::tr[td[contains(.,'Test Mail 01')]]/descendant::a[contains(.,'Report')]")
    @page.click_and_wait "//div[@id='mailing']/descendant::tr[td[contains(.,'Test Mail 01')]]/descendant::a[contains(.,'Report')]"
  end
  
  def cancel_mail
    assert @selenium.is_text_present("Cancel")
    @page.click_and_wait "link=Cancel"
    assert_equal "Are you sure you want to cancel this mailing?", @selenium.get_confirmation()
    assert @selenium.is_text_present("The mailing has been canceled.")
  end

  def delete_mail
    assert @selenium.is_text_present("Delete")
    @page.click_and_wait "link=Delete"
    assert_equal "Are you sure you want to delete this mailing?", @selenium.get_confirmation()
    assert @selenium.is_text_present("Selected mailing has been deleted.")
  end
end
