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

require_once 'CRM/Contact/Page/View.php';

class CRM_Contact_Page_View_Participant extends CRM_Contact_Page_View 
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
        if ( CRM_Utils_Request::retrieve( 'history', 'Boolean', CRM_Core_DAO::$_nullObject ) ) {
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Event_Form_ActivityView',  
                                                           'View Participant Details',  
                                                           $this->_action ); 
            
        } else {
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Event_Form_ParticipantView',  
                                                           'View Participant',  
                                                           $this->_action ); 
        }
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
        $this->preProcess( );
        
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
        case 'basic':
            $url = CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $this->_contactId );
            break;
            
        case 'dashboard':
            $url = CRM_Utils_System::url( 'civicrm/event', 'reset=1' );
            break;
            
        case 'participant':
            if ( CRM_Utils_Request::retrieve( 'history', 'Boolean', CRM_Core_DAO::$_nullObject ) ) {
                $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                              "reset=1&force=1&selectedChild=activity&cid={$this->_contactId}&history=1&aid={$activityId}" );     
            } else {
                $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                              "reset=1&force=1&cid={$this->_contactId}&selectedChild=participant" );
            }
            break;
            
        default:
            $cid = null;
            if ( $this->_contactId ) {
                $cid = '&cid=' . $this->_contactId;
            }
            $url = CRM_Utils_System::url( 'civicrm/event/search', 
                                          'reset=1&force=1' . $cid );
            break;
        }
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $url );
    }
}

?>
