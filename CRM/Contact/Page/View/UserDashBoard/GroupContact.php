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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 * This class contains functions for managing Groups of a Contact. 
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Page/View/UserDashBoard.php';

class CRM_Contact_Page_View_UserDashBoard_GroupContact extends CRM_Contact_Page_View_UserDashBoard
{
    /**
     * This function is called when action is browse
     * 
     * return null
     * @access public
     */
    function browse( ) 
    {  
        $count   = CRM_Contact_BAO_GroupContact::getContactGroup($this->_contactId, null, null, true);

        $in      =& CRM_Contact_BAO_GroupContact::getContactGroup($this->_contactId,
                                                                  'Added',
                                                                  null, false, true );
        $pending =& CRM_Contact_BAO_GroupContact::getContactGroup($this->_contactId,
                                                                  'Pending',
                                                                  null, false, true );
        $out     =& CRM_Contact_BAO_GroupContact::getContactGroup($this->_contactId,
                                                                  'Removed',
                                                                  null, false, true );

        $this->assign       ( 'groupCount'  , $count );
        $this->assign_by_ref( 'groupIn'     , $in );
        $this->assign_by_ref( 'groupPending', $pending );
        $this->assign_by_ref( 'groupOut'    , $out );
    }

    /**
     * This function is called when action is update
     * 
     * @param int    $groupID group id 
     *
     * return null
     * @access public
     */
    function edit( $groupId = null ) 
    {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Contact_Form_GroupContact', ts("Contact's Groups"), CRM_Core_Action::ADD );
        $controller->setEmbedded( true );

        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url('civicrm/user', 'reset=1&id='. $this->_contactId ) ,false);

        $controller->reset( );

        $controller->set( 'contactId', $this->_contactId );

        $controller->set( 'groupId'  , $groupId );
        $controller->set( 'context'  , 'user' );
        $controller->process( );
        $controller->run( );

    }

    /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) 
    {
        $this->edit(  );
        $this->browse( );
    }

 
    /**
     * function to remove/ rejoin the group
     *
     * @param int $groupContactId id of crm_group_contact
     * @param string $status this is the status that should be updated.
     *
     * $access public
     */
    function del($groupContactId, $status ) 
    {
        $groupContact =& new CRM_Contact_DAO_GroupContact( );
        $groupId = CRM_Contact_BAO_GroupContact::getGroupId($groupContactId);
       
        switch ($status) {
        case 'i' :
            $groupStatus = 'Added';
            break;
        case 'p' :
            $groupStatus = 'Pending';
           
            break;
        case 'o' :
            $groupStatus = 'Removed';
            break;
        }
        $contactID = array($this->_contactId);
        $method = 'Admin';
        CRM_Contact_BAO_GroupContact::removeContactsFromGroup($contactID, $groupId, $method  ,$groupStatus);

    }
}

?>