<?php

/**
 *
 * This class is a generic class to be used when we want to display
 * a list of rows along with a set of associated actions
 *
 * Centralizing this code enables us to write a generic lister and enables
 * us to automate the export process. To use this class, the object has to
 * implement the Selector/Api.interface.php class
 *
 */

require_once 'CRM/Pager.php';
require_once 'CRM/Sort.php';
require_once 'CRM/Reports/Excel.php';

class CRM_Selector_Controller {

  /* a CRM Object that implements CRM_Selector_API */
  private $_object;
    
  /* the CRM_Sort object */
  private $_sort;

  /* the current column to sort on */
  private $_sortId;

  /* the sortOrder array */
  private $_sortOrder;

  /* the CRM_Pager object */
  private $_pager;

  /* the pageId */
  private $_pageId;
    
  /* offset and rowCount */
  private $_pagerOffset;
  private $_pagerRowCount;

  /* the objectAction for the WebObject */
  private $_action;

  function __construct($object, $pageID, $sortID, $action) {
    $this->_object = $object;
        
    $this->_pageId = $pageID;
    $this->_sortId = $sortID;
    $this->_action = $action;
        
    $params = array(
                    'totalCount' => $this->_object->getTotalCount($action),
                    'pageID'     => $this->_pageId
                    );
    $this->_object->getPagerParams($action, $params);

    /*
     * Set the default values of RowsPerPage
     */
    $params{'rowCount'} = $params{'rowCount'} ? $params{'rowCount'} : CRM_Pager::ROWCOUNT;
    
    $this->_pager = new CRM_Pager( $params );
        
    list($this->_pagerOffset, $this->_pagerRowCount) =
      $this->_pager->getOffsetAndRowCount();

    $this->_sortOrder = $this->_object->getSortOrder($action);
    $this->_sort = new CRM_Sort( $this->_sortOrder, $this->_sortId );
  }

  function setResponseData($response) {
    $response->setVar('pager', $this->_pager->toArray() );
    $response->setVar('sort' , $this->_sort->getLinks() );
        
    $response->setVar('columnHeaders', 
                      $this->_object->getColumnHeaders( $this->_action ) );
            
    $rows =    $this->_object->getRows( $this->_action, 
                                        $this->_pagerOffset,
                                        $this->_pagerRowCount,
                                        $this->_sort );
    if ( count( $rows ) ) {
      $response->setVar('rowsEmpty', false);
      $response->setVar('rows'     , $rows);
    } else {
      $response->setVar('rowsEmpty', true);
    }

  }

  function getPager() {
    return $this->_pager;
  }

  function getSort() {
    return $this->_sort;
  }
    
  function export() {
    $fileName = $this->_object->getExportFileName     ( $this->_action );
    $headers  = $this->_object->getExportColumnHeaders( $this->_action );
    $rows     = $this->_object->getExportRows         ( $this->_action );

    /**
     * At this point we need to remove the 'action' export from the session
     * Else this might result in an infinite loop
     * Will do this once we figure out how we handle session data
     */

    CRM_Reports_Excel::writeCSVFile( $fileName, $headers, $rows );

    exit();
  }


}

?>
