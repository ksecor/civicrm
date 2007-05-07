<?php

require_once 'api/crm.php';

require_once 'CRM/Core/BAO/Domain.php';
require_once 'CRM/Utils/Token.php';

class TestOfCRM560 extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testTokenReplace( )
    {
        $string = ' {domain.address}, {domain.name}, {domain.email}';
        $domain = CRM_Core_BAO_Domain::getDomainById( 1 );

        $html = CRM_Utils_Token::replaceDomainTokens($string, $domain, true);

        echo $html . "\n";
    }
}

?>
