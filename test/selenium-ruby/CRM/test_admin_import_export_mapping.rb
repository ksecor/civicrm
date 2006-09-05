# This is a test case of using Selenium and its Ruby bindings
# Information' Import Export Mapping definition
# This test case allows you to add/edit/disable/enable/delete Import export mapping information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminImportExportMapping < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
    
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click import-Export Mapping
    assert_equal "Import/Export\nMappings", @selenium.get_text("//a[@id='id_Import_ExportMappings']")
    @page.click_and_wait "//a[@id='id_Import_ExportMappings']"
  end
  
  def teardown
    @page.logout
  end
  
  # Edit import-export mapping information
  def test_1_editMapping
    if !@selenium.is_text_present("There are currently no saved import or export mappings. You create saved mappings as part of an Import or Export task.")
      @page.click_and_wait "link=Edit"
      @selenium.type "name", "importMapping"
      
      #submit form
      @page.click_and_wait "_qf_Mapping_next"
    end
  end
  # Delete import-export mapping information
  def test_2_deleteMapping
    if !@selenium.is_text_present("There are currently no saved import or export mappings. You create saved mappings as part of an Import or Export task.")
      @page.click_and_wait "link=Delete"
      assert @selenium.is_text_present("WARNING : Are you sure you want to delete Import Mapping? This action can not be undone.")
      
      #submit form
      @page.click_and_wait "_qf_Mapping_next"
    end
  end
end
