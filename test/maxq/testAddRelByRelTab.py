# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
from com.bitmechanic.maxq import DBUtil
import Common
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testAddRelByRelTab(PyHttpTestCase):
    def runTest(self):
        self.msg('Test started')

        drupal_path = Common.DRUPAL_PATH
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/''') % drupal_path)
        url = "%s/" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 1 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        params = [
            ('''edit[destination]''', '''node'''),
            ('''edit[name]''', Common.USERNAME),
            ('''edit[pass]''', Common.PASSWORD),
            ('''op''', '''Log in'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/user/login?edit[destination]=node&edit[name]=manishzope&edit[pass]=manish&op=Log in''') % drupal_path)
        url = "%s/user/login" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 2 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/node''') % drupal_path)
        url = "%s/node" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 3 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/search''') % drupal_path)
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 4 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        params = [
            ('''_qf_default''', '''Search:refresh'''),
            ('''contact_type''', '''Individual'''),
            ('''group''', ''''''),
            ('''tag''', ''''''),
            ('''sort_name''', '''Zope'''),
            ('''_qf_Search_refresh''', '''Search'''),
            ('''task''', ''''''),
            ('''radio_ts''', '''ts_sel'''),]
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        db = DBUtil("%s" % Common.MSQLDRIVER, "jdbc:mysql://%s/%s" % (Common.DBHOST, Common.DBNAME), "%s" % Common.DBUSERNAME, "%s" % Common.DBPASSWORD)

        queryCA = 'select min(id) from crm_contact where sort_name like \'%%%s%%\' and contact_type=\'Individual\'' % params[4][1]
        queryCB = 'select id from crm_contact where contact_type=\'Household\' order by RAND() limit 1'
        contactA = db.loadVal(queryCA)
        contactB = db.loadVal(queryCB)
        
        db.close()
        
        params = [
            ('''_qf_Search_display''', '''true'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/drupal/civicrm/contact/search?_qf_Search_display=true'''))
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 6 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        contactID = '''%s''' % contactA
        params = [
            ('''reset''', '''1'''),
            ('''cid''', contactID),]
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/drupal/civicrm/contact/view?reset=1&cid=94'''))
        url = "%s/civicrm/contact/view" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/drupal/civicrm/contact/view/rel'''))
        url = "%s/civicrm/contact/view/rel" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''cid''', contactID),
            ('''action''', '''add'''),
            ('''reset''', '''1'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/rel?cid=90&action=add&reset=1''') % drupal_path)
        url = "%s/civicrm/contact/view/rel" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/favicon.ico''') % drupal_path)
        #url = "%s/favicon.ico" % drupal_path
        #self.msg("Testing URL: %s" % url)
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 10 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)
        
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
        self.assertEquals("Assert number 11 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_Relationship_display''', '''true'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/rel?_qf_Relationship_display=true''') % drupal_path)
        url = "%s/civicrm/contact/view/rel" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 12 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''/favicon.ico''') % drupal_path)
        #url = "http://localhost/favicon.ico" 
        #self.msg("Testing URL: %s" % url)
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 13 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)

        contactCheck = '''contact_check[%s]''' % int(contactB)
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
        self.assertEquals("Assert number 14 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''action''', '''browse'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/rel?action=browse''') % drupal_path)
        url = "%s/civicrm/contact/view/rel" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 15 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAddRelByRelTab("testAddRelByRelTab")
    test.Run()
