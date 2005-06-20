<?php

$url  = $_GET['url'];
$data = '';

// make sure thre url starts with http
if ( strpos( $url, 'http://' ) === 0 ) {
    $data = file_get_contents( $url );
    if ( strpos( $data, '<?xml version="1.0"' ) !== 0 ) {
        $data = '';
    }
}

echo $data;

exit(1);

?>