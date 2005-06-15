from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import DBUtil
import commonAPI


# definition of test class
class adminDeleteMobileProvider(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
        
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        nameMP      = 'Test Mobile Provider'
        
        queryID     = 'select id from crm_mobile_provider where name like \'%%%s%%\'' % nameMP
        mobilePID   = db.loadVal(queryID)
        queryD      = 'delete from crm_mobile_provider where id=%s' % mobilePID

        if mobilePID :
            if db.execute(queryD) :
                print "***************************************************************"
                print "\'%s\' Mobile Provider Deleted Successfully" % nameMP
                print "***************************************************************"
            else :
                print "***************************************************************"
                print "Some Problem while Deleting \'%s\' Mobile Provider" % nameMP
                print "***************************************************************"
        else :
            print "***************************************************************"
            print "\'%s\' Mobile Provider Does Not Exists" % nameMP
            print "***************************************************************"

# Code to load and run the test
if __name__ == 'main':
    test = adminDeleteMobileProvider("adminDeleteMobileProvider")
    test.Run()
