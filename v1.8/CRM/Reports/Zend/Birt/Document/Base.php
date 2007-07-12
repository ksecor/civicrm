<?php
require_once ('Reports/Zend/Birt.php');

class Reports_Zend_Birt_Document_Base extends Reports_Zend_Birt {
	
	/**
	 * Creates a org.zend.birt.ZBBridge Java object (using the JavaBridge)
	 * Sets a BIRT report document file
	 *
	 * @param string $rptDocumentFile Full path to a BIRT report document file
	 */
	function __construct($rptDocumentFile) {
		parent::__construct();
		$this->setReport($rptDocumentFile);
	}
	
	/**
	 * Set a BIRT report document file. Overwrite the current used file
	 * Note: If the setReport() function failed, the report document file that was set in
	 *  the constructor will be used
	 *
	 * @param string $rptDocumentFile Full path to a BIRT report document file
	 * @return bool True on success
	 */
	function setReport($rptDocumentFile) {
		if (!$rptDocumentFile) {
			return $this->_error("Report document file was not set.");
		} elseif (!file_exists($rptDocumentFile)) {
			return $this->_error("The report document file '$rptDocumentFile' doesn't exist.");
		} elseif (!is_file($rptDocumentFile)) {
			return $this->_error("Report document file '$rptDocumentFile' is not a file.");
		} elseif (!is_readable($rptDocumentFile)) {
			return $this->_error("The report document file '$rptDocumentFile' is not readable.");
		}
		return $this->birt_object->setReportDocument($rptDocumentFile);
	}
	
	/**
	 * Returns the number of pages in the report
	 *
	 * @return int
	 */
	function getNumberOfPages() {
		return $this->birt_object->getReportHTMLPageCount();
	}
	
	/**
	 * Returns an array of all the bookmarks in a report
	 *
	 * @return array Array of bookmark names (strings)
	 */
	function getBookmarks() {
		return $this->birt_object->getBookmarks();
	}
	
	/**
	 * Given a bookmark in a report, find the (first) page that the bookmark appears in
	 *
	 * @param string $bookmark The name of the bookmark
	 * @return int The page number that the bookmark appears first or false if bookmark was not found
	 */
	function getHTMLBookmarkPage($bookmark) {
		$pageNumber = $this->birt_object->getHTMLPageOfBookmark($bookmark);
		if ($pageNumber == -1) {	// Bookmark is not found
			//return $this->_error("Bookmark '$bookmark' doesn't exists");
			return false;
		}
		return $pageNumber;
	}
	
	/**
	* Returns a list of child TOC (Table Of Content) nodes for a specific TOC node id
	* Use null to get the TOC children of the TOC root
	* Returns array of properties for each TOC node with the node id, node display string and node bookmark
	*
	* @param string $parentTocNodeId Use null to get the TOC children of the TOC root
	* @return array Array of TOC nodes, each one is an arary with TOC properties
	*/
	function getToc($parentTocNodeId=null) {
		return $this->birt_object->getToc($parentTocNodeId);
	}
	
	/**
	 * Renders the report document to a report in the specified format, save the report output in a file
	 * 
	 * @param string $reportFormat The format of the report output, one of the BIRT_REPORT_FORMAT_* constants
	 * @param mixed $pageNumber The page number (or range of page numbers) to render
	 * 							i.e. 3 for render only page 3
	 * 								 2-4 to render pages 2,3 and 4
	 * 							use the string 'all' to render all pages
	 * @param string $reportFile Name for the report output file (full path)
	 * @return bool Success status
	 */
	function renderReportToFile($reportFormat, $reportFile, $pageNumber='all') {
		if (!$this->_isSupportedFormat($reportFormat)) {
			return false;
		}
		if (!$this->_fileNotWritable($reportFile, 'report')) {
			return false;
		}
		if ($reportFormat == BIRT_REPORT_FORMAT_HTML_EMBEDDED) {
			// If the report format is HTML embedded, the embeded parameter (the second) is true and the report format is actually HTML
			return $this->birt_object->renderReportFile(BIRT_REPORT_FORMAT_HTML, true, $pageNumber, $reportFile);
		} else {
			return $this->birt_object->renderReportFile($reportFormat, false, $pageNumber, $reportFile);
		}
	}
	
	/**
	 * Renders the report document to a report in the specified format, return the rendered report output as a string
	 *
	 * @param string $reportFormat The format of the report output, one of the BIRT_REPORT_FORMAT_* constants
	 * @param mixed $pageNumber The page number (or range of page numbers) to render
	 * 							i.e. 3 for render only page 3
	 * 								 2-4 to render pages 2,3 and 4
	 * 							use the string 'all' to render all pages
	 * @return string The report output or false on failure
	 */
	function renderReport($reportFormat, $pageNumber='all') {
		if (!$this->_isSupportedFormat($reportFormat)) {
			return false;
		}
		if ($reportFormat == BIRT_REPORT_FORMAT_HTML_EMBEDDED) {
			// If the report format is HTML embedded, the embeded parameter (the second) is true and the report format is actually HTML
			return $this->birt_object->renderReportStream(BIRT_REPORT_FORMAT_HTML, true, $pageNumber);
		} else {
			return $this->birt_object->renderReportStream($reportFormat, false, $pageNumber);
		}
	}
	
	/**
	 * Renders the report document to a report in the specified format, output the rendered report output
	 *
	 * @param string $reportFormat The format of the report output, one of the BIRT_REPORT_FORMAT_* constants
	 * @param mixed $pageNumber The page number (or range of page numbers) to render
	 * 							i.e. 3 for render only page 3
	 * 								 2-4 to render pages 2,3 and 4
	 * 							use the string 'all' to render all pages
	 * @return bool Success status
	 */
	function renderReportToOutput($reportFormat, $pageNumber='all') {
		$reportOutput = $this->renderReport($reportFormat,$pageNumber);
		if ($reportOutput === false) {
			return false;
		}
		echo $reportOutput;
		return true;
	}
	
	/**
	 * Close the report document object.
	 *
	 */
	function closeReport() {
		$this->birt_object->closeReport();
	}
}

?>