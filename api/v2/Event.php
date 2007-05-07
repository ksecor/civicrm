<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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
function civicrm_event_create( $params ) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array.');
    }
    
    if (!$params["title"] || ! $params['event_type_id'] || ! $params['start_date']) {
        return civicrm_create_error('Missing require fileds ( title, event type id,start date)');
    }
    
    if ( !$params['domain_id'] ) {
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton();
        $params['domain_id'] = $config->domainID();
    }
    
    $error = _crm_check_required_fields( $params, 'CRM_Event_DAO_Event');
    if ( is_a($error, 'CRM_Core_Error')  ) {
        return civicrm_create_error( "Event is not created" );
    }
    
    $ids['event'      ] = $params['id'];
    $ids['eventTypeId'] = $params['event_type_id'];
    $ids['startDate'  ] = $params['start_date'];
    
    require_once 'CRM/Event/BAO/Event.php';
    $eventBAO = CRM_Event_BAO_Event::create($params, $ids);
    
    $event = array();
    _civicrm_object_to_array($eventBAO, $event);

    if ( is_a( $event, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( "Event is not created" );
    } else {
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
function civicrm_event_search( $params ) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array.');
    }
    if ( ! isset($params['id'])) {
        return civicrm_create_error('Required id (event ID) parameter is missing.');
    }
    $query = "SELECT * FROM civicrm_event WHERE ";
    $count =0;
    foreach ( $params as $key => $value ) {
        $count++;
        if ( $count != 1) {       
            $query .= " AND ";
        }
        $query .= $key ." = '" . $value . "'" ;
    }
    $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
    if ( $dao->fetch( ) ) {
        return $dao;
    }
    return null;
}

/**
 * Update an existing event
 *
 * This api is used for updating an existing event.
 * Required parrmeters : id of a event
 * 
 * @param  Array   $params  an associative array of title/value property values of civicrm_event
 * 
 * @return array of updated event property values
 * @access public
 */
function &civicrm_event_update( $params ) {
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return civicrm_create_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Event/DAO/Event.php';
    $eventDAO =& new CRM_Event_DAO_Event( );
    $eventDAO->id = $params['id'];
    if ($eventDAO->find(true)) {
        $fields = $eventDAO->fields( );
        foreach ( $fields as $name => $field) {
            if (array_key_exists($name, $params)) {
                $eventDAO->$name = $params[$name];
            }
        }
        $eventDAO->save();
    }
    
    $event = array();
    _civicrm_object_to_array( $eventDAO, $event );
    return $event;
}

/**
 * Deletes an existing event
 * 
 * This API is used for deleting a event
 * 
 * @param  Int  $eventID    ID of event to be deleted
 * 
 * @return null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function &civicrm_event_delete( $eventID ) {
    if ( ! $eventID ) {
        return civicrm_create_error( 'Invalid value for eventID' );
    }
    require_once 'CRM/Event/BAO/Event.php';
    $eventDelete = CRM_Event_BAO_Event::del($eventID);
    return $eventDelete ? null : civicrm_create_error('Error while deleting participant');
}
?>
