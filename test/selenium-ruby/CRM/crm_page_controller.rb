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
    clickAndWait 'op'
  end
  
  # log out of Drupal
  def logout()
    clickAndWait 'link=log out'
    @selenium.stop
  end
  
  def clickAndWait element
    @selenium.click element
    @selenium.wait_for_page_to_load 300000
  end
  
end
