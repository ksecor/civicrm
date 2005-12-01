<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id: Selector.php 2609 2005-08-17 00:16:37Z lobo $
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/Selector/API.php';

require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';

require_once 'CRM/Contact/BAO/Contact.php';


/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria (specifically for
 * results of advanced search options.
 *
 */
class CRM_Mailing_Selector_Event    extends CRM_Core_Selector_Base 
                                    implements CRM_Core_Selector_API 
{
    /**
     * array of supported links, currenly null
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * what event type are we browsing?
     */
    private $_event;

    /**
     * should we only count distinct contacts?
     */
    private $_is_distinct;
    
    /**
     * which mailing are we browsing events from?
     */
    private $_mailing_id;

    /**
     * do we want events tied to a specific job?
     */
    private $_job_id;

    /**
     * for click-through events, do we only want those from a specific url?
     */
    private $_url_id;
    
    /**
     * we use desc to remind us what that column is, name is used in the tpl
     *
     * @var array
     */
    public $_columnHeaders;

    /**
     * Class constructor
     *
     * @param string $event         The event type (queue/delivered/open...)
     * @param boolean $distinct     Count only distinct contact events?
     * @param int $mailing          ID of the mailing to query
     * @param int $job              ID of the job to query.  If null, all jobs from $mailing are queried.
     * @param int $url              If the event type is a click-through, do we want only those from a specific url?
     *
     * @return CRM_Contact_Selector_Profile
     * @access public
     */
    function __construct($event, $distinct, $mailing, $job = null, $url = null )
    {
        $this->_event_type  = $event;
        $this->_is_distinct = $distinct;
        $this->_mailing_id  = $mailing;
        $this->_job_id      = $job;
        $this->_url_id      = $url;
    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     *
     * @return array
     * @access public
     * @static
     */
    static function &links()
    {
        return self::$_links;
    } //end of function


    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) 
    {
        $params['csvString']    = null;
        $params['rowCount']     = CRM_Utils_Pager::ROWCOUNT;
        $params['status']       = ts('%1 %%StatusMessage%%', array(1 =>
        $this->eventToTitle()));
        
        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    }//end of function


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
        $mailing = CRM_Mailing_BAO_Mailing::getTableName();
        $job = CRM_Mailing_BAO_Job::getTableName();
        if ( ! isset( $this->_columnHeaders ) ) {
            $this->_columnHeaders = array( 
                array(
                    'name'  => ts('Contact'),
                ), 
                array(
                    'name' => ts('Email Address'),
                ), 
            );
            if ($this->_event_type == 'bounce') {
                $this->_columnHeaders = array_merge($this->_columnHeaders, array(
                    array(
                        'name'  => ts('Bounce Type'),
                    ),
                    array(
                        'name'  => ts('Bounce Reason'),
                    ),
                ));
            } elseif ($this->_event_type == 'unsubscribe') {
                $this->_columnHeaders = array_merge($this->_columnHeaders, array(
                    array(
                        'name'  => ts('Opt-Out'),
                    ),
                ));
            } elseif ($this->_event_type == 'click') {
                $this->_columnHeaders = array_merge($this->_columnHeaders, array(
                    array(
                        'name'  => ts('URL'),
                    ),
                ));
            } elseif ($this->_event_type == 'forward') {
                $this->_columnHeaders = array_merge($this->_columnHeaders,
                array(
                    array(
                        'name'  => ts('Forwarded Email'),
                    ),
                ));
            }

            $this->_columnHeaders = array_merge($this->_columnHeaders,
                array(
                    array(
                        'name' => ts('Date'),
                    ), 
                ));
        }
        return $this->_columnHeaders;
    }


    /**
     * Returns total number of rows for the query.
     *
     * @param 
     * @return int Total number of rows 
     * @access public
     */
    function getTotalCount($action)
    {
        switch($this->_event_type) {
            case 'queue':
                    $event =& new CRM_Mailing_Event_BAO_Queue();
                    return $event->getTotalCount(   $this->_mailing_id, 
                                                    $this->_job_id );
                break;
            case 'delivered':
                    $event =& new CRM_Mailing_Event_BAO_Delivered();
                    return $event->getTotalCount(   $this->_mailing_id, 
                                                    $this->_job_id,
                                                    $this->_is_distinct );
                break;
            case 'opened':
                    $event =& new CRM_Mailing_Event_BAO_Opened();
                    return $event->getTotalCount(   $this->_mailing_id, 
                                                    $this->_job_id,
                                                    $this->_is_distinct );
                break;
            case 'bounce':
                    $event =& new CRM_Mailing_Event_BAO_Bounce();
                    return $event->getTotalCount(   $this->_mailing_id, 
                                                    $this->_job_id,
                                                    $this->_is_distinct );
                break;
            case 'forward':
                    $event =& new CRM_Mailing_Event_BAO_Forward();
                    return $event->getTotalCount(   $this->_mailing_id,
                                                    $this->_job_id,
                                                    $this->_is_distinct );
            case 'reply':
                    $event =& new CRM_Mailing_Event_BAO_Reply();
                    return $event->getTotalCount(   $this->_mailing_id, 
                                                    $this->_job_id,
                                                    $this->_is_distinct );
                break;
            case 'unsubscribe':
                    $event =& new CRM_Mailing_Event_BAO_Unsubscribe();
                    return $event->getTotalCount(   $this->_mailing_id, 
                                                    $this->_job_id,
                                                    $this->_is_distinct );
                break;
            case 'click':
                    $event =& new CRM_Mailing_Event_BAO_TrackableURLOpen();
                    return $event->getTotalCount(   $this->_mailing_id, 
                                                    $this->_job_id,
                                                    $this->_is_distinct,
                                                    $this->_url_id );
                break;
            default:
                return 0;
        }
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
        switch($this->_event_type) {
            case 'queue':
                    return
                    CRM_Mailing_Event_BAO_Queue::getRows($this->_mailing_id,
                                    $this->_job_id, $offset, $rowCount, $sort);
                break;
            case 'delivered':
                    return
                    CRM_Mailing_Event_BAO_Delivered::getRows($this->_mailing_id,
                                    $this->_job_id, $this->_is_distinct,
                                    $offset, $rowCount, $sort);

                break;
            case 'opened':
                    return
                    CRM_Mailing_Event_BAO_Opened::getRows($this->_mailing_id,
                                    $this->_job_id, $this->_is_distinct,
                                    $offset, $rowCount, $sort);
                break;
            case 'bounce':
                    return
                    CRM_Mailing_Event_BAO_Bounce::getRows($this->_mailing_id,
                                    $this->_job_id, $this->_is_distinct,
                                    $offset, $rowCount, $sort);
                break;
            case 'forward':
                    return
                    CRM_Mailing_Event_BAO_Forward::getRows($this->_mailing_id,
                                    $this->_job_id, $this->_is_distinct,
                                    $offset, $rowCount, $sort);
            case 'reply':
                    return
                    CRM_Mailing_Event_BAO_Reply::getRows($this->_mailing_id,
                                    $this->_job_id, $this->_is_distinct,
                                    $offset, $rowCount, $sort);
                break;
            case 'unsubscribe':
                    return
                    CRM_Mailing_Event_BAO_Unsubscribe::getRows($this->_mailing_id,
                                    $this->_job_id, $this->_is_distinct,
                                    $offset, $rowCount, $sort);
                break;
            case 'click':
                    return
                    CRM_Mailing_Event_BAO_TrackableURLOpen::getRows(
                                    $this->_mailing_id, $this->_job_id, 
                                    $this->_is_distinct, $this->_url_id,
                                    $offset, $rowCount, $sort);
                break;
            default:
                return null;
        }
    }

    /**
     * name of export file.
     *
     * @param string $output type of output
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv') {
    }

    function eventToTitle() {
        static $events = null;

        if (empty($events)) {
            $events = array(
                'queue'     => ts('Intended Recipients'),
                'delivered' => ts('Succesful Deliveries'),
                'bounce'    => ts('Bounces'),
                'forward'   => ts('Forwards'),
                'reply'     => $this->_is_distinct 
                            ? ts('Unique Replies') 
                            : ts('Replies'),
                'unsubscribe' => ts('Unsubscriptions'),
                'click'     => $this->_is_distinct 
                            ? ts('Unique Click-throughs') 
                            : ts('Click-throughs'),
                'opened'    => $this->_is_distinct
                            ? ts('Unique Tracked Opens')
                            : ts('Tracked Opens')
            );
        }
        return $events[$this->_event_type];
    }

    function getTitle() {
        return $this->eventToTitle();
    }
}//end of class

?>
