<?php

/**
 *
 * This class extends the PEAR pager object by substituting standard default pager arguments
 * We also extract the pageId from either the GET variables or the POST variable (since we
 * use a POST to jump to a specific page. At some point we should evaluate if we want
 * to use Pager_Jumping instead. We've changed the format to allow navigation by jumping
 * to a page and also First, Prev CURRENT Next Last
 *
 */

require_once 'Pager/Sliding.php';

class CRM_Pager extends Pager_Sliding {

  const ROWCOUNT = 50;

  const PAGE_ID          = 'crmPageId';
  const PAGE_ID_BOTTOM   = 'crmPageId_bottom';
  
  private $_values;  // array of caculated Pager values;
  
  /**  
   * The pager constructor. Takes a few values, and then assigns a lot of defaults
   * to the PEAR pager class
   * We have embedded some html in this class. Need to figure out how to export this
   * to the top level at some point in time
   *
   * @param int     total        the total count of items to be displayed
   * @param int     currentPage  the page currently being displayed
   * @param string  status       the status message to be displayed. It embeds a token
   *                             %%statusMessage%% that will be replaced with which items
   *                             are currently being displayed
   * @param string  csvString    the title of the link to be displayed for the export
   * @param int     perPage      the number of items displayed per page
   *
   * @return object              the newly created and initialized pager object
   *
   * @access public
   *
   */
  function __construct( $total,
                        $currentPage = 1,
                        $status      = null,
                        $csvString   = '',
                        $perPage     = CRM_Pager::ROWCOUNT ) {

    if( $status === null ) {
      $status = "Contacts %%statusMessage%%";
    }

    $params =& $this->getDefaultParams( $total, $perPage, $currentPage );

    $this->Pager_Sliding( $params);

    $links = $this->getLinks( );

    list( $offset, $limit ) = $this->getOffsetAndLimit( );
    $start = $offset + 1;
    $end   = $offset + $limit;
    if ( $end > $total ) {
      $end = $total;
    }

    if ( $total == 0 ) {
      $statusMessage = '';
    } else {
      $statusMessage = "$start - $end of $total";
    }
    $status = str_replace( '%%StatusMessage%%', $statusMessage );

    $this->_values = array(
                           'first'       => $links['first'],
                           'back'        => $links['back'] ,
                           'next'        => $links['next'] ,
                           'last'        => $links['last'] ,
                           'pages'       => $links['pages'],
                           'currentPage' => $this->getCurrentPageID(),
                           'numPages'    => $this->numPages(),
                           'excelString' => $excelString,
                           'status'      => $status,
                           );


    /**
     * A page cannot have two variables with the same form name. Hence in the 
     * pager display, we have a form submission at the top with the normal
     * page variable, but a different form element for one at the bottom
     *
     */
    $this->_values['titleTop']    = 'Page <input size=2 maxlength=3 name="' . CRM_Pager::PAGE_ID . '" type="text" value="' . $this->_values['currentPage'] . '" /> of ' . $this->_values['numPages'];
    $this->_values['titleBottom']    = 'Page <input size=2 maxlength=3 name="' . CRM_Pager::PAGE_ID_BOTTOM . '" type="text" value="' . $this->_values['currentPage'] . '" /> of ' . $this->_values['numPages'];
    
  }


  /*
   * This function return an array of name, value pairs for insertion
   * into a template engine like Smarty
   * 
   * @param void
   *
   * @return array associative array of name value pairs
   *               some of the values have html embedded within them
   *
   * @access public
   *
   */
  function toArray() {
	return $this->_values;
  }

  /**
   * helper function to assign remaining pager options as good default
   * values
   *
   * @param int     total        the total count of items to be displayed
   * @param int     perPage      the number of items displayed per page
   * @param int     defaultCurrentPage  the current page number, default to 1
   *
   * @return array               the set of options needed to initialize the parent
   *                             constructor
   *
   * @access public
   *
   */
  function &getDefaultParams( &$total, $perPage, $defaultCurrentPage = 1 ) {
    $config = CRM_Config::instance( );

    $params = array( );

    /* set the mode for the pager to Sliding */
    $params['mode']       = 'Sliding';

    /* also set the urlVar to be a crm specific get variable */
    $params['urlVar']     = CRM_Pager::PAGE_ID;
    
    /* set this to a small value, since we dont use this functionality */
    $params['delta']      = 1;

    $params['totalItems'] = $total;
    $params['perPage']    = $perPage;
    $params['append']     = true;
    $params['separator']  = '';
    $params['spacesBeforeSeparator'] = 1;
    $params['spacesAfterSeparator']  = 1;


    // set previous and next text labels
    $params['prevImg']    = '<img src="' . $config->imageBase . 'previous.gif" alt="Previous" border="0" />';
    $params['nextImg']    = '<img src="' . $config->imageBase . 'next.gif" alt="Next" border="0" />';


    // set first and last text fragments
    $params['firstPagePre']  = '';
    $params['firstPageText'] = '<img src="' . $config->imageBase . 'first.gif" alt="First" border="0" />';
    $params['firstPagePost'] = '';

    $params['lastPagePre']   = '';
    $params['lastPageText']  = '<img src="' . $config->imageBase . 'last.gif" alt="Last" border="0" />';
    $params['lastPagePost']  = '';

    $params['currentPage'] = CRM_Pager::getPageID( $defaultCurrentPage );

    return $params;
  }

  /**
   * Figure out the current page number based on value of
   * GET / POST variables. Hierarchy rules are followed,
   * GET over-rides a POST, a POST at the top overrides
   * a POST at the bottom (of the page)
   *
   * @param int defaultPageId   current pageId
   *
   * @return int                new pageId to display to the user
   *
   * @access public
   *
   */
  static function getPageID( $defaultPageId = 1) {
    if ( isset( $_GET[ CRM_Pager::PAGE_ID ] ) ) {
      $currentPage = max( (int ) @$_GET[ CRM_Pager::PAGE_ID ], 1 );
    } else if ( isset( $_POST[ CRM_Pager::PAGE_ID ] ) ) {
      $currentPage = max( (int ) @$_POST[ CRM_Pager::PAGE_ID ], 1 );
    } else if ( isset( $_POST[ CRM_Pager::PAGE_ID_BOTTOM ] ) ) {
      $currentPage = max( (int ) @$_POST[ CRM_Pager::PAGE_ID_BOTTOM ], 1 );
    } else {
      $currentPage = $defaultPageId;
    }
    
    return $currentPage;
  }


  /**
   * Use the pager class to get the pageId and Offset
   *
   * @param void
   *
   * @return array: an array of the pageID and offset
   *
   * @access public
   *
   */
  function getOffsetAndRowCount( ) {
    $pageId = $this->getCurrentPageID( );
    if ( ! $pageId ) {
      $pageId = 1;
    }

    $offset = ( $pageId - 1 ) * $this->_perPage;

    return array( $offset, $this->_perPage );
  }

}

?>
