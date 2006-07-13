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

require_once 'CRM/Core/StateMachine.php';

/**
 * State machine for managing different states of the Quest process.
 *
 */
class CRM_Quest_StateMachine_MatchApp extends CRM_Core_StateMachine {

    static $_dependency = null;

    /**
     * class constructor
     *
     * @param object  CRM_Quest_Controller_PreApp
     * @param int     $action
     *
     * @return object CRM_Quest_StateMachine_PreApp
     */
    function __construct( &$controller, $action = CRM_Core_Action::NONE ) {
        parent::__construct( $controller, $action );

        $this->rebuild( $controller, $action );
    }

    public function rebuild( &$controller, $action = CRM_Core_Action::NONE ) {
        // ensure the states array is reset
        $this->_states = array( );

        $firstPages = array(
                              'CRM_Quest_Form_MatchApp_Personal'      => null,
                              'CRM_Quest_Form_MatchApp_Scholarship'   => null,
                              'CRM_Quest_Form_MatchApp_Educational'   => null,
                              'CRM_Quest_Form_MatchApp_Household'     => null,
                              'CRM_Quest_Form_MatchApp_Guardian'      => null,
                              'CRM_Quest_Form_MatchApp_Sibling'       => null,
                              'CRM_Quest_Form_MatchApp_Income'        => null,
                              'CRM_Quest_Form_MatchApp_WorkExperience' => null,
                                                          
                            );
        
        $dynamic = array();// 'Household', 'Sibling', 'Income' );
        $dynamicPages = array( );
        foreach ( $dynamic as $d ) {
            require_once "CRM/Quest/Form/App/$d.php";
            eval( '$pages =& CRM_Quest_Form_MatchApp_' . $d . '::getPages( $controller );' );
            $dynamicPages = array_merge( $dynamicPages, $pages );
        }

        $grades = array( 'Nine'   => '9th Grade',
                         'Ten'    => '10th Grade',
                         'Eleven' => '11th Grade',
                         'Twelve' => '12th Grade',
                         'Summer' => 'Summer School' );
        $gradePages = array( );
        $gradePages["Academic-Information"]  = array( 'className' => "CRM_Quest_Form_MatchApp_Academic",
                                                      'title'     => 'Academic Information',
                                                      'options'   => array( ) );

        foreach ( $grades as $grade => $title ) {
            $gradePages["Academic-{$grade}"] = array( 'className' => "CRM_Quest_Form_MatchApp_Transcript_$grade",
                                                        'title'     => $title,
                                                        'options'   => array( ) );
        }

        $essays = array( 'Short'           => 'Short Answers',
                         'Extracurricular' => 'Extracurricular Essay',
                         'Experience'      => 'Experience that excites you',
                         'Biographical'    => 'Biographical Essay',
                         'PersonalStat'    => 'Personal Statement',
                         'Optional'        => 'Optional Essay' );
        $essayPages = array( );
        foreach ( $essays as $essay => $title ) {
            $essayPages["Essay-{$essay}"] = array( 'className' => "CRM_Quest_Form_MatchApp_Essay_$essay",
                                                   'title'     => $title,
                                                   'options'   => array( ) );
        }


        $lastPages = array( );

        $this->_pages = array_merge( $firstPages, $dynamicPages, $lastPages, $gradePages, $essayPages );

        $this->addSequentialPages( $this->_pages, $action );
    }

    public function &getDependency( ) {
        if ( self::$_dependency == null ) {
            self::$_dependency = array(
                                       'Personal'    => array( ),
                                       'Scholarship' => array( 'Personal'  => 1 ),
                                       'Educational' => array( 'Personal'  => 1 ),
                                       'Household'   => array( 'Personal'  => 1 ),
                                       'Guardian'    => array( 'Household' => 1 ),
                                       'Sibling'     => array( 'Personal'  => 1 ),
                                       'Income'      => array( 'Personal'  => 1 ),
                                       'HighSchool'  => array( 'Personal'  => 1 ),
                                       'SchoolOther' => array( 'Personal'  => 1 ),
                                       'Academic'    => array( 'Personal'  => 1 ),
                                       'WorkExperience'     => array( 'Personal'  => 1 ),
                                       'Testing'     => array( 'Personal'  => 1 ),
                                       'Essay'       => array( 'Personal'  => 1 ),
                                       'Submit'      => array( 'Personal'  => 1 )
                                       );
        }

        return self::$_dependency;
    }

    public function checkDependency( &$controller, &$form ) {
        return;

        $dependency =& $this->getDependency( );

        $name = explode( '-', $form->getName( ) );
        $formName = $name[0];
        
        $data =& $controller->container( );

        foreach ( $dependency[$formName] as $name => $value ) {
            // for each name check that all pages are valid
            foreach ( $this->_pageNames as $pageName ) {
                if ( substr( $pageName, 0, strlen( $name ) ) == $name ) {
                    if ( ! $data['valid'][$pageName] ) {
                        $title = $form->getCompleteTitle( );
                        $otherTitle = $controller->_pages[$pageName]->getCompleteTitle( );
                        $session =& CRM_Core_Session::singleton( );
                        $session->setStatus( "The $otherTitle section must be completed before you can go to $title ." );
                        CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/quest/matchapp',
                                                                           "_qf_{$name}_display=1" ) );
                    }
                }
            }
        }
    }

    public function checkApplication( &$controller ) {
        return;

        $data =& $controller->container( );

        foreach ( $this->_pageNames as $pageName ) {
            if ( ! $data['valid'][$pageName] ) {
                $title = $controller->_pages[$pageName]->getCompleteTitle( );
                $session =& CRM_Core_Session::singleton( );
                $session->setStatus( "The $title section must be completed before you can submit the application" );
                CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/quest/matchapp',
                                                                   "_qf_{$pageName}_display=1" ) );
            }
        }
    }

    public function validPage( $name, &$valid ) {
        return true;

        $dependency =& $this->getDependency( );

        $name = explode( '-', $name );
        $formName = $name[0];
        
        foreach ( $dependency[$formName] as $name => $value ) {
            // for each name check that all pages are valid
            foreach ( $this->_pageNames as $pageName ) {
                if ( substr( $pageName, 0, strlen( $name ) ) == $name ) {
                    if ( ! $valid[$pageName] ) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

}

?>