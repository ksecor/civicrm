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
     * This hook is called before a db write on some core objects.
     * This hook does not allow the abort of the operation 
     * 
     * @param string $op         the type of operation being performed 
     * @param string $objectName the name of the object 
     * @param object $id         the object id if available
     * @param array  $params     the parameters used for object creation / editing
     *  
     * @return null the return value is ignored
     * @access public 
     */ 
    static function pre( $op, $objectName, $id, &$params ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 4, $op, $objectName, $id, $params, $op, \'civicrm_pre\' );' );  
    }

    /** 
     * This hook is called after a db write on some core objects.
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
                  '::invoke( 4, $op, $objectName, $objectId, $objectRef, $op, \'civicrm_post\' );' );  
    }

    /**
     * This hook retrieves links from other modules and injects it into
     * the view contact tabs
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
                  '::invoke( 3, $op, $objectName, $objectId, $op, $op, \'civicrm_links\' );' );  
    }

    /** 
     * This hook is invoked when building a CiviCRM form. This hook should also
     * be used to set the default values of a form element
     * 
     * @param string $formName the name of the form
     * @param object $form     reference to the form object
     *
     * @return null the return value is ignored
     */
    static function buildForm( $formName, &$form ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 2, $formName, $form, $formName, $formName, $formName, \'civicrm_buildForm\' );' );  
    }

    /** 
     * This hook is invoked when a CiviCRM form is submitted. If the module has injected
     * any form elements, this hook should save the values in the database
     * 
     * @param string $formName the name of the form
     * @param object $form     reference to the form object
     *
     * @return null the return value is ignored
     */
    static function postProcess( $formName, &$form ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 2, $formName, $form, $formName, $formName, $formName, \'civicrm_postProcess\' );' );  
    }

    /** 
     * This hook is invoked during all CiviCRM form validation. An array of errors
     * detected is returned. Else we assume validation succeeded.
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
                  '::invoke( 4, $formName, $fields, $files, $form, $formName, \'civicrm_validate\' );' );  
    }

    /** 
     * This hook is called before a db write on a custom table
     * 
     * @param string $op         the type of operation being performed 
     * @param string $groupID    the custom group ID
     * @param object $entityID   the entityID of the row in the custom table
     * @param array  $params     the parameters that were sent into the calling function
     *  
     * @return null the return value is ignored
     * @access public 
     */ 
    static function custom( $op, $groupID, $entityID, &$params ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 4, $op, $groupID, $entityID, $params, $op, \'civicrm_custom\' );' );  
    }

    /** 
     * This hook is called when composing the ACL where clause to restrict
     * visibility of contacts to the logged in user
     * 
     * @param int $type the type of permission needed
     * @param array $tables (reference ) add the tables that are needed for the select clause
     * @param array $whereTables (reference ) add the tables that are needed for the where clause
     * @param int    $contactID the contactID for whom the check is made
     * @param string $where the currrent where clause 
     *  
     * @return null the return value is ignored
     * @access public 
     */
    static function aclWhereClause( $type, &$tables, &$whereTables, &$contactID, &$where ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 5, $type, $tables, $whereTables, $contactID, $where, \'civicrm_aclWhereClause\' );' );  
    }

    /** 
     * This hook is called when composing the ACL where clause to restrict
     * visibility of contacts to the logged in user
     * 
     * @param int    $type          the type of permission needed
     * @param int    $contactID     the contactID for whom the check is made
     * @param string $tableName     the tableName which is being permissioned
     * @param array  $allGroups     the set of all the objects for the above table
     * @param array  $currentGroups the set of objects that are currently permissioned for this contact
     *  
     * @return null the return value is ignored
     * @access public 
     */
    static function aclGroup( $type, $contactID, $tableName, &$allGroups, &$currentGroups ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 5, $type, $contactID, $tableName, $allGroups, $currentGroups, \'civicrm_aclGroup\' );' );  
    }

    /** 
     * This hook is called when building the menu table
     * 
     * @param array $files The current set of files to process
     *  
     * @return null the return value is ignored
     * @access public 
     */
    static function xmlMenu( &$files ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        $null =& CRM_Core_DAO::$_nullObject;
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 1, $files, $null, $null, $null, $null, \'civicrm_xmlMenu\' );' );
    }

    /** 
     * This hook is called when rendering the dashboard (q=civicrm/dashboard)
     * 
     * @param int $contactID - the contactID for whom the dashboard is being rendered
     *  
     * @return string the html snippet to include in the dashboard
     * @access public 
     */
    static function dashboard( $contactID ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        $null =& CRM_Core_DAO::$_nullObject;
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 1, $contactID, $null, $null, $null, $null, \'civicrm_dashboard\' );' );
    }

    /** 
     * This hook is called when building the amount structure for a Contribution or Event Page
     * 
     * @param int    $pageType - is this a contribution or event page
     * @param object $form     - reference to the form object
     * @param array  $amount   - the amount structure to be displayed
     *  
     * @return null
     * @access public 
     */
    static function buildAmount( $pageType, &$form, &$amount ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        $null =& CRM_Core_DAO::$_nullObject;
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 3, $pageType, $form, $amount, $null, $null, \'civicrm_buildAmount\' );' );
    }

    /** 
     * This hook is called when rendering the tabs for a contact (q=civicrm/contact/view)c
     * 
     * @param array $tabs      - the array of tabs that will be displayed
     * @param int   $contactID - the contactID for whom the dashboard is being rendered
     *  
     * @return null
     * @access public 
     */
    static function tabs( &$tabs, $contactID ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        $null =& CRM_Core_DAO::$_nullObject;
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 2, $tabs, $contactID, $null, $null, $null, \'civicrm_tabs\' );' );
    }

    /** 
     * This hook is called when sending an email / printing labels
     * 
     * @param array $tokens    - the list of tokens that can be used for the contact
     *  
     * @return null
     * @access public 
     */
    static function tokens( &$tokens ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        $null =& CRM_Core_DAO::$_nullObject;
        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 1, $tokens, $null, $null, $null, $null, \'civicrm_tokens\' );' );
    }

    /** 
     * This hook is called when sending an email / printing labels to get the values for all the 
     * tokens returned by the 'tokens' hook
     * 
     * @param array       $details    - the array to store the token values indexed by contactIDs (unless it a single)
     * @param int / array $contactIDs - an array of contactIDs, in some situations we also send a single contactID. 
     *  
     * @return null
     * @access public 
     */
    static function tokenValues( &$details, &$contactIDs ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        $null =& CRM_Core_DAO::$_nullObject;

        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 2, $details, $contactIDs, $null, $null, $null, \'civicrm_tokenValues\' );' );
    }

    /** 
     * This hook is called before a CiviCRM Page is rendered. You can use this hook to insert smarty variables
     * in a  template
     * 
     * @param object $page - the page that will be rendered
     *  
     * @return null
     * @access public 
     */
    static function pageRun( &$page ) {
        $config =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
        $null =& CRM_Core_DAO::$_nullObject;

        return   
            eval( 'return ' .
                  $config->userHookClass .
                  '::invoke( 1, $page, $null, $null, $null, $null, \'civicrm_pageRun\' );' );
    }

}
