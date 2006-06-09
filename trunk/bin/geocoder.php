<?php

ini_set( 'include_path', '.:../packages' );

require_once 'XML/RPC.php';

$string = '455 FDR Drive, New York, NY 10002';
// $string = '24 ashbury terrace, San Francisco, California, 94117';
// $string = '107 garden street, great neck, new york, 11021';

$params   = array( new XML_RPC_Value( $string, 'string' ) );
$message  = new XML_RPC_Message( 'geocode', $params );
$client   = new XML_RPC_Client ( '/service/xmlrpc', 'rpc.geocoder.us' );
$response = $client->send( $message );
print_r( $response );
$data = XML_RPC_decode( $response->value( ) );
$data = $data[0];

print_r( $data );

?>