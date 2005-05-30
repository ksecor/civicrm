<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/Activity.php';

/**
 * Business object for managing activities
 *
 */
class CRM_Core_BAO_Activity extends CRM_Core_DAO_Activity {

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
     * @return object CRM_Core_BAO_Activity object
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults)
    {
        $activity = new CRM_Core_DAO_Activity();
        $activity->copyValues($params);
        if ($activity->find(true)) {
            $activity->storeValues($defaults);
            return $activity;
        }
        return null;
    }


    /**
     * Delete an activity record from the database
     *
     * @param int $activityTableId
     * @return none
     *
     * @access public
     * @static
     */
    static function del($activityTableId)
    {
        $activity = new CRM_Core_DAO_Activity();
        $activity->id = $activityTableId;
        $activity->delete();
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     *
     * @return array (reference)   the values that could be potentially assigned to smarty
     * @access public
     * @static
     */
    static function &getValues(&$params, &$values)
    {
        // get top 3 activities
        //$values['activity']['data']  =& CRM_Core_BAO_Activity::getActivity($params['contact_id'], 3);
        $values['activity']['data']  =& CRM_Core_BAO_Activity::getActivity($params['contact_id'], 0, 3);

        // get the total number of activities
        $values['activity']['totalCount'] = CRM_Core_BAO_Activity::getNumActivity($params['contact_id']);

        //CRM_Core_Error::debug_var('values', $values);
        
        return $values;
    }

    /**
     * function to get the list of activities for contact.
     *
     * @param int     $contactId       contact id 
     * @param int     $offset          which row to start from ?
     * @param int     $rowCount        how many rows to fetch
     * @param object  $sort            object describing sort order for sql query.
     *
     * @return array (reference)|int   $values the relevant data object values for the contact or
                                       the total count when $count is true
     *
     * @access public
     * @static
     */
    static function &getActivity($contactId, $offset=null, $rowCount=null, $sort=null)
    {
        $activityDAO = new CRM_Core_DAO_Activity();
        $activityDAO->entity_table = 'crm_contact';
        $activityDAO->entity_id = $contactId;

        // selection criteria
        $activityDAO->selectAdd();
        $activityDAO->selectAdd('id, activity_type, activity_summary, activity_date');

        // default of user specified sort order ?
        if ($sort) {
            $activityDAO->orderBy($sort->orderBy());
        } else {
            $activityDAO->orderBy('activity_date desc'); // default sort order
        }
        
        // how many rows to get ?
        $activityDAO->limit($offset, $rowCount);

        // fire query, get rows, populate array and return it please.
        $values = array();
        $activityDAO->find();
        while($activityDAO->fetch()) {
            $id = $activityDAO->id;
            $values[$id]['activity_type'] = $activityDAO->activity_type;
            $values[$id]['activity_summary'] = $activityDAO->activity_summary;
            $values[$id]['activity_date'] = $activityDAO->activity_date;
        }
        return $values;
    }

    /**
     * function to get number of activities for a contact.
     *
     * @param  int $contactId   contact id 
     * @return int $numActivity number of activities
     *
     * @access public
     * @static
     */
    static function &getNumActivity($contactId)
    {
        $activityDAO = new CRM_Core_DAO_Activity();
        $activityDAO->entity_table = 'crm_contact';
        $activityDAO->entity_id = $contactId;
        return $activityDAO->count();
    }
}
?>