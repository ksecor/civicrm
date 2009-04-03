<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Contact/Page/View.php';
require_once 'CRM/Contact/BAO/Contact.php';

/**
 * CiviCRM Dashboard
 *
 */
class CRM_Contact_Page_View_DashBoard extends CRM_Contact_Page_View 
{

    protected $_rows = array();
    protected $_totalCountOpenActivity = array();
    protected $_totalCountActivity = array();
    protected $_contactIds = array();
    protected $_history = array();
    protected $_displayName = array();

    /*
     * Heart of the viewing process. The runner gets all the meta data for
     * the contact and calls the appropriate type of page to view.
     *
     * @return void
     * @access public
     *
     */
    function preProcess()
    {
        $params   = array( );
        $defaults = array( );
        $ids      = array( );
        
        $session =& CRM_Core_Session::singleton( );
        $uid  = $session->get( 'userID' );
        
        if ( ! $uid) {
            require_once 'CRM/Utils/System.php';
            CRM_Utils_System::setUFMessage( ts( 'We could not find a user id. You must be logged in to access the CiviCRM Home Page and menus.' ) );
            CRM_Core_Error::statusBounce( ts( 'We could not find a user id. You must be logged in to access the CiviCRM Home Page and menus.' ) );
        }

        $this->assign( 'contactId', $uid);
        $this->_action = CRM_Utils_Request::retrieve('action', 'String',
                                                     $this, false, 'view');
        $this->assign( 'action', $this->_action);

        // a user can always view their own activity history
        // if they have access CiviCRM permission
        $this->_permission = CRM_Core_Permission::VIEW;
        
        // make the permission edit if the user has edit permission on the contact
        require_once 'CRM/Contact/BAO/Contact/Permission.php';
        if ( CRM_Contact_BAO_Contact_Permission::allow( $uid, CRM_Core_Permission::EDIT ) ) {
            $this->_permission = CRM_Core_Permission::EDIT;
        }

        $displayName = $this->get( 'displayName' );
        
        list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $uid);
        
        $this->set( 'displayName' , $displayName );
        $this->set( 'contactImage', $contactImage );

        CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName, $displayName );
        CRM_Utils_Recent::add( $displayName,
                               CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $uid ),
                               $contactImage,$uid );
        
        // call hook to get html from other modules
        require_once 'CRM/Utils/Hook.php';
        $contentPlacement = CRM_Utils_Hook::DASHBOARD_BELOW;  // ignored but needed to prevent warnings
        $html = CRM_Utils_Hook::dashboard( $uid, $contentPlacement );
        if ( is_array( $html ) ) {
            $this->assign_by_ref( 'hookContent', $html );
            $this->assign( 'hookContentPlacement', $contentPlacement );
        }

    }
    
    /**
     * Browse all activities for a particular contact
     *
     * @return none
     *
     * @access public
     */
    function browse($id, $admin)
    { 
        $config =& CRM_Core_Config::singleton( );
        if ( ! $config->civiHRD ) { 
            require_once "CRM/Activity/BAO/Activity.php";
            $this->_totalCountOpenActivity = CRM_Activity_BAO_Activity::getNumOpenActivity( $id );
            $this->_contactIds             = $id;

            require_once 'CRM/Core/Selector/Controller.php';

            $output = CRM_Core_Selector_Controller::SESSION;

            require_once 'CRM/Activity/Selector/Activity.php';
            $selector   =& new CRM_Activity_Selector_Activity( $id, $this->_permission , $admin, 'home' );
            $sortID = CRM_Utils_Sort::sortIDValue( $this->get( CRM_Utils_Sort::SORT_ID  ),
                                               $this->get( CRM_Utils_Sort::SORT_DIRECTION ) );

            $controller =& new CRM_Core_Selector_Controller($selector, $this->get(CRM_Utils_Pager::PAGE_ID),
                                                        $sortID, CRM_Core_Action::VIEW, $this, $output);
            $controller->setEmbedded(true);
            $controller->run();
            $this->_rows = $controller->getRows($controller);
            $controller->moveFromSessionToTemplate( );

            $this->_displayName = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $id, 'display_name');

            $this->assign( 'rows',         $this->_rows);
            $this->assign( 'contactId',    $this->_contactIds);
            $this->assign( 'display_name', $this->_displayName);
            $this->assign( 'context',      'home');

            // check if case is enabled
            require_once 'CRM/Core/BAO/Preferences.php';
            $viewOptions = CRM_Core_BAO_Preferences::valueOptions( 'contact_view_options', true, null, true );

            $enableCase = false;
            if ( $viewOptions[ts('CiviCase')] ) { 
                $enableCase = true;
            }
        
            $this->assign( 'enableCase', $enableCase);
        }

        require_once 'CRM/Core/Block.php';
        $this->assign( 'menuBlock'    , CRM_Core_Block::getContent( 1 ) );
        $this->assign( 'shortcutBlock', CRM_Core_Block::getContent( 2 ) );
        $this->assign( 'searchBlock'  , CRM_Core_Block::getContent( 4 ) );
    }
        
    /**
     * perform actions and display for activities.
     *
     * @return none
     *
     * @access public
     */
    function run( )
    {
        $this->preProcess( );
        
        CRM_Utils_System::setTitle( ts('CiviCRM Home') );

        //Get the id of Logged in User
        $session =& CRM_Core_Session::singleton( );
        $id  = $session->get( 'userID' );
        
        $admin = 
            CRM_Core_Permission::check( 'view all activities' ) ||
            CRM_Core_Permission::check( 'administer CiviCRM' );

        $this->browse( $id, $admin );
      
        return parent::run( );
    }
}

