<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/


/**
 * This interface defines the set of functions a class needs to implement
 * to use the CRM/Selector object.
 *
 * Using this interface allows us to standardize on multiple things including
 * list display, pagination, sorting and export in multiple formats (CSV is
 * supported right now, XML support will be added as and when needed
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

interface CRM_Selector_API {

    /**
     * Based on the action, the GET variables and the session state
     * it adds various key => value pairs to the params array including
     * 
     *  status    - the status message to display. Modifiers will be defined
     *              to integrate the total count and the current state of the 
     *              page: e.g. Displaying Page 3 of 5
     *  csvString - The html string to display for export as csv
     *  rowCount  - the number of rows to be included
     *
     * @param string action the action being performed
     * @param array  params the array that the pagerParams will be inserted into
     * 
     * @return void
     *
     * @access public
     *
     */
    function getPagerParams( $action, &$params );

    /**
     * returns the sort order array for the given action
     *
     * @param string action the action being performed
     *  
     * @return array the elements that can be sorted along with their properties
     * @access public
     *
     */
    function &getSortOrder( $action );
    
    /**
     * returns the column headers as an array of tuples:
     * (name, sortName (key to the sort array))
     *
     * @param string $action the action being performed
     * @param enum   $type   what should the result set include (web/email/csv)
     *
     * @return array the column headers that need to be displayed
     * @access public
     */
    function &getColumnHeaders( $action = null, $type = null );
    
    /**
     * returns the number of rows for this action
     *
     * @param string action the action being performed
     *
     * @return int   the total number of rows for this action
     *
     * @access public
     *
     */
    function getTotalCount( $action );
    
    /**
     * returns all the rows in the given offset and rowCount
     *
     * @param enum   $action   the action being performed
     * @param int    $offset   the row number to start from
     * @param int    $rowCount the number of rows to return
     * @param string $sort     the sql string that describes the sort order
     * @param enum   $type     what should the result set include (web/email/csv)
     *
     * @return int   the total number of rows for this action
     * @access public
     */
    function &getRows( $action, $offset, $rowCount, $sort, $type = null );

    /**
     * return the template (.tpl) filename
     *
     * @param string $action the action being performed
     *
     * @return string 
     * @access public
     *
     */
    function getTemplateFileName( $action = null );

    /**
     * return the filename for the exported CSV
     *
     * @param string type   the type of export required: csv/xml/foaf etc
     *
     * @return string the fileName which we will munge to skip spaces and
     *                special characters to avoid various browser issues
     *
     */
    function getExportFileName( $type = 'csv' );

}

?>
