<?php

/* Include the Zend_Birt_Report_Document object according to the PHP version
   The reason for these "work around" and for multiple files and classes is to be able
    to have one Zend_Birt_Report_Design object with 2 different versions for PHP4 and PHP5
*/
if (substr(PHP_VERSION,0,1) == 5) {
	require_once('Reports/Zend/Birt/Document/PHP5.php');
} else {
	require_once('Reports/Zend/Birt/Document/PHP4.php');
}

?>