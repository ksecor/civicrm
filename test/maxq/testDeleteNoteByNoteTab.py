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
class testDeleteNoteByNoteTab(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')
        
        drupal_path = commonConst.DRUPAL_PATH
        
        commonAPI.login(self)
        
        name    = 'Zope, Manish'
        queryID = 'select id from civicrm_contact where sort_name=\'%s\'' % name
        
        contactID = db.loadVal(queryID)
        if contactID :
            CID = '''%s''' % contactID
            
            params = [
                ('''_qf_default''', '''Search:refresh'''),
                ('''contact_type''', ''''''),
                ('''group''', ''''''),
                ('''tag''', ''''''),
                ('''sort_name''', '''manish'''),
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
                ('''reset''', '''1'''),
                ('''cid''', CID),]
            url = "%s/civicrm/contact/view" % drupal_path
            self.msg("Testing URL: %s" % url)
            Validator.validateRequest(self, self.getMethod(), "get", url, params)
            self.get(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            
            params = [
                ('''reset''', '''1'''),
                ('''cid''', CID),]
            url = "%s/civicrm/contact/view/note" % drupal_path
            self.msg("Testing URL: %s" % url)
            Validator.validateRequest(self, self.getMethod(), "get", url, params)
            self.get(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            
            note    = 'This is Test Note'
            queryID = 'select id from civicrm_note where note like \'%%%s%%\'' % note
            noteID  = db.loadVal(queryID)
            
            if noteID :
                NID = '''%s''' % noteID
                params = [
                    ('''action''', '''delete'''),
                    ('''reset''', '''1'''),
                    ('''cid''', CID),
                    ('''id''', NID),
                    ('''confirmed''', '''1'''),]
                url = "%s/civicrm/contact/view/note" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 11 failed", 302, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                if self.responseContains('%s' % note) :
                    print ("**************************************************************************************")
                    print "\'%s\' Note is not Deleted" % note
                    print ("**************************************************************************************")
                else :
                    print ("**************************************************************************************")
                    print "\'%s\' Note is Deleted Successfully" % note
                    print ("**************************************************************************************")
            else :
                print ("**************************************************************************************")
                print "\'%s\' Note does not exists" % note
                print ("**************************************************************************************")
        else :
            print "********************************************************************************"
            print "No such contact having Name \"%s\" currently Exists" % name
            print "********************************************************************************"
        
        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testDeleteNoteByNoteTab("testDeleteNoteByNoteTab")
    test.Run()
