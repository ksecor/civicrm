<?php
require_once ('Reports/Zend/PHP5_Wrapper.php');
require_once('Reports/Zend/Birt/Design/Base.php');

/**
 * This Zend_Birt_Report_Design is for PHP5, it wraps a real
 *  Zend_Birt_Report_Design_Base object for catching and throwing exceptions
 */
class Reports_Zend_Birt_Design extends Reports_Zend_PHP5_Wrapper {
	
	/**
	 * Create an instance of a Zend_Birt_Report_Design class for PHP5
	 *
	 * @param string $rptDesignFile Full path to a BIRT report design file
	 * @throws Exception
	 */
	public function __construct($rptDesignFile) {
		$this->instanceWrappedObject('Reports_Zend_Birt_Design_Base', $rptDesignFile);
        echo "$rptDesignFile<p>";
	}
	
}

?>