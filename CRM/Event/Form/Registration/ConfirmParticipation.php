<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Event/Form/Registration.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration_ConfirmParticipation extends CRM_Event_Form_Registration
{
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess( ) 
    {
        $participnatId = CRM_Utils_Request::retrieve( 'participantId', 'Positive', $this );
        
        //get the contact and event id and assing to session.
        $values = array( );
        $csContactID = $eventId = null;
        $params = array('id' => $participnatId );
        if ( $participnatId ) {
            require_once 'CRM/Event/BAO/Participant.php';
            CRM_Core_DAO::commonRetrieve( 'CRM_Event_DAO_Participant', $params, $values, 
                                          array( 'contact_id', 'event_id', 'status_id' ) );
        }
        
        $this->_participnatStatusId = $values['status_id'];
        $eventId = CRM_Utils_Array::value( 'event_id', $values );
        $csContactID = CRM_Utils_Array::value( 'contact_id', $values );
        
        // make sure we have right permission to edit this user
        require_once 'CRM/Contact/BAO/Contact.php';
        if ( $csContactID && $eventId ) {
            $session =& CRM_Core_Session::singleton( );
            $this->set( 'eventID', $eventId );
            $this->set( 'participantID', $participnatId );
            if ( $csContactID != $session->get( 'userID' ) ) {
                require_once 'CRM/Contact/BAO/Contact/Permission.php';
                if ( CRM_Contact_BAO_Contact_Permission::validateChecksumContact( $csContactID, $this ) ) {
                    $session =& CRM_Core_Session::singleton( );
                    $session->set( 'userID', $csContactID );
                }
            }
        }
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $buttons = array( );
        require_once 'CRM/Event/PseudoConstant.php';
        // only pending status class family able to confirm.
        if ( array_key_exists( $this->_participnatStatusId, 
                               CRM_Event_PseudoConstant::participantStatus( null, "class = 'Pending'" ) ) ) {
            $buttons = array_merge( $buttons, array( array( 'type'      => 'next',
                                                            'name'      => ts('Confirm'), 
                                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                                            'isDefault' => true   ))); 
        }
        
        // status class other than Negative should able to cancel registration.
        if ( array_key_exists( $this->_participnatStatusId,
                               CRM_Event_PseudoConstant::participantStatus( null, "class != 'Negative'" ) ) ) {
            $buttons = array_merge( $buttons, array(array( 'type'    => 'submit',
                                                           'name'    => ts('Cancel the registration'),
                                                           'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')));
        }
        $buttons = array_merge( $buttons,  array( array ( 'type'     => 'cancel', 
                                                          'name'     => ts('Cancel') ) ) );
        $this->addButtons( $buttons );
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        //get the button.
        $buttonName = $this->controller->getButtonName( );
        $eventId = $this->get( 'eventID' );
        $participnatId = $this->get( 'participantID' );
        
        if ( $buttonName == '_qf_ConfirmParticipation_next' ) {
            //check user registration status is from pending class
            $url = CRM_Utils_System::url( 'civicrm/event/register', "reset=1&id={$eventId}&participnatId={$participnatId}" );
            CRM_Utils_System::redirect( $url );
        } else if ( $buttonName == '_qf_ConfirmParticipation_submit' ) {
            //need to registration status to 'cancelled'.
            require_once 'CRM/Event/PseudoConstant.php';
            $canceledId = array_search( 'Cancelled', CRM_Event_PseudoConstant::participantStatus( null, "class = 'Negative'" ) );
            
            //set status to cancelled 
            CRM_Core_DAO::setFieldValue( 'CRM_Event_DAO_Participant', $participnatId, 'status_id', $canceledId );
            
            $config =& CRM_Core_Config::singleton( );
            CRM_Core_Error::statusBounce( ts( 'Event registration have been canceled.' ), $config->userFrameworkBaseURL );
        }
    }
}

