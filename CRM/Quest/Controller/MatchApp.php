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

require_once 'CRM/Core/Controller.php';

class CRM_Quest_Controller_MatchApp extends CRM_Core_Controller {

    protected $_action;

    /**
     * class constructor
     */
    function __construct( $title = null, $action = CRM_Core_Action::NONE, $modal = true ) {
        parent::__construct( $title, $modal );
        
        $cid = $this->get( 'contactID' );
        $this->_action = CRM_Utils_Request::retrieve('action', 'String',
                                                     $this, false, 'update' );
        $this->assign( 'action', $this->_action );
        if ( ! $cid ) {
            $cid    = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                   $this );
            $session =& CRM_Core_Session::singleton( );
            $uid     = $session->get( 'userID' );

            if ( $cid ) {
                require_once 'CRM/Contact/BAO/Contact.php';
                require_once 'CRM/Utils/System.php';
                if ( ( $cid != $uid ) && ($this->_action & CRM_Core_Action::UPDATE) ) {
                    if ( ! CRM_Contact_BAO_Contact::permissionedContact( $uid , CRM_Core_Permission::EDIT ) ) {
                        CRM_Utils_System::statusBounce( ts('You do not have the necessary permission to edit this Application.') );
                    } 
                } else if (($cid != $uid ) && ($this->_action & CRM_Core_Action::VIEW) ) {
                    if ( ! CRM_Contact_BAO_Contact::permissionedContact( $uid , CRM_Core_Permission::VIEW ) ) {
                        CRM_Utils_System::statusBounce( ts('You do not have the necessary permission to view this Application.') );
                    }
                }
            } else {
                $cid = $uid;
            }

            if ( ! $cid ) {
                CRM_Core_Error::fatal( ts( "Could not find a valid contact id" ) );
            }
            $this->set( 'contactID', $cid );

            // set contact id and welcome name
       
            $dao =& new CRM_Contact_DAO_Contact( );
            $dao->id = $cid;
            if ( $dao->find( true ) ) {
                $this->set( 'welcome_name',
                             $dao->display_name );
            } else {
                CRM_Core_Error::fatal( ts( "Could not find a valid contact record" ) );
            }
       
        }

        $studentID = $this->get( 'studentID' );
        if ( ! $studentID ) {
            require_once 'CRM/Quest/DAO/Student.php';
            $dao =& new CRM_Quest_DAO_Student( );
            $dao->contact_id = $cid;
            if ( $dao->find( true ) ) {
                $this->set( 'studentID', $dao->id );
            }
        }

        require_once 'CRM/Quest/StateMachine/MatchApp.php';
        $this->_stateMachine =& new CRM_Quest_StateMachine_MatchApp( $this, $this->_action );

        // create and instantiate the pages
        $this->addPages( $this->_stateMachine, $this->_action );

        // add all the actions
        $config =& CRM_Core_Config::singleton( );
        $this->addActions( $config->uploadDir, array( 'uploadFile' ) );

        // get the task status object, if not there create one
        require_once 'CRM/Project/DAO/TaskStatus.php';
        $dao =& new CRM_Project_DAO_TaskStatus( );
        $dao->responsible_entity_table = 'civicrm_contact';
        $dao->responsible_entity_id    = $cid;
        $status =& CRM_Core_OptionGroup::values( 'task_status', true );
        if ( ! $dao->find( true ) ) {
            $dao->task_id             = 2;
            $dao->target_entity_table = 'civicrm_contact';
            $dao->target_entity_id    = $cid;
            $dao->create_date         = date( 'YmdHis' );
            
            $dao->status_id = $status['Not Started'];
            $dao->save( );
        } else if ( $dao->status_detail ) {
            $data =& $this->container( );
            $data['valid'] = unserialize( $dao->status_detail );
        }

        $this->set( 'taskStatusID', $dao->id );
        $this->assign( 'taskStatus', array_search( $dao->status_id, $status ) );

        //set user context
        $session =& CRM_Core_Session::singleton();
        $userContext = $session->readUserContext( );
        $this->assign( 'userContext', $userContext );
    }

    /**
     * Process the request, overrides the default QFC run method
     * This routine actually checks if the QFC is modal and if it
     * is the first invalid page, if so it call the requested action
     * if not, it calls the display action on the first invalid page
     * avoids the issue of users hitting the back button and getting
     * a broken page
     *
     * This run is basically a composition of the original run and the
     * jump action
     *
     */
    function run( ) {
        // early escape if we are previewing the application
        if ( $this->_action == CRM_Core_Action::PREVIEW ) {
            return $this->preview( );
        }

        // the names of the action and page should be saved
        // note that this is split into two, because some versions of
        // php 5.x core dump on the triple assignment :)
        $this->_actionName = $this->getActionName();
        list($pageName, $action) = $this->_actionName;

        if ( $this->isModal( ) ) {
            if ( ! $this->isValid( $pageName ) ) {
                $pageName = $this->findInvalid( );
                $action   = 'display';
            }
        }

        // check dependency first
        // if dependency fails, this does not return, but does a redirect
        $this->_stateMachine->checkDependency( $this, $this->_pages[$pageName] );

        $this->wizardHeader( $pageName );

        // note that based on action, control might not come back!!
        // e.g. if action is a valid JUMP, u basically do a redirect
        // to the appropriate place
        $this->_pages[$pageName]->handle($action);
        return $pageName;
    }

    /**
     * Create the header for the wizard from the list of pages
     * Store the created header in smarty
     *
     * @param string $currentPageName name of the page being displayed
     * @return array
     * @access public
     */
    function wizardHeader( $currentPageName ) {
        $wizard          = array( );
        $wizard['steps'] = array( );

        $count           = 0;
        
        $sections = array( 'Guardian' => array( 'title'     => 'Parent/Guardian Detail',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Sibling'  => array( 'title'     =>'Sibling Information',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Income'   => array( 'title'     => 'Household Income',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Extracurricular' => array( 'title' => 'Extracurricular Information',
                                                       'processed' => true,
                                                       'valid'     => true,
                                                       'index'     => 0 ),
                           'Academic' => array( 'title' => 'Academic Information',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Essay'    => array( 'title' => 'Essays',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Amherst'  => array( 'title'     => 'Amherst College',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Bowdoin'  => array( 'title'     => 'Bowdoin College',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Columbia'  => array( 'title'     => 'Columbia University',
                                                 'processed' => true,
                                                 'valid'     => true,
                                                 'index'     => 0 ),
                           'Pomona'   => array( 'title'     => 'Pomona College',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Rice'     => array( 'title'     => 'Rice University',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Stanford' => array( 'title'     => 'Stanford University',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Wellesley' => array( 'title'     => 'Wellesley College',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
                           'Wheaton'  => array( 'title'     => 'Wheaton College',
                                                'processed' => true,
                                                'valid'     => true,
                                                'index'     => 0 ),
          );

        $subCount = 0;
        $data =& $this->container( );
        foreach ( $this->_pages as $name => $page ) {
            $subNames = explode( '-', $name );
            $step  = true;
            $link  = $this->_stateMachine->validPage( $name, $data['valid'] ) ? $page->getLink ( ) : null;
            $valid = ( $name == 'SchoolOther' ) ? 1 : $data['valid'][$name];
            if ( CRM_Utils_Array::value( $subNames[0], $sections ) ) {
                $step      = false;
                $collapsed = true;
                if ( $sections[$subNames[0]]['processed'] ) {
                    $count++;
                    $sections[$subNames[0]]['processed'] = false;

                    // remember the index to fix valid status
                    $sections[$subNames[0]]['index'] = count( $wizard['steps'] );

                    $wizard['steps'][] = array( 'name'       => $name,
                                                'title'      => $sections[$subNames[0]]['title'],
                                                'link'       => $link,
                                                'valid'      => $valid,
                                                'step'       => true,
                                                'stepNumber' => $count,
                                                'collapsed'  => false );
                    $subCount = 1;
                    $stepNumber = $count . ".$subCount";
                } else {
                    $subCount++;
                    $stepNumber = $count . ".$subCount";
                }
                // the section valid is an AND of all subsection valid
                $sections[$subNames[0]]['valid'] = $valid & $sections[$subNames[0]]['valid'];
            } else {
                $count++;
                $stepNumber = $count;
                $collapsed  = false;
            }
            $wizard['steps'][] = array( 'name'       => $name,
                                        'title'      => $page->getTitle( ),
                                        'link'       => $link,
                                        'valid'      => $valid,
                                        'step'       => $step,
                                        'stepNumber' => $stepNumber,
                                        'collapsed'  => $collapsed );

            if ( $name == $currentPageName ) {
                $wizard['currentStepNumber']    = $stepNumber;
                $wizard['currentStepName']      = $name;
                $wizard['currentStepTitle']     = $page->getTitle( );
                $wizard['currentStepRootTitle'] = null;
            }
        }

        // fix valid status of all section heads
        foreach ( $sections as $name => $value ) {
            $wizard['steps'][$value['index']]['valid'] = $value['valid'];
        }

        $wizard['stepCount']         = $count;

        if ( strpos( $wizard['currentStepNumber'], '.' ) !== false ) {
            list( $one, $two ) = explode( '.', $wizard['currentStepNumber'] );

            // fix collapsed of sub section
            foreach ( $wizard['steps'] as $idx => $value ) {
                if ( $value['stepNumber'] == $one ) {
                    $wizard['currentStepRootTitle'] = $value['title'] . ': ';
                }
                list( $three, $four ) = explode( '.', $value['stepNumber'] );
                if ( $one == $three ) {
                    $wizard['steps'][$idx]['collapsed'] = false;
                }
            }
        }

        $this->addWizardStyle( $wizard ); 

        $this->assign( 'wizard', $wizard );
        return $wizard;
    }

    function addWizardStyle( &$wizard ) {
        $wizard['style'] = array('barClass'             => 'app',
                                 'stepPrefixCurrent'    => ' ',
                                 'stepPrefixPast'       => ' ',
                                 'stepPrefixFuture'     => ' ', 
                                 'subStepPrefixCurrent' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                 'subStepPrefixPast'    => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                 'subStepPrefixFuture'  => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                 'showTitle'            => 0 );
    }

    function rebuild( ) {
        $this->_stateMachine->rebuild( $this );

        $this->_pages = array( );
        $this->addPages( $this->_stateMachine );
    }

    function checkApplication( ) {
        $this->_stateMachine->checkApplication( $this );
    }

    function preview( ) {
        // lets switch to print mode
        $this->_print = true;
        
        // cache a display object
        $display =& new CRM_Core_QuickForm_Action_Display( $this->_stateMachine );

        // we need to run each form and display it
        $pageNames = array_keys( $this->_pages );
        $html = array( );
        foreach ( $pageNames as $name ) {
            // build the form and then display it
            $this->_pages[$name]->setAction( CRM_Core_Action::VIEW | CRM_Core_Action::PREVIEW );
            $this->_pages[$name]->buildForm( );
            $this->wizardHeader( $name );
            $title = $this->_pages[$name]->getCompleteTitle( );

            $html[$title] = $display->renderForm( $this->_pages[$name], true );
        }

        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'pageTitle', '2006 College Prep Scholarship Application' );
        $template->assign_by_ref( 'pageHTML', $html );
        
        echo $template->fetch( "CRM/Quest/Page/View/Preview.tpl" );
        exit( );
    }

    function getTemplateFile( ) {
        if ( $this->_action & CRM_Core_Action::PREVIEW ) {
            return 'CRM/common/printBody.tpl';
        } else if ( $this->getPrint( ) ) {
            return 'CRM/common/print.tpl';
        } else {
            return 'CRM/index.tpl';
        }
    }

}

?>