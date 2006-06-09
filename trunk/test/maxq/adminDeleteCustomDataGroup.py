from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import DBUtil
import commonAPI


# definition of test class
class adminDeleteCustomDataGroup(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
        
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        nameCDG       = 'Test Group'
        
        queryCDGID    = 'select id from civicrm_custom_group where title like \'%%%s%%\'' % nameCDG
        customDataGID = db.loadVal(queryCDGID)
        queryD        = 'delete from civicrm_custom_group where id=%s' % customDataGID
        
        if customDataGID :
            if db.execute(queryD) :
                print "***************************************************************"
                print "\'%s\' Custom Data Grop Deleted Successfully" % nameCDG
                print "***************************************************************"
            else :
                print "***************************************************************"
                print "Some Problem while Deleteing \'%s\' Custom Data Group" % nameCDG
                print "***************************************************************"
        else :
            print "***************************************************************"
            print "\'%s\' Custom Data Group Does Not Exists" % nameCDG
            print "***************************************************************"
            
# Code to load and run the test
if __name__ == 'main':
    test = adminDeleteCustomDataGroup("adminDeleteCustomDataGroup")
    test.Run()
