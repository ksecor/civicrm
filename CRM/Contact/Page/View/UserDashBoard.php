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

        if ( in_array( 'CiviContribute', $config->enableComponents ) &&
             ( CRM_Core_Permission::access( 'CiviContribute' ) ||
               CRM_Core_Permission::check('make online contributions') )
             ) {
            $components['CiviContribute'] = 'CiviContribute';
            require_once "CRM/Contact/Page/View/UserDashBoard/Contribution.php";
            $contribution = new CRM_Contact_Page_View_UserDashBoard_Contribution( );
            $contribution->run( );
        }
        
        if ( in_array( 'CiviMember', $config->enableComponents ) &&
             ( CRM_Core_Permission::access( 'CiviMember' ) ||
               CRM_Core_Permission::check('make online contributions') )
             ) {
            $components['CiviMember'] = 'CiviMember';
            require_once "CRM/Contact/Page/View/UserDashBoard/Membership.php";
            $membership = new CRM_Contact_Page_View_UserDashBoard_Membership( );
            $membership->run( );
        }

        if ( in_array( 'CiviEvent', $config->enableComponents ) &&
             ( CRM_Core_Permission::access( 'CiviEvent' ) ||
               CRM_Core_Permission::check('register for events') )
             ) {
            $components['CiviEvent'] = 'CiviEvent';
            require_once "CRM/Contact/Page/View/UserDashBoard/Participant.php";
            $participant = new CRM_Contact_Page_View_UserDashBoard_Participant( );
            $participant->run( );
        }        

        $this->assign ( 'components', $components );

        //build group selector
        require_once "CRM/Contact/Page/View/UserDashBoard/GroupContact.php";
        $gContact = new CRM_Contact_Page_View_UserDashBoard_GroupContact( );
        $gContact->run( );
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
}

?>
