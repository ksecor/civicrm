<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Activity/DAO/ActivityTarget.php';

/**
 * This class is for activity assignment functions
 *
 */
class CRM_Activity_BAO_ActivityTarget extends CRM_Activity_DAO_ActivityTarget
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }

    /**
     * funtion to add activity assignment
     *
     * @param array  $params       (reference ) an assoc array of name/value pairs
     * @param array  $ids          (reference ) the array that holds all the db ids
     * @param array  $activityType activity type  
     *
     * @return object activity type of object that is added
     * @access public
     * @static
     */
    public function add( $activity_id, $target_contact_id ) {
        $this->activity_id = $activity_id;
        $this->target_contact_id = $target_contact_id;
        return $this->save();
    }


    function retrieveTargetIdByActivityId( $activity_id ) 
    {
        $this->activity_id = $activity_id;
        if ( $this->find( true ) ) {
            return $this->target_contact_id;
        }
        return null;
    }
                                                    

    /**
     * takes an associative array and creates a Activity Assignment object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @access public
     * @static
     */
    static function &createActivityTarget(&$params , $ids ) 
    {         
    }
    /**
     * delete record for activity id in activity_assignment
     *
     * @param int    $id  ID of the activity for which the records needs to be deleted.
     * @param string $entityTable entity table name 
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    public static function remove( $id )
    {
      require_once 'CRM/Activity/DAO/ActivityTarget.php';
        $activityAssign =  new CRM_Activity_DAO_ActivityTarget();
        $activityAssign->activity_entity_table = $entityTable;
        $activityAssign->activity_entity_id = $id;
        if ($activityAssign->find(true)){
            return $activityAssign->delete();
        }
    }

}

?>
