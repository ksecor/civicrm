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
    
    if (option[0,3] == 'con') or (option[0,3] == 'CON')
      #create_contact()
    end
    
    if (option[0,3] == 'adm') or (option[0,3] == 'ADM')
      #admin()
    end
    
    if (option[0,3] == 'imp') or (option[0,3] == 'IMP')
      #print 'IMPORT'
    end
    
    if (option[0,3] == 'src') or (option[0,3] == 'SRC')
      #print 'Search'
    end
    
    if (option[0,3] == 'cct') or (option[0,3] == 'CCT')
      #print 'Contribute'
    end
    
    if (option[0,3] == 'cmb') or (option[0,3] == 'CMB')
      #print 'CiviMember'
    end
    
    if (option[0,3] == 'cml') or (option[0,3] == 'CML')
      #print 'CiviMail'
    end
    
    
    # Contact Related
    # if option  == 'contact'
#       create_contact()
#     end
    
#     # Groups Related
#     if option == 'group'
#       group()
#     end
    
#     # Import Contact, Activity History
#     if option == 'import'
#       #import()
#     end
    
#     ## Administer CiviCRM Section
    
#     #Configure section
#     if option == 'admin_configure'
#       admin_configure()
#     end
    
#     #Setup section
#     admin_setup()
    
#     #CiviContribute section
#     admin_civicontribute()
    
#     #CiviMember section
#     admin_civimember()
    
#     # CiviCRM Components
#     civicontribute()
#     civimember()
    
#     # All types of search
#     search()
        
    return suite
  end
  
  def mine
    print "==+"
  end
  
  def create_contact
    suite << TC_TestNewIndividual.suite
    suite << TC_TestNewHousehold.suite
    suite << TC_TestNewOrganization.suite
  end
  
  def group
    suite << TC_TestNewGroup.suite
    suite << TC_TestManageGroup.suite
  end
  
  def admin_configure
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
  end
  
  def admin_setup
    suite << TC_TestAdminGender.suite
    suite << TC_TestAdminIMProvider.suite
    suite << TC_TestAdminMobileProvider.suite
    suite << TC_TestAdminPrefix.suite
    suite << TC_TestAdminSuffix.suite
    suite << TC_TestPrefCommMethod.suite
  end
  
  def admin_civicontribute
    #suite << TC_TestAdminOnlineContribution.suite
    suite << TC_TestAdminManagePremium.suite
    suite << TC_TestAdminContributionTypes.suite
    suite << TC_TestAdminPaymentInstrument.suite
    suite << TC_TestAdminCreditCard.suite
  end
  
  def admin_civimember
    suite << TC_TestAdminMembershipType.suite
    suite << TC_TestAdminMembershipStatus.suite
  end
  
  def import
    suite << TC_TestImportContacts.suite
  end
  
  def search
    suite << TC_TestFindContacts.suite
    suite << TC_TestAdvancedSearch.suite
    suite << TC_TestSearchBuilder.suite
  end

  def civicontribute
    suite << TC_TestContactContribution.suite
    #suite << TC_TestCiviContribute.suite
    suite << TC_TestFindContribution.suite
  end
  
  def civimember
    suite << TC_TestContactMembership.suite
    #suite << TC_TestCiviMember.suite
    suite << TC_TestFindMembership.suite
  end
  
end

if __FILE__ == $0
  Test::Unit::UI::Console::TestRunner.run(TS_CiviCRMTests)
end
