<?php

/**
 * This interface defines the set of functions a class needs to implement
 * to use the CRM/Selector object.
 *
 * Using this interface allows us to standardize on multiple things including
 * list display, pagination, sorting and export in multiple formats (CSV is
 * supported right now, XML support will be added as and when needed
 *
 */

interface CRM_Selector_API {

    /*
     * Based on the action, the GET variables and the session state
     * it adds various key => value pairs to the params array including
     * 
     *  status    - the status message to display. Modifiers will be defined
     *              to integrate the total count and the current state of the 
     *              page: e.g. Displaying Page 3 of 5
     *  csvString - The html string to display for export as csv
     *  rowCount  - the number of rows to be included
     *  delta     - The number of links surronding the current page.
     *
     */
    function getPagerParams( $action, &$params );

    /* returns the sort order array for the given action */
    function getSortOrder( $action );
    
    /* returns the column headers as an array of tuples:
     *  (label, sortLink)
     */
    function getColumnHeaders( $action );
    
    /* returns the number of rows for this action */
    function getTotalCount( $action );
    
    /* returns all the rows in the given range */
    function getRows( $action, $offset, $rowCount, $sort );

    /* get the column headers for export */
    function getExportColumnHeaders( $action );
    
    /* get the row data for export */
    function getExportRows( $action );

    /* return the filename for the exported CSV */
    function getExportFileName( $action );

}

?>
