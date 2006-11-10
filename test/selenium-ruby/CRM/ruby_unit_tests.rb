require 'test/unit'
require 'test/unit/ui/console/testrunner'
require 'crm_tests'

class TS_CiviCRMTests
  
  def self.suite
    suite = Test::Unit::TestSuite.new
    
    if ARGV.length != 1
      exit
    end
    
    option = ARGV[0]
    
    # Following Case Structure is used for Executing Menuing System.
    case option
    when '1' then
      suite << TC_TestNewIndividual.suite
      # contact_individual()
    when '2' then
      suite << TC_TestNewHousehold.suite
      # contact_household()
    when '3' then
      suite << TC_TestNewOrganization.suite
      # contact_organization()
    when '4' then
      suite << TC_TestNewGroup.suite
      # new_group()
    when '5' then
      suite << TC_TestManageGroup.suite
      # manage_group()
    when '6' then
      suite << TC_TestAdminActivity.suite
      suite << TC_TestAdminDuplicateMatching.suite
      suite << TC_TestAdminLocation.suite
      suite << TC_TestAdminRelationshipType.suite
      suite << TC_TestAdminTag.suite
      suite << TC_TestAdminDomainInformation.suite
      suite << TC_TestAdminOptionGroup.suite
      suite << TC_TestAdminImportExportMapping.suite
      # admin_configure()
    when '7' then
      suite << TC_TestAdminCustomData.suite
      # admin_customData()
    when '8' then
      suite << TC_TestAdminProfile.suite
     # admin_customProfile()
    when '9' then
      suite << TC_TestAdminGender.suite
      suite << TC_TestAdminIMProvider.suite
      suite << TC_TestAdminMobileProvider.suite
      suite << TC_TestAdminPrefix.suite
      suite << TC_TestAdminSuffix.suite
      suite << TC_TestPrefCommMethod.suite
      # admin_setup()      
    when '10' then
      suite << TC_TestAdminOnlineContribution.suite
      suite << TC_TestAdminManagePremium.suite
      suite << TC_TestAdminContributionTypes.suite
      suite << TC_TestAdminPaymentInstrument.suite
      suite << TC_TestAdminCreditCard.suite
      # admin_civiContribute()  
    when '11' then
      suite << TC_TestAdminMembershipType.suite
      suite << TC_TestAdminMembershipStatus.suite
     # admin_civiMember()
    when '12' then
      suite << TC_TestFindContacts.suite
     # basic_search()
    when '13' then
      suite << TC_TestAdvancedSearch.suite
     # advanced_search()
    when '14' then
      suite << TC_TestSearchBuilder.suite
     # search_builder()
    when '15' then
      #suite << TC_TestImportContacts.suite
    when '16' then
      suite << TC_TestImportActivityHistory.suite
    when '17' then
      suite << TC_TestFindContribution.suite
    when '18' then
      suite << TC_TestContactMembership.suite
    when '19' then
      suite << TC_TestFindMembership.suite
    when '20' then
      suite << TC_TestContactContribution.suite
    when '21' then
      print "Sory no operation present"
    else
      print "Sorry, you have entered wrong choice. Please try again"
    end
        
    return suite
  end 
end

if __FILE__ == $0
  Test::Unit::UI::Console::TestRunner.run(TS_CiviCRMTests)
end
    
