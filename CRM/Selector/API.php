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
     * @param string action the action being performed
     *
     * @return array the column headers that need to be displayed
     *
     * @access public
     *
     */
    function &getColumnHeaders( $action );
    
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
     * @param string action   the action being performed
     * @param int    offset   the row number to start from
     * @param int    rowCount the number of rows to return
     * @param string sort     the sql string that describes the sort order
     *
     * @return int   the total number of rows for this action
     *
     * @access public
     *
     */
    function &getRows( $action, $offset, $rowCount, $sort );

    /**
     * return the template (.tpl) filename
     *
     * @param string $action the action being performed
     *
     * @return string 
     * @access public
     *
     */
    function getTemplateFileName( $action );

    /**
     * return the "module" name. This name will be used to prefix
     * the variables in the session scope
     *
     *@param string $action the action being performed
     *
     * @return string
     * @access public
     *
     */
    function getModuleName( $action );

    /**
     * returns the column headers as an array of tuples:
     * (name, sortName (key to the sort array))
     * This is specifically for Export (typically exports have
     * a whole lot more detailed information than normal
     * html reports. Since export could be in various formats
     * am introducing a type field here
     *
     * @param string action the action being performed
     * @param string type   the type of export required: csv/xml/foaf etc
     *
     * @return array the column headers that need to be displayed
     *
     * @access public
     *
     */
    function getExportColumnHeaders( $action, $type = 'csv' );
  
    /**
     * returns all the rows for the given action
     * This will typically be a big set, and hence is a slow
     * time consuming process. Might need to add an iterator
     * and make it more stream-lined, so we can actually
     * fetch and shove bytes down the network pipe at the same
     * time
     *
     * @param string action the action being performed
     * @param string type   the type of export required: csv/xml/foaf etc
     *
     * @return array all the data rows for the specific action
     *
     * @access public
     *
     */
    function getExportRows( $action, $type = 'csv' );
  
    /**
     * return the filename for the exported CSV
     *
     * @param string action the action being performed
     * @param string type   the type of export required: csv/xml/foaf etc
     *
     * @return string the fileName which we will munge to skip spaces and
     *                special characters to avoid various browser issues
     *
     */
    function getExportFileName( $action, $type = 'csv' );

}

?>
