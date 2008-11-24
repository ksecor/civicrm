<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
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

    /**
     * Open Case activity type id
     */
    protected $_openCaseId = null;

    /**
     * Change Case Type activity type id
     */
    protected $_changeCaseTypeId = null;

    /**
     * Change Case Status activity type id
     */
    protected $changeCaseStatusId = null;
    
    function preProcess( )
    {
        $this->_id        = CRM_Utils_Request::retrieve( 'id' , 'Positive', $this );
        $this->_contactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );

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

        $this->_openCaseId         = CRM_Core_OptionGroup::getValue( 'activity_type', 'Open Case', 'name' );
        $this->_changeCaseTypeId   = CRM_Core_OptionGroup::getValue( 'activity_type', 'Change Case Type', 'name' );
        $this->_changeCaseStatusId = CRM_Core_OptionGroup::getValue( 'activity_type', 'Change Case Status', 'name' );

        $this->assign( 'openCaseId'       ,$this->_openCaseId);
        $this->assign( 'changeCaseTypeId' ,$this->_changeCaseTypeId);
        $this->assign( 'changeCaseStatusId',$this->_changeCaseStatusId);
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

        $links  =& self::links( );
        $action = array_sum(array_keys($links));

        $queryParams = array();
        $query = " 
      SELECT civicrm_case.id as case_id, 
             case_type.label as case_type, 
             case_status.label as case_status, 
             case_relation_type.name_b_a as case_role, 
             max(civicrm_activity.activity_date_time) as case_recent_activity_date, 
             activity_type.label as case_recent_activity_type, 
             min(civicrm_activity.due_date_time) as case_scheduled_activity_date, 
             activity_type.label as case_scheduled_activity_type  

        FROM civicrm_contact contact_a 
             LEFT JOIN  civicrm_case_contact ON civicrm_case_contact.contact_id = contact_a.id  
             INNER JOIN civicrm_case ON civicrm_case_contact.case_id = civicrm_case.id 
             LEFT JOIN  civicrm_relationship case_relationship ON case_relationship.contact_id_a = civicrm_case_contact.contact_id  
             LEFT JOIN  civicrm_relationship_type case_relation_type ON ( case_relation_type.id = case_relationship.relationship_type_id AND case_relation_type.id = case_relationship.relationship_type_id ) 
             LEFT JOIN  civicrm_option_group option_group_case_status ON (option_group_case_status.name = 'case_status') 
             LEFT JOIN  civicrm_option_value case_status ON ( civicrm_case.status_id = case_status.value AND option_group_case_status.id = case_status.option_group_id )  
             LEFT JOIN  civicrm_option_group option_group_case_type ON (option_group_case_type.name = 'case_type') 
             LEFT JOIN  civicrm_option_value case_type ON ( civicrm_case.case_type_id = case_type.value AND option_group_case_type.id = case_type.option_group_id )  
             LEFT JOIN  civicrm_activity_target ON civicrm_activity_target.target_contact_id = contact_a.id  
             LEFT JOIN  civicrm_activity ON civicrm_activity.id = civicrm_activity_target.activity_id  
             LEFT JOIN  civicrm_option_group option_group_activity_type ON (option_group_activity_type.name = 'activity_type') 
             LEFT JOIN  civicrm_option_value activity_type ON ( civicrm_activity.activity_type_id = activity_type.value AND option_group_activity_type.id = activity_type.option_group_id )  

        WHERE civicrm_case_contact.contact_id = {$this->_contactId} 
        GROUP BY civicrm_case.id  
        ORDER BY case_scheduled_activity_date desc  ";

        $case = CRM_Core_DAO::executeQuery( $query, $queryParams );
        $values = array( );
        while ( $case->fetch() ) {

            $values[$case->case_id]['action'] = CRM_Core_Action::formLink( $links,
                                                                           $action,
                                                                           array( 'id'    => $case->case_id,
                                                                                  'cid'   => $this->_contactId,
                                                                                  'atype' => $this->_changeCaseTypeId ) );
            
            $values[$case->case_id]['case_type']                    = $case->case_type;
            $values[$case->case_id]['case_status']                  = $case->case_status;
            $values[$case->case_id]['case_role']                    = $case->case_role;
            $values[$case->case_id]['case_recent_activity_date']    = $case->case_recent_activity_date;
            $values[$case->case_id]['case_recent_activity_type']    = $case->case_recent_activity_type;
            $values[$case->case_id]['case_scheduled_activity_date'] = $case->case_scheduled_activity_date;
            $values[$case->case_id]['case_scheduled_activity_type'] = $case->case_scheduled_activity_type;
            $values[$case->case_id]['id']                           = $case->case_id;
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
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE ) ) {
            $this->edit( );
        }
                
        if ( $this->_contactId ) {
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
            if ($config->civiHRD){ 
                self::$_links = array(
                                      CRM_Core_Action::UPDATE  => array(
                                                                        'name'  => ts('Edit'),
                                                                        'url'   => 'civicrm/contact/view/case',
                                                                        'qs'    => 'action=update&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=case',
                                                                        'title' => ts('Edit Case')
                                                                        ),
                                      
                                      CRM_Core_Action::FOLLOWUP  => array(
                                                                          'name'  => ts('Add Activity'),
                                                                          'url'   => 'civicrm/contact/view/activity',
                                                                          'qs'    => 'action=add&reset=1&context=case&caseid=%%id%%&cid=%%cid%%',
                                                                          'title' => ts('Add Activity')
                                                                    )
                                      );
            } else {
                self::$_links = array(
                                      CRM_Core_Action::VIEW    => array(
                                                                        'name'  => ts('Manage Case'),
                                                                        'url'   => 'civicrm/contact/view/case',
                                                                        'qs'    => 'action=view&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=case&context=case',
                                                                        'title' => ts('Manage Case')
                                                                        ),
                                                      
                                      CRM_Core_Action::DELETE  => array(
                                                                        'name'  => ts('Delete Case'),
                                                                        'url'   => 'civicrm/contact/view/case',
                                                                        'qs'    => 'action=delete&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=case',
                                                                        'title' => ts('Delete Case')
                                                                        ),
                                      
                                      );
            }
        }
        return self::$_links;
    }
    
    function setContext( ) 
    {
        $this->_id        = CRM_Utils_Request::retrieve('id', 'Integer',   $this);
        $this->_contactId = CRM_Utils_Request::retrieve('cid','Positive',  $this);
//        CRM_Core_Error::debug('caseid', $this->_id);

        $context = CRM_Utils_Request::retrieve( 'context', 'String', $this );
        $url = null;
        switch ( $context ) {
        case 'case':
            if ( $this->_contactId ) {
                $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                             "reset=1&cid={$this->_contactId}&action=view&selectedChild=case" );
            }
            break;
        
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

        default :
            if ( $this->_contactId && $this->_id ) {
                $url = CRM_Utils_System::url( 'civicrm/contact/view/case',
                                              "reset=1&cid={$this->_contactId}&action=view&id={$this->_id}&show=1&selectedChild=case" );
            }
            break;
        }
        
        if ( $url ) {
            $session =& CRM_Core_Session::singleton( ); 
            $session->pushUserContext( $url );
        }
    }
}


