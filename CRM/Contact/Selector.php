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


require_once 'CRM/Pager.php';
require_once 'CRM/Selector/Base.php';
require_once 'CRM/Selector/API.php';


/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria
 *
 * This class is a generic class and should be used by any / all
 * objects that requires contacts to be selectively listed (list / search)
 *
 */
class CRM_Contact_Selector extends CRM_Selector_Base implements CRM_Selector_API {

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

    protected $_contact;

    function __construct() {
        $this->_contact = new CRM_Contacts_BAO_Individual();
    
        $this->_contact->domain_id = 1;
    }

    function &getLinks() {
        return self::$_links;
    }

    function getPagerParams( $action, &$params ) {
        $params['status']       = "Contacts %%StatusMessage%%";
        $params['csvString']    = null;
        $params['rowCount']     = CRM_Pager::ROWCOUNT;

        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    }

    function getSortOrder( $action ) {
        static $order = array(
                              'first_name' => CRM_Sort::DESCENDING,
                              'last_name'  => CRM_Sort::DESCENDING,
                              'id'         => CRM_Sort::ASCENDING,
                              );
        return $order;
    }

    function getColumnHeaders( $action ) {
        static $headers = array(
                                array(
                                      'name' => 'Contact Id',
                                      'sort' =>'id',
                                      ),
                                array(
                                      'name' => 'First Name',
                                      'sort' => 'first_name',
                                      ),
                                array(
                                      'name' => 'Last Name',
                                      'sort' => 'last_name',
                                      ),
                                );
        return $headers;
    }

    function getTotalCount( $action ) {
        return $this->_contact->count();
    }

    function getRows( $action, $offset, $rowCount, $sort ) {
        $rows = array();
        $this->_contact->limit( $offset, $rowCount );
        $this->_contact->orderBy( $sort->orderBy( ) );

        $this->_contact->find();
        while ( $this->_contact->fetch( ) ) {
            $row = array();
            $row['contact_id'] = $this->_contact->contact_id;
            $row['first_name'] = $this->_contact->first_name;
            $row['last_name']  = $this->_contact->last_name;

            $rows[] = $row;
        }
        return $rows;
    }

    function getTemplateFileName( $action ) {
        $className    = get_class( $this );
        $templateName = str_replace( '_', '/', $className ) . '.tpl';
        return $templateName;
    }

    function getExportColumnHeaders( $action, $type = 'csv' ) {
    }

    function getExportRows( $action, $type = 'csv' ) {
    }

    function getExportFileName( $action, $type = 'csv' ) {
    }

}

?>