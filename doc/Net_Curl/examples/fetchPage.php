<?php

// php -q fetchPage.php http://www.example.com
//
// Fetches the URL passed to the constructor and echos it out.

require_once('Net/Curl.php');

$curl = & new Net_Curl($argv[1]);
$result = $curl->create();
if (!PEAR::isError($result)) {  

    // Set other options here with Net_Curl::setOption()

    $result = $curl->execute();
    if (!PEAR::isError($result)) {
        echo $result."\n";
    } else {
        echo $result->getMessage()."\n";
    }

    $curl->close();
} else {
    echo $result->getMessage()."\n";
}

?>
