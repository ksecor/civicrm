from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import DBUtil
import commonAPI


# definition of test class
class adminDeleteIMProvider(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
        
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        nameIM  = 'Test IM'

        queryID = 'select id from crm_im_provider where name like \'%%%s%%\'' % nameIM
        IMPID   = db.loadVal(queryID)
        queryD  = 'delete from crm_location_type where id=%s' % IMPID

        if IMPID :
            if db.execute(queryD) :
                print "***************************************************************"
                print "\'%s\' IMProvider Deleted Successfully" % nameIM
                print "***************************************************************"
            else :
                print "***************************************************************"
                print "Some Problem while Deleteing \'%s\' IMProvider" % nameIM
                print "***************************************************************"
        else :
            print "***************************************************************"
            print "\'%s\' IMProvider Does Not Exists" % nameIM
            print "***************************************************************"

# Code to load and run the test
if __name__ == 'main':
    test = adminDeleteIMProvider("adminDeleteIMProvider")
    test.Run()
