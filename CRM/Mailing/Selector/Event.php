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
                array(
                    'name' => ts('Date'),
                ), 
            );
            if ($this->_event_type == 'bounce') {
                $this->_columnHeaders += array(
                    array(
                        'name'  => ts('Bounce Type'),
                    ),
                    array(
                        'name'  => ts('Bounce Reason'),
                    ),
                );
            } elseif ($this->_event_type == 'unsubscribe') {
                $this->_columnHeaders += array(
                    array(
                        'name'  => ts('Opt-Out'),
                    ),
                );
            } elseif ($this->_event_type == 'url') {
                $this->_columnHeaders += array(
                    array(
                        'name'  => ts('URL'),
                    ),
                );
            }
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
    }

    /**
     * name of export file.
     *
     * @param string $output type of output
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv') {
    }
    
}//end of class

?>
