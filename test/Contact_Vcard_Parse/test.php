<?php
    
    // report all errors
    error_reporting(E_ALL);
    
    // include the class file
    require_once 'Contact_Vcard_Parse.php';
    
    // instantiate a parser object
    $parse = new Contact_Vcard_Parse();
    
    // parse it
    $data = $parse->fromFile('test.vcf');
    
    // output results
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
?>