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

        $config =& CRM_Core_Config::singleton( );

        // also initialize the i18n framework
        $i18n   =& CRM_Core_I18n::singleton( );

        if ( $config->userFramework == 'Joomla' ) {
            require_once 'CRM/Core/Joomla.php';
            CRM_Core_Joomla::sidebarLeft( );
        }

        // set active Component
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'activeComponent', 'CiviCRM' );
        $template->assign( 'formTpl'        , 'default' );

        switch ( $args[1] ) {

        case 'contact'  : 
            self::contact ( $args );
            break;

        case 'admin'    : 
            self::admin   ( $args );
            break;

        case 'dashboard':
            self::dashboard($args);
            break;
            
        case 'history'  : 
            self::history ( $args );
            break;

        case 'group'    : 
            self::group   ( $args );
            break;
        
        case 'import'   : 
            self::import  ( $args );
            break;
       
        case 'export'   : 
            self::export  ( $args );
            break;

        case 'activity' : 
            self::activity( $args );
            break;

        case 'profile'  : 
            self::profile ( $args );
            break;

        case 'file':
            self::file( $args );
            break;

        case 'acl':
            self::acl( $args );
            break;

        case 'user':
            self::user($args);
            break;

        default         :
            if ( CRM_Core_Component::invoke( $args, 'main' ) ) {
                break;
            }
            CRM_Utils_System::redirect( );
            break;

        }

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
        } else {
            $breadCrumbPath = CRM_Utils_System::url( 'civicrm/contact/search/basic', 'force=1' );
        }
        
        $additionalBreadCrumb = "<a href=\"$breadCrumbPath\">" . ts('Search Results') . '</a>';
       
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
        
        if ( $args[2] == 'domain' ) {
            $session =& CRM_Core_Session::singleton();
            $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/domain', 'action=view' ) );
            $wrapper =& new CRM_Utils_Wrapper( );
            return $wrapper->run( 'CRM_Contact_Form_Domain', ts('Domain Information Page'), null);
        }
        
        if ( $args[2] == 'email' ) {
            // set the userContext stack
            $session =& CRM_Core_Session::singleton();
            $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view', 'action=browse&selectedChild=activity' ) );

            $wrapper =& new CRM_Utils_Wrapper( );
            return $wrapper->run( 'CRM_Contact_Form_Task_Email', ts('Email a Contact'),  null );
        }

        if ( $args[2] == 'map' ) {
            $wrapper =& new CRM_Utils_Wrapper( );
            if ( CRM_Utils_Array::value( 3, $args ) == 'event' ) {
                return $wrapper->run( 'CRM_Contact_Form_Task_Map_Event', ts('Map Event Location'),  null );
            } else {
                return $wrapper->run( 'CRM_Contact_Form_Task_Map', ts('Map Contact'),  null );
            }
        }

        if ($args[2] == 'view') {
            
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            $thirdArg = CRM_Utils_Array::value( 3, $args, '' );
            $fourthArg = CRM_Utils_Array::value(4, $args, 0);
            
            switch ( $thirdArg ) {
            case 'contribution':
                if ( ! CRM_Core_Permission::check('access CiviContribute') ) {
                    CRM_Core_Error::fatal( 'You do not have access to this page' );
                }
                require_once 'CRM/Contact/Page/View/Contribution.php'; 
                $view =& new CRM_Contact_Page_View_Contribution( );
                break;

           
            case 'membership':
                if ( ! CRM_Core_Permission::check('access CiviMember') ) {
                    CRM_Core_Error::fatal( 'You do not have access to this page' );
                }
                require_once 'CRM/Contact/Page/View/Membership.php'; 
                $view =& new CRM_Contact_Page_View_Membership( );
                break;

            case 'participant':
                if ( ! CRM_Core_Permission::check('access CiviEvent') ) {
                    CRM_Core_Error::fatal( 'You do not have access to this page' );
                }
                require_once 'CRM/Contact/Page/View/Participant.php'; 
                $view =& new CRM_Contact_Page_View_Participant( );
                break;

            case 'note':
                require_once 'CRM/Contact/Page/View/Note.php';
                $view =& new CRM_Contact_Page_View_Note( );
                break;

            case 'rel':
                require_once 'CRM/Contact/Page/View/Relationship.php';
                $view =& new CRM_Contact_Page_View_Relationship( );
                break;

            case 'group':
                require_once 'CRM/Contact/Page/View/GroupContact.php';
                $view =& new CRM_Contact_Page_View_GroupContact( );
                break;

            case 'tag':
                require_once 'CRM/Contact/Page/View/Tag.php';
                $view =& new CRM_Contact_Page_View_Tag( );
                break;
            
            case 'basic':
                require_once 'CRM/Contact/Page/View/Basic.php';
                $view =& new CRM_Contact_Page_View_Basic( );
                break;
            
            case 'log':
                require_once 'CRM/Contact/Page/View/Log.php';
                $view =& new CRM_Contact_Page_View_Log( );
                break;
            
            case 'cd':
                require_once 'CRM/Contact/Page/View/CustomData.php';
                $view =& new CRM_Contact_Page_View_CustomData( );
                break;

            case 'activity':
                $activityId = CRM_Utils_Request::retrieve('activity_id', 'Positive',
                                                          CRM_Core_DAO::$_nullObject );
                $show = CRM_Utils_Request::retrieve('show', 'Boolean',
                                                    CRM_Core_DAO::$_nullObject );

                $session =& CRM_Core_Session::singleton();
                
                if(!$show) {
                    if ($activityId)  {
                        $session->set('activityId', $activityId);
                    } else {
                        $activityId = $session->get('activityId');
                    }
                }

                if ($activityId == 1) {
                    require_once 'CRM/Contact/Page/View/Meeting.php';
                    $view =& new CRM_Contact_Page_View_Meeting( );
                } elseif($activityId == 2) {
                    require_once 'CRM/Contact/Page/View/Phonecall.php';
                    $view =& new CRM_Contact_Page_View_Phonecall( );
                } elseif($activityId == 3) {
                    $details = CRM_Utils_Request::retrieve('details', 'String',
                                                           CRM_Core_DAO::$_nullObject );
                    if ($details) {
                        require_once 'CRM/Contact/Page/View/Email.php';
                        $view =& new CRM_Contact_Page_View_Email('View Email Details'); 
                    } else {
                        $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view', 'action=browse&selectedChild=activity' ) );
                        $wrapper =& new CRM_Utils_Wrapper( );
                        return $wrapper->run( 'CRM_Contact_Form_Task_Email', ts('Email a Contact'),  null );
                    }
                } elseif ($activityId == 4 ) {
                    require_once 'CRM/Contact/Page/View/SMS.php';
                    $view =& new CRM_Contact_Page_View_SMS( );
                } elseif ($activityId > 4) {
                    require_once 'CRM/Contact/Page/View/OtherActivity.php';
                    $view =& new CRM_Contact_Page_View_OtherActivity( );
                } else {
                    require_once 'CRM/Contact/Page/View/Activity.php';
                    $view =& new CRM_Contact_Page_View_Activity( );
                }

                break;
                
            case 'vcard':
                require_once 'CRM/Contact/Page/View/Vcard.php';
                $view =& new CRM_Contact_Page_View_Vcard();
                break;

            case 'delete':
                $wrapper =& new CRM_Utils_Wrapper( ); 
                return $wrapper->run( 'CRM_Contact_Form_Task_Delete', ts('Delete Contact'),  null ); 

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
                } elseif ( file_exists( realpath ( dirname( __FILE__ ) . "../Contact/Page/View/{$contact_type}.php" ) ) ) {
                    require_once 'CRM/Contact/Page/View/' . $contact_type . '.php';
                    eval( '$view =& new CRM_Contact_Page_View_' . $contact_type . '( );' );
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
        
        if ( $args[2] == 'search' ) {
            return self::search( $args );
        }
        
        return CRM_Utils_System::redirect( );
    }

    /**
     * This function for CiviCRM dashboard
     *
     *
     * @static
     * @access public
     */
    static function dashboard( $args ) 
    {
        require_once 'CRM/Contact/Page/View/DashBoard.php';
        $view =& new CRM_Contact_Page_View_DashBoard( );
        return $view->run();
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
        } else if ( $thirdArg == 'simple' ) {
            // set the userContext stack
            $session =& CRM_Core_Session::singleton();
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
        } elseif ( file_exists( realpath (dirname( __FILE__ ) . "../Contact/Form/{$contact_type}.php" ) ) ) {
            $wrapper->run( "CRM_Contact_Form_{$contact_type}", ts('New %1', array(1 => $contact_type)), $action, true );
        } else {
            $wrapper->run( 'CRM_Contact_Form_Edit', ts( 'New Contact' ), $action, true );
        }
    }
    
    /**
     * This function contains the actions for history arguments
     *
     * @param $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function history( $args ) 
    {
        if ( $args[2] == 'activity' && $args[3] == 'detail' ) {
            require_once 'CRM/History/Page/Activity.php';
            $page =& new CRM_History_Page_Activity('View Activity Details');
            return $page->run( );
        }

        if ($args[2] == 'email') {
            require_once 'CRM/History/Page/Email.php';
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
    static function admin( $args ) 
    {
        if ( $args[1] !== 'admin' ) {
            return;
        }

        if ( ! CRM_Core_Permission::check('administer CiviCRM') ) {
            CRM_Core_Error::fatal( 'You do not have access to this page' );
        }
       
        $view = null;
        switch ( CRM_Utils_Array::value( 2, $args, '' ) ) {
            
        case 'access':
            require_once 'CRM/Admin/Page/Access.php';
            $view =& new CRM_Admin_Page_Access(ts('Access Control'));
            break;

        case 'locationType':
            require_once 'CRM/Admin/Page/LocationType.php';
            $view =& new CRM_Admin_Page_LocationType(ts('View Location Types'));
            break;

        case 'reltype':
            require_once 'CRM/Admin/Page/RelationshipType.php';
            $view =& new CRM_Admin_Page_RelationshipType(ts('View Relationship Types'));
            break;

        case 'custom':
            if ( $args[3] == 'group' ) {
                if ( CRM_Utils_Array::value( 4, $args ) != 'field' ) {
                    require_once 'CRM/Custom/Page/Group.php';
                    $view =& new CRM_Custom_Page_Group(ts('Custom Data Group'));
                } else {
                    if ( CRM_Utils_Array::value( 5, $args ) != 'option' ) {
                        require_once 'CRM/Custom/Page/Field.php';
                        $view =& new CRM_Custom_Page_Field(ts('Custom Data Field'));
                    } else {
                        require_once 'CRM/Custom/Page/Option.php';
                        $view =& new CRM_Custom_Page_Option(ts('Custom Data Field'));
                        $url  = CRM_Utils_System::url( 'civicrm/admin/custom/group/field' );
                        $additionalBreadCrumb = '<a href="' . $url . '">' . ts('Custom Data Fields') . '</a>';
                        CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
                    }
                }
            }
            break;

        case 'uf':
            if ( $args[3] == 'group' ) {
                if ( CRM_Utils_Array::value( 4, $args ) != 'field' ) {
                    require_once 'CRM/UF/Page/Group.php';
                    $view =& new CRM_UF_Page_Group(ts('CiviCRM Profile Group'));
                } else {
                    require_once 'CRM/UF/Page/Field.php';
                    $view =& new CRM_UF_Page_Field(ts('CiviCRM Profile Field'));
                }
            }
            break;

        case 'tag':
            require_once 'CRM/Admin/Page/Tag.php';
            $view =& new CRM_Admin_Page_Tag(ts('View Tags'));
            break;
        
        case 'mapping':
            require_once 'CRM/Admin/Page/Mapping.php';
            $view =& new CRM_Admin_Page_Mapping(ts('View Mapping'));
            break;
            
        case 'options':
            require_once 'CRM/Admin/Page/Options.php';
            $view =& new CRM_Admin_Page_Options(ts('View Options'));
            break;   
            
        case 'synchUser':
            require_once 'CRM/Admin/Page/DrupalUser.php';
            $view =& new CRM_Admin_Page_DrupalUser(ts('Sync Drupal Users'));
            break;   

        case 'backup':
            require_once 'CRM/Admin/Page/DomainDump.php';
            $view =& new CRM_Admin_Page_DomainDump(ts('Backup Database'));
            break;   
            
        case 'dupematch':
            require_once 'CRM/Admin/Page/DupeMatch.php';
            $view =& new CRM_Admin_Page_DupeMatch(ts('Duplicate Matching'));
            break;
            
        case 'optionGroup':
            require_once 'CRM/Admin/Page/OptionGroup.php';
            $view =& new CRM_Admin_Page_OptionGroup(ts('View Option Groups'));
            break;

        case 'optionValue':
            require_once 'CRM/Admin/Page/OptionValue.php';
            $view =& new CRM_Admin_Page_OptionValue(ts('View Option Values'));
            
            $url  = CRM_Utils_System::url( 'civicrm/admin' );
            $additionalBreadCrumb = '<a href="' . $url . '">' . ts('Administer CiviCRM') . '</a>';
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            
            $url  = CRM_Utils_System::url( 'civicrm/admin/optionGroup' );
            $additionalBreadCrumb = '<a href="' . $url . '">' . ts('Options') . '</a>';
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            break;

        case 'setting':
            return self::setting( $args );
         
        case 'component':
            require_once 'CRM/Mailing/Page/Component.php';
            $view =& new CRM_Mailing_Page_Component( );
            break;
            
        case 'mail':
            $wrapper =& new CRM_Utils_Wrapper( );
            return $wrapper->run( 'CRM_Admin_Form_Setting_Mail', ts('CiviMail Settings'), null); 
          
        default:
            require_once 'CRM/Core/Component.php';
            if ( CRM_Core_Component::invoke( $args, 'admin' ) ) {
                return;
            }
            require_once 'CRM/Admin/Page/Admin.php';
            $view =& new CRM_Admin_Page_Admin(ts('Administer CiviCRM'));
            break;
        }


        if ( $view ) {
            return $view->run( );
        }

        return CRM_Utils_System::redirect( CRM_Utils_System::url('civicrm/admin', 'reset=1', false) );
    }

    /**
     * This function contains the action for import arguments
     *
     * @params $args array this array contains the arguments of the url 
     *
     * @static
     * @access public
     */
    static function import( $args ) 
    {

        if ( $args[1] != 'import' ) {
            return;
        }

        if ( ! CRM_Core_Permission::check('import contacts') ) {
            CRM_Core_Error::fatal( 'You do not have access to this page' );
        }

        $secondArg = CRM_Utils_Array::value( 2, $args, '' ); 
        $session =& CRM_Core_Session::singleton( );

        if ( $secondArg == 'activityHistory' ) {
            $session->pushUserContext(CRM_Utils_System::url('civicrm/import/activityHistory', 'reset=1'));

            require_once 'CRM/History/Import/Controller.php';
            $controller =& new CRM_History_Import_Controller(ts('Import Activity History'));
        } else {
            $session->pushUserContext(CRM_Utils_System::url('civicrm/import', 'reset=1'));

            require_once 'CRM/Import/Controller.php';
            $controller =& new CRM_Import_Controller(ts('Import Contacts'));
        }
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
    static function group( $args ) 
    {
        if ( $args[1] !== 'group' ) {
            return;
        }

        switch ( CRM_Utils_Array::value( 2, $args ) ) {
        case 'add':
            if ( ! CRM_Core_Permission::check('edit groups') ) {
                CRM_Core_Error::fatal( 'You do not have access to this page' );
            }

            require_once 'CRM/Group/Controller.php';
            $controller =& new CRM_Group_Controller(ts('Groups'), CRM_Core_Action::ADD);
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(CRM_Utils_System::url('civicrm/group', 'reset=1'));
            return $controller->run();

        case 'search':
            return self::search( $args );

        default:
            require_once 'CRM/Group/Page/Group.php';
            $view =& new CRM_Group_Page_Group(ts('View Groups'));
            return $view->run();
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

        if ($secondArg == 'view') {
            $session =& CRM_Core_Session::singleton();
            require_once 'CRM/Profile/Page/View.php';
            $view =& new CRM_Profile_Page_View();
            return $view->run();
        }


        if ($secondArg == 'map' ) {
            // set the userContext stack
            $session =& CRM_Core_Session::singleton();
            $session->pushUserContext( CRM_Utils_System::url('civicrm/profile' ) );

            $wrapper =& new CRM_Utils_Wrapper( );
            return $wrapper->run( 'CRM_Contact_Form_Task_Map', ts('Map Contact'),  null );
        }

        if ( $secondArg == 'edit' || $secondArg == 'create' ) {
            // set the userContext stack
            $session =& CRM_Core_Session::singleton(); 
            $session->pushUserContext( CRM_Utils_System::url('civicrm/profile', 'reset=1' ) ); 

            $userID = $session->get( 'userID' );

            $buttonType = $_POST['_qf_Edit_cancel'];
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
     * This function contains the actions for file arguments
     * 
     * @param $args array this array contains the arguments of the url 
     * 
     * @static 
     * @access public 
     */ 
    static function file( $args ) 
    { 
        if ( $args[1] !== 'file' ) { 
            return; 
        } 

        require_once 'CRM/Core/Page/File.php';
        $page =& new CRM_Core_Page_File( );
        return $page->run( );
    }

    /** 
     * This function contains the actions for acl arguments
     * 
     * @param $args array this array contains the arguments of the url 
     * 
     * @static 
     * @access public 
     */ 
    static function acl( $args ) 
    { 
        if ( $args[1] !== 'acl' ) { 
            return; 
        } 

        if ( ! CRM_Core_Permission::check('administer CiviCRM') ) {
            CRM_Core_Error::fatal( 'You do not have access to this page' );
        }

        $secondArg = CRM_Utils_Array::value( 2, $args );
        if (  $secondArg == 'entityrole' ) {
            require_once 'CRM/ACL/Page/EntityRole.php';
            $page =& new CRM_ACL_Page_EntityRole( );
        } else {
            require_once 'CRM/ACL/Page/ACL.php';
            $page =& new CRM_ACL_Page_ACL( );
        }
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
        $realm = $_GET['realm'];
        if ($realm == 'contribution') {
            $controller = 'CRM_Contribute_Import_Controller';
        } else if ( $realm == 'membership' ) {
            $controller = 'CRM_Member_Import_Controller';
        } else if ( $realm == 'event' ) {
            $controller = 'CRM_Event_Import_Controller';
        } else if ( $realm == 'history' ) {
            $controller = 'CRM_History_Import_Controller';
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
    
    static function onlySSL( $args ) 
    {
        if ( $args[1] = 'contribute' && $args[2] == 'transact' ) {
            return true;
        }
        return false;
    }

    /** 
     * This function contains the actions for setting arguments
     * 
     *  $args array this array contains the arguments of the url 
     * 
     * @static 
     * @access public 
     */ 

    static function setting ( $args ) 
    {
        if ( $args[2] !== 'setting' ) {
            return; 
        }
       
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url('civicrm/admin/setting', 'reset=1' ) );

        $wrapper =& new CRM_Utils_Wrapper( );
        
        $thirdArg = CRM_Utils_Array::value( 3, $args, '' );
        switch ( $thirdArg ) {
        case 'component' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Component', ts('Components'), null); 
            break;
        case 'path' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Path', ts('File System Paths'), null); 
            break;
        case 'url' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Url', ts('Site URLs'), null); 
            break;
        case 'smtp' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Smtp', ts('Smtp Server'), null); 
            break;
        case 'mapping' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Mapping', ts('Mapping and Geocoding'), null); 
            break;
        case 'payment' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Payment', ts('Online Payments'), null); 
            break;
        case 'localization' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Localization', ts('Localization'), null); 
            break;
        case 'address' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Address', ts('Address Formatting'), null); 
            break;
        case 'date' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Date', ts('Date Formatting'), null); 
            break;
        case 'misc' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Miscellaneous', ts('Miscellaneous'), null); 
            break;
        case 'debug' : 
            $output = $wrapper->run( 'CRM_Admin_Form_Setting_Debugging', ts('Debugging'), null); 
            break;
        default : 
            require_once 'CRM/Admin/Page/Setting.php';
            $view =& new CRM_Admin_Page_Setting();
            $output = $view->run();
            break;
        }
        $config =& CRM_Core_Config::singleton();
        $config->cleanup(1);
        return $output;
    }

    /**
     * This function for User dashboard
     *
     *
     * @static
     * @access public
     */
    static function user( $args ) 
    {
        if ( $args[1] !== 'user' ) {
            return;
        }

        require_once 'CRM/Contact/Page/View/UserDashBoard.php';
        $view =& new CRM_Contact_Page_View_UserDashBoard( );
        return $view->run();
    }


}

?>
