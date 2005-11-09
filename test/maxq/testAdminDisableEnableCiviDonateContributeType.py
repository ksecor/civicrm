# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
from com.bitmechanic.maxq import DBUtil
import commonConst, commonAPI
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'

# definition of test class
class testAdminDisableEnableCiviDonateContributeType(PyHttpTestCase):

    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)

    def runTest(self):
        self.msg('Test started')
        
        drupal_path = commonConst.DRUPAL_PATH
        
        commonAPI.login(self)
        
        params = [
            ('''reset''', '''1'''),]
        url = "%s/civicrm/contribute/admin" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        url = "%s/civicrm/contribute/admin/contributionType" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        name    = 'New Contribution'
        tmpqueryID = 'select id from civicrm_contribution_type where name=\'%s\'' % name
        qid     = db.loadVal(tmpqueryID)
        
        if qid :
            QID = '''%s''' % qid
            params = [
                ('''action''', '''disable'''),
                ('''id''', QID),]
            url = "%s/civicrm/contribute/admin/contributionType" % drupal_path
            self.msg("Testing URL: %s" % url)
            Validator.validateRequest(self, self.getMethod(), "get", url, params)
            self.get(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            
            params = [
                ('''action''', '''enable'''),
                ('''id''', QID),]
            url = "%s/civicrm/contribute/admin/contributionType" % drupal_path
            self.msg("Testing URL: %s" % url)
            Validator.validateRequest(self, self.getMethod(), "get", url, params)
            self.get(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
        else :
            print "******************************************************************"
            print "Contribution Type \'%s\' does not exists" % name
            print "******************************************************************"
        
        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    
    # ^^^ Insert new recordings here.  (Do not remove this line.)

# Code to load and run the test
if __name__ == 'main':
    test = testAdminDisableEnableCiviDonateContributeType("testAdminDisableEnableCiviDonateContributeType")
    test.Run()
