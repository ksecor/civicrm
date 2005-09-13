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
class testAddRelByContactTab(PyHttpTestCase):
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
            ('''_qf_default''', '''Search:refresh'''),
            ('''contact_type''', ''''''),
            ('''group''', ''''''),
            ('''tag''', ''''''),
            ('''sort_name''', ''''''),
            ('''_qf_Search_refresh''', '''Search'''),]
        url = "%s/civicrm/contact/search/basic" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_Search_display''', '''true'''),]
        url = "%s/civicrm/contact/search/basic" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''set''', '''1'''),
            ('''path''', '''civicrm/server/search'''),]
        url = "%s/civicrm/server/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''q''', '''civicrm/contact/search/basic'''),
            ('''force''', '''1'''),
            ('''sortByCharacter''', '''Z'''),]
        url = "%s/civicrm/contact/search/basic" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''set''', '''1'''),
            ('''path''', '''civicrm/server/search'''),]
        url = "%s/civicrm/server/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 11 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        nameI      = 'Zope, Manish'
        nameH      = 'Zope House'
        
        queryCA    = 'select id from civicrm_contact where sort_name=\'%s\' and contact_type=\'Individual\'' % nameI
        contactIID = db.loadVal(queryCA)
        
        if contactIID :
            CID = '''%s''' % contactIID
            params = [
                ('''reset''', '''1'''),
                ('''cid''', CID),]
            url = "%s/civicrm/contact/view" % drupal_path
            self.msg("Testign URL: %s" % url)
            Validator.validateRequest(self, self.getMethod(), "get", url, params)
            self.get(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 12 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            
            params = [
                ('''action''', '''add'''),
                ('''cid''', CID),]
            url = "%s/civicrm/contact/view/rel" % drupal_path
            self.msg("Testing URL: %s" % url)
            Validator.validateRequest(self, self.getMethod(), "get", url, params)
            self.get(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 13 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            
            params = [
                ('''_qf_default''', '''Relationship:next'''),
                ('''relationship_type_id''', '''7_a_b'''),
                ('''name''', ''''''),
                ('''_qf_Relationship_refresh''', '''Search'''),]
            url = "%s/civicrm/contact/view/rel" % drupal_path
            self.msg("Testing URL: %s" % url)
            Validator.validateRequest(self, self.getMethod(), "post", url, params)
            self.post(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 14 failed", 302, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            
            params = [
                ('''_qf_Relationship_display''', '''true'''),]
            url = "%s/civicrm/contact/view/rel" % drupal_path
            self.msg("Testing URL: %s" % url)
            Validator.validateRequest(self, self.getMethod(), "get", url, params)
            self.get(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 15 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            
            queryCB    = 'select id from civicrm_contact where sort_name=\'Zope House\' and contact_type=\'Household\''
            contactHID = db.loadVal(queryCB)

            if contactHID :
                contactCheck = '''contact_check[%s]''' % int(contactHID)
                
                params = [
                    ('''_qf_default''', '''Relationship:next'''),
                    ('''relationship_type_id''', '''7_a_b'''),
                    ('''name''', ''''''),
                    (contactCheck, '''1'''),
                    ('''start_date[d]''', ''''''),
                    ('''start_date[M]''', ''''''),
                    ('''start_date[Y]''', ''''''),
                    ('''end_date[d]''', ''''''),
                    ('''end_date[M]''', ''''''),
                    ('''end_date[Y]''', ''''''),
                    ('''_qf_Relationship_next''', '''Save Relationship'''),]
                url = "%s/civicrm/contact/view/rel" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "post", url, params)
                self.post(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 16 failed", 302, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                params = [
                    ('''action''', '''browse'''),]
                url = "%s/civicrm/contact/view/rel" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 17 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)

                print ("**************************************************************************************")
                print "Relationship \" \'%s\' Household Member of \'%s\' \" is Added Successfully" % (nameI, nameH)
                print ("**************************************************************************************")
            else :
                print ("**************************************************************************************")
                print " Household \'%s\' do not Exists" % nameH
                print ("**************************************************************************************")
        else:
            print ("**************************************************************************************")
            print " Individual \'%s\' do not Exists" % nameI
            print ("**************************************************************************************")
        
        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAddRelByContactTab("testAddRelByContactTab")
    test.Run()
