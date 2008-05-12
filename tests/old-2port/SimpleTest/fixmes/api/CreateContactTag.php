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

    function testCreateContactTagV14()            
    {                           
        $params = array( 'first_name' => 'foo', 
                         'last_name'  => 'bar', 
                         'email'      => 'fooV14@bar.org',                 
                         );      
        $contact =& crm_create_contact( $params, 'Individual' ); 

        require_once 'CRM/Core/DAO/Tag.php';
        $tag =& new CRM_Core_DAO_Tag( );
        $tag->id = 2;
        if ( $tag->find( true ) ) {
            crm_create_entity_tag( $tag, $contact ); 
        }
    }

}


