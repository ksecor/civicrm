# This is a test case of using Selenium and its Ruby bindings
# Information' Import contacts definition

require 'crm_page_controller'
require '../selenium'


class TC_TestImportContacts < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
  end
  
  def teardown
    @page.logout
  end
  
  def test_import_contacts
    import_contacts()
  end
  
  #allow user to upload the file (step 1 of 4)
  def import_contacts
    #upload a file
    assert_equal "Import", @selenium.get_text("link=Import")
    @page.click_and_wait "link=Import"

    #upload File
    # @filename= CRMUploadFile.new

    # $path_name="/home/abhilasha/expmap.csv"
    # $var_name="uploadFile"

    # @selenium.type("file",@filename.loadfromfile($file_path,$var_name))

    #add details
    @page.check "//input[@type='radio' and @value='1']"
#    @selenium.check "dateFormats"
    @selenium.type "uploadFile", "/home/abhilasha/expmap.csv"
    @page.check "skipColumnHeader"
   
    
    #submit form
    assert_equal "Continue >>", @selenium.get_value("//input[@type='submit' and @name='_qf_UploadFile_upload']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_UploadFile_upload']"

    #Match Fields (step 2 of 4)
     @selenium.check "saveMapping"
     @selenium.type "saveMappingName", "Import Contacts"
     @selenium.type "saveMappingDesc", "test import mapping"

    #submit form
     assert_equal "Continue >>", @selenium.get_value("_qf_MapField_next")
     @page.click_and_wait "//input[@type='submit' and @name='_qf_MapField_next']"
    
    #Preview (step 3 of 4)
    assert_equal "Import Now >>", @selenium.get_value("_qf_Preview_next")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Preview_next']"

    #Summary (step 4 of 4)
    assert_equal "Done", @selenium.get_value("_qf_Summary_next")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Summary_next']"
  end
end
