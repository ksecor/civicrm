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
            CRM_Core_DAO::commonRetrieve( 'CRM_Event_DAO_Participant', $params, $values, array( 'contact_id', 'event_id' ) );
        }
        
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
        $this->addButtons(array( 
                                array ( 'type'      => 'next',
                                        'name'      => ts('Confirm'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );  
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        //user want to walk through registration wizard.
        if ( $this->controller->exportValue( $this->_name, '_qf_ConfirmParticipation_next' ) ) {
            $eventId = $this->get( 'eventID' );
            $participnatId = $this->get( 'participantID' );
            $url = CRM_Utils_System::url( 'civicrm/event/register', "reset=1&id={$eventId}&participnatId={$participnatId}" );
            CRM_Utils_System::redirect( $url );
        }
    }
}

