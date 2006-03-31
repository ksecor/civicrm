<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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

class CRM_Quest_Controller_PreApp extends CRM_Core_Controller {

    /**
     * class constructor
     */
    function __construct( $title = null, $action = CRM_Core_Action::NONE, $modal = true ) {
        parent::__construct( $title, $modal );

        // set contact id and welcome name
        if ( ! $this->get( 'contact_id' ) ) {
            $session =& CRM_Core_Session::singleton( );
            $cid = $session->get( 'userID' );
            if ( ! $cid ) {
                CRM_Core_Error::fatal( ts( "Could not find a valid contact id" ) );
            }
            $this->set( 'contact_id' , $cid );
            require_once 'CRM/Contact/DAO/Individual.php';
            $dao =& new CRM_Contact_DAO_Individual( );
            $dao->contact_id = $cid;
            if ( $dao->find( true ) ) {
                $this->set( 'welcome_name',
                            $dao->first_name );
            } else {
                // make sure contact exists
                $dao =& new CRM_Contact_DAO_Contact( );
                $dao->id = $cid;
                if ( $dao->find( true ) ) {
                    $this->set( 'welcome_name',
                                $dao->display_name );
                } else {
                    CRM_Core_Error::fatal( ts( "Could not find a valid contact record" ) );
                }
            }
        }

        require_once 'CRM/Quest/StateMachine/PreApp.php';
        $this->_stateMachine =& new CRM_Quest_StateMachine_PreApp( $this, $action );

        // create and instantiate the pages
        $this->addPages( $this->_stateMachine, $action );

        // add all the actions
        $config =& CRM_Core_Config::singleton( );
        $this->addActions( $config->uploadDir, array( 'uploadFile' ) );

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

        // note that based on action, control might not come back!!
        // e.g. if action is a valid JUMP, u basically do a redirect
        // to the appropriate place
        $this->wizardHeader( $pageName );

        // check dependency first
        // if dependency fails, this does not return, but does a redirect
        $this->_stateMachine->checkDependency( $this, $this->_pages[$pageName] );

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
        
        $sections = array( 'Guardian' => array( 'title' => 'Parent/Guardian Detail',
                                                'valid' => true ),
                           'Sibling'  => array( 'title' => 'Sibling Information',
                                                'valid' => true ),
                           'Income'   => array( 'title' => 'Household Income',
                                                'valid' => true ) );

        $subCount = 0;
        foreach ( $this->_pages as $name => $page ) {
            $subNames = explode( '-', $name );
            $step  = true;
            if ( CRM_Utils_Array::value( $subNames[0], $sections ) ) {
                $step      = false;
                $collapsed = true;
                if ( $sections[$subNames[0]]['valid'] ) {
                    $count++;
                    $sections[$subNames[0]]['valid'] = false;
                    $wizard['steps'][] = array( 'name'       => $name,
                                                'title'      => $sections[$subNames[0]]['title'],
                                                'link'       => $page->getLink ( ),
                                                'step'       => true,
                                                'stepNumber' => $count,
                                                'collapsed'  => false );
                    $subCount = 1;
                    $stepNumber = $count . ".$subCount";
                } else {
                    $subCount++;
                    $stepNumber = $count . ".$subCount";
                }
            } else {
                $count++;
                $stepNumber = $count;
                $collapsed  = false;
            }

            $wizard['steps'][] = array( 'name'       => $name,
                                        'title'      => $page->getTitle( ),
                                        'link'       => $page->getLink ( ),
                                        'step'       => $step,
                                        'stepNumber' => $stepNumber,
                                        'collapsed'  => $collapsed );

            if ( $name == $currentPageName ) {
                $wizard['currentStepNumber'] = $count;
                $wizard['currentStepName']   = $name;
                $wizard['currentStepTitle']  = $page->getTitle( );
            }
        }

        $wizard['stepCount']         = $count;

        $this->addWizardStyle( $wizard ); 

        $this->assign( 'wizard', $wizard );
        return $wizard;
    }

    function addWizardStyle( &$wizard ) {
        $wizard['style'] = array('barClass'          => 'preApp',
                                 'stepPrefixCurrent' => ' ',
                                 'stepPrefixPast'    => ' ',
                                 'stepPrefixFuture'  => ' ' );
    }

}

?>