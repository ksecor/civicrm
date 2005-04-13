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

require_once 'CRM/Page.php';

class CRM_Contact_Page_GroupContact {

    /**
     * class constructor
     */
    function __construct( ) {
    }

    static function view( $page, $groupId ) {
        $groupContact = new CRM_Contact_DAO_GroupContact( );
        $groupContact->id = $groupContactId;
        if ( $groupContact->find( true ) ) {
            $values = array( );
            $groupContact->storeValues( $values );
            $page->assign( 'groups', $values );
        }
        
        self::browse( $page );
    }

    static function browse( $page ) {
  
        $groupContact = new CRM_Contact_DAO_GroupContact( );

        $contactId   = $page->getContactId( );

        /*
        $groupContact->contact_id   = $page->getContactId( );

        $groupContact->orderBy( 'id desc' );

        $values = array( );
        $groupContact->find( );
        while ( $groupContact->fetch( ) ) {
            $values[$groupContact->id] = array( );
            $groupContact->storeValues( $values[$groupContact->id] );
        }
        */
        
        $strSql = "SELECT crm_group.id as crm_group_id,crm_group.name as crm_group_name,
                           crm_group_contact.in_date as in_date, crm_group_contact.out_date as out_date
                    FROM crm_group, crm_group_contact 
                    WHERE crm_group.id = crm_group_contact.group_id
                      AND crm_group_contact.contact_id = ".$contactId;
        
        $groupContact->query($strSql);
     
        $count = 0;
        while ( $groupContact->fetch() ) {
            
            $values[$groupContact->crm_group_id]['id'] = $groupContact->crm_group_id;
            $values[$groupContact->crm_group_id]['name'] = $groupContact->crm_group_name;
            $values[$groupContact->crm_group_id]['in_date'] = $groupContact->in_date;
            $values[$groupContact->crm_group_id]['out_date'] = $groupContact->out_date;
            
            $groupContact->storeValues( $values[$groupContact->crm_group_id] );
            $count++;
        }

        $page->assign( 'groupCount', $count );
        $page->assign( 'groupContact', $values );
    }

    static function edit( $page, $mode, $groupId = null ) {

        $controller = new CRM_Controller_Simple( 'CRM_GroupContact_Form_GroupContact', 'Contact GroupContacts', $mode );

        // set the userContext stack
        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();
        $session->pushUserContext( $config->httpBase . 'contact/view/group&op=browse' );

        $controller->reset( );

        $controller->set( 'contactId'  , $page->getContactId( ) );
        $controller->set( 'groupId'   , $groupId );
 
        $controller->process( );
        $controller->run( );

    }

    static function run( $page ) {
        $contactId = $page->getContactId( );
        $page->assign( 'contactId', $contactId );

        $op = CRM_Request::retrieve( 'op', $page, false, 'browse' );
        $page->assign( 'op', $op );

        switch ( $op ) {
        case 'view':
            $groupId = $_GET['gcid'];
            self::view( $page, $groupId );
            break;

        case 'edit':
            $groupId = $_GET['gcid'];
            self::edit( $page, CRM_Form::MODE_UPDATE, $groupId );
            break;

        case 'add':
            self::edit( $page, CRM_Form::MODE_ADD );
            break;
        }

        self::browse( $page );
    }

}

?>