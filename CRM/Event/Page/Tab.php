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

require_once 'CRM/Contact/Page/View.php';

class CRM_Event_Page_Tab extends CRM_Contact_Page_View 
{
    /**
     * This function is called when action is browse
     * 
     * return null
     * @access public
     */
    function browse( ) 
    {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Event_Form_Search', ts('Events'), $this->_action );
        $controller->setEmbedded( true );
        $controller->reset( );
        $controller->set( 'cid'  , $this->_contactId );
        $controller->set( 'context', 'participant' ); 
        $controller->process( );
        $controller->run( );
    }
    
    /** 
     * This function is called when action is view
     *  
     * return null 
     * @access public 
     */ 
    function view( ) 
    {    
        // build associated contributions
        $this->associatedContribution( );
        
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Event_Form_ParticipantView',  
                                                       'View Participant',  
                                                       $this->_action ); 
        $controller->setEmbedded( true );  
        $controller->set( 'id' , $this->_id );  
        $controller->set( 'cid', $this->_contactId );  
        
        return $controller->run( ); 
    }
    
    /** 
     * This function is called when action is update or new 
     *  
     * return null 
     * @access public 
     */ 
    function edit( ) 
    { 
        // build associated contributions
        $this->associatedContribution( );
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Event_Form_Participant', 
                                                       'Create Participation', 
                                                       $this->_action );
        $controller->setEmbedded( true ); 
        $controller->set( 'id' , $this->_id ); 
        $controller->set( 'cid', $this->_contactId ); 
        
        return $controller->run( );
    }
    
    
    /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) 
    {
        // we should call contact view, preprocess only for participant mode
        $contactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        $context   = CRM_Utils_Request::retrieve( 'context', 'String', $this );

        $this->preProcess( );
        /*****
         * KURUND: FIX ME PLEASE
        if ( $contactId && $context != 'search' ) {
            $this->preProcess( );
        } else {
            // this case is for batch update, event registration action 
            $this->_action = CRM_Core_Action::ADD;
            $this->assign( 'action', $this->_action );
        }
        ****/

        if ( $this->_permission == CRM_Core_Permission::EDIT && ! CRM_Core_Permission::check( 'edit event participants' ) ) {
            $this->_permission = CRM_Core_Permission::VIEW; // demote to view since user does not have edit event participants rights
            $this->assign( 'permission', 'view' );
        }
        
        // check if we can process credit card registration
        $processors = CRM_Core_PseudoConstant::paymentProcessor( false, false,
                                                                 "billing_mode IN ( 1, 3 )" );
        if ( count( $processors ) > 0 ) {
            $this->assign( 'newCredit', true );
        } else {
            $this->assign( 'newCredit', false );
        }
        
        $this->setContext( );

        if ( $this->_action & CRM_Core_Action::VIEW ) { 
            $this->view( ); 
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE ) ) {
            $this->edit( ); 
        } else {
            $this->browse( ); 
        }
        
        return parent::run( );
    }
    
    function setContext( ) 
    {
        $context = CRM_Utils_Request::retrieve( 'context', 'String', $this, false, 'search' );
        switch ( $context ) {
            
        case 'dashboard':           
            $url = CRM_Utils_System::url( 'civicrm/event', 'reset=1' );
            break;
            
        case 'search':
            $url = CRM_Utils_System::url( 'civicrm/event/search', 'force=1' );
            break;
            
        case 'user':
            $url = CRM_Utils_System::url( 'civicrm/user', 'reset=1' );
            break;
            
        case 'participant':
            $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactId}&selectedChild=participant" );
            break;

        case 'home':
            $url = CRM_Utils_System::url( 'civicrm/dashboard', 'force=1' );
            break;

        case 'activity':
            $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactId}&selectedChild=activity" );
            break;
            
        default:
            $cid = null;
            if ( $this->_contactId ) {
                $cid = '&cid=' . $this->_contactId;
            }
            $url = CRM_Utils_System::url( 'civicrm/event/search', 
                                          'force=1' . $cid );
            break;
        }
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $url );
    }

    /** 
     * This function is used for the to show the associated
     * contribution for the participant 
     * 
     * return null 
     * @access public 
     */
    function associatedContribution( )
    {
        if ( CRM_Core_Permission::access( 'CiviContribute' ) ) {
            $this->assign( 'accessContribution', true );
            $session =& CRM_Core_Session::singleton( );
            $session->set( 'action', $this->_action );
            $session->set( 'participantId'  , $this->_id );
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_Search', ts('Contributions'), null );  
            $controller->setEmbedded( true );                           
            $controller->reset( );  
            $controller->set( 'force', 1 );
            $controller->set( 'cid'  , $this->_contactId );
            $controller->set( 'participantId'  , $this->_id );
            $controller->set( 'context', 'contribution' ); 
            $controller->process( );  
            $controller->run( );
        } else {
            $this->assign( 'accessContribution', false );
        }
    }
}


