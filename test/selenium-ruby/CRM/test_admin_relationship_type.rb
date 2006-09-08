# This is a test case of using Selenium and its Ruby bindings
# Information' Relationship definition
# This test case allows you to add/edit/disable/enable/delete relationship information

require 'crm_page_controller'
require '../selenium'

class TC_TestAdminRelationshipType < Test::Unit::TestCase
  def setup
    @page = CRMPageController.new
    @selenium = @page.start_civicrm
    @page.login
   
  end
  
  def teardown
    @page.logout
  end
  
 def test_relationship_type
   #Click Administer CiviCRM
   assert_equal "CiviCRM", @selenium.get_text("link=CiviCRM")
   @page.click_and_wait "link=CiviCRM"

   #Click Administer CiviCRM
   assert_equal "Administer CiviCRM", @selenium.get_text("link=Administer CiviCRM")
   @page.click_and_wait "link=Administer CiviCRM"

   #Click Relationship Type
   assert_equal "Relationship\nTypes", @selenium.get_text("//a[@id='id_RelationshipTypes']")
   @page.click_and_wait "//a[@id='id_RelationshipTypes']"

   add_relationship_type()
   view_relationship_type()
   edit_relationship_type()
   enable_relationship_type()
   disable_relationship_type()
   delete_relationship_type()
 end   
  
  # Add new Relationship type information
  def add_relationship_type
    assert_equal "» New Relationship Type", @selenium.get_text("link=» New Relationship Type")
    @page.click_and_wait "link=» New Relationship Type"
    
    # Read new Relationship information
    @selenium.type "name_a_b", "brother of"
    @selenium.type "name_b_a", "brother of"
    @selenium.select "contact_type_a", "label=Individuals"
    @selenium.select "contact_type_b", "label=Individuals"
    @selenium.type "description", "Cousion Brothers"
    
    # Submit the form 
    @page.click_and_wait "_qf_RelationshipType_next"
    assert @selenium.is_text_present("The Relationship Type has been saved.")
  end
  
  # View Relationship Types
  def view_relationship_type
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'brother of')]]/descendant::a[contains(.,'View')]"
    
    #Click Done after view
    @page.click_and_wait "done"
  end

  # Editing relationship type information
  def edit_relationship_type
    
    #Click Relationship Type
    assert @selenium.is_text_present("brother of")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'brother of')]]/descendant::a[contains(.,'Edit')]"
   
    @selenium.select "contact_type_a", "label=Organizations"
    @selenium.select "contact_type_b", "label=Organizations"
    @selenium.uncheck "is_active" 
    
    #Submit the form 
    @page.click_and_wait "_qf_RelationshipType_next"
    assert @selenium.is_text_present("The Relationship Type has been saved.")
  end
  
  # Disable Relationship Type
  def disable_relationship_type
    assert @selenium.is_text_present("brother of")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'brother of')]]/descendant::a[contains(.,'Disable')]"
    assert_equal "Are you sure you want to disable this relationship type?\n\nUsers will no longer be able to select this value when adding or editing relationships between contacts.", @selenium.get_confirmation()
  end
  
  # Enable Relationship Type
  def enable_relationship_type
    assert @selenium.is_text_present("brother of")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'brother of')]]/descendant::a[contains(.,'Enable')]"
  end
  
  # Delete Relatiuonship Type
  def delete_relationship_type
    assert @selenium.is_text_present("brother of")
    @page.click_and_wait "//div[@id='ltype']/descendant::tr[td[contains(.,'brother of')]]/descendant::a[contains(.,'Delete')]"
    assert @selenium.is_text_present("WARNING: Deleting this option will result in the loss of all Relationship type records which use the option. This may mean the loss of a substantial amount of data, and the action cannot be undone. Do you want to continue?")
    @page.click_and_wait "_qf_RelationshipType_next"
    assert @selenium.is_text_present("Selected Relationship type has been deleted.")
  end
end
