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
 * This class handle case related functions
 *
 */
class CRM_Contact_Page_View_Case extends CRM_Contact_Page_View 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * View details of a case
     *
     * @return void
     * @access public
     */
    function view( ) 
    {

        $controller =& new CRM_Core_Controller_Simple( 'CRM_Case_Form_Case',  
                                                       'View Case',  
                                                       $this->_action ); 
        $controller->setEmbedded( true ); 

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $url = CRM_Utils_System::url('civicrm/contact/view', 'action=browse&selectedChild=case&cid=' . $this->_contactId );
        $session->pushUserContext( $url ); 
        $controller->set( 'id' , $this->_id );  
        $controller->set( 'cid', $this->_contactId );
        
        $controller->run();
        $session =& CRM_Core_Session::singleton();
        $this->assign( 'totalCountOpenActivity',
                       CRM_Contact_BAO_Contact::getNumOpenActivity( $this->_contactId ) );
        require_once 'CRM/Contact/Selector/Activity.php' ;
        require_once 'CRM/Core/Selector/Controller.php';
        $output = CRM_Core_Selector_Controller::SESSION;
        $selector   =& new CRM_Contact_Selector_Activity($this->_contactId, 1 );
        $controller =& new CRM_Core_Selector_Controller($selector, $this->get(CRM_Utils_Pager::PAGE_ID),
                                                        $sortID, CRM_Core_Action::VIEW, $this,  $output, null, $this->_id);
        
        
        $controller->setEmbedded(true);
        $links  =& self::caseViewLinks( );
        $action = array_sum(array_keys($links));
        $controller->run();
        $controller->moveFromSessionToTemplate( );
    }

    /**
     * This function is called when action is browse
     *
     * return null
     * @access public
     */
    function browse( ) {

        $links  =& self::links( );
        $action = array_sum(array_keys($links));
        $caseStatus  = array( 1 => 'Resolved', 2 => 'Ongoing' ); 
        $caseType = CRM_Core_OptionGroup::values('f1_case_type');

        require_once 'CRM/Case/DAO/Case.php';
        $case = new CRM_Case_DAO_Case( );
        $case->contact_id = $this->_contactId;
        $case->find();
        while ( $case->fetch() ) {
            CRM_Core_DAO::storeValues( $case, $values[$case->id] );
            $values[$case->id]['action'] = CRM_Core_Action::formLink( $links,
                                                                      $action,
                                                                      array( 'id'  => $case->id,
                                                                             'cid' => $this->_contactId ) );
            $values[$case->id]['casetag1_id'] = $caseType[$values[$case->id]['casetag1_id']];
            $values[$case->id]['status_id']   = $caseStatus[$values[$case->id]['status_id']];

        } 
        
        $this->assign( 'cases', $values );
    }

    /**
     * This function is called when action is update or new
     * 
     * return null
     * @access public
     */
    function edit( ) 
    {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Case_Form_Case', 
                                                       'Create Case', 
                                                       $this->_action );
        $controller->setEmbedded( true );
        $this->_id = CRM_Utils_Request::retrieve('id', 'Integer',
                                                 $this);
        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $url = CRM_Utils_System::url('civicrm/contact/view', 'action=browse&selectedChild=case&cid=' . $this->_contactId );
        $session->pushUserContext( $url );
        
        if (CRM_Utils_Request::retrieve('confirmed', 'Boolean',
                                        CRM_Core_DAO::$_nullObject )) {
            if (CRM_Utils_Request::retrieve('mode', 'String',
                                            CRM_Core_DAO::$_nullObject)){
                
                $url = CRM_Utils_System::url('civicrm/contact/view/case', 'action=view&cid=' . $this->_contactId.'&id='.$this->_id .'&selectedChild=case');
                $session->pushUserContext( $url );
                $id = CRM_Utils_Request::retrieve('aid','Integer', CRM_Core_DAO::$_nullObject);
                require_once 'CRM/Case/BAO/Case.php';
                CRM_Case_BAO_Case::deleteCaseActivity( $id );
                CRM_Utils_System::redirect($url);
                return;
                
            } else {
                require_once 'CRM/Case/BAO/Case.php';
                CRM_Case_BAO_Case::deleteCase( $this->_id );
                CRM_Utils_System::redirect($url);
            }
        }
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
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE ) ) {
            $this->edit( );
        }
                
        $this->browse( );
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
        if (!(self::$_links)) {
            $deleteExtra = ts('Are you sure you want to delete this case?');

            self::$_links = array(
                                  CRM_Core_Action::VIEW    => array(
                                                                    'name'  => ts('Show Case'),
                                                                    'url'   => 'civicrm/contact/view/case',
                                                                    'qs'    => 'action=view&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=case',
                                                                    'title' => ts('Show Case')
                                                                    ),
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/contact/view/case',

                                                                    'qs'    => 'action=update&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=case',
                                                                    'title' => ts('Edit Case')
                                                                    ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Remove'),
                                                                    'url'   => 'civicrm/contact/view/case',
                                                                    'qs'    => 'action=delete&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=case',
                                                                    'extra' => 'onclick = "if (confirm(\'' . $deleteExtra . '\') ) this.href+=\'&amp;confirmed=1\'; else return false;"',                                                                    
                                                                    'title' => ts('Remove')
                                                                    ),
                                  );
        }
        return self::$_links;
    }
    
    static function &caseViewLinks()
    {
        if (!(self::$_links)) {
            $deleteExtra = ts('Are you sure you want to detach this case?');
            
            self::$_links = array(
                                 
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/contact/view/activity',
                                                                    
                                                                    'qs'    => 'activity_id=%%atype%%&action=update&reset=1&id=%%rid%%&cid=%%cid%%&subType=%%atype%%&context=case&caseid=%%id%%',
                                                                    'title' => ts('Edit Activity')
                                                                    ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Detach'),
                                                                    'url'   => 'civicrm/contact/view/case',
                                                                    'qs'    => 'action=delete&reset=1&cid=%%cid%%&aid=%%aid%%&id=%%id%%&selectedChild=case&mode=view',
                                                                    'extra' => 'onclick = "if (confirm(\'' . $deleteExtra . '\') ) this.href+=\'&amp;confirmed=1\'; else return false;"',                                                                    
                                                                    'title' => ts('Detach Activity')
                                                                    ),
                                  );
        }
        return self::$_links;
    }
    


}

?>
