# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
import commonConst, commonAPI
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testEditNoteByNoteTab(PyHttpTestCase):
    #def setUp(self):
    #    global db
    #    db = commonAPI.dbStart()
    
    #def tearDown(self):
    #    commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')

        drupal_path = commonConst.DRUPAL_PATH

        commonAPI.login(self)
        
        db = DBUtil("%s" % commonConst.MSQLDRIVER, "jdbc:mysql://%s/%s" % (commonConst.DBHOST, commonConst.DBNAME), "%s" % commonConst.DBUSERNAME, "%s" % commonConst.DBPASSWORD)

        note   = '\'This is Test Note\''
        query  = 'select id from crm_note where note like \'%%%s%%\'' % note
        noteID = db.loadVal(query)
        
        db.close()

        NID = '''%s''' % noteID
        params = [
            ('''nid''', NID),
            ('''action''', '''update'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/note?nid=201&action=update''') % drupal_path)
        url = "%s/civicrm/contact/view/note" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        #url = "http://localhost/favicon.ico"
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 6 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_default''', '''Note:next'''),
            ('''note''', '''Adding test Note....Added note is being tested for editing..'''),
            ('''_qf_Note_next''', '''Save'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/note?_qf_default=Note:next&note=Adding test Note....Added note is being tested for editing..&_qf_Note_next=Save''') % drupal_path)
        url = "%s/civicrm/contact/view/note" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''action''', '''browse'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/note?action=browse''') % drupal_path)
        url = "%s/civicrm/contact/view/note" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        if NID :
            print ("**************************************************************************************")
            print "The Note %s is Edited Successfully" % note
            print ("**************************************************************************************")
        else :
            print ("**************************************************************************************")
            print ("There is no Note like %s") % note
            print ("**************************************************************************************")
        
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        #url = "http://localhost/favicon.ico"
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 9 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)
        
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testEditNoteByNoteTab("testEditNoteByNoteTab")
    test.Run()
