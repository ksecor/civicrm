# This is a test case of using Selenium and its Ruby bindings
# Information' Gender definition

require 'test/unit'
require 'crm_page_controller'
require '../selenium'
# how many concurrent browser instances should be run?
#instances = 1
#threads = []

#instances.times do
#  threads << Thread.new do
 
class TestLoginOut < Test::Unit::TestCase
  
  def setup
    @page = CRMPageController.new
    @selenium = @page.startCivicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_checkText
    assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
  end
  
end
#  end
#end
# wait for all of the threads to finish
#threads.each { |thread| thread.join }
