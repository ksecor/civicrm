<?php
$filename = "ContactData.csv";

$fh = fopen($filename, "r");
$data = fgetcsv($fh);
print_r($data);
fclose($fh);

?>
