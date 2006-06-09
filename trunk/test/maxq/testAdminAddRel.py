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
class testAdminAddRel(PyHttpTestCase):
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
            ('''action''', '''add'''),
            ('''reset''', '''1'''),]
        url = "%s/civicrm/admin/reltype" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        queryName      = 'select name_a_b from civicrm_relationship_type'       
        queryID        = 'select max(id) from civicrm_relationship_type'
        relName = db.loadRows(queryName)
        relNum  = db.loadVal(queryID)
        
        params = [
            ('''_qf_default''', '''RelationshipType:next'''),
            ('''name_a_b''', '''Owner Of'''),
            ('''name_b_a''', '''Owner For'''),
            ('''contact_type_a''', '''Organization'''),
            ('''contact_type_b''', '''Individual'''),
            ('''description''', '''This is test Relationship '''),
            ('''_qf_RelationshipType_next''', '''Save'''),]
        url = "%s/civicrm/admin/reltype" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        for i in range(int(relNum)) :
            if relName[i].values()[0] == params[1][1] :
                print ("**************************************************************************************")
                print ("Relationship \'" + relName[i].values()[0] + "\' already exists")
                print ("**************************************************************************************")
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 6 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                break
            else :
                continue
        else :
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 7 failed", 302, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''reset''', '''1'''),
            ('''action''', '''browse'''),]
        url = "%s/civicrm/admin/reltype" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAdminAddRel("testAdminAddRel")
    test.Run()
