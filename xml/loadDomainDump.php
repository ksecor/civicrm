<?php

if( empty($argv[1]) ) {
    echo "Usage: php loadDump.php </path/to/LOADBACKUP.sql>\n\n";
    exit(1);
}
$fileName = $argv[1]."LOADBACKUP.sql";
$fp = file($fileName);

foreach ($fp as $key => $value) {
    system($fp[$key]);
}

?>