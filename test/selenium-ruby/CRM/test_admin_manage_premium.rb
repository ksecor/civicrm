# This is a test case of using Selenium and its Ruby bindings
# Information' manage Premium definition
# This test case allows you to add/edit/disable/enable/delete manage premium information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminManagePremium < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_manage_premium
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    assert_equal "Manage\nPremiums", @selenium.get_text("//a[@id='id_ManagePremiums']")
    @page.click_and_wait "//a[@id='id_ManagePremiums']"
    
    add_premium()
    edit_premium()
    enable_premium()
    disable_premium()
    delete_premium()
  end

  # Add new Manage Premium information
  def add_premium
    assert_equal "» New Premium", @selenium.get_text("link=» New Premium")
    @page.click_and_wait "link=» New Premium"
    
    # Read new Premium information
    @selenium.type  "product_name", "premium1"
    @selenium.type  "description", "Description"
    @selenium.type  "sku", "SKU1"
    @selenium.check "//input[@value='default_image']"
    @selenium.type  "min_contribution", "100"
    @selenium.type  "price", "150"
    @selenium.type  "cost", "120"
       
    @selenium.click  "//a[img/@alt='open section']"
    @selenium.type   "fixed_period_start_day", "0806"
    @selenium.type   "duration_interval", "1"
    @selenium.select "duration_unit", "label=Month"
    @selenium.type   "frequency_interval", "1"
    @selenium.select "duration_unit", "label=Year"
    @selenium.select "frequency_unit", "label=Month"
    @selenium.select "period_type", "label=Rolling"

    # Submit the form 
    @page.click_and_wait "_qf_ManagePremiums_upload"
    assert @selenium.is_text_present("The Premium Product \"premium1\" has been saved.")
  end
  
  # Editing Premium information
  def edit_premium
    assert_equal "Edit", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'premium1')]]/descendant::a[contains(.,'Edit')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'premium1')]]/descendant::a[contains(.,'Edit')]"
   
    @selenium.check "//input[@value='noImage']"
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_ManagePremiums_upload"
    assert @selenium.is_text_present("The Premium Product \"premium1\" has been saved.")
  end

  # Enable Premium type
  def enable_premium
    assert_equal "Enable", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'premium1')]]/descendant::a[contains(.,'Enable')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'premium1')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Disable Premium type
  def disable_premium
    assert_equal "Disable", @selenium.get_text("//div[@id='ltype']/descendant::tr[td[contains(.,'premium1')]]/descendant::a[contains(.,'Disable')]")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'premium1')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this premium? This action will remove the premium from any contribution pages that currently offer it. However it will not delete the premium record - so you can re-enable it and add it back to your contribution page(s) at a later time.", @selenium.get_confirmation()
  end
  
  # Delete Premium type
  def delete_premium
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'premium1')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("Are you sure you want to delete this premium? This action cannot be undone. This will also remove the premium from any contribution pages that currently include it. ")
    @page.click_and_wait"_qf_ManagePremiums_next"
    assert @selenium.is_text_present("Selected Premium Product type has been deleted.")
  end
end
