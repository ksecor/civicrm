<?php
require_once 'Common.php';
require_once 'GenDataset.php';

// This constant is used to set size fo dataset. 
// The value entered is multiple of 1000 or 1k
// Ex. if the constant value is 10 then dataset size will be (1000 * 10)
$sizeOfDS = 10;

$objCommon     = new test_RSTest_Common();
$recordSetSize = $objCommon->recordsetSize($sizeOfDS);

echo "\n";
echo "Recordset Size : " . ($recordSetSize / 1000) . " K";
echo "\n";

echo "\nStarting Generating Dataset \n";
$objGenDataset = new test_RSTest_GenDataset($recordSetSize);
$startTime = microtime(true);
$objGenDataset->run();
$endTime   = microtime(true);
echo "\nEmding Generating Dataset \n";
echo "Time taken for Generating Recordset : " . ($endTime - $startTime) . " seconds";
echo "\n";


?>