<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of the History part of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/utils.php';

require_once 'CRM/Core/BAO/History.php';

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
 * @return CRM_Core_DAO_History|CRM_Error Newly created History object
 *
 * @access public
 */
function &crm_create_activity_history(&$params)
{
    _crm_initialize( );

    // return error if we do not get any params
    if (empty($params)) {
        return _crm_error(ts('Input Parameters empty'));
    }

    $error = _crm_check_history_params($params, 'Activity');
    if (is_a($error, 'CRM_Core_Error')) {
        return $error;
    }
    $ids = array();
    $history = CRM_Core_BAO_History::create($params, $ids, 'Activity');
    return $history;
}

/**
 * Get an existing History
 *
 * Returns an array containing existing Histories which matches ALL property
 * values passed in $params.
 *
 * @param array $params           Associative array of property name/value
 *                                pairs to attempt to match on.
 *
 * @param int     $offset          which row to start from?
 * @param int     $rowCount        how many rows to fetch
 * @param object|array  $sort      object or array describing sort order for sql query.
 *
 *
 * @param array $returnProperties Which properties should be included in the
 *                                returned array. If NULL, the default
 *                                set of properties will be included.
 *
 * @return array $values          Return the array containing the matched Activity Histories
 *
 * @access public
 *
 */
function &crm_get_activity_history(&$params, $offset = null, $numRow = null, $sort = null)
{
    _crm_initialize( );

    $values =& CRM_Core_BAO_History::getHistory($params, $offset, $numRow, $sort, 'Activity');
    return $values;
}

/**
 * Get an existing History object
 *
 * Returns a single existing History object which matches ALL property
 * values passed in $params. An error object is returned if there is
 * no match, or more than one match. 
 *
 * @param array   $params    Associative array of property name/value
 *                           pairs to attempt to match on.
 * @param array   $deaults   An assoc array to hold the flattened values.   
 *
 * @return CRM_Core_DAO_$typeHistory|CRM_Core_Error  Return the Contact Object if found, else Error Object
 *
 * @access public
 *
 */
function &crm_get_activity_history_object(&$params, &$defaults)
{
    _crm_initialize( );

    $historyObject =& CRM_Core_BAO_History::retrieve($params, $defaults);
    return $historyObject;
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
    _crm_initialize( );

    $error = _crm_check_activity_history_object($historyDAO, true);
    if (is_a($error, 'CRM_Core_Error')) {
        return $error;
    }

    $error = _crm_update_object($historyDAO, $params);

    if (is_a($error, 'CRM_Core_Error')) {
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
    _crm_initialize( );

    $error = _crm_check_activity_history_object($historyDAO);
    if (is_a($error, 'CRM_Core_Error')) {
        return $error;
    }
    return $historyDAO->delete();
}



/**
 * Check if object is valid and has an id
 *
 * @param CRM_Core_DAO_ActivityHistory $historyDAO Activity History object to be checked
 *
 * @param boolean $checkForId - check if id is set
 *
 * @return true|CRM_Core_Error  An error if 'contact' is invalid,
 *                              permissions are insufficient, etc.
 *
 * @access public
 *
 */
function _crm_check_activity_history_object(&$historyDAO, $checkForId=false)
{
    _crm_initialize( );

    // check if valid DAO
    if (!is_a($historyDAO, 'CRM_Core_DAO_ActivityHistory')) {
        return _crm_error(ts('Invalid history object passed in'));
    }

    if ($checkForId && (!isset($historyDAO->id))) {
        return _crm_error(ts('History object does not contain a primary key - it is needed for update operation'));
    }

    return true;
}

?>
