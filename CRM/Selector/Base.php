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

class CRM_Selector_Base {

    /**
     * the sort order which is computed from the columnHeaders
     *
     * @var array
     */
    static $_order;

    /**
     * This function gets the attribute for the action that
     * it matches.
     *
     * @param string  match      the action to match against
     * @param string  attribute  the attribute to return ( name, link, linkName, menuName )
     *
     * @return string            the attribute that matches the action if any
     *
     * @access public
     *
     */
    function getActionAttribute( $match, $attribute = 'name' ) {
        $links = $this->getLinks();

        foreach ( $link as $action => $item ) {
            if ( $match & $action ) {
                return $item[$attribute];
            }
        }
        return null;
    }

    /**
     * This is a virtual function, since the $_links array is typically
     * static, we use a virtual function to get the links array. Each 
     * inherited class must redefine this function
     *
     * links is an array of associative arrays. Each element of the array
     * has 4 fields
     *
     * name    : the name of the link
     * link    : the URI to be used for this link, along with any strings that will
     *           be replaced dynamically
     * linkName: (same as name??)
     * menuName: Sometimes the linkName and menuName differ. The linkName could be
     *           "Edit Contact", while the menuName would be the more descriptive
     *           "Edit Contact Details"
     *
     */
    function &getLinks() {
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
    function getTemplateFileName($action)
    {
        return str_replace('_', '/', get_class( $this ) ) . '.tpl';
    }//end of function

    /**
     * compose the module name from the class name
     *
     * @param string $action the action being performed
     *
     * @return string module name
     * @access public
     */
    function getModuleName($action)
    {
        $names = explode( '_', get_class($this) );

        // we compose the module name from the last two tokens
        // we use two tokens to prevent name conflicts
        $last = array_pop( $names );
        $prev = array_pop( $names );
        return ucfirst( $prev ) . '_' . ucfirst( $last );
    }//end of function

    /**
     * getter for the sorting direction for the fields which will be displayed on the form.
     *
     * @param string action the action being performed
     *
     * @return array the elements that can be sorted along with their properties
     * @access public
     */
    function &getSortOrder( $action ) {
        $columnHeaders =& $this->getColumnHeaders( );
        if ( ! isset( self::$_order ) ) {
            self::$_order = array( );
            $start  = 2;
            $firstElementNotFound = true;
            foreach ( $columnHeaders as &$header ) {
                if ( array_key_exists( 'sort', $header ) ) {
                    if ( $firstElementNotFound && $header['direction'] != CRM_Sort::DONTCARE ) {
                        self::$_order[1] =& $header;
                        $firstElementNotFound = false;
                    } else {
                        self::$_order[$start++] =& $header;
                    }
                }
            }
            if ( $firstElementNotFound ) {
                CRM_Error::fatal( "Could not find a valid sort directional element" );
            }
        }
        return self::$_order;
    }

}

?>