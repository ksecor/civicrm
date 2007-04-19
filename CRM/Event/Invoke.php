<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

class CRM_Event_Invoke
{
    static function admin( &$args ) 
    {
        if ( $args[1] !== 'admin' && $args[2] !== 'event' ) {
            return;
        }

        if ( ! CRM_Core_Permission::check( 'administer CiviCRM' ) ||
             ! CRM_Core_Permission::check('access CiviEvent') ) {
            CRM_Core_Error::fatal( ts( 'You do not have access to this page' ) );
        }

        
        $view = null;

        require_once 'CRM/Event/Page/ManageEvent.php';
        $view =& new CRM_Event_Page_ManageEvent(ts('Manage Event'));

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
        if ( $args[1] !== 'event' ) {  
            return;  
        }
       
        $session =& CRM_Core_Session::singleton( );
        $config  =& CRM_Core_Config::singleton ( );

        $secondArg = CRM_Utils_Array::value( 2, $args, '' );

        if ( $secondArg == 'ical' ) {
            require_once 'CRM/Event/Page/ICalendar.php'; 
            $view =& new CRM_Event_Page_ICalendar( );
            return $view->run();
        } else if ( $secondArg == 'register' ) { 
            if ( $config->enableSSL     &&
                 CRM_Core_Invoke::onlySSL( $args ) ) {
                if ( !isset($_SERVER['HTTPS'] ) ) {
                    CRM_Utils_System::redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
                } else {
                    CRM_Utils_System::mapConfigToSSL( );
                }
            }

            $id     = CRM_Utils_Request::retrieve('id', 'Positive', CRM_Core_DAO::$_nullObject, false, null, 'GET');
            $session->pushUserContext( CRM_Utils_System::url("civicrm/event/info", "id={$id}&reset=1" ) );
            
            // also reset the bread crumb
            CRM_Utils_System::resetBreadCrumb( );

            require_once 'CRM/Event/Controller/Registration.php';
            $controller =& new CRM_Event_Controller_Registration(ts('Online Registration'));
            return $controller->run();
        } else if ($secondArg == 'info') {
            // also reset the bread crumb
            CRM_Utils_System::resetBreadCrumb( );

            require_once 'CRM/Event/Page/EventInfo.php';
            $page =& new CRM_Event_Page_EventInfo( );
            return $page->run( );
        }
        
        if ( ! CRM_Core_Permission::check('access CiviEvent') ) {
            CRM_Core_Error::fatal( 'You do not have access to this page.' );
        }

        if ($secondArg == 'search') {
            require_once 'CRM/Event/Controller/Search.php';
            $controller =& new CRM_Event_Controller_Search($title, $mode); 
            $url = 'civicrm/event/search';
            $session->pushUserContext(CRM_Utils_System::url($url, 'force=1')); 
            $controller->set( 'context', 'search' );
            return $controller->run();
        } elseif ($secondArg == 'import') {
            if ( ! CRM_Core_Permission::check('administer CiviCRM') ) {
                CRM_Core_Error::fatal( 'You do not have access to this page' );
            }
            require_once 'CRM/Event/Import/Controller.php';
            $controller =& new CRM_Event_Import_Controller(ts('Import Participants'));
            $session->pushUserContext( CRM_Utils_System::url("civicrm/event", "reset=1" ) );
            return $controller->run();
        } elseif( $secondArg == 'manage' ){
            require_once 'CRM/Event/Page/ManageEvent.php';
            $page =& new CRM_Event_Page_ManageEvent( );
            return $page->run( );
        } else {
            require_once 'CRM/Event/Page/DashBoard.php';
            $view =& new CRM_Event_Page_DashBoard( ts('CiviEvent DashBoard') );
            return $view->run( );
        }
    }
}
?>
