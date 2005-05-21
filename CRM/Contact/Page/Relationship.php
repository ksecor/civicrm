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
* This class contains functions for managing Relationship(s) of a Contact. 
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

class CRM_Contact_Page_Relationship {

    /**
     * View details of a relationship
     *
     * @param object $page - the view page
     * @param int $relationshipId - which relationship do we want to view ?
     *
     * @return none
     *
     * @access public
     * @static
     */
    static function view($page, $relationshipId)
    {
        $contactId = $page->getContactId( );
        $viewRelationship = CRM_Contact_BAO_Relationship::getRelationship( $contactId, null, null, null, $relationshipId );

        $page->assign( 'viewRelationship', $viewRelationship );
       
    }

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
        $contactId = $page->getContactId( );
        
        $currentRelationships = CRM_Contact_BAO_Relationship::getRelationship($contactId);
        $pastRelationships = CRM_Contact_BAO_Relationship::getRelationship( $contactId, 1 );
        $disableRelationships = CRM_Contact_BAO_Relationship::getRelationship( $contactId, 2 );
        
        $page->assign( 'currentRelationships', $currentRelationships );
        $page->assign( 'pastRelationships', $pastRelationships );
        $page->assign( 'disableRelationships', $disableRelationships );
        
    }
    
    
    /**
     * This function is called when action is update for relationship page
     * 
     * @param object $page CRM_Contact_Page_Relationship
     * @param int    $mode mode of the page which depends on the action
     * @param int    $realtionshipID relationship id 
     *
     * return null
     * @static
     * @access public
     */
    static function edit( $page, $mode, $relationshipId = null ) {

        $controller = new CRM_Core_Controller_Simple( 'CRM_Contact_Form_Relationship', 'Contact Relationships', $mode );
        $controller->setEmbedded( true );

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view/rel', 'action=browse' ) );
        
        // rtype is the variable that tells type of realationship (a_b or b_a)
        //$rtype = CRM_Utils_Request::retrieve( 'rtype', $this );
        $rtype = CRM_Utils_Request::retrieve( 'rtype', $controller );

        $controller->set( 'contactId'     , $page->getContactId( ) );
        $controller->set( 'relationshipId', $relationshipId );
        $controller->process( );
        $controller->run( );
    }


   /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * @param object $page CRM_Contact_Page_Relationship
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

        $rid = CRM_Utils_Request::retrieve( 'rid', $page, false, 0 );

        if ( $action & CRM_Core_Action::VIEW ) {
            self::view( $page, $rid );
        } else if ( $action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD ) ) {
            self::edit( $page, $action, $rid );
        } else if ( $action & CRM_Core_Action::DELETE ) {
            self::delete( $rid );
        } else if ( $action & CRM_Core_Action::DISABLE ) {
            CRM_Contact_BAO_Relationship::setIsActive( $rid, 0 ) ;
        } else if ( $action & CRM_Core_Action::ENABLE ) {
            CRM_Contact_BAO_Relationship::setIsActive( $rid, 1 ) ;
        } 

        self::browse( $page );
    }
    
   /**
     * This function is called to delete the relationship of a contact
     * 
     * @param int $relationshipId relationship id
     * 
     * return null
     * @static
     * @access public
     */
    static function delete( $relationshipId ) {
        // calls a function to delete relationship
        CRM_Contact_BAO_Relationship::del($relationshipId);
    }

}

?>