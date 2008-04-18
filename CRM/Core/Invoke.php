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

        if ( $args[1] == 'menu' && 
             $args[2] == 'rebuild' ) {
            CRM_Core_Menu::store( );
            CRM_Core_Session::setStatus( ts( 'Menu has been rebuilt' ) );
            return CRM_Utils_System::redirect( );
        }

        $config =& CRM_Core_Config::singleton( );

        // also initialize the i18n framework
        $i18n   =& CRM_Core_I18n::singleton( );

        if ( $config->userFramework == 'Joomla' ) {
            $config->userFrameworkURLVar = 'task';

            require_once 'CRM/Core/Joomla.php';
            // joomla 1.5RC1 seems to push this in the POST variable, which messes
            // QF and checkboxes
            unset( $_POST['option'] );
            CRM_Core_Joomla::sidebarLeft( );
        } else if ( $config->userFramework == 'Standalone' ) {
            require_once 'CRM/Core/Session.php';
            $session =& CRM_Core_Session::singleton( ); 
            if ( $session->get('new_install') !== true ) {
                require_once 'CRM/Core/Standalone.php';
                CRM_Core_Standalone::sidebarLeft( );
            }
        }

        // set active Component
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'activeComponent', 'CiviCRM' );
        $template->assign( 'formTpl'        , 'default' );

        while ( ! empty( $args ) ) {
            // get the menu items
            $path = implode( '/', $args );
            $item =& CRM_Core_Menu::get( $path );

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
                
                CRM_Utils_System::setTitle( $item['title'] );
                CRM_Utils_System::appendBreadCrumb( $item['breadcrumb'] );
                
                if ( is_array( $item['page_callback'] ) ) {
                    // Added since url is not refreshed. Should be
                    // removed when all the methods have been removed from
                    // this invoke file. 
                    $newArgs = explode( '/', $_GET['q'] );
                    require_once( str_replace( '_',
                                               DIRECTORY_SEPARATOR,
                                               $item['page_callback'][0] ) . '.php' );
                    call_user_func( $item['page_callback'],
                                    $newArgs );
                    return;
                } else if (strstr($item['page_callback'], '_Form')) {
                    $wrapper =& new CRM_Utils_Wrapper( );

                    if ( CRM_Utils_Array::value('page_arguments', $item) ) {
                        $pageArgs = CRM_Core_Menu::getArrayForPathArgs( $item['page_arguments'] );
                    }
                    
                    return $wrapper->run( $item['page_callback'],
                                          $item['title'], 
                                          $pageArgs );
                } else {
                    // page and controller have the same style
                    $newArgs = explode( '/', $_GET['q'] );
                    require_once( str_replace( '_',
                                               DIRECTORY_SEPARATOR,
                                               $item['page_callback'] ) . '.php' );
                    eval( '$page =& new ' .
                          $item['page_callback'] .
                          ' ( );' );
                    return $page->run( $newArgs, CRM_Utils_Array::value('page_arguments', $item, null) );
                }
            }
            array_pop( $args );
        }

        CRM_Core_Error::fatal( 'hey, how did we land up here?' );
        CRM_Utils_System::redirect( );
        return;
    }

    /**
     * This function contains the actions for arg[1] = contact
     *
     * @param $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function contact( $args ) 
    {

        $session =& CRM_Core_Session::singleton();

        $isAdvanced = $session->get('isAdvanced');
        if ( $isAdvanced == '1' ) {
            $breadCrumbPath = CRM_Utils_System::url( 'civicrm/contact/search/advanced', 'force=1' );
        } else if ( $isAdvanced == '2' ) {
            $breadCrumbPath = CRM_Utils_System::url( 'civicrm/contact/search/builder', 'force=1' );
        } else if ( $isAdvanced == '3' ) {
            $breadCrumbPath = CRM_Utils_System::url( 'civicrm/contact/search/custom', 'force=1' );
        } else {
            $breadCrumbPath = CRM_Utils_System::url( 'civicrm/contact/search/basic', 'force=1' );
        }
        
        if ( $args[1] !== 'contact' ) {
            return;
        }

        if ( $args[2] == 'add' ) {
            if ( ! CRM_Core_Permission::check('add contacts') ) {
                CRM_Core_Error::fatal( 'You do not have access to this page' );
            }

            $contactType    = CRM_Utils_Request::retrieve('ct','String', CRM_Core_DAO::$_nullObject,false,null,'GET');
            $contactSubType = CRM_Utils_Request::retrieve('cst','String', CRM_Core_DAO::$_nullObject,false,null,'GET');
            return self::form( CRM_Core_Action::ADD, $contactType, $contactSubType );
        }
        
        if ($args[2] == 'view') {
            $contactId = CRM_Utils_Request::retrieve( 'cid' , 'Positive', $this );
            $path = CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $contactId );
            CRM_Utils_System::appendBreadCrumb( ts('View Contact'), $path );
            CRM_Utils_System::appendBreadCrumb( ts('Search Results'), $breadCrumbPath );
            $thirdArg = CRM_Utils_Array::value( 3, $args, '' );
            $fourthArg = CRM_Utils_Array::value(4, $args, 0);

            if ( ! $thirdArg || ($thirdArg == 'activity') ) {
                //build other activity select
                $controller =& new CRM_Core_Controller_Simple( 'CRM_Activity_Form_ActivityLinks',
                                                               ts('Activity Links'), null );
                $controller->setEmbedded( true );
                $controller->run( );
            }
            
            switch ( $thirdArg ) {

            default:
                $id = CRM_Utils_Request::retrieve( 'cid', 'Positive', CRM_Core_DAO::$_nullObject ); 
                if ( ! $id ) {
                    $id = $session->get( 'view.id' );
                    if ( ! $id ) {
                        CRM_Core_Error::statusBounce( ts( 'Could not retrieve a valid contact' ) );
                    }
                }

                $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid=$id" ) );
                
                $contact_sub_type = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'contact_sub_type' );
                $contact_type     = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'contact_type'     );
                
                $properties =& CRM_Core_Component::contactSubTypeProperties( $contact_sub_type, 'View' );

                if( $properties ) {
                    require_once $properties['file'];
                    eval( '$view =& new ' . $properties['class'] . '( );' );
                } else {
                    require_once 'CRM/Contact/Page/View/Tabbed.php';
                    $view =& new CRM_Contact_Page_View_Tabbed( );
                }
                break;
            }
            if ( isset( $_GET['snippet'] ) && $_GET['snippet'] ) {
                $view->setPrint( CRM_Core_Smarty::PRINT_SNIPPET );
            }
            return $view->run( );
        }
        
        return CRM_Utils_System::redirect( );
    }

    /**
     * This function for CiviCRM logout
     *
     *
     * @static
     * @access public
     *
     */

    static function logout($args)
    {
        session_destroy();
        header("Location:index.php");
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
        } else if ( $thirdArg == 'simple' ) {
            // set the userContext stack
            $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/search/simple' ) );

            $wrapper =& new CRM_Utils_Wrapper( );
            return $wrapper->run( 'CRM_Contact_Form_Search_Simple', ts('Simple Search'),  null );
        } else {
            $mode  = CRM_Core_Action::BASIC;
            $title = ts('Search');
            $url   = 'civicrm/contact/search/basic';
        }

        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          CRM_Core_DAO::$_nullObject, false, 0, 'GET');
        if ( $id ) {
            $session->set('id', $id);
        }

        require_once 'CRM/Contact/Controller/Search.php';
        $controller =& new CRM_Contact_Controller_Search($title, $mode);

        if ( isset( $_GET['snippet'] ) && $_GET['snippet'] ) {
            $controller->setPrint( CRM_Core_Smarty::PRINT_SNIPPET ); 
        }
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

    /**
     * handle the export case. this is a hack, so please fix soon
     *
     * @param $args array this array contains the arguments of the url
     *
     * @static
     * @access public
     */
    static function export( $args ) 
    {
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
        } else if ($type == 3) {
            $varName = 'duplicates';
            $saveFileName = 'Import_Duplicates.csv';
        } else if ($type == 4) {
            $varName = 'mismatch';
            $saveFileName = 'Import_Mismatch.csv';
        }else {
            /* FIXME we should have an error here */
            return;
        }
        
        // FIXME: a hack until we have common import
        // mechanisms for contacts and contributions
        $realm = CRM_Utils_Array::value('realm',$_GET);
        if ($realm == 'contribution') {
            $controller = 'CRM_Contribute_Import_Controller';
        } else if ( $realm == 'membership' ) {
            $controller = 'CRM_Member_Import_Controller';
        } else if ( $realm == 'event' ) {
            $controller = 'CRM_Event_Import_Controller';
        } else if ( $realm == 'activity' ) {
            $controller = 'CRM_Activity_Import_Controller';
        } else {
            $controller = 'CRM_Import_Controller';
        }
        
        require_once 'CRM/Core/Key.php';
        $qfKey = CRM_Core_Key::get( $controller );
        
        $fileName = $session->get($varName . 'FileName', "{$controller}_{$qfKey}");
        
        $config =& CRM_Core_Config::singleton( ); 
        
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Length: ' . filesize($fileName) );
        header('Content-Disposition: attachment; filename=' . $saveFileName);
        
        readfile($fileName);
        
        exit();
    }
}


