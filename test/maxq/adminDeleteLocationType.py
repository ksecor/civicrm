from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import DBUtil
import commonAPI


# definition of test class
class adminDeleteLocationType(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
        
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        nameLT      = 'Test Location Type'
        
        queryID     = 'select id from crm_location_type where name like \'%%%s%%\'' % nameLT
        locationTID = db.loadVal(queryID)
        queryD      = 'delete from crm_location_type where id=%s' % locationTID

        if locationTID :
            if db.execute(queryD) :
                print "***************************************************************"
                print "\'%s\' Location Type Deleted Successfully" % nameLT
                print "***************************************************************"
            else :
                print "***************************************************************"
                print "Some Problem while Deleteing \'%s\' Location Type" % nameLT
                print "***************************************************************"
        else :
            print "***************************************************************"
            print "\'%s\' Location Type Does Not Exists" % nameLT
            print "***************************************************************"

# Code to load and run the test
if __name__ == 'main':
    test = adminDeleteLocationType("adminDeleteLocationType")
    test.Run()
