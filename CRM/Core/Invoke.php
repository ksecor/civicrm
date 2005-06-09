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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/I18n.php';

/**
 * Given an argument list, invoke the appropriate CRM function
 * Serves as a wrapper between the UserFrameWork and Core CRM
 */
class CRM_Core_Invoke {

    static function invoke( $args ) {
        if ( $args[0] !== 'civicrm' ) {
            return;
        }

        switch ( $args[1] ) {

        case 'contact': return self::contact( $args );

        case 'admin'  : return self::admin  ( $args );

        case 'history': return self::history( $args );

        case 'group'  : return self::group  ( $args );

        case 'import' : return self::import ( $args );

        default       : return CRM_Utils_System::redirect( CRM_Utils_System::url('civicrm/contact/search', 'reset=1', false) );

        }
    }

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

        if ( $args[2] == 'view' ) {
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            switch ( $args[3] ) {
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
            case 'cd':
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_CD );
                break;
            case 'activity':
                $view =& new CRM_Contact_Page_View( '', CRM_Contact_Page_View::MODE_ACTIVITY );
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

    static function form( $action ) {
        CRM_Utils_System::setUserContext( array( 'civicrm/contact/search', 'civicrm/contact/view' ) );
        $wrapper =& new CRM_Utils_Wrapper( );
        $wrapper->run( 'CRM_Contact_Form_Edit', ts('Contact Page'), $action );
    }
    
    static function history( $args ) {
        if ( $args[2] == 'activity' && $args[3] == 'detail' ) {
            $page =& new CRM_History_Page_Activity('View Activity Details');
            return $page->run( );
        }
    }

    static function admin( $args ) {
        if ( $args[1] !== 'admin' ) {
            return;
        }

        $view = null;
        switch ( $args[2] ) {
        case 'locationType':
            $view =& new CRM_Admin_Page_LocationType(ts('View Location Types'));
            break;
        case 'IMProvider':
            $view =& new CRM_Admin_Page_IMProvider(ts('View Instant Messenger Providers'));
            break;
        case 'mobileProvider':
            $view =& new CRM_Admin_Page_IMProvider(ts('View Mobile Providers'));
            break;
        case 'reltype':
            $view =& new CRM_Admin_Page_RelationshipType(ts('View Relationship Types'));
            break;
        case 'custom':
            if ( $args[3] == 'group' ) {
                if ( $args[4] != 'field' ) {
                    $view =& new CRM_Custom_Page_Group(ts('Custom Data Group'));
                } else {
                    $additionalBreadCrumb = ts('<a href="%1">Custom Data</a>', array(1 => 'civicrm/admin/custom/group'));
                    CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
                    $view =& new CRM_Custom_Page_Field(ts('Custom Data Field'));
                }
            }
        default:
            $view =& new CRM_Admin_Page_Tag(ts('View Tags'));
            break;
        }
        if ( $view ) {
            return $view->run( );
        }

        return CRM_Utils_System::redirect( CRM_Utils_System::url('civicrm/contact/search', 'reset=1', false) );
    }

    static function import( $args ) {
        $controller =& new CRM_Import_Controller(ts('Import Contacts'));
        return $controller->run();
    }

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

}

?>
