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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
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
 * This class is used to retrieve and display open activities for a contact
 *
 */
class CRM_Contact_Selector_Activity extends CRM_Core_Selector_Base implements CRM_Core_Selector_API 
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
     * contactId - contact id of contact whose open activies are displayed
     *
     * @var int
     * @access protected
     */
    protected $_contactId;

    /**
     * Class constructor
     *
     * @param int $contactId - contact whose open activities we want to display
     * @param int $permission - the permission we have for this contact 
     *
     * @return CRM_Contact_Selector_Activity
     * @access public
     */
    function __construct($contactId, $permission) 
    {
        $this->_contactId  = $contactId;
        $this->_permission = $permission;
    }


    /**
     * This method returns the action links that are given for each search row.
     * currently the action links added for each row are 
     * 
     * - View
     *
     * @param string $activityType type of activity
     *
     * @return array
     * @access public
     *
     */
    static function &actionLinks( $activityType ) 
    {
        $url = '';
        $extra = '';

        // helper variable for nicer formatting
        $deleteExtra = ts('Are you sure you want to delete this activity record?');

        self::$_actionLinks = array(
                                    CRM_Core_Action::UPDATE => array(
                                                                     'name'     => ts('Edit'),
                                                                     'url'      => 'civicrm/contact/view/activity',
                                                                     'qs'       => 'activity_id='.$activityType.'&action=update&reset=1&id=%%id%%&cid=%%cid%%',
                                                                     'title'    => ts('View Activity'),
                                                                     ),
                                    CRM_Core_Action::DELETE => array(
                                                                     'name'     => ts('Delete'),
                                                                     'url'      => 'civicrm/contact/view/activity',
                                                                     'qs'       => $extra.'&activity_id='.$activityType.'&action=delete&reset=1&id=%%id%%&cid=%%cid%%',
                                                                     'extra' => 'onclick = "if (confirm(\'' . $deleteExtra . '\') ) this.href+=\'&confirmed=1\'; else return false;"',
                                                                     'title'    => ts('Delete Activity'),
                                                                     ),
                                    );
        
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
        $params['status']       = ts('Open Activities %%StatusMessage%%');
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
        return CRM_Contact_BAO_Contact::getNumOpenActivity($this->_contactId);
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
        $params['contact_id'] = $this->_contactId;
        $rows =& CRM_Contact_BAO_Contact::getOpenActivities($params, $offset, $rowCount, $sort, 'Activity');

        foreach ($rows as $k => $row) {
            $row =& $rows[$k];

            //check the if the activity type is meeting or phonecall
            if (is_numeric($row['activity_type'])) {
                if ($row['activity_type'] == 2) {
                    $row['activity_type'] = ts('Phone Call');
                } else {
                    $row['activity_type'] = ts('Meeting');
                }
            }

            $actionLinks =& self::actionLinks($row['activity_type_id']);
            $actionMask  =  array_sum(array_keys($actionLinks)) & CRM_Core_Action::mask( $this->_permission );

            if ($output != CRM_Core_Selector_Controller::EXPORT && $output != CRM_Core_Selector_Controller::SCREEN) {
                // check if callback exists
                if ( CRM_Utils_Array::value( 'callback', $row ) ) {
                    $row['action'] = CRM_Core_Action::formLink($actionLinks,
                                                               $actionMask,
                                                               array('activity_history_id'=>$k,
                                                                     'callback'=>$row['callback'],
                                                                     'module'=>$row['module'],
                                                                     'activity_id'=>$row['activity_id'],
                                                                     'cid' => $this->_contactId ) );
                } else {
                    $row['action'] = CRM_Core_Action::formLink($actionLinks,
                                                               $actionMask,
                                                               array('id'  => $row['id'],
                                                                     'cid' => $this->_contactId ) );
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
                                          array('name'      => ts('Activity Type'),
                                                'sort'      => 'activity_type',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array('name' => ts('Subject')),
                                          array('name' => ts('Created By')),
                                          array('name' => ts('With Contact')),
                                          array(
                                                'name'      => ts('Scheduled'),
                                                'sort'      => 'date',
                                                'direction' => CRM_Utils_Sort::DESCENDING,
                                                ),
                                          array('name'      => ts('Status')),

                                          array('desc' => ts('Actions')),
                                          );
        }
        return self::$_columnHeaders;
    }
}
?>
