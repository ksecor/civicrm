<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | http://www.civicrm.org/licensing/                                 |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of CRM API for Event.
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/utils.php';

/**
 * Create a Event Participants
 *  
 * This API is used for creating a Participants of Event.
 * Required parameters : event_id , participant_status_id, participant_role_id.
 * 
 * @param   array  $params     an associative array of name/value property values of civicrm_participant
 * @param   int    $contactID  ID of a contact
 * 
 * @return array of newly created membership property values.
 * @access public
 */
function crm_create_contact_participant($params, $contactID)
{
    _crm_initialize();
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['event_id']) || !isset($params['status_id']) || !isset($params['role_id']) || empty($contactID)) {
        return _crm_error( 'Required parameter missing' );
    }
    
    $params['contact_id'] = $contactID;
    
    require_once 'CRM/Event/BAO/Participant.php';
    $ids = array();
    $participantBAO = CRM_Event_BAO_Participant::add($params, $ids);
    
    $participant = array();
    _crm_object_to_array($participantBAO, $participant);
    return $participant;
}

?>