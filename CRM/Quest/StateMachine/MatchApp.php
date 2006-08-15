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
abstract class CRM_Quest_StateMachine_MatchApp extends CRM_Core_StateMachine {

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
        $this->addSequentialPages( $this->_pages, $action );
    }

    abstract public function &getDependency( );

    public function checkDependency( &$controller, &$form ) {
        $dependency =& $this->getDependency( );
        if ( empty( $dependency ) ) {
            return;
        }

        $name = explode( '-', $form->getName( ) );
        $formName = $name[0];
        
        $data =& $controller->container( );
        if (is_array ( $dependency[$formName] ) ) {
            foreach ( $dependency[$formName] as $name => $value ) {
                // for each name check that all pages are valid
                foreach ( $this->_pageNames as $pageName ) {
                    if ( substr( $pageName, 0, strlen( $name ) ) == $name ) {
                        if ( ! $data['valid'][$pageName] ) {
                            $title = $form->getCompleteTitle( );
                            $otherTitle = $controller->_pages[$pageName]->getCompleteTitle( );
                            $session =& CRM_Core_Session::singleton( );
                            $session->setStatus( "The $otherTitle section must be completed before you can go to $title ." );
                            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/quest/matchapp/' . strtolower( $controller->_subType ),
                                                                               "_qf_{$name}_display=1" ) );
                        }
                    }
                }
            }
        }
    }

    // NEED TO FIX THIS
    public function checkApplication( &$controller ) {
        // if view or preview skip
        if ( $this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::PREVIEW ) ) {
            return;
        }

        $data =& $controller->container( );

        foreach ( $this->_pageNames as $pageName ) {
            // skip the submit page
            if ( $pageName == 'Submit' || $pageName == 'PartnerSubmit' ) {
                continue;
            }

            if ( ! $data['valid'][$pageName] ) {
                $title = $controller->_pages[$pageName]->getCompleteTitle( );
                $session =& CRM_Core_Session::singleton( );
                $session->setStatus( "The $title section must be completed before you can submit the application" );
                CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/quest/matchapp/' . strtolower( $controller->_subType ),
                                                                   "_qf_{$pageName}_display=1" ) );
            }
        }
    }

    public function isApplicationComplete( &$controller ) {
        $data =& $controller->container( );

        foreach ( $this->_pageNames as $name ) {
            $valid = ( $name == 'SchoolOther' || $name == 'Transcript-Summer') ? 1 : $data['valid'][$name];
            if ( ! $valid ) {
                return false;
            }
        }
        return true;
    }

    public function validPage( $name, &$valid ) {
        $dependency =& $this->getDependency( );
        if ( empty( $dependency ) ) {
            return true;
        }

        $name = explode( '-', $name );
        $formName = $name[0];

        if (is_array ( $dependency[$formName] ) ) { 
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
        }
        return true;
    }

}

?>