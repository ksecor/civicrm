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
class CRM_Contact_Selector extends CRM_Core_Selector_Base implements CRM_Core_Selector_API 
{
    /**
     * array of supported links, currenly null
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * we use desc to remind us what that column is, name is used in the tpl
     *
     * @var array
     * @static
     */
    static $_columnHeaders;

    /**
     * The sql clause we use to get the list of contacts
     *
     * @var string
     * @access protected
     */
    protected $_clause

    /**
     * The tables involved in the query
     *
     * @var array
     * @access protected
     */
    protected $_tables;

    /**
     * Class constructor
     *
     * @param string clause the query where clause
     * @param array  tables the tables involved in the query
     *
     * @return CRM_Contact_Selector_Profile
     * @access public
     */
    function __construct( &$clause, &$tables )
    {
        $this->_clause = $clause;
        $this->_tables = $table;
    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     *
     * @return array
     * @access public
     *
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
        $params['status']       = ts('Contact %%StatusMessage%%');
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
        if ( ! isset( self::$_columnHeaders ) ) {
        }
        return self::$_columnHeaders;
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
        return $this->query( true );
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
        $result = $this->query(false, $offset, $rowCount, $sort);

        // process the result of the query
        $rows = array( );

        $mask = CRM_Core_Action::mask( CRM_Core_Permission::getPermission( ) );

        while ($result->fetch()) {
            $row = CRM_Core_BAO_UFGroup::getDataValues( $result->id );
            $rows[] = $row;
        }
        return $rows;
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
        if ( ! isset( self::$_columnHeaders ) )
        {
            self::$_columnHeaders = array(
                                          array('desc' => ts('Contact Type') ),
                                          array(
                                                'name'      => ts('Name'),
                                                'sort'      => 'sort_name',
                                                'direction' => CRM_Utils_Sort::ASCENDING,
                                                ),
                                          array('name' => ts('Address') ),
                                          array(
                                                'name'      => ts('City'),
                                                'sort'      => 'city',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                        array(
                                              'name'      => ts('State'),
                                              'sort'      => 'state',
                                              'direction' => CRM_Utils_Sort::DONTCARE,
                                             ),
                                        array(
                                              'name'      => ts('Postal'),
                                              'sort'      => 'postal_code',
                                              'direction' => CRM_Utils_Sort::DONTCARE,
                                             ),
                                        array(
                                              'name'      => ts('Country'),
                                              'sort'      => 'country',
                                              'direction' => CRM_Utils_Sort::DONTCARE,
                                             ),
                                        array(
                                              'name'      => ts('Email'),
                                              'sort'      => 'email',
                                              'direction' => CRM_Utils_Sort::DONTCARE,
                                             ),
                                        array('name' => ts('Phone') ),
                                        array('desc' => ts('Actions') ),
                                    );
        }
        return self::$_columnHeaders;
    }


}//end of class

?>
