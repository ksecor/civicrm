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

require_once 'CRM/Core/Page.php';

class CRM_Contact_Page_GroupContact {
    
    /**
     * This function is called when action is browse
     * 
     * @param object $page CRM_Contact_Page_GroupContact
     * 
     * return null
     * @static
     * @access public
     */
    static function browse( $page ) {
  
        $contactId   = $page->getContactId( );

        $count   = CRM_Contact_BAO_GroupContact::getContactGroup($contactId, null, null, true);
        
        $in      =& CRM_Contact_BAO_GroupContact::getContactGroup($contactId, 'In' );
        $pending =& CRM_Contact_BAO_GroupContact::getContactGroup($contactId, 'Pending' );
        $out     =& CRM_Contact_BAO_GroupContact::getContactGroup($contactId, 'Out' );

        $page->assign       ( 'groupCount'  , $count );
        $page->assign_by_ref( 'groupIn'     , $in );
        $page->assign_by_ref( 'groupPending', $pending );
        $page->assign_by_ref( 'groupOut'    , $out );
    }

    /**
     * This function is called when action is update
     * 
     * @param object $page CRM_Contact_Page_GroupContact
     * @param int    $mode mode of the page which depends on the action
     * @param int    $groupID group id 
     *
     * return null
     * @static
     * @access public
     */
    static function edit( $page, $mode, $groupId = null ) {
        $controller = new CRM_Core_Controller_Simple( 'CRM_Contact_Form_GroupContact', 'Contact GroupContacts', $mode );
        $controller->setEmbedded( true );

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $config  = CRM_Core_Config::singleton();

        $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view/group', 'action=browse' ) );

        $controller->reset( );

        $controller->set( 'contactId'  , $page->getContactId( ) );
        $controller->set( 'groupId'   , $groupId );
 
        $controller->process( );
        $controller->run( );

    }

    /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * @param object $page CRM_Contact_Page_GroupContact
     * 
     * return null
     * @static
     * @access public
     */
    static function run( $page ) {

        $contactId = $page->getContactId( );
        $page->assign( 'contactId', $contactId );

        $action = CRM_Utils_Request::retrieve( 'action', $page, false, 'browse' );
        $page->assign( 'action', $action );

        if ( $action == CRM_Core_Action::DELETE ) {
            $groupContactId = $_GET['gcid'];
            $status = $_GET['st'];
            if (is_numeric($groupContactId) && strlen(trim($status))) {
                self::del( $groupContactId,$status );
            }
        }

        self::edit( $page, CRM_Core_Action::ADD );
        self::browse( $page );
    }

 
    /*
     * function to remove/ rejoin the group
     *
     * @param int $groupContactId id of crm_group_contact
     * @param string $status this is the status that should be updated.
     *
     * $access public
     * @static
     */
    static function del($groupContactId, $status ) {
        $groupContact = new CRM_Contact_DAO_GroupContact( );
        
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