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
 *
 * A simple base class for objects that need to implement the selector api
 * interface. This class provides common functionality with regard to actions
 * and display names
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Core_Selector_Base {
    /**
     * the sort order which is computed from the columnHeaders
     *
     * @var array
     */
    protected $_order;

    /**
     * This function gets the attribute for the action that
     * it matches.
     *
     * @param string  match      the action to match against
     * @param string  attribute  the attribute to return ( name, link, title )
     *
     * @return string            the attribute that matches the action if any
     *
     * @access public
     *
     */
    function getActionAttribute( $match, $attribute = 'name' ) {
        $links =& $this->links();

        // does not work for php4 .. pls revert when done with php4
        //foreach ( $link as $action => &$item ) {
        foreach ( $link as $action => $item ) {
            if ( $match & $action ) {
                return $item[$attribute];
            }
        }
        return null;
    }

    /**
     * This is a static virtual function returning reference on links array. Each 
     * inherited class must redefine this function
     *
     * links is an array of associative arrays. Each element of the array
     * has at least 3 fields
     *
     * name    : the name of the link
     * url     : the URI to be used for this link
     * qs      : the parameters to the above url along with any dynamic substitutions
     * title   : A more descriptive name, typically used in breadcrumbs / navigation
     */
    static function &links() {
        return null;
    }

    /**
     * compose the template file name from the class name
     *
     * @param string $action the action being performed
     *
     * @return string template file name
     * @access public
     */
    function getTemplateFileName($action = null)
    {
        return (str_replace('_', DIRECTORY_SEPARATOR, CRM_Utils_System::getClassName($this)) . ".tpl");
    }

    /**
     * getter for the sorting direction for the fields which will be displayed on the form.
     *
     * @param string action the action being performed
     *
     * @return array the elements that can be sorted along with their properties
     * @access public
     */
    function &getSortOrder( $action ) {
        $columnHeaders =& $this->getColumnHeaders( null );
        if ( ! isset( $this->_order ) ) {
            $this->_order = array( );
            $start  = 2;
            $firstElementNotFound = true;
            // does not work for php4...
            //foreach ( $columnHeaders as &$header ) {
            foreach ($columnHeaders as $k => $header) {
                $header =& $columnHeaders[$k];
                if (array_key_exists( 'sort', $header)) {
                    if ( $firstElementNotFound && $header['direction'] != CRM_Utils_Sort::DONTCARE ) {
                        $this->_order[1] =& $header;
                        $firstElementNotFound = false;
                    } else {
                        $this->_order[$start++] =& $header;
                    }
                }
                unset($header);
            }
            if ( $firstElementNotFound ) {
                CRM_Core_Error::fatal( "Could not find a valid sort directional element" );
            }
        }
        return $this->_order;
    }

}

?>