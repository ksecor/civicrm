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
class testSearchByONameOraganization(PyHttpTestCase):
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
            ('''reset''', '''1'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/search?reset=1''') % drupal_path)
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        # self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        # url = "http://localhost/favicon.ico"
        # params = None
        # Validator.validateRequest(self, self.getMethod(), "get", url, params)
        # self.get(url, params)
        # self.msg("Response code: %s" % self.getResponseCode())
        # self.assertEquals("Assert number 6 failed", 404, self.getResponseCode())
        # Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_default''', '''Search:refresh'''),
            ('''contact_type''', '''Organization'''),
            ('''group''', ''''''),
            ('''tag''', ''''''),
            ('''sort_name''', '''intel'''),
            ('''_qf_Search_refresh''', '''Search'''),
            ('''task''', ''''''),
            ('''radio_ts''', '''ts_sel'''),]
        
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        db = DBUtil("%s" % Common.MSQLDRIVER, "jdbc:mysql://%s/%s" % (Common.HOST, Common.DBNAME), "%s" % Common.DBUSERNAME, "%s" % Common.DBPASSWORD)
        
        name    = '%s' % params[4][1]
        contact = '%s' % params[1][1]
        group   = '%s' % params[2][1]
        tag     = '%s' % params[3][1]

        query = 'SELECT count(DISTINCT crm_contact.id)  FROM crm_contact \
        LEFT JOIN crm_location ON (crm_contact.id = crm_location.contact_id AND crm_location.is_primary = 1) \
        LEFT JOIN crm_address ON crm_location.id = crm_address.location_id \
        LEFT JOIN crm_phone ON (crm_location.id = crm_phone.location_id AND crm_phone.is_primary = 1) \
        LEFT JOIN crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1) \
        LEFT JOIN crm_state_province ON crm_address.state_province_id = crm_state_province.id \
        LEFT JOIN crm_country ON crm_address.country_id = crm_country.id \
        LEFT JOIN crm_group_contact ON crm_contact.id = crm_group_contact.contact_id \
        LEFT JOIN crm_entity_tag ON crm_contact.id = crm_entity_tag.entity_id \
        WHERE crm_contact.contact_type=\'%s\' AND LOWER(crm_contact.sort_name) LIKE \'%%%s%%\' AND 1' % (contact, name)

        noOfContact = db.loadVal(query)
        if noOfContact == '1' :
            string = "Found %s contact" % noOfContact
        else :
            string = "Found %s contacts" % noOfContact
        
        db.close()

        params = [
            ('''_qf_Search_display''', '''true'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/search?_qf_Search_display=true''') % drupal_path)
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        print ("*********************************************************************************")
        print ("The Citeria for search is ")
        self.msg ("%s : %s" % ("Contact Type", contact))
        self.msg ("%s : %s" % ("Group       ", group))
        self.msg ("%s : %s" % ("Tag         ", tag))
        self.msg ("%s : %s" % ("Sort Name   ", name))
        print ("*********************************************************************************")

        if self.responseContains(string) :
            print ("*********************************************************************************")
            self.msg ("Search \"%s\"" % string)
            print ("*********************************************************************************")
        
        elif noOfContact == '0' :
            print ("*********************************************************************************")
            self.msg("The Response is \"%s\"" % string )
            print ("*********************************************************************************")            
        
        else :
            print ("*********************************************************************************")
            self.msg("The Response does not match with the result from the database ")
            print ("*********************************************************************************")            
        
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testSearchByONameOraganization("testSearchByONameOraganization")
    test.Run()
