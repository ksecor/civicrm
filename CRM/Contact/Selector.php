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
require_once 'CRM/Contact/BAO/Contact.php';


/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria
 *
 * This class is a generic class and should be used by any / all
 * objects that requires contacts to be selectively listed (list / search)
 *
 */
class CRM_Contact_Selector extends CRM_Selector_Base implements CRM_Selector_API 
{
    /**
     * This defines two actions- View and Edit.
     *
     * @var array
     */
    static $_links = array(
                           CRM_Action::VIEW => array(
                                                     'name'     => 'View Contact',
                                                     'link'     => '/crm/contact?action=view&id=%%id%%',
                                                     'linkName' => 'View Contact',
                                                     'menuName' => 'View Contact Details'
                                                     ),
                           CRM_Action::EDIT => array(
                                                     'name'     => 'Edit Contact',
                                                     'link'     => '/crm/contact?action=edit&id=%%id%%',
                                                     'linkName' => 'Edit Contact',
                                                     'menuName' => 'Edit Contact Details'
                                                     ),
                           );

        static $_columnHeaders = array(
                                       array('name' => ''),
                                       array('name' => ''),
                                       array(
                                             'name'      => 'Name',
                                             'sort'      => 'crm_contact_sort_name',
                                             'direction' => CRM_Sort::ASCENDING,
                                             ),
                                       array('name' => 'Address'),
                                       array(
                                             'name'      => 'City',
                                             'sort'      => 'crm_address_city',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array(
                                             'name'      => 'State',
                                             'sort'      => 'crm_state_province_name',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array('name' => 'Postal'),
                                       array(
                                             'name'      => 'Country',
                                             'sort'      => 'crm_country_name',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array('name' => 'Email'),
                                       array('name' => 'Phone'),
                                       array('name' => ''),
                                       );
    
    /**
     * This caches the content for the display system.
     *
     * @var string
     */
    protected $_contact;

    /**
     * Class constructor
     *
     * @param array $params (reference ) array of parameters for query
     *
     * @return CRM_Contact_Selector
     * @access public
     */
    function __construct(&$params) 
    {
        //object of BAO_Contact_Individual for fetching the records from db
        $this->_contact = new CRM_Contact_BAO_Contact();
        
        foreach ($params as $name => $value) {
            $this->_contact->$name = $value;
        }
        
    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     * currently the links added for each row are 
     * 
     * - View
     * - Edit
     *
     * @param none
     *
     * @return array
     * @access public
     *
     */
    function &getLinks() 
    {
        return CRM_Contact_Selector::$_links;
    } //end of function

    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) 
    {
        $params['status']       = "Contact %%StatusMessage%%";
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
        return $this->_contact->count();
    }//end of function


    /**
     * getter for all the database values to be displayed on the form while listing
     *
     * @param 
     * @return array 
     * @access public
     */
    function getRows($action, $offset, $rowCount, $sort)
    {
        return $this->_contact->getSearchRows($offset, $rowCount, $sort);
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