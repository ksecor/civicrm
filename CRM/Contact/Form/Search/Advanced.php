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
 * advanced search, extends basic search
 */
class CRM_Contact_Form_Search_Advanced extends CRM_Contact_Form_Search {

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

        // add checkboxes for contact type
        $cb_contact_type = array( );
        foreach (CRM_PseudoConstant::$contactType as $k => $v) {
            $cb_contact_type[] = HTML_QuickForm::createElement('checkbox', $k, null, $v);
        }
        $this->addGroup($cb_contact_type, 'cb_contact_type', 'Show Me....', '<br />');
        
        // checkboxes for groups
        $cb_group = array();
        foreach (CRM_PseudoConstant::$group as $groupID => $groupName) {
            $this->addElement('checkbox', "cb_group[$groupID]", null, $groupName);
        }

        // checkboxes for categories
        $cb_category = array();
        foreach (CRM_PseudoConstant::$category as $categoryID => $categoryName) {
            $cb_category[] = $this->addElement('checkbox', "cb_category[$categoryID]", null, $categoryName);
        }

        // add text box for last name, first name, street name, city
        $this->addElement('text', 'sort_name', 'Contact Name', CRM_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        $this->addElement('text', 'street_name', 'Street Name:', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'street_name'));
        $this->addElement('text', 'city', 'City:',CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'city'));

        // select for state province
        $stateProvince = array('' => ' - any state/province - ') + CRM_PseudoConstant::$stateProvince;
        $this->addElement('select', 'state_province', 'State/Province', $stateProvince);

        // select for country
        $country = array('' => ' - any country - ') + CRM_PseudoConstant::$country;
        $this->addElement('select', 'country', 'Country', $country);

        // add text box for postal code
        $this->addElement('text', 'postal_code', 'Postal Code', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );
        $this->addElement('text', 'postal_code_low', 'Postal Code Range From', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );
        $this->addElement('text', 'postal_code_high', 'To', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );

        // checkboxes for location type
        $cb_location_type = array();
        $locationType = CRM_PseudoConstant::$locationType + array('any' => 'Any Locations');
        foreach ($locationType as $locationTypeID => $locationTypeName) {
            $cb_location_type[] = HTML_QuickForm::createElement('checkbox', $locationTypeID, null, $locationTypeName);
        }
        $this->addGroup($cb_location_type, 'cb_location_type', 'Include these locations', '&nbsp;');
        
        // checkbox for primary location only
        $this->addElement('checkbox', 'cb_primary_location', null, 'Search for primary locations only');        

        // some tasks.. what do we want to do with the selected contacts ?
        $tasks = array( '' => '- actions -' ) + CRM_Contact_Task::$tasks;
        $this->add('select', 'task'   , 'Actions: '    , $tasks    );

        $rows = $this->get( 'rows' );
        if ( is_array( $rows ) ) {
            foreach ( $rows as &$row ) {
                $this->addElement( 'checkbox', $row['checkbox'] );
            }
        }

        // new saved search link
        if (CRM_Request::retrieve('nss', $this)) {
            // since there's a request for a new saved search
            // we need to display form components for saved search
            // add components for saving the search
            //$this->addElement('checkbox', 'cb_ss', null, 'Save Search ?', array('checked'=>'0'));
            $this->addElement('checkbox', 'cb_ss', null, 'Save Search ?');
            $this->addElement('text', 'ss_name', 'Name', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'name') );
            $this->addElement('text', 'ss_description', 'Description', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'description'));
        } else if (($ssid=CRM_Request::retrieve('ssid')) && (CRM_Request::retrieve('action') == 'edit')) {
            // since there's a request for edit an existing saved search
            // we need to display correctly populated form components for saved search
            
            // get the ss
            $ssBAO = new CRM_Contact_BAO_SavedSearch();
            $ssBAO->id = $ssid;
            if($ssBAO->find(1)) {
                $this->addElement('checkbox', 'cb_ss', null, 'Save Search ?', array('checked'=>true));
                $this->addElement('text', 'ss_name', 'Name', 
                                  CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'name') + array('value' => $ssBAO->name));
                $this->addElement('text', 'ss_description', 'Description', 
                                  CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'description')+array('value'=>$ssBAO->description));
            } else {
                // fatal error ssid does not exist
                CRM_Error::fatal("saved search with id $ssid not found");
            }
        }

        // add the buttons
        $this->addButtons(array(
                                array ( 'type'      => 'refresh',
                                        'name'      => 'Search',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'reset',
                                        'name'      => 'Reset'),
                                )
                          );

    }


    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        $defaults = array();

        // since we have a ssid we need to set defaults differently
        if ($ssid = CRM_Request::retrieve('ssid')) {
            // ssid is set hence we need to set defaults using form values of SSID
            $ssDAO = new CRM_Contact_DAO_SavedSearch();
            $ssDAO->id = $ssid;
            $ssDAO->selectAdd();
            $ssDAO->selectAdd('id, form_values');
            if($ssDAO->find(1)) {
                // make sure u unserialize - since it's stored in serialized form
                $defaults = unserialize($ssDAO->form_values);
            }
        } else {
            $csv = array();
            $session = CRM_Session::singleton( );        
            $session->getVars($csv, CRM_Session::SCOPE_CSV);
            // name
            $defaults['sort_name'] = $csv['name'];
            // contact_type
            if($csv['contact_type']) {
                $defaults['cb_contact_type'] = array($csv['contact_type'] => 1);
            }
            // group
            if($csv['group']) {
                $defaults['cb_group'] = array($csv['group'] => 1);
            }

            // category
            if($csv['category']) {
                $defaults['cb_category'] = array($csv['category'] => 1);
            }
        }

        return $defaults;
    }

    /**
     * The preprocessing of the form gets done here.
     *
     * @param none
     *
     * @return none 
     * @access public
     */
    function preProcess( ) {
        $fv = $this->controller->exportValues($this->_name);
        $selector = new CRM_Contact_Selector($fv, $this->_mode);
        $controller = new CRM_Selector_Controller($selector , null, null, CRM_Action::VIEW, $this, CRM_Selector_Controller::TRANSFER );

        if ($controller->hasChanged() || CRM_Request::retrieve('ssid') ) {
            $this->postProcess( );
        }
        $controller->moveFromSessionToTemplate( );
    }


    /**
     * The post processing of the form gets done here.
     *
     * Key things done during post processing are
     *      - check for reset or next request. if present, skip post procesing.
     *      - now check if user requested running a saved search, if so, then
     *        the form values associated with the saved search are used for searching.
     *      - if user has done a submit with new values the regular post submissing is 
     *        done.
     * The processing consists of using a Selector / Controller framework for getting the
     * search results.
     *
     * @param none
     *
     * @return none 
     * @access public
     */
    function postProcess() 
    {
        if($_GET['reset'] == 1) {
            return;
        }
        
        // check actionName and if next, then do not repeat a search, since we are going to the next page
        list( $pageName, $action ) = $this->controller->getActionName( );
        if ( $action == 'next' ) {
            return;
        }

        $fv = array();

        // get form values either from saved search or from user submission
        if ($ssid = CRM_Request::retrieve('ssid')) {
            // ssid is set hence we need to set the form values for it.
            // also we need to set the values in the form...
            $ssDAO = new CRM_Contact_DAO_SavedSearch();
            $ssDAO->id = $ssid;
            $ssDAO->selectAdd();
            $ssDAO->selectAdd('id, form_values');
            if($ssDAO->find(1)) {
                // make sure u unserialize - since it's stored in serialized form
                $fv = unserialize($ssDAO->form_values);
            }
        } else {
            // get user submitted values
            $fv = $this->controller->exportValues($this->_name);
        }

        $session = CRM_Session::singleton( );

        // important - we need to store the form values in the session in case we want to save it.
        $session->set("fv", serialize($fv), CRM_Session::SCOPE_AS);

        // set up csv
        $this->_setCSV($fv);

        $selector = new CRM_Contact_Selector($fv, $this->_mode);
        $controller = new CRM_Selector_Controller($selector , null, null, CRM_Action::VIEW, $this, CRM_Selector_Controller::SESSION );
        $controller->run();

        // has the user asked to save query ?
        if ($fv['cb_ss']) {
            // save the search
            $ssBAO = new CRM_Contact_BAO_SavedSearch();
            $ssBAO->domain_id = 1;   // hack for now
            $ssBAO->name = $fv['ss_name'];
            $ssBAO->description = $fv['ss_description'];
            $ssBAO->search_type = CRM_Form::MODE_ADVANCED;
            $ssBAO->form_values = serialize($fv);
            $ssBAO->insert();
        }
    }


    /**
     * set the CSV common Search Values.
     *
     * Common Search Values (CSV) consists of an array of 4 fields
     * which is stored in the session with a scope commonSearchValues
     *
     * @param array reference
     *
     * @return none 
     * @access public
     */
    private function _setCSV(&$fv) {
        $session = CRM_Session::singleton( );

        // store the user submitted values in the common search values scope
        $session->set("name", $fv['sort_name'], CRM_Session::SCOPE_CSV);        
        $session->set("contact_type", $fv['cb_contact_type'] ? key($fv['cb_contact_type']) : "", CRM_Session::SCOPE_CSV);
        $session->set("group", $fv['cb_group'] ? key($fv['cb_group']) : "", CRM_Session::SCOPE_CSV);
        $session->set("category", $fv['cb_category'] ? key($fv['cb_category']) : "", CRM_Session::SCOPE_CSV);
    }

    protected function populatePseudoConstant() {
        parent::populatePseudoConstant();
        // populate stateprovince, country, locationtype
        CRM_PseudoConstant::populateStateProvince();
        CRM_PseudoConstant::populateCountry();
        CRM_PseudoConstant::populateLocationType();
    }
}
?>