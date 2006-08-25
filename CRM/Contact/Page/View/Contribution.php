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

class CRM_Contact_Page_View_Contribution extends CRM_Contact_Page_View {

   /**
     * This function is called when action is browse
     * 
     * return null
     * @access public
     */
    function browse( ) {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_Search', ts('Contributions'), $this->_action );
        $controller->setEmbedded( true );
        $controller->reset( );
        $controller->set( 'cid'  , $this->_contactId );
        $controller->set( 'context', 'contribution' ); 
        $controller->process( );
        $controller->run( );
    }

    /** 
     * This function is called when action is view
     *  
     * return null 
     * @access public 
     */ 
    function view( ) {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_ContributionView',  
                                                       'View Contribution',  
                                                       $this->_action ); 
        $controller->setEmbedded( true );  
        $controller->set( 'id' , $this->_id );  
        $controller->set( 'cid', $this->_contactId );  

        if ( $_GET['snippet'] ) {
            $controller->setPrint( CRM_Core_Smarty::PRINT_SNIPPET );
        }

        return $controller->run( ); 
    }

    /** 
     * This function is called when action is update or new 
     *  
     * return null 
     * @access public 
     */ 
    function edit( ) { 
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_Contribution', 
                                                       'Create Contribution', 
                                                       $this->_action );
        $controller->setEmbedded( true ); 
        $controller->set( 'id' , $this->_id ); 
        $controller->set( 'cid', $this->_contactId ); 

        return $controller->run( );
    }


   /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) {
        $this->preProcess( );

        $this->setContext( );

        if ( $this->_action & CRM_Core_Action::VIEW ) { 
            $this->view( ); 
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE ) ) { 
            $this->edit( ); 
        } else {
            $this->browse( );
        }

        return parent::run( );
    }

    function setContext( ) {
        $context = CRM_Utils_Request::retrieve( 'context', 'String',
                                                $this, false, 'search' );

        switch ( $context ) {
        case 'basic':
            $url = CRM_Utils_System::url( 'civicrm/contact/view/basic',
                                          'reset=1&cid=' . $this->_contactId );
            break;

        case 'dashboard':
            $url = CRM_Utils_System::url( 'civicrm/contribute',
                                          'reset=1' );
            break;

        case 'contribution':
            $url = CRM_Utils_System::url( 'civicrm/contact/view/contribution',
                                          'reset=1&force=1&cid=' . $this->_contactId );
            break;

        default:
            $cid = null;
            if ( $this->_contactId ) {
                $cid = '&cid=' . $this->_contactId;
            }
            $url = CRM_Utils_System::url( 'civicrm/contribute/search', 
                                          'reset=1&force=1' . $cid );
            break;
        }

        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $url );
    }

}

?>
