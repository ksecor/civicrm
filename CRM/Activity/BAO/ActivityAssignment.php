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

require_once 'CRM/Activity/DAO/ActivityAssignment.php';

/**
 * This class is for activity assignment functions
 *
 */
class CRM_Activity_BAO_ActivityAssignment extends CRM_Activity_DAO_ActivityAssignment
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
    public function addAssignment( $activity_id, $assignee_contact_id ) {
        $this->activity_id = $activity_id;
        $this->assignee_contact_id = $assignee_contact_id;
        return $this->save();
    }


    /**
     * Delete record for activity id in activity_assignment
     *
     * @param int    $id  ID of the activity for which the records needs to be deleted.
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    public function removeAssignment( $id )
    {
        $this->id = $id;
        if( $this->find( true ) ) {
            return $this->delete();
        }
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
     * @static
     */
    public function updateAssignment( $id, $assignee_contact_id )
    {
        $this->id = $id;
        if( $this->find( true ) ) {
            $this->assignee_contact_id = $assignee_contact_id;
            return $this->save();
        }
    }




    public function retrieveAssigneeIdByActivityId( $activity_id ) 
    {
        $this->activity_id = $activity_id;
        if ( $this->find( true ) ) {
            return $this->assignee_contact_id;
        }
        return null;
    }
    


}

?>
