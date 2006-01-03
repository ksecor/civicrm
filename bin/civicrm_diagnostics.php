<?php

function civicrm_diagnostic() {
    require_once '../civicrm.config.php';
    require_once 'CRM/Core/Config.php';
    
    $config  =& new CRM_Core_Config();
    
    $drupalPath = explode('/', $config->templateCompileDir);
    
    $pathArray = array();
    foreach($drupalPath as $path ) {
        if ($path == 'files') {
            break;
        }
        $pathArray[] = $path;
    }


    $drupalPath = implode( '/',$pathArray );
    $PHPVersion = phpversion();
    
    $mysqlPath  = $config->mysqlPath;
    $mysqlVersion = exec($mysqlPath.'mysql --version');
    
    $versionFile = "../xml/version.xml";
    $dom = DomDocument::load( $versionFile );
    $dom->xinclude( );
    $versionXML = simplexml_import_dom( $dom );
    
    $civicrmVersion = $versionXML->version_no;

    
    $f = fopen($drupalPath.'/CHANGELOG.txt', 'r'); 
    list(, $version) = explode(' ', fgets($f, 13)); fclose($f); 
    $drupalVersion = $version;
    
    //echo "PHP Version      : ".$PHPVersion."\n";
    //echo "MySQL Version    : ".$mysqlVersion."\n";
    //echo "CiviCRM Version  : ".$civicrmVersion."\n";
    //echo "Drupal Version   : ".$drupalVersion."\n";
    
    return $returnString = "PHP Version:".$PHPVersion."<br>"."MySQL Version:".$mysqlVersion."<br>"."CiviCRM Version:".$civicrmVersion."<br>"."Drupal Version:".$drupalVersion."<br>";
}

echo  civicrm_diagnostic();

?>