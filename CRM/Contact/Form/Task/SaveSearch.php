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
 * This class provides the functionality to save a search
 * Saved Searches are used for saving frequently used queries
 */
class CRM_Contact_Form_Task_SaveSearch extends CRM_Contact_Form_Task {

    /**
     * class constructor
     *
     */
    function __construct( $name, $state, $mode = self::MODE_NONE ) {
        parent::__construct($name, $state, $mode);
    }

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess()
    {
        /*
         * initialize the task and row fields
         */
        parent::preProcess();
    }

    /**
     * Build the form - it consists of
     *    - displaying the QILL (query in local language)
     *    - displaying elements for saving the search
     *
     * @param none
     * @access public
     * @return void
     */
    function buildQuickForm()
    {
        // get the qill 
        // get the session variables for search scope
        $session = CRM_Session::singleton( );        
        $session->getVars($searchScope, CRM_Contact_Form_Search::SESSION_SCOPE_SEARCH);
        $qill = CRM_Contact_Selector::getQILL($searchScope['fv'], $searchScope['type']);

        // need to save qill for the smarty template
        $template = CRM_Core_Smarty::singleton( );
        $template->assign('qill', $qill);

        $this->add('text', 'ss_name', 'Name', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'name'), true);
        $this->addElement('text', 'ss_description', 'Description', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'description'));
        $this->addDefaultButtons( 'Save Search' );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess()
    {

        $session = CRM_Session::singleton( );        
        $session->getVars($searchScope, CRM_Contact_Form_Search::SESSION_SCOPE_SEARCH);

        // saved search form values
        $fv = $this->controller->exportValues($this->_name);

        // save the search
        $ssBAO = new CRM_Contact_BAO_SavedSearch();
        $ssBAO->domain_id = 1;   // hack for now
        $ssBAO->name = $fv['ss_name'];
        $ssBAO->description = $fv['ss_description'];
        $ssBAO->search_type = $searchScope['type'];
        $ssBAO->form_values = serialize($searchScope['fv']);
        $ssBAO->insert();
        CRM_Session::setStatus( 'Your search has been saved as "' . $fv['ss_name'] . '"' );
    }
}
?>