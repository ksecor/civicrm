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

    // public so that the state machine can access thi
    public    $_subType;
    public    $_subTypeTasks;

    protected $_sections;

    /**
     * class constructor
     */
    function __construct( $title = null, $action = CRM_Core_Action::NONE, $modal = true, $subType = null ) {
        parent::__construct( $title, $modal );
        
        $this->_subTypeTasks = array( 'Personal'  => 14,
                                      'Household' => 15,
                                      'School'    => 16,
                                      'Essay'     => 17,
                                      'College'   => 18,
                                      'Partner'   => 19 );
        
        $cid = $this->get( 'contactID' );
        $this->_action = CRM_Utils_Request::retrieve('action', 'String',
                                                     $this, false, 'update' );
        $this->assign( 'action', $this->_action );
        $this->assign( 'appName', 'MatchApp');
        $this->_subType = $subType;

        if ( ! $cid ) {
            $cid    = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                   $this );
            $session =& CRM_Core_Session::singleton( );
            $uid     = $session->get( 'userID' );

            if ( $cid ) {
                require_once 'CRM/Contact/BAO/Contact.php';
                require_once 'CRM/Utils/System.php';
                if ( $cid != $uid ) {
                    if ($this->_action & CRM_Core_Action::UPDATE) {
                        if ( ! CRM_Contact_BAO_Contact::permissionedContact( $uid , CRM_Core_Permission::EDIT ) ) {
                            CRM_Utils_System::statusBounce( ts('You do not have the necessary permission to edit this Application.') );
                        } 
                    }
                    if ($this->_action & CRM_Core_Action::VIEW) {
                        if ( ! CRM_Contact_BAO_Contact::permissionedContact( $uid , CRM_Core_Permission::VIEW ) ) {
                            CRM_Utils_System::statusBounce( ts('You do not have the necessary permission to view this Application.') );
                        }
                    }
                    $this->assign('questURL', 'questURL');
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

        // make sure this controller is ok to go
        $this->validateCategory( );

        require_once "CRM/Quest/StateMachine/MatchApp/$subType.php";
        eval( '$this->_stateMachine =& new CRM_Quest_StateMachine_MatchApp_' . $subType . '( $this, $this->_action );' );

        // create and instantiate the pages
        $this->addPages( $this->_stateMachine, $this->_action );

        // add all the actions
        $config =& CRM_Core_Config::singleton( );
        $this->addActions( $config->uploadDir, array( 'uploadFile' ) );

        require_once 'CRM/Project/BAO/TaskStatus.php';
        CRM_Project_BAO_TaskStatus::getTaskStatusInitial( $this,
                                                          'civicrm_contact', $cid,
                                                          'civicrm_contact', $cid,
                                                          $this->_subTypeTasks[$this->_subType] );

        // also initialize application task status
        CRM_Project_BAO_TaskStatus::getTaskStatusInitial( $this,
                                                          'civicrm_contact', $cid,
                                                          'civicrm_contact', $cid,
                                                          8,
                                                          'appTaskStatus', false );

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
        
        $subCount = 0;
        $data =& $this->container( );
        foreach ( $this->_pages as $name => $page ) {
            $subNames = explode( '-', $name );
            $step  = true;
            $link  = $this->_stateMachine->validPage( $name, $data['valid'] ) ? $page->getLink ( ) : null;
            $valid = ( $name == 'SchoolOther' ) ? 1 : $data['valid'][$name];
            if ( CRM_Utils_Array::value( $subNames[0], $this->_sections ) ) {
                $step      = false;
                $collapsed = true;
                if ( $this->_sections[$subNames[0]]['processed'] ) {
                    $count++;
                    $this->_sections[$subNames[0]]['processed'] = false;

                    // remember the index to fix valid status
                    $this->_sections[$subNames[0]]['index'] = count( $wizard['steps'] );

                    $wizard['steps'][] = array( 'name'       => $name,
                                                'title'      => $this->_sections[$subNames[0]]['title'],
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
                $this->_sections[$subNames[0]]['valid'] = $valid & $this->_sections[$subNames[0]]['valid'];
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
        foreach ( $this->_sections as $name => $value ) {
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

        $category =& self::getCategory( );
        foreach ( $category['steps'] as $name => $value ) {
            if ( $name == $this->_subType ) {
                $category['steps'][$name]['current'] = true;
            } else {
                $category['steps'][$name]['current'] = false;
            }
        }

        $this->assign_by_ref( 'category', $category );

        return $wizard;
    }

    function addWizardStyle( &$wizard ) {
        $wizard['style'] = array('barClass'             => 'app',
                                 'stepPrefixCurrent'    => '&nbsp; ',
                                 'stepPrefixPast'       => '&nbsp; ',
                                 'stepPrefixFuture'     => '&nbsp; ', 
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

    function isApplicationComplete( ) {
        return $this->_stateMachine->isApplicationComplete( $this );
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

    static function &getCategory( ) {
        $session =& CRM_Core_Session::singleton( );
        $category = $session->get( 'questMatchAppCategory' );
        if ( ! $category ) {
            $category = array( );
            $category['steps'] = array( );
            
            $category['steps']['Personal'] = 
                array( 'link'    => CRM_Utils_System::url( 'civicrm/quest/matchapp/personal',
                                                           'reset=1' ),
                       'title'   => 'Personal Information',
                       'current' => true,
                       'valid'   => false );
            $category['steps']['Household'] = 
                array( 'link'    => CRM_Utils_System::url( 'civicrm/quest/matchapp/household',
                                                           'reset=1' ),
                       'title'   => 'Household Information',
                       'current' => false,
                       'valid'   => false );
            $category['steps']['School'] = 
                array( 'link'    => CRM_Utils_System::url( 'civicrm/quest/matchapp/school',
                                                           'reset=1' ),
                       'title'   => 'School Information',
                       'current' => false,
                       'valid'   => false );
            $category['steps']['Essay'] = 
                array( 'link'    => CRM_Utils_System::url( 'civicrm/quest/matchapp/essay',
                                                           'reset=1' ),
                       'title'   => 'Essays',
                       'current' => false,
                       'valid'   => false );
            $category['steps']['College'] = 
                array( 'link'    => CRM_Utils_System::url( 'civicrm/quest/matchapp/college',
                                                           'reset=1' ),
                       'title'   => 'College Match',
                       'current' => false,
                       'valid'   => false );
            $category['steps']['Partner'] = 
                array( 'link'    => CRM_Utils_System::url( 'civicrm/quest/matchapp/partner',
                                                           'reset=1' ),
                       'title'   => 'Partner Supplements',
                       'current' => false,
                       'valid'   => false );
            $session->set( 'questMatchAppCategory', $category );
        }
        return $category;
    }

    function validateCategory( ) {
        return true;
    }

    function matchAppComplete( ) {
        $values = implode( ',', array_values( $this->_subTypeTasks ) );
        $query = "
SELECT count(*)
FROM   civicrm_task_status t
WHERE  t.responsible_entity_table = 'civicrm_contact'
  AND  t.responsible_entity_id    = $cid
  AND  t.target_entity_table      = 'civicrm_contact'
  AND  t.target_entity_id         = $cid
  AND  t.task_id IN ( $values )
  AND  t.status_id = 328
";
        $result = CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
        return ( $result == count( $values ) ) ? true : false;
    }

}

?>