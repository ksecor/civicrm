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

class CRM_Utils_Hook {

    /** 
     * This hook will be called on any operation on some core CiviCRM 
     * objects. We will extend the functionality over a period of time 
     * to make it similar to Drupal's user hook, where the external module 
     * can inject and collect form elements and form information into a 
     * Drupal form (specifically the registration page and the account 
     * information page) 
     * 
     * @param string $op         the type of operation being performed 
     * @param string $objectName the name of the object 
     * @param object $id         the object id if available
     * @param array  $params     the parameters used for object creation / editing
     *  
     * @return mixed             based on op. pre-hooks return a boolean or
     *                           an error message which aborts the operation
     * @access public 
     */ 
    static function pre( $op, $objectName, $id, &$params ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::fourArgsHook( $op, $objectName, $objectId, $objectRef, \'civicrm_pre\' );' );  
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
     * @param string $objectName the name of the object 
     * @param int    $objectId   the unique identifier for the object 
     * @param object $objectRef  the reference to the object if available 
     *  
     * @return mixed             based on op. pre-hooks return a boolean or
     *                           an error message which aborts the operation
     * @access public 
     */ 
    static function post( $op, $objectName, $objectId, &$objectRef ) {
        $config =& CRM_Core_Config::singleton( );  
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::fourArgsHook( $op, $objectName, $objectId, $objectRef, \'civicrm_post\' );' );  
    }

    /**
     * This hook retrieves links from other modules and injects it into
     * CiviCRM forms
     *
     * @param string $op         the type of operation being performed
     * @param string $objectName the name of the object
     * @param int    $objectId   the unique identifier for the object 
     *
     * @return array|null        an array of arrays, each element is a tuple consisting of id, url, img, title, weight
     *
     * @access public
     */
    static function links( $op, $objectName, &$objectId ) {
        $config =& CRM_Core_Config::singleton( );  
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::threeArgsHook( $op, $objectName, $objectId, \'civicrm_links\' );' );  
    }

    /** 
     * 
     * @param string $formName the name of the form
     * @param array  &$fields   the POST parameters as filtered by QF
     * @param array  &$files    the FILES parameters as sent in by POST
     * @param array  &$form     the form object
     * @param array  $
     * @return mixed             formRule hooks return a boolean or
     *                           an array of error messages which display a QF Error
     * @access public 
     */ 
    static function validate( $formName, &$fields, &$files, &$form ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::fourArgsHook( $formName, $fields, $files, $form, \'civicrm_validate\' );' );  
    }

    static function custom( $op, $groupID, $entityID, &$params ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::fourArgsHook( $op, $groupID, $entityID, $params, \'civicrm_custom\' );' );  
    }

    static function buildForm( $className, &$form ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::twoArgsHook( $className, $form, \'civicrm_buildForm\' );' );  
    }

    static function postProcess( $className, &$form ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::twoArgsHook( $className, $form, \'civicrm_postProcess\' );' );  
    }

    static function aclClause( $type, &$tables, &$whereTables, &$contactID, &$where ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::fiveArgsHook( $type, $tables, $whereTables, $contactID, $where, \'civicrm_aclClause\' );' );  
    }

}
