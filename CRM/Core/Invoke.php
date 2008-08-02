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
 * Given an argument list, invoke the appropriate CRM function
 * Serves as a wrapper between the UserFrameWork and Core CRM
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Core_Invoke 
{
    /**
     * This is the main function that is called on every click action and based on the argument
     * respective functions are called
     *
     * @param $args array this array contains the arguments of the url 
     * 
     * @static
     * @access public
     */    
    static function invoke( $args ) 
    {
        require_once 'CRM/Core/I18n.php';
        require_once 'CRM/Utils/Wrapper.php';
        require_once 'CRM/Core/Action.php';
        require_once 'CRM/Utils/Request.php';
        require_once 'CRM/Core/Menu.php';
        require_once 'CRM/Core/Component.php';
        require_once 'CRM/Core/Permission.php';

        if ( $args[0] !== 'civicrm' ) {
            return;
        }

        if ( isset($args[1]) and $args[1] == 'menu' and 
             isset($args[2]) and $args[2] == 'rebuild' ) {
            CRM_Core_Menu::store( );
            CRM_Core_Session::setStatus( ts( 'Menu has been rebuilt' ) );
            return CRM_Utils_System::redirect( );
        }

        // first fire up IDS and check for bad stuff
        require_once 'CRM/Core/IDS.php';
        $ids = new CRM_Core_IDS( );
        $ids->check( $args );

        $config =& CRM_Core_Config::singleton( );

        // also initialize the i18n framework
        $i18n   =& CRM_Core_I18n::singleton( );

        if ( $config->userFramework == 'Standalone' ) {
            require_once 'CRM/Core/Session.php';
            $session =& CRM_Core_Session::singleton( ); 
            if ( $session->get('new_install') !== true ) {
                require_once 'CRM/Core/Standalone.php';
                CRM_Core_Standalone::sidebarLeft( );
            } else if ( $args[1] == 'standalone' && $args[2] == 'register' ) {
                CRM_Core_Menu::store( );
            }
        }

        // get the menu items
        $path = implode( '/', $args );
        $item =& CRM_Core_Menu::get( $path );

        if ( $config->userFramework == 'Joomla' && $item ) {
            $config->userFrameworkURLVar = 'task';

            require_once 'CRM/Core/Joomla.php';
            // joomla 1.5RC1 seems to push this in the POST variable, which messes
            // QF and checkboxes
            unset( $_POST['option'] );
            CRM_Core_Joomla::sidebarLeft( );
        }

        // set active Component
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'activeComponent', 'CiviCRM' );
        $template->assign( 'formTpl'        , 'default' );

        if ( $item ) {
            if ( ! array_key_exists( 'page_callback', $item ) ) {
                CRM_Core_Error::debug( 'Bad item', $item );
                CRM_Core_Error::fatal( ts( 'Bad menu record in database' ) );
            }

            // check that we are permissioned to access this page
            if ( ! CRM_Core_Permission::checkMenuItem( $item ) ) {
                CRM_Utils_System::permissionDenied( );
                return;
            }
                
            if ( isset( $item['title'] ) ) {
                CRM_Utils_System::setTitle( $item['title'] );
            }
            if ( isset( $item['breadcrumb'] ) && !isset( $item['is_public'] ) ) {
                CRM_Utils_System::appendBreadCrumb( $item['breadcrumb'] );
            }

            $pageArgs = null;
            if ( CRM_Utils_Array::value('page_arguments', $item) ) {
                $pageArgs = CRM_Core_Menu::getArrayForPathArgs( $item['page_arguments'] );
            }

            $template =& CRM_Core_Smarty::singleton( );
            if ( isset( $item['is_public'] ) &&
                 $item['is_public'] ) {
                $template->assign( 'urlIsPublic', true );
            } else {
                $template->assign( 'urlIsPublic', false );
            }

            if ( isset($item['return_url']) ) {
                $session =& CRM_Core_Session::singleton( );
                $args = CRM_Utils_Array::value( 'return_url_args',
                                                $item,
                                                'reset=1' );
                $session->pushUserContext( CRM_Utils_System::url( $item['return_url'], 
                                                                  $args ) );
            }

            if ( is_array( $item['page_callback'] ) ) {
                $newArgs = explode( '/',
                                    $_GET[$config->userFrameworkURLVar] );
                require_once( str_replace( '_',
                                           DIRECTORY_SEPARATOR,
                                           $item['page_callback'][0] ) . '.php' );
                return call_user_func( $item['page_callback'], 
                                       $newArgs );
            } else if (strstr($item['page_callback'], '_Form')) {
                $wrapper =& new CRM_Utils_Wrapper( );
                return $wrapper->run( $item['page_callback'],
                                      $item['title'], 
                                      $pageArgs );
            } else {
                $newArgs  = explode( '/',
                                     $_GET[$config->userFrameworkURLVar] );
                require_once( str_replace( '_',
                                           DIRECTORY_SEPARATOR,
                                           $item['page_callback'] ) . '.php' );
                $mode = 'null';
                if ( isset( $pageArgs['mode'] ) ) {
                    $mode = $pageArgs['mode'];
                    unset( $pageArgs['mode'] );
                }
                $title = CRM_Utils_Array::value( 'title', $item );
                if (strstr($item['page_callback'], '_Page')) {
                    eval ( '$object =& ' .
                           "new {$item['page_callback']}( '{$title}', $mode );" );
                } else if (strstr($item['page_callback'], '_Controller')) { 
                    $addSequence = 'false';
                    if ( isset( $pageArgs['addSequence'] ) ) {
                        $addSequence = $pageArgs['addSequence'];
                        $addSequence = $addSequence ? 'true' : 'false';
                        unset( $pageArgs['addSequence'] );
                    }
                    eval ( '$object =& ' .
                           "new {$item['page_callback']} ( '{$title}', true, $mode, null, $addSequence );" );
                } else {
                    CRM_Core_Error::fatal( );
                }
                return $object->run( $newArgs, $pageArgs );
            }
        }
        
        CRM_Core_Menu::store( );
        CRM_Core_Session::setStatus( ts( 'Menu has been rebuilt' ) );
        return CRM_Utils_System::redirect( );
    }

    /**
     * This function contains the actions for search arguments
     *
     * @param $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function search( $args ) 
    {
        CRM_Core_Error::fatal( 'Please rebuild your menu array.' );

        $session =& CRM_Core_Session::singleton( );
        $thirdArg = CRM_Utils_Array::value( 3, $args, '' );

        if ( $thirdArg == 'advanced' ) {
            // advanced search
            $mode  = CRM_Core_Action::ADVANCED;
            $title = ts('Advanced Search');
            $url   = 'civicrm/contact/search/advanced';
        } else if ( $thirdArg == 'builder' ) {
            $mode    =  CRM_Core_Action::PROFILE;
            $title   = ts( 'Search Builder' );
            $url   = 'civicrm/contact/search/builder';
        } else if ( $thirdArg == 'custom' ) {
            $mode    =  CRM_Core_Action::COPY;
            $title   = ts( 'Custom Search' );
            $url   = 'civicrm/contact/search/custom';
        } else {
            $mode  = CRM_Core_Action::BASIC;
            $title = ts('Search');
            $url   = 'civicrm/contact/search/basic';
        }

        require_once 'CRM/Contact/Controller/Search.php';
        $controller =& new CRM_Contact_Controller_Search($title, true, $mode);

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
    static function form( $action, $contact_type, $contact_sub_type ) 
    {
        CRM_Utils_System::setUserContext( array( 'civicrm/contact/search/basic', 'civicrm/contact/view' ) );
        $wrapper =& new CRM_Utils_Wrapper( );
        
        require_once 'CRM/Core/Component.php';
        $properties =& CRM_Core_Component::contactSubTypeProperties( $contact_sub_type, 'Edit' );
        if( $properties ) {
            $wrapper->run( $properties['class'], ts('New %1', array(1 => $contact_sub_type)), $action, true );
        } else {
            $wrapper->run( 'CRM_Contact_Form_Edit', ts( 'New Contact' ), $action, true );
        }
    }
    
    /** 
     * This function contains the actions for profile arguments 
     * 
     * @param $args array this array contains the arguments of the url 
     * 
     * @static 
     * @access public 
     */ 
    static function profile( $args ) 
    { 
        if ( $args[1] !== 'profile' ) { 
            return; 
        } 

        $secondArg = CRM_Utils_Array::value( 2, $args, '' ); 

        if ($secondArg == 'map' ) {

            $controller =& new CRM_Core_Controller_Simple( 'CRM_Contact_Form_Task_Map',
                                                           ts('Map Contact'),
                                                           null, false, false, true );

            $profileGID  = CRM_Utils_Request::retrieve( 'gid', 'Integer',
                                                       $controller,
                                                       true );
            $profileView = CRM_Utils_Request::retrieve( 'pv', 'Integer',
                                                        $controller,
                                                        false );
            // set the userContext stack
            $session =& CRM_Core_Session::singleton();
            if ( $profileView ) {
                $session->pushUserContext( CRM_Utils_System::url( 'civicrm/profile/view' ) );
            } else {
                $session->pushUserContext( CRM_Utils_System::url( 'civicrm/profile' ) );
            }

            $controller->set( 'profileGID', $profileGID );
            $controller->process( );
            return $controller->run( );
        }

        if ( $secondArg == 'edit' || $secondArg == 'create' ) {
            // set the userContext stack
            $session =& CRM_Core_Session::singleton(); 
            $session->pushUserContext( CRM_Utils_System::url('civicrm/profile', 'reset=1' ) ); 

            $buttonType = CRM_Utils_Array::value('_qf_Edit_cancel',$_POST);
            if ( $buttonType == 'Cancel' ) {
                $cancelURL = CRM_Utils_Request::retrieve('cancelURL',
                                                         'String',
                                                         CRM_Core_DAO::$_nullObject,
                                                         false,
                                                         null,
                                                         $_POST );
                if ( $cancelURL ) {
                    CRM_Utils_System::redirect( $cancelURL );
                }
            }

            if ( $secondArg == 'edit' ) {
                $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Edit',
                                                               ts('Create Profile'),
                                                               CRM_Core_Action::UPDATE,
                                                               false, false, true );
                $controller->set( 'edit', 1 );
                $controller->process( );
                return $controller->run( );
            } else {
                $wrapper =& new CRM_Utils_Wrapper( ); 
                return $wrapper->run( 'CRM_Profile_Form_Edit',
                                      ts( 'Create Profile' ),
                                      CRM_Core_Action::ADD,
                                      false, true );
            } 
        } 

        require_once 'CRM/Profile/Page/Listings.php';
        $page =& new CRM_Profile_Page_Listings( );
        return $page->run( );
    }

}


