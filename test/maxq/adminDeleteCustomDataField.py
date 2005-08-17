from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import DBUtil
import commonAPI


# definition of test class
class adminDeleteCustomDataField(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
        
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        nameCDF       = 'Test Field'
        queryCDFID    = 'select id from civicrm_custom_field where label like \'%%%s%%\'' % nameCDF
        customDataFID = db.loadVal(queryCDFID)
        queryD        = 'delete from civicrm_custom_field where id=%s' % customDataFID

        if customDataFID :
            if db.execute(queryD) :
                print "***************************************************************"
                print "\'%s\' Custom Data Grop Deleted Successfully" % nameCDF
                print "***************************************************************"
            else :
                print "***************************************************************"
                print "Some Problem while Deleteing \'%s\' Custom Data Group" % nameCDF
                print "***************************************************************"
        else :
            print "***************************************************************"
            print "\'%s\' Custom Data Field Does Not Exists" % nameCDF
            print "***************************************************************"

# Code to load and run the test
if __name__ == 'main':
    test = adminDeleteCustomDataField("adminDeleteCustomDataField")
    test.Run()
