from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import DBUtil
import commonAPI


# definition of test class
class adminDeleteRel(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
        
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        nameAB  = 'Test A B'
        
        queryID = 'select id from civicrm_relationship_type where name_a_b like \'%%%s%%\'' % nameAB
        relID   = db.loadVal(queryID)
        if relID :
            queryD  = 'delete from civicrm_relationship_type where id=%s' % relID
            
            if db.execute(queryD) :
                print "***************************************************************"
                print "\'%s\' Relationship Deleted Successfully" % nameAB
                print "***************************************************************"
            else :
                print "***************************************************************"
                print "Some Problem while Deleteing \'%s\' Relationship" % nameAB
                print "***************************************************************"
        else :
            print "***************************************************************"
            print "\'%s\' Relationship Does Not Exists" % nameAB
            print "***************************************************************"

# Code to load and run the test
if __name__ == 'main':
    test = adminDeleteRel("adminDeleteRel")
    test.Run()
