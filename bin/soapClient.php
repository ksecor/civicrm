<?php

$client =& new SoapClient(null, array(
                                      'location' => 'http://civicrm1.electricembers.net/~lobo/drupal/modules/civicrm/extern/soap.php',
                                      'uri' => 'urn:civicrm', 'trace' => 1 )
                          );
$result =& $client->ping( '123' );

print_r($result);
$result =& $client->ping( 'def' );           
print_r($result);                                                       
$result =& $client->ping( 'abc' );           
print_r($result);                                                       
 

?>