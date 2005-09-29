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

/** 
 *  this file contains functions for individual prefix
 */

class CRM_Core_BAO_IndividualPrefix extends CRM_Core_DAO_IndividualPrefix {

    /**
     * static holder for the default LT
     */
    static $_defaultIndividualPrefix = null;


    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
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
     * @return object CRM_Core_BAO_IndividualPrefix object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) {
        $individualPrefix =& new CRM_Core_DAO_IndividualPrefix( );
        $individualPrefix->copyValues( $params );
        if ( $individualPrefix->find( true ) ) {
            CRM_Core_DAO::storeValues( $individualPrefix, $defaults );
            return $individualPrefix;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_IndividualPrefix', $id, 'is_active', $is_active );
    }


    /**
     * retrieve the list of prefix
     *
     * @return object           The default activity type object on success,
     *                          null otherwise
     * @static
     * @access public
     */
    static function &getDefault() {
        if (self::$_defaultIndividualPrefix == null) {
            $defaults = array();
            self::$_defaultIndividualPrefix = self::retrieve($params, $defaults);
        }
        return self::$_defaultIndividualPrefix;
    }

    /**
     * function to add the individual prefix
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) {
        
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        
        // action is taken depending upon the mode
        $individualPrefix               =& new CRM_Core_DAO_IndividualPrefix( );
        $individualPrefix->domain_id    = CRM_Core_Config::domainID( );
        
        $individualPrefix->copyValues( $params );;
        
        $individualPrefix->id = CRM_Utils_Array::value( 'individualPrefix', $ids );
        $individualPrefix->save( );
        return $individualPrefix;
    }
    
    /**
     * Function to delete Gender Title
     * 
     * @param int $titleId
     * @static
     */
    
    static function del($titleId) 
    {
        //check dependencies
        $individual = & new CRM_Contact_DAO_Individual();
        $individual->prefix_id = $titleId;
        $individual->find();
        while($individual->fetch()) {
            $contactId = $individual->contact_id;
           
            $session =& CRM_Core_Session::singleton( );
            $currentUserId = $session->get( 'userID' );
                       if ($currentUserId !=$contactId) {
                CRM_Contact_BAO_Contact::deleteContact( $contactId );
          
            }else {
                return false;
            }
        }
        
        $prefix = & new CRM_Core_DAO_IndividualPrefix();
        $prefix->id = $titleId;
        $prefix->delete();
        return true;
    }

    
}

?>
