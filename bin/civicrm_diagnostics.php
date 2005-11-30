<?php

$drupalPath = '/opt/apache2/htdocs/drupal/';


$PHPVersion = phpversion();
$mysqlVersion = exec('mysql --version');

$versionFile = "../xml/version.xml";
$dom = DomDocument::load( $versionFile );
$dom->xinclude( );
$versionXML = simplexml_import_dom( $dom );

$civicrmVersion = $versionXML->version_no;


$f = fopen($drupalPath.'CHANGELOG.txt', 'r'); 
list(, $version) = explode(' ', fgets($f, 13)); fclose($f); 
$drupalVersion = $version;

echo "PHP Version      : ".$PHPVersion."\n";
echo "MySQL Version    : ".$mysqlVersion."\n";
echo "CiviCRM Version  : ".$civicrmVersion."\n";
echo "Drupal Version   : ".$drupalVersion."\n";


?>