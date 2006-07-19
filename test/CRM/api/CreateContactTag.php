<?php

require_once 'api/crm.php';

class TestOfCreateContactTag extends UnitTestCase {
    
    function testCreateContactTag()
    {
        $params = array( 'first_name' => 'foo',
                         'last_name'  => 'bar',
                         'email'      => 'foo@bar.org',
                         );
        $contact =& crm_create_contact( $params, 'Individual' );

        $params = array ('id'   => 2 );
        $tag = crm_get_tag($params);

        crm_create_entity_tag( $tag, $contact );
    }

}

?>