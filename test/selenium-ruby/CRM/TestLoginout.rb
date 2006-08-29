# This is a test case of using Selenium and its Ruby bindings
# Information' Gender definition

require 'crm_page_controller'

require 'test/unit'


require 'crm_config'
config = CRMConfig.new

# for Array#get_random_and_delete
require 'array'

# for String.gen_random
require 'string'

# how many concurrent browser instances should be run?
instances = 1
threads = []

instances.times do
  threads << Thread.new do
    
    @selenium = CRMPageController.new 'localhost', 4444, config.browser, config.uf_root
    @selenium.start
    @selenium.open config.login_url  
    
    # submit the form 
    # - Account information
    @selenium.type 'edit[name]', 'abhilasha'
    @selenium.type 'edit[pass]', 'abhi'
    
    #click button to login
    @selenium.click("op")
    @selenium.wait_for_page_to_load("15000")
    
    #exit
    @selenium.stop

  end
end
# wait for all of the threads to finish
threads.each { |thread| thread.join }
