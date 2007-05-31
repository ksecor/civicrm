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
function civicrm_event_create( &$params ) 
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
    
    $error = _civicrm_check_required_fields( $params, 'CRM_Event_DAO_Event' );
    if ($error['is_error']) {
        return civicrm_create_error( $error['error_message'] );
    }

    $ids['event'      ] = $params['id'];
    $ids['eventTypeId'] = $params['event_type_id'];
    $ids['startDate'  ] = $params['start_date'];
    $ids['event_id']            =  $params['event_id'];
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
    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array.');
    }
    if ( ! isset($params['event_id'])) {
        return civicrm_create_error('Required id (event ID) parameter is missing.');
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

function civicrm_event_search(&$params ) {

    $inputParams      = array( );
    $returnProperties = array( );
    $otherVars = array( 'sort', 'offset', 'rowCount' );
    
    $sort     = null;
    $offset   = 0;
    $rowCount = 25;
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 7 ) == 'return.' ) {
            $returnProperties[ substr( $n, 7 ) ] = 1;
        } elseif ( array_key_exists( $n, $otherVars ) ) {
            $$n = $v;
        } else {
            $inputParams[$n] = $v;
        }
    }
    require_once 'CRM/Contact/BAO/Query.php';
    require_once 'CRM/Event/BAO/Query.php';  
    if ( empty( $returnProperties ) ) {
        $returnProperties = CRM_Event_BAO_Query::defaultReturnProperties( CRM_Contact_BAO_Query::MODE_EVENT );
    }

    $newParams =& CRM_Contact_BAO_Query::convertFormValues( $params);

    $query =& new CRM_Contact_BAO_Query( $newParams, $returnProperties, null );
    list( $select, $from, $where ) = $query->query( );
    
    $sql = "$select $from $where"; 

    if ( ! empty( $sort ) ) {
        $sql .= " ORDER BY $sort ";
    }
    $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
    
    $event = array( );
    while ( $dao->fetch( ) ) {
        $event[$dao->event_id] = $query->store( $dao );
    }
  
 
    require_once 'CRM/Core/BAO/CustomGroup.php';
    $groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Event', $dao->event_id, false,1);
    CRM_Core_BAO_CustomGroup::setDefaults( $groupTree, $defaults, false, false ); 
    if ( is_array( $defaults ) ) {
      foreach ( $defaults as $key => $val ) {
	$event[$dao->event_id][$key] = $val;
      }
    }
    
 
    $dao->free( );
    return $event;

}


/**
 * Deletes an existing event
 * 
 * This API is used for deleting a event
 * 
 * @param  Int  $eventID    ID of event to be deleted
 * 
 * @return boolean        true if success, else false
 * @access public
 */
function &civicrm_event_delete( &$eventID ) {
    if ( ! $eventID ) {
        return civicrm_create_error( 'Invalid value for eventID' );
    }
    require_once 'CRM/Event/BAO/Event.php';
    $eventDelete = CRM_Event_BAO_Event::del($eventID);
    return $eventDelete ? null : civicrm_create_error('Error while deleting participant');
}
?>
