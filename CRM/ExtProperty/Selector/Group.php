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
 * $Id$
 *
 */

require_once 'CRM/Pager.php';
require_once 'CRM/Sort.php';
require_once 'CRM/Selector/Base.php';
require_once 'CRM/Selector/API.php';
require_once 'CRM/Form.php';

/**
 * This class is used to retrieve and display a range of
 * groups that belong to a domain
 *
 */
class CRM_ExtProperty_Selector_Group extends CRM_Selector_Base implements CRM_Selector_API 
{
    /**
     * This defines two actions- View and Edit.
     *
     * @var array
     */
    static $_links = array(
                           CRM_Action::VIEW    => array(
                                                        'name'  => 'View',
                                                        'link'  => 'civicrm/extproperty/group?op=view&id=%%id%%',
                                                        'title' => 'View Extended Property Group',
                                                        ),
                           CRM_Action::EDIT    => array(
                                                        'name'  => 'Edit',
                                                        'link'  => 'civicrm/extproperty/group?op=edit&id=%%id%%',
                                                        'title' => 'Edit Extended Property Group'),
                           CRM_Action::DISABLE => array(
                                                        'name'  => 'Disable',
                                                        'link'  => 'civicrm/extproperty/group?op=disable&id=%%id%%',
                                                        'title' => 'Disable Extended Property Group',
                                                        ),
                           CRM_Action::EXPAND  => array(
                                                         'name'  => 'List',
                                                         'link'  => 'civicrm/extproperty/field?op=browse&gid=%%id%%',
                                                         'title' => 'List Extended Property Group Fields',
                                                         ),
                           );

    static $_columnHeaders = array(
                                   array('name'      => 'Title',
                                         'sort'      => 'title',
                                         'direction' => CRM_Sort::ASCENDING,
                                         ),
                                   array(
                                         'name'      => 'Description',
                                         ),
                                   array(
                                         'name'      => 'Status',
                                         ),
                                   array(
                                         'name'      => 'Used For',
                                         ),
                                   array(
                                         'name'      => '',
                                         ),
                                   );
    
    /**
     * Class constructor
     *
     * @param array $params (reference ) array of parameters for query
     *
     * @return CRM_Contact_Selector
     * @access public
     */
    function __construct( )
    {
        $this->_group = new CRM_DAO_ExtPropertyGroup( );
    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     *
     * @param none
     *
     * @return array
     * @access public
     *
     */
    function &getLinks() 
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
        $params['status']       = "Extended Property Group %%StatusMessage%%";
        $params['csvString']    = null;
        $params['rowCount']     = CRM_Pager::ROWCOUNT;

        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    }//end of function

    /**
     * getter for headers for each column of the displayed form.
     *
     * @param 
     * @return array (reference)
     * @access public
     */
    function &getColumnHeaders($action) 
    {
        return self::$_columnHeaders;
    }


    /**
     * getter for all the database values to be displayed on the form while listing
     *
     * @param 
     * @return array 
     * @access public
     */
    function getTotalCount($action)
    {
        return $this->_group->count( );
    }//end of function


    /**
     * getter for all the database values to be displayed on the form while listing
     *
     * @param int      $action   the type of action links
     * @param int      $offset   the offset for the query
     * @param int      $rowCount the number of rows to return
     * @param CRM_Sort $sort     the sort object
     *
     * @return array (reference)
     * @access public
     */
    function &getRows($action, $offset, $rowCount, $sort)
    {
        $result = $this->_group->find( );

        $rows = array( );
        while ($result->fetch( )) {
            $rows[$result->id] = array();
            $result->storeValues( $rows[$result->id] );

            $row['edit'] = CRM_System::url( 'civicrm/contact/edit', 'reset=1&cid=' . $result->contact_id );
            $row['view'] = CRM_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $result->contact_id );
        }

        return $rows;
    }
    

    function getExportColumnHeaders($action, $type = 'csv')
    {
    }

    function getExportRows($action, $type = 'csv')
    {
    }

    function getExportFileName($action, $type = 'csv')
    {
    }


}//end of class

?>