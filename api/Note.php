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

// /**
//  * Definition of the Contact part of the CRM API. 
//  * More detailed documentation can be found 
//  * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
//  * here}
//  *
//  * @package CRM
//  * @author Donald A. Lobo <lobo@yahoo.com>
//  * @copyright Donald A. Lobo 01/15/2005
//  * $Id$
//  *
//  */

/**
 * Files required for this package
 */
require_once 'api/utils.php';

require_once 'CRM/Core/BAO/Note.php';

/**
 *
 */

function &crm_create_note( &$params ) {

    if ( empty( $params ) || empty($params['entity_table']) || empty($params['entity_id'])
         || empty($params['note']) || empty($params['contact_id']) ) {
        return _crm_error( 'Required Parameter(s) empty' );
    }
    $note =& new CRM_Core_DAO_Note( );
    
    if (!$params['modified_date']) {
        $params['modified_date']  = date("Ymd");
    }
    
    $note->copyValues( $params );
    $note->save( );
    return $note;
}

function &crm_get_note( &$params ) {
    
    if (empty( $params )) {
        return _crm_error( 'Required Parameter(s) not available' );
    }
    
    if ($params['id']) {
        $note =& new CRM_Core_DAO_Note( );
        $note->id = $params['id'];
        if ($note->find(true)) {
            return $note;
        }
    } else {
        if ($params['entity_table'] || $params['entity_id']) {
            $noteArray = array();
            $note =& new CRM_Core_DAO_Note( );
            $note->entity_table = $params['entity_table'];
            $note->entity_id = $params['entity_id'];
            $note->find();
            while ($note->fetch()) {
                $noteArray[] = clone($note);
            }
            return $noteArray;
        } else {
            return _crm_error( 'Required Parameter(s) not available' );
        }
    }
}

function &crm_delete_note( &$params ) {
    
    if (empty( $params )) {
        return _crm_error( 'Required Parameter(s) not available' );
    }
    
    if ($params['id']) {
        $deletedNotes = array();
        $note =& new CRM_Core_BAO_Note( );
        $note->id = $params['id'];
        if ($note->find(true)) {
            $deletedNotes[] = $note->id;
            $note->delete();
        }
        $deletedNotes['number'] = 1;
        return $deletedNotes;
    } else {
        if ($params['entity_table'] || $params['entity_id']) {
            $number = 0;
            $deletedNotes = array();
            $note =& new CRM_Core_BAO_Note( );
            $note->entity_table = $params['entity_table'];
            $note->entity_id = $params['entity_id'];
            $note->find();
            while ($note->fetch()) {
                $deletedNotes[] = $note->id;
                $note->delete();
                $number++;
            }
            $deletedNotes['number'] = $number;
            return $deletedNotes;
        } else {
            return _crm_error( 'Required Parameter(s) not available' );
        }
    }
}

function &crm_update_note( &$params ) {
    
    if (empty( $params )) {
        return _crm_error( 'Required Parameter(s) not available' );
    }
    
    if ($params['id']) {
        $note =& new CRM_Core_BAO_Note( );
        $note->id = $params['id'];
        if ($note->find(true)) {
            $note->copyValues( $params );
            if (!$params['modified_date']) {
                $note->modified_date = date("Ymd");
            }
            $note->save();
        }
        return $note;
    } else {
        return _crm_error( 'note-id not available' );
    }
}

?>
