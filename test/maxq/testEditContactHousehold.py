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
class testEditContactHousehold(PyHttpTestCase):
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
        
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/search''') % drupal_path)
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 3 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        #self.msg("Testing URL: %s" % self.replaceURL('''%s/node''') % drupal_path)
        url = "%s/node" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 4 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        db = DBUtil("%s" % Common.MSQLDRIVER, "jdbc:mysql://%s/%s" % (Common.DBHOST, Common.DBNAME), "%s" % Common.DBUSERNAME, "%s" % Common.DBPASSWORD)

        queryHID       = 'select id from crm_contact where contact_type=\'Household\' order by RAND() limit 1'
        householdID    = db.loadVal(queryHID) 
        queryHName1    = 'select sort_name from crm_contact where id=%s' % householdID
        householdName1 = db.loadVal(queryHName1)
        db.close()

        HID = '''%s''' % householdID
        params = [
            ('''reset''', '''1'''),
            ('''cid''', HID),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/edit?reset=1&cid=103''') % drupal_path)
        url = "%s/civicrm/contact/edit" % drupal_path
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
            ('''_qf_default''', '''Edit:next'''),
            ('''household_name''', '''Andrew Zope\\'s home'''),
            ('''nick_name''', '''Zope\\'s home'''),
            ('''__privacy[do_not_phone]''', '''1'''),
            ('''privacy[do_not_phone]''', '''1'''),
            ('''privacy[do_not_email]''', ''''''),
            ('''privacy[do_not_mail]''', ''''''),
            ('''preferred_communication_method''', '''Phone'''),
            ('''location[1][location_type_id]''', '''1'''),
            ('''location[1][is_primary]''', '''1'''),
            ('''location[1][phone][1][phone_type]''', '''Phone'''),
            ('''location[1][phone][1][phone]''', '''93590827'''),
            ('''location[1][phone][2][phone_type]''', '''Mobile'''),
            ('''location[1][phone][2][phone]''', '''69066533'''),
            ('''location[1][phone][3][phone_type]''', ''''''),
            ('''location[1][phone][3][phone]''', ''''''),
            ('''location[1][email][1][email]''', ''''''),
            ('''location[1][email][2][email]''', ''''''),
            ('''location[1][email][3][email]''', ''''''),
            ('''location[1][im][1][provider_id]''', '''1'''),
            ('''location[1][im][1][name]''', '''Welcome to Zope House'''),
            ('''location[1][im][2][provider_id]''', ''''''),
            ('''location[1][im][2][name]''', ''''''),
            ('''location[1][im][3][provider_id]''', ''''''),
            ('''location[1][im][3][name]''', ''''''),
            ('''location[1][address][street_address]''', '''W 722K Niepodległości Rd SE'''),
            ('''location[1][address][supplemental_address_1]''', '''Attn: Accounting'''),
            ('''location[1][address][supplemental_address_2]''', ''''''),
            ('''location[1][address][city]''', ''''''),
            ('''location[1][address][state_province_id]''', ''''''),
            ('''location[1][address][postal_code]''', '''449394'''),
            ('''location[1][address][country_id]''', ''''''),
            ('''location[2][location_type_id]''', '''1'''),
            ('''location[2][phone][1][phone_type]''', ''''''),
            ('''location[2][phone][1][phone]''', ''''''),
            ('''location[2][phone][2][phone_type]''', ''''''),
            ('''location[2][phone][2][phone]''', ''''''),
            ('''location[2][phone][3][phone_type]''', ''''''),
            ('''location[2][phone][3][phone]''', ''''''),
            ('''location[2][email][1][email]''', ''''''),
            ('''location[2][email][2][email]''', ''''''),
            ('''location[2][email][3][email]''', ''''''),
            ('''location[2][im][1][provider_id]''', ''''''),
            ('''location[2][im][1][name]''', ''''''),
            ('''location[2][im][2][provider_id]''', ''''''),
            ('''location[2][im][2][name]''', ''''''),
            ('''location[2][im][3][provider_id]''', ''''''),
            ('''location[2][im][3][name]''', ''''''),
            ('''location[2][address][street_address]''', ''''''),
            ('''location[2][address][supplemental_address_1]''', ''''''),
            ('''location[2][address][supplemental_address_2]''', ''''''),
            ('''location[2][address][city]''', ''''''),
            ('''location[2][address][state_province_id]''', ''''''),
            ('''location[2][address][postal_code]''', ''''''),
            ('''location[2][address][country_id]''', ''''''),
            ('''_qf_Edit_next''', '''Save'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/drupal/civicrm/contact/edit?_qf_default=Edit:next&household_name=Andrew Zope\'s home&nick_name=Zope\'s home&__privacy[do_not_phone]=1&privacy[do_not_phone]=1&privacy[do_not_email]=&privacy[do_not_mail]=&preferred_communication_method=Phone&location[1][location_type_id]=1&location[1][is_primary]=1&location[1][phone][1][phone_type]=Phone&location[1][phone][1][phone]=93590827&location[1][phone][2][phone_type]=Mobile&location[1][phone][2][phone]=69066533&location[1][phone][3][phone_type]=&location[1][phone][3][phone]=&location[1][email][1][email]=&location[1][email][2][email]=&location[1][email][3][email]=&location[1][im][1][provider_id]=1&location[1][im][1][name]=Welcome to Zope House&location[1][im][2][provider_id]=&location[1][im][2][name]=&location[1][im][3][provider_id]=&location[1][im][3][name]=&location[1][address][street_address]=W 722K Niepodległości Rd SE&location[1][address][supplemental_address_1]=Attn: Accounting&location[1][address][supplemental_address_2]=&location[1][address][city]=&location[1][address][state_province_id]=&location[1][address][postal_code]=449394&location[1][address][country_id]=&location[2][location_type_id]=1&location[2][phone][1][phone_type]=&location[2][phone][1][phone]=&location[2][phone][2][phone_type]=&location[2][phone][2][phone]=&location[2][phone][3][phone_type]=&location[2][phone][3][phone]=&location[2][email][1][email]=&location[2][email][2][email]=&location[2][email][3][email]=&location[2][im][1][provider_id]=&location[2][im][1][name]=&location[2][im][2][provider_id]=&location[2][im][2][name]=&location[2][im][3][provider_id]=&location[2][im][3][name]=&location[2][address][street_address]=&location[2][address][supplemental_address_1]=&location[2][address][supplemental_address_2]=&location[2][address][city]=&location[2][address][state_province_id]=&location[2][address][postal_code]=&location[2][address][country_id]=&_qf_Edit_next=Save'''))
        url = "%s/civicrm/contact/edit" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        householdName2 = params[1][1]

        print ("**************************************************************************************")
        print "\'%s\' is Edited with \'%s\'" % (householdName1, householdName2)
        print ("**************************************************************************************")
        
        # self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        # url = "http://localhost/favicon.ico"
        # params = None
        # Validator.validateRequest(self, self.getMethod(), "get", url, params)
        # self.get(url, params)
        # self.msg("Response code: %s" % self.getResponseCode())
        # self.assertEquals("Assert number 8 failed", 404, self.getResponseCode())
        # Validator.validateResponse(self, self.getMethod(), url, params)
        
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testEditContactHousehold("testEditContactHousehold")
    test.Run()
