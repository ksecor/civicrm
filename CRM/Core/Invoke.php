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
 * Given an argument list, invoke the appropriate CRM function
 * Serves as a wrapper between the UserFrameWork and Core CRM
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/I18n.php';
require_once 'CRM/Contact/Controller/Search.php';

class CRM_Core_Invoke {
    
    /**
     * This is the main function that is called on every click action and based on the argument
     * respective functions are called
     *
     * @param $args array this array contains the arguments of the url 
     * 
     * @static
     * @access public
     */    
    static function invoke( $args ) {

        //CRM_Core_Error::le_method();

        if ( $args[0] !== 'civicrm' ) {
            return;
        }

        $config =& CRM_Core_Config::singleton( );
        if ( $config->userFramework == 'Mambo' ) {
            CRM_Core_Mambo::sidebarLeft( );
        }

        CRM_Utils_Menu::createLocalTasks( $_GET[$config->userFrameworkURLVar] );

        switch ( $args[1] ) {

        case 'contact': return self::contact( $args );

        case 'admin'  : return self::admin  ( $args );

        case 'history': return self::history( $args );

        case 'group'  : return self::group  ( $args );

        case 'import' : return self::import ( $args );
       
        case 'export' : return self::export ( $args );

        case 'activity' : return self::activity ( $args );

        default       : return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contact/search', 'reset=1' ) );

        }
    }

    /**
     * This function contains the actions for arg[1] = contact
     *
     * @param $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function contact( $args ) {
        if ( $args[1] !== 'contact' ) {
            return;
        }

        if ( substr( $args[2], 0, 3 ) == 'add' ) {
            return self::form( CRM_Core_Action::ADD );
        }

        $additionalBreadCrumb = ts('<a href="%1">Search Results</a>', array(1 => 'civicrm/contact/search?force=1'));
        if ( $args[2] == 'edit' ) {
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            return self::form( CRM_Core_Action::UPDATE );
        }

        if ( $args[2] == 'email' ) {
            // set the userContext stack
            $session =& CRM_Core_Session::singleton();
            $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view/activity', 'action=browse' ) );

            $wrapper =& new CRM_Utils_Wrapper( );
            $wrapper->run( 'CRM_Contact_Form_Task_Email', ts('Email a Contact'),  null );
        }

        if ( $args[2] == 'view' ) {

            // need to add tabs menu local task for contact's customdata
            //CRM_Core_Error::debug_log_message('add voter info and edu qual tabs pls');
            //CRM_Utils_Menu::createLocalTasks('civicrm/contact/view/voter_info');
            //CRM_Utils_Menu::createLocalTasks('civicrm/contact/view/edu_qual');
            //             $m1 = array(
            //                         'path'    => 'civicrm/contact/view/voter',
            //                         'title'   => ts('Voter Info'),
            //                         'type'    => CRM_Utils_Menu::CALLBACK,
            //                         'crmType' => CRM_Utils_Menu::LOCAL_TASK,
            //                         'weight'  => 7,
            //                         );
            //             CRM_Utils_Menu::add($m1);


            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            $thirdArg = CRM_Utils_Array::value( 3, $args, '' );


            switch ( $thirdArg ) {
            case 'note':
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_NOTE );
                break;
            case 'rel':
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_REL );
                break;
            case 'group':
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_GROUP );
                break;
            case 'tag':
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_TAG );
                break;
                //case 'voter':
                //CRM_Core_Error::debug_log_message('voter cd found');
            case 'cd':
                //CRM_Core_Error::debug_log_message('cd found');
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_CD );
                break;
            case 'activity':
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_ACTIVITY );
                break;
            case 'call':
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_CALL );
                break;
            case 'meeting':
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_MEETING );
                break;
            default:
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_NONE );
                break;
            }
            return $view->run( );
        }

        if ( $args[2] == 'search' ) {
            return self::search( $args );
        }

        return CRM_Utils_System::redirect( CRM_Utils_System::url('civicrm/contact/search', 'reset=1', false) );
    }


    /**
     * This function contains the actions for search arguments
     *
     * @param $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function search( $args ) {
       
        $thirdArg = CRM_Utils_Array::value( 3, $args, '' );

        if ( $thirdArg == 'saved' ) {
            $page =& new CRM_Contact_Page_SavedSearch( '', CRM_Contact_Page_View::MODE_NONE );
            return $page->run( );
        }

        if ( $thirdArg == 'advanced' ) {
            // advanced search
            $mode  = CRM_Core_Action::ADVANCED;
            $title = ts('Advanced Search');
            $url   = 'civicrm/contact/search/advanced';
        } else {
            $mode  = CRM_Core_Action::BASIC;
            $title = ts('Search');
            $url   = 'civicrm/contact/search';
        }
        $controller =& new CRM_Contact_Controller_Search($title, $mode);
        $session =& CRM_Core_Session::singleton( );
        $session->pushUserContext(CRM_Utils_System::url($url, 'force=1'));
        return $controller->run();
    }
    
    /**
     * This function contains the default action
     *
     * @param $action 
     *
     * @static
     * @access public
     */
    static function form( $action ) {
        CRM_Utils_System::setUserContext( array( 'civicrm/contact/search', 'civicrm/contact/view' ) );
        $wrapper =& new CRM_Utils_Wrapper( );
        $wrapper->run( 'CRM_Contact_Form_Edit', ts('Contact Page'), $action );
    }
    
    /**
     * This function contains the actions for history arguments
     *
     * @param $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function history( $args ) {
        if ( $args[2] == 'activity' && $args[3] == 'detail' ) {
            $page =& new CRM_History_Page_Activity('View Activity Details');
            return $page->run( );
        }

        if ($args[2] == 'email') {
            $page =& new CRM_History_Page_Email('View Email Details');
            return $page->run( );
        }
    }


    /**
     * This function contains the actions for admin arguments
     *
     * @param $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function admin( $args ) {
        if ( $args[1] !== 'admin' ) {
            return;
        }

        $view = null;
        switch ( CRM_Utils_Array::value( 2, $args, '' ) ) {
        case 'locationType':
            $view =& new CRM_Admin_Page_LocationType(ts('View Location Types'));
            break;
        case 'IMProvider':
            $view =& new CRM_Admin_Page_IMProvider(ts('View Instant Messenger Providers'));
            break;
        case 'mobileProvider':
            $view =& new CRM_Admin_Page_MobileProvider(ts('View Mobile Providers'));
            break;
        case 'reltype':
            $view =& new CRM_Admin_Page_RelationshipType(ts('View Relationship Types'));
            break;
        case 'custom':
            if ( $args[3] == 'group' ) {
                if ( $args[4] != 'field' ) {
                    $view =& new CRM_Custom_Page_Group(ts('Custom Data Group'));
                } else {
                    if ( $args[5] != 'option' ) {
                        $view =& new CRM_Custom_Page_Field(ts('Custom Data Field'));
                    } else {
                        $view =& new CRM_Custom_Page_Option(ts('Custom Data Field'));
                        $additionalBreadCrumb = '<a href="civicrm/admin/custom/group/field">' . ts('Custom Data Field') . '</a>';
                        CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
                    }
                }
            }
            break;
        case 'uf':
            if ( $args[3] == 'group' ) {
                if ( $args[4] != 'field' ) {
                    $view =& new CRM_UF_Page_Group(ts('CiviCRM Profile Group'));
                } else {
                   $view =& new CRM_UF_Page_Field(ts('CiviCRM Profile Field'));
                }
            }
            break;
        default:
            $view =& new CRM_Admin_Page_Tag(ts('View Tags'));
            break;
        }
        if ( $view ) {
            return $view->run( );
        }

        return CRM_Utils_System::redirect( CRM_Utils_System::url('civicrm/contact/search', 'reset=1', false) );
    }

    /**
     * This function contains the action for import arguments
     *
     * @params $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function import( $args ) {
        $controller =& new CRM_Import_Controller(ts('Import Contacts'));
        return $controller->run();
    }


    /**
     * This function contains the actions for group arguments
     *
     * @params $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function group( $args ) {
        if ( $args[1] !== 'group' ) {
            return;
        }

        switch ( CRM_Utils_Array::value( 2, $args ) ) {
        case 'add':
            $controller =& new CRM_Group_Controller(ts('Groups'), CRM_Core_Action::ADD);
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(CRM_Utils_System::url('civicrm/group', 'reset=1'));
            return $controller->run();

        case 'search':
            return self::search( $args );

        default:
            $view =& new CRM_Group_Page_Group(ts('View Groups'));
            return $view->run();
        }
    }

    static function export( $args ) {
        // FIXME:  2005-06-22 15:17:33 by Brian McFee <brmcfee@gmail.com>
        // This function is a dirty, dirty hack.  It should live in its own
        // file.
        $session =& CRM_Core_Session::singleton();
        $type = $_GET['type'];
        
        if ($type == 1) {
            $varName = 'errors';
            $saveFileName = 'Import_Errors.csv';
        } else if ($type == 2) {
            $varName = 'conflicts';
            $saveFileName = 'Import_Conflicts.csv';
        } else {
            /* FIXME we should have an error here */
            return;
        }
        
        $fileName = $session->get($varName . 'FileName', 
                                    'CRM_Import_Controller');
                                    
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Length: ' . filesize($fileName) );
        header('Content-Disposition: attachment; filename=' . $saveFileName);

        readfile($fileName);
        
        exit();
    }

}

?>
