from com.bitmechanic.maxq import DBUtil
import commonConst

def login(test) :
    url = "%s/" %  commonConst.DRUPAL_PATH
    print "Testing URL: " + url
    params = None
    test.get(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Assert number 1 failed", 200, test.getResponseCode())
    
    params = [
        ('''destination''', '''node'''),
        ('''edit[name]''',  commonConst.USERNAME),
        ('''edit[pass]''',  commonConst.PASSWORD),
        ('''op''', '''Log in'''),]
    url = "%s/user/login" %  commonConst.DRUPAL_PATH
    print "Testing URL: " + url
    test.post(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Assert number 2 failed", 302, test.getResponseCode())
    
    url = "%s/node" %  commonConst.DRUPAL_PATH
    print "Testing URL: " + url
    params = None
    test.get(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Assert number 3 failed", 200, test.getResponseCode())
    
    url = "%s/civicrm/" %  commonConst.DRUPAL_PATH
    print "Testing URL: " + url
    params = None
    test.get(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Assert number 4 failed", 302, test.getResponseCode())
    
    params = [
        ('''reset''', '''1'''),]
    url = "%s/civicrm/contact/search/basic" % commonConst.DRUPAL_PATH
    print "Testing URL: %s" % url
    test.get(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Assert number 5 failed", 200, test.getResponseCode())
    
    params = [
        ('''set''', '''1'''),
        ('''path''', '''civicrm/server/search'''),]
    url = "%s/civicrm/server/search" % commonConst.DRUPAL_PATH
    print "Testing URL: %s" % url
    test.get(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Assert number 6 failed", 200, test.getResponseCode())

def logout(test):
    url = "%s/logout" % commonConst.DRUPAL_PATH
    print "Testing URL: %s" % url
    params = None
    test.get(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Logout Assertion failed", 302, test.getResponseCode())

def dbStart() :
    return DBUtil("%s" % commonConst.MSQLDRIVER, "jdbc:mysql://%s/%s" % (commonConst.DBHOST, commonConst.DBNAME), "%s" % commonConst.DBUSERNAME, "%s" % commonConst.DBPASSWORD)

def dbStop(db) :
    db.close()
