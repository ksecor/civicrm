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
      # Add New Individual
      suite << TC_TestNewIndividual.suite

    when '2' then
      # Add New HouseHold
      suite << TC_TestNewHousehold.suite

    when '3' then
      # Add New Organization
      suite << TC_TestNewOrganization.suite

    when '4' then
      # Add New Group
      suite << TC_TestNewGroup.suite

    when '5' then
      # Manage Group
      suite << TC_TestManageGroup.suite

    when '6' then
      # Activity Type
      suite << TC_TestAdminActivity.suite            
      # Duplicate Matching
      suite << TC_TestAdminDuplicateMatching.suite  
      # Location Types 
      suite << TC_TestAdminLocation.suite           
      # Relationship Types
      suite << TC_TestAdminRelationshipType.suite    
      # Tags
      suite << TC_TestAdminTag.suite                 
      # Edit Domain Information
      suite << TC_TestAdminDomainInformation.suite  
      # Import Export Mapping 
      suite << TC_TestAdminImportExportMapping.suite
      # Message Template
      suite << TC_TestAdminMessageTemplates.suite

    when '7' then
      # Custom Data 
      suite << TC_TestAdminCustomData.suite          

    when '8' then
      # Profile
      suite << TC_TestAdminProfile.suite             

    when '9' then
      # Gender Option
      suite << TC_TestAdminGender.suite
      # Instant messanger service
      suite << TC_TestAdminIMProvider.suite
      # Mobile Phone Provider
      suite << TC_TestAdminMobileProvider.suite
      # Individual Prefix
      suite << TC_TestAdminPrefix.suite
      # Individual Suffix
      suite << TC_TestAdminSuffix.suite
      # Preferred Communication Method
      suite << TC_TestPrefCommMethod.suite    

    when '10' then
      # Configure Online Contribution Pages
      suite << TC_TestAdminOnlineContribution.suite
      # Manage Premiums
      suite << TC_TestAdminManagePremium.suite
      # Contribution Types
      suite << TC_TestAdminContributionTypes.suite
      # Payment Instruments
      suite << TC_TestAdminPaymentInstrument.suite
      # Accept Credit Card
      suite << TC_TestAdminCreditCard.suite

    when '11' then
      # Membership Type
      suite << TC_TestAdminMembershipType.suite
      # Membership Status Rule
      suite << TC_TestAdminMembershipStatus.suite

    when '12' then
      # Manage Events
      suite << TC_TestAdminManageEvents.suite
      # Event Types
      suite << TC_TestAdminEventType.suite
      #participant status
      suite << TC_TestAdminParticipantStatus.suite
      #participant role
      suite << TC_TestAdminParticipantRole.suite

    when '13' then  
      #find contacts
      suite << TC_TestFindContacts.suite

    when '14' then
      # Find Contacts using Advanced search
      suite << TC_TestAdvancedSearch.suite

    when '15' then
      #Find Contacts using search builder
      suite << TC_TestSearchBuilder.suite

    when '16' then
      print "Sory, no test case present for this operation"
      #suite << TC_TestImportContacts.suite

    when '17' then
      print "Sory, no test case present for this operation"
      #suite << TC_TestImportActivityHistory.suite

    when '18' then
      #find Contribution 
      suite << TC_TestFindContribution.suite

    when '19' then
      print "Sory, no test case present for this operation"
      #suite << TC_TestImportContribution.suite

    when '20' then
      # find membership
      suite << TC_TestFindMembership.suite

    when '21' then
      suite << TC_TestContactMembership.suite

    when '22' then
      print "Sory, no test case present for this operation"

    when '23' then
      #CiviEvent Section
      suite << TC_TestContactEvents.suite
      suite << TC_TestEventFindParticipants.suite
      # suite << TC_TestEventImportParticipant.suite
    else
      print "Sorry, you have entered wrong choice. Please try again"
    end        
    return suite
  end 
end

if __FILE__ == $0
  Test::Unit::UI::Console::TestRunner.run(TS_CiviCRMTests)
end
    
