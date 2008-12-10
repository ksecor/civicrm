<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Contact/Page/View.php';
require_once 'CRM/Case/BAO/Case.php';

/**
 * This class handle case related functions
 *
 */
class CRM_Case_Page_Tab extends CRM_Contact_Page_View 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    function preProcess( )
    {
        $this->_id        = CRM_Utils_Request::retrieve( 'id' , 'Positive', $this );
        $this->_contactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        $this->_context   = CRM_Utils_Request::retrieve( 'context', 'String', $this );

        // contact id is not mandatory for case form. If not found, don't call
        // parent's pre-process and proceed further.
        if ( $this->_contactId ) {
            parent::preProcess( );
        } else {
            // we would need action to proceed further.
            $this->_action = CRM_Utils_Request::retrieve('action', 'String',
                                                         $this, false, 'add');
            if ( $this->_action & CRM_Core_Action::VIEW ) {
                CRM_Core_Error::fatal('Contact Id is required for view action.');
            }
            $this->assign( 'action', $this->_action);
        }

        $activityTypes = CRM_Case_PseudoConstant::activityType( );

        $this->assign( 'openCaseId'        ,$activityTypes['Open Case']['id']);
        $this->assign( 'changeCaseTypeId'  ,$activityTypes['Change Case Type']['id']);
        $this->assign( 'changeCaseStatusId',$activityTypes['Change Case Status']['id']);
    }

    /**
     * View details of a case
     *
     * @return void
     * @access public
     */
    function view( ) 
    {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Case_Form_CaseView',
                                                       'View Case',  
                                                       $this->_action ); 
        $controller->setEmbedded( true ); 
        $controller->set( 'id' , $this->_id );  
        $controller->set( 'cid', $this->_contactId );
        $controller->run();
        
        $this->assign( 'caseId',$this->_id);
        require_once 'CRM/Activity/Selector/Activity.php' ;
        require_once 'CRM/Core/Selector/Controller.php';
        $output = CRM_Core_Selector_Controller::SESSION;
        $selector   =& new CRM_Activity_Selector_Activity($this->_contactId, $this->_permission, false, 'case' );
        $controller =& new CRM_Core_Selector_Controller($selector, $this->get(CRM_Utils_Pager::PAGE_ID),
                                                        $sortID, CRM_Core_Action::VIEW, $this,  $output, null, $this->_id);
        
        
        $controller->setEmbedded(true);

        $controller->run();
        $controller->moveFromSessionToTemplate( );

        $this->assign( 'context', 'case');
    }

    /**
     * This function is called when action is browse
     *
     * return null
     * @access public
     */
    function browse( ) {
       
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Case_Form_Search', ts('Case'), null ); 
        $controller->setEmbedded( true ); 
        $controller->reset( ); 
        $controller->set( 'limit', 20 );
        $controller->set( 'force', 1 );
        $controller->set( 'context', 'case' ); 
        $controller->process( ); 
        $controller->run( ); 
    }

    /**
     * This function is called when action is update or new
     * 
     * return null
     * @access public
     */
    function edit( ) 
    {
        $config =& CRM_Core_Config::singleton( );

        $controller =& new CRM_Core_Controller_Simple( 'CRM_Case_Form_Case', 
                                                       'Open Case', 
                                                       $this->_action );
        
        $controller->setEmbedded( true );
        $controller->set( 'id' , $this->_id ); 
        $controller->set( 'cid', $this->_contactId ); 
        
        return $controller->run( );
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

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->view( );
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE | CRM_Core_Action::RENEW ) ) {
            $this->edit( );
        } else if ( $this->_contactId ) {
            $this->browse( );
        }

        $this->setContext( );

        return parent::run( );
    }


    /**
     * Get action links
     *
     * @return array (reference) of action links
     * @static
     */
    static function &links()
    {
        $config =& CRM_Core_Config::singleton( );
       
        if (!(self::$_links)) {
            $deleteExtra = ts('Are you sure you want to delete this case?');
            self::$_links = array(
                                  CRM_Core_Action::VIEW    => array(
                                                                    'name'  => ts('Manage Case'),
                                                                    'url'   => 'civicrm/contact/view/case',
                                                                    'qs'    => 'action=view&reset=1&cid=%%cid%%&id=%%id%%',
                                                                    'title' => ts('Manage Case')
                                                                    ),
                                  
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/contact/view/case',
                                                                    'qs'    => 'action=delete&reset=1&cid=%%cid%%&id=%%id%%',
                                                                    'title' => ts('Delete Case')
                                                                    ),
                                  
                                  );
        }
        return self::$_links;
    }
    
    function setContext( ) 
    {
        $context = $this->get('context');
        $url     = null;

        switch ( $context ) {
        case 'activity':
            if ( $this->_contactId ) {
                $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                              "reset=1&force=1&cid={$this->_contactId}&selectedChild=activity" );
            }
            break;
            
        case 'dashboard':           
            $url = CRM_Utils_System::url( 'civicrm/case', 'reset=1' );
            break;
                
        case 'search':
            $url = CRM_Utils_System::url( 'civicrm/case/search', 'force=1' );
            break;
                
        case 'home':
            $url = CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' );
            break;

        default:
            if ( $this->_contactId ) {
                $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                              "reset=1&force=1&cid={$this->_contactId}&selectedChild=case" );
            }
            break;
        }
        
        if ( $url ) {
            $session =& CRM_Core_Session::singleton( ); 
            $session->pushUserContext( $url );
        }
    }
}


