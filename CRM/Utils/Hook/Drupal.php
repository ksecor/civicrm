<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Utils_Hook_Drupal {

    static function commonHook( &$arg1, &$arg2, &$arg3, &$arg4, $fnSuffix, $threeArgs = false ) { 
        $result = array( );
        // copied from user_module_invoke
        if (function_exists('module_list')) {
            foreach ( module_list() as $module) { 
                $function = $module . $fnSuffix;
                if ( function_exists( $function ) ) {
                    if ( $threeArgs ) {
                        $fResult = $function( $arg1, $arg2, $arg3, $arg4 );
                    } else {
                        $fResult = $function( $arg1, $arg2, $arg3 );
                    }
                    if ( is_array( $fResult ) ) {
                        $result = array_merge( $result, $fResult );
                    }
                }
            }
        }
        return empty( $result ) ? true : $result;
   }

    /** 
     * This hook will be called on any operation on some core CiviCRM 
     * objects. We will extend the functionality over a period of time 
     * to make it similar to Drupal's user hook, where the external module 
     * can inject and collect form elements and form information into a 
     * Drupal form (specifically the registration page and the account 
     * information page) 
     * 
     * @param string $op         the type of operation being performed 
     * @param string $objectName the BAO class name of the object 
     * @param object $id         the object id if available
     * @param array  $params     the parameters used for object creation / editing
     *  
     * @return mixed             based on op. pre-hooks return a boolean and/or
     *                           an error message which aborts the operation
     * @access public 
     */ 
    static function pre( $op, $objectName, $id, &$params ) {
        return self::commonHook( $op, $objectName, $id, $params, '_civicrm_pre' );
    }

    /** 
     * This hook will be called on any operation on some core CiviCRM 
     * objects. We will extend the functionality over a period of time 
     * to make it similar to Drupal's user hook, where the external module 
     * can inject and collect form elements and form information into a 
     * Drupal form (specifically the registration page and the account 
     * information page) 
     * 
     * @param string $op         the type of operation being performed 
     * @param string $objectName the BAO class name of the object 
     * @param int    $objectId   the unique identifier for the object 

     * @param object $objectRef  the reference to the object if available 
     *  
     * @return mixed             based on op. pre-hooks return a boolean and/or
     *                           an error message which aborts the operation
     * @access public 
     */ 
    static function post( $op, $objectName, $objectId, &$objectRef ) {
        return self::commonHook( $op, $objectName, $id, $params, '_civicrm_post' );
    }

    /**
     * This hook retrieves links from other modules and injects it into
     * CiviCRM forms
     *
     * @param string $op         the type of operation being performed
     * @param string $objectName the name of the object
     * @param int    $objectId   the unique identifier for the object 
     *
     * @return array|null        an array of arrays, each element is a tuple consisting of url, img, title
     *
     * @access public
     */
    static function links( $op, $objectName, $objectId ) {
        return module_invoke_all( 'civicrm_links', $op, $objectName, $objectId ); 
    }

    static function validate( $formName, &$fields, &$files, &$form ) {
        return self::commonHook( $formName, $fields, $files, $form, '_civicrm_validate' );
    }

    static function defaults( $formName, &$defaults, &$form ) {
        return self::commonHook( $formName, $defaults, $form, null, '_civicrm_defaults', true );
    }

    static function custom( $op, $groupID, $entityID, &$params ) {
        return self::commonHook( $op, $groupID, $entityID, $params, '_civicrm_custom' );
    }

}
