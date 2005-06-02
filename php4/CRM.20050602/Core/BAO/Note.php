<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

define( 'CRM_CORE_BAO_NOTE_MAX_NOTES',3);

require_once 'CRM/Core/DAO/Note.php';
require_once 'CRM/Core/DAO/Note.php';

/**
 * BAO object for crm_note table
 */
class CRM_Core_BAO_Note extends CRM_Core_DAO_Note {

    /**
     * const the max number of notes we display at any given time
     * @var int
     */
       /**
     * given a note id, retrieve the note text
     * 
     * @param int $id id of the note to retrieve
     *
     * @return string the note text or null if note not found
     * @static
     *
     */
     function getNoteText( $id ) {
        $note = new CRM_Core_DAO_Note( );
        $note->id = $id;
        if ( $note->find( true ) ) {
            return $note->note;
        }
        return null;
    }

    /**
     * takes an associative array and creates a note object
     *
     * the function extract all the params it needs to initialize the create a
     * note object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Core_BAO_Note object
     * @access public
     * @static
     */
     function add( &$params ) 
    {
        $dataExists = CRM_Core_BAO_Note::dataExists( $params );
        if ( ! $dataExists ) {
            return null;
        }

        $note = new CRM_Core_BAO_Note( );
        
        $params['modified_date'] = date("Ymd");
        $params['table_id']      = $params['contact_id'];
        $params['table_name']    = 'crm_contact';

        $note->copyValues( $params );

        $note->contact_id = 1;
        $note->save( );

        return $note;
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
     function dataExists( &$params ) 
    {
        // return if no data present
        if ( ! strlen( $params['note']) ) {
            return false;
        } 
        return true;
     }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     * @param int   $numNotes      the maximum number of notes to return (0 if all)
     *
     * @return void
     * @access public
     * @static
     */
     function &getValues( &$params, &$values, &$ids, $numNotes = CRM_CORE_BAO_NOTE_MAX_NOTES) {
        $note = new CRM_Core_BAO_Note( );
       
        $note->table_id   = $params['contact_id'] ;        
        $note->table_name = 'crm_contact';

        // get the total count of notes
        $values['noteTotalCount'] = $note->count( );

        // get only 3 recent notes
        $note->orderBy( 'modified_date desc' );
        $note->limit( $numNotes );
        $note->find();

        $notes       = array( );
        $ids['note'] = array( );
        $count = 0;
        while ( $note->fetch() ) {
            $values['note'][$note->id] = array();
            $ids['note'][] = $note->id;
            
            $note->storeValues( $values['note'][$note->id] );

            $notes[] = $note;

            $count++;
            // if we have collected the number of notes, exit loop
            if ( $numNotes > 0 && $count >= $numNotes ) {
                break;
            }
        }
        
        return $notes;
    }


    /**
     * Function to delete the notes
     *
     * @param int $id note id
     *
     * @return null
     * @access public
     * @static
     *
     */
     function del ( $id ) {
        // delete from relationship table
        $note = new CRM_Core_DAO_Note( );
        $note->id = $id;
        $note->delete();
        
    }

    /**
     * Delete the object records that are associated with this contact
     *
     * @param  int  $contactId id of the contact to delete
     *
     * @return void
     * @access public
     * @static
     */
     function deleteContact( $contactId ) {
        $note = new CRM_Core_DAO_Note( );

        $note->table_name = 'crm_contact';
        $note->table_id   = $contactId;
        $note->delete( );
    }
}

?>