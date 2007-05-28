<?php
require_once('Reports/Zend/Birt.php');
require_once('Reports/Zend/Birt/Document.php');

/**
 * Using the Zend Java Bridge, it wraps a Java object, and gives PHP functionality to 
 *  handle (set,configure and render) a BIRT report design file
 * 
 * The Zend_Birt_Report_Design_Base is written in PHP4
 */
class Reports_Zend_Birt_Design_Base extends Reports_Zend_Birt {
	
	/**
	 * The parameters of the report
	 *
	 * @var array
	 */
	var $_reportParameters = array();
	
	/**
	 * Creates a org.zend.birt.ZBBridge Java object (using the Zend Java Bridge)
	 * Sets a BIRT report design file
	 * Note: If the setReport() function failed, the report design file that was set in
	 *  the constructor will be used
	 *
	 * @param string $rptDesignFile Full path to a BIRT report design file
	 */
	function __construct($rptDesignFile) {
		parent::__construct();
		$this->setReport($rptDesignFile);
	}
	
	/**
	 * Set a BIRT report design file. Overwrite the current used file
	 * Note: If the setReport() function failed, the report document file that was set in
	 *  the constructor will be used
	 *
	 * @param string $rptDesignFile Full path to a BIRT report design file
	 * @return bool True on success
	 */
	function setReport($rptDesignFile) {
		if (!$rptDesignFile) {
			return $this->_error("Report design file was not set.");
		} elseif (!file_exists($rptDesignFile)) {
			return $this->_error("The report design file '$rptDesignFile' doesn't exist.");
		} elseif (!is_file($rptDesignFile)) {
			return $this->_error("Report design file '$rptDesignFile' is not a file.");
		} elseif (!is_readable($rptDesignFile)) {
			return $this->_error("The report design file '$rptDesignFile' is not readable.");
		}
		return $this->birt_object->setReport($rptDesignFile);
	}
	
	/**
	 * Sets a parameter for the report. If this parameter is already set, it will overwrite it's value
	 *
	 * @param string $name Parameter name
	 * @param string $value Parameter value
	 */
	function setParameter($name, $value) {
		$this->_reportParameters[$name] = $value;
	}
	
	/**
	 * Generate and save a BIRT report document file from the report design file
	 * The report document file can later be handled using the Zend_Birt_Report_Document class
	 * 
	 * @param string $rptDocumentFile Name for the report document file (full path)
	 * @param array $parameters (optional) If set, this will be the parameters that will be used
	 * 						 to create the report document and will overwrite any parameters that
	 * 						 was already set (using the setParameter() method)
	 * @return bool Success status
	 */
	function createReportDocumentFile($rptDocumentFile, $parameters=null) {
		if (!$this->_fileNotWritable($rptDocumentFile, 'report document')) {
			return false;
		}
		if (!is_array($parameters)) {
			$parameters = $this->_reportParameters;
		}
		return $this->birt_object->runReport($parameters, $rptDocumentFile);
	}
	
	/**
	 * Generate and save a BIRT report document file from the report design file
	 * Returns a Zend_Birt_Report_Document object that was initialized with the created report document file
	 * 
	 * @param string $rptDocumentFile Name for the report document file (full path)
	 * @param array $parameters (optional) If set, this will be the parameters that will be used
	 * 						 to create the report document and will overwrite any parameters that
	 * 						 was already set (using the setParameter() method)
	 * @return Zend_Birt_Report_Document Returns the generated Zend_Birt_Report_Document object or false on failure
	 */
	function generateReportDocument($rptDocumentFile, $parameters=null) {
		if (!$this->createReportDocumentFile($rptDocumentFile, $parameters)) {
			return false;
		}
		return new Reports_Zend_Birt_Document($rptDocumentFile);
	}
	
	/**
	 * Returns an array of all the parameters that a report can get
	 * Returns only the parameters names, for more details use the getParameterDetails() method
	 *
	 * @return array Array of parameters names (strings)
	 */
	function getParameters() {
		return $this->birt_object->getParameters();
	}
	
	/**
	 * Returns an array of all the parameters with full details that a report can get
	 * Examples for parameter details are the parameter default value, parameter data type, disaply name,
	 * 	whether or not this parameter is cascading (controls the value options for another parameter) etc.
	 *
	 * @return array Assosiative array with the parameter name as key and an array of the paramater details as value
	 */
	function getParametersDetails() {
		return $this->birt_object->getParameterDetails();
	}
	
	/**
	 * Returns a list of value options for a cascading parameter according to the cascading parent value
	 *
	 * @param string $parameterName The name of the parameter to get its list of values
	 * @param mixed $value The value of the parameter's cascading parent
	 * @return array Array of values or false on failure
	 */
	function getCascadingParameterValues($parameterName, $value) {
		// The getCascadingSelectionList function must recieve an array as the value argument
		$value = (array)$value;
		
		return $this->birt_object->getCascadingSelectionList($parameterName, $value);
	}
	
	/**
	 * Render the report design to a report in the specified format, return the rendered report output as a string
	 *
	 * @param string $reportFormat The format of the report output, one of the BIRT_REPORT_FORMAT_* constants
	 * @param array $parameters (optional) If set, this will be the parameters that will be used
	 * 						 to create the report and will overwrite any parameters that
	 * 						 was already set (using the setParameter() method)
	 * @return string The report output or false on failure
	 */
	function renderReport($reportFormat, $parameters=null) {
		if ($reportFormat == BIRT_REPORT_FORMAT_PDF) {
			return $this->_renderStreamPdfReport($parameters);
		}
		return $this->_runAndRenderReport($reportFormat,null,$parameters);
	}
	
	/**
	 * Render the report design to a report in the specified format, output the rendered report output
	 *
	 * @param string $reportFormat The format of the report output, one of the BIRT_REPORT_FORMAT_* constants
	 * @param array $parameters (optional) If set, this will be the parameters that will be used
	 * 						 to create the report and will overwrite any parameters that
	 * 						 was already set (using the setParameter() method)
	 * @return bool Success status
	 */
	function renderReportToOutput($reportFormat, $parameters=null) {
		if ($reportFormat == BIRT_REPORT_FORMAT_PDF) {
			$reportOutput = $this->_renderStreamPdfReport($parameters);
		} else {
			$reportOutput = $this->_runAndRenderReport($reportFormat,null,$parameters);
		}
		
		if ($reportOutput === false) {
			return false;
		}
		
		if ($reportFormat == BIRT_REPORT_FORMAT_PDF) {
			header("Content-Type: application/pdf");
			header("Content-Disposition: inline; filename=\"report.pdf\"");
		}
		echo $reportOutput;
		return true;
	}
	
	/**
	 * Render the report design to a report in the specified format, save the report output in a file
	 * 
	 * @param string $reportFormat The format of the report output, one of the BIRT_REPORT_FORMAT_* constants
	 * @param string $reportFile Name for the report output file (full path)
	 * @param array $parameters (optional) If set, this will be the parameters that will be used
	 * 						 to create the report and will overwrite any parameters that
	 * 						 was already set (using the setParameter() method)
	 * @return bool Success status
	 */
	function renderReportToFile($reportFormat, $reportFile, $parameters=null) {
		if (!$this->_fileNotWritable($reportFile, 'report')) {
			return false;
		}
		return $this->_runAndRenderReport($reportFormat,$reportFile,$parameters);
	}
	
	/**
	 * Render the report design to a report in the specified format
	 *
	 * @access protected
	 * @param string $reportFormat The format of the report output, one of the BIRT_REPORT_FORMAT_* constants
	 * @param string $reportFile If set, the report output will be saved ito a file, else it will be returned as a string
	 * @param array $parameters (optional) If set, this will be the parameters that will be used
	 * 						 to create the report and will overwrite any parameters that
	 * 						 was already set (using the setParameter() method)
	 * @return mixed If report outupt is saved to a file the function will return a success status (boolean),
	 * 				 if not, it will return the report output as a string (or false on failure)
	 */
	function _runAndRenderReport($reportFormat, $reportFile=null, $parameters=null) {
		if (!$this->_isSupportedFormat($reportFormat)) {
			return false;
		}
		if (!is_array($parameters)) {
			$parameters = $this->_reportParameters;
		}
		$embedded = false;
		if ($reportFormat == BIRT_REPORT_FORMAT_HTML_EMBEDDED) {
			// If the report format is HTML embedded, the embeded parameter (the forth) is true and the report format is actually HTML
			$reportFormat = BIRT_REPORT_FORMAT_HTML;
			$embedded = true;
		}
				
		$return = $this->birt_object->runAndRenderReport($reportFormat,
					    							  	 $parameters,
													  	 $reportFile,
													  	 $embedded,
													  	 ($reportFile == null)
														);
		$this->birt_object->closeReport();
		return $return;
	}
	
	/**
	 * Returns array of information about the report, things like report description, title, etc. 
	 *
	 * @return array Assosiative array of the report infromation
	 */
	function getReportInfo() {
		return $this->birt_object->getReportInfo();
	}
	
	
	/**
	 * @param array $parameters
	 * @return string (PDF stream)
	 */
	function _renderStreamPdfReport($parameters=null) {
		$tempFileName = '/birtTemp'.time().'.pdf';
		
		if (!$this->renderReportToFile(BIRT_REPORT_FORMAT_PDF, BIRT_TMP_DIR . $tempFileName, $parameters)) {
			return $this->getError();
		}
		$result = file_get_contents(BIRT_TMP_DIR.$tempFileName);
		@unlink(BIRT_TMP_DIR.$tempFileName);
		return $result;
	}
	
}
?>