<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/Note.php';

/**
 * BAO object for crm_note table
 */
class CRM_Core_BAO_Note extends CRM_Core_DAO_Note {

    /**
     * const the max number of notes we display at any given time
     * @var int
     */
    const MAX_NOTES = 3;
    
    /**
     * given a note id, retrieve the note text
     * 
     * @param int  $id   id of the note to retrieve
     * 
     * @return string   the note text or null if note not found
     * 
     * @access public
     * @static
     */
    static function getNoteText( $id ) {
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Note', $id, 'note' );
    }
    
    /**
     * given a note id, retrieve the note subject
     * 
     * @param int  $id   id of the note to retrieve
     * 
     * @return string   the note subject or null if note not found
     * 
     * @access public
     * @static
     */
    static function getNoteSubject( $id ) {
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Note', $id, 'subject' );
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
    static function &add( &$params , $ids) 
    {
        $dataExists = self::dataExists( $params );
        if ( ! $dataExists ) {
            return null;
        }

        $note =& new CRM_Core_BAO_Note( );
        
        $params['modified_date']  = date("Ymd");
       
        $note->copyValues( $params );

        $session =& CRM_Core_Session::singleton( );
        $note->contact_id = $session->get( 'userID' );
        if ( ! $note->contact_id ) {
            if ( $params['entity_table'] == 'civicrm_contact' ) {
                $note->contact_id = $params['entity_id'];
            } else {
                CRM_Utils_System::statusBounce(ts('We could not find your logged in user ID'));
            }
        }
        if( $ids ["id"] )  {
            $note->id = $ids ["id"];
        }
        
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
    static function dataExists( &$params ) 
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
     * @return object   Object of CRM_Core_BAO_Note
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids, $numNotes = self::MAX_NOTES ) {
        $note =& new CRM_Core_BAO_Note( );
       
        $note->entity_id    = $params['contact_id'] ;        
        $note->entity_table = 'civicrm_contact';

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
            
            CRM_Core_DAO::storeValues( $note, $values['note'][$note->id] );

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
    static function del ( $id ) {
        // delete from relationship table
        $note =& new CRM_Core_DAO_Note( );
        $note->id = $id;
        $note->delete();
        CRM_Core_Session::setStatus( ts('Selected Note has been Deleted Successfuly.') );        
    }

    /**
     * delete all records for this contact id
     * 
     * @param int  $id    ID of the contact for which records needs to be deleted.
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    public static function deleteContact($id)
    {
        // need to delete for both entity_id
        $dao =& new CRM_Core_DAO_Note();
        $dao->entity_table = 'civicrm_contact';
        $dao->entity_id   = $id;
        $dao->delete();

        // and the creator contact id
        $dao =& new CRM_Core_DAO_Note();
        $dao->contact_id = $id;        
        $dao->delete();
    }

    /**
     * retrieve all records for this entity-id
     * 
     * @param int  $id ID of the relationship for which records needs to be retrieved.
     * 
     * @return array    Array of note properties
     * 
     * @access public
     * @static
     */
    public static function &getNote($id)
    {
        $viewNote = array();

        $query = "
SELECT   id, note FROM civicrm_note
WHERE    entity_table = 'civicrm_relationship' 
  AND    entity_id = %1
ORDER BY modified_date desc";
        $params = array( 1 => array( $id, 'Integer' ) );

        $dao =& CRM_Core_DAO::executeQuery( $query, $params );

        while ( $dao->fetch() ) {
            $viewNote[$dao->id] = $dao->note;
        }
        return $viewNote;
    }
}
?>