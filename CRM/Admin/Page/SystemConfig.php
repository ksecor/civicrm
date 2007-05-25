<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Page for displaying System and User Config
 */
class CRM_Admin_Page_SystemConfig extends CRM_Core_Page
{
    protected $_system    = false;
    protected $_contactID = null;
    protected $_action    = null;

    function preProcess( ) {
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Postive',
                                                         $this, false );
        $this->_system    = CRM_Utils_Request::retrieve( 'system', 'Boolean',
                                                         $this, false, false );
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'view' );
        $this->assign( 'action', $action );

        if ( $this->_system ) {
            if ( CRM_Core_Permission::check( 'administer CiviCRM' ) ) {
                $this->_contactID = null;
            } else {
                CRM_Utils_System::fatal( 'You do not have permission to edit system options' );
            }
        } else {
            if ( ! $this->_contactID ) {
                $session =& CRM_Core_Session::singleton( );
                $this->_contactID = $session->get( 'userID' );
                if ( ! $this->_contactID ) {
                    CRM_Utils_System::fatal( 'Could not retrieve contact id' );
                }
                $this->set( 'cid', $this->_contactID );
            }
        }
    }

    function run ( ) {
        $this->preProcess( );

        if ( $this->_action & ( CRM_Core_Action::ADD | CRM_Core_Action::UPDATE ) ) {
            return $this->edit( );
        } else {
            return $this->view( );
        }
    }

    function edit( ) {
        require_once 'CRM/Admin/Form/SystemConfig.php';
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Admin_Form_SystemConfig',
                                                       'System Settings', $this->_action );
        $controller->process( );
        $controller->run( );
    }

    function view( ) {
        require_once 'CRM/Core/DAO/SystemConfig.php';
        $dao =& new CRM_Core_DAO_SystemConfig( );

        if ( $this->_system ) {
            $dao->is_domain  = 1;
        } else {
            $dao->is_domain  = 0;
            $dao->contact_id = $this->_contactID;
        }
        

    }
}