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
 * $Id: Selector.php 11510 2007-09-18 09:21:34Z lobo $
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/Selector/API.php';

require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';

require_once 'CRM/Contact/BAO/Contact.php';
require_once 'CRM/Contact/BAO/Query.php';
require_once 'CRM/Contact/BAO/SearchCustom.php';

/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria (specifically for
 * results of advanced search options.
 *
 */
class CRM_Contact_Selector_Custom extends CRM_Core_Selector_Base implements CRM_Core_Selector_API 
{
    /**
     * This defines two actions- View and Edit.
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
     * Properties of contact we're interested in displaying
     * @var array
     * @static
     */
    static $_properties = array( 'contact_id', 'contact_type', 'display_name' );

    /**
     * This caches the content for the display system.
     *
     * @var string
     * @access protected
     */
    protected $_contact;

    /**
     * formValues is the array returned by exportValues called on
     * the HTML_QuickForm_Controller for that page.
     *
     * @var array
     * @access protected
     */
    public $_formValues;

    /**
     * params is the array in a value used by the search query creator
     *
     * @var array
     * @access protected
     */
    public $_params;

    /**
     * represent the type of selector
     *
     * @var int
     * @access protected
     */
    protected $_action;

    protected $_query;

    /**
     * the public visible fields to be shown to the user
     *
     * @var array
     * @access protected
     */
    protected $_fields;

    /**
     * The object that implements the search interface
     */
    protected $_search;

    /**
     * Class constructor
     *
     * @param array $formValues array of form values imported
     * @param array $params     array of parameters for query
     * @param int   $action - action of search basic or advanced.
     *
     * @return CRM_Contact_Selector
     * @access public
     */
    function __construct( $customSearchClass,
                          $formValues = null,
                          $params = null,
                          $returnProperties = null,
                          $action = CRM_Core_Action::NONE,
                          $includeContactIds = false,
                          $searchChildGroups = true ) {
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $customSearchClass ) . '.php' );
        eval( '$this->_search = new ' . $customSearchClass . '( $formValues );' );
    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     * currently the links added for each row are 
     * 
     * - View
     * - Edit
     *
     * @return array
     * @access public
     *
     */
    static function &links( ) {

        if (!(self::$_links)) {
            self::$_links = array(
                                  CRM_Core_Action::VIEW   => array(
                                                                   'name'     => ts('View'),
                                                                   'url'      => 'civicrm/contact/view',
                                                                   'qs'       => 'reset=1&cid=%%id%%',
                                                                   'title'    => ts('View Contact Details'),
                                                                  ),
                                  CRM_Core_Action::UPDATE => array(
                                                                   'name'     => ts('Edit'),
                                                                   'url'      => 'civicrm/contact/add',
                                                                   'qs'       => 'reset=1&action=update&cid=%%id%%',
                                                                   'title'    => ts('Edit Contact Details'),
                                                                  ),
                                  );

            $config = CRM_Core_Config::singleton( );
            if ( $config->mapAPIKey && $config->mapProvider) {
                self::$_links[CRM_Core_Action::MAP] = array(
                                                            'name'     => ts('Map'),
                                                            'url'      => 'civicrm/contact/map',
                                                            'qs'       => 'reset=1&cid=%%id%%',
                                                            'title'    => ts('Map Contact'),
                                                            );
            }
        }
        return self::$_links;
    } //end of function


    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) {
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
    function &getColumnHeaders($action = null, $output = null) {
        $columns = $this->_search->columns( );
        if ( $output == CRM_Core_Selector_Controller::EXPORT ) {
            return array_keys( $columns );
        } else {
            $headers = array( );
            foreach ( $columns as $name => $key ) {
                $headers[] = array( 'name' => $name,
                                    'sort' => $key,
                                    'direction' => CRM_Utils_Sort::ASCENDING );
            }
            return $headers;
        }
    }


    /**
     * Returns total number of rows for the query.
     *
     * @param 
     * @return int Total number of rows 
     * @access public
     */
    function getTotalCount( $action ) {
        return CRM_Contact_BAO_SearchCustom::getTotalCount( $this->_search );
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
        $config =& CRM_Core_Config::singleton( );


        $params = array( );
        $sql = $this->_search->all( $params, $offset, $rowCount, $sort );
        CRM_Contact_BAO_SearchCustom::addDomainClause( $sql, $params );

        if ( ( $output == CRM_Core_Selector_Controller::EXPORT || 
               $output == CRM_Core_Selector_Controller::SCREEN ) &&
             $this->_formValues['radio_ts'] == 'ts_sel' ) {
            CRM_Contact_BAO_SearchCustom::includeContactIDs( $sql, $this->_formValues );
        }

        CRM_Contact_BAO_SearchCustom::addSortOffset( $sql, $offset, $rowcount, $sort );

        $dao = CRM_Core_DAO::executeQuery( $sql, $params );

        $columnNames = $this->_search->columns( );
        $columnNames = array_values( $columnNames );
        $links       = self::links( );
        $mask        = CRM_Core_Action::mask( CRM_Core_Permission::getPermission( ) );

        // process the result of the query
        $rows = array( );
        while ( $dao->fetch( ) ) {
            $row = array();

            // the columns we are interested in
            foreach ($columnNames as $property) {
                if ( ! empty( $dao->$property ) ) {
                    $row[$property] = $dao->$property;
                }
            }
            if ( ! empty( $row ) ) {
                $row['checkbox'] = CRM_Core_Form::CB_PREFIX . $dao->contact_id;
                $row['action']   = CRM_Core_Action::formLink( $links,
                                                              $mask ,
                                                              array( 'id' => $dao->contact_id ) );
                $rows[] = $row;
            }
        }

        return $rows;
    }
   
    /**
     * Given the current formValues, gets the query in local
     * language
     *
     * @param  array(reference)   $formValues   submitted formValues
     *
     * @return array              $qill         which contains an array of strings
     * @access public
     */
  
    // the current internationalisation is bad, but should more or less work
    // for most of "European" languages
    public function getQILL( )
    {
        return null;
    }

    /**
     * name of export file.
     *
     * @param string $output type of output
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv') {
        return ts('CiviCRM Custom Search');
    }

    function &alphabetQuery( ) {
        $params = array( );
        $sql = $this->_search->alphabet( $params, 0, 0, null );
        CRM_Contact_BAO_SearchCustom::addDomainClause( $sql, $params );

        return CRM_Core_DAO::executeQuery( $sql, $params );
    }

    function &contactIDQuery( ) {
        $params = array( );
        $sql = $this->_search->contactIDs( $params );
        CRM_Contact_BAO_SearchCustom::addDomainClause( $sql, $params );

        return CRM_Core_DAO::executeQuery( $sql, $params );
    }

}//end of class

?>
