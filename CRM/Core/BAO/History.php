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
 * Business object for managing history
 * This BAO manages all types of histories like locationType, RelationshipType, Activity etc.
 *
 */
class CRM_Core_BAO_History {

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     * @param string $type - which history do we want ?
     *
     * @return object CRM_Core_DAO_History object
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults, $type='Activity')
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, 'CRM_Core_DAO_' . $type . 'History') . '.php');
        eval('$historyDAO =& new CRM_Core_DAO_' . $type . 'History();');
        $historyDAO->copyValues($params);
        if ($historyDAO->find(true)) {
            CRM_Core_DAO::storeValues( $historyDAO, $defaults);
            return $historyDAO;
        }
        return null;
    }


    /**
     * Delete an history record from the database
     *
     * @param int $historyTableId
     * @return none
     *
     * @access public
     * @static
     */
    static function del($historyTableId, $type='Activity')
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, 'CRM_Core_DAO_' . $type . 'History') . '.php');
        eval('$historyDAO =& new CRM_Core_DAO_' . $type . 'History();');
        $historyDAO->id = $historyTableId;
        $historyDAO->delete();
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
    static function &getValues(&$params, &$values, $type='Activity')
    {
        // get top 3 histories
        $values['activity']['data']  =& CRM_Core_BAO_History::getHistory($params, 0, 3, null, $type);

        // get the total number of histories
        $values['activity']['totalCount'] =& CRM_Core_BAO_History::getNumHistory($params['entity_id'], $type);

        return $values;
    }

    /**
     * function to get the list of history for an entity.
     *
     * @param array reference $params  array of parameters 
     * @param int     $offset          which row to start from ?
     * @param int     $rowCount        how many rows to fetch
     * @param object|array  $sort      object or array describing sort order for sql query.
     * @param type    $type            type of history we're interested in
     *
     * @return array (reference)      $values the relevant data object values for history
     *
     * @access public
     * @static
     */
    static function &getHistory(&$params, $offset=null, $rowCount=null, $sort=null, $type='Activity')
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, 'CRM_Core_DAO_' . $type . 'History') . '.php');
        eval('$historyDAO =& new CRM_Core_DAO_' . $type . 'History();');

        // if null hence no search criteria
        if (!isset($params)) {
            $params = array();
        }
        
        $historyDAO->copyValues($params);

        // sort order
        $historyDAO->orderBy(CRM_Core_DAO::getSortString($sort, 'activity_date desc, activity_type asc'));

        // how many rows to get ?
        $historyDAO->limit($offset, $rowCount);
        
        // fire query, get rows, populate array and return it please.
        $values = array();
        $historyDAO->find();
        while($historyDAO->fetch()) {
            $values[$historyDAO->id] = array();
            CRM_Core_DAO::storeValues( $historyDAO, $values[$historyDAO->id]);
        }
        return $values;
    }

    /**
     * function to get number of histories for a contact.
     *
     * @param  int $entityId   entity id 
     * @param  int $type       type of activity
     * @return int $numHistory number of histories
     *
     * @access public
     * @static
     */
    static function &getNumHistory($entityId, $type='Activity')
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, 'CRM_Core_DAO_' . $type . 'History') . '.php');
        eval('$historyDAO =& new CRM_Core_DAO_' . $type . 'History();');
        $historyDAO->entity_table = 'civicrm_contact';
        $historyDAO->entity_id = $entityId;
        return $historyDAO->count();
    }


    /**
     * takes an associative array and creates an actvity history object
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Core_DAO_ActivityHistory object 
     * @access public
     * @static
     */
    static function create(&$params, &$ids, $type = 'Activity')
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, 'CRM_Core_DAO_' . $type . 'History') . '.php');
        eval('$historyDAO =& new CRM_Core_DAO_' . $type . 'History();');

        $historyDAO->copyValues($params);
        $historyDAO->id        = CRM_Utils_Array::value(strtolower($type) . '_history', $ids);

        return $historyDAO->save();
    }
}
?>
