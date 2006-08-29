require '../selenium'
require 'crm_config'

class CRMPageController
    
  def startCivicrm
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
    @selenium.click 'op'
    @selenium.wait_for_page_to_load "15000"
  end
  
  # log out of Drupal
  def logout()
    @selenium.click 'link=log out'
    @selenium.wait_for_page_to_load "15000"
    @selenium.stop
  end
  
  def click_and_wait what_to_click
    @selenium.click what_to_click
    @selenium.wait_for_page_to_load 300000
  end
  
end
