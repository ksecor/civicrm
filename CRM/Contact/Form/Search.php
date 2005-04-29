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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Files required
 */
require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Core/Selector/Controller.php';
require_once 'CRM/Contact/Selector.php';

/**
 * Base Search / View form for *all* listing of multiple 
 * contacts
 */
class CRM_Contact_Form_Search extends CRM_Form {

    const SESSION_SCOPE_SEARCH   = 'search';

    /*
     * csv - common search values
     * @static
     * @access protected
     */
    static $csv = array('contact_type', 'group', 'category');

    /**
     * Class construtor
     *
     * @param string    $name  name of the form
     * @param CRM_State $state State object that is controlling this form
     * @param int       $mode  Mode of operation for this form
     *
     * @return CRM_Contact_Form_Search
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE)
    {
        parent::__construct($name, $state, $mode);
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        $this->populatePseudoConstant();

        $contactType = array('any' => ' - any contact - ') + CRM_PseudoConstant::$contactType;
        $this->add('select', 'contact_type', 'Show me.... ', $contactType);

        // add select for groups
        $group = array('any' => ' - any group - ') + CRM_PseudoConstant::$group;
        $this->add('select', 'group', 'in', $group);

        // add select for categories
        $category = array('any' => ' - any category - ') + CRM_PseudoConstant::$category;
        $this->add('select', 'category', 'Category', $category);

        // text for sort_name
        $this->add('text', 'sort_name', 'Name:', CRM_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        
        // some tasks.. what do we want to do with the selected contacts ?
        $tasks = array( '' => '- actions -' ) + CRM_Contact_Task::$tasks;
        $this->add('select', 'task'   , 'Actions: '    , $tasks    );

        // need to perform tasks on all or selected items ? using radio_ts(task selection) for it
        $this->addElement('radio', 'radio_ts', null, 'selected records only', 'ts_sel', array('checked'=>null));
        $this->addElement('radio', 'radio_ts', null, 'all', 'ts_all');

        // ??
        $rows = $this->get( 'rows' );
        if ( is_array( $rows ) ) {
            foreach ( $rows as &$row ) {
                $this->addElement( 'checkbox', $row['checkbox'] );
            }
        }

        // add buttons
        $this->addButtons( array(
                                 array ( 'type'      => 'refresh',
                                         'name'      => 'Search' ,
                                         'isDefault' => true     )
                                 )        
                           );
        
        /*
         * add the go button for the action form, note it is of type 'next' rather than of type 'submit'
         *
         */
        $this->add('submit', $this->getButtonName( 'next' ), 'Perform Action!', array( 'class' => 'form-submit',
                                                                                       'onclick' => "return checkPerformAction('mark_x', '".$this->getName()."');" ) );
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        $defaults = array();

        // get the session variables for search scope
        $session = CRM_Session::singleton( );        
        $session->getVars($searchScope, CRM_Contact_Form_Search::SESSION_SCOPE_SEARCH);

        // sort_name remains same across basic/advanced search
        $defaults['sort_name'] = $searchScope['fv']['sort_name'];
        
        // defaults for the rest depend on type of search in the session
        switch ($searchScope['type']) {
        case CRM_Form::MODE_BASIC:
            foreach (self::$csv as $v) {
                $defaults[$v] = $searchScope['fv'][$v];
            }
            break;
        case CRM_Form::MODE_ADVANCED:
            foreach (self::$csv as $v) {
                $defaults[$v] = $searchScope['fv']['cb_'.$v] ? key($searchScope['fv']['cb_'.$v]) : 'any';
            }
            break;
        }
        return $defaults;
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) {
        $this->addFormRule( array( 'CRM_Contact_Form_Search', 'formRule' ) );
    }

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) {
        $fv = $this->controller->exportValues($this->_name);
        $selector = new CRM_Contact_Selector($fv, $this->_mode);
        $controller = new CRM_Selector_Controller($selector , null, null, CRM_Action::VIEW, $this, CRM_Selector_Controller::TRANSFER );

        if ( $controller->hasChanged( ) ) {
            $this->postProcess( );
        }
        $controller->moveFromSessionToTemplate( );
    }

    /**
     * this method is called for processing a submitted search form
     *
     * @param none
     * @return void
     * @access public
     */
    function postProcess() 
    {
        // if we are in reset state, i.e. just entered the form, dont display any result
        if($_GET['reset'] == 1) {
            return;
        }

        // get user submitted values
        $fv = $this->controller->exportValues($this->_name);

        /* after every search form is submitted we save the following in the session
         *     - type of search 'type'
         *     - submitted form values 'fv'
         *     - task query 'tq'
         *     - QILL 'qill'
         */

        $session = CRM_Session::singleton();
        $session->set('type', $this->_mode, self::SESSION_SCOPE_SEARCH);
        $session->set('fv', $fv, self::SESSION_SCOPE_SEARCH);

        // check actionName and if next, then do not repeat a search, since we are going to the next page
        list($pageName, $action) = $this->controller->getActionName();

        if ($action == 'next') {
            return;
        }

        // create the selector, controller and run - store results in session
        $selector = new CRM_Contact_Selector($fv, $this->_mode);
        $controller = new CRM_Selector_Controller($selector , null, null, CRM_Action::VIEW, $this, CRM_Selector_Controller::SESSION );
        $controller->run();
    }


    /**
     * Add a form rule for this form. If Go is pressed then we must select some checkboxes
     * and an action
     */
    static function formRule( &$fields ) {
        // check actionName and if next, then do not repeat a search, since we are going to the next page
        
        if ( array_key_exists( '_qf_Search_next', $fields ) ) {
            if ( ! CRM_Array::value( 'task', $fields ) ) {
                return array( 'task' => 'Please select a valid action.' );
            }

            // if the all contact option is selected, ignore the contact checkbox validation
            if ($fields['radio_ts'] == 'ts_all') { 
                return true;
            }

            foreach ( $fields as $name => $dontCare ) {
                if ( substr( $name, 0, self::CB_PREFIX_LEN ) == self::CB_PREFIX ) {
                    return true;
                }
            }
            return array( 'task' => 'Please select one or more checkboxes to perform the action on.' );
        }
        return true;
    }

    protected function populatePseudoConstant() {
        CRM_PseudoConstant::populateGroup();
        CRM_PseudoConstant::populateCategory();
    }
}
?>