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

require_once 'CRM/Contact/Page/View.php';

/**
 * Page for displaying list of Meetings
 */
class CRM_Contact_Page_View_Meeting extends CRM_Contact_Page_View
{

    function edit( )
    {
        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url('civicrm/contact/view/activity', 'action=browse' ) );

        $controller =& new CRM_Core_Controller_Simple( 'CRM_Activity_Form_Meeting', 'Contact Meetings', $this->_action );
        $controller->reset( );

        $controller->setEmbedded( true );
        $controller->set( 'contactId', $page->getContactId( ) );
        $controller->set( 'id'       , $this->_id );
        $controller->set( 'pid'      , $page->get( 'pid' ) );
        $controller->set( 'log'      , $page->get( 'log' ) );

        $controller->process( );
        $controller->run( );
    }

    function run( )
    {
        $pid = CRM_Utils_Request::retrieve( 'pid', $page ); 
        $log = CRM_Utils_Request::retrieve( 'log', $page ); 
        
        if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::VIEW) ) {
            $this->edit( );
        } else if ( $this->_action & CRM_Core_Action::DELETE ) {
            $this->delete( );
        }
    }
    
    function delete( ) 
    {
        CRM_Core_BAO_Meeting::del( $this->_id );
    }

}
?>
