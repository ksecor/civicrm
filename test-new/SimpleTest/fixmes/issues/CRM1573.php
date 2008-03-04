<?php

require_once 'api/crm.php';

require_once 'CRM/Core/BAO/Domain.php';
require_once 'CRM/Utils/Token.php';

class TestOfI1573 extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testTokenReplace( )
    {
        $string = ' {domain.address}, {domain.name}, {domain.email} {contact.display_name} http://localhost/~lobo/drupal/civicrm/profile/edit?reset=1&gid=1&id={contact.contact_id}&{contact.checksum}';
        $domain = CRM_Core_BAO_Domain::getDomainById( 1 );

        $html = CRM_Utils_Token::replaceDomainTokens($string, $domain, true);

        $params  = array( 'contact_id' => 102 );
        $contact =& crm_fetch_contact( $params );
        if ( is_a( $contact, 'CRM_Core_Error' ) ) {
            CRM_Core_Error::debug( 'c', $contact );
        }

        $html = CRM_Utils_Token::replaceContactTokens( $html, $contact, false );
        echo $html . "<p>";
    }
}


