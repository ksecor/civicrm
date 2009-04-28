<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                               |
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
class CRM_Event_Form_Registration_ParticipantConfirm extends CRM_Event_Form_Registration
{
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess( ) 
    {
        $this->_participantId = CRM_Utils_Request::retrieve( 'participantId', 'Positive', $this );
        
        //get the contact and event id and assing to session.
        $values = array( );
        $csContactID = $eventId = null;
        if ( $this->_participantId ) {
            require_once 'CRM/Event/BAO/Participant.php';
            $params = array('id' => $this->_participantId );
            CRM_Core_DAO::commonRetrieve( 'CRM_Event_DAO_Participant', $params, $values, 
                                          array( 'contact_id', 'event_id', 'status_id' ) );
        }
        
        $this->_participantStatusId = $values['status_id'];
        $this->_eventId = CRM_Utils_Array::value( 'event_id', $values );
        $csContactID = CRM_Utils_Array::value( 'contact_id', $values );
        
        // make sure we have right permission to edit this user
        require_once 'CRM/Contact/BAO/Contact.php';
        if ( $csContactID && $this->_eventId ) {
            $session =& CRM_Core_Session::singleton( );
            if ( $csContactID != $session->get( 'userID' ) ) {
                require_once 'CRM/Contact/BAO/Contact/Permission.php';
                if ( CRM_Contact_BAO_Contact_Permission::validateChecksumContact( $csContactID, $this ) ) {
                    $session =& CRM_Core_Session::singleton( );
                    $session->set( 'userID', $csContactID );
                }
            }
        } else {
            $config =& CRM_Core_Config::singleton( );
            CRM_Core_Error::statusBounce( ts( 'You do not have permission to access this event registration. Contact the site administrator if you need assistance.' ),$config->userFrameworkBaseURL );
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
        if ( array_key_exists( $this->_participantStatusId, 
                               CRM_Event_PseudoConstant::participantStatus( null, "class = 'Pending'" ) ) ) {
            
            //need to confirm that though participant confirming
            //registration but is there enough space to confirm.
            require_once 'CRM/Event/PseudoConstant.php';
            require_once 'CRM/Event/BAO/Participant.php';
            $emptySeats = CRM_Event_BAO_participant::pendingToConfirmSpaces( $this->_eventId );
            $additonalIds = CRM_Event_BAO_participant::getAdditionalParticipantIds( $this->_participantId );
            $requireSpace = 1 + count( $additonalIds );
            if ( $emptySeats !== null && ( $requireSpace > $emptySeats ) ) {
                CRM_Core_Session::setStatus( ts( "Oops it's looks like there are no enough space for your event registration." ) );
            } else {
                $buttons = array_merge( $buttons, array( array( 'type'      => 'next',
                                                                'name'      => ts('Confirm'), 
                                                                'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                                                'isDefault' => true   ))); 
            }
        }
        
        // status class other than Negative should able to cancel registration.
        if ( array_key_exists( $this->_participantStatusId,
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
        $eventId = $this->_eventId;
        $participantId = $this->_participantId;
        
        if ( $buttonName == '_qf_ParticipantConfirm_next' ) {
            //check user registration status is from pending class
            $url = CRM_Utils_System::url( 'civicrm/event/register', "reset=1&id={$eventId}&participantId={$participantId}" );
            CRM_Utils_System::redirect( $url );
        } else if ( $buttonName == '_qf_ParticipantConfirm_submit' ) {
            //need to registration status to 'cancelled'.
            require_once 'CRM/Event/PseudoConstant.php';
            require_once 'CRM/Event/BAO/Participant.php';
            $cancelledId = array_search( 'Cancelled', CRM_Event_PseudoConstant::participantStatus( null, "class = 'Negative'" ) );
            $additionalParticipantIds = CRM_Event_BAO_Participant::getAdditionalParticipantIds( $participantId );
            
            $participantIds = array_merge( array( $participantId ), $additionalParticipantIds );
            $results = CRM_Event_BAO_Participant::transitionParticipants( $participantIds, null, $cancelledId, true );
            
            $statusMessage = ts( "%1 Event registration(s) has been cancelled.", array( 1 => count( $participantIds ) ) );
            if ( CRM_Utils_Array::value( 'mailedParticipants', $results ) ) {
                foreach ( $results['mailedParticipants'] as $key => $displayName ) {
                    $statusMessage .=  ts( "<br>Mail has been sent to : %1", array( 1 => $displayName ) );
                }
            }
            
            $config =& CRM_Core_Config::singleton( );
            CRM_Core_Error::statusBounce( $statusMessage, $config->userFrameworkBaseURL );
        }
    }
}

