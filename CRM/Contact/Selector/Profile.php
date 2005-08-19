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
class CRM_Contact_Selector_Profile extends CRM_Core_Selector_Base implements CRM_Core_Selector_API 
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
    protected $_clause;

    /**
     * The tables involved in the query
     *
     * @var array
     * @access protected
     */
    protected $_tables;

    /**
     * the public visible fields to be shown to the user
     *
     * @var array
     * @access protected
     */
    protected $_fields;

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
        $this->_tables = $tables;

        $this->_fields = CRM_Core_BAO_UFGroup::getListingFields( CRM_Core_Action::VIEW,
                                                                 CRM_Core_BAO_UFGroup::PUBLIC_VISIBILITY |
                                                                 CRM_Core_BAO_UFGroup::LISTINGS_VISIBILITY );
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
            self::$_columnHeaders = array( );
            foreach ( $this->_fields as $field ) {
                self::$_columnHeaders[] = array( 'name'=> $field['title'] );
            }
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
        return $this->query( true, 0, 0 );
    }

    /**
     * run a query to retrieve all the ids related to this search
     *
     * @param boolean count - are we only interested in the count
     * @param int    $offset   the row number to start from
     * @param int    $rowCount the number of rows to return
     *
     * @return int|CRM_CORE_DAO   the total number of contacts or a dao object
     */
    function query( $count, $offset, $rowCount ) {
        if ( $count ) {
            $sql = ' SELECT count( DISTINCT( civicrm_contact.id ) ) ';
        } else {
            $sql = ' SELECT DISTINCT( civicrm_contact.id ) as contact_id ';
        }

        $sql .= CRM_Contact_BAO_Contact::fromClause( $this->_tables );
        $sql .= ' WHERE ' . $this->_clause;

        if ( $rowCount > 0 ) {
            $sql .= " LIMIT $offset, $rowCount ";
        }

        $dao =& new CRM_Core_DAO( );
        $dao->query($sql);

        if ( $count ) {
            $result = $dao->getDatabaseResult();
            $row    = $result->fetchRow();
            return $row[0];
        } else {
            return $dao;
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
        $result = $this->query(false, $offset, $rowCount);

        // process the result of the query
        $rows = array( );

        while ($result->fetch()) {
            $row = array( );
            CRM_Core_BAO_UFGroup::getValues( $result->contact_id, $this->_fields, $row );
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * name of export file.
     *
     * @param string $output type of output
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv') {
        return ts('CiviCRM Profile Listings');
    }
    
}//end of class

?>
