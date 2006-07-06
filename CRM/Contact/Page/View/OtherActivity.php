<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contact/Page/View.php';

/**
 * Page for displaying list of OtherActivitys
 */
class CRM_Contact_Page_View_OtherActivity extends CRM_Contact_Page_View
{

     /**
     * This function is called when action is update or new
     * 
     * return null
     * @access public
     */
    
    function edit( )
    {
        //set the path depending on open activity or activity history (view mode)
        $history = CRM_Utils_Request::retrieve( 'history', 'String',
                                                $this ); 
        $context = CRM_Utils_Request::retrieve( 'context', 'String',
                                                $this );
        
        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        if ( $context == 'Home' ) {
            $url = CRM_Utils_System::url('civicrm', 'reset=1' );
        } else {
            $url = CRM_Utils_System::url('civicrm/contact/view/activity',
                                         "show=1&action=browse&reset=1&history={$history}&cid={$this->_contactId}" );
        }
        $session->pushUserContext( $url );
        
        if (CRM_Utils_Request::retrieve('confirmed', 'Boolean',
                                        CRM_Core_DAO::$_nullObject )){
            require_once 'CRM/Core/BAO/OtherActivity.php';
            CRM_Core_BAO_OtherActivity::del( $this->_id);
            CRM_Utils_System::redirect($url);
        }

        $controller =& new CRM_Core_Controller_Simple( 'CRM_Activity_Form_OtherActivity', ts('Contact Other Activity'), $this->_action );
        $controller->reset( );
        $controller->setEmbedded( true );

        $controller->set( 'contactId', $this->_contactId );
        $controller->set( 'id'       , $this->_id );
        $controller->set( 'pid'      , $this->get( 'pid' ) );
        $controller->set( 'log'      , $this->get( 'log' ) );

        $controller->process( );
        $controller->run( );
    }

    /**
     * This function is the main function that is called when the page loads,
     * it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( )
    {
        $this->preProcess( );

        $pid = CRM_Utils_Request::retrieve( 'pid', 'Positive',
                                            $this ); 
        $log = CRM_Utils_Request::retrieve( 'log', 'String',
                                            $this ); 
        
        if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::VIEW | CRM_Core_Action::DELETE) ) {
            $this->edit( );
        }

        return parent::run( );
    }
}
?>
