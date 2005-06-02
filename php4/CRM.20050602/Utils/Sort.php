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
 * Base class to provide generic sort functionality. Note that some ideas
 * have been borrowed from the drupal tablesort.inc code. Also note that
 * since the Pager and Sort class are similar, do match the function names
 * if introducing additional functionality
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */


define( 'CRM_UTILS_SORT_ASCENDING',1);
define( 'CRM_UTILS_SORT_DESCENDING',2);
define( 'CRM_UTILS_SORT_DONTCARE',4);
define( 'CRM_UTILS_SORT_SORT_ID','crmSID');
define( 'CRM_UTILS_SORT_SORT_DIRECTION','crmSortDirection');

require_once 'CRM/Utils/System.php';
class CRM_Utils_Sort {

    /**
     * constants to determine what direction each variable
     * is to be sorted
     *
     * @var int
     */
    
           
          
            

        /**
         * the name for the sort GET/POST param
         *
         * @var string
         */
                 
          /**
     * name of the sort function. Used to isolate session variables
     * @var string
     */
    var $_name;

    /**
     * array of variables that influence the query
     *
     * @var array
     */
    var $_vars;

    /**
     * the newly formulated base url to be used as links
     * for various table elements
     *
     * @var string
     */
    var $_link;

    /**
     * what's the name of the sort variable in a REQUEST
     *
     * @var string
     */
    var $_urlVar;

    /**
     * What variable are we currently sorting on
     *
     * @var string
     */
    var $_currentSortID;

    /**
     * What direction are we sorting on
     *
     * @var string
     */
    var $_currentSortDirection;


    /**
     * The output generated for the current form
     *
     * @var array
     */
    var $_response;

    /**
     * The constructor takes an assoc array
     *   key names of variable (which should be the same as the column name)
     *   value: ascending or descending
     *
     * @param mixed $vars - assoc array as described above
     *
     * @return void
     *
     * @access public
     *
     */
    function CRM_Utils_Sort( &$vars, $defaultSortOrder = null ) {
        $this->_vars      = array( );
        $this->_response  = array();

        foreach ( $vars as $weight => $value ) {
            $this->_vars[$weight] = array(
                                          'name'      => $value['sort'],
                                          'direction' => $value['direction'],
                                          'title'     => $value['name'],
                                          );
        }
    
        $this->_currentSortID        = 1;
        $this->_currentSortDirection = $this->_vars[$this->_currentSortID]['direction'];
        $this->_urlVar               = CRM_UTILS_SORT_SORT_ID;
        $this->_link                 = CRM_Utils_System::makeUrl( $this->_urlVar );

        $this->initialize( $defaultSortOrder );
    }

    function orderBy( ) {
        if ( $this->_vars[$this->_currentSortID]['direction'] == CRM_UTILS_SORT_ASCENDING|| 
             $this->_vars[$this->_currentSortID]['direction'] == CRM_UTILS_SORT_DONTCARE) {
            return $this->_vars[$this->_currentSortID]['name'] . ' asc';
        } else {
            return $this->_vars[$this->_currentSortID]['name'] . ' desc';
        }
    }

    function sortIDValue( $index, $dir ) {
        return ( $dir == CRM_UTILS_SORT_DESCENDING) ? $index . '_d' : $index . '_u';
    }
  
    function getSortID( $defaultSortOrder ) {
        $url = $_GET[CRM_UTILS_SORT_SORT_ID] ? $_GET[CRM_UTILS_SORT_SORT_ID] : $defaultSortOrder;

        if ( empty( $url ) ) {
            return;
        }

        list( $current, $direction ) = explode( '_', $url );
      
        if ( $direction == 'u' ) {
            $direction = CRM_UTILS_SORT_ASCENDING;
        } else if  ( $direction == 'd' ) {
            $direction = CRM_UTILS_SORT_DESCENDING;
        } else {
            $direction = CRM_UTILS_SORT_DONTCARE;
        }

        $this->_currentSortID               = $current;
        $this->_currentSortDirection        = $direction;
        $this->_vars[$current]['direction'] = $direction;
    }

    function initialize( $defaultSortOrder ) {
        $this->getSortID( $defaultSortOrder );

        $this->_response = array( );

        $current = $this->_currentSortID;
        foreach ( $this->_vars as $index => $item ) {
            $name = $item['name'];
            $this->_response[$name] = array();

            $newDirection = ( $item['direction'] == CRM_UTILS_SORT_ASCENDING) ? CRM_UTILS_SORT_DESCENDING: CRM_UTILS_SORT_ASCENDING;

            if ( $current == $index ) {
                if ( $item['direction'] == CRM_UTILS_SORT_ASCENDING) {
                    $class = 'sort-ascending';
                } else {
                    $class = 'sort-descending';
                }
            } else {
                $class     = 'sort-none';
            }

            $this->_response[$name]['link'] = '<a href="' . $this->_link . $this->sortIDValue( $index, $newDirection ) . '" class=' . $class . '>' . $item['title'] . '</a>';
        }
    }

    /**
     * getter for currentSortID
     *
     * @return int
     * @acccess public
     */
    function getCurrentSortID( ) {
        return $this->_currentSortID;
    }

    /**
     * getter for currentSortDirection
     *
     * @return int
     * @acccess public
     */
    function getCurrentSortDirection( ) {
        return $this->_currentSortDirection;
    }
}

?>