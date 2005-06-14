from com.bitmechanic.maxq import DBUtil
import commonConst

def login(test) :
    url = "%s/" %  commonConst.DRUPAL_PATH
    print "Testing URL" + url
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
    print "Testing URL" + url
    test.post(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Assert number 4 failed", 302, test.getResponseCode())

    url = "%s/node" %  commonConst.DRUPAL_PATH
    print "Testing URL" + url
    params = None
    test.get(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Assert number 5 failed", 200, test.getResponseCode())

    url = "%s/civicrm/contact/search" %  commonConst.DRUPAL_PATH
    print "Testing URL" + url
    params = None
    test.get(url, params)
    test.msg("Response code: %s" % test.getResponseCode())
    test.assertEquals("Assert number 7 failed", 200, test.getResponseCode())

def dbStart() :
    return DBUtil("org.gjt.mm.mysql.Driver", "jdbc:mysql://localhost/civicrm", "civicrm", "Mt!Everest")

def dbStop(db) :
    db.close()
