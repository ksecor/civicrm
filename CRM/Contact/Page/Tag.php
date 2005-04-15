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

class CRM_Contact_Page_Tag {

    /**
     * class constructor
     */
    function __construct( ) {
    }

    static function browse( $page ) {
        $controller = new CRM_Controller_Simple( 'CRM_Tag_Form_Tag', 'Contact Tags', $mode );
        
        // set the userContext stack
        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();
        $session->pushUserContext( $config->httpBase . 'civicrm/contact/view/tag&op=browse' );

        $controller->reset( );
        $controller->set( 'contactId'  , $page->getContactId( ) );

        $controller->process( );
        $controller->run( );
    }

    static function run( $page ) {
        self::browse( $page );
    }

}

?>