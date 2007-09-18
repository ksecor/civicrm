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
    //static $_properties = array('activity_type', 'activity_summary', 'activity_date');

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
     * @param int $contactId  - contact whose history we want to display
     * @param int $permission - the permission we have for this contact 
     *
     * @return CRM_History_Selector
     * @access public
     */
    function __construct($entityId, $permission,$showLink =null) 
    {
        $this->_entityId   = $entityId;
        $this->_permission = $permission;
        $this->_showLink = $showLink;
    }


    /**
     * This method returns the action links that are given for each search row.
     * currently the action links added for each row are 
     * 
     * - Details
     * - Delete
     *
     *
     * @return array
     * @access public
     *
     */
    static function &actionLinks() 
    {
        $deleteExtra = ts('Are you sure you want to delete this Activity History record?');
        if (!isset(self::$_actionLinks)) {
            self::$_actionLinks = array(
                                        CRM_Core_Action::VIEW   => array(
                                                                         'name'     => ts('Details'),
                                                                         'url'      => 'civicrm/history/activity/detail',
                                                                         'qs'       => 'id=%%id%%&activity_id=%%activity_id%%&cid=%%cid%%&context=activity',
                                                                         'title'    => ts('View Activity Details'),
                                                                         ),
                                        CRM_Core_Action::DELETE => array(
                                                                         'name'     => ts('Delete'),
                                                                         'url'      => 'civicrm/contact/view/activity',
                                                                         'qs'       => 'show=1&action=delete&id=%%id%%&cid=%%cid%%&context=activity',
                                                                         'extra' => 'onclick = "if (confirm(\'' . $deleteExtra . '\')) this.href+=\'&amp;confirmed=1\'; else return false;"',
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
        $params['status']       = ts('History %%StatusMessage%%');
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
            $csvHeaders = array( ts('Activity Type'), ts('Description'), ts('Activity Date'));
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
        $params = array('entity_table' => 'civicrm_contact', 'entity_id' => $this->_entityId);
        $rows =& CRM_Core_BAO_History::getHistory($params, $offset, $rowCount, $sort, 'Activity');
        
        if($this->_showLink){
            $links = array();
        }else{
            $links =& self::actionLinks();
        }

        $mask  =  array_sum(array_keys($links)) & CRM_Core_Action::mask( $this->_permission );
        foreach ($rows as $k => $row) {
            $row =& $rows[$k];

            // localize the built-in activity names for display
            // (these are not enums, so we can't use any automagic here)
            switch ($row['activity_type']) {
                case 'Meeting':    $row['activity_type'] = ts('Meeting');    break;
                case 'Phone Call': $row['activity_type'] = ts('Phone Call'); break;
                case 'Email':      $row['activity_type'] = ts('Email');      break;
                case 'SMS':        $row['activity_type'] = ts('SMS');        break;
                case 'Event':      $row['activity_type'] = ts('Event');      break;
            }

            if ($output != CRM_Core_Selector_Controller::EXPORT && $output != CRM_Core_Selector_Controller::SCREEN) {
                // check if callback exists
                if ( $row['callback'] &&
                     CRM_Utils_System::validCallback( $row['callback'] ) ) {
                    $row['action'] = CRM_Core_Action::formLink($links,
                                                               $mask,
                                                               array( 'id' => $k,
                                                                      'activity_id'=>$row['activity_id'],
                                                                      'cid' => $this->_entityId ) );                    
                } else {
                    $actionLinks = $links;
                    unset($actionLinks[CRM_Core_Action::VIEW]);
                    $row['action'] = CRM_Core_Action::formLink($actionLinks, $mask, array('id'=>$k,'cid' => $this->_entityId));
                }
            }
            unset($row);
        }
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
        return ts('CiviCRM Activity History');
    }

    /**
     * get colunmn headers for search selector
     *
     *
     * @return array $_columnHeaders
     * @access private
     */
    private static function &_getColumnHeaders() 
    {
        if (!isset(self::$_columnHeaders)) {
            self::$_columnHeaders = array(
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
