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
     * funtion to add activity target
     *
     * @param array  $activity_id           (reference ) an assoc array of name/value pairs
     * @param array  $target_contact_id     (reference ) the array that holds all the db ids
     *
     * @return object activity type of object that is added
     * @access public
     * 
     */
    public function createTarget( $activity_id, $target_contact_id ) 
    {
        $this->activity_id = $activity_id;
        $this->target_contact_id = $target_contact_id;
        return $this->save();
    }

    /**
     * Update record for activity id in activity_assignment
     *
     * @param int    $id  ID of the activity for which the records needs to be deleted.
     * @param int    $assignee_contact_id contact id for assignee
     * 
     * @return void
     * 
     * @access public
     * 
     */
    public function updateTarget( $id, $target_contact_id )
    {  
        $this->id = $id;
        if( $this->find( true ) ) {
            $this->target_contact_id = $target_contact_id;
            return $this->save();
        }
    }


    /**
     * function to remove activity target
     *
     * @param int    $id  ID of the activity for which the records needs to be deleted.
     * 
     * @return void
     * 
     * @access public
     * 
     */
    public function removeTarget( $id )
    {
        $this->id = $id;
        if( $this->find( true ) ) {
            return $this->delete();
        }
    }

    /**
     * function to retrieve id of target contact by activity_id
     *
     * @param int    $id  ID of the activity
     * 
     * @return mixed
     * 
     * @access public
     * 
     */
    public function retrieveTargetIdByActivityId( $activity_id ) 
    {
        $this->activity_id = $activity_id;
        if ( $this->find( true ) ) {
            return $this->target_contact_id;
        }
        return null;
    }

}

?>
