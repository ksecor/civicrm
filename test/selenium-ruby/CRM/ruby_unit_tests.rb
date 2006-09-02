require 'test/unit'
require 'test/unit/ui/console/testrunner'
require 'crm_tests'

class TS_CiviCRMTests
  def self.suite
    suite = Test::Unit::TestSuite.new
    suite << TC_TestAdminGender.suite
    suite << TC_TestAdminIMProvider.suite
    suite << TC_TestAdminMPProvider.suite
    suite << TC_TestAdminActivity.suite
    suite << TC_TestAdminPrefix.suite
    suite << TC_TestAdminSuffix.suite
    suite << TC_TestAdminTag.suite
    suite << TC_TestAdminLocation.suite
    suite << TC_TestAdminProfile.suite
    suite << TC_TestAdminMembershipType.suite
    suite << TC_TestAdminOptionGroup.suite
    return suite
  end
end

if __FILE__ == $0
  Test::Unit::UI::Console::TestRunner.run(TS_CiviCRMTests)
end
