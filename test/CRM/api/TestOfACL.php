<?php

require_once 'api/crm.php';

class TestOfACLCache extends UnitTestCase {
    
    function testACLCache()
    {
        require_once 'CRM/ACL/BAO/Cache.php';
        $acls = CRM_ACL_BAO_Cache::build( 102 );
        CRM_Core_Error::debug( 'a', $acls );
    }

}

?>