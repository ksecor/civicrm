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
require_once 'CRM/Selector/Base.php';
require_once 'CRM/Selector/API.php';
require_once 'CRM/Form.php';
require_once 'CRM/Contact/Individual.php';


/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria
 *
 * This class is a generic class and should be used by any / all
 * objects that requires contacts to be selectively listed (list / search)
 *
 */
class CRM_Contact_Individual_Selector extends CRM_Selector_Base implements CRM_Selector_API 
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
    /**
     * This caches the content for the display system.
     *
     * @var string
     */
    protected $_contact;
    //var $_form;
    
    function __construct() 
    {

        //object of BAO_Contact_Individual for fetching the records from db
        $this->_contact = new CRM_Contact_BAO_Contact_Individual();
        $this->_contact->domain_id = 1;
    }//end of constructor


    /** 
     * This function is used to provide the dynamic HTML content to the Selector template.
     * The return value of this function is an array which is assigned to the form variable
     * in smarty and is used to access the form elements.
     */


    function buildForm($name) {
        
        $form = new CRM_Form($name, 'post', "/drupal/index.php?q=crm/contact/list");
        $form->addElement('text', 'abc', 'abc');
        return $form->toSmarty();
    }

    
    function &getLinks() 
    {
        return CRM_Contact_Selector::$_links;
    }//end of function

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
     * getter for the sorting direction for the fields which will be displayed on the form. 
     *
     * @param 
     * @return array 
     * @access public
     */    
    function getSortOrder($action) 
    {
        static $order = array(
                              'crm_contact_id'                             => CRM_Sort::ASCENDING,                              
                              'crm_contact_domain_id'                      => CRM_Sort::ASCENDING,                              
                              'crm_contact_sort_name'                      => CRM_Sort::DESCENDING,
                              'crm_contact_contact_type'                   => CRM_Sort::DESCENDING,
                              'crm_contact_preferred_communication_method' => CRM_Sort::DESCENDING,
                              );
        return $order;
    }//end of function

    /**
     * getter for headers for each column of the displayed form.
     *
     * @param 
     * @return array 
     * @access public
     */
    function getColumnHeaders($action) 
    {
        static $headers = array(
                                array(
                                      'name' => 'Contact ID',
                                      'sort' => 'crm_contact_id',
                                      ),
                                array(
                                      'name' => 'Domain ID',
                                      'sort' => 'crm_contact_domain_id',
                                      ),
                                
                                array(
                                      'name' => 'Name',
                                      'sort' => 'crm_contact_sort_name',
                                      ),
                                array(
                                      'name' => 'Contact',
                                      'sort' => 'crm_contact_contact_type',
                                      ),
                                array(
                                      'name' => 'Communication Method',
                                      'sort' => 'crm_contact_preferred_communication_method',
                                      ),
                                
                                );
        return $headers;
    }//end of function

    
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
        $rows = array();
        $this->_contact->limit($offset, $rowCount);
        $this->_contact->orderBy($sort->orderBy());
        $this->_contact->find();
        while ($this->_contact->fetch()) {
            $row = array();
            
            $row['contact_id']                     = $this->_contact->crm_contact_id;
            $row['domain_id']                      = $this->_contact->crm_contact_domain_id;
            $row['sort_name']                      = $this->_contact->crm_contact_sort_name;
            $row['contact_type']                   = (strlen(trim($this->_contact->crm_contact_contact_type))) ? $this->_contact->crm_contact_contact_type : "-----";
            $row['preferred_communication_method'] = (strlen(trim($this->_contact->crm_contact_preferred_communication_method))) ? $this->_contact->crm_contact_preferred_communication_method : "-----";

            $rows[] = $row;
        }//end of while loop
        return $rows;
    }//end of function
    

    /**
     *getter for template file name
     *
     * @param string
     * @return tempalate file name
     * @access public
     */
    function getTemplateFileName($action) 
    {
        $className    = get_class($this);
        $templateName = str_replace('_', '/', $className) . '.tpl';
        return $templateName;
    }//end of function

    function getExportColumnHeaders($action, $type = 'csv') 
    {
    }//end of function

    function getExportRows($action, $type = 'csv') 
    {
    }//end of function

    function getExportFileName($action, $type = 'csv') 
    {
    }//end of function


}//end of class


?>