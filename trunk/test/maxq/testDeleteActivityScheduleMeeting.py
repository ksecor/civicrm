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
class testDeleteActivityScheduleMeeting(PyHttpTestCase):
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
        queryCA = 'select id from civicrm_contact where sort_name=\'%s\'' % name
        cid     = db.loadVal(queryCA)
        
        if cid :
            CID = '''%s''' % cid
            
            subject  = 'Scheduling Test Meeting'
            queryAID = 'select max(id) from civicrm_meeting where target_entity_id=\'%s\' and subject=\'%s\'' % (cid, subject)
            
            qid      = db.loadVal(queryAID)
            
            if qid :
                QID = '''%s''' % qid
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
                self.assertEquals("Assert number 6 failed", 302, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                params = [
                    ('''_qf_Search_display''', '''true'''),]
                url = "%s/civicrm/contact/search/basic" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
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
                    ('''show''', '''1'''),
                    ('''reset''', '''1'''),
                    ('''cid''', CID),]
                url = "%s/civicrm/contact/view/activity" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                params = [
                    ('''activity_id''', '''1'''),
                    ('''action''', '''delete'''),
                    ('''reset''', '''1'''),
                    ('''id''', QID),
                    ('''cid''', CID),
                    ('''confirmed''', '''1'''),]
                url = "%s/civicrm/contact/view/activity" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 11 failed", 302, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                params = [
                    ('''show''', '''1'''),
                    ('''action''', '''browse'''),
                    ('''reset''', '''1'''),
                    ('''history''', ''''''),
                    ('''cid''', CID),]
                url = "%s/civicrm/contact/view/activity" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 12 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                print "****************************************************************"
                print "Activity \'%s\' Deleted Successfully." % subject
                print "****************************************************************"
            else :
                print "****************************************************************"
                print "Activity \'%s\' not found." % subject
                print "****************************************************************"
        else :
            print ("**************************************************************************************")
            print " Individual \'Zope, Manish\' do not Exists"
            print ("**************************************************************************************")
        
        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testDeleteActivityScheduleMeeting("testDeleteActivityScheduleMeeting")
    test.Run()
