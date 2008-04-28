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

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contact/BAO/Contact.php';

/**
 * CMS User Dashboard
 * This class is used to build User Dashboard
 *
 */
class CRM_Contact_Page_View_UserDashBoard extends CRM_Core_Page
{
    public $_contactId        = null;

    /*
     * always show public groups
     */
    public $_onlyPublicGroups = true;

    public $_edit = true;

     /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    function __construct( ) {
        parent::__construct( );

        $check = CRM_Core_Permission::check( 'access Contact Dashboard' );
        
        if ( ! $check ) {
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' ) );
            break;
        }
        
        $this->_contactId =  CRM_Utils_Request::retrieve('id', 'Positive', $this );

        $session          =& CRM_Core_Session::singleton( );
        $userID           =  $session->get( 'userID' );
         
        if ( ! $this->_contactId ) { 
            $this->_contactId = $userID;
        } else  if ( $this->_contactId != $userID ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            if ( ! CRM_Contact_BAO_Contact::permissionedContact( $this->_contactId, CRM_Core_Permission::VIEW ) ) {
                CRM_Core_Error::fatal( ts( 'You do not have permission to view this contact' ) );
            }
            if ( ! CRM_Contact_BAO_Contact::permissionedContact( $this->_contactId, CRM_Core_Permission::EDIT ) ) {
                $this->_edit = false;
            }
        }
    }

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
        if ( ! $this->_contactId) {
            CRM_Core_Error::fatal( ts( 'We could not find a contact id.' ) );
        }

        list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );

        $this->set( 'displayName' , $displayName );
        $this->set( 'contactImage', $contactImage );

        CRM_Utils_System::setTitle( ts( 'Dashboard - %1', array( 1 => $displayName ) ) );
  
        $this->assign('recentlyViewed', false);
    }
    
    /**
     * Function to build user dashboard
     *
     * @return none
     * @access public
     */
    function buildUserDashBoard( )
    {
        //build component selectors
        $components = array( );
        $config =& CRM_Core_Config::singleton( );

        require_once 'CRM/Core/BAO/Preferences.php';
        $this->_userOptions  = CRM_Core_BAO_Preferences::valueOptions( 'user_dashboard_options' );
       
        if ( $this->_userOptions['Contributions'] &&
             in_array( 'CiviContribute', $config->enableComponents ) &&
             ( CRM_Core_Permission::access( 'CiviContribute' ) ||
               CRM_Core_Permission::check('make online contributions') )
             ) {
            $components['CiviContribute'] = 'CiviContribute';
            require_once "CRM/Contact/Page/View/UserDashBoard/Contribution.php";
            $contribution = new CRM_Contact_Page_View_UserDashBoard_Contribution( );
            $contribution->run( );
        }
        
        if ( $this->_userOptions['Memberships'] &&
             in_array( 'CiviMember', $config->enableComponents ) &&
             ( CRM_Core_Permission::access( 'CiviMember' ) ||
               CRM_Core_Permission::check('make online contributions') )
             ) {
            $components['CiviMember'] = 'CiviMember';
            require_once "CRM/Contact/Page/View/UserDashBoard/Membership.php";
            $membership = new CRM_Contact_Page_View_UserDashBoard_Membership( );
            $membership->run( );
        }

        if ( $this->_userOptions['Events'] &&
             in_array( 'CiviEvent', $config->enableComponents ) &&
             ( CRM_Core_Permission::access( 'CiviEvent' ) ||
               CRM_Core_Permission::check('register for events') )
             ) {
            $components['CiviEvent'] = 'CiviEvent';
            require_once "CRM/Contact/Page/View/UserDashBoard/Participant.php";
            $participant = new CRM_Contact_Page_View_UserDashBoard_Participant( );
            $participant->run( );
        }        

        if ( $this->_userOptions['My Contacts / Organizations'] ) {
            $components['My Contacts / Organizations'] = 'My Contacts / Organizations';

            $links =& self::links( );
            $mask  = CRM_Core_Action::mask( $this->_permission );
           
            $currentRelationships = CRM_Contact_BAO_Relationship::getRelationship($this->_contactId,
                                                                                  CRM_Contact_BAO_Relationship::CURRENT  ,
                                                                                  0, 0, 0,
                                                                                  $links, $mask );
            $this->assign( 'currentRelationships',  $currentRelationships  );
        }
        $this->assign ( 'components', $components );

        if ( $this->_userOptions['Groups'] ) {
            $this->assign( 'showGroup', true );
            //build group selector
            require_once "CRM/Contact/Page/View/UserDashBoard/GroupContact.php";
            $gContact = new CRM_Contact_Page_View_UserDashBoard_GroupContact( );
            $gContact->run( );
        } else {
            $this->assign( 'showGroup', false );
        }
    }
        
    /**
     * perform actions and display for user dashboard
     *
     * @return none
     *
     * @access public
     */
    function run( )
    {
        $this->preProcess( );
        
        $this->buildUserDashBoard( );
      
        return parent::run( );
    }
    
     /**
     * Get action links
     *
     * @return array (reference) of action links
     * @static
     */
    static function &links()
    {
        if (!(self::$_links)) {
            $deleteExtra = ts('Are you sure you want to delete this relationship?');
            $disableExtra = ts('Are you sure you want to disable this relationship?');
            $enableExtra = ts('Are you sure you want to re-enable this relationship?');

            self::$_links = array(
                                  CRM_Core_Action::VIEW    => array(
                                                                    'name'  => ts('Dashboard'),
                                                                    'url'   => 'civicrm/user',
                                                                    'qs'    => 'reset=1&id=%%cbid%%',
                                                                    'title' => ts('View Relationship')
                                                                    ),
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/contact/relatedcontact',
                                                                    'qs'    => 'action=update&reset=1&cid=%%cbid%%',
                                                                    'title' => ts('Edit Relationship')
                                                                    ),
                                  CRM_Core_Action::DISABLE => array(
                                                                    'name'  => ts('Disable'),
                                                                    'url'   => 'civicrm/contact/view/rel',
                                                                    'qs'    => 'action=disable&reset=1&cid=%%cid%%&id=%%id%%&rtype=%%rtype%%&selectedChild=rel',
                                                                    'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                    'title' => ts('Disable Relationship')
                                                                    ),
                                  );
        }
        return self::$_links;
    }
}


