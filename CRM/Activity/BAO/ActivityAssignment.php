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
     * Add activity assignment.
     *
     * @param array  $params       (reference ) an assoc array of name/value pairs
     * @param array  $ids          (reference ) the array that holds all the db ids
     *
     * @return object activity type of object that is added
     * @access public
     * 
     */
    public function create( &$params ) 
    {
        $this->copyValues( $params );
        return $this->save();
    }


    /**
     * Delete activity assignment.
     *
     * @param int    $id  ID of the activity assignment.
     * 
     * @return void
     * 
     * @access public
     * 
     */
    public function removeAssignment( $id )
    {
        $this->id = $id;
        if( $this->find( true ) ) {
            return $this->delete();
        }
    }

    /**
     * Retrieve assignee_id by activity_id
     *
     * @param int    $id  ID of the activity
     * 
     * @return void
     * 
     * @access public
     * 
     */
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
