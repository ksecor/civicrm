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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * This class is for Meeting functions
 *
 */

class CRM_Core_BAO_Meeting extends CRM_Core_DAO_Meeting 
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }

   /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $ids            the array that holds all the db ids
     *
     * @return object CRM_Core_BAO_Meeting object
     * @access public
     * @static
     */
    static function add( &$params, &$ids ) 
    {
        if ( ! self::dataExists( $params ) ) {
            return null;
        }

        $meeting =& new CRM_Core_DAO_Meeting();
        
        $meeting->copyValues($params);
        
        $meeting->id = CRM_Utils_Array::value( 'meeting', $ids );

        return $meeting->save( );
        
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
        if (CRM_Utils_Array::value( 'subject', $params)) {
            return true;
        }
        return false;
    }


    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Core_BAO_Meeting object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $meeting =& new CRM_Core_DAO_Meeting( );
        $meeting->copyValues( $params );
        if ( $meeting->find( true ) ) {
            CRM_Core_DAO::storeValues( $meeting, $defaults );
            return $meeting;
        }
        return null;
    }

    /**
     * Function to delete the meeting
     *
     * @param int $id meeting id
     *
     * @return null
     * @access public
     * @static
     *
     */
    static function del ( $id ) 
    {
        $meeting =& new CRM_Core_DAO_Meeting( );
        $meeting->id = $id;
        $meeting->delete();
        CRM_Core_Session::setStatus( ts('Selected Meeting has been deleted.') );
    }


    /**
     * delete all records for this contact id
     *
     * @param int $id
     */
    public static function deleteContact($id)
    {
        // need to delete for both source and target
        $dao = new CRM_Core_DAO_Meeting();
        $dao->source_contact_id = $id;
        $dao->delete();

        $dao = new CRM_Core_DAO_Meeting();
        $dao->target_entity_table = 'civicrm_contact';
        $dao->target_entity_id    = $id;        
        $dao->delete();
    }


}

?>
