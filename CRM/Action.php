<?php
/**
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
 * The core concept of the system is an action performed on an
 * object. Typically this will be a "data model" object as 
 * specified in the API specs. We attempt to keep the number
 * and type of actions consistent and similar across all 
 * objects (thus providing both reuse and standards)
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Array.class.php';

class CRM_Action {

    /**
     * Different possible actions are defined here
     *
     * @var const
     *
     * @access public
     */
    const
        CREATE        =     1,
        VIEW          =     2,
        UPDATE        =     4,
        DELETE        =     8,
        EXPORT        =    16;
  
    /**
     * map the action names to the relevant constant. We perform
     * bit manipulation operations so we can perform multiple
     * actions on the same object if needed
     *
     * @var array  _names  tupe of variable name to action constant
     *
     * @access private
     * @static
     *
     */
    static $_names = array(
                           'create'        => CRM_Action::CREATE,
                           'view'          => CRM_Action::VIEW  ,
                           'update'        => CRM_Action::UPDATE,
                           'delete'        => CRM_Action::DELETE,
                           'export'        => CRM_Action::EXPORT,
                           );

    /**
     *
     * returns the actions to be performed for the page being 
     * processed. Uses the $_GET super global directly. Should
     * we be using a Request Object instead? This seems fine since
     * we actually do exact mapping in a fixed pre determined array
     * ($_names)
     *
     * @param string kwd     the name of the GET parameter
     * @param string default the default action for this page is none exists
     *
     * @return int the action mask corresponding to the GET param
     * @access public
     * @static
     *
     */
    static function get( $kwd = 'action', $default = null ) {
        $urlVar = CRM_Array::value( $kwd, $_GET );
        if ( ! isset( $urlVar ) ) {
            $urlVar = $default;
        }
    
        $action = 0;
        if ( $urlVar ) {
            $items = explode( '|', $urlVar );
            $action = CRM_Action::map( $items );
        }
        return $action;
    }

    /**
     * Given a string or an array of strings, determine the bitmask
     * for this set of actions
     *
     * @param mixed either a single string or an array of strings
     *
     * @return int the action mask corresponding to the input args
     * @access public
     * @static
     *
     */
    static function map( $item ) {
        $mask = 0;

        if ( is_array( $item ) ) {
            foreach ( $item as $it ) {
                $mask |= CRM_Action::mapItem( $it );
            }
            return $mask;
        } else {
            return CRM_Action::mapItem( $item );
        }
    }

    /**
     * Given a string determine the bitmask for this specific string
     *
     * @param string the input action to process
     *
     * @return int the action mask corresponding to the input string
     * @access public
     * @static
     *
     */
    static function mapItem( $item ) {
        $mask = CRM_Array::value( trim( $item ), CRM_Action::$_names );
        return $mask ? $mask : 0;
    }

    /**
     *
     * Given an action mask, find the corresponding description
     *
     * @param int the action mask
     *
     * @return string the corresponding action description
     * @access public
     * @static
     *
     */
    static function description( $mask ) {
        static $_description;
        if ( ! isset( $description ) ) {
            $description = array_flip( CRM_Action::$_names );
        }
        
        $desc = CRM_Array::value( $mask, $description );
        return $desc ? $desc : 'NO DESCRIPTION SET';
    }

}

?>
