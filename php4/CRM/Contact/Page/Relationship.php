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

$GLOBALS['_CRM_CONTACT_PAGE_RELATIONSHIP']['_links'] = '';

require_once 'CRM/Contact/BAO/Relationship.php';
require_once 'CRM/Core/Controller/Simple.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Utils/Request.php';
require_once 'CRM/Core/Page.php';

class CRM_Contact_Page_Relationship {
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    
    
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
     function view($page, $relationshipId)
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
     function browse( $page ) {
        $contactId = $page->getContactId( );

        $links =& CRM_Contact_Page_Relationship::links( );
        $currentRelationships = CRM_Contact_BAO_Relationship::getRelationship($contactId,
                                                                              CRM_CONTACT_BAO_RELATIONSHIP_CURRENT  ,
                                                                              0, 0, 0,
                                                                              $links );
        $pastRelationships    = CRM_Contact_BAO_Relationship::getRelationship( $contactId,
                                                                               CRM_CONTACT_BAO_RELATIONSHIP_PAST     ,
                                                                               0, 0, 0,
                                                                               $links );
        $disableRelationships = CRM_Contact_BAO_Relationship::getRelationship( $contactId,
                                                                               CRM_CONTACT_BAO_RELATIONSHIP_DISABLED ,
                                                                               0, 0, 0,
                                                                               $links );
        
        $page->assign( 'currentRelationships', $currentRelationships );
        $page->assign( 'pastRelationships'   , $pastRelationships );
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
     function edit( $page, $mode, $relationshipId = null ) {

        $controller = new CRM_Core_Controller_Simple( 'CRM_Contact_Form_Relationship', 'Contact Relationships', $mode );
        $controller->setEmbedded( true );

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view/rel', 'action=browse' ) );
        
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
      function run( $page ) {

        $contactId = $page->getContactId( );
        $page->assign( 'contactId', $contactId );

        $action = CRM_Utils_Request::retrieve( 'action', $page, false, 'browse' );

        $page->assign( 'action', $action );

        $rid = CRM_Utils_Request::retrieve( 'rid', $page, false, 0 );

        if ( $action & CRM_CORE_ACTION_VIEW ) {
            CRM_Contact_Page_Relationship::view( $page, $rid );
        } else if ( $action & ( CRM_CORE_ACTION_UPDATE | CRM_CORE_ACTION_ADD ) ) {
            CRM_Contact_Page_Relationship::edit( $page, $action, $rid );
        } else if ( $action & CRM_CORE_ACTION_DELETE ) {
            CRM_Contact_Page_Relationship::delete( $rid );
        } else if ( $action & CRM_CORE_ACTION_DISABLE ) {
            CRM_Contact_BAO_Relationship::setIsActive( $rid, 0 ) ;
        } else if ( $action & CRM_CORE_ACTION_ENABLE ) {
            CRM_Contact_BAO_Relationship::setIsActive( $rid, 1 ) ;
        } 

        CRM_Contact_Page_Relationship::browse( $page );
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
     function delete( $relationshipId ) {
        // calls a function to delete relationship
        CRM_Contact_BAO_Relationship::del($relationshipId);
    }

    /**
     * Get action Links
     *
     * @param none
     * @return array (reference) of action links
     * @static
     */
     function &links()
    {
        if ( ! isset( $GLOBALS['_CRM_CONTACT_PAGE_RELATIONSHIP']['_links'] ) )
        {
            $deleteExtra = ts('Are you sure you want to delete this relationship?');
            $disableExtra = ts('Are you sure you want to disable this relationship?');
            $enableExtra = ts('Are you sure you want to re-enable this relationship?');

            $GLOBALS['_CRM_CONTACT_PAGE_RELATIONSHIP']['_links'] = array(
                                  CRM_CORE_ACTION_VIEW    => array(
                                                                    'name'  => ts('View'),
                                                                    'url'   => 'civicrm/contact/view/rel',
                                                                    'qs'    => 'action=view&rid=%%rid%%&rtype=%%rtype%%',
                                                                    'title' => ts('View Relationship')
                                                                    ),
                                  CRM_CORE_ACTION_UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/contact/view/rel',
                                                                    'qs'    => 'action=update&rid=%%rid%%&rtype=%%rtype%%',
                                                                    'title' => ts('Edit Relationship')
                                                                    ),
                                  CRM_CORE_ACTION_ENABLE  => array(
                                                                    'name'  => ts('Enable'),
                                                                    'url'   => 'civicrm/contact/view/rel',
                                                                    'qs'    => 'action=enable&rid=%%rid%%&rtype=%%rtype%%',
                                                                    'extra' => 'onclick = "return confirm(\'' . $enableExtra . '\');"',
                                                                    'title' => ts('Enable Relationship')
                                                                    ),
                                  CRM_CORE_ACTION_DISABLE => array(
                                                                    'name'  => ts('Disable'),
                                                                    'url'   => 'civicrm/contact/view/rel',
                                                                    'qs'    => 'action=disable&rid=%%rid%%&rtype=%%rtype%%',
                                                                    'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                    'title' => ts('Disable Relationship')
                                                                    ),
                                  CRM_CORE_ACTION_DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/contact/view/rel',
                                                                    'qs'    => 'action=delete&rid=%%rid%%&rtype=%%rtype%%',
                                                                    'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
                                                                    'title' => ts('Delete Relationship')
                                                                    ),
                                  );
        }
        return $GLOBALS['_CRM_CONTACT_PAGE_RELATIONSHIP']['_links'];
    }
                                  
}

?>