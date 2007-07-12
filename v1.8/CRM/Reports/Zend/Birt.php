<?php
/** If enabled, the full Java exception will be caught as error, otherwise, only the exception cause */
define('BIRT_DEBUG_MODE', false);

// Possible format options for BIRT reoprts
/** Report format is a full stand-alone HTML page */
define('BIRT_REPORT_FORMAT_HTML', 'HTML');
/** Report format is an embedded HTML block in a DIV element */
define('BIRT_REPORT_FORMAT_HTML_EMBEDDED', 'HTML_EMBEDDED');
/** Report format is a PDF document */
define('BIRT_REPORT_FORMAT_PDF', 'PDF');

class Reports_Zend_Birt {
	
	/**
	 * Birt object to handle a report
	 *
	 * @var ZBBridge
	 */
	var $birt_object=null;
	
	/**
	 * Last error occured
	 *
	 * @var string
	 */
	var $_error=null;
	
	/**
	 * Checks if Java was loaded and creates a ZBBridge object
	 * 
	 * @return ZBirt
	 */
	function __construct( ) {
		// Ignore all Java exceptions in PHP4
		ini_set('java.exception_error_level',0);
		// Make sure Java is throwing exceptions so we could catch them in PHP5
		if (function_exists('java_throw_exceptions')) {
			@java_throw_exceptions(1);
		}
		
		// Make sure that the java extension is loaded (it must be the zend java bridge)
		if (!extension_loaded('java') || !function_exists('java_get_statistics')) {
			trigger_error('Zend Java Bridge extension was not loaded', E_USER_ERROR);
			die();
		}
		
		// Make sure that the Java Server is running, the java_get_statistics() should return an array
		if (!is_array(@java_get_statistics())) {
			trigger_error('Failed to connect to the Zend Java Bridge server on port '.ini_get('java.server_port'), E_USER_ERROR);
			die();
		}
		
		// Create the Zend BIRT Bridge Java object
		$this->birt_object = new Java('org.zend.birt.ZBBridge');
		
		// Check if the Zend BIRT Bridge Java object was created
		if (!is_object($this->birt_object)) {
			trigger_error('Failed to create a ZBBridge Java object. '.$this->getException(), E_USER_ERROR);
			die();
		}
	}
	
	/**
	 * Sets 2 configuration parameters for the report images
	 * The direcotry where the report images will be created and the URL for those images
	 *  (the second only applies if the report will be rendered as HTML)
	 *
	 * @param string $imageDir The path where the report images will be created during the report rendering
	 * 							If path is invalid or not writable, the default imageDir will be used (from the BIRT config file)
	 * @param string $baseURL (optional) The base URL that will be used to view the report images in the report
	 * 						  Only applies if the report will be rendered as HTML, the base URL and the image name
	 * 						  will be the value of the src attribute of the IMG tags in the HTML report
	 */
	function setImageConfiguration($imageDir, $baseURL=null) {
        echo "$imageDir, $baseURL<p>";
		$this->birt_object->setImageConfiguration($imageDir, $baseURL);
	}
	
	/**
	 * Set the hyperlinks URL format for the dynamic hyperlinks in the report
	 * The given format string will be the value of the href attribute in the A tags in the HTML report
	 * The value of the format string may contain several special keys
	 *  that will be dynamicly replaced during the report rendering
	 * Those special keys are:
	 * 	$BASEURL$ 	 - Deprecated
	 * 	$REPORTPATH$ - Will be replaced with the absolute path for the direcotry where the report design file was
	 * 	$REPORTNAME$ - Will be replaced with a name of a report design file that was specified
	 * 				   in the design of the rendered report
	 * 	$PARAMETERS$ - Will be replaced with a string representing all the report parameters in an array
	 * 				   i.e. If the report parameters are array(a=>1,b=>2) the $PARAMETERS$
	 * 						will be repalced with &vars[a]=1&vars[b]=2
	 * 	$BOOKMARK$   - Will be replaced with the fragment char (#) and the name of the bookmark for this link
	 * 	$FORMAT$     - Will be replaced with the format of the rendered report
	 *
	 * @param string $formatString As described in the function description
	 */
	function setHyperlinkConfiguration($formatString) {
		$this->birt_object->setHyperlinkConfiguration(null, $formatString);	
	}
	
	/**
	 * Returns the last occured error
	 *
	 * @return string The last error message
	 */
	function getError() {
		// If there is an error, we return it
		if ($this->_error) {
			return $this->_error;
		}
		// Else, we get the last Java exception as error
		return $this->getException();
	}
	
	/**
	 * Set an error message and return false
	 *
	 * @access private
	 * @param string $errorMsg The error message
	 * @return bool Always return false
	 */
	function _error($errorMsg) {
		$this->_error = $errorMsg;
		return false;
	}
	
	/**
	 * Checks if there was a Java exception and returns the exception error
	 *
	 * @return string The exception as string (if there was one) ot false if there wasn't
	 */
	function getException() {
		if (!function_exists('java_last_exception_get')) {
			return false;
		}
		$exception = java_last_exception_get();
		if ($exception) {
			java_last_exception_clear();
			
			if (!BIRT_DEBUG_MODE) {
				// Get the cause of the Java exception
				while (is_object($exception->getCause())) {
					$exception = $exception->getCause();
				}
			}
			
			return $exception->toString();
		}
		
		return false;
	}
	
	/**
	 * Return whether a file is writable or not (if the file doesn't exists, it's directory must be writable)
	 * If the file is not writable, an error message will be set
	 *
	 * @access protected
	 * @param string $file
	 * @param string $fileDescription This will be used as the file description in the error messages
	 * @return bool
	 */
	function _fileNotWritable($file, $fileDescription) {
		if (!$file) {
			return $this->_error("$fileDescription file was not set");
		}
		if (file_exists($file) && !is_writable($file)) {
			return $this->_error("Cannot create $fileDescription file, file '$file' is not writable");
		} elseif (!file_exists($file) && !is_writable(dirname($file))) {
			return $this->_error("Cannot create $fileDescription file, directory '".dirname($file)."' is not writable");
		}
		return true;	// File is writable
	}
	
	/**
	 * check if the given format is a supported output format for BIRT reports
	 *
	 * @access protected
	 * @param string $format
	 * @return bool
	 */
	function _isSupportedFormat($format) {
		if (!in_array(strtoupper($format), array(BIRT_REPORT_FORMAT_HTML, BIRT_REPORT_FORMAT_HTML_EMBEDDED, BIRT_REPORT_FORMAT_PDF))) {
			return $this->_error("Report output format '$format' is not supported");
		}
		return true;
	}
	
}
?>