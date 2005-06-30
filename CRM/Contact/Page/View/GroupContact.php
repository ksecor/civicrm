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
 * This class contains functions for managing Groups of a Contact. 
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Contact/Page/View.php';

class CRM_Contact_Page_View_GroupContact extends CRM_Contact_Page_View {
    
    /**
     * This function is called when action is browse
     * 
     * return null
     * @access public
     */
    function browse( $this ) {
  
        $count   = CRM_Contact_BAO_GroupContact::getContactGroup($this->_contactId, null, null, true);
        
        $in      =& CRM_Contact_BAO_GroupContact::getContactGroup($this->_contactId, 'In' );
        $pending =& CRM_Contact_BAO_GroupContact::getContactGroup($this->_contactId, 'Pending' );
        $out     =& CRM_Contact_BAO_GroupContact::getContactGroup($this->_contactId, 'Out' );

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
    function edit( $groupId = null ) {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Contact_Form_GroupContact', 'Contact GroupContacts', $this->_action );
        $controller->setEmbedded( true );

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();

        $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view/group', 'action=browse' ) );

        $controller->reset( );

        $controller->set( 'contactId', $this->_contactId );
        $controller->set( 'groupId'  , $groupId );
 
        $controller->process( );
        $controller->run( );

    }

    /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) {
        $this->preProcess( );

        if ( $this->_action == CRM_Core_Action::DELETE ) {
            $groupContactId = CRM_Utils_Request::retrieve( 'gcid' );
            $status         = CRM_Utils_Request::retrieve( 'st' );
            if ( is_numeric($groupContactId) && $status ) {
                self::del( $groupContactId,$status );
            }
        }

        $this->edit( CRM_Core_Action::ADD );
        $this->browse( );
        return parent::run( );
    }

 
    /**
     * function to remove/ rejoin the group
     *
     * @param int $groupContactId id of crm_group_contact
     * @param string $status this is the status that should be updated.
     *
     * $access public
     * @static
     */
    static function del($groupContactId, $status ) {
        $groupContact =& new CRM_Contact_DAO_GroupContact( );
        
        switch ($status) {
        case 'i' :
            $groupContact->status = 'In';
            $groupContact->in_date = date('Ymd');
            $groupContact->in_method = 'Admin';
            break;
        case 'p' :
            $groupContact->status = 'Pending';
            $groupContact->pending_date = date('Ymd');
            break;
        case 'o' :
            $groupContact->status = 'Out';
            $groupContact->out_date = date('Ymd');
            $groupContact->out_method = 'Admin';
            break;
        }
        
        $groupContact->id = $groupContactId;

        $groupContact->save();

    }
}

?>