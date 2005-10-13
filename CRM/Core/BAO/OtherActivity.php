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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */
require_once 'CRM/Core/DAO/Activity.php';

/**
 * This class is for OtherActivity functions
 *
 */

class CRM_Core_BAO_OtherActivity extends CRM_Core_DAO_Activity 
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
     * @return object CRM_Core_BAO_OtherActivity object
     * @access public
     * @static
     */
    static function add( &$params, &$ids ) 
    {
        if ( ! self::dataExists( $params ) ) {
            return null;
        }

        $otherActivity =& new CRM_Core_DAO_Activity();
        
        $otherActivity->copyValues($params);
        
        $otherActivity->id = CRM_Utils_Array::value( 'otherActivity', $ids );

        return $otherActivity->save( );
        
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
     * @return object CRM_Core_BAO_OtherActivity object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $otherActivity =& new CRM_Core_DAO_Activity( );
        $otherActivity->copyValues( $params );
        if ( $otherActivity->find( true ) ) {
            CRM_Core_DAO::storeValues( $otherActivity, $defaults );
            return $otherActivity;
        }
        return null;
    }

    /**
     * Function to delete the otherActivity
     *
     * @param int $id otherActivity id
     *
     * @return null
     * @access public
     * @static
     *
     */
    static function del ( $id ) 
    {
        $otherActivity =& new CRM_Core_DAO_Activity( );
        $otherActivity->id = $id;
        $otherActivity->delete();
        CRM_Core_Session::setStatus( ts('Selected OtherActivity has been deleted.') );
    }


    /**
     * delete all records for this contact id
     *
     * @param int $id
     */
    public static function deleteContact($id)
    {
        // need to delete for both source and target
        $dao =& new CRM_Core_DAO_Activity();
        $dao->source_contact_id = $id;
        $dao->delete();

        $dao =& new CRM_Core_DAO_Activity();
        $dao->target_contact_id = $id;        
        $dao->delete();
    }


}

?>
