<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 * This class acts as our base controller class and adds additional 
 * functionality and smarts to the base QFC. Specifically we create
 * our own action classes and handle the transitions ourselves by
 * simulating a state machine. We also create direct jump links to any
 * page that can be used universally.
 *
 * This concept has been discussed on the PEAR list and the QFC FAQ
 * goes into a few details. Please check
 * http://pear.php.net/manual/en/package.html.html-quickform-controller.faq.php
 * for other useful tips and suggestions
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Action/Direct.php';

require_once 'CRM/Core/StateMachine.php';

class CRM_Core_Controller extends HTML_QuickForm_Controller {

    /**
     * the title associated with this controller
     *
     * @var object
     */
    protected $_title;

    /**
     * the state machine associated with this controller
     *
     * @var object
     */
    protected $_stateMachine;

    /**
     * Is this object being embedded in another object. If
     * so the display routine needs to not do any work. (The
     * parent object takes care of the display)
     *
     * @var boolean
     */
    protected $_embedded = false;

    /**
     * Are we in print mode? if so we need to modify the display
     * functionality to do a minimal display :)
     *
     * @var boolean
     */
    protected $_print = false;

    /**
     * cache the smarty template for efficiency reasons
     *
     * @var CRM_Core_Smarty
     */
    static protected $_template;

    /**
     * cache the session for efficiency reasons
     *
     * @var CRM_Core_Session
     */
    static protected $_session;

    /**
     * All CRM single or multi page pages should inherit from this class. 
     *
     * @param string  descriptive title of the controller
     * @param boolean whether controller is modal
     *
     * @access public
     *   
     * @return void
     *
     */
    function __construct( $title = null, $modal = true ) {
        $this->HTML_QuickForm_Controller( get_class( $this ), $modal );

        $this->_title = $title;

        // let the constructor initialize this, should happen only once
        if ( ! isset( self::$_template ) ) {
            self::$_template = CRM_Core_Smarty::singleton( );
            self::$_session  = CRM_Core_Session::singleton( );
        }

        // if the request has a reset value, initialize the controller session
        if ( $_GET['reset'] ) {
            $this->reset( );
        }
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
        
        // if we are displaying the page, set the wizard
        if ( $action == 'display' ) {
            $this->wizardHeader( $pageName );
        }

        $this->_pages[$pageName]->handle($action);

        return $pageName;
    }

    /**
     * Helper function to add all the needed default actions. Note that the framework
     * redefines all of the default QFC actions
     *
     * @param string   directory to store all the uploaded files
     * @param array    names for the various upload buttons (note u can have more than 1 upload)
     *
     * @access private
     * @return void
     *
     */
    function addActions( $uploadDirectory = null, $uploadNames = null ) {
        static $names = array(
                              'display'   => 'CRM_Core_QuickForm_Action_Display',
                              'next'      => 'CRM_Core_QuickForm_Action_Next'   ,
                              'back'      => 'CRM_Core_QuickForm_Action_Back'   ,
                              'process'   => 'CRM_Core_QuickForm_Action_Process',
                              'cancel'    => 'CRM_Core_QuickForm_Action_Cancel' ,
                              'refresh'   => 'CRM_Core_QuickForm_Action_Refresh',
                              'done'      => 'CRM_Core_QuickForm_Action_Done'   ,
                              'jump'      => 'CRM_Core_QuickForm_Action_Jump'   ,
                              'submit'    => 'CRM_Core_QuickForm_Action_Submit' ,
                              );

        foreach ( $names as $name => &$classPath ) {
            $this->addAction( $name, new $classPath( $this->_stateMachine ) );
        }
    
        if ( ! empty( $uploadDirectory ) ) {
            $this->addAction('upload' ,
                             new CRM_Core_QuickForm_Action_Upload ($this->_stateMachine,
                                                                   $uploadDirectory,
                                                                   $uploadNames));
        }
    
    }

    /**
     * getter method for stateMachine
     *
     * @return object
     * @access public
     */
    function getStateMachine( ) {
        return $this->_stateMachine;
    }

    /**
     * setter method for stateMachine
     *
     * @param object a stateMachineObject
     *
     * @return void
     * @access public
     */
    function setStateMachine( $stateMachine) {
        $this->_stateMachine = $stateMachine;
    }

    /**
     * add pages to the controller. Note that the controller does not really care
     * the order in which the pages are added
     *
     * @param object stateMachine  the state machine object
     * @param int    mode          the mode in which the state machine is operating
     *                             typicaly this will be add/view/edit
     *
     * @return void
     * @access public
     *
     */
    function addPages( $stateMachine, $mode = CRM_Core_Form::MODE_NONE ) {
        $pages = $stateMachine->getPages( );

        foreach ( $pages as $classPath ) {
            $stateName   = CRM_Utils_String::getClassName($classPath);

            $$stateName = new $classPath( $stateName,
                                          $stateMachine->find( $classPath ),
                                          $mode );
            $this->addPage( $$stateName );
            $this->addAction( $stateName, new HTML_QuickForm_Action_Direct( ) );
        }
    }

    /**
     * QFC does not provide native support to have different 'submit' buttons.
     * We introduce this notion to QFC by using button specific data. Thus if
     * we have two submit buttons, we could have one displayed as a button and
     * the other as an image, both are of type 'submit'.
     *
     * @return string the 
     * @access public
     *
     */
    function getButtonName( ) {
        $data =& $this->container();
        return CRM_Utils_Array::value( '_qf_button_name', $data );
    }

    /**
     * function to destroy all the session state of the controller.
     *
     * @access public
     * @return void
     */
    function reset( ) {
        $this->container( true );
        self::$_session->resetScope( $this->_name );
    }

    /**
     * virtual function to do any processing of data.
     * Sometimes it is useful for the controller to actually process data.
     * This is typically used when we need the controller to figure out
     * what pages are potentially involved in this wizard. (this is dynamic
     * and can change based on the arguments
     *
     * @return void
     * @access public
     */
    function process( ) {
    }

    /**
     * Store the variable with the value in the form scope
     *
     * @param  string|array $name  name  of the variable or an assoc array of name/value pairs
     * @param  mixed        $value value of the variable if string
     *
     * @access public
     * @return void
     *
     */
    function set( $name, $value = null) {
        self::$_session->set( $name, $value, $this->_name );
    }

    /**
     * Get the variable from the form scope
     *
     * @param  string name  : name  of the variable
     *
     * @access public

     * @return mixed
     *
     */
    function get( $name ) {
        return self::$_session->get( $name, $this->_name );
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
        foreach ( $this->_pages as $name => $page ) {
            $count++;
            $wizard['steps'][] = array( 'name'  => $name,
                                        'title' => $page->getTitle( ),
                                        'link'  => $page->getLink ( ) );

            if ( $name == $currentPageName ) {
                $wizard['currentStepNumber'] = $count;
                $wizard['currentStepTitle']  = $page->getTitle( );
            }
        }

        $wizard['stepCount']         = $count;

        $this->assign( 'wizard', $wizard );
        return $wizard;
    }

    /**
     * assign value to name in template
     *
     * @param array|string $name  name  of variable
     * @param mixed $value value of varaible
     *
     * @return void
     * @access public
     */
    function assign( $var, $value = null) {
        self::$_template->assign($var, $value);
    }

    /**
     * setter for embedded 
     *
     * @param boolean $embedded
     *
     * @return void
     * @access public
     */
    function setEmbedded( $embedded  ) {
        $this->_embedded = $embedded;
    }

    /**
     * getter for embedded 
     *
     * @return boolean return the embedded value
     * @access public
     */
    function getEmbedded( ) {
        return $this->_embedded;
    }

    /**
     * setter for print 
     *
     * @param boolean $print
     *
     * @return void
     * @access public
     */
    function setPrint( $print  ) {
        $this->_print = $print;
    }

    /**
     * getter for print 
     *
     * @return boolean return the print value
     * @access public
     */
    function getPrint( ) {
        return $this->_print;
    }

}

?>