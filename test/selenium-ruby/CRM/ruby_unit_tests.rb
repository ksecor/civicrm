require 'test/unit'
require 'test/unit/ui/console/testrunner'
require 'crm_tests'

class TS_CiviCRMTests
  def self.suite
    suite = Test::Unit::TestSuite.new
    
    #Dashboard section
    suite << TC_TestNewIndividual.suite
    suite << TC_TestNewGroup.suite
    suite << TC_TestManageGroup.suite
    suite << TC_TestNewHousehold.suite
    suite << TC_TestNewOrganization.suite
    #suite << TC_TestImportContacts.suite
    #suite << TC_TestCiviContribute.suite
    #suite << TC_TestCiviMember.suite
    
    #Individual Test Cases
    suite << TC_TestContactMembership.suite
    suite << TC_TestContactContribution.suite
    suite << TC_TestFindMembership.suite
    suite << TC_TestFindContribution.suite
      
    #Search section
    suite << TC_TestFindContacts.suite
    suite << TC_TestAdvancedSearch.suite
    suite << TC_TestSearchBuilder.suite

    ## Administer CiviCRM Section
    
    #Configure section
    suite << TC_TestAdminActivity.suite
    suite << TC_TestAdminProfile.suite
    suite << TC_TestAdminCustomData.suite
    suite << TC_TestAdminDuplicateMatching.suite
    suite << TC_TestAdminLocation.suite
    suite << TC_TestAdminRelationshipType.suite
    suite << TC_TestAdminTag.suite
    suite << TC_TestAdminDomainInformation.suite
    suite << TC_TestAdminOptionGroup.suite
    suite << TC_TestAdminImportExportMapping.suite
    
    #Setup section
    suite << TC_TestAdminGender.suite
    suite << TC_TestAdminIMProvider.suite
    suite << TC_TestAdminMobileProvider.suite
    suite << TC_TestAdminPrefix.suite
    suite << TC_TestAdminSuffix.suite
    suite << TC_TestPrefCommMethod.suite
    
    #CiviContribute section
    #suite << TC_TestAdminOnlineContribution.suite
    suite << TC_TestAdminManagePremium.suite
    suite << TC_TestAdminContributionTypes.suite
    suite << TC_TestAdminPaymentInstrument.suite
    suite << TC_TestAdminCreditCard.suite
    
    #CiviMember section
    suite << TC_TestAdminMembershipType.suite
    suite << TC_TestAdminMembershipStatus.suite

    return suite
  end
end

if __FILE__ == $0
  Test::Unit::UI::Console::TestRunner.run(TS_CiviCRMTests)
end
