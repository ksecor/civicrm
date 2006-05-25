<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/utils.php';

require_once 'CRM/Core/BAO/Note.php';

/**
 * Creates a new note
 *
 * This api is used to create a note record for an existing contact.
 * 'entity_table', 'entity_id', 'note' and 'contact_id' are the required parameters.
 * 
 * @param array $params  Associative array of name/value property.
 * 
 * @return Array of all Note property values.
 *
 * @access public
 */

function &crm_create_note( &$params ) {
    if ( !is_array($params) ) {
        return _crm_error( 'params is not an array' );
    }
    if ( !isset($params['entity_table']) || 
         !isset($params['entity_id'])    || 
         !isset($params['note'])         || 
         !isset($params['contact_id'] ) ) {
        return _crm_error( 'Required Parameter(s) missing.' );
    }
    $noteBAO =& new CRM_Core_BAO_Note( );
    
    if ( !isset($params['modified_date']) ) {
        $params['modified_date']  = date("Ymd");
    }
    
    $noteBAO->copyValues( $params );
    $noteBAO->save( );
    
    $properties = array('id', 'entity_table', 'entity_id', 'note', 'contact_id', 'modified_date', 'subject');
    foreach ($properties as $name) {
        if ( array_key_exists($name, $noteBAO) ) {
            $createdNote[$name] = $noteBAO->$name;
        }
    }
    return $createdNote;
}

/**
 * Retrieves required note properties, if exists 
 *
 * This api is used to retrieve details of an existing note record.
 * Required Parameters :
 *      1. id OR
 *      2. entity_id and entity_table
 *
 * @param array $params  Associative array of name/value property
 * 
 * @return Array of requierd Note object(s)
 * @access public
 */

function &crm_get_note( &$params ) {
    if ( ! is_array($params) ) {
        return _crm_error( 'Params is not an array.' );
    }
    
    if ( ! isset($params['id']) && ( ! isset($params['entity_id']) || ! isset($params['entity_table']) ) ) {
        return _crm_error( 'Required parameters missing.' );
    }
    $noteBAO =& new CRM_Core_BAO_Note( );
    
    $properties = array('id', 'entity_table', 'entity_id', 'note', 'contact_id', 'modified_date', 'subject');
    
    foreach ($properties as $name) {
        if ( array_key_exists($name, $params) ) {
            $noteBAO->$name = $params[$name];
        }
    }
    
    if ( $noteBAO->find() ) {
        while ($noteBAO->fetch()) {
            $noteArray[] = clone($noteBAO);
        }
        return $noteArray;
    } else {
        return _crm_error( 'Exact match not found.' );
    }
}

/**
 * Deletes a note record. 
 *
 * This api is used to delete an existing note record.
 * 
 * Required Parameters :
 *      1. id OR
 *      2. entity_id and entity_table
 * 
 * @param array $params  Associative array of property name/value pairs, sufficient to delete a note. 
 * 
 * @return number of notes deleted if successfull or CRM_Core_Error otherwise.
 * 
 * @access public
 */

function &crm_delete_note( &$params ) {
    
    if ( ! is_array( $params )) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) && ( !isset($params['entity_id']) || !isset($params['entity_table']) ) ) {
        return _crm_error( 'Required parameter(s) missing' );
    }
    
    $noteBAO =& new CRM_Core_BAO_Note( );
    
    $properties = array('id', 'entity_table', 'entity_id', 'note', 'contact_id', 'modified_date', 'subject');
    
    foreach ($properties as $name) {
        if ( array_key_exists($name, $params) ) {
            $noteBAO->$name = $params[$name];
        }
    }
    
    if ( $noteBAO->find() ) {
        $notesDeleted = $noteBAO->delete();
        return $notesDeleted;
    } else {
        return _crm_error( 'Exact match not found.' );
    }
}

/**
 * Updates a note record. 
 *
 * This api is used to update an existing note record.
 * 'id' of the note-record to be updated is the required parameter.
 *
 * @param array $params  Associative array of property name/value pairs with new values to be updated with. 
 * 
 * @return Array of all Note property values (updated).
 *
 * @access public
 */
function &crm_update_note( &$params ) {
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    $noteBAO =& new CRM_Core_BAO_Note( );
    $noteBAO->id = $params['id'];
    if ($noteBAO->find(true)) {
        $noteBAO->copyValues( $params );
        if ( !$params['modified_date'] && !$noteBAO->modified_date) {
            $noteBAO->modified_date = date("Ymd");
        }
        $noteBAO->save();
    }
    
    $properties = array('id', 'entity_table', 'entity_id', 'note', 'contact_id', 'modified_date', 'subject');
    foreach ($properties as $name) {
        if ( array_key_exists($name, $noteBAO) ) {
            $updatedNote[$name] = $noteBAO->$name;
        }
    }
    return $updatedNote;
}
?>