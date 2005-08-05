<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * This class contains functions for managing Tag(tag) for a contact
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Core_BAO_EntityTag extends CRM_Core_DAO_EntityTag 
{

    /**
     *
     * Given a contact id, it returns an array of tag id's the 
     * contact belongs to.
     *
     * @param string $entityTable name of the entity table usually 'civicrm_contact'
     * @param int $entityID id of the entity usually the contactID.
     * @return array() reference $tag array of catagory id's the contact belongs to.
     *
     * @access public
     * @static
     */
    static function &getTag($entityTable = 'civicrm_contact', $entityID) 
    {
        $tag = array();

        $entityTag =& new CRM_Core_BAO_EntityTag();
        $entityTag->entity_table = $entityTable;
        $entityTag->entity_id = $entityID;
        $entityTag->find();

        while ($entityTag->fetch()) {
            $tag[$entityTag->tag_id] = $entityTag->tag_id;
        } 
        return $tag;        
    }

    /**
     * takes an associative array and creates a entityTag object
     *
     * the function extract all the params it needs to initialize the create a
     * group object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Core_BAO_EntityTag object
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
       
        $dataExists = self::dataExists( $params );
        if ( ! $dataExists ) {
            return null;
        }

        $entityTag =& new CRM_Core_BAO_EntityTag( );
        $entityTag->copyValues( $params );
        $entityTag->save( );
        return $entityTag;
    }

    /**
     * Check if there is data to create the object
     *
     * @params array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        return ($params['tag_id'] == 0) ? false : true;
     }

    /**
     * Function to delete the tag for a contact
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Core_BAO_EntityTag object
     * @access public
     * @static
     *
     */
    static function del( &$params ) 
    {
        $entityTag =& new CRM_Core_BAO_EntityTag( );
        $entityTag->copyValues( $params );
        $entityTag->delete( );
        return $entityTag;
    }


    /**
     * Given an array of contact ids, add all the contacts to the tags 
     *
     * @param array  $contactIds (reference ) the array of contact ids to be added
     * @param int    $tagId the id of the tag
     *
     * @return array             (total, added, notAdded) count of contacts added to group
     * @access public
     * @static
     */
    static function addContactsToTag( &$contactIds, $tagId ) {
        $numContactsAdded    = 0;
        $numContactsNotAdded = 0;

        foreach ( $contactIds as $contactId ) {
            $tag =& new CRM_Core_DAO_EntityTag( );
            
            $tag->entity_id    = $contactId;
            $tag->entity_table = 'civicrm_contact';
            $tag->tag_id  = $tagId;
            if ( ! $tag->find( ) ) {
                $tag->save( );
                $numContactsAdded++;
            } else {
                $numContactsNotAdded++;
            }
        }

        return array( count($contactIds), $numContactsAdded, $numContactsNotAdded );
    }


}

?>
