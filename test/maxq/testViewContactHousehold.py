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
class testViewContactHousehold(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')

        drupal_path = commonConst.DRUPAL_PATH

        commonAPI.login(self)

        name    = 'Zope House'
        queryID = 'select id from crm_contact where sort_name like \'%%%s%%\'' % name
        
        CID     = '''%s''' % db.loadVal(queryID)
        params = [
            ('''reset''', '''1'''),
            ('''cid''', CID),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view?reset=1&cid=101''') % drupal_path)
        url = "%s/civicrm/contact/view" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        print ("**************************************************************************************")
        print "Viewing Household \'%s\'" % name
        print ("**************************************************************************************")
        
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testViewContactHousehold("testViewContactHousehold")
    test.Run()
