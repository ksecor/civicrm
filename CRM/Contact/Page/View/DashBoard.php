<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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
        
        $this->assign( 'contactId', $uid);
        if ( ! $uid) {
            CRM_Core_Error::statusBounce( ts( 'We could not find a contact id.' ) );
        }

        $this->_action = CRM_Utils_Request::retrieve('action', 'String',
                                                     $this, false, 'view');
        $this->assign( 'action', $this->_action);

        // a user can always view their own activity history
        // if they have access CiviCRM permission
        $this->_permission = CRM_Core_Permission::VIEW;
        
        // also add the cid params to the Menu array
        CRM_Core_Menu::addParam( 'cid', $uid );
        
        $displayName = $this->get( 'displayName' );
        
        list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $uid);
        
        $this->set( 'displayName' , $displayName );
        $this->set( 'contactImage', $contactImage );
        
        CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName );
        CRM_Utils_Recent::add( $displayName,
                               CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $uid ),
                               $contactImage,$uid );
        
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
        $this->_totalCountOpenActivity = CRM_Contact_BAO_Contact::getNumOpenActivity( $id );
        $this->_contactIds             = $id;

        require_once 'CRM/Core/Selector/Controller.php';

        $output = CRM_Core_Selector_Controller::SESSION;
        require_once 'CRM/Activity/Selector/Activity.php';
        $selector   =& new CRM_Activity_Selector_Activity( $id, $this->_permission , $admin, 'Home' );
        $sortID = CRM_Utils_Sort::sortIDValue( $this->get( CRM_Utils_Sort::SORT_ID  ),
                                               $this->get( CRM_Utils_Sort::SORT_DIRECTION ) );

        $controller =& new CRM_Core_Selector_Controller($selector, $this->get(CRM_Utils_Pager::PAGE_ID),
                                                        $sortID, CRM_Core_Action::VIEW, $this, $output);
        $controller->setEmbedded(true);
        $controller->run();
        $this->_rows = $controller->getRows($controller);
        $controller->moveFromSessionToTemplate( );
        
        $this->_displayName = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $id, 'display_name');

        $this->assign( 'rows',                   $this->_rows);
        $this->assign( 'contactId',              $this->_contactIds);
        $this->assign( 'totalCountOpenActivity', $this->_totalCountOpenActivity);
        $this->assign( 'display_name',           $this->_displayName);

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
        
        $admin = CRM_Core_Permission::check( 'administer CiviCRM' );

        $this->browse( $id, $admin );
      
        return parent::run( );
    }
}
?>
