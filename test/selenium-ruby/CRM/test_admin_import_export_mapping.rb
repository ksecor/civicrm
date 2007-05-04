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
  end
  
  def teardown
    @page.logout
  end
  
  def test_import_export_mapping
    move_to_import_export_mapping()
    
    edit_mapping()
    delete_mapping()
  end
  
  def move_to_import_export_mapping
    #Click Administer CiviCRM
    assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
    @page.click_and_wait "link=Administer CiviCRM"
    
    #Click import-Export Mapping
    assert_equal "Import/Export\nMappings", @selenium.get_text("//a[@id='id_Import_ExportMappings']")
    @page.click_and_wait "//a[@id='id_Import_ExportMappings']"
  end
  
  # Edit import-export mapping information
  def edit_mapping
    if !@selenium.is_text_present("There are currently no saved import or export mappings. You create saved mappings as part of an Import or Export task.")
      @page.click_and_wait "link=Edit"
      @selenium.type "description", "The descrition is edited while testing by selenium-ruby test cases."
      
      #submit form
      @page.click_and_wait "_qf_Mapping_next"
    end
  end
  
  # Delete import-export mapping information
  def delete_mapping
    if ! @selenium.is_text_present("There are currently no saved import or export mappings. You create saved mappings as part of an Import or Export task.")
      @page.click_and_wait "link=Delete"
      if @selenium.is_element_present("//div[@id='mapping']/descendant::tr[td[contains(.,'Import')]]/td[3]")
        assert @selenium.is_text_present("WARNING : Are you sure you want to delete Import Mapping? This action can not be undone.")
      elsif @selenium.get_text("//div[@id='mapping']/descendant::tr[td[contains(.,'Export')]]/td[3]") == 'Export'
        assert @selenium.is_text_present("WARNING : Are you sure you want to delete Export Mapping? This action can not be undone.")
      end
            
      #submit form
      @page.click_and_wait "_qf_Mapping_next"
    end
  end
end
