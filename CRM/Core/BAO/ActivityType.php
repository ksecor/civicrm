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

require_once 'CRM/Core/DAO/ActivityType.php';

class CRM_Core_BAO_ActivityType extends CRM_Core_DAO_ActivityType 
{

    /**
     * static holder for the default LT
     */
    static $_defaultActivityType = null;
    

    /**
     * class constructor
     */
    function __construct( ) 
    {
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
     * @return object CRM_Core_BAO_ActivityType object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $activityType =& new CRM_Core_DAO_ActivityType( );
        $activityType->copyValues( $params );
        if ( $activityType->find( true ) ) {
            CRM_Core_DAO::storeValues( $activityType, $defaults );
            return $activityType;
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
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_ActivityType', $id, 'is_active', $is_active );
    }


    /**
     * retrieve the default activity_type
     *
     * @param NULL
     * 
     * @return object           The default activity type object on success,
     *                          null otherwise
     * @static
     * @access public
     */
    static function &getDefault() 
    {
        if (self::$_defaultActivityType == null) {
            $params = array('is_default' => 1);
            $defaults = array();
            self::$_defaultActivityType = self::retrieve($params, $defaults);
        }
        return self::$_defaultActivityType;
    }

    /**
     * retrieve the id and decription
     *
     * @param NULL
     * 
     * @return Array            id and decription
     * @static
     * @access public
     */
    static function &getActivityDescription() 
    {
        $query = "SELECT id ,description FROM civicrm_activity_type WHERE is_active = 1 AND id > 3 ORDER BY name";
        $dao   =& new CRM_Core_DAO_ActivityType();
        $dao->query($query);
        $description =array();
        while($dao->fetch()) {
            $description[ $dao->id] = $dao->description;

        }
       
       
        return $description;
    }

    /**
     * function to add the activity types
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) 
    {
        
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        $params['is_default'] =  CRM_Utils_Array::value( 'is_default', $params, false );
        
        // action is taken depending upon the mode
        $activityType               =& new CRM_Core_DAO_ActivityType( );
        $activityType->domain_id    = CRM_Core_Config::domainID( );
        
        $activityType->copyValues( $params );;
        
        if ($params['is_default']) {
            $unsetDefault =& new CRM_Core_DAO();
            $query = 'UPDATE civicrm_activity_type SET is_default = 0';
            $unsetDefault->query($query);
        }
        
        $activityType->id = CRM_Utils_Array::value( 'activityType', $ids );
        $activityType->save( );
        return $activityType;
    }
    
    /**
     * Function to delete activity Types 
     * 
     * @param  int  $activityTypeId     Id of the activity type to be deleted.
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    static function del($activityTypeId) 
    {
        require_once 'CRM/Core/DAO/Activity.php';
        //check dependencies
        
        $activityIds = array( );
        //get the list of activities from civicrm_activity for this activity type
        $activity =& new CRM_Core_DAO_Activity( );
        $activity->activity_type_id = $activityTypeId;
        $activity->find();
        while ($activity->fetch()) {
            $activityIds[$activity->id] = $activity->id;
        }
        
        foreach ($activityIds as $key) {
            //delete from civicrm_activity
            $activity->id = $key;
            $activity->delete();
        }
        
        //delete from civicrm_activity_history
        /*      
        $activityHistory =& new CRM_Core_DAO_ActivityHistory( );
        $activityHistory->activity_id = $activityId;
        $activityHistory->delete();
        */

        //delete from activity Type table
        $activityType =& new CRM_Core_DAO_ActivityType( );
        $activityType->id = $activityTypeId;
        $activityType->delete();
    }
}

?>
