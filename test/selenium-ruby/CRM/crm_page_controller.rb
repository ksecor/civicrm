require 'test/unit'
require '../selenium'
require 'crm_config'

class CRMPageController
  
  def start_civicrm
    @config = CRMConfig.new
    @selenium = Selenium::SeleneseInterpreter.new('localhost', 4444, @config.browser, @config.uf_root)
    @selenium.start
    return @selenium
  end
  
  # login to Drupal
  def login()
    @selenium.open(@config.login_url)
    @selenium.type('edit[name]', @config.user)
    @selenium.type('edit[pass]', @config.pass)
    click_and_wait 'op'
    
    # Click link CiviCRM
    click_and_wait "link=CiviCRM"  
  end
  
  # log out of Drupal
  def logout()
    click_and_wait 'link=log out'
    @selenium.stop
  end
  
  # Click an element (button, link, image etc.)
  def click_and_wait element
    @selenium.click element
    @selenium.wait_for_page_to_load 30000
  end
end
