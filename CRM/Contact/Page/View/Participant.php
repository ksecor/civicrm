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

class CRM_Contact_Page_View_Participant extends CRM_Contact_Page_View 
{
    /**
     * The action links that we need to display for the edit and view screen
     *
     * @var array
     * @static
     */
    static $_links = null;
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
        if ( CRM_Utils_Request::retrieve( 'history', 'Boolean', $this ) ) {
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

        if ( $this->_permission == CRM_Core_Permission::EDIT && ! CRM_Core_Permission::check( 'edit event participants' ) ) {
            $this->_permission = CRM_Core_Permission::VIEW; // demote to view since user does not have edit event participants rights
            $this->assign( 'permission', 'view' );
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
     * @form array $form (ref.) an assoc array of name/value pairs
     * return null 
     * @access public 
     */
    function associatedContribution( &$form )
    {
        require_once 'CRM/Event/BAO/ParticipantPayment.php';
        $particpant =& new CRM_Event_BAO_ParticipantPayment( );
        $particpant->participant_id = $form['id'];
        $ids = array( );
        $defaults = array( );
        $permission = CRM_Core_Permission::VIEW;
        if ( CRM_Core_Permission::check( 'edit contributions' ) ) {
            $permission = CRM_Core_Permission::EDIT;
        }
        $mask = CRM_Core_Action::mask( $permission );
        
        if ( $particpant->find( true ) ) {
            $this->_online = true;
            require_once 'CRM/Contribute/BAO/Contribution.php';
            require_once 'CRM/Contribute/PseudoConstant.php';
            require_once 'CRM/Event/Form/Participant.php';
            $params = array( 'id' => $particpant->contribution_id );
            CRM_Contribute_BAO_Contribution::getValues( $params, $defaults, $ids );
            $conType   = CRM_Contribute_PseudoConstant::contributionType( );
            $conStatus = CRM_Contribute_PseudoConstant::contributionStatus( );
            $defaults['contributionType']   = $conType[$defaults['contribution_type_id']];
            $this->_contributionType = $defaults['contribution_type_id'];
            $defaults['contributionStatus'] = $conStatus[$defaults['contribution_status_id']];
            $defaults['action']             = CRM_Core_Action::formLink( self::links('all' ), $mask, 
                                                                         array('id' => $defaults['id'], 
                                                                               'cid'=> $defaults['contact_id'] ));
            $this->assign('contribution',$defaults);
        }
    }

    /**
     * Get action links
     *
     * @return array (reference) of action links
     * @access public
     */
    function &links( $status = 'all' )
    { 
        if ( ! CRM_Utils_Array::value( 'view', self::$_links ) ) { 
            self::$_links['view'] = array(
                                          CRM_Core_Action::VIEW    => array(
                                                                            'name'  => ts('View'),
                                                                            'url'   => 'civicrm/contact/view/contribution',
                                                                            'qs'    => 'reset=1&id=%%id%%&cid=%%cid%%&action=view&context=contribution&selectedChild=participant',
                                                                            'title' => ts('View Contribution')
                                                                            ),
                                          );
        }
        
        if ( ! CRM_Utils_Array::value( 'all', self::$_links ) ) {
            $extraLinks = array(
                                CRM_Core_Action::UPDATE => array(
                                                                 'name'  => ts('Edit'),
                                                                 'url'   => 'civicrm/contact/view/contribution',
                                                                 'qs'    => 'reset=1&action=update&cid=%%cid%%&id=%%id%%&context=contribution&subType='.$this->_contributionType,
                                                                 'title' => ts('Edit Contribution')
                                                                 ),
                                CRM_Core_Action::DELETE => array(
                                                                 'name'  => ts('Delete'),
                                                                 'url'   => 'civicrm/contact/view/contribution',
                                                                 'qs'    => 'reset=1&action=delete&cid=%%cid%%&id=%%id%%&context=contribution',
                                                                 'title' => ts('Delete Membership')
                                                                 ),
                                );
            self::$_links['all'] = self::$_links['view'] + $extraLinks;
        }
        return self::$_links[$status];
    }


}


