<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once 'CRM/Contact/Page/View.php';

/**
 * Main page for viewing history of activities.
 *
 */

class CRM_Contact_Page_View_Activity extends CRM_Contact_Page_View {

    /**
     * Browse all activities for a particular contact
     *
     * @return none
     *
     * @access public
     */
    function browse( )
    {
        $this->assign( 'totalCountOpenActivity',
                       CRM_Contact_BAO_Contact::getNumOpenActivity( $this->_contactId ) );
        require_once 'CRM/Core/Selector/Controller.php';

        $output = CRM_Core_Selector_Controller::SESSION;
        require_once 'CRM/Activity/Selector/Activity.php';
        $selector   =& new CRM_Activity_Selector_Activity($this->_contactId, $this->_permission );
        $sortID     = null;
        if ( $this->get( CRM_Utils_Sort::SORT_ID  ) ) {
            $sortID = CRM_Utils_Sort::sortIDValue( $this->get( CRM_Utils_Sort::SORT_ID  ),
                                                   $this->get( CRM_Utils_Sort::SORT_DIRECTION ) );
        }
        $controller =& new CRM_Core_Selector_Controller($selector, $this->get(CRM_Utils_Pager::PAGE_ID),
                                                            $sortID, CRM_Core_Action::VIEW, $this, $output);
        $controller->setEmbedded(true);
        $controller->run();
        $controller->moveFromSessionToTemplate( );
    }


    function edit( )
    {

        // DRAFTING: Do we need following two lines?
        $context = CRM_Utils_Request::retrieve( 'context', 'String',$this );
        $this->assign('context', $context );

        // DRAFTING: Do we need following line?
        $this->_id = CRM_Utils_Request::retrieve('id', 'Integer', $this);
        
        // DRAFTING: There might be smarter way to incorporate case infomation - investigate this.
        $this->_caseID = CRM_Utils_Request::retrieve( 'caseid', 'Integer', $this );
        $this->assign('caseid', $this->_caseID );

        // DRAFTING: Why the hell do we need user context here?
        $session =& CRM_Core_Session::singleton();
        $edit = CRM_Utils_Request::retrieve( 'edit', 'Integer',$this );

        CRM_Core_Error::debug( 'edit', $edit  );

        // DRAFTING: Need to sort out the situation with cases here.
        if( $edit ){
            $url = CRM_Utils_System::url( 'civicrm/contact/view/activity',
                   "activity_id=1&action=view&reset=1&selectedChild=activity&id=" . $this->_id."&cid=". $this->_contactId."&subType=1&context=".$context . "");
        }

        if ( $context == 'Home' ) {
            if( $edit ) {
                $url = CRM_Utils_System::url('civicrm/contact/view/activity',"activity_id=1&action=view&reset=1&selectedChild=activity&id=". $this->_id."&cid=". $this->_contactId."&history={$history}&subType=1&context=".$context);
            }else{
                $url = CRM_Utils_System::url('civicrm', 'reset=1' );
            }
        } else if ($context == 'case'){
            
             if($edit){
//                $url = CRM_Utils_System::url('civicrm/contact/view/activity',"activity_id=1&action=view&reset=1&selectedChild=activity&id=". $this->_id."&cid=". $this->_contactId."&history={$history}&subType=1&context=".$context."&caseid=".$this->_caseID);
             }else{
             
                 $url = CRM_Utils_System::url('civicrm/contact/view/case',
                                              "show=1&action=view&reset=1&cid={$this->_contactId}&id={$this->_caseID}&selectedChild=case" );
             }
             
        } else {

            if($edit){
                
                $url = CRM_Utils_System::url('civicrm/contact/view/activity',"activity_id=1&action=view&reset=1&selectedChild=activity&id=". $this->_id."&cid=". $this->_contactId."&history={$history}&subType=1&context=activity");
            } else{ 
                $url = CRM_Utils_System::url('civicrm/contact/view',
                                             "show=1&action=browse&reset=1&history={$history}&cid={$this->_contactId}&selectedChild=activity" );
            }
        }      
        $session->pushUserContext( $url );
        
        if (CRM_Utils_Request::retrieve('confirmed', 'Boolean',
                                        CRM_Core_DAO::$_nullObject )){
            
                require_once 'CRM/Activity/BAO/Activity.php';
                
                CRM_Activity_BAO_Activity::del( $this->_id, 'Meeting');
            CRM_Utils_System::redirect($url);
        }
        
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Activity_Form_Meeting', ts('Contact Meetings'), $this->_action );
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
     * Heart of the viewing process. The runner gets all the meta data for
     * the contact and calls the appropriate type of page to view.
     *
     * @return void
     * @access public
     *
     */
    function preProcess()
    {
        parent::preProcess();

        // we need to retrieve privacy preferences
        // to (un)display the 'Send an Email' link
        $params   = array( );
        $defaults = array( );
        $ids      = array( );
        $params['id'] = $params['contact_id'] = $this->_contactId;
        CRM_Contact_BAO_Contact::retrieve($params, $defaults, $ids);
        CRM_Contact_BAO_Contact::resolveDefaults($defaults);
        $this->assign($defaults);
    }


    function delete( )
    {
        $url     = 'civicrm/contact/view';

        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url($url, 'action=browse&selectedChild=activity&history=1&show=1' ) );

        $controller =& new CRM_Core_Controller_Simple('CRM_Activity_Form_Activity',
                                                       ts('Delete Activity Record'),
                                                       $this->_action );
        $controller->set('id', $this->_id);
        $controller->setEmbedded( true );
        $controller->process( );
        $controller->run( );
    }

    /**
     * perform actions and display for activities.
     *
     * @return none
     *
     * @access public
     */
    function run( )
    {
        $this->preProcess( );
        
        // route behaviour of contact/view/activity based on action defined
        if ( $this->_action & 
           ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::VIEW ) ) {
            $this->edit( );
        } elseif ( $this->_action & CRM_Core_Action::DELETE ) {
            $this->delete( );
        } else {
            $this->browse( );
        }
        
        return parent::run( );
    }
}
?>
