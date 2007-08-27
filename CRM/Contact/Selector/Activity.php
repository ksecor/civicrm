<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

    protected $_admin;

    protected $_context;

    /**
     * Class constructor
     *
     * @param int $contactId - contact whose open activities we want to display
     * @param int $permission - the permission we have for this contact 
     *
     * @return CRM_Contact_Selector_Activity
     * @access public
     */
    function __construct($contactId, $permission, $admin = false, $context = 'activity' ) 
    {
        $this->_contactId  = $contactId;
        $this->_permission = $permission;
        $this->_admin      = $admin;
        $this->_context    = $context;
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

                                                                     'qs'       => "activity_id={$activityType}&action=update&reset=1&id=%%id%%&cid=%%cid%%&context=%%cxt%%&subType={$activityType}",
                                                                     'title'    => ts('View Activity'),
                                                                     ),
                                  
                                    CRM_Core_Action::DELETE => array(
                                                                     'name'     => ts('Delete'),
                                                                     'url'      => 'civicrm/contact/view/activity',
                                                                     'qs'       => "{$extra}&activity_id={$activityType}&action=delete&reset=1&id=%%id%%&cid=%%cid%%&context=%%cxt%%",
                                                                     'extra' => 'onclick = "if (confirm(\'' . $deleteExtra . '\') ) this.href+=\'&amp;confirmed=1\'; else return false;"',
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
        return CRM_Contact_BAO_Contact::getNumOpenActivity($this->_contactId, $this->_admin);
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
    function &getRows($action, $offset, $rowCount, $sort, $output = null, $case = null) {
        $params['contact_id'] = $this->_contactId;
        
        $rows =& CRM_Contact_BAO_Contact::getOpenActivities($params, $offset, $rowCount, $sort, 'Activity', $this->_admin, $case);
        if ( empty( $rows ) ) {
            return $rows;
        }

        //print_r($rows);
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
            
            //for case subject
            if ($row['case_id']){
                $row['case_subjectID'] = CRM_Core_DAO::getFieldValue('CRM_Case_DAO_CaseActivity', $row['case_id'],'case_id' );
                $row['case'] = CRM_Core_DAO::getFieldValue('CRM_Case_BAO_Case',$row['case_subjectID'],'subject'); 
            }
            require_once "CRM/Core/OptionGroup.php";
            $caseActivity = CRM_Core_OptionGroup::values('case_activity_type');
            $row['case_activity'] = $caseActivity[$row['case_activity']];
            // retrieve to_contact
            require_once "CRM/Activity/BAO/Activity.php";
            $assignCID = CRM_Activity_BAO_Activity::retrieveActivityAssign( $row['activity_type_id'],$row['id']);
            if( $assignCID ) {
                require_once "CRM/Contact/BAO/Contact.php";
                $row['to_contact']    = CRM_Contact_BAO_Contact::displayName( $assignCID );
                $row['to_contact_id'] = $assignCID;
            }

                // add class to this row if overdue
            if ( CRM_Utils_Date::overdue( $row['date'] ) ) {
                $row['overdue'] = 1;
                $row['class']   = 'status-overdue';
            } else {
                $row['overdue'] = 0;
                $row['class']   = 'status-ontime';
            }

            $actionLinks =& self::actionLinks($row['activity_type_id']);
            require_once 'CRM/Contact/Page/View/Case.php';
            $caseLinks = CRM_Contact_Page_View_Case::caseViewLinks();     
            $caseAction = array_sum(array_keys($caseLinks));
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
                                                                     'cid' => $this->_contactId,
                                                                     'cxt' => $this->_context ) );
                } elseif( $case) {
                    $row['action'] = CRM_Core_Action::formLink($caseLinks,
                                                               $caseAction,
                                                               array('aid'  => $row['case_id'],
                                                                     'atype'=> $row['activity_type_id'],
                                                                     'rid'  => $row['id'],
                                                                     'id'  =>  $row['case_subjectID'],
                                                                     'cid' => $this->_contactId
                                                                      ));
                }else {
                   
                    $row['action'] = CRM_Core_Action::formLink($actionLinks,
                                                               $actionMask,
                                                               array('id'  => $row['id'],
                                                                     'cid' => $this->_contactId,
                                                                     'cxt' => $this->_context ) );

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
                                          array('name'      => ts('Type')),
                                          array('name'      => ts('Case')),
                                          array('name'      => ts('Activity'),
                                                'sort'      => 'activity_type',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array('name' => ts('From Contact')),
                                          array('name' => ts('Regarding Contact')),
                                          array('name' => ts('To Contact')),
                                          array(
                                                'name'      => ts('Date'),
                                                'sort'      => 'date',
                                                'direction' => CRM_Utils_Sort::ASCENDING,
                                                ),
                                          array('desc' => ts('Actions')),
                                          );
        }
        return self::$_columnHeaders;
    }
}
?>
