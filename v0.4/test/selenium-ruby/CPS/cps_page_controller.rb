require 'test/unit'
require '../selenium'
require 'cps_config'
require '/usr/lib/ruby/1.8/rexml/document'
include REXML
require '/usr/lib/ruby/1.8/pathname.rb'
class CPSPageController
  
  def start_civicrm
    @config = CPSConfig.new
    @selenium = Selenium::SeleneseInterpreter.new('localhost', 4444, @config.browser, @config.uf_root)
    @selenium.start
    return @selenium
  end
  
  # login to Drupal
  def login()
    @selenium.open(@config.login_url)
    @selenium.type('name', @config.user)
    @selenium.type('pass', @config.pass)
    click_and_wait 'op'
    
    # Click link CiviCRM
    click_and_wait "link=CiviCRM"  
  end
  
  # log out of Drupal
  def logout()
    click_and_wait 'link=Log out'
    @selenium.stop
  end
  
  # Click an element (button, link, image etc.)
  def click_and_wait element
    @selenium.click element
    @selenium.wait_for_page_to_load 15000
  end
  
  def openFileDirectory()
    directory = Array.new()
    files = Array.new()
    
    #get directory XML
    dirObj = Pathname.new(@config.xml)
    #find its subdirectory
    directory = dirObj.children()
    
    directory.each{|file|
      fileName = String.new(file)
      files    = files.push(fileName.concat("/Application.xml"))
    }
    return files
  end 
  
end
    
