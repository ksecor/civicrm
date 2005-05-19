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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contact/DAO/SavedSearch.php';

class CRM_Contact_Page_SavedSearch extends CRM_Core_Page {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links;

    function delete( $id ) {
        $savedSearch = new CRM_Contact_DAO_SavedSearch();
        $savedSearch->id = $id;
        $savedSearch->delete( );
        return;
    }

    function browse( ) {
        $rows = array();
        
        $savedSearch = new CRM_Contact_DAO_SavedSearch();
        $savedSearch->selectAdd();
        $savedSearch->selectAdd('id, name, search_type, description, form_values');
        $savedSearch->find();
        $properties = array('id', 'name', 'description');
        while ($savedSearch->fetch()) {
            $row = array();
            foreach ($properties as $property) {
                $row[$property] = $savedSearch->$property;
            }
            $row['query_detail'] = CRM_Contact_Selector::getQILL(unserialize($savedSearch->form_values), $savedSearch->search_type);
            $row['action']       = CRM_Core_Action::formLink( self::links(), null, array( 'id' => $row['id'] ) );
            $rows[] = $row;
        }
        $this->assign('rows', $rows);
        
        return parent::run();
    }

    function run( ) {
        $action = CRM_Utils_Request::retrieve( 'action', $this, false, 'browse' );

        $this->assign( 'action', $action );

        if ( $action & CRM_Core_Action::DELETE ) {
            $id  = CRM_Utils_Request::retrieve( 'id', $this, true );
            $this->delete($id );
        } 
        $this->browse( );
    }

    static function &links()
    {

        if ( ! isset( self::$_links ) ) 
        {

            $deleteExtra = ts('Do you really want to remove this Saved Search?');

            self::$_links = array(
                                  CRM_Core_Action::VIEW   => array(
                                                                   'name'  => ts('Search'),
                                                                   'url'   => 'civicrm/contact/search/advanced',
                                                                   'qs'    => 'reset=1&force=1&ssID=%%id%%',
                                                                   'title' => ts('Search')
                                                                  ),
                                  CRM_Core_Action::DELETE => array(
                                                                   'name'  => ts('Delete'),
                                                                   'url'   => 'civicrm/contact/search/saved',
                                                                   'qs'    => 'action=delete&id=%%id%%',
                                                                   'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
                                                                   'title' => ts('Delete Saved Search')
                                                                  ),
                                 );
        }
        return self::$_links;
    }

}

?>