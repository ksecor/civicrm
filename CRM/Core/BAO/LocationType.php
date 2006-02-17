<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/LocationType.php';


class CRM_Core_BAO_LocationType extends CRM_Core_DAO_LocationType {

    /**
     * static holder for the default LT
     */
    static $_defaultLocationType = null;


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
     * @return object CRM_Core_BAO_LocaationType object on success, null otherwise
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) {
        $locationType =& new CRM_Core_DAO_LocationType( );
        $locationType->copyValues( $params );
        if ( $locationType->find( true ) ) {
            CRM_Core_DAO::storeValues( $locationType, $defaults );
            return $locationType;
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
     * 
     * @access public
     * @static
     */
    static function setIsActive( $id, $is_active ) {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_LocationType', $id, 'is_active', $is_active );
    }
    
    /**
     * retrieve the default location_type
     * 
     * @param NULL
     * 
     * @return object           The default location type object on success,
     *                          null otherwise
     * @static
     * @access public
     */
    static function &getDefault() {
        if (self::$_defaultLocationType == null) {
            $params = array('is_default' => 1);
            $defaults = array();
            self::$_defaultLocationType = self::retrieve($params, $defaults);
        }
        return self::$_defaultLocationType;
    }
    
    /**
     * Function to delete location Types 
     * 
     * @param  int  $locationTypeId     ID of the location type to be deleted.
     * 
     * @access public
     * @static
     */
    static function del($locationTypeId) 
    {
        require_once 'CRM/Core/DAO/Location.php';
        require_once 'CRM/Core/DAO/Address.php';
        require_once 'CRM/Core/DAO/IM.php';
        require_once 'CRM/Core/DAO/Phone.php';
        require_once 'CRM/Core/DAO/Email.php';
        require_once 'CRM/Core/DAO/Location.php';

        //check dependencies
        $location = & new CRM_Core_DAO_Location();
        $location->location_type_id = $locationTypeId;
        $location->find();
        while($location->fetch()){
            //delete address
            $address  = & new CRM_Core_DAO_Address();
            $address->location_id = $location->id;
            $address->delete();
            //delete Im
            $im = & new CRM_Core_DAO_IM();
            $im->location_id = $location->id;
            $im->delete();
            //delete Phone 
            $phone = & new CRM_Core_DAO_Phone();
            $phone->location_id = $location->id;
            $phone->delete();
            //delete Email
            $email = & new CRM_Core_DAO_Email();
            $email->location_id = $location->id;
            $email->delete();
        }
        $location = & new CRM_Core_DAO_Location();
        $location->location_type_id = $locationTypeId;
        $location->delete();
        $locationType = & new CRM_Core_DAO_LocationType();
        $locationType->id = $locationTypeId;
        $locationType->delete();
    }
}
?>