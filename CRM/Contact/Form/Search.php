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

    //const SESSION_SCOPE_CSV = "commonSearchValues";


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
        CRM_Error::le_method();
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
        $this->add('submit', $this->getButtonName( 'next' ), 'Perform Action!', array( 'class' => 'form-submit' ) );

        CRM_Error::le_method();
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        CRM_Error::le_method();

        $defaults = array();

        // dont want to populate default values if
        // user wants to start afresh.
        if($_GET['reset'] == 1) {
            return;
        }

        $csv = array();

        $session = CRM_Session::singleton( );        
        $session->getVars($csv, CRM_Session::SCOPE_CSV);
      
        CRM_Error::debug_var('csv', $csv);

        $defaults = $csv;

        CRM_Error::debug_var('defaults', $defaults);

        CRM_Error::ll_method();

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

    function postProcess() 
    {
        // if we are in reset state, i.e. just entered the form, dont display any result
        if($_GET['reset'] == 1) {
            return;
        }
        
        // check actionName and if next, then do not repeat a search, since we are going to the next page
        list($pageName, $action) = $this->controller->getActionName();
        if ($action == 'next') {
            return;
        }

        // get user submitted values
        $fv = $this->controller->exportValues($this->_name);

        // set the scope for csv
        $this->_setCSV($fv);
        
        // create the selector, controller and run - store results in session
        $selector = new CRM_Contact_Selector($fv, $this->_mode);
        $controller = new CRM_Selector_Controller($selector , null, null, CRM_Action::VIEW, $this, CRM_Selector_Controller::SESSION );
        $controller->run();

        CRM_Error::ll_method();
    }



    private function _setCSV(&$fv) {
        // store the user submitted values in the common search values scope
        $session = CRM_Session::singleton( );
        $session->set("name", $fv['sort_name'], CRM_Session::SCOPE_CSV);        
        $session->set("contact_type", ($fv['contact_type']=='any') ? "" : $fv['contact_type'], CRM_Session::SCOPE_CSV);
        $session->set("group", ($fv['group']=='any') ? "" : $fv['group'], CRM_Session::SCOPE_CSV);
        $session->set("category", ($fv['category']=='any') ? "" : $fv['category'], CRM_Session::SCOPE_CSV);
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