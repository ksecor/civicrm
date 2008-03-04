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

class CRM_Grant_Invoke
{
    static function admin( &$args ) 
    {
        if ( $args[1] !== 'admin' && $args[2] !== 'grant' ) {
            return;
        }

        if ( ! CRM_Core_Permission::check( 'administer CiviCRM' ) ||
             ! CRM_Core_Permission::check('access CiviGrant') ) {
            CRM_Core_Error::fatal( ts( 'You do not have access to this page' ) );
        }

        
        $view = null;

        require_once 'CRM/Event/Page/ManageEvent.php';
        $view =& new CRM_Event_Page_ManageEvent(ts('Manage Grant'));

        if ( $view ) {
            return $view->run( );
        }
        
        return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm' ) );
    }

    /*
     * This function contains the actions for event arguments  
     *  
     * @param $args array this array contains the arguments of the url  
     *  
     * @static  
     * @access public  
     */  
    static function main( &$args )
    {
        if ( $args[1] !== 'grant' ) {  
            return;  
        }

        $session =& CRM_Core_Session::singleton( );
        $config  =& CRM_Core_Config::singleton ( );

        $secondArg = CRM_Utils_Array::value( 2, $args, '' );

        if ( ! CRM_Core_Permission::check('access CiviGrant') ) {
            CRM_Core_Error::fatal( ts('You do not have access to this page.') );
        }

        if ($secondArg == 'search') {
            require_once 'CRM/Grant/Controller/Search.php';
            $controller =& new CRM_Grant_Controller_Search(); 
            $url = 'civicrm/grant/search';
            $session->pushUserContext(CRM_Utils_System::url($url, 'force=1')); 
            $controller->set( 'context', 'search' );
            return $controller->run();
        } elseif ($secondArg == 'import') {
            if ( ! CRM_Core_Permission::check('administer CiviCRM') ) {
                CRM_Core_Error::fatal( ts('You do not have access to this page') );
            }
            require_once 'CRM/Grant/Import/Controller.php';
            $controller =& new CRM_Grant_Import_Controller(ts('Import Grants'));
            $session->pushUserContext( CRM_Utils_System::url("civicrm/grant", "reset=1" ) );
            return $controller->run();
        } else {
            require_once 'CRM/Grant/Page/DashBoard.php';
            $view =& new CRM_Grant_Page_DashBoard( ts('CiviGrant DashBoard') );
            return $view->run( );
        }
    }
}

