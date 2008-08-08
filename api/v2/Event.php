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
 * Definition of CRM API for Event.
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/v2/utils.php';

/**
 * Create a Event
 *  
 * This API is used for creating a Event
 * 
 * @param   array  $params  an associative array of title/value property values of civicrm_event
 * 
 * @return array of newly created event property values.
 * @access public
 */
function civicrm_event_create( &$params ) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array');
    }
    
    if (!$params["title"] || ! $params['event_type_id'] || ! $params['start_date']) {
        return civicrm_create_error('Missing require fields ( title, event type id,start date)');
    }
    
    $error = _civicrm_check_required_fields( $params, 'CRM_Event_DAO_Event' );
    if ($error['is_error']) {
        return civicrm_create_error( $error['error_message'] );
    }
    
    // Do we really want $params[id], even if we have
    // $params[event_id]? if yes then please uncomment the below line 
    
    //$ids['event'      ] = $params['id'];
    
    $ids['eventTypeId'] = $params['event_type_id'];
    $ids['startDate'  ] = $params['start_date'];
    $ids['event_id']    = $params['event_id'];
    
    require_once 'CRM/Event/BAO/Event.php';
    $eventBAO = CRM_Event_BAO_Event::create($params, $ids);
    
    if ( is_a( $eventBAO, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( "Event is not created" );
    } else {
        $event = array();
        _civicrm_object_to_array($eventBAO, $event);
        $values = array( );
        $values['event_id'] = $event['id'];
        $values['is_error']   = 0;
    }
    
    return $values;
}

/**
 * Get an Event.
 * 
 * This api is used to retrieve all data for an existing Event.
 * Required parameters : id of event
 * 
 * @params  array $params  an associative array of title/value property values of civicrm_event
 * 
 * @return  If successful array of event data; otherwise object of CRM_Core_Error.
 * @access public
 */
function civicrm_event_get( &$params ) 
{
    _civicrm_initialize();
    
    if ( ! is_array( $params ) || empty( $params ) ) {
        return civicrm_create_error('Params is not an array');
    }
    
    $event  =& civicrm_event_search( $params );
    
    if ( count( $event ) != 1 &&
         ! $event['returnFirst'] ) {
        return civicrm_create_error( ts( '%1 event matching input params', array( 1 => count( $event ) ) ) );
    }
    
    if ( civicrm_error( $event ) ) {
        return $event;
    }
    
    $event = array_values( $event );
    return $event[0];
}
/**
 * Get Event record.
 * 
 *
 * @params  array  $params     an associative array of name/value property values of civicrm_event
 *
 * @return  Array of all found event property values.
 * @access public
 */  

function civicrm_event_search( &$params ) 
{
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 7 ) == 'return.' ) {
            $returnProperties[ substr( $n, 7 ) ] = 1;
        }
    }
    require_once 'CRM/Core/BAO/CustomGroup.php';
    require_once 'CRM/Event/BAO/Event.php';
    $eventDAO = new CRM_Event_BAO_Event( );
    $eventDAO->copyValues( $params );
    
    $eventDAO->find( );
    
    $event = array( );
    while ( $eventDAO->fetch( ) ) {
        $event[$eventDAO->id] = array( );
        CRM_Core_DAO::storeValues( $eventDAO, $event[$eventDAO->id] );
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Event', $eventDAO->id, false, $eventDAO->event_type_id );
        $defaults = array( );
        CRM_Core_BAO_CustomGroup::setDefaults( $groupTree, $defaults );
        if ( !empty( $defaults ) ) {
            foreach ( $defaults as $key => $val ) {
                $event[$eventDAO->id][$key] = $val;
            }
        }
    } //end of the loop
    $eventDAO->free( );
    return $event;
}


/**
 * Deletes an existing event
 * 
 * This API is used for deleting a event
 * 
 * @param  Array  $params    array containing event_id to be deleted
 * 
 * @return boolean        true if success, error otherwise
 * @access public
 */
function &civicrm_event_delete( &$params ) 
{
    if ( empty( $params ) ) {
        return civicrm_create_error( ts( 'No input parameters present' ) );
    }
    
    $eventID = null;
    
    $eventID = CRM_Utils_Array::value( 'event_id', $params );
    
    if ( ! isset( $eventID ) ) {
        return civicrm_create_error( ts( 'Invalid value for eventID' ) );
    }
    
    require_once 'CRM/Event/BAO/Event.php';
    
    return CRM_Event_BAO_Event::del( $eventID ) ?  civicrm_create_success( ) : civicrm_create_error( ts( 'Error while deleting event' ) );
}

