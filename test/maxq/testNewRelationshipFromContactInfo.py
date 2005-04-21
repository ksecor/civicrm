# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testNewRelationship(PyHttpTestCase):
    def runTest(self):
        self.msg('Test started')

        drupal_path = "http://localhost/drupal"
        self.msg("Testing URL: %s" % self.replaceURL('''%s/''') % drupal_path)
        url = "%s/" % drupal_path
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 1 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        params = [
            ('''edit[destination]''', '''node'''),
            ('''edit[name]''', self.userInput('Enter Drupal UserName')),
            ('''edit[pass]''', self.userInput('Enter Drupal Password')),
            ('''op''', '''Log in'''),]
        self.msg("Testing URL: %s" % self.replaceURL('''%s/user/login?edit[destination]=node&edit[name]=manishzope&edit[pass]=manish&op=Log in''') % drupal_path)
        url = "%s/user/login" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 2 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        self.msg("Testing URL: %s" % self.replaceURL('''%s/node''') % drupal_path)
        url = "%s/node" % drupal_path
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 3 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        params = [
            ('''action''', '''add'''),]
        self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/rel?action=add''') % drupal_path)
        url = "%s/civicrm/contact/view/rel" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 4 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        # self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        # url = "http://localhost/favicon.ico"
        # params = None
        # Validator.validateRequest(self, self.getMethod(), "get", url, params)
        # self.get(url, params)
        # self.msg("Response code: %s" % self.getResponseCode())
        # self.assertEquals("Assert number 5 failed", 404, self.getResponseCode())
        # Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''relationship_type_id''', '''1_a_b'''),
            ('''contact_type''', '''Individual'''),
            ('''name''', '''Zope'''),]
        self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/rel?relationship_type_id=1_a_b&contact_type=Individual&name=Zope''') % drupal_path)
        url = "%s/civicrm/contact/view/rel" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 6 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        # self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        # url = "http://localhost/favicon.ico"
        # params = None
        # Validator.validateRequest(self, self.getMethod(), "get", url, params)
        # self.get(url, params)
        # self.msg("Response code: %s" % self.getResponseCode())
        # self.assertEquals("Assert number 7 failed", 404, self.getResponseCode())
        # Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''relationship_type_id''', '''1_a_b'''),
            ('''contact_type''', '''Individual'''),
            ('''name''', '''Zope'''),
            ('''contact_check[66]''', '''1'''),
            ('''contact_check[93]''', '''1'''),
            ('''start_date[d]''', ''''''),
            ('''start_date[M]''', ''''''),
            ('''start_date[Y]''', ''''''),
            ('''end_date[d]''', ''''''),
            ('''end_date[M]''', ''''''),
            ('''end_date[Y]''', ''''''),
            ('''_qf_Relationship_next''', '''Save Relationship'''),]
        self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/rel?relationship_type_id=1_a_b&contact_type=Individual&name=Zope&contact_check[66]=1&contact_check[93]=1&start_date[d]=&start_date[M]=&start_date[Y]=&end_date[d]=&end_date[M]=&end_date[Y]=&_qf_Relationship_next=Save Relationship''') % drupal_path)
        url = "%s/civicrm/contact/view/rel" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''action''', '''browse'''),]
        self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view/rel?action=browse''') % drupal_path)
        url = "%s/civicrm/contact/view/rel" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        # self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        # url = "http://localhost/favicon.ico"
        # params = None
        # Validator.validateRequest(self, self.getMethod(), "get", url, params)
        # self.get(url, params)
        # self.msg("Response code: %s" % self.getResponseCode())
        # self.assertEquals("Assert number 10 failed", 404, self.getResponseCode())
        # Validator.validateResponse(self, self.getMethod(), url, params)
        

    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testNewRelationship("testNewRelationship")
    test.Run()
