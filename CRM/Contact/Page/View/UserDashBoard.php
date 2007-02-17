<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
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
    public $_contactId   = null;

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
        $admin   = CRM_Core_Permission::check( 'access User Dashboard' );
        
        if ( !$admin ) {
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' ) );
            break;
        }
        
        $session =& CRM_Core_Session::singleton( );
        $this->_contactId = $session->get( 'userID' );
        
        if ( ! $this->_contactId) {
            CRM_Core_Error::statusBounce( ts( 'We could not find a contact id.' ) );
        }
     
        list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );
        
        CRM_Utils_System::setTitle( 'User Dashboard' );
  
        $this->assign ( 'displayName', $displayName );
        $this->assign ( 'DisplayName', $contactImage . ' ' . $displayName );
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
        if ( CRM_Core_Permission::access( 'CiviContribute' ) ) {
            $components['CiviContribute'] = 'CiviContribute';
        }
        
        if ( CRM_Core_Permission::access( 'CiviMember' ) ) {
            $components['CiviMember'] = 'CiviMember';
            require_once "CRM/Contact/Page/View/UserDashBoard/Membership.php";
            $membership = new CRM_Contact_Page_View_UserDashBoard_Membership( );
            $membership->run( );
        }

        if ( CRM_Core_Permission::access( 'CiviEvent' ) ) {
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
