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


/**
 * Business object for managing history
 * This BAO manages all types of histories like locationType, RelationshipType, Activity etc.
 *
 */
class CRM_Core_BAO_History {

    /**
     * class constructor
     */
    function __construct( ) {
        //parent::__construct( );
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
     * @param string $type - which history do we want ?
     *
     * @return object CRM_Core_DAO_History object
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults, $type='Activity')
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $type . 'History') . ".php");
        eval('$historyDAO =& new CRM_Core_DAO_' . $type . 'History();');
        $historyDAO->copyValues($params);
        if ($historyDAO->find(true)) {
            //$historyDAO->storeValues($defaults); //this is not working in php4
            $historyDAO->storeValues(&$defaults);
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
    static function del($historyTableId, $type='Ativity')
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $type . 'History') . ".php");
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
        $values['activity']['data']  =& CRM_Core_BAO_History::getHistory(&$params, 0, 3, null, $type);

        // get the total number of histories
        $values['activity']['totalCount'] = CRM_Core_BAO_History::getNumHistory($params['entity_id'], $type);

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
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $type . 'History') . ".php");
        eval('$historyDAO =& new CRM_Core_DAO_' . $type . 'History();');
        
        //$historyDAO->copyValues($params); this is not working in php4
        $historyDAO->copyValues(&$params);

        // selection criteria
        $historyDAO->selectAdd();
        $historyDAO->selectAdd('id, activity_type, activity_summary, activity_date, module, callback, activity_id');

        // sort order
        $historyDAO->orderBy(CRM_Core_DAO::getSortString($sort, "activity_date desc"));

        // how many rows to get ?
        $historyDAO->limit($offset, $rowCount);
        
        // fire query, get rows, populate array and return it please.
        $values = array();
        $historyDAO->find();
        while($historyDAO->fetch()) {
            $id = $historyDAO->id;
            $values[$id]['activity_type']    = $historyDAO->activity_type;
            $values[$id]['activity_summary'] = $historyDAO->activity_summary;
            $values[$id]['activity_date']    = $historyDAO->activity_date;
            $values[$id]['module']           = $historyDAO->module;
            $values[$id]['callback']         = $historyDAO->callback;
            $values[$id]['activity_id']      = $historyDAO->activity_id;
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
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $type . 'History') . ".php");
        eval('$historyDAO =& new CRM_Core_DAO_' . $type . 'History();');
        $historyDAO->entity_table = 'crm_contact';
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
     * @return object CRM_Core_DAO_HistoryHistory object 
     * @access public
     * @static
     */
    static function create(&$params, $type='Activity')
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $type . 'History') . ".php");
        eval('$historyDAO =& new CRM_Core_DAO_' . $type . 'History();');
        $historyDAO->copyValues($params);
        return $historyDAO->save();
    }
}
?>