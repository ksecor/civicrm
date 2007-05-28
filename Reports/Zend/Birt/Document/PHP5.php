<?php
require_once ('Reports/Zend/PHP5_Wrapper.php');
require_once ('Reports/Zend/Birt/Document/Base.php');

/**
 * This Zend_Birt_Report_Document is for PHP5, it wraps a real
 *  Zend_Birt_Report_Document_Base object for catching and throwing exceptions
 */
class Reports_Zend_Birt_Document extends Reports_Zend_PHP5_Wrapper {
	
	/**
	 * Create an instance of a Zend_Birt_Report_Document class for PHP5
	 *
	 * @param string $rptDocumentFile Full path to a BIRT report document file
	 * @throws Exception
	 */
	public function __construct($rptDocumentFile) {
		$this->instanceWrappedObject('Reports_Zend_Birt_Document_Base', $rptDocumentFile);
	}
	
}

?>