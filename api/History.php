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
 * Definition of the History part of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'PEAR.php';

require_once 'CRM/Core/Error.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/BAO/History.php';
require_once 'CRM/Core/I18n.php';



/**
 * Most API functions take in associative arrays ( name => value pairs
 * as parameters. Some of the most commonly used parameters are
 * described below
 *
 * @param array $params           an associative array used in construction
                                  / retrieval of the object
 * @param array $returnProperties the limited set of object properties that
 *                                need to be returned to the caller
 *
 */


/**
 * Create a new Activity History.
 *
 * Creates a new history record and returns the newly created
 * History object. Minimum required data values are entity_id,
 *                  and activity_id
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new history.
 *
 * @return CRM_Core_BAO_History|CRM_Error Newly created History object
 *
 * @access public
 */
function &crm_create_activity_history(&$params)
{
    // return error if we do not get any params
    if (empty($params)) {
        return _crm_error(ts('Input Parameters empty'));
    }

    $error = _crm_check_history_params($params, 'Activity');
    // does not work for php4
    //if ($error instanceof CRM_Core_Error) {
    if (is_a($error, CRM_Core_Error)) {
        return $error;
    }
    $history = CRM_Core_BAO_History::create($params, 'Activity');
    return $history;
}

/**
 * Get an existing History
 *
 * Returns a single existing History object which matches ALL property
 * values passed in $params. An error object is returned if there is
 * no match, or more than one match. 
 *
 * @param array $params           Associative array of property name/value
 *                                pairs to attempt to match on.
 *
 * @param array $returnProperties Which properties should be included in the
 *                                returned Contact object. If NULL, the default
 *                                set of properties will be included.
 *
 * @return CRM_Core_DAO_$typeHistory|CRM_Core_Error  Return the Contact Object if found, else Error Object
 *
 * @access public
 *
 */
function &crm_get_activity_history($params, $sort, $offset, $numRow)
{
    $values =& CRM_Core_BAO_History::getHistory($params, $offset, $numRow, $sort, 'Activity');
    return $values;
}

/**
 * Update a history record
 *
 * Updates history record  with the values passed in the 'params' array. An
 * error is returned if an invalid history record is passed, or an invalid
 * property name or property value is included in 'params'. An error
 * is also returned if the processing the update would violate data
 * integrity rules, e.g. if a group id value is passed which is
 * not present
 *
 * <b>Clearing Property Values with Update APIs</b>
 * 
 * <ul>
 * <li>For any CRM 'update' API...to clear the value of an existing
 * property (i.e. set it to empty) - pass the property name in the
 * $params array with a NULL value.</li>
 * </ul>
 *
 * @param CRM_Core_DAO_ActivityHistory $historyDAO A valid history object
 * @param array $params  Associative array of property name/value pairs to be updated. 
 *  
 * @return CRM_Core_DAO_ActivityHistory|CRM_Core_Error  Return the updated Contact Object else
 *                                Error Object (if integrity violation)
 *
 * @access public
 *
 */
function &crm_update_activity_history(&$historyDAO, &$params)
{

    CRM_Core_Error::le_method();

    $error = _crm_check_activity_history_object($historyDAO);
    if (is_a($error, CRM_Core_Error)) {

        CRM_Core_Error::debug_log_message('breakpoint 10');
        return $error;
    }

    $error = _crm_update_object($historyDAO, $params);
    if (is_a($error, CRM_Core_Error)) {

        CRM_Core_Error::debug_log_message('breakpoint 20');
        return $error;
    }

    return $historyDAO;
}


/**
 * Delete a specified activity history.
 *
 * @param CRM_Core_DAO_ActivityHistory $historyDAO Activity History object to be deleted
 *
 * @return void|CRM_Core_Error  An error if 'contact' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function &crm_delete_activity_history(&$historyDAO)
{
    $error = _crm_check_activity_history_object($historyDAO);
    if (is_a($error, CRM_Core_Error)) {
        return $error;
    }
    return $historyDAO->delete();
}



/**
 * Check if object is valid and has an id
 *
 * @param CRM_Core_DAO_ActivityHistory $historyDAO Activity History object to be checked
 *
 * @return true|CRM_Core_Error  An error if 'contact' is invalid,
 *                              permissions are insufficient, etc.
 *
 * @access public
 *
 */
function _crm_check_activity_history_object(&$historyDAO)
{

    CRM_Core_Error::le_method();


    // check if valid DAO
    if (is_a($historyDAO, CRM_Core_DAO_ActivityHistory)) {

        CRM_Core_Error::debug_log_message('breakpoint 10');

        return _crm_error(ts('Invalid history object passed in'));
    }

    // since it is update, id should be set
    if (!isset($historyDAO->id)) {

        CRM_Core_Error::debug_log_message('breakpoint 20');

        return _crm_error(ts('History object does not contain a primary key - it is needed for update operation'));
    }

    return true;
}
?>
