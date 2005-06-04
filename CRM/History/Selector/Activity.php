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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id: Selector.php 1204 2005-05-27 19:32:55Z lobo $
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/Selector/API.php';

require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';

require_once 'CRM/Contact/BAO/Contact.php';


/**
 * This class is used to retrieve and display history.
 *
 */
class CRM_History_Selector_Activity extends CRM_Core_Selector_Base implements CRM_Core_Selector_API 
{
    /**
     * This defines two actions - Details and Delete.
     *
     * @var array
     * @static
     */
    static $_actionLinks;

    /**
     * we use desc to remind us what that column is, name is used in the tpl
     *
     * @var array
     * @static
     */
    static $_columnHeaders;

    /**
     * Properties of contact we're interested in displaying
     * @var array
     * @static
     */
    static $_properties = array('activity_type', 'activity_summary', 'activity_date');

    /**
     * entityId - entity id of entity whose history are displayed
     *
     * @var int
     * @access protected
     */
    protected $_entityId;

    /**
     * history object for the selector
     *
     * @var int
     * @access protected
     */
    protected $_history;

    /**
     * Class constructor
     *
     * @param int $contactId - contact whose history we want to display
     *
     * @return CRM_History_Selector
     * @access public
     */
    function __construct($entityId) 
    {
        $this->_entityId = $entityId;
    }


    /**
     * This method returns the action links that are given for each search row.
     * currently the action links added for each row are 
     * 
     * - Details
     * - Delete
     *
     * @param none
     *
     * @return array
     * @access public
     *
     */
    static function &actionLinks() 
    {

        if (!isset(self::$_actionLinks)) {
            self::$_actionLinks = array(
                                  CRM_Core_Action::VIEW   => array(
                                                                   'name'     => ts('Details'),
                                                                   'url'      => 'civicrm/contact/view',
                                                                   'qs'       => 'reset=1&cid=%%id%%',
                                                                   'title'    => ts('View Contact Details'),
                                                                  ),
                                  CRM_Core_Action::DELETE => array(
                                                                   'name'     => ts('Delete'),
                                                                   'url'      => 'civicrm/contact/activity',
                                                                   'qs'       => 'action=delete&history_id=%%id%%',
                                                                   'title'    => ts('Delete Activity History'),
                                                                  ),
                                 );
        }
        return self::$_actionLinks;
    }


    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) 
    {
        $params['status']       = "History %%StatusMessage%%";
        $params['csvString']    = null;
        $params['rowCount']     = CRM_Utils_Pager::ROWCOUNT;

        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    }


    /**
     * returns the column headers as an array of tuples:
     * (name, sortName (key to the sort array))
     *
     * @param string $action the action being performed
     * @param enum   $output what should the result set include (web/email/csv)
     *
     * @return array the column headers that need to be displayed
     * @access public
     */
    function &getColumnHeaders($action = null, $output = null) 
    {
        if ($output==CRM_Core_Selector_Controller::EXPORT || $output==CRM_Core_Selector_Controller::SCREEN) {
            $csvHeaders = array( 'Activity Type', 'Description', 'Activity Date');
            foreach (self::_getColumnHeaders() as $column ) {
                if (array_key_exists( 'name', $column ) ) {
                    $csvHeaders[] = $column['name'];
                }
            }
            return $csvHeaders;
        } else {
            return self::_getColumnHeaders();
        }
    }


    /**
     * Returns total number of rows for the query.
     *
     * @param string $action - action being performed
     * @return int Total number of rows 
     * @access public
     */
    function getTotalCount($action)
    {
        return CRM_Core_BAO_History::getNumHistory($this->_entityId, 'Activity');
    }


    /**
     * returns all the rows in the given offset and rowCount
     *
     * @param enum   $action   the action being performed
     * @param int    $offset   the row number to start from
     * @param int    $rowCount the number of rows to return
     * @param string $sort     the sql string that describes the sort order
     * @param enum   $output   what should the result set include (web/email/csv)
     *
     * @return int   the total number of rows for this action
     */
    function &getRows($action, $offset, $rowCount, $sort, $output = null) {
        $config =& CRM_Core_Config::singleton();

        $params = array('entity_id' => $this->_entityId);
        $rows = CRM_Core_BAO_History::getHistory($params, $offset, $rowCount, $sort, 'Activity');

        CRM_Core_Error::debug_var('rows', $rows);

//         while ($result->fetch()) {
//             $row = array();

//             // the columns we are interested in
//             foreach (self::$_properties as $property) {
//                 $row[$property] = $result->$property;
//             }

//             if ( $output != CRM_Core_Selector_Controller::EXPORT && $output != CRM_Core_Selector_Controller::SCREEN ) {
//                 $row['checkbox'] = CRM_Core_Form::CB_PREFIX . $result->contact_id;
//                 $row['action'] = CRM_Core_Action::formLink( self::actionLinks(), null, array( 'id' => $result->contact_id ) );
//                 $contact_type  = '<img src="' . $config->resourceBase . 'i/contact_';
//                 switch ($result->contact_type) {
//                 case 'Individual' :
//                     $contact_type .= 'ind.gif" alt="Individual">';
//                     break;
//                 case 'Household' :
//                     $contact_type .= 'house.png" alt="Household" height="16" width="16">';
//                     break;
//                 case 'Organization' :
//                     $contact_type .= 'org.gif" alt="Organization" height="16" width="18">';
//                     break;
                    
//                 }
//                 $row['contact_type'] = $contact_type;
//             }

//             $rows[] = $row;
//         }
        return $rows;
    }
    
    /**
     * name of export file.
     *
     * @param string $output type of output
     * @return string name of the file
     */
    function getExportFileName($output = 'csv')
    {
        return 'CiviCRM Activity History';
    }

    /**
     * get colunmn headers for search selector
     *
     *
     * @param none
     * @return array $_columnHeaders
     * @access private
     */
    private static function &_getColumnHeaders() 
    {
        if (!isset(self::$_columnHeaders)) {
            self::$_columnHeaders = array(
                                          array('desc' => ts('Select')),
                                          array(
                                                'name'      => ts('Activity Type'),
                                                'sort'      => 'activity_type',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array('name' => ts('Description')),
                                          array(
                                                'name'      => ts('Activity Date'),
                                                'sort'      => 'activity_date',
                                                'direction' => CRM_Utils_Sort::DESCENDING,
                                                ),
                                          array('desc' => ts('Actions')),
                                          );
        }
        return self::$_columnHeaders;
    }
}
?>